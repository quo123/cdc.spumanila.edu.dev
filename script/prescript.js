String.prototype.str_replace = function(find, replace) {
	var replaceString = this;
	for (var i = 0; i < find.length; i++) {
		replaceString = replaceString.replace(new RegExp(find[i], "g"), replace[i]);
	}
	return replaceString;
};

function htmlDecode(str) {
	var ascii = ['&', '<', '>', '"', "'", '/', "\t", '+'];
	var htmlc = ['&amp;','&lt;', '&gt;', '&quot;', '&#x27;', '&#x2F;', '&emsp;', '&plus;'];
	str = str.str_replace(htmlc, ascii);
	return str;
}

function htmlEncode(str) {
	var ascii = ['[&]', '[<]', '[>]', '["]', "[']", '[/]', "[\t]", '[+]'];
	var htmlc = ['&amp;','&lt;', '&gt;', '&quot;', '&#x27;', '&#x2F;', '&emsp;', '&plus;'];
	str = str.str_replace(ascii, htmlc);
	return str;
}

function attrEscape(str) {
	str = str.replace('"', '\"');
	return str;
}

$.fn.scrollView = function() {
    return this.each(function() {
        $('html, body').animate({
            scrollTop: $(this).offset().top
        }, 200);
    });
};

//constants
var imgLoading = '<img src="/images/loading.gif" />';
var phpSeeker = '/ajax/seeker.php';
var phpActivity = '/ajax/activity.php';
var phpSave = '/ajax/save.php';
var phpStudentEval = '/ajax/student_eval.php';
var phpLogin = '/ajax/login.php';
var phpLogout = '/ajax/logout.php';
var phpRegister = '/ajax/register.php';
var phpGetStudentInfo = '/ajax/get_studentinfo.php';
var phpReports = '/ajax/reports.php';

//main function
$(function() {
	$('#tabs').tabs();
	
	$(document).tooltip({
		track: true,
		items: 'li.reqs',
		show: false,
		hide: false
	});
	
	var login = $('#login-form').dialog({
		resizable: false,
		autoOpen: false,
		height: 273,
		width: 300,
		modal: true,
		close: function() {
			$(this).find('form')[0].reset();
			reset(this, 'Login', '');
		}
	}).on('submit', function(e) {
		e.preventDefault();
		var thisthis = this;
		var loginbutton = $(this).find('input[type=submit]');
		var form = $(this).find('form')[0];
		var fd = $(form).serialize();
		var response = $(this).find('.response');
		
		$(loginbutton).val('').addClass('button-loading');
		if ($(form).data('submitting')) {
			return false;
		}
		$('.submit', form).prop('disabled', true);
		$(form).data('submitting', true);
		
		$.post(phpLogin, fd, function(data) {
			errorWrapper(data, function() {
				$(response).html(data);
				if (data.indexOf('Welcome back,') > -1) {
					login.dialog('option', 'closeOnEscape', false);
					setTimeout(function() {		
						login.dialog('close');
						window.location.replace('/');
					}, 2000);
				} else {
					reset(thisthis, 'Login');
				}		
			});
		});
	});
	
	var register = $('#register-form').dialog({
		resizable: false,
		autoOpen: false,
		height: 296,
		width: 300,
		modal: true,
		close: function() {
			$(this).find('form')[0].reset();
			reset(this, 'Register', '');
		}
	}).on('submit', function(e) {
		e.preventDefault();
		var thisthis = this;
		var loginbutton = $(this).find('input[type=submit]');
		var form = $(this).find('form')[0];
		var fd = $(form).serialize();
		var response = $(this).find('.response');
		
		$(loginbutton).val('').addClass('button-loading');
		if ($(form).data('submitting')) {
			return false;
		}
		$('.submit', form).prop('disabled', true);
		$(form).data('submitting', true);
		
		$.post(phpRegister, fd, function(data) {
			errorWrapper(data, function() {
				$(response).html(data);
				if (data === 'Registration successful!') {
					setTimeout(function() {
						register.dialog('close');
					}, 2000);
				} else {
					reset(thisthis, 'Register');
				}
			});
		});
	});
	
	$('#login-button').on('click', function() {
		login.dialog('open');
	});
	
	$('#register-button').on('click', function() {
		register.dialog('open');
	});
	
	$('#logout-button').on('click', function() {
		$.get(phpLogout, function(data) {
			alertWrapper(data, null, function() {
				window.location.reload();
			});
		});
	});
	
	var add = $('#add-form').dialog({
//		closeOnEscape: false,
		resizable: false,
		autoOpen: false,
		height: 510,
		width: 925,
		modal: true,
		close: function() {
			$(this).find('form')[0].reset();
			reset(this, '', 'Input student information.');
		},
		buttons: {
			"Add student": function() {
				$(this).find('input[type=submit]').click();
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	}).on('submit', function(e) {
		e.preventDefault();
		var thisthis = this;
		var form = $(this).find('form')[0];
		var fd = $(form).serialize();
		var response = $(this).find('.response');
		
		if ($(form).data('submitting')) {
			return false;
		}
		$('.submit', form).prop('disabled', true);
		$(form).data('submitting', true);
		$.post(phpGetStudentInfo, fd, function(data) {
			errorWrapper(data, function() {
				if (data === 'Record added!') {
					alertWrapper(data, null, function() {
						add.dialog('close');
					});
					studentSeeker(null, null, 'RECENT');
				} else {
					$(response).html(data);
					reset(thisthis, '');
				}
			});
		});
	});
	
	
	$('#add-student').button().on('click', function() {
		add.dialog('open');
	});
	
	$('#bday, #Bday').datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: "c-40:c"
	}).on('change', function() {		
		var bdate = toUTC($(this).val());
		var now = toUTC(Date.now());
		var age = Math.floor((now - bdate) / (1000*60*60*24*365.25));
		age = age > 0 ? age : 0;
		$('#age, #Age').val(age);
	});
	
//	console.log($("#student-summary-records table"));
	$('#student-summary-records table').tablesorter();
	var studentSummary = $('#student-summary').dialog({
		resizable: false,
		autoOpen: false,
		height: 420,
		width: 667,
		modal: true,
		open: function() {
			populateStudentTable($(this));
		},
		close: function() {
			$(this).find('table.records tbody').html('');
		},
		buttons: {
			Done: function() {
				$(this).dialog('close');
			}
		}
	});
	
	$('#searchbutton').hover(
		function() {
			$(this).addClass('ui-state-hover');
		},
		function() {
			$(this).removeClass('ui-state-hover');
		}
	).click(function() {
		var search = $('#searchfield').val().trim();
		if (search.length === 0) {
			studentSummary.dialog('open');
		} else {
			$('#searchbox').submit();
		}
	});
	
	if ($('#searchfield').length > 0) {
		studentSeeker(null, null, 'RECENT');
	}
	
	$('#searchfield').on('input', function() {
		var search = $(this).val();
		if (search.length === 0) {
			studentSeeker(null, null, 'RECENT');
			$('#searchfield').attr('data', '-1');
		} else if (search === 'ALL') {
			studentSeeker(null, null, 'ALL');
			$('#searchfield').attr('data', '-1');
		}
	});

	$('#searchbox').on('submit', function(e) {
		e.preventDefault();
		$('#search-results').html('<legend>Searching</legend>' + imgLoading);
		studentSeeker(null, null, $('#searchfield').val());
		$('#searchfield').attr('data', '-1');
	});
	
	$('#searchfield').autocomplete({
		source: phpSeeker,
		minLength: 1,
		autoFocus: true,
		select: studentSeeker
	});
	
	var edit = $('#edit-form').dialog({
		resizable: false,
		autoOpen: false,
		height: 410,
		width: 925,
		modal: true,
		close: function() {
			$(this).find('form')[0].reset();
			reset(this, '', 'Changes will be automatically saved.');
			edit.dialog().find('input, select').unbind('change.autoSave');
		},
		buttons: {
			Delete: function() {
				delWrapper(function() {
					var fd = {
						delstud: currentStudent
					};
//					console.log('deleted ' + currentStudent);
					$.post(phpActivity, fd, function(data) {
						errorWrapper(data, function() {
							alertWrapper(data, null, function() {
								edit.dialog('close');
							});
							studentSeeker(null, null, 'RECENT');
						});
					});
				});
			},
			Done: function() {
				$(this).dialog('close');
			}
		}
	}).on('submit', function(e) {
		e.preventDefault();
		$(this).dialog('close');
	});
	
	$('#Bday').on('change', function() {
		$('#Age').change();
	});
	
	var currentStudent;
	
	$('#search-results').on('click', '.result-edit', function() {
		var result;
		var id = $(this).attr('data');
		var fd = {edit: id};
		
		currentStudent = id;
		$.get(phpSeeker, fd, function(data) {
			errorWrapper(data, function() {
				result = JSON.parse(data);
				edit.dialog('open').find('input').attr('autocomplete', 'off');
				$.each(result, function(key, value) {
					edit.dialog().find('#' + key).val(value);
					edit.dialog().find('#' + key).on('focus', function() {
						$(this).addClass('edit-editing');
					}).focusout(function() {
						$(this).removeClass('edit-editing');
					});
					
					edit.dialog().find('#' + key).on('change.autoSave', function() {
						var thisthis = $(this);
						var validity = thisthis.prop('validity');
						if ((thisthis.val().trim() !== '') && validity.valid === true) {
							thisthis.removeClass('edit-editing edit-error');
							thisthis.addClass('edit-saved');
							var fd = $('#' + key).serialize();
							fd = "sid=" + result.sid + "&" + fd;
							$.post(phpSave, fd, function(data2) {
//								console.log(data2);
								refreshResults();
							});
							
							setTimeout(function() {
								thisthis.removeClass('edit-saved');
							}, 2000);
						} else {
							thisthis.addClass('edit-error');
						}
					});					
				});
				
			});
		});
		
	}).on('click', '.reqs', function(e) {
		currentStudent = $(this).parent('ul').siblings('.result-edit').attr('data');
		var listItem = $(this);
		var booleans = ['rp'];
		var req = listItem.attr('data-id');
		
		if (booleans.indexOf(req) > -1) {
			var newval = listItem.hasClass('good') ? 0 : 1;
			var post = 'sid=' + currentStudent + '&' + req + '=' + newval;
			$.post(phpSave, post, function(data) {
//				console.log(data);
				refreshResults();
			});
		} else if (req === 're') {
			var fd = {
				'completereqs': currentStudent
			};
			
			$.get(phpSeeker, fd, function(data) {
				errorWrapper(data, function() {
					editEval.dialog('open');
				});
			});
		} else if (req === 'ri') {
			oncampus.dialog('open');
			actType = 'oncampus';
		} else if (req === 'ro' || req === 'rc') {
			offcampus.dialog('open');
			actType = 'offcampus';
		}
	}).on('click', '.result-details', function(e) {
		currentStudent = $(this).siblings('.result-edit').attr('data');
		detailsMenu.show().position({
			my: 'left top',
			at: 'left bottom',
			of: this
		}).focus();
		
		return false;
	});
	
	var detailsMenu = $('#details-menu').menu().hide();
	detailsMenu.blur(function() {
		$(this).hide();
	});
	
	var actType;
	
	var oncampus = $('#oncampus-form').dialog({
		resizable: false,
		autoOpen: false,
		height: 450,
		width: 789,
		modal: true,
		open: function() {
			actType = 'oncampus';
			populateActTable('onid', currentStudent, $(this));
		},
		close: function() {
			$(this).find('table.records tbody').html('');
		},
		buttons: {
			Done: function() {
				$(this).dialog('close');
			}
		}
	});
	
	var oncampusForm = $('#oncampus-add').dialog({
		resizable: false,
		autoOpen: false,
		height: 390,
		width: 402,
		modal: true,
		open: function() {
			$('#onhours').val($('#slider-onhours').slider('value'));
			defaultSem('#oncampus-add');
		},
		close: function() {
			$(this).find('form')[0].reset();
			reset(this, '', '0000, ');
			resetBox($(this).find('.others'), function() {
				$('#slider-onhours').slider('option', 'value', 0);
			});
			$(this).dialog().find('input, select').unbind('change.autoSave');
		},
		buttons: {
			Add: function() {
				$(this).find('input[type=submit]').click();
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	}).on('submit', 'form', function(e) {
		e.preventDefault();
		var fd = $(this).serialize();
		fd += '&type=oncampus&student=' + currentStudent;
		$.post(phpActivity, fd, function(data) {
//			console.log(data);
			if (data.indexOf('Record added!') > -1) {
				alertWrapper('Record added!');
				oncampusForm.dialog('close');
				refreshResults();
				populateActTable('onid', currentStudent, $('#oncampus-form'));
			} else {
				alertWrapper(data);
			}
		});
	}).on('change', 'select', function() {
		var otherBox = $('#on-' + $(this).attr('name'));
		if ($(this).val() === 'others') {
			otherBox
				.prop('disabled', false)
				.attr('placeholder', 'Please specify')
				.removeClass('invisible')
				.addClass('focusglow')
				.prop('required', true);
		} else {
			resetBox(otherBox);
		}
	}).on('change.autoSave', '.year', function() {
		var year = parseInt($(this).val()) + 1;
		$('.response').text(year + ', ');
	});
	
	$('#slider-onhours, #slider-edit-onhours').slider({
		range: 'min',
		value: 0,
		min: 0,
		max: 8,
		step: 0.5,
		slide: function(e, ui) {
			$('#onhours, #onhours-edit').val(ui.value);
			if (ui.value < 8) {
				$(this).find('.ui-slider-range').removeClass('greenslider').addClass('redslider');
			} else {
				$(this).find('.ui-slider-range').removeClass('redslider').addClass('greenslider');				
			}
		}
	});
	
	
	$('#oncampus-menu').on('click', function() {
		oncampus.dialog('open');
		actType = 'oncampus';
	});
	
	$('#oncampus-button').button().on('click', function() {
		oncampusForm.dialog('open');
	});
	
	
	var offcampus = $('#offcampus-form').dialog({
		resizable: false,
		autoOpen: false,
		height: 450,
		width: 1149,
		modal: true,
		open: function() {
			actType = 'offcampus';
			populateActTable('offid', currentStudent, $(this));
		},
		close: function() {
			$(this).find('table.records tbody').html('');
		},
		buttons: {
			Done: function() {
				$(this).dialog('close');
			}
		}
	});
	
	var offcampusForm = $('#offcampus-add').dialog({
		resizable: false,
		autoOpen: false,
		height: 470,
		width: 402,
		modal: true,
		open: function() {
			$('#offhours').val($('#slider-offhours').slider('value'));
			defaultSem('#offcampus-add');
		},
		close: function() {
			$(this).find('form')[0].reset();
			reset(this, '', '0000, ');
			resetBox($(this).find('.others'), function() {
				$('#slider-offhours').slider('option', 'value', 0);
			});
			$(this).dialog().find('input, select').unbind('change.autoSave');
		},
		buttons: {
			Add: function() {
				$(this).find('input[type=submit]').click();
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	}).on('submit', 'form', function(e) {
		e.preventDefault();
		var fd = $(this).serialize();
		fd += '&type=offcampus&student=' + currentStudent;
		$.post(phpActivity, fd, function(data) {
//			console.log(data);
			if (data.indexOf('Record added!') > -1) {
				alertWrapper('Record added!');
				offcampusForm.dialog('close');
				refreshResults();
				populateActTable('offid', currentStudent, $('#offcampus-form'));
			} else {
				alertWrapper(data);
			}
		});
	}).on('change', 'select', function() {
		var otherBox = $('#off-' + $(this).attr('name'));
		if ($(this).val() === 'others') {
			otherBox
				.prop('disabled', false)
				.attr('placeholder', 'Please specify')
				.removeClass('invisible')
				.addClass('focusglow')
				.prop('required', true);
		} else {
			resetBox(otherBox);
		}
	}).on('change.autoSave', '.year', function() {
		var year = parseInt($(this).val()) + 1;
		$('.response').text(year + ', ');
	});
	
	$('#slider-offhours, #slider-edit-offhours').slider({
		range: 'min',
		value: 0,
		min: 0,
		max: 4,
		step: 0.5,
		slide: function(e, ui) {
			$('#offhours, #offhours-edit').val(ui.value);
			if (ui.value < 4) {
				$(this).find('.ui-slider-range').removeClass('greenslider').addClass('redslider');
			} else {
				$(this).find('.ui-slider-range').removeClass('redslider').addClass('greenslider');				
			}
		}
	});
	
	$('#offcampus-menu').on('click', function() {
		offcampus.dialog('open');
		actType = 'offcampus';
	});
	
	$('#offcampus-button').button().on('click', function() {
		offcampusForm.dialog('open');
	});
	
	var actNameMenuOff = $('#actname-menu-off').menu().hide();
	actNameMenuOff.blur(function() {
		$(this).hide();
	});
	
	var actNameMenuOn = $('#actname-menu-on').menu().hide();
	actNameMenuOn.blur(function() {
		$(this).hide();
	});
	
	var currentAct;
	var currentSched;
	var currentEvalForm;
	$('.records').on('click', '.t-actname', function(e) {
		var form = $(this).parents('div.account-form').attr('id');
		var menu = form === 'offcampus-form' ? actNameMenuOff : actNameMenuOn;
		actType = form.substring(0, form.indexOf('-'));
		menu.show().position({
			my: 'left top',
			at: 'right bottom',
			of: e
		}).focus();
		currentAct = $(this).parents('tr').attr('data-id');
		
		return false;
		
	}).on('click', '.t-start, .t-end', function(e) {
		currentSched = $(this).parents('.table-rows').attr('data-id');
		schedMenu.show().position({
			my: 'left top',
			at: 'right bottom',
			of: e
		}).focus();
		
		return false;
	}).on('click', '.t-category', function() {
		clickEdit(this, editCategory, 'category');
	}).on('click', '.t-initiator', function() {
		clickEdit(this, editInitiator, 'initiator');
	}).on('click', '.t-organizer', function() {
		clickEdit(this, editOrganizer, 'organizer');
	}).on('click', '.t-address', function() {
		clickEdit(this, editAddress, 'address');
	}).on('click', '.t-pointperson', function() {
		clickEdit(this, editPointperson, 'pointperson');
	}).on('click', '.t-contact', function() {
		clickEdit(this, editContact, 'contact');
	}).on('click', '.t-hours', function() {
		if (actType === 'oncampus') {
			clickEdit(this, editOnHours, 'hours', 8);
		} else if (actType === 'offcampus') {
			clickEdit(this, editOffHours, 'hours', 4);
		}
	}).on('click', '.t-cert', function() {
		clickEdit(this, null, 'cert');
	}).on('click', '.t-eval-schoolyear, .t-eval-semester', function(e) {
		currentEvalForm = $(this).parents('.table-rows').attr('data-id');
		evalMenu.show().position({
			my: 'left top',
			at: 'right bottom',
			of: e
		}).focus();
		return false;
	});
	
	var schedMenu = $('#schedule-menu').menu().hide();
	schedMenu.blur(function() {
		$(this).hide();
	});
	
	$('.sched-delete-menu').on('click', function() {
		var fd = {'delete': currentSched};
		$.post(phpActivity, fd, function(data) {
			if (data.indexOf('Record deleted!') > -1) {
				alertWrapper('Schedule deleted!');
				var newfd = {actid: currentAct, type: actType};
				populateSchedTable(newfd, $('#schedule-form'));				
			} else {
				alertWrapper(data);
			}
		});
	});
	
	var schedule = $('#schedule-form').dialog({
		resizable: false,
		autoOpen: false,
		height: 300,
		width: 409,
		modal: true,
		open: function() {
			var dis = $(this);
			var fd = {actid: currentAct, type: actType};
			populateSchedTable(fd, dis);
		},
		close: function() {
			$(this).find('table.records tbody').html('');
		},
		buttons: {
			Done: function() {
				$(this).dialog('close');
			}
		}
	});
	
	var scheduleAdd = $('#schedule-add').dialog({
		resizable: false,
		autoOpen: false,
		height: 210,
		width: 370,
		modal: true,
		close: function() {
			$(this).find('form')[0].reset();
			reset(this, '');
		},
		buttons: {
			Add: function() {
				$(this).find('input[type=submit]').click();
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	}).on('submit', 'form', function(e) {
		e.preventDefault();
		var fd = $(this).serialize();
		fd += '&type=' + actType + "&actid=" + currentAct;
		$.post(phpActivity, fd, function(data) {
//			console.log(data);
			errorWrapper(data, function() {
				if (data.indexOf('Record added!') > -1) {
					alertWrapper('Schedule added!');
					scheduleAdd.dialog('close');
					populateSchedTable(fd, $('#schedule-form'));
				} else {
					alertWrapper(data);
				}
			});
		});
	});
	
	$('.act-sched-menu').on('click', function() {
		schedule.dialog('open');
	});
	
	$('#addsched-button').button().on('click', function() {
		scheduleAdd.dialog('open');
	});
	
	$('#sched-start, #sched-end').datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: "c-10:c+1"
	});
	
	$('.act-delete-menu').on('click', function() {
		delWrapper(function() {
			var fd = {
				delact: currentAct,
				type: actType
			};
			$.post(phpActivity, fd, function(data) {
				errorWrapper(data, function() {
					alertWrapper(data);
					if (actType === 'oncampus') {
						populateActTable('onid', currentStudent, $('#oncampus-form'));
					} else {
						populateActTable('offid', currentStudent, $('#offcampus-form'));
					}
				});
			});
		});
	});

	//One-click editing of activities
	var editActName = standardEditBox('#edit-actname-form', 155, 395);
	$('.act-edit-menu').on('click', function() {
		var fd = {
			'editact': currentAct,
			'type': actType,
			'field': 'actname'
		};
		
		$.get(phpSeeker, fd, function(data) {
			editActName.dialog('open').find('.edit-textbox').val(data);
		});
	});
	
	var editCategory = standardEditBox('#edit-category-form', 155, 405);
	var editInitiator = standardEditBox('#edit-initiator-form', 155, 405);
	var editOrganizer = standardEditBox('#edit-organizer-form', 155, 395);
	var editAddress = standardEditBox('#edit-address-form', 155, 395);
	var editPointperson = standardEditBox('#edit-pointperson-form', 155, 275);
	var editContact = standardEditBox('#edit-contact-form', 155, 275);
	var editOnHours = standardEditBox('#edit-onhours-form', 155, 275);
	var editOffHours = standardEditBox('#edit-offhours-form', 155, 275);
	
	var settings = $('#settings-form').dialog({
		resizable: false,
		autoOpen: false,
		height: 260,
		width: 300,
		modal: true,
		open: function() {
			$.get(phpSeeker + '?editsettings', function(data) {
//				console.log(data);
				errorWrapper(data, function() {
					var result = JSON.parse(data);
					$.each(result, function(key, value) {
						$('#settings-' + key).val(value);
					});
					$('#endyear').text(result.year + 1);
				});
			});
		},
		close: function() {
			$(this).find('form')[0].reset();
			reset(this, '', 'Set current school year and semester.');
			$(this).dialog().find('input, select').unbind('change.autoSave');
		},
		buttons: {
			Done: function() {
				$(this).dialog('close');
			}
		}
	}).on('submit', function(e) {
		e.preventDefault();
		$(this).dialog('close');
	}).on('change.autoSave', '#settings-year', function() {
		var year = parseInt($(this).val()) + 1;
		$('#endyear').text(year);
		autoSaver(this);
	}).on('change.autoSave', '#settings-sem', function() {
		autoSaver(this);
	});
	
	$('#settings-button').on('click', function() {
		settings.dialog('open');
	});
	
	var editEval = $('#edit-eval-form').dialog({
		resizable: false,
		autoOpen: false,
		height: 190,
		width: 375,
		modal: true,
		open: function() {
			var thisthis = $(this);
			var fd = {
				'evalcode': currentStudent
			};
			
			$.get(phpSeeker, fd, function(data) {
				errorWrapper(data, function() {
					thisthis.find('#eval-request').val(data);
					if (data === 'OK') {
						thisthis.find('.noeval').addClass('nodisplay');
					} else {
						thisthis.find('.noeval').removeClass('nodisplay');
					}
				});
			});
			
			$.get(phpSeeker + '?editsettings', function(data) {
				errorWrapper(data, function() {
					var result = JSON.parse(data);
					var endyear = result.year + 1;
					$(thisthis).siblings('.ui-dialog-titlebar')
							.children('.ui-dialog-title')
							.html('Evaluation (' + toOrdinal(result.sem) + ' Semester, A.Y. ' + result.year + '&ndash;' + endyear + ')');
				});
			});
		},
		close: function() {
			$(this).find('form')[0].reset();
			reset(this, '');
		},
		buttons: {
			Done: function() {
				$(this).dialog('close');
			}
		}
	}).on('submit', function(e) {
		e.preventDefault();
	});
	
	$('#eval-request-button').button().on('click', function() {
		var form = $('#edit-eval-form');
		var fd = {
			genreq: currentStudent
		};
		$.get(phpSeeker, fd, function(data) {
			errorWrapper(data, function() {
				form.find('#eval-request').val(data);
			});
		});
	});
	
	var evalMenu = $('#eval-menu').menu().hide();
	evalMenu.blur(function() {
		$(this).hide();
	});
	
	var viewEval = $('#view-eval').dialog({
		resizable: false,
		autoOpen: false,
		height: 270,
		width: 309,
		modal: true,
		open: function() {
			var dis = $(this);
			var fd = {vieweval: currentStudent};
			populateEvalTable(fd, dis);
		},
		close: function() {
			$(this).find('table.records tbody').html('');
		},
		buttons: {
			Done: function() {
				$(this).dialog('close');
			}
		}
	});
	
	$('#eval-view-button').button().on('click', function() {
		viewEval.dialog('open');
	});
	
	$('.eval-view-menu').on('click', function() {
		viewEvalForm.dialog('open');
	});
	
	$('.eval-delete-menu').on('click', function() {
		delWrapper(function() {
			var fd = {deleval: currentEvalForm};
			$.post(phpActivity, fd, function(data) {
				errorWrapper(data, function() {
					alertWrapper(data);
					populateEvalTable({vieweval: currentStudent}, $('#view-eval'));
				});
			});			
		});
	});
	
	var viewEvalForm = $('#view-eval-form').dialog({
		resizable: false,
		autoOpen: false,
		height: 550,
		width: 700,
		modal: true,
		open: function() {
			var dis = $(this);
			var fd = {viewevalform: currentEvalForm};
			$.get(phpSeeker, fd, function(data) {
				if (!errorWrapper(data, function() {
					var result = JSON.parse(data);
					$.each(result, function(key, val) {
						dis.find('#' + key).val(val);
					});
					
					dis.siblings('.ui-dialog-titlebar').children('.ui-dialog-title').html(formatYearSem(result.schoolyear, result.semester));
					
					var sliders = dis.find('.rating-slider');
					$.each(sliders, function(key, val) {
						var rating = $(val).siblings('.invisible.hourbox').val();
						$(val).slider('option', 'value', rating).slider('option', 'disabled', true);
						if (rating < 3) {
							$(val).find('.ui-slider-range').removeClass('greenslider').addClass('redslider');
							$(val).siblings('.rate-threshold').show();
						} else {
							$(val).find('.ui-slider-range').removeClass('redslider').addClass('greenslider');
							$(val).siblings('.rate-threshold').hide();
						}
					});
					dis.animate({
						scrollTop: 0
					}, 200);
				})) {
					viewEvalForm.dialog('close');
				};
			});
		},
		close: function() {
			$(this).find('form')[0].reset();
			$(this).siblings('.ui-dialog-titlebar').children('.ui-dialog-title').html('Evaluation');
			reset(this, '');
			resetArea($(this).find('.rate-threshold textarea'), function() {
				$('.rating-slider').slider('option', 'value', 4);
			});			
		},
		buttons: {
			Close: function() {
				$(this).dialog('close');
			}
		}
	}).on('submit', function(e) {
		e.preventDefault();
	});
	
	var reportType = 0;
	var currentQuest = 1;
	$('#report-questions').selectmenu({
		width: 120,
		select: function(event, ui) {
			currentQuest = ui.item.value;
			var fd = {question: currentQuest, type: reportType};
			populateReports(fd);
		}
	});
		
	$('#report-mod').selectmenu({
		width: 95,
		select: function(event, ui) {
			reportType = ui.item.label === 'Current' ? 0 : 1;
			var fd = {question: currentQuest, type: reportType};
			populateReports(fd);
		}
	});
	
	//Shortcut functions
	
	/**
	 * Standard Edit Box
	 * @param {type} selector
	 * @param {type} height
	 * @param {type} width
	 * @returns {unresolved}
	 */
	function standardEditBox(selector, height, width) {
		return $(selector).dialog({
			resizable: false,
			autoOpen: false,
			height: height,
			width: width,
			modal: true,
			close: function() {
				$(this).find('form')[0].reset();
				reset(this, '');
				resetBox($(this).find('.others'), function() {
					$('.edit-slider').slider('option', 'value', 0);
				});
				$(this).find('input, select').unbind('change.autoSave');
			},
			buttons: {
				Save: function() {
					$(this).find('input[type=submit]').click();
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		}).on('submit', 'form', function(e) {
			e.preventDefault();
			var fd = $(this).serialize();
			fd += '&edit=&type=' + actType + "&actid=" + currentAct;
			$.post(phpActivity, fd, function(data) {
				errorWrapper(data, function() {
//					console.log(data);
					$(selector).dialog('close');
					if (actType === 'oncampus') {
						populateActTable('onid', currentStudent, $('#oncampus-form'));
					} else {
						populateActTable('offid', currentStudent, $('#offcampus-form'));
					}
					refreshResults();
				});
			});
		}).on('change', 'select', function() {
			var otherBox = $(this).siblings('.edit-textbox');
			if ($(this).val() === 'others') {
				otherBox
					.prop('disabled', false)
					.attr('placeholder', 'Please specify')
					.removeClass('invisible')
					.addClass('focusglow')
					.prop('required', true);
			} else {
				resetBox(otherBox);
			}
		});
	}
	
	/**
	 * Click-to-edit table cells
	 * @param {type} dis
	 * @param {type} dialogBox
	 * @param {type} field
	 * @param {type} limit
	 * @returns {undefined}
	 */
	function clickEdit(dis, dialogBox, field, limit) {
		var form = $(dis).parents('div.account-form').attr('id');
		actType = form.substring(0, form.indexOf('-'));
		currentAct = $(dis).parents('tr').attr('data-id');
		
		var fd = {
			'editact': currentAct,
			'type': actType,
			'field': field
		};
		if (dialogBox !== null) {
			$.get(phpSeeker, fd, function(data) {
//				console.log(data);
				if (dialogBox.dialog().find('.edit-combobox ').length > 0) {
					var standardCats = [];
					dialogBox.dialog().find('.edit-combobox > option').each(function() {
						var value = $(this).val();
						if (value !== '' && value !== 'others') {
							standardCats.push(value);
						}
					});

					dialogBox.dialog('open');
					if (standardCats.indexOf(data) > -1) {
						dialogBox.dialog().find('.edit-combobox').val(data);					
					} else {
						dialogBox.dialog().find('.edit-combobox').val('others');
						dialogBox.dialog().find('.edit-combobox').trigger('change');
						dialogBox.dialog().find('.edit-textbox').focus().val(data);
					}	
				} else {
					dialogBox.dialog('open').find('.edit-textbox').val(data);
				}

				if (limit !== null) {
					if (data < limit) {
						dialogBox.dialog().find('.ui-slider-range').removeClass('greenslider').addClass('redslider');
					} else {
						dialogBox.dialog().find('.ui-slider-range').removeClass('redslider').addClass('greenslider');				
					}
					dialogBox.dialog().find('.edit-slider').slider('option', 'value', data);
				}			
			});			
		} else {
			var boolMap = {NONE: 1, OK: 0};
			var boolText = $(dis).text();
			fd = {
				edit: '',
				type: actType,
				actid: currentAct,
				cert: boolMap[boolText]
			}
			
			$.post(phpActivity, fd, function(data) {
				errorWrapper(data, function() {
//					console.log(data);
					if (actType === 'oncampus') {
						populateActTable('onid', currentStudent, $('#oncampus-form'));
					} else {
						populateActTable('offid', currentStudent, $('#offcampus-form'));
					}
					refreshResults();
				});
			});
		}
	}
	
	
	var currentEval;
	$('#evaluate').button({
		icons: { primary: 'ui-icon-contact' }
	}).on('click', function() {
		getEvalform.dialog('open');
	});
	
	var getEvalform = $('#get-eval-form').dialog({
		dialogClass: 'no-close',
		resizable: false,
		autoOpen: false,
		height: 160,
		width: 200,
		modal: true,
		close: function() {
			$(this).find('form')[0].reset();
			reset(this, '');
		},
		buttons: {
			Go: function() {
				$(this).find('input[type=submit]').click();
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	}).on('submit', function(e) {
		e.preventDefault();
		var fd = $(this).find('form').serialize();
		$.post(phpStudentEval, fd, function(data) {
			errorWrapper(data, function() {
				var result = JSON.parse(data);
				currentEval = result.id;
				var title = result.name + ' [' + result.course + '-' + result.year + '] (' + formatYearSem(result.schoolyear, result.semester) + ')';
				getEvalform.dialog('close');
				$('#eval-form').siblings('.ui-dialog-titlebar').children('.ui-dialog-title').html(title);
				evalForm.dialog('open');
			});
		});
	});
	
	var evalForm = $('#eval-form').dialog({
		resizable: false,
		autoOpen: false,
		height: 550,
		width: 700,
		modal: true,
		open: function() {
			$(this).find('.rate-threshold').hide();
			$(this).find('.ui-slider-range').removeClass('redslider').addClass('greenslider');
			resetArea($(this).find('.rate-threshold textarea'));
			$(this).animate({
				scrollTop: 0
			}, 200);
		},
		close: function() {
			$(this).find('form')[0].reset();
			$(this).siblings('.ui-dialog-titlebar').children('.ui-dialog-title').html('Evaluation');
			reset(this, '');
			resetArea($(this).find('.rate-threshold textarea'), function() {
				$('.rating-slider').slider('option', 'value', 4);
			});			
		},
		buttons: {
			Submit: function() {
				$(this).find('input[type=submit]').click();
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	}).on('submit', function(e) {
		e.preventDefault();
		var fd = $(this).find('form').serializeArray();
		fd.push({name: 'evaluation', value: currentEval});
		$.post(phpStudentEval, fd, function(data) {
			errorWrapper(data, function() {
				alertWrapper(data, 'Evaluation', function() {
					evalForm.dialog('close');
				});
			});
		});
	});
	
	$('#slider-rate-act, #slider-rate-part').slider({
		range: 'min',
		value: 4,
		min: 1,
		max: 4,
		slide: function(e, ui) {
			var dis = $(this);
			dis.siblings('.invisible.hourbox').val(ui.value);
			if (ui.value < 3) {
				dis.find('.ui-slider-range').removeClass('greenslider').addClass('redslider');
				dis.siblings('.rate-threshold').show(200, function() {
					$(this).find('textarea').prop('disabled', false).prop('required', true);
					if (dis.siblings('.changedummy').val() === '0') {
						dis.siblings('.changedummy').val('1').trigger('change');
					}
				});
			} else {
				dis.find('.ui-slider-range').removeClass('redslider').addClass('greenslider');
				resetArea(dis.siblings('.rate-threshold').find('textarea'), function() {
					dis.siblings('.rate-threshold').hide(200, function() {
						if (dis.siblings('.changedummy').val() === '1') {
							dis.siblings('.changedummy').val('0').trigger('change');
						}
					});
				});	
			}
		}
	});
});

function formatYearSem(schoolyear, semester) {
	var nextyear = parseInt(schoolyear) + 1;
	var schoolyear = schoolyear + '&ndash;' + nextyear;
	var sem = parseInt(semester);
	return toOrdinal(sem) + ' Semester, A.Y. ' + schoolyear;
}

function populateEvalTable(fd, dis) {
	var keys = {schoolyear: 'School Year', semester: 'Semester'};
	$.get(phpSeeker, fd, function(data) {
		var result = JSON.parse(data);
		var table = $(dis).find('table.records tbody').html('');

		$.each(result, function(act, details) {
			var temp = $(table).append('<tr>');
			var row = $(temp).find('tr:last').addClass('table-rows');

			$.each(details, function(key, val) {
				if (key !== 'id') {
					row.append('<td>');
					var col = row.find('td:last');
					col.text(val);
					col.attr('title', keys[key]).addClass('t-eval-' + key);
				} else if (key === 'id') {
					row.attr('data-id', val);
				}
			});
		});
	});
}

function populateSchedTable(fd, dis) {
	var keys = {start: 'Start Time', end: 'End Time'};
	$.get(phpSeeker + '?getsched', fd, function(data) {
		var result = JSON.parse(data);
		var table = $(dis).find('table.records tbody').html('');

		$.each(result, function(act, details) {
			var temp = $(table).append('<tr>');
			var row = $(temp).find('tr:last').addClass('table-rows');

			$.each(details, function(key, val) {
				if (key !== 'dateid') {
					row.append('<td>');
					var col = row.find('td:last');
					col.text(val);
					col.attr('title', keys[key]).addClass('t-' + key);
				} else if (key === 'dateid') {
					row.attr('data-id', val);
				}
			});
		});
	});
}

function populateActTable(actid, currentStudent, dis) {
	var fd, validcols;
	if (actid === 'offid') {
		fd = {acts: 'offcampus', sid: currentStudent};
		validcols = {
			'actname':'Activity Name',
			'organizer':'Organizer',
			'address':'Address',
			'pointperson':'Point Person',
			'contact':'Contact Number',
			'schoolyear':'School Year',
			'semester':'Semester',
			'hours':'Hours Served',
			'cert':'Certificate'
		};
		
	} else if (actid === 'onid') {
		fd = {acts: 'oncampus', sid: currentStudent};
		validcols = {
			'actname':'Activity Name',
			'category':'Category',
			'initiator':'Initiator',
			'schoolyear':'School Year',
			'semester':'Semester',
			'hours':'Hours Served'
		};
	}
	
	var keys = Object.getOwnPropertyNames(validcols);
	
	var settings;
	$.get(phpSeeker, 'editsettings', function(data) {
		var jdata = JSON.parse(data);
		settings = jdata.year + '.' + jdata.sem;
	});
	
	$.get(phpSeeker, fd, function(data) {
		var result = JSON.parse(data);
		var table = $(dis).find('table.records tbody').html('');

		$.each(result, function(act, details) {
			var temp = $(table).append('<tr>');
			var row = $(temp).find('tr:last').addClass('table-rows');

			var yearsem = details.schoolyear + '.' + details.semester;			
			if (parseFloat(settings) > parseFloat(yearsem)) {
				row.addClass('old');
			}

			$.each(details, function(key, val) {
				if ($.inArray(key, keys) > -1) {
					row.append('<td>');
					var col = row.find('td:last');
					switch (key) {
						case 'schoolyear': col.html(val + ' &ndash; ' + (parseInt(val) + 1)); break;
						case 'eval':
						case 'cert': col.text(val === '0' ? 'NONE' : 'OK'); break;
						default: col.text(val);
					}
					col.attr('title', validcols[key]).addClass('t-' + key);
				} else if (key === actid) {
					row.attr('data-id', val);
				}
			});
		});
	});
}

function populateStudentTable(dis) {
	var keys = {lname: 'Last Name', fname: 'First Name', mname: 'Middle Initial', course: 'Course'};
	$.get(phpSeeker + '?allstudents', function(data) {
		var result = JSON.parse(data);
		var table = $(dis).find('table.records tbody').html('');

		$.each(result, function(act, details) {
			var temp = $(table).append('<tr>');
			var row = $(temp).find('tr:last').addClass('table-rows');

			$.each(details, function(key, val) {
				if (key !== 'sid') {
					row.append('<td>');
					var col = row.find('td:last');
					col.text(val);
					col.attr('title', keys[key]).addClass('t-' + key);
				} else if (key === 'sid') {
					row.attr('data-id', val);
				}
			});
		});
		
		$('#student-summary-records table').trigger('update');
	});
}

function populateReports(fd) {
	$('#report-results').html('<legend>Collecting answers</legend>'+ imgLoading);

	$.get(phpReports, fd, function(data) {
		$('#report-results').html('<legend>Answers</legend>');
		errorWrapper(data, function() {
			console.log(data);
			var result = JSON.parse(data);
			$('#quest').text(result.quest);
			$.each(result.ans, function(key, val) {
//				console.log(val);
				var answerbox = $('<div>').addClass('resultbox noselect');
				var fullname = $('<span>').text(val.fullname).addClass('report-fullname');
				var endyear = parseInt(val.year) + 1;
				var yearsem = $('<span>').text(toOrdinal(parseInt(val.sem)) + ' semester, A.Y. ' + val.year + ' - ' + endyear).addClass('report-yearsem');

				var answer = val.answer2 === null ? val.answer : (parseInt(val.answer) > 0 ? 'Rating: ' + val.answer : 'Core Value: ' + val.answer);
				var answer1 = $('<p>').text(answer).addClass('report-answer good');
				if (parseInt(val.answer) > 0) {
					if (parseInt(val.answer) < 3) {
						answer1.removeClass('good').addClass('bad');
					}
				}
				
				if (val.answer2 === null) {
					answer1.removeClass('good bad');
				}
				
				var answer2 = val.answer2 !== null ? $('<p>').text(val.answer2).addClass('report-answer') : null;

				answerbox.append(fullname, yearsem, answer1, answer2);

				$('#report-results').append(answerbox);
			});
		});
	});
}

function defaultSem(id) {
	$.get(phpSeeker + '?editsettings', function(data) {
		errorWrapper(data, function() {
			var result = JSON.parse(data);
			var next = parseInt(result.year) + 1;
			$(id).find('.year').val(result.year);
			$(id).find('.response').text(next + ', ');
			$(id).find('.sem').val(result.sem);
		});
	});
}

function toOrdinal(n) {
	var last = n.toString().charAt(n.length);
	switch (last) {
		case '1': return n + 'st';
		case '2': return n + 'nd';
		case '3': return n + 'rd';
		default: return n + 'th';
	}
}

function resetBox(box, f) {
	if (f && typeof f  === 'function') { f(); }
	return box
		.prop('disabled', true)
		.val('')
		.attr('placeholder', '')
		.removeClass('focusglow')
		.addClass('invisible')
		.prop('required', false);
}

function resetArea(area, f) {
	if (f && typeof f  === 'function') { f(); }
	return area
		.prop('disabled', true)
		.val('')
		.prop('required', false);
}

function autoSaver(element) {
	var thisthis = $(element);
	var validity = thisthis.prop('validity');
	if ((thisthis.val().trim() !== '') && validity.valid === true) {
		thisthis.removeClass('edit-editing edit-error');
		thisthis.addClass('edit-saved');
		var fd = thisthis.serialize();
		$.post(phpSave, fd, function(data) {
//			console.log(data);
			refreshResults();
		});

		setTimeout(function() {
			thisthis.removeClass('edit-saved');
		}, 2000);
	} else {
		thisthis.addClass('edit-error');
	}
}

function toUTC(dateString) {
	var date = new Date(dateString);
	return Date.UTC(date.getFullYear(),
		date.getMonth(),
		date.getDate(),
		date.getHours(),
		date.getMinutes(),
		date.getSeconds(),
		date.getMilliseconds()
	);
};

function refreshResults() {
	if ($('#searchfield').val().trim() === '') {
		studentSeeker(null, null, 'RECENT');
	} else {
		if ($('#searchfield').attr('data') > 0) {
			studentSeeker(null, null, $('#searchfield').attr('data'));
		} else {
			studentSeeker(null, null, $('#searchfield').val());
		}
	}
}

function studentSeeker(e, ui, x) {
	var search = x;
	if (ui) {
		search = ui.item.id;
		$('#searchfield').attr('data', ui.item.id);
	}
	var fd = {student: search};
	$.get(phpSeeker, fd, function(data) {
		errorWrapper(data, function() {
			$('#search-results').html(data);
		});
	});
}

function reset(form, button, text) {
	$('.submit', form).prop('disabled', false);
	$(form).find('form').data('submitting', false);
	$(form).find('input[type=submit]').removeClass('button-loading');
	$(form).find('input[type=submit]').removeAttr('style');
	$(form).find('input[type=submit]').val(button);
	if (text || text === '') $(form).find('.response').html(text);
}

/* Wrapper functions */

function errorWrapper(data, f, title, f2) {
	if (data.indexOf('Error:') > -1) {
		title = !title || title.trim() === '' ? 'Error' : title;
		alertWrapper(data.substring(6), title, f2);
		return false;
	} else {
		f();
		return true;
	}
}

function delWrapper(f) {
	if (f && typeof f === 'function') {
		return $('#confirm-delete-form').dialog({
			resizable: false,
			autoOpen: true,
			height: 160,
			width: 375,
			modal: true,
			dialogClass: 'no-close',
			close: function() {
				$(this).find('form')[0].reset();
				reset(this, '');
			},
			buttons: {
				Delete: function() {
					$(this).find('input[type=submit]').click();
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		}).on('submit', function(e) {
			e.preventDefault();
			var text = $(this).find('.focusglow').val();
			text = text.trim().toLowerCase();
			if (text === 'delete') {
				f();
				$(this).dialog('close');
			}
		});
	} else {
		return false
	}
}

function alertWrapper(message, title, f) {
	title = !title || title.trim() === '' ? 'Alert' : title;
	var alertHTML = '<div id="modal-alert-dialog" class="account-form no-close" title="' + title + '"><p id="modal-alert-msg">Alert!</p></div>';
	return $(alertHTML).dialog({
		resizable: false,
		autoOpen: true,
		minHeight: 120,
		minWidth: 200,
		modal: true,
		dialogClass: "no-close",
		open: function() {
			$(this).find('#modal-alert-msg').text(message);
		},
		close: function() {
			if (f && typeof f  === 'function') { f(); }
		},
		buttons: {
			Ok: function() {
				$(this).dialog('close');
			}
		}
	});
}

function confirmWrapper(message, title, f) {
	title = !title || title.trim() === '' ? 'Confirm' : title;
	var confirmHTML = '<div id="modal-confirm-dialog" class="account-form" title="' + title + '"><form><p id="modal-confirm-msg">Confirm?</p><input type="submit" tabindex="-1" class="dummy-submit"></form></div>';
	if (f && typeof f === 'function') {
		return $(confirmHTML).dialog({
			resizable: false,
			autoOpen: true,
			minHeight: 120,
			minWidth: 300,
			modal: true,
			dialogClass: "no-close",
			open: function() {
				$(this).find('#modal-confirm-msg').text(message);
			},
			close: function() {
				$(this).find('form')[0].reset();
			},
			buttons: {
				Ok: function() {
					$(this).find('input[type=submit]').click();
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		}).on('submit', function(e) {
			e.preventDefault();
			f();
			$(this).dialog('close');
		});
	} else {
		return false
	}
}