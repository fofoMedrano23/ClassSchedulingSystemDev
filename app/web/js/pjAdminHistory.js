var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreatePayment = $("#frmCreatePayment"),
			$frmUpdatePayment = $("#frmUpdatePayment"),
			chosen = ($.fn.chosen !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if (chosen) 
		{
			$("#class_id").chosen();
			if($frmCreatePayment.length > 0 || $frmUpdatePayment.length > 0)
			{
				$("#student_id").chosen();
			}
		}
		if ($frmCreatePayment.length > 0) {
			$frmCreatePayment.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		if ($frmUpdatePayment.length > 0) {
			$frmUpdatePayment.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		if ($("#grid").length > 0 && datagrid) {
			
			var gridOpts = {
					buttons: [{type: "edit", url: "index.php?controller=pjAdminHistory&action=pjActionUpdate&student_id={:student_id}&id={:id}"},
					          {type: "delete", url: "index.php?controller=pjAdminHistory&action=pjActionDeletePayment&id={:id}"}
					          ],
					columns: [{text: myLabel.created, type: "text", sortable: true, editable: false, width: 150},
					          {text: myLabel.amount, type: "text", sortable: true, editable: false, width: 80},
					          {text: myLabel.class, type: "text", sortable: true, editable: false, width: 250},
					          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 90,options: [
					                                                                                     {label: myLabel.paid, value: "paid"}, 
					                                                                                     {label: myLabel.refund, value: "refund"},
					                                                                                     {label: myLabel.due, value: "due"}
					                                                                                     ], applyClass: "pj-status"}],
					dataUrl: "index.php?controller=pjAdminHistory&action=pjActionGetHistory" + pjGrid.queryString,
					dataType: "json",
					fields: ['created', 'amount', 'course', 'status'],
					paginator: {
						actions: [
						   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminHistory&action=pjActionDeletePaymentBulk", render: true, confirmation: myLabel.delete_confirmation},
						   {text: myLabel.exported, url: "index.php?controller=pjAdminHistory&action=pjActionExportHistory", ajax: false}
						],
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					saveUrl: "index.php?controller=pjAdminHistory&action=pjActionSaveHistory&id={:id}",
					select: {
						field: "id",
						name: "record[]"
					}
				};
			if(pjGrid.isStudent == true)
			{
				gridOpts = {
					buttons: [],
					columns: [{text: myLabel.created, type: "text", sortable: true, editable: false, width: 150},
					          {text: myLabel.amount, type: "text", sortable: true, editable: false, width: 80},
					          {text: myLabel.class, type: "text", sortable: true, editable: false, width: 300},
					          {text: myLabel.status, type: "select", sortable: true, editable: false, width: 120,options: [
					                                                                                     {label: myLabel.paid, value: "paid"}, 
					                                                                                     {label: myLabel.refund, value: "refund"},
					                                                                                     {label: myLabel.due, value: "due"}
					                                                                                     ], applyClass: "pj-status"}],
					dataUrl: "index.php?controller=pjAdminHistory&action=pjActionGetHistory" + pjGrid.queryString,
					dataType: "json",
					fields: ['created', 'amount', 'course', 'status'],
					paginator: {
						
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					saveUrl: "index.php?controller=pjAdminHistory&action=pjActionSaveHistory&id={:id}",
					select: {
						field: "id",
						name: "record[]"
					}
				};
			}
			if(pjGrid.queryString == "")
			{
				gridOpts = {
						buttons: [{type: "edit", url: "index.php?controller=pjAdminBookings&action=pjActionUpdatePayment&id={:id}"},
						          {type: "delete", url: "index.php?controller=pjAdminHistory&action=pjActionDeletePayment&id={:id}"}
						          ],
						columns: [{text: myLabel.created, type: "text", sortable: true, editable: false, width: 130},
						          {text: myLabel.name, type: "text", sortable: true, editable: false, width: 110},
						          {text: myLabel.amount, type: "text", sortable: true, editable: false, width: 70},
						          {text: myLabel.class, type: "text", sortable: true, editable: false, width: 180},
						          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 90,options: [
						                                                                                     {label: myLabel.paid, value: "paid"}, 
						                                                                                     {label: myLabel.refund, value: "refund"},
						                                                                                     {label: myLabel.due, value: "due"}
						                                                                                     ], applyClass: "pj-status"}],
						dataUrl: "index.php?controller=pjAdminHistory&action=pjActionGetHistory" + pjGrid.queryString,
						dataType: "json",
						fields: ['created', 'name', 'amount', 'course', 'status'],
						paginator: {
							actions: [
							   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminHistory&action=pjActionDeletePaymentBulk", render: true, confirmation: myLabel.delete_confirmation},
							   {text: myLabel.exported, url: "index.php?controller=pjAdminHistory&action=pjActionExportHistory", ajax: false}
							],
							gotoPage: true,
							paginate: true,
							total: true,
							rowCount: true
						},
						saveUrl: "index.php?controller=pjAdminHistory&action=pjActionSaveHistory&id={:id}",
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
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminHistory&action=pjActionGetHistory" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminHistory&action=pjActionGetHistory" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
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
			$.post("index.php?controller=pjAdminHistory&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminHistory&action=pjActionGetHistory");
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
			$grid.datagrid("load", "index.php?controller=pjAdminHistory&action=pjActionGetHistory" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("change", "#student_id", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.get("index.php?controller=pjAdminBookings&action=pjActionGetClasses", {
				student_id: $(this).val()
			}).done(function (data) {
				$('#pjCssClassBox').html(data);
				$("#class_id").chosen();
			});
			return false;
		});
	});
})(jQuery_1_8_2);