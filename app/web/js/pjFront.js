(function (window, undefined){
	"use strict";
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		routes = [
		          	{pattern: /^#!\/loadClasses$/, eventName: "loadClasses"},
		          	{pattern: /^#!\/loadClass\/id:(\d+)?$/, eventName: "loadClass"},
		          	{pattern: /^#!\/loadCheckout$/, eventName: "loadCheckout"},
		          	{pattern: /^#!\/loadPreview$/, eventName: "loadPreview"}
		         ];
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function hashBang(value) {
		if (value !== undefined && value.match(/^#!\//) !== null) {
			if (window.location.hash == value) {
				return false;
			}
			window.location.hash = value;
			return true;
		}
		
		return false;
	}
	
	function onHashChange() {
		var i, iCnt, m;
		for (i = 0, iCnt = routes.length; i < iCnt; i++) {
			m = window.location.hash.match(routes[i].pattern);
			if (m !== null) {
				pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
				break;
			}
		}
		if (m === null) {
			pjQ.$(window).trigger("loadClasses");
		}
	}
	pjQ.$(window).on("hashchange", function (e) {
    	onHashChange.call(null);
    });
	
	function ClassScheduling(opts) {
		if (!(this instanceof ClassScheduling)) {
			return new ClassScheduling(opts);
		}
				
		this.reset.call(this);
		this.init.call(this, opts);
		
		return this;
	}
	
	ClassScheduling.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	ClassScheduling.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	ClassScheduling.prototype = {
		reset: function () {
			this.$container = null;			
			this.container = null;
			this.order = null;
			this.id = null;
			this.opts = {};
			
			return this;
		},
		
		disableButtons: function () {
			this.$container.find(".btn").each(function (i, el) {
				pjQ.$(el).attr("disabled", "disabled");
			});
		},
		enableButtons: function () {
			this.$container.find(".btn").removeAttr("disabled");
		},
		
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById("pjCssContainer_" + self.opts.index);
						
			self.$container = pjQ.$(self.container);
			
			this.$container.on('click.css', '.pjCssHome', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (!hashBang("#!/loadClasses")) 
				{
					self.loadClasses.call(self);
				}
				return false;
			}).on('change.css', '.pjCssMenuNav', function(e){
				
				var hash = pjQ.$(this).val();
				if (!hashBang("#!/" + hash)) 
				{
					pjQ.$(window).trigger("#!/" + hash);
				}
				return false;
			}).on('change.css', '.pjCssOrderBy', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.order = pjQ.$(this).val();
				if (!hashBang("#!/loadClasses")) 
				{
					self.loadClasses.call(self);
				}
				return false;
			}).on('click.css', '.pjCssViewDetails', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var course_id = pjQ.$(this).attr('data-id');
				if (!hashBang("#!/loadClass/id:" + course_id)) 
				{
					pjQ.$(window).trigger("#!/loadClass/id:" + course_id);
				}
				return false;
			}).on('click.css', '.pjCssBtnBook', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var class_id = pjQ.$('#class_id_' + self.opts.index).val();
				if(class_id != '')
				{
					var params = {};
					params.class_id = class_id;
					if(self.opts.session_id != '')
					{
						params.session_id = self.opts.session_id;
					}
					self.disableButtons.call(self);
					pjQ.$.get([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSetClass"].join(""), params).done(function (data) {
						if (!hashBang("#!/loadCheckout")) 
						{
							self.loadCheckout.call(self);
						}
					}).fail(function () {
						
					});
				}
				return false;
			}).on("change.sbs", "select[name='payment_method']", function () {
				self.$container.find(".pjSbsCcWrap").hide();
				self.$container.find(".pjSbsBankWrap").hide();
				switch (pjQ.$("option:selected", this).val()) {
				case 'creditcard':
					self.$container.find(".pjSbsCcWrap").show();
					break;
				case 'bank':
					self.$container.find(".pjSbsBankWrap").show();
					break;
				}
			}).on('click.css', '.pjCssBackToClass', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var course_id = pjQ.$(this).attr('data-id');
				self.disableButtons.call(self);
				if (!hashBang("#!/loadClass/id:" + course_id)) 
				{
					pjQ.$(window).trigger("#!/loadClass/id:" + course_id);
				}
				return false;
			}).on('click.css', '.pjCssBackToCheckout', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.disableButtons.call(self);
				if (!hashBang("#!/loadCheckout")) 
				{
					self.loadCheckout.call(self);
				}
				return false;
			}).on('click.css', '.pjCssBtnStartOver', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.disableButtons.call(self);
				if (!hashBang("#!/loadClasses")) 
				{
					self.loadClasses.call(self);
				}
				return false;
			}).on('click.css', '#pjCssImage_' + self.opts.index, function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $captchaImg = pjQ.$(this);
				if($captchaImg.length > 0){
					var rand = Math.floor((Math.random()*999999)+1); 
					$captchaImg.attr("src", self.opts.folder + 'index.php?controller=pjFrontEnd&action=pjActionCaptcha&rand=' + rand);
					pjQ.$('#pjCssCheckoutForm_' + self.opts.index).find('input[name="captcha"]').val("");
				}
				return false;
			}).on('click.css', '.pjCssLogin', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $loginForm = pjQ.$('#pjCssLoginForm_'+ self.opts.index);
				$loginForm.find('input[name="login_email"]').val("");
				$loginForm.find('input[name="login_password"]').val("");
				pjQ.$('#pjLoginMessage_'+ self.opts.index).html("").parent().parent().hide();
				pjQ.$('#pjCssLoginModal').modal('show');
				return false;
			}).on('click.css', '.pjCssLogout', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var params = {};
				params.locale = self.opts.locale;
				params.index = self.opts.index;
				if(self.opts.session_id != '')
				{
					params.session_id = self.opts.session_id;
				}
				self.disableButtons.call(self);
				pjQ.$.get([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionLogout"].join(""), params).done(function (data) {
					if (!hashBang("#!/loadCheckout")) 
					{
						self.loadCheckout.call(self);
					}
				}).fail(function () {
					
				});
				return false;
			});
			
			pjQ.$(window).on("loadClasses", this.$container, function (e) {
				self.loadClasses.call(self);
			}).on("loadClass", this.$container, function (e) {
				if(arguments.length == 2)
				{
					self.id = arguments[1];
				}
				self.loadClass.call(self);
			}).on("loadCheckout", this.$container, function (e) {
				self.loadCheckout.call(self);
			}).on("loadPreview", this.$container, function (e) {
				self.loadPreview.call(self);
			});
			
			if (window.location.hash.length === 0) {
				this.loadClasses.call(this);
			} else {
				onHashChange.call(null);
			}
			
			pjQ.$(document).on("click.css", '.pjCssLinkForgotPassword', function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $forgotForm = pjQ.$('#pjCssForgotForm_'+ self.opts.index);
				$forgotForm.find('input[name="email"]').val("");
				pjQ.$('#pjForgotMessage_'+ self.opts.index).removeClass('text-danger text-success').html("").parent().parent().hide();
				pjQ.$('#pjCssLoginModal').modal('hide');
				pjQ.$('#pjCssForgotModal').modal('show');
				return false;
			}).on("click.css", '.pjCssLinkLogin', function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $loginForm = pjQ.$('#pjCssLoginForm_'+ self.opts.index);
				$loginForm.find('input[name="login_email"]').val("");
				$loginForm.find('input[name="login_password"]').val("");
				pjQ.$('#pjLoginMessage_'+ self.opts.index).html("").parent().parent().hide();
				pjQ.$('#pjCssForgotModal').modal('hide');
				pjQ.$('#pjCssLoginModal').modal('show');
				return false;
			});
		},
		
		loadClasses: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			if(self.order != null)
			{
				params.order = self.order;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionClasses"].join(""), params).done(function (data) {
				self.$container.html(data);
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
			}).fail(function () {
				
			});
		},
		loadClass: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			if(self.id != null)
			{
				params.id = self.id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionClass"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadClasses")) 
					{
						self.loadClasses.call(self);
					}
				}else{
					self.$container.html(data);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		loadCheckout: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = this.opts.locale;
			params.index = this.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadClasses")) 
					{
						self.loadClasses.call(self);
					}
				}else{
					self.$container.html(data);
					self.bindCheckout.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindCheckout: function(){
			var self = this,
				index = this.opts.index;
		
			pjQ.$('.modal-dialog').css("z-index", "9999"); 
			
			if (validate) 
			{
				var $form = pjQ.$('#pjCssCheckoutForm_'+ self.opts.index);
				var remote_url = self.opts.folder + "index.php?controller=pjFrontEnd&action=pjActionCheckCaptcha";
				if(self.opts.session_id != '')
				{
					remote_url += "&session_id=" + self.opts.session_id;
				}
				$form.validate({
					rules: {
						"captcha": {
							remote: remote_url
						}
					},
					onkeyup: false,
					errorElement: 'li',
					errorPlacement: function (error, element) {
						if(element.attr('name') == 'terms')
						{
							error.appendTo(element.parent().next().find('ul'));
						}else{
							error.appendTo(element.next().find('ul'));
						}
					},
		            highlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'terms')
						{
							element.parent().parent().removeClass('has-success').addClass('has-error');
						}else{
							element.parent().removeClass('has-success').addClass('has-error');
						}
		            },
		            unhighlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	if(element.attr('name') == 'terms')
						{
							element.parent().parent().removeClass('has-error').addClass('has-success');
						}else{
							element.parent().removeClass('has-error').addClass('has-success');
						}
		            },
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout"].join(""), $form.serialize()).done(function (data) {
							if (data.status == "OK") {
								if (!hashBang("#!/loadPreview")) 
								{
									self.loadPreview.call(self);
								}
							}else{
								pjQ.$('#pjCaptchaMsg_' + self.opts.index).show();
								setTimeout(function(){ pjQ.$('#pjCaptchaMsg_' + self.opts.index).hide(); }, 3000);
								self.enableButtons.call(self);
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
				
				var $form = pjQ.$('#pjCssLoginForm_'+ self.opts.index);
				$form.validate({
					onkeyup: false,
					errorElement: 'li',
					errorPlacement: function (error, element) {
						if(element.attr('name') == 'terms')
						{
							error.appendTo(element.parent().next().find('ul'));
						}else{
							error.appendTo(element.next().find('ul'));
						}
					},
		            highlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	element.parent().removeClass('has-success').addClass('has-error');
		            },
		            unhighlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	element.parent().removeClass('has-error').addClass('has-success');
		            },
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionCheckLogin"].join(""), $form.serialize()).done(function (data) {
							if(data.code == '200')
							{
								pjQ.$('#pjCssLoginModal').modal('hide');
								if (!hashBang("#!/loadCheckout")) 
								{
									self.loadCheckout.call(self);
								}
							}else{
								var $loginMessage = pjQ.$('#pjLoginMessage_'+ self.opts.index);
								$loginMessage.html(data.text);
								$loginMessage.parent().parent().show();
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
				
				var $form = pjQ.$('#pjCssForgotForm_'+ self.opts.index);
				$form.validate({
					onkeyup: false,
					errorElement: 'li',
					errorPlacement: function (error, element) {
						if(element.attr('name') == 'terms')
						{
							error.appendTo(element.parent().next().find('ul'));
						}else{
							error.appendTo(element.next().find('ul'));
						}
					},
		            highlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	element.parent().removeClass('has-success').addClass('has-error');
		            },
		            unhighlight: function(ele, errorClass, validClass) {
		            	var element = pjQ.$(ele);
		            	element.parent().removeClass('has-error').addClass('has-success');
		            },
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSendPassword"].join(""), $form.serialize()).done(function (data) {
							var $forgotMessage = pjQ.$('#pjForgotMessage_'+ self.opts.index);
							if(data.code == '200')
							{
								$forgotMessage.addClass('text-success');
							}else{
								$forgotMessage.addClass('text-danger');
							}
							$forgotMessage.html(data.text);
							$forgotMessage.parent().parent().show();
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
			}
		},
		loadPreview: function () {
			var self = this,
				index = this.opts.index,
				params = {};
			params.locale = self.opts.locale;
			params.index = self.opts.index;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionPreview"].join(""), params).done(function (data) {
				if (data.code != undefined && data.status == 'ERR') {
					if (!hashBang("#!/loadClasses")) 
					{
						self.loadClasses.call(self);
					}
				}else{
					self.$container.html(data);
					self.bindPreview.call(self);
					pjQ.$('html, body').animate({
				        scrollTop: self.$container.offset().top
				    }, 500);
				}
			}).fail(function () {
				
			});
		},
		bindPreview: function(){
			var self = this,
				index = this.opts.index;
		
			if (validate) 
			{
				var $form = pjQ.$('#pjCssPreviewForm_'+ self.opts.index);
				$form.validate({
					submitHandler: function (form) {
						self.disableButtons.call(self);
						var $form = pjQ.$(form);
						pjQ.$.post([self.opts.folder, "index.php?controller=pjFrontEnd&action=pjActionSaveBooking"].join(""), $form.serialize()).done(function (data) {
							if (data.code == "200") {
								self.getPaymentForm.call(self, data);
							} else if (data.code == "119") {
								self.enableButtons.call(self);
							}
						}).fail(function () {
							self.enableButtons.call(self);
						});
						return false;
					}
				});
			}
		},
		getPaymentForm: function(obj){
			var self = this,
				index = this.opts.index;
			var	params = {};
			params.locale = self.opts.locale;
			params.index = self.opts.index;
			params.booking_id =  obj.booking_id;
			params.payment_method = obj.payment;
			if(self.opts.session_id != '')
			{
				params.session_id = self.opts.session_id;
			}
			pjQ.$.get([this.opts.folder, "index.php?controller=pjFrontPublic&action=pjActionGetPaymentForm"].join(""), params).done(function (data) {
				self.$container.html(data);
				switch (obj.payment) {
					case 'paypal':
						self.$container.find("form[name='cssPaypal']").trigger('submit');
						break;
					case 'authorize':
						self.$container.find("form[name='cssAuthorize']").trigger('submit');
						break;
					case 'creditcard':
					case 'bank':
					case 'cash':
						break;
				}
				pjQ.$('html, body').animate({
			        scrollTop: self.$container.offset().top
			    }, 500);
			}).fail(function () {
				log("Deferred is rejected");
			});
		}
	};
	
	window.ClassScheduling = ClassScheduling;	
})(window);