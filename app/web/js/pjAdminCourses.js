var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateCourse = $("#frmCreateCourse"),
			$frmUpdateCourse = $("#frmUpdateCourse"),
			$frmUpdatePeriod = $("#frmUpdatePeriod"),
			$dialogDelete = $("#dialogDeleteImage"),
			$dialogDeletePeriod = $("#dialogDeletePeriod"),
			$dialogDuplicate = $('#dialogDuplicate'),
			$dialogEmptyPeriod = $('#dialogEmptyPeriod'),
			tipsy = ($.fn.tipsy !== undefined),
			dialog = ($.fn.dialog !== undefined),
			multiselect = ($.fn.multiselect !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined)
			has_delete = false;
		
		if (tipsy) {
			$(".listing-tip").tipsy({
				offset: 1,
				opacity: 1,
				html: true,
				gravity: "nw",
				className: "tipsy-listing"
			});
		}
		$(".field-int").spinner({
			min: 0
		});
		if ($frmCreateCourse.length > 0 && validate) {
			$frmCreateCourse.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				},
				submitHandler: function(form){
					var duplidated = false,
						empty = false;
						$duplicated_tr = null,
						$duplicated_next_tr = null;
					$('#tblPeriods > tbody > tr').each(function(index){
						var $tr = $(this),
							id = $tr.attr('data-id'),
							start_date = $('input[name="start_date['+id+']"]').val(),
							end_date = $('input[name="end_date['+id+']"]').val();
						
						if($tr.find('.cpNoPeriods').length == 0)
						{
							$('#tblPeriods > tbody > tr').each(function(idx){
								if(idx > index)
								{
									var $next_tr = $(this),
										next_id = $next_tr.attr('data-id'),
										next_start_date = $('input[name="start_date['+next_id+']"]').val(),
										next_end_date = $('input[name="end_date['+next_id+']"]').val();
								}
								
								if(start_date == next_start_date && end_date == next_end_date)
								{
									duplidated = true;
									$duplicated_tr = $tr;
									$duplicated_next_tr = $next_tr;
										
									return false;
								}
							});
						}else{
							empty = true;
						}
					});
					if(duplidated == true)
					{
						$dialogDuplicate.data('tr', $duplicated_tr).data('next_tr', $duplicated_next_tr).dialog('open');
					}else{
						if(empty == true && has_delete == true)
						{
							$dialogEmptyPeriod.data('form', form).dialog('open');
						}else{
							form.submit();
						}
					}
					return false;
				}
			});
		}
		if ($frmUpdateCourse.length > 0 && validate) {
			$frmUpdateCourse.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
					var localeId = $(validator.errorList[0].element, this).attr('lang');
					if(localeId != undefined)
					{
						$(".pj-multilang-wrap").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).css('display','block');
							}else{
								$(this).css('display','none');
							}
						});
						$(".pj-form-langbar-item").each(function( index ) {
							if($(this).attr('data-index') == localeId)
							{
								$(this).addClass('pj-form-langbar-item-active');
							}else{
								$(this).removeClass('pj-form-langbar-item-active');
							}
						});
					}
				},
				submitHandler: function(form){
					var duplidated = false,
						empty = false;
						$duplicated_tr = null,
						$duplicated_next_tr = null;
					$('#tblPeriods > tbody > tr').each(function(index){
						var $tr = $(this),
							id = $tr.attr('data-id'),
							start_date = $('input[name="start_date['+id+']"]').val(),
							end_date = $('input[name="end_date['+id+']"]').val();
						
						if($tr.find('.cpNoPeriods').length == 0)
						{
							$('#tblPeriods > tbody > tr').each(function(idx){
								if(idx > index)
								{
									var $next_tr = $(this),
										next_id = $next_tr.attr('data-id'),
										next_start_date = $('input[name="start_date['+next_id+']"]').val(),
										next_end_date = $('input[name="end_date['+next_id+']"]').val();
								}
								
								if(start_date == next_start_date && end_date == next_end_date)
								{
									duplidated = true;
									$duplicated_tr = $tr;
									$duplicated_next_tr = $next_tr;
										
									return false;
								}
							});
						}else{
							empty = true;
						}
					});
					if(duplidated == true)
					{
						$dialogDuplicate.data('tr', $duplicated_tr).data('next_tr', $duplicated_next_tr).dialog('open');
					}else{
						if(empty == true && has_delete == true)
						{
							$dialogEmptyPeriod.data('form', form).dialog('open');
						}else{
							form.submit();
						}
					}
					return false;
				}
			});
		}
		if ($dialogDelete.length > 0 && dialog) 
		{
			$dialogDelete.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 320,
				buttons: (function () {
					var buttons = {};
					buttons[cssApp.locale.button.delete] = function () {
						$.ajax({
							type: "GET",
							dataType: "json",
							url: $dialogDelete.data('href'),
							success: function (res) {
								if(res.code == 200){
									$('#image_container').remove();
									$dialogDelete.dialog('close');
								}
							}
						});
					};
					buttons[cssApp.locale.button.cancel] = function () {
						$dialogDelete.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		if ($dialogDeletePeriod.length > 0 && dialog) 
		{
			$dialogDeletePeriod.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 450,
				buttons: (function () {
					var buttons = {};
					buttons[cssApp.locale.button.delete] = function () {
						var $tbody = $("#tblPeriods tbody"),
							$tr = $dialogDeletePeriod.data('tr');
						$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
							$tr.remove();
							if($tbody.find('tr').length == 0)
							{
								$tbody.html('<tr><td colspan="5" class="cpNoPeriods">'+myLabel.no_periods_defined+'</td></tr>');
							}
						});
						$dialogDeletePeriod.dialog("close");
					};
					buttons[cssApp.locale.button.cancel] = function () {
						$dialogDeletePeriod.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminCourses&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminCourses&action=pjActionDeleteCourse&id={:id}"}
				          ],
				columns: [{text: myLabel.title, type: "text", sortable: true, editable: true, width: 170, editableWidth: 150},
				          {text: myLabel.class_size, type: "text", sortable: true, editable: false, width: 90},
				          {text: myLabel.price, type: "text", sortable: true, editable: false, width: 70},
				          {text: myLabel.periods, type: "text", sortable: true, editable: false, width: 160},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 90, editableWidth: 80, options: [
			                                                                                     {label: myLabel.active, value: "T"}, 
			                                                                                     {label: myLabel.inactive, value: "F"}
			                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminCourses&action=pjActionGetCourse" + pjGrid.queryString,
				dataType: "json",
				fields: ['title', 'size', 'price', 'periods', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminCourses&action=pjActionDeleteCourseBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminCourses&action=pjActionSaveCourse&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		if ($dialogEmptyPeriod.length > 0 && dialog) 
		{
			$dialogEmptyPeriod.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				width: 380,
				buttons: (function () {
					var buttons = {};
					
					buttons[cssApp.locale.button.ok] = function () {
						$dialogEmptyPeriod.dialog("close");
						var form =  $dialogEmptyPeriod.data('form');
						form.submit();
					};
										
					return buttons;
				})()
			});
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
						var $tr = $dialogDuplicate.data('tr'),
							$next_tr = $dialogDuplicate.data('next_tr'),
							seconds = 2000,
							tr_background = $tr.css('backgroundColor'),
							next_tr_background = $next_tr.css('backgroundColor');
						$tr.css('backgroundColor', '#FFB4B4');
						$next_tr.css('backgroundColor', '#FFB4B4');
						setTimeout(function(){
							$tr.css("backgroundColor", tr_background);
							$next_tr.css("backgroundColor", next_tr_background);
						}, seconds);
					};
					
					return buttons;
				})()
			});
		}
		if ($frmUpdatePeriod.length > 0 && validate) {
			$frmUpdatePeriod.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminCourses&action=pjActionGetCourse", "title", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminCourses&action=pjActionGetCourse", "title", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminCourses&action=pjActionGetCourse", "title", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-delete-image", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogDelete.data('href', $(this).data('href')).dialog("open");
		}).on("click", ".cpAddPeriod", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			
			var $tbody = $("#tblPeriods tbody"),
				index = Math.ceil(Math.random() * 999999);
			
			var clone_text = $("#tblPeriodsClone").find("tbody").html();
			clone_text = clone_text.replace(/\{INDEX\}/g, 'cp_' + index);
			if($tbody.find(".cpNoPeriods").length == 0)
			{
				$tbody.append(clone_text);
			}else{
				$tbody.html(clone_text);
			}
		}).on("click", ".cpRemovePeriod", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var students = parseInt($(this).attr('data-students'), 10);
			if(students > 0)
			{
				$dialogDeletePeriod.data('tr', $(this).closest("tr")).dialog('open');
			}else{
				var $tbody = $("#tblPeriods tbody"),
					$tr = $(this).closest("tr");
				var index = $tr.attr('data-id');
				if(index.indexOf("cp_") !== 0)
				{
					has_delete = true;
				}
				$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
					$tr.remove();
					if($tbody.find('tr').length == 0)
					{
						$tbody.html('<tr><td colspan="5" class="cpNoPeriods">'+myLabel.no_periods_defined+'</td></tr>');
					}
				});
			}
			return false;
		}).on("focusin", ".datepick", function (e) {
			var minDate, maxDate,
				$this = $(this),
				index = $(this).data('index'),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),					
					onClose: function(dateText){
						$this.valid();
					}
				};
			
			switch ($this.attr("name")) {
			case "start_date["+index+"]":
				maxDate = $this.closest("tr").find(".datepick[name='end_date["+index+"]']").datepicker({
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev")
				}).datepicker("getDate");
				$this.closest("tr").find(".datepick[name='end_date["+index+"]']").datepicker("destroy").removeAttr("id");
				if (maxDate !== null) {
					custom.maxDate = maxDate;
				}
				if($frmCreateCourse.length > 0)
				{
					custom.minDate = 0;
				}else{
					if($this.hasClass('dateClone'))
					{
						custom.minDate = 0;
					}
				}
				break;
			case "end_date["+index+"]":
				minDate = $this.closest("tr").find(".datepick[name='start_date["+index+"]']").datepicker({
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev")
				}).datepicker("getDate");
				$this.closest("tr").find(".datepick[name='start_date["+index+"]']").datepicker("destroy").removeAttr("id");
				if (minDate !== null) {
					custom.minDate = minDate;
				}else{
					if($frmCreateCourse.length > 0)
					{
						custom.minDate = 0;
					}else{
						if($this.hasClass('dateClone'))
						{
							custom.minDate = 0;
						}
					}
				}
				break;
			}
			$this.not('.hasDatepicker').datepicker($.extend(o, custom));
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				if(!$dp.is('[disabled=disabled]'))
				{
					$dp.trigger("focusin").datepicker("show");
				}
			}
		}).on("submit", ".stident-frm-filter", function (e) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminCourses&action=pjActionGetStudents" + pjGrid.queryString, "name", "ASC", content.page, content.rowCount);
			return false;
		});
		
		if ($("#student_grid").length > 0 && datagrid) {
			
			var $grid = $("#student_grid").datagrid({
				columns: [{text: myLabel.name, type: "text", sortable: true, editable: false, width: 250},
				          {text: myLabel.email, type: "text", sortable: true, editable: false, width: 300},
				          {text: myLabel.phone, type: "text", sortable: true, editable: false, width: 150},],
				dataUrl: "index.php?controller=pjAdminCourses&action=pjActionGetStudents" + pjGrid.queryString,
				dataType: "json",
				fields: ['name', 'email', 'phone'],
				paginator: {
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminCourses&action=pjActionSaveCourse&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
	});
})(jQuery_1_8_2);