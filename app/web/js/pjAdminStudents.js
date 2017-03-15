var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateStudent = $("#frmCreateStudent"),
			$frmUpdateStudent = $("#frmUpdateStudent"),
			chosen = ($.fn.chosen !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if (chosen) {
			$("#country_id").chosen();
			$("#class_id").chosen();
		}
		if ($frmCreateStudent.length > 0) {
			$frmCreateStudent.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminStudents&action=pjActionCheckEmail"
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($frmUpdateStudent.length > 0) {
			$frmUpdateStudent.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminStudents&action=pjActionCheckEmail&id=" + $frmUpdateStudent.find("input[name='id']").val()
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		function formatPayment (str, obj) {
			return str != '' ? '<a href="index.php?controller=pjAdminHistory&action=pjActionIndex&student_id='+obj.id+'">'+str+'</a>' : myLabel.na;
		}
		if ($("#grid").length > 0 && datagrid) {
			
			var gridOpts = {
					buttons: [{type: "edit", url: "index.php?controller=pjAdminStudents&action=pjActionUpdate&id={:id}"},
					          {type: "delete", url: "index.php?controller=pjAdminStudents&action=pjActionDeleteStudent&id={:id}"}
					          ],
					columns: [{text: myLabel.name, type: "text", sortable: true, editable: true, width: 120},
					          {text: myLabel.email, type: "text", sortable: true, editable: true, width: 185},
					          {text: myLabel.phone, type: "text", sortable: true, editable: true, width: 110},
					          {text: myLabel.payment, type: "text", sortable: true, editable: false, width: 80, renderer: formatPayment},
					          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 90, options: [
					                                                                                     {label: myLabel.active, value: "T"}, 
					                                                                                     {label: myLabel.inactive, value: "F"}
					                                                                                     ], applyClass: "pj-status"}],
					dataUrl: "index.php?controller=pjAdminStudents&action=pjActionGetStudent" + pjGrid.queryString,
					dataType: "json",
					fields: ['name', 'email', 'phone', 'amount', 'status'],
					paginator: {
						actions: [
						   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminStudents&action=pjActionDeleteStudentBulk", render: true, confirmation: myLabel.delete_confirmation},
						   {text: myLabel.revert_status, url: "index.php?controller=pjAdminStudents&action=pjActionStatusStudent", render: true},
						   {text: myLabel.exported, url: "index.php?controller=pjAdminStudents&action=pjActionExportStudent", ajax: false}
						],
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					saveUrl: "index.php?controller=pjAdminStudents&action=pjActionSaveStudent&id={:id}",
					select: {
						field: "id",
						name: "record[]"
					}
				};
			if(pjGrid.isTeacher == true)
			{
				gridOpts = {
					buttons: [],
					columns: [{text: myLabel.name, type: "text", sortable: true, editable: false, width: 180},
					          {text: myLabel.email, type: "text", sortable: true, editable: false, width: 250},
					          {text: myLabel.phone, type: "text", sortable: true, editable: false, width: 150},
					          {text: myLabel.status, type: "select", sortable: true, editable: false, width: 100, options: [
					                                                                                     {label: myLabel.active, value: "T"}, 
					                                                                                     {label: myLabel.inactive, value: "F"}
					                                                                                     ], applyClass: "pj-status"}],
					dataUrl: "index.php?controller=pjAdminStudents&action=pjActionGetStudent" + pjGrid.queryString,
					dataType: "json",
					fields: ['name', 'email', 'phone', 'status'],
					paginator: {
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					saveUrl: "index.php?controller=pjAdminStudents&action=pjActionSaveStudent&id={:id}",
					select: {
						field: "id",
						name: "record[]"
					}
				};
			}
			var $grid = $("#grid").datagrid(gridOpts);
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
				q: "",
				class_id: "",
				name: "",
				phone: "",
				email: "",
				from_date: "",
				to_date: "",
				status: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminStudents&action=pjActionGetStudent", "name", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminStudents&action=pjActionGetStudent", "name", "ASC", content.page, content.rowCount);
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
			$.post("index.php?controller=pjAdminStudents&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminStudents&action=pjActionGetStudent");
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
				q: $this.find("input[name='q']").val(),
				class_id: "",
				name: "",
				phone: "",
				email: "",
				from_date: "",
				to_date: "",
				status: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminStudents&action=pjActionGetStudent", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("change", "#class_id", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.class_id = $this.val();
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminStudents&action=pjActionGetStudent", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
			e.stopPropagation();
			$(".pj-form-filter-advanced").toggle();
		}).on("submit", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var obj = {},
				$this = $(this),
				arr = $this.serializeArray(),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
				obj[arr[i].name] = arr[i].value;
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminStudents&action=pjActionGetStudent", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			$(".pj-button-detailed").trigger("click");
			$("#status").val('');
			$("#name").val('');
			$("#email").val('');
			$("#phone").val('');
			$("#from_date").val('');
			$("#to_date").val('');
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
			
		}).on("focusin", ".datepicker", function (e) {
			var minDate, maxDate,
				$this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
			};
			switch ($this.attr("name")) {
			case "from_date":
				if($(".datepicker[name='to_date']").val() != '')
				{
					maxDate = $(".datepicker[name='to_date']").datepicker({
						firstDay: $this.attr("rel"),
						dateFormat: $this.attr("rev"),
					}).datepicker("getDate");
					$(".datepicker[name='to_date']").datepicker("destroy").removeAttr("id");
					if (maxDate !== null) {
						custom.maxDate = maxDate;
					}
				}
				break;
			case "to_date":
				if($(".datepicker[name='from_date']").val() != '')
				{
					minDate = $(".datepicker[name='from_date']").datepicker({
						firstDay: $this.attr("rel"),
						dateFormat: $this.attr("rev")
					}).datepicker("getDate");
					$(".datepicker[name='from_date']").datepicker("destroy").removeAttr("id");
					if (minDate !== null) {
						custom.minDate = minDate;
					}
				}
				break;
			}
			$(this).datepicker($.extend(o, custom));
			
		});
	});
})(jQuery_1_8_2);