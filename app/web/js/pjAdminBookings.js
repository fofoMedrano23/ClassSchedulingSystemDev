var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateBooking = $("#frmCreateBooking"),
			$frmUpdateBooking = $("#frmUpdateBooking"),
			$dialogConfirmation = $("#dialogConfirmation"),
			$dialogCancellation = $("#dialogCancellation"),
			validate = ($.fn.validate !== undefined),
			chosen = ($.fn.chosen !== undefined);
			dialog = ($.fn.dialog !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			tOpt = {
				activate: function (event, ui) {
					$(":input[name='tab_id']").val($(ui.newPanel).prop('id'));
				}
			};
			
		if ($tabs.length > 0 && tabs) 
		{
			$tabs.tabs(tOpt);
		}
		if (chosen) 
		{
			$("#class_id").chosen();
			$("#student_id").chosen();
			$("#country_id").chosen();
		}
		if ($frmCreateBooking.length > 0 && validate) {
			$frmCreateBooking.validate({
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'student_id')
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
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				}
			});
			if($('#class_id').val() != "")
			{
				calcPrice();
				updateEditClassUrl();
			}
		}
		if ($frmUpdateBooking.length > 0 && validate) {
			$frmUpdateBooking.validate({
				errorPlacement: function (error, element) {
					if(element.attr('name') == 'student_id')
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
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    };
				}
			});
			updateEditClassUrl();
		}
		function formatClass (str, obj) {
			return '<a href="index.php?controller=pjAdminSchedule&action=pjActionEdit&id='+obj.class_id+'">'+str+'</a>';
		}
		function formatStudent (str, obj) {
			return '<a href="index.php?controller=pjAdminStudents&action=pjActionUpdate&id='+obj.student_id+'">'+str+'</a>';
		}
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminBookings&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBooking&id={:id}"},
						 ],
				columns: [
				          {text: myLabel.class, type: "text", sortable: true, editable: false, width: 260, renderer: formatClass},
				          {text: myLabel.student, type: "text", sortable: true, editable: false, width: 200, renderer: formatStudent},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, options: [
				                                                                                     {label: myLabel.pending, value: "pending"}, 
				                                                                                     {label: myLabel.confirmed, value: "confirmed"},
				                                                                                     {label: myLabel.cancelled, value: "cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString,
				dataType: "json",
				fields: ['class', 'name','status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBookingBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminBookings&action=pjActionExportBooking", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminBookings&action=pjActionSaveBooking&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}	
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-hover").siblings(".pj-button").removeClass("pj-button-hover");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			$(".pj-button-detailed").trigger("click");
			if (chosen) {
				$("#event_id").val('').trigger("liszt:updated");
			}
		}).on("change", "#payment_method", function (e) {
			switch ($("option:selected", this).val()) {
				case 'creditcard':
					$(".boxCC").show();
					$(".pj-cc-field").addClass('required');
					break;
				default:
					$(".boxCC").hide();
					$(".pj-cc-field").removeClass('required');
			}
		}).on("change", "#class_id", function (e) {
			calcPrice();
			updateEditClassUrl();
		}).on("change", "#student_id", function (e) {
			if($(this).val() != '')
			{
				$('#pjCssEditStudent').css('display', 'block');
				var href = $('#pjCssEditStudent').attr('data-href');
				href = href.replace("{ID}", $(this).val());
				$('#pjCssEditStudent').attr('href', href);
			}else{
				$('#pjCssEditStudent').css('display', 'none');
			}
		}).on("change", "input[name='student_type']", function (e) {
			var type = $('input[name="student_type"]:checked').val();
			if(type == 'new')
			{
				$('.css-required').addClass('required');
				$('#student_id').removeClass('required');
				$('#newBox').show();
				$('#existingBox').hide();
			}else{
				$('.css-required').removeClass('required');
				$('#student_id').addClass('required');
				$('#newBox').hide();
				$('#existingBox').show();
			}
		}).on("click", ".pjSbsSendConfirm", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogConfirmation.data('id', $(this).attr('data-id')).dialog('open');
		}).on("click", ".pjSbsSendCancel", function (e) {
			if (e && e.pjSbsSendCancel) {
				e.preventDefault();
			}
			$dialogCancellation.data('id', $(this).attr('data-id')).dialog('open');
		});
		function updateEditClassUrl()
		{
			var class_id = $('#class_id').val();
			if(class_id != '')
			{
				$('#pjCssEditClass').css('display', 'block');
				var href = $('#pjCssEditClass').attr('data-href');
				href = href.replace("{ID}", class_id);
				$('#pjCssEditClass').attr('href', href);
			}else{
				$('#pjCssEditClass').css('display', 'none');
			}
		}
		function calcPrice()
		{
			var subtotal = 0;
			var tax = 0;
			var total = 0;
			var deposit = 0;
			
			var price = $('#class_id option:selected').attr('data-price');
			
			if(price != '')
			{
				subtotal = parseInt(price, 10);
			}
			if(subtotal > 0)
			{
				var tax_percentage = parseFloat($('#tax').attr('data-tax'));
				var deposit_percentage = parseFloat($('#deposit').attr('data-deposit'));
				tax = (subtotal * tax_percentage) / 100;
				total = subtotal + tax;
				deposit = (total * deposit_percentage) / 100;
				$('#subtotal').val(subtotal.toFixed(2));
				$('#tax').val(tax.toFixed(2));
				$('#total').val(total.toFixed(2));
				$('#deposit').val(deposit.toFixed(2));
			}else{
				$('#subtotal').val('');
				$('#tax').val('');
				$('#total').val('');
				$('#deposit').val('');
			}
		}
		
		if ($dialogConfirmation.length > 0 && dialog) {
			$dialogConfirmation.dialog({
				autoOpen: false,
				draggable: false,
				resizable: false,
				modal: true,
				width: 645,
				open: function () {
					$dialogConfirmation.html("");
					$.get("index.php?controller=pjAdminBookings&action=pjActionConfirmation", {
						"booking_id": $dialogConfirmation.data('id')
					}).done(function (data) {
						$dialogConfirmation.html(data);
						validator = $dialogConfirmation.find("form").validate({
							
						});
						$dialogConfirmation.dialog("option", "position", "center");
						attachTinyMce.call(null);
					});
				},
				buttons: (function () {
					var buttons = {};
					buttons[cssApp.locale.button.send] = function () {
						tinymce.get("confirm_message").save();	
						if (validator.form()) {
							$.post("index.php?controller=pjAdminBookings&action=pjActionConfirmation", $dialogConfirmation.find("form").serialize()).done(function (data) {
								$dialogConfirmation.dialog("close");
							})
						}
					};
					buttons[cssApp.locale.button.cancel] = function () {
						$dialogConfirmation.dialog("close");
					};
					
					return buttons;
				})()
			});
		}
		if ($dialogCancellation.length > 0 && dialog) {
			$dialogCancellation.dialog({
				autoOpen: false,
				draggable: false,
				resizable: false,
				modal: true,
				width: 645,
				open: function () {
					$dialogConfirmation.html("");
					$.get("index.php?controller=pjAdminBookings&action=pjActionCancellation", {
						"booking_id": $dialogCancellation.data('id')
					}).done(function (data) {
						$dialogCancellation.html(data);
						validator = $dialogCancellation.find("form").validate({
							
						});
						$dialogCancellation.dialog("option", "position", "center");
						attachTinyMce.call(null);
					});
				},
				buttons: (function () {
					var buttons = {};
					buttons[cssApp.locale.button.send] = function () {
						tinymce.get("confirm_message").save();	
						if (validator.form()) {
							$.post("index.php?controller=pjAdminBookings&action=pjActionCancellation", $dialogCancellation.find("form").serialize()).done(function (data) {
								$dialogCancellation.dialog("close");
							})
						}
					};
					buttons[cssApp.locale.button.cancel] = function () {
						$dialogCancellation.dialog("close");
					};
					
					return buttons;
				})()
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
	});
})(jQuery_1_8_2);