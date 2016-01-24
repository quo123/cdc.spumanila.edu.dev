<?php
require_once 'classDB.php';
require_once 'crypto.php';

function open_image($file) {
	$size = getimagesize($file);
//	if ($size[0] > 5000 || $size[1] > 5000) {
//		return false;
//	}
	switch($size["mime"]) {
		case "image/jpg":
		case "image/pjpeg":
		case "image/jpeg": $image = imagecreatefromjpeg($file); break;
		case "image/gif": $image = imagecreatefromgif($file); break;
		case "image/x-png":
		case "image/png":
			$required_memory = Round(($size[0]+500) * ($size[1]+500) * $size["bits"]);
			$new_limit = memory_get_usage() + $required_memory;
			ini_set("memory_limit", $new_limit);
			$image = imagecreatefrompng($file);
//			ini_restore("memory_limit");
//			die('sdsd');
//			ini_set("memory_limit", '128M');
			break;
		default: $image = false; break;
	}
	return array($image, $size);
}

/**
 *
 * @param resource $src
 * @param array() $sizes
 * @param string $mode 'RIGHT' || 'LEFT' || 'CENTER'
 * @param int $cropwidth
 * @param int $cropheight
 * @return resource
 */
function inline_crop($src, $sizes, $mode = 'CENTER', $cropwidth = 200, $cropheight = 193) {
	//resize
	$srcx = $sizes[0];
	$srcy = $sizes[1];
	$ratio = $cropwidth/$cropheight;
	$xratio = $cropwidth/$srcx;
	$yratio = $cropheight/$srcy;
	
	//preserve transparency
	$canvas = imagecreatetruecolor($cropwidth, $cropheight);
	imagesavealpha($canvas, true);
	$trans = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
	imagefill($canvas, 0, 0, $trans);
	
	if ($srcx/$srcy >= $ratio) {
		$crop = array($srcx*$yratio, $cropheight);
		switch ($mode) {
			case 'RIGHT': imagecopyresampled($canvas, $src, $cropwidth-$crop[0], 0, 0, 0, $crop[0], $crop[1], $srcx, $srcy); break;
			case 'CENTER': imagecopyresampled($canvas, $src, ($cropwidth-$crop[0])/2, 0, 0, 0, $crop[0], $crop[1], $srcx, $srcy); break;
			case 'LEFT':
			default: imagecopyresampled($canvas, $src, 0, 0, 0, 0, $crop[0], $crop[1], $srcx, $srcy);
		}	
	} else {
		$crop = array($cropwidth, $srcy*$xratio);
		imagecopyresampled($canvas, $src, 0, 0, 0, 0, $crop[0], $crop[1], $srcx, $srcy);
	}
	
//	$crop = ($srcx/$srcy >= $ratio) ? array($srcx*$yratio, $cropheight) : array($cropwidth, $srcy*$xratio);
	return $canvas;
}

/**
 * 
 * @param resource $src
 * @param array() $sizes
 * @param int $thumbwidth
 * @param int $thumbheight
 * @param array() $backcolor
 * @return resource
 */
function make_thumbnail($src, $sizes,  $thumbwidth = 125, $thumbheight = 125, $backcolor = array(35, 35, 35)) {
	//resize
	$srcx = $sizes[0];
	$srcy = $sizes[1];
	$ratio = $thumbwidth/$thumbheight;
//	$xratio = $thumbwidth/$srcx;
//	$yratio = $thumbheight/$srcy;
	
	if ($srcx/$srcy >= $ratio) {
		$thumbheight = $srcy * ($thumbheight/$srcx);
	} else {
		$thumbwidth = $srcx * ($thumbwidth/$srcy);
	}
	
	$canvas = imagecreatetruecolor($thumbwidth, $thumbheight);
	if ($backcolor === null) {
		//preserve transparency
		imagesavealpha($canvas, true);
		$trans = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
		imagefill($canvas, 0, 0, $trans);
	} else {
		//create bg for transparencies
		$bg = imagecolorallocate($canvas, $backcolor[0], $backcolor[1], $backcolor[2]);
		imagefill($canvas, 0, 0, $bg);
	}
	imagecopyresampled($canvas, $src, 0, 0, 0, 0, $thumbwidth, $thumbheight, $srcx, $srcy);
	
	return $canvas;
}

/** CREATING INLINE IMAGES ON THE FLY
 * 
 * $file = open_image($filepath);<br>
 * echo "<img src=\"".inline_image(inline_crop($file[0], $file[1]), $file[1]["mime"])."\" />";
 * 
*/
function inline_image($imageresource, $mime) {
	ob_start();
		switch ($mime) {
			case 'image/jpg':
			case 'image/pjpeg':
			case 'image/jpeg': imagejpeg($imageresource, null, 100); break;
			case 'image/gif': imagegif($imageresource); break;
			case 'image/x-png':
			case 'image/png': imagepng($imageresource); break;
			default: return false;
		}		
		$contents = ob_get_contents();
	ob_end_clean();
	switch ($mime) {
		case 'image/jpg':
		case 'image/pjpeg':
		case 'image/jpeg': return 'data:image/jpeg;base64,'.base64_encode($contents);
		case 'image/gif': return 'data:image/gif;base64,'.base64_encode($contents);
		case 'image/x-png':
		case 'image/png': return 'data:image/png;base64,'.base64_encode($contents);
	}
}

/**
 * A simple image-handling and resizing class.
 */
class NewsImage {
	private $allowedexts = array('gif', 'jpeg', 'jpg', 'png');
	private $allowedtypes = array('image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/x-png', 'image/png');
	private $folderpath = 'uploads/news_images/';
	private $tempfolder = 'uploads/temp/';
	private $thumbfolder = 'uploads/b_thumb/';
	private $image;
	private $imagehash;
	private $newfname;
	private $dbname;
	private $extension;
	private $dimensions;
	private $tempfile;
	private $newfile;
	private $thumbfile;
	private $thumbcolor;
	private $thumbmime = 'image/jpg';
	private $thumbext;
	private $prefwidth = 581;
	private $prefheight = 367;
	private $existing = true;
	private $cropmode = 'CENTER';
	private $maxsize = 5242880;
	private $filesize;
	
	public function __construct($image) {
		$this->image = $image;
	}

	public function parse($thumb = false) {
		if ($this->image['error'] == UPLOAD_ERR_OK) {
			$temp = explode('.', $this->image['name']);
			$this->extension = strtolower(end($temp));
			if (in_array($this->image['type'], $this->allowedtypes) && in_array($this->extension, $this->allowedexts)) {
				return $this->parse_image($thumb);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Build the image
	 * @param boolean $resize
	 * @param boolean $thumb
	 * @param string $table The table that holds image info.
	 * @param string $name The column that stores the original filename of the image uploaded by the client.
	 * @param string $file The column that stores the new filename based on current timestamp.
	 * @return boolean Returns false if file conflict exists in temp folder.
	 */
	public function build($resize = true, $thumb = false, $table = 'images', $name = 'imagename', $file = 'imagefile') {
		if (!file_exists($this->tempfile)) {
			return $this->build_image($resize, $thumb, $table, $name, $file);
		} else {
			echo $this->image['name'].' already exists in temp folder. ';
			return false;
		}
	}
	
	private function parse_image($thumb) {
		$this->dimensions = getimagesize($this->image['tmp_name']);
		if ($this->dimensions[0] > 5000 || $this->dimensions[1] > 5000) {
			return false;
		}
		$newname = get_timestamp();
		$this->newfname = $newname[0].'.'.$this->extension;
		$this->tempfile = $this->tempfolder.$this->newfname;
		$this->newfile = $this->folderpath.$this->newfname;
		$this->thumbfile = $thumb ? $this->thumbfolder.'t'.$newname[0].'.'.$this->thumbext : '';
		$this->filesize = $this->image['size'];
		return true;
	}
	
	private function build_image($resize, $thumb, $table, $name, $file) {
		//move image to temp folder
		move_uploaded_file($this->image['tmp_name'], $this->tempfile);
		$this->imagehash = hash_file('md5', $this->tempfile);
		$this->check_db($table, $name, $file);
		if ($this->existing && file_exists($this->newfile)) {
			echo "File already exists. ";
//			echo "File already exists. Links redirected to $this->newfname. ";
		} else if (!file_exists($this->newfile)) {
			if ($resize) {
				if ($this->image['type'] != 'image/gif') {
					if ($temp = open_image($this->tempfile)) {
						$src = $temp[0];
						$canvas = inline_crop($src, $this->dimensions, $this->cropmode, $this->prefwidth, $this->prefheight);
						$this->save_image($this->image['type'], $canvas, $this->newfile);
						imagedestroy($canvas);
						imagedestroy($src);
					} else {
						unlink($this->tempfile);
						echo 'Error: image too large or invalid. ';
						return false;
					}
				} else {
					copy($this->tempfile, $this->newfile);
					echo 'Image uploaded! ';
				}
			} else {
				if ($thumb) {
					if ($temp = open_image($this->tempfile)) {
						$src = $temp[0];
						$canvas = make_thumbnail($src, $this->dimensions, $this->prefwidth, $this->prefheight, $this->thumbcolor);
						$this->save_image($this->thumbmime, $canvas, $this->thumbfile);
					} else {
						unlink($this->tempfile);
						echo 'Error: image too large or invalid. ';
						return false;
					}
				}
				copy($this->tempfile, $this->newfile);
			}
		}
		unlink($this->tempfile);
		$this->tempfile = null;
		return true;
	}
	
	private function check_db($table, $name, $file) {
		$db = new DBObject('newspum');
//		$sql = "SELECT * FROM images WHERE originalmd5 = '{$this->imagehash}'";
		$sql = sprintf("SELECT * FROM $table WHERE originalmd5 = '%s'", $this->imagehash);
		$result = $db->query($sql);
		if ($row = mysqli_fetch_array($result)) {
			$this->imagehash = $row['originalmd5'];
			$this->newfname = $row[$file]; //default row['imagefile']
			$this->newfile = $this->folderpath.$this->newfname;
			$temp = explode('.', $row[$file]);
			$this->thumbfile = $this->thumbfolder.'t'.$temp[0].'.'.$this->thumbext;
			$this->dbname = $db->escape(trim($row[$name])); //default row['imagename']
			$this->existing = true;
		} else {
			$this->dbname = $db->escape(trim($this->image['name']));
			$this->existing = false;
		}
	}

	private function save_image($mime, $file, $path) {
		switch($mime) {
			case 'image/jpg':
			case 'image/pjpeg':
			case 'image/jpeg': imagejpeg($file, $path, 100); echo 'Image uploaded! '; return true;
			case 'image/x-png':
			case 'image/png': imagepng($file, $path); echo 'Image uploaded! '; return true;
			default: return false;
		}
	}
	
	public function getAllowedExts() {
		return $this->allowedexts;
	}

	public function getAllowedTypes() {
		return $this->allowedtypes;
	}

	public function getFolderpath() {
		return $this->folderpath;
	}

	public function getTempfolder() {
		return $this->tempfolder;
	}

	public function getImage() {
		return $this->image;
	}

	public function getImagehash() {
		return $this->imagehash;
	}

	public function getNewfname() {
		return $this->newfname;
	}

	public function getDbname() {
		return $this->dbname;
	}

	public function getExtension() {
		return $this->extension;
	}

	public function getDimensions() {
		return $this->dimensions;
	}

	public function getTempfile() {
		return $this->tempfile;
	}

	public function getNewfile() {
		return $this->newfile;
	}
	
	public function getPrefWidth() {
		return $this->prefwidth;
	}

	public function getPrefHeight() {
		return $this->prefheight;
	}

	public function setPrefWidth($prefwidth) {
		$this->prefwidth = $prefwidth;
	}

	public function setPrefHeight($prefheight) {
		$this->prefheight = $prefheight;
	}
	
	public function setAllowedExts($allowedexts) {
		$this->allowedexts = $allowedexts;
	}

	public function setAllowedTypes($allowedtypes) {
		$this->allowedtypes = $allowedtypes;
	}

	public function setFolderpath($folderpath) {
		$this->folderpath = $folderpath;
	}

	public function setTempfolder($tempfolder) {
		$this->tempfolder = $tempfolder;
	}
	
	function getCropMode() {
		return $this->cropmode;
	}

	function setCropMode($cropmode) {
		$this->cropmode = $cropmode;
	}

	function getThumbfolder() {
		return $this->thumbfolder;
	}

	function setThumbfolder($thumbfolder) {
		$this->thumbfolder = $thumbfolder;
	}

	function getThumbfile() {
		return $this->thumbfile;
	}
	
	function getThumbcolor() {
		return $this->thumbcolor;
	}

	function setThumbcolor($thumbcolor) {
		$this->thumbcolor = $thumbcolor;
	}
	
	function getThumbmime() {
		return $this->thumbmime;
	}

	function setThumbmime($thumbmime) {
		$this->thumbmime = $thumbmime;
		switch ($thumbmime) {
			case 'image/x-png':
			case 'image/png': $this->thumbext = 'png'; break;
			default: $this->thumbext = 'jpg';
		}
		$this->parse_image(true);
	}
	
	function getMaxSize() {
		return $this->maxsize;
	}

	function setMaxSize($maxsize) {
		$this->maxsize = $maxsize;
	}

	function getFileSize() {
		return $this->filesize;
	}

}