<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminBookings extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory()
				->join('pjMultiLang', "t2.model='pjCourse' AND t2.foreign_id=t1.course_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->join('pjClass', "t3.id=t1.class_id", 'left')
				->join('pjStudent', "t4.id=t1.student_id", 'left');
				
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjBookingModel->where("(t1.id = '$q' OR t1.uuid = '$q' OR t4.name LIKE '%$q%' OR t2.content LIKE '%$q%')");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('confirmed','cancelled','pending')))
			{
				$pjBookingModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['student_id']) && (int) $_GET['student_id'] > 0)
			{
				$pjBookingModel->where('t1.student_id', $_GET['student_id']);
			}
			if (isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
			{
				$pjBookingModel->where('t1.class_id', $_GET['class_id']);
			}
			if (isset($_GET['start_date']) && !empty($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['end_date']))
			{
				$start_date = pjUtil::formatDate($_GET['start_date'], $this->option_arr['o_date_format']);
				$end_date = pjUtil::formatDate($_GET['end_date'], $this->option_arr['o_date_format']);
				$pjBookingModel->where("(t1.start_date BETWEEN '$start_date' AND '$end_date')");
			}elseif(isset($_GET['start_date']) && !empty($_GET['start_date']) && isset($_GET['end_date']) && empty($_GET['end_date'])){
				$start_date = pjUtil::formatDate($_GET['start_date'], $this->option_arr['o_date_format']);
				$pjBookingModel->where("(`start_date` >= '$start_date')");
			}elseif(isset($_GET['start_date']) && empty($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['end_date'])){
				$end_date = pjUtil::formatDate($_GET['end_date'], $this->option_arr['o_date_format']);
				$pjBookingModel->where("(`start_date` <= '$end_date')");
			}
			
			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjBookingModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjBookingModel
				->select("t1.*, t2.content as class, t4.name, t3.start_date, 
								AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			
			foreach($data as $k => $v)
			{
				$v['class'] = $v['class'] . ' ('. date($this->option_arr['o_date_format'], strtotime($v['start_date'])) .')';
				$v['name'] = pjSanitize::html($v['name']);
				$data[$k] = $v;
			}
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionPayments()
	{
		$this->checkLogin();
	
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminHistory.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if($_POST['column'] == 'status')
			{
				$pjBookingModel = pjBookingModel::factory();
				$arr = $pjBookingModel->find($_GET['id'])->getData();
				if($arr['status'] == 'cancelled' && $_POST['value'] != 'cancelled')
				{
					$course_arr = pjCourseModel::factory()->find($arr['course_id'])->getData();
					$cnt_bookings = $pjBookingModel->reset()->where('class_id', $arr['class_id'])->where('status <>', 'cancelled')->findCount()->getData();
					if($cnt_bookings < $course_arr['size'])
					{
						$pjBookingModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
					}
				}else{
					$pjBookingModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
				}
			}else{
				pjBookingModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			}
		}
		exit;
	}
	
	public function pjActionExportBooking()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjBookingModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Bookings-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionDeleteBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjBookingModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjBookingPaymentModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBookingBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjBookingModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjBookingPaymentModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['booking_create']))
			{
				$data = array();
				
				$class_arr = pjClassModel::factory()->find($_POST['class_id'])->getData();
				
				if($_POST['student_type'] == 'new')
				{
					$student_data = array();
					$student_data['email'] = isset($_POST['email']) ? $_POST['email'] : ':NULL';
					$student_data['password'] = 'pass';
					$student_data['title'] = isset($_POST['title']) ? $_POST['title'] : ':NULL';
					$student_data['name'] = isset($_POST['name']) ? $_POST['name'] : ':NULL';
					$student_data['phone'] = isset($_POST['phone']) ? $_POST['phone'] : ':NULL';
					$student_data['education'] = isset($_POST['education']) ? $_POST['education'] : ':NULL';
					$student_data['birthdate'] = isset($_POST['birthdate']) ? $_POST['birthdate'] : ':NULL';
					$student_data['gender'] = isset($_POST['gender']) ? $_POST['gender'] : ':NULL';
					$student_data['experience'] = isset($_POST['experience']) ? $_POST['experience'] : ':NULL';
					$student_data['country_id'] = isset($_POST['country_id']) ? $_POST['country_id'] : ':NULL';
					$student_data['status'] = 'T';
					
					$student_id = pjStudentModel::factory()->setAttributes($student_data)->insert()->getInsertId();
					$data['student_id'] = $student_id;
				}
				
				$data['uuid'] = time();
				$data['ip'] = pjUtil::getClientIp();
				$data['course_id'] = $class_arr['course_id'];
								
				$id = pjBookingModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AR03';
				}else{
					$err = 'AR04';
				}
				
				if(isset($_POST['edit_class_id']))
				{
					if($_POST['edit_class_id'] == $_POST['class_id'])
					{
						pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminSchedule&action=pjActionEdit&id=".$_POST['class_id']."&err=$err#tabs-2");
					}else{
						pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
					}
				}else{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
				}
			}else{
				
				$class_arr = pjClassModel::factory()
					->select("t1.*, t2.content AS course, t3.price, t3.size, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.class_id=t1.id AND `TB`.status != 'cancelled') booked")
					->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->join('pjCourse', "t3.id = t1.course_id ", 'left')
					->where("t3.status", 'T')
					->orderBy("course ASC, start_date ASC")
					->findAll()
					->getData();
				$this->set('class_arr', $class_arr);
				
				$student_arr = pjStudentModel::factory()->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				$this->set('student_arr', $student_arr);
				
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
				
				$this->set('country_arr', $country_arr);
				
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
                                
                                $education_arr = pjEducationModel::factory()
					->findAll()
					->getData();
				
				$this->set('education_arr', $education_arr);
		
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminStudents.js');
                                
                                $gender_arr = pjGenderModel::factory()
					->findAll()
					->getData();
				
				$this->set('gender_arr', $gender_arr);
		
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminStudents.js');
			}
		} else {
			
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['booking_update']))
			{
				$pjBookingModel = pjBookingModel::factory();
				
				$arr = pjBookingModel::factory()->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AR08");
				}
				
				$data = array();
				
				$class_arr = pjClassModel::factory()->find($_POST['class_id'])->getData();
				
				if($_POST['student_type'] == 'new')
				{
					$student_data = array();
					$student_data['email'] = isset($_POST['email']) ? $_POST['email'] : ':NULL';
					$student_data['password'] = 'pass';
					$student_data['title'] = isset($_POST['title']) ? $_POST['title'] : ':NULL';
					$student_data['name'] = isset($_POST['name']) ? $_POST['name'] : ':NULL';
					$student_data['phone'] = isset($_POST['phone']) ? $_POST['phone'] : ':NULL';
					$student_data['education'] = isset($_POST['education']) ? $_POST['education'] : ':NULL';
					$student_data['birthdate'] = isset($_POST['birthdate']) ? $_POST['birthdate'] : ':NULL';
					$student_data['gender'] = isset($_POST['gender']) ? $_POST['gender'] : ':NULL';
					$student_data['experience'] = isset($_POST['experience']) ? $_POST['experience'] : ':NULL';
					$student_data['country_id'] = isset($_POST['country_id']) ? $_POST['country_id'] : ':NULL';
					$student_data['status'] = 'T';
					
					$student_id = pjStudentModel::factory()->setAttributes($student_data)->insert()->getInsertId();
					$data['student_id'] = $student_id;
				}
				
				$data['ip'] = pjUtil::getClientIp();
				$data['course_id'] = $class_arr['course_id'];
				
				$pjBookingModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				$err = 'AR01';
				if($arr['status'] == 'cancelled' && $_POST['status'] != 'cancelled')
				{
					$course_id = $class_arr['course_id'];
					$course_arr = pjCourseModel::factory()->find($course_id)->getData();
					$cnt_bookings = $pjBookingModel->reset()->where('class_id', $_POST['class_id'])->where('status <>', 'cancelled')->findCount()->getData();
					if($cnt_bookings >= $course_arr['size'])
					{
						$pjBookingModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array('status' => 'cancelled'));
						$err = 'AR09';
					}
				}
				
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			}else{
				
				$arr = pjBookingModel::factory()->find($_GET['id'])->getData();
				if(count($arr) <= 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=AR08");
				}
				$this->set('arr', $arr);
				
				$class_arr = pjClassModel::factory()
				->select("t1.*, t2.content AS course, t3.price")
				->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->join('pjCourse', "t3.id = t1.course_id ", 'left')
				->where("t3.status", 'T')
				->orderBy("course ASC, start_date ASC")
				->findAll()
				->getData();
				$this->set('class_arr', $class_arr);
				
				$student_arr = pjStudentModel::factory()->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				$this->set('student_arr', $student_arr);
				
				$country_arr = pjCountryModel::factory()
				->select('t1.id, t2.content AS country_title')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->orderBy('`country_title` ASC')
				->findAll()
				->getData();
				
				$this->set('country_arr', $country_arr);
				
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
                                
                                $education_arr = pjEducationModel::factory()
					->findAll()
					->getData();
				
				$this->set('education_arr', $education_arr);
		
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminStudents.js');
                                
                                $gender_arr = pjGenderModel::factory()
					->findAll()
					->getData();
				
				$this->set('gender_arr', $gender_arr);
		
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminStudents.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	public function pjActionConfirmation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['send_confirm']) && !empty($_POST['to']) && !empty($_POST['from']) &&
					!empty($_POST['subject']) && !empty($_POST['message']))
			{
				$Email = new pjEmail();
				$Email->setContentType('text/html');
				
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
						->setSender($this->option_arr['o_smtp_user']);
				}
	
				$subject = $_POST['subject'];
				$message = $_POST['message'];
				if (get_magic_quotes_gpc())
				{
					$subject = stripslashes($_POST['subject']);
					$message = stripslashes($_POST['message']);
				}
				
				$r = $Email
					->setTo($_POST['to'])
					->setFrom($_POST['from'])
					->setSubject($subject)
					->send($message);
					
				if ($r)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email failed to send.'));
			}
	
			if (isset($_GET['booking_id']) && (int) $_GET['booking_id'] > 0)
			{
				$pjMultiLangModel = pjMultiLangModel::factory();
				$lang_message = $pjMultiLangModel->reset()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_email_confirmation_message')
					->limit(0, 1)
					->findAll()->getData();
				$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_email_confirmation_subject')
					->limit(0, 1)
					->findAll()->getData();
	
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					$booking_arr = pjBookingModel::factory()->find($_GET['booking_id'])->getData();
					$tokens = pjAppController::getTokens($_GET['booking_id'], $this->option_arr, PJ_SALT, $this->getLocaleId());
					
					$student = pjStudentModel::factory()->find($booking_arr['student_id'])->getData();
					
					$subject_client = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
					$message_client = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
					$this->set('arr', array(
							'to' => $student['email'],
							'from' => $this->getAdminEmail(),
							'message' => $message_client,
							'subject' => $subject_client
					));
				}
			} else {
				exit;
			}
		}
	}
	
	public function pjActionCancellation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['send_cancellation']) && !empty($_POST['to']) && !empty($_POST['from']) &&
					!empty($_POST['subject']) && !empty($_POST['message']))
			{
				$Email = new pjEmail();
				$Email->setContentType('text/html');
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
					->setTransport('smtp')
					->setSmtpHost($this->option_arr['o_smtp_host'])
					->setSmtpPort($this->option_arr['o_smtp_port'])
					->setSmtpUser($this->option_arr['o_smtp_user'])
					->setSmtpPass($this->option_arr['o_smtp_pass'])
					->setSender($this->option_arr['o_smtp_user']);
				}
	
				$subject = $_POST['subject'];
				$message = $_POST['message'];
				if (get_magic_quotes_gpc())
				{
					$subject = stripslashes($_POST['subject']);
					$message = stripslashes($_POST['message']);
				}
	
				$r = $Email
				->setTo($_POST['to'])
				->setFrom($_POST['from'])
				->setSubject($subject)
				->send($message);
					
				if ($r)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email failed to send.'));
			}
	
			if (isset($_GET['booking_id']) && (int) $_GET['booking_id'] > 0)
			{
				$pjMultiLangModel = pjMultiLangModel::factory();
				$lang_message = $pjMultiLangModel->reset()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_email_cancel_message')
					->limit(0, 1)
					->findAll()->getData();
				$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_email_cancel_subject')
					->limit(0, 1)
					->findAll()->getData();
	
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					$booking_arr = pjBookingModel::factory()->find($_GET['booking_id'])->getData();
					$tokens = pjAppController::getTokens($_GET['booking_id'], $this->option_arr, PJ_SALT, $this->getLocaleId());
	
					$student = pjStudentModel::factory()->find($booking_arr['student_id'])->getData();
					
					$subject_client = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
					$message_client = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
	
					$this->set('arr', array(
							'to' => $student['email'],
							'from' => $this->getAdminEmail(),
							'message' => $message_client,
							'subject' => $subject_client
					));
				}
			} else {
				exit;
			}
		}
	}
	
	public function pjActionGetClasses()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if(isset($_GET['student_id']) && (int) $_GET['student_id'] > 0)
			{
				$class_arr = pjClassModel::factory()
					->select("t1.*, t2.content AS course")
					->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->where("(t1.id IN(SELECT `TB`.class_id FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.student_id='".$_GET['student_id']."'))")
					->orderBy("course ASC, start_date ASC")
					->findAll()
					->getData();
				$this->set('class_arr', $class_arr);
			}
		}
	}
	public function pjActionCreatePayment()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			if (isset($_POST['payment_create']))
			{
				$data = array();
				$data['created'] = date('Y-m-d H:i:s');
				$id = pjStudentPaymentModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjPayment', 'data');
					}
					$err = 'ASP03';
				}else{
					$err = 'ASP04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionPayments&err=$err");
			} else {
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();

				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));

				$student_arr = pjStudentModel::factory()->where('status', 'T')->where("(t1.id IN(SELECT `TB`.student_id FROM `".pjBookingModel::factory()->getTable()."` AS `TB`))")->orderBy('name ASC')->findAll()->getData();
				$this->set('student_arr', $student_arr);
				
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminHistory.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	public function pjActionUpdatePayment()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			if (isset($_POST['payment_update']))
			{
				pjStudentPaymentModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll($_POST);
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjPayment', 'data');
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionPayments&err=ASP01");
			} else {
				$arr = pjStudentPaymentModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionPayments&err=ASP08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjPayment');
				$this->set('arr', $arr);

				$class_arr = pjClassModel::factory()
					->select("t1.*, t2.content AS course")
					->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->where("(t1.id IN(SELECT `TB`.class_id FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.student_id='".$arr['student_id']."'))")
					->orderBy("course ASC, start_date ASC")
					->findAll()
					->getData();
				$this->set('class_arr', $class_arr);

				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();

				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));

				$student_arr = pjStudentModel::factory()->where('status', 'T')->where("(t1.id IN(SELECT `TB`.student_id FROM `".pjBookingModel::factory()->getTable()."` AS `TB`))")->orderBy('name ASC')->findAll()->getData();
				$this->set('student_arr', $student_arr);
				
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminHistory.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>
