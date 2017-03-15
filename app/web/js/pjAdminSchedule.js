var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateSchedule = $("#frmCreateSchedule"),
			$frmUpdateSchedule = $("#frmUpdateSchedule"),
			$frmEditSchedule = $("#frmEditSchedule"),
			$dialogDuplicate = $('#dialogDuplicate'),
			$dialogEmailTeacher = $("#dialogEmailTeacher"),
			$dialogEmailStudent = $("#dialogEmailStudent"),
			tabs = ($.fn.tabs !== undefined),
			dialog = ($.fn.dialog !== undefined),
			chosen = ($.fn.chosen !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			$tabs = $("#tabs"),
			validator = null;
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs({});
		}
		if (chosen) 
		{
			$("#class_id").chosen();
			$("#teacher_id").chosen();
			$("#filter_class_id").chosen();
			$("#filter_teacher_id").chosen();
		}
		if ($dialogDuplicate.length > 0 && dialog) 
		{
			$dialogDuplicate.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 380,
				buttons: (function () {
					var buttons = {};
					
					buttons[cssApp.locale.button.ok] = function () {
						$dialogDuplicate.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		if ($frmEditSchedule.length > 0) {
			$frmEditSchedule.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				submitHandler: function(form)
				{
					$.ajax({
						type: "POST",
						data: $frmEditSchedule.serialize(),
						dataType: 'json',
						url: "index.php?controller=pjAdminSchedule&action=pjActionCheckCreate",
						success: function (data) {
							if(data.status == 'OK')
							{
								form.submit();
							}else{
								if(data.code == 100)
								{
									$frmEditSchedule.find('input[name="start_time['+data.index+']"]').addClass('err');
									$frmEditSchedule.find('input[name="end_time['+data.index+']"]').addClass('err');
								}else if(data.code == 101){
									$frmEditSchedule.find('select[name="teacher_id['+data.index+']"]').addClass('err');
								}else if(data.code == 102){
									$frmEditSchedule.find('input[name="date['+data.index+']"]').addClass('err');
									$frmEditSchedule.find('input[name="start_time['+data.index+']"]').addClass('err');
									$frmEditSchedule.find('input[name="end_time['+data.index+']"]').addClass('err');
								}
								$dialogDuplicate.html(data.text).dialog('open');
							}
						}
					});
				}
			});
		}
		if ($frmCreateSchedule.length > 0) {
			$frmCreateSchedule.validate({
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'class_id')
					{
						error.insertAfter(element.parent().parent());
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				submitHandler: function(form)
				{
					$.ajax({
						type: "POST",
						data: $frmCreateSchedule.serialize(),
						dataType: 'json',
						url: "index.php?controller=pjAdminSchedule&action=pjActionCheckCreate",
						success: function (data) {
							if(data.status == 'OK')
							{
								form.submit();
							}else{
								if(data.code == 100)
								{
									$frmCreateSchedule.find('input[name="start_time['+data.index+']"]').addClass('err');
									$frmCreateSchedule.find('input[name="end_time['+data.index+']"]').addClass('err');
								}else if(data.code == 101){
									$frmCreateSchedule.find('select[name="teacher_id['+data.index+']"]').addClass('err');
								}else if(data.code == 102){
									$frmCreateSchedule.find('input[name="date['+data.index+']"]').addClass('err');
									$frmCreateSchedule.find('input[name="start_time['+data.index+']"]').addClass('err');
									$frmCreateSchedule.find('input[name="end_time['+data.index+']"]').addClass('err');
								}
								$dialogDuplicate.html(data.text).dialog('open');
							}
						}
					});
				}
			});
			if($('#class_id').val() != '')
			{
				loadSchedule();
			}
		}
		if ($frmUpdateSchedule.length > 0) {
			$frmUpdateSchedule.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				submitHandler: function(form)
				{
					$.ajax({
						type: "POST",
						data: $frmUpdateSchedule.serialize(),
						dataType: 'json',
						url: "index.php?controller=pjAdminSchedule&action=pjActionCheckSchedule",
						success: function (data) {
							if(data.status == 'OK')
							{
								form.submit();
							}else{
								$dialogDuplicate.html(data.text).dialog('open');
							}
						}
					});
				}
			});
		}
		function formatClass (str, obj) {
			if(pjGrid.isStudent == true)
			{
				return '<a href="index.php?controller=pjAdminSchedule&action=pjActionPrintClass&class_id='+obj.class_id+'" target="_blank">'+str+'</a>';
			}else{
				return '<a href="index.php?controller=pjAdminSchedule&action=pjActionEdit&id='+obj.class_id+'">'+str+'</a>';
			}
		}
		if ($("#grid").length > 0 && datagrid) {
			
			var gridOpts = {
					buttons: [{type: "edit", url: "index.php?controller=pjAdminSchedule&action=pjActionUpdate&id={:id}"},
					          {type: "delete", url: "index.php?controller=pjAdminSchedule&action=pjActionDeleteSchedule&id={:id}"}
					          ],
					columns: [{text: myLabel.class, type: "text", sortable: false, editable: false, width: 170, renderer: formatClass},
					          {text: myLabel.teacher, type: "text", sortable: false, editable: false, width: 120},
					          {text: myLabel.venue, type: "text", sortable: false, editable: false, width: 90},
					          {text: myLabel.date, type: "text", sortable: false, editable: false, width: 80},
					          {text: myLabel.time, type: "text", sortable: false, editable: false, width: 120}],
					dataUrl: "index.php?controller=pjAdminSchedule&action=pjActionGetSchedule" + pjGrid.queryString,
					dataType: "json",
					fields: ['class', 'teacher', 'venue', 'date', 'time'],
					paginator: {
						actions: [
						   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminSchedule&action=pjActionDeleteScheduleBulk", render: true, confirmation: myLabel.delete_confirmation}
						],
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					saveUrl: "index.php?controller=pjAdminSchedule&action=pjActionSaveSchedule&id={:id}",
					select: {
						field: "id",
						name: "record[]"
					}
				};
			if(pjGrid.isTeacher == true)
			{
				gridOpts = {
					buttons: [],
					columns: [{text: myLabel.class, type: "text", sortable: false, editable: false, width: 200},
					          {text: myLabel.teacher, type: "text", sortable: false, editable: false, width: 120},
					          {text: myLabel.venue, type: "text", sortable: false, editable: false, width: 130},
					          {text: myLabel.date, type: "text", sortable: false, editable: false, width: 80},
					          {text: myLabel.time, type: "text", sortable: false, editable: false, width: 120}],
					dataUrl: "index.php?controller=pjAdminSchedule&action=pjActionGetSchedule" + pjGrid.queryString,
					dataType: "json",
					fields: ['class', 'teacher', 'venue', 'date', 'time'],
					paginator: {
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					saveUrl: "index.php?controller=pjAdminSchedule&action=pjActionSaveSchedule&id={:id}",
					select: {
						field: "id",
						name: "record[]"
					}
				};
			}
			if(pjGrid.isStudent == true)
			{
				gridOpts = {
					buttons: [{type: "btn-print pj-button", target: "_blank", url: "index.php?controller=pjAdminSchedule&action=pjActionPrintClass&class_id={:class_id}"}],
					columns: [{text: myLabel.class, type: "text", sortable: false, editable: false, width: 220},
					          {text: myLabel.teacher, type: "text", sortable: false, editable: false, width: 120},
					          {text: myLabel.venue, type: "text", sortable: false, editable: false, width: 90},
					          {text: myLabel.date, type: "text", sortable: false, editable: false, width: 80},
					          {text: myLabel.time, type: "text", sortable: false, editable: false, width: 120}],
					dataUrl: "index.php?controller=pjAdminSchedule&action=pjActionGetSchedule" + pjGrid.queryString,
					dataType: "json",
					fields: ['class', 'teacher', 'venue', 'date', 'time'],
					paginator: {
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					saveUrl: "index.php?controller=pjAdminSchedule&action=pjActionSaveSchedule&id={:id}",
					select: {
						field: "id",
						name: "record[]"
					},
					onRender: function () {
						$("#grid").find('.pj-table-icon-btn-print').text(myLabel.btnPrint);
					}
				};
			}
			var $grid = $("#grid").datagrid(gridOpts);
		}
		if ($("#student_grid").length > 0 && datagrid) {
			
			var $student_grid = $("#student_grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminBookings&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBooking&id={:id}"},
						 ],
				columns: [{text: myLabel.name, type: "text", sortable: true, editable: false, width: 120},
				          {text: myLabel.email, type: "text", sortable: true, editable: false, width: 160},
				          {text: myLabel.phone, type: "text", sortable: true, editable: false, width: 100},
				          {text: myLabel.deposit_paid, type: "text", sortable: true, editable: false, width: 100},
				          {text: myLabel.status, type: "select", sortable: true, editable: false, width: 100, options: [
				  				                                                                                     {label: myLabel.pending, value: "pending"}, 
				  				                                                                                     {label: myLabel.confirmed, value: "confirmed"},
				  				                                                                                     {label: myLabel.cancelled, value: "cancelled"}
				  				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminSchedule&action=pjActionGetStudents" + pjGrid.queryString,
				dataType: "json",
				fields: ['name', 'email', 'phone', 'deposit', 'status'],
				paginator: {
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		$(document).on("click", "*", function (e) {
			if(!$(e.target).hasClass('pj-table-icon-menu'))
			{
				$('.pj-menu-list-wrap').hide();
			}
		}).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminSchedule&action=pjActionGetSchedule", "start_ts", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminSchedule&action=pjActionGetSchedule", "start_ts", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-status-1", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}).on("click", ".pj-status-0", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminSchedule&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminSchedule&action=pjActionGetSchedule");
			});
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminSchedule&action=pjActionGetSchedule", "start_ts", "ASC", content.page, content.rowCount);
			return false;
		}).on("focusin", ".datepick", function (e) {
			var $this = $(this);
			var opts = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev")
				};
			if($this.hasClass('dateClone'))
			{
				opts.minDate = 0;
			}
			$this.datepicker(opts);
		}).on("focusin", ".pj-timepicker", function (e) {
			var $this = $(this);
			var type = $this.attr('data-type');
			var index = $this.attr('data-index');
			var timeOpts = {
					showPeriod: myLabel.showperiod,
					defaultTime: ''
				};
			$this.timepicker(timeOpts);
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
			
		}).on("change", "#filter_class_id", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			obj['class_id'] = $this.val();
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminSchedule&action=pjActionGetSchedule", "start_ts", "ASC", content.page, content.rowCount);
			return false;
		}).on("change", "#filter_teacher_id", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			obj['teacher_id'] = $this.val();
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminSchedule&action=pjActionGetSchedule", "start_ts", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".cpAddSchedule", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			
			var $tbody = $("#tblSchedule tbody"),
				index = Math.ceil(Math.random() * 999999);
			
			var clone_text = $("#tblScheduleClone").find("tbody").html();
			clone_text = clone_text.replace(/\{INDEX\}/g, 'cp_' + index);
			$tbody.append(clone_text);
		}).on("click", ".cpRemoveSchedule", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tbody = $("#tblSchedule tbody"),
				$tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});	
			return false;
		}).on("click", ".pjCssStudentsTab", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var index = $("#tabs-2").index();
			$tabs.tabs("option", "active", index-1);
			return false;
		}).on("submit", ".frm-student-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $student_grid.datagrid("option", "content"),
				cache = $student_grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$student_grid.datagrid("option", "cache", cache);
			$student_grid.datagrid("load", "index.php?controller=pjAdminSchedule&action=pjActionGetStudents" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("change", "#class_id", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			loadSchedule();
			return false;
		}).on("click", ".pj-table-icon-menu", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var diff, lf,
				$this = $(this),
				$list = $this.siblings(".pj-menu-list-wrap");
			diff = Math.ceil( ($list.outerWidth() - $this.outerWidth()) / 2 );
			if (diff > 0) {
				lf = $this.offset().left - diff;
				if (lf < 0) {
					lf = 0;
				}
			} else {
				lf  = $this.offset().left + diff;
			}
			$list.css({
				"top": $this.offset().top + $this.outerHeight() + 2,
				"left": lf
			});
		
			$list.toggle();
			$(".pj-menu-list-wrap").not($list).hide();
			return false;
		}).on("click", ".lnkNext", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var	index = $(this).attr('data-index'),
				$this = $(this),
				$tr = $(this).closest( "tr" );
			
			var post_data = new Object();
			var date = null;
			$("td input", $tr).each(function () {
			    var input = $(this);
			    post_data[input.attr("name")] = input.val();
			    if(input.hasClass('datepick'))
			    {
			    	date = input.val();
			    }
			});
			$("td select", $tr).each(function () {
			    var select = $(this);
			    post_data[select.attr("name")] = select.val();
			});
			if(date != '')
			{
				$.post("index.php?controller=pjAdminSchedule&action=pjActionNextPeriod&period=" + $(this).attr('data-period'), post_data).done(function (data) {
					var $tbody = $("#tblSchedule tbody");
					$tbody.append(data);
				});
			}
			return false;
		}).on("click", ".pjCssEmailTeacher", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogEmailTeacher.data('id', $(this).attr('data-id')).dialog('open');
		}).on("click", ".pjCssEmailStudent", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogEmailStudent.data('id', $(this).attr('data-id')).dialog('open');
		}).on("click", ".cpAddRecipient", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tbody = $("#tblTeacher tbody"),
				index = Math.ceil(Math.random() * 999999);
			
			var clone_text = $("#tblTeacherClone").find("tbody").html();
			clone_text = clone_text.replace(/\{INDEX\}/g, 'cp_' + index);
			$tbody.append(clone_text);
			
		}).on("click", ".cpRemoveRecipient", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tbody = $("#tblTeacher tbody"),
				$tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});
			return false;
		}).on("click", ".cpAddStudent", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tbody = $("#tblStudent tbody"),
				index = Math.ceil(Math.random() * 999999);
			
			var clone_text = $("#tblTeacherClone").find("tbody").html();
			clone_text = clone_text.replace(/\{INDEX\}/g, 'cp_' + index);
			$tbody.append(clone_text);
			
		}).on("click", ".cpRemoveStudent", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tbody = $("#tblStudent tbody"),
				$tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});
			return false;
		});
		
		function loadSchedule()
		{
			var class_id = $('#class_id').val();
			$.ajax({
				type: "GET",
				data: {class_id: class_id},
				dataType: 'html',
				url: "index.php?controller=pjAdminSchedule&action=pjActionLoadSchedule",
				success: function (data) {
					$("#tblSchedule tbody").html(data);
					if(class_id != '')
					{
						$('#pjCssEditClass').css('display', 'block');
						var href = $('#pjCssEditClass').attr('data-href');
						href = href.replace("{ID}", class_id);
						$('#pjCssEditClass').attr('href', href);
						$('#class_id').valid();
					}else{
						$('#pjCssEditClass').css('display', 'none');
					}
				}
			});
		}
		
		function attachTinyMce(options) {
			if (window.tinymce !== undefined) {
				tinymce.EditorManager.editors = [];
				var defaults = {
					selector: "textarea.mceEditor",
					theme: "modern",
					width: 610,
					height: 330,
					plugins: [
				         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
				         "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
				         "save table contextmenu directionality emoticons template paste textcolor"
				    ],
				    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"
				};
				var settings = $.extend({}, defaults, options);
				tinymce.init(settings);
			}
		}
		
		if ($dialogEmailTeacher.length > 0 && dialog) {
			$dialogEmailTeacher.dialog({
				autoOpen: false,
				draggable: false,
				resizable: false,
				modal: true,
				width: 645,
				position: ['center',50],
				open: function () {
					$dialogEmailTeacher.html("");
					$.get("index.php?controller=pjAdminSchedule&action=pjActionEmailTeacher", {
						"class_id": $dialogEmailTeacher.data('id')
					}).done(function (data) {
						$dialogEmailTeacher.html(data);
						validator = $dialogEmailTeacher.find("form").validate({
							
						});
					});
				},
				buttons: (function () {
					var buttons = {};
					buttons[cssApp.locale.button.send] = function () {
						if (validator.form()) {
							$.post("index.php?controller=pjAdminSchedule&action=pjActionEmailTeacher", $dialogEmailTeacher.find("form").serialize()).done(function (data) {
								$dialogEmailTeacher.dialog("close");
							})
						}
					};
					buttons[cssApp.locale.button.cancel] = function () {
						$dialogEmailTeacher.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		
		if ($dialogEmailStudent.length > 0 && dialog) {
			$dialogEmailStudent.dialog({
				autoOpen: false,
				draggable: false,
				resizable: false,
				modal: true,
				width: 645,
				position: ['center',50],
				open: function () {
					$dialogEmailStudent.html("");
					$.get("index.php?controller=pjAdminSchedule&action=pjActionEmailStudent", {
						"class_id": $dialogEmailStudent.data('id')
					}).done(function (data) {
						$dialogEmailStudent.html(data);
						validator = $dialogEmailStudent.find("form").validate({
							
						});
					});
				},
				buttons: (function () {
					var buttons = {};
					buttons[cssApp.locale.button.send] = function () {
						if (validator.form()) {
							$.post("index.php?controller=pjAdminSchedule&action=pjActionEmailStudent", $dialogEmailStudent.find("form").serialize()).done(function (data) {
								$dialogEmailStudent.dialog("close");
							})
						}
					};
					buttons[cssApp.locale.button.cancel] = function () {
						$dialogEmailStudent.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
	});
})(jQuery_1_8_2);