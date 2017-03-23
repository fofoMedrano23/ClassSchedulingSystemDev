<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontEnd extends pjFront
{
	public function __construct()
	{
		parent::__construct();
		$this->setAjax(true);
		$this->setLayout('pjActionEmpty');
	}

	public function pjActionLoad()
	{
		$this->setAjax(false);
		$this->setLayout('pjActionFront');
		
		$_terms_conditions = pjMultiLangModel::factory()->select('t1.*')
			->where('t1.model','pjOption')
			->where('t1.locale', $this->getLocaleId())
			->where('t1.field', 'o_terms')
			->limit(0, 1)
			->findAll()->getData();
		$terms_conditions = '';
		if(!empty($_terms_conditions))
		{
			$terms_conditions = $_terms_conditions[0]['content'];
		}
		$this->set('terms_conditions', $terms_conditions);
		
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLoadCss()
	{
		$dm = new pjDependencyManager(PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	
		$theme = $this->option_arr['o_theme'];
		$fonts = $this->option_arr['o_theme'];
		if(isset($_GET['theme']) && in_array($_GET['theme'], array('theme1', 'theme2', 'theme3', 'theme4', 'theme5', 'theme6', 'theme7', 'theme8', 'theme9', 'theme10')))
		{
			$theme = $_GET['theme'];
			$fonts = $_GET['theme'];
		}
		$arr = array(
				array('file' => "$fonts.css", 'path' => PJ_CSS_PATH . "fonts/"),
				array('file' => 'style.css', 'path' => PJ_CSS_PATH),
				array('file' => "$theme.css", 'path' => PJ_CSS_PATH . "themes/",
				array('file' => 'transitions.css', 'path' => PJ_CSS_PATH))
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();
				
			if ($string !== FALSE)
			{
				echo str_replace(
						array('../fonts/glyphicons', "pjWrapper"),
						array(
								PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/fonts/glyphicons',
								"pjWrapperClassScheduling_" . $theme
						),
						$string
				) . "\n";
			}
		}
		exit;
	}
	
	public function pjActionCaptcha()
	{
		$Captcha = new pjCaptcha('app/web/obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage('app/web/img/button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
	}

	public function pjActionCheckCaptcha()
	{
		if (!isset($_GET['captcha']) || empty($_GET['captcha']) || strtoupper($_GET['captcha']) != $_SESSION[$this->defaultCaptcha]){
			echo 'false';
		}else{
			echo 'true';
		}
		exit;
	}
	
	public function pjActionSetClass()
	{
		if($this->isXHR())
		{
			if(isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
			{
				$this->_set('class_id', $_GET['class_id']);
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}
	public function pjActionCheckLogin()
	{
		if($this->isXHR())
		{
			$login_err = __('login_err', true);
			if(isset($_POST['css_login']))
			{
				$pjStudentModel = pjStudentModel::factory();
				
				$student = $pjStudentModel
					->where('t1.email', $_POST['login_email'])
					->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", $pjStudentModel->escapeStr($_POST['login_password']), PJ_SALT))
					->limit(1)
					->findAll()
					->getData();
				
				if (count($student) != 1)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => $login_err[100]));
				} else {
					$student = $student[0];
					if ($student['status'] != 'T')
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => $login_err[101]));
					}
		
					$last_login = date("Y-m-d H:i:s");
					$_SESSION[$this->defaultFrontStudent] = $student;
			
					$data = array();
					$data['last_login'] = $last_login;
					$pjStudentModel->reset()->setAttributes(array('id' => $student['id']))->modify($data);
				
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => $login_err[102]));
			}
		}
	}
	public function pjActionSendPassword()
	{
		if($this->isXHR())
		{
			$forgot_err = __('forgot_err', true);
			if(isset($_POST['css_forgot']))
			{
				$pjStudentModel = pjStudentModel::factory();
				$student = $pjStudentModel
					->select("t1.*, AES_DECRYPT(t1.password, '".PJ_SALT."') AS `password`")
					->where('t1.email', $_POST['email'])
					->limit(1)
					->findAll()
					->getData();
	
				if (count($student) != 1)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => $forgot_err[100]));
				} else {
					$student = $student[0];
					if ($student['status'] != 'T')
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => $forgot_err[101]));
					}
					
					pjFrontEnd::pjActionConfirmSendStudent($this->option_arr, $student['id'], PJ_SALT, 'forgot');
					
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => $forgot_err[200]));
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => $forgot_err[102]));
			}
		}
	}
	public function pjActionLogout()
	{
		if($this->isXHR())
		{
			if(isset($_SESSION[$this->defaultFrontStudent]))
			{
				unset($_SESSION[$this->defaultFrontStudent]);
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
		}
	}
	public function pjActionSaveBooking()
	{
		if ($this->isXHR())
		{
			if (!isset($_POST['css_preview']) || !isset($_SESSION[$this->defaultForm]) || empty($_SESSION[$this->defaultForm]) || !isset($_SESSION[$this->defaultStore]) || empty($_SESSION[$this->defaultStore]))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 109));
			}
			if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_SESSION[$this->defaultForm]['captcha']) ||
					!pjCaptcha::validate($_SESSION[$this->defaultForm]['captcha'], $_SESSION[$this->defaultCaptcha]) ))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110));
			}
	
			$STORE = @$_SESSION[$this->defaultStore];
			$FORM = @$_SESSION[$this->defaultForm];
	
			$data = array();
			$student_data = array();
			$pjStudentModel = pjStudentModel::factory();
			
			$data['student_id'] = ':NULL';
			
			$student_data['email'] = isset($FORM['c_email']) ? $FORM['c_email'] : ':NULL';
			$student_data['password'] = isset($FORM['c_password']) ? $FORM['c_password'] : 'pass';
			$student_data['email'] = isset($FORM['c_email']) ? $FORM['c_email'] : ':NULL';
			$student_data['title'] = isset($FORM['c_title']) ? $FORM['c_title'] : ':NULL';
			$student_data['name'] = isset($FORM['c_name']) ? $FORM['c_name'] : ':NULL';
			$student_data['phone'] = isset($FORM['c_phone']) ? $FORM['c_phone'] : ':NULL';
			$student_data['education'] = isset($FORM['c_education']) ? $FORM['c_education'] : ':NULL';
			$student_data['birthdate'] = isset($FORM['c_birthdate']) ? $FORM['c_birthdate'] : ':NULL';
			$student_data['gender'] = isset($FORM['c_gender']) ? $FORM['c_gender'] : ':NULL';
			$student_data['experience'] = isset($FORM['c_experience']) ? $FORM['c_experience'] : ':NULL';
			$student_data['country_id'] = isset($FORM['c_country']) ? $FORM['c_country'] : ':NULL';
			
			if($this->isFrontLogged())
			{
				$data['student_id'] = $_SESSION[$this->defaultFrontStudent]['id'];
				$pjStudentModel->reset()->where('id', $data['student_id'])->limit(1)->modifyAll($student_data);
			}else{
				if(isset($FORM['c_email']))
				{
					$student_arr = $pjStudentModel->where('email', $FORM['c_email'])->limit(1)->findAll()->getData();
					if(count($student_arr) == 1)
					{
						$data['student_id'] = $student_arr[0]['id'];
						$pjStudentModel->reset()->where('id', $student_arr[0]['id'])->limit(1)->modifyAll($student_data);
					}
				}
				
				if($data['student_id'] == ':NULL')
				{
					$student_data['status'] = 'T';
					$student_data['created'] = date('Y-m-d H:i:s');
				
					$student_id = $pjStudentModel->reset()->setAttributes($student_data)->insert()->getInsertId();
					if ($student_id !== false && (int) $student_id > 0)
					{
						$data['student_id'] = $student_id;
						pjFrontEnd::pjActionConfirmSendStudent($this->option_arr, $student_id, PJ_SALT, 'account');
					}
				}
			}
			
			$data['uuid'] = time();
			$data['course_id'] = $STORE['course_id'];
			$data['class_id'] = $STORE['class_id'];
			$data['notes'] = $FORM['c_notes'];
			$data['ip'] = pjUtil::getClientIp();
			$data['status'] = $this->option_arr['o_booking_status'];
			$data['created'] = date('Y-m-d H:i:s');
			$payment = ':NULL';
			if(isset($FORM['payment_method']))
			{
				if (isset($FORM['payment_method'])){
					$payment = $FORM['payment_method'];
				}
			}
			
			$price_arr = pjAppController::calPrice($STORE['course_id'], $this->option_arr);
			
			$data['subtotal'] = $price_arr['subtotal'];
			$data['tax'] = $price_arr['tax'];
			$data['total'] = $price_arr['total'];
			$data['deposit'] = $price_arr['deposit'];
			
			$pjBookingModel = pjBookingModel::factory();
			$id = $pjBookingModel->setAttributes(array_merge($FORM, $data))->insert()->getInsertId();
			if ($id !== false && (int) $id > 0)
			{
				$arr = $pjBookingModel->reset()->find($id)->getData();
	
				$pdata = array();
				$pdata['booking_id'] = $id;
				$pdata['payment_method'] = $payment;
				$pdata['payment_type'] = 'online';
				$pdata['amount'] = $arr['deposit'];
				$pdata['status'] = 'notpaid';
				pjBookingPaymentModel::factory()->setAttributes($pdata)->insert();
	
				pjFrontEnd::pjActionConfirmSend($this->option_arr, $id, PJ_SALT, 'confirm');
	
				unset($_SESSION[$this->defaultStore]);
				unset($_SESSION[$this->defaultForm]);
					
				$json = array('code' => 200, 'text' => '', 'booking_id' => $id, 'payment' => $payment);
				pjAppController::jsonResponse($json);
			}else {
				pjAppController::jsonResponse(array('code' => 'ERR', 'code' => 119));
			}
		}
	}
		
	public function pjActionConfirmAuthorize()
	{
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		$pjBookingModel = pjBookingModel::factory();
	
		$booking_arr = $pjBookingModel->find($_POST['x_invoice_num'])->getData();
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
	
		if (count($booking_arr) > 0)
		{
			$params = array(
					'transkey' => $this->option_arr['o_authorize_transkey'],
					'x_login' => $this->option_arr['o_authorize_merchant_id'],
					'md5_setting' => $this->option_arr['o_authorize_md5_hash'],
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
			);
	
			$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
			if ($response !== FALSE && $response['status'] === 'OK')
			{
				$pjBookingModel->reset()
					->setAttributes(array('id' => $response['transaction_id']))
					->modify(array('status' => $this->option_arr['o_payment_status'], 'processed_on' => ':NOW()'));
	
				pjBookingPaymentModel::factory()
					->setAttributes(array('booking_id' => $response['transaction_id'], 'payment_type' => 'online'))
					->modify(array('status' => 'paid'));

				$pdata = array();
				$pdata['student_id'] = $booking_arr['student_id'];
				$pdata['class_id'] = $booking_arr['class_id'];
				$pdata['amount'] = $booking_arr['deposit'];
				$pdata['status'] = 'paid';
				$pdata['created'] = date('Y-m-d H:i:s');
				pjStudentPaymentModel::factory($pdata)->insert()->getInsertId();
				
				pjFrontEnd::pjActionConfirmSend($this->option_arr, $booking_arr['id'], PJ_SALT, 'payment');
	
			} elseif (!$response) {
				$this->log('Authorization failed');
			} else {
				$this->log('Booking not confirmed. ' . $response['response_reason_text']);
			}
			?>
				<script type="text/javascript">window.location.href="<?php echo $this->option_arr['o_thankyou_page']; ?>";</script>
			<?php
			return;
		}
	}
		
	public function pjActionConfirmPaypal()
	{
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		$pjBookingModel = pjBookingModel::factory();
	
		$booking_arr = $pjBookingModel->find($_POST['custom'])->getData();
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
	
		$params = array(
				'txn_id' => @$booking_arr['txn_id'],
				'paypal_address' => $this->option_arr['o_paypal_address'],
				'deposit' => @$booking_arr['deposit'],
				'currency' => $this->option_arr['o_currency'],
				'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
	
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Booking confirmed');
			$pjBookingModel->reset()->setAttributes(array('id' => $booking_arr['id']))->modify(array(
					'status' => $this->option_arr['o_payment_status'],
					'txn_id' => $response['transaction_id'],
					'processed_on' => ':NOW()'
			));
			pjBookingPaymentModel::factory()
				->setAttributes(array('booking_id' => $booking_arr['id'], 'payment_type' => 'online'))
				->modify(array('status' => 'paid'));

			$pdata = array();
			$pdata['student_id'] = $booking_arr['student_id'];
			$pdata['class_id'] = $booking_arr['class_id'];
			$pdata['amount'] = $booking_arr['deposit'];
			$pdata['status'] = 'paid';
			$pdata['created'] = date('Y-m-d H:i:s');
			pjStudentPaymentModel::factory($pdata)->insert()->getInsertId();
				
			pjFrontEnd::pjActionConfirmSend($this->option_arr, $booking_arr['id'], PJ_SALT, 'payment');
				
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Booking not confirmed');
		}
		pjUtil::redirect($this->option_arr['o_thankyou_page']);
	}
		
	public function pjActionCancel()
	{
		$this->setAjax(false);
		$this->setLayout('pjActionCancel');
	
		$pjBookingModel = pjBookingModel::factory();
	
		if (isset($_POST['booking_cancel']))
		{
			$booking_arr = $pjBookingModel->find($_POST['id'])->getData();
			if (count($booking_arr) > 0)
			{
				$sql = "UPDATE `".$pjBookingModel->getTable()."` SET status = 'cancelled' WHERE SHA1(CONCAT(`id`, `created`, '".PJ_SALT."')) = '" . $_POST['hash'] . "'";
	
				$pjBookingModel->reset()->execute($sql);
	
				$arr = $pjBookingModel->reset()->find($_POST['id'])->getData();
				pjFrontEnd::pjActionConfirmSend($this->option_arr, $arr['id'], PJ_SALT, 'cancel');
	
				pjUtil::redirect($_SERVER['PHP_SELF'] . '?controller=pjFrontEnd&action=pjActionCancel&err=200');
			}
		}else{
			if (isset($_GET['hash']) && isset($_GET['id']))
			{
				$arr = $pjBookingModel
					->reset()
					->select("t1.*, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
					->find($_GET['id'])
					->getData();
				if (count($arr) == 0)
				{
					$this->set('status', 2);
				}else{
					if ($arr['status'] == 'cancelled')
					{
						$this->set('status', 4);
					}else{
						$hash = sha1($arr['id'] . $arr['created'] . PJ_SALT);
						if ($_GET['hash'] != $hash)
						{
							$this->set('status', 3);
						}else{
							$student = pjStudentModel::factory()
								->select("t1.*, t2.content as country_title")
								->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.country_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
								->find($arr['student_id'])->getData();
							$class = pjClassModel::factory()->find($arr['class_id'])->getData();
							$course = pjCourseModel::factory()								
								->select("t1.*, t2.content as title")
								->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
								->find($arr['course_id'])
								->getData();
							
							$this->set('arr', $arr);
							$this->set('student', $student);
							$this->set('class', $class);
							$this->set('course', $course);
						}
					}
				}
			}else if (!isset($_GET['err'])) {
				$this->set('status', 1);
			}
		}
	}

	public function pjActionConfirmSend($option_arr, $booking_id, $salt, $opt)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
			->setTransport('smtp')
			->setSmtpHost($option_arr['o_smtp_host'])
			->setSmtpPort($option_arr['o_smtp_port'])
			->setSmtpUser($option_arr['o_smtp_user'])
			->setSmtpPass($option_arr['o_smtp_pass'])
			->setSender($option_arr['o_smtp_user'])
			;
		}
		$Email->setContentType('text/html');
	
		$admin_phone = $this->getAdminPhone();
		$from_email = $this->getAdminEmail();
	
		$all_admin_emails = $this->getAllAdminEmails();
		
		$locale_id = $this->getLocaleId();
	
		$booking_arr = pjBookingModel::factory()->find($booking_id)->getData();
		$student_arr = pjStudentModel::factory()->find($booking_arr['student_id'])->getData();
	
		$tokens = pjAppController::getTokens($booking_id, $option_arr, PJ_SALT, $locale_id);
	
		$pjMultiLangModel = pjMultiLangModel::factory();
	
		if ($option_arr['o_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_payment_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_payment_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
					->setTo($student_arr['email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if ($option_arr['o_admin_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_payment_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_payment_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				foreach($all_admin_emails as $admin_email)
				{
					$Email
						->setTo($admin_email)
						->setFrom($from_email)
						->setSubject($lang_subject[0]['content'])
						->send($message);
				}
			}
		}
		if(!empty($admin_phone) && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_sms_payment_message')
				->limit(0, 1)
				->findAll()->getData();
			if (count($lang_message) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'type' => 'unicode',
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $admin_phone;
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
	
		if ($option_arr['o_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_confirmation_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
					
				$Email
					->setTo($student_arr['email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if ($option_arr['o_admin_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_confirmation_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				foreach($all_admin_emails as $admin_email)
				{
					$Email
						->setTo($admin_email)
						->setFrom($from_email)
						->setSubject($lang_subject[0]['content'])
						->send($message);
				}
			}
		}
		if(!empty($student_arr['phone']) && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_sms_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			if (count($lang_message) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'type' => 'unicode',
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $student_arr['phone'];
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
		if(!empty($admin_phone) && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_sms_confirmation_message')
				->limit(0, 1)
				->findAll()->getData();
			if (count($lang_message) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				if($message != '')
				{
					$params = array(
							'text' => $message,
							'type' => 'unicode',
							'key' => md5($option_arr['private_key'] . PJ_SALT)
					);
					$params['number'] = $admin_phone;
					$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
				}
			}
		}
	
		if ($option_arr['o_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_cancel_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_cancel_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
					->setTo($student_arr['email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
		if ($option_arr['o_admin_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_cancel_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_admin_email_cancel_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				foreach($all_admin_emails as $admin_email)
				{
					$Email
					->setTo($admin_email)
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
				}
			}
		}
	}
	public function pjActionConfirmSendStudent($option_arr, $student_id, $salt, $opt)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
			->setTransport('smtp')
			->setSmtpHost($option_arr['o_smtp_host'])
			->setSmtpPort($option_arr['o_smtp_port'])
			->setSmtpUser($option_arr['o_smtp_user'])
			->setSmtpPass($option_arr['o_smtp_pass'])
			->setSender($option_arr['o_smtp_user'])
			;
		}
		$Email->setContentType('text/html');
	
		$from_email = $this->getAdminEmail();
	
		$locale_id = $this->getLocaleId();
	
		$student_arr = pjStudentModel::factory()->find($student_id)->getData();
		$tokens = pjAppController::getStudentTokens($student_arr, $option_arr, PJ_SALT, $locale_id);
	
		$pjMultiLangModel = pjMultiLangModel::factory();
	
		if ($option_arr['o_email_account'] == 1 && $opt == 'account')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_account_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_account_subject')
				->limit(0, 1)
				->findAll()->getData();
	
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
				$Email
				->setTo($student_arr['email'])
				->setFrom($from_email)
				->setSubject($lang_subject[0]['content'])
				->send($message);
			}
		}
		if ($option_arr['o_email_forgot'] == 1 && $opt == 'forgot')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_forgot_message')
				->limit(0, 1)
				->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_forgot_subject')
				->limit(0, 1)
				->findAll()->getData();
		
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
		
				$Email
					->setTo($student_arr['email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send($message);
			}
		}
	}
}
?>