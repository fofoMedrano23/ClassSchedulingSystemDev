<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontPublic extends pjFront
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setAjax(true);
		
		$this->setLayout('pjActionEmpty');
	}
	
	public function pjActionClasses()
	{
		if($this->isXHR())
		{
			$pjClassModel = pjClassModel::factory();
			$pjCourseModel = pjCourseModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCourse' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
				->join('pjMultiLang', "t4.foreign_id = t1.id AND t4.model = 'pjCourse' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'duration'", 'left');
			
			$column_map = array('date' => 'start_date', 'size' => 'size', 'price' => 'price');
			$column = isset($_GET['order']) ? $column_map[$_GET['order']] : 'start_date';
			$direction = 'ASC';
			
			$arr = $pjCourseModel
				->select("t1.*, t2.content as title, t3.content as description, t4.content as duration, (SELECT TC.start_date FROM `".$pjClassModel->getTable()."` AS TC WHERE TC.course_id=t1.id AND TC.start_date > CURDATE() ORDER BY TC.start_date ASC LIMIT 1) AS start_date")
				->where('t1.status', 'T')
				->orderBy("$column $direction")
				->findAll()
				->getData();
			
			$class_arr = array();
			$course_id_arr = $pjCourseModel->findAll()->getDataPair(null, 'id');
			if(!empty($course_id_arr))
			{
				$temp_class_arr = $pjClassModel
					->select("t1.*, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.class_id=t1.id AND `TB`.status != 'cancelled') booked")
					->whereIn('course_id', $course_id_arr)
					->where("(t1.start_date > CURDATE())")
					->orderBy("start_date ASC")
					->findAll()
					->getData();
				foreach($temp_class_arr as $k => $v)
				{
					$class_arr[$v['course_id']][] = $v;
				}
			}
			
			$this->set('arr', $arr);
			$this->set('class_arr', $class_arr);
		}
	}
	
	public function pjActionClass()
	{
		if($this->isXHR())
		{
			if(isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjClassModel = pjClassModel::factory();
				$pjCourseModel = pjCourseModel::factory();
				$arr = $pjCourseModel
					->select("t1.*, t2.content as title, t3.content as description, t4.content as duration")
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCourse' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
					->join('pjMultiLang', "t4.foreign_id = t1.id AND t4.model = 'pjCourse' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'duration'", 'left')
					->find($_GET['id'])->getData();
				if(!empty($arr))
				{
					if($arr['status'] == 'T')
					{
						$this->_set('course_id', $arr['id']);
						
						$class_arr = $pjClassModel
							->select("t1.*, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.class_id=t1.id AND `TB`.status != 'cancelled') booked")
							->where('course_id', $_GET['id'])
							->where("(t1.start_date > CURDATE())")
							->orderBy("start_date ASC")
							->findAll()->getData();
						$class_id_arr = $pjClassModel->findAll()->getDataPair(null, 'id');
						
						if(!empty($class_id_arr))
						{
							$teacher_arr = pjTeacherModel::factory()
								->select("t1.*, t2.content as description")
								->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjTeacher' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'description'", 'left')
								->where('t1.status', 'T')
								->where("(t1.id IN (SELECT TS.teacher_id FROM `".pjScheduleModel::factory()->getTable()."` AS TS WHERE TS.class_id IN(".join(',', $class_id_arr).") ))")
								->findAll()
								->getData();
							$this->set('teacher_arr', $teacher_arr);
						}
						$this->set('arr', $arr);
						$this->set('class_arr', $class_arr);
					}else{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
					}
				}else{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
			}
		}
	}
	public function pjActionCheckout()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['course_id']) &&
					isset($_SESSION[$this->defaultStore]['class_id']))
			{
				if(isset($_POST['css_checkout']))
				{
					if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_POST['captcha']) ||
							!pjCaptcha::validate($_POST['captcha'], $_SESSION[$this->defaultCaptcha]) ))
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 110));
					}
					
					$_SESSION[$this->defaultForm] = $_POST;
			
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200));
				}else{
					$course_id = $this->_get('course_id');
					$class_id = $this->_get('class_id');
					
					$pjCourseModel = pjCourseModel::factory();
					$pjClassModel = pjClassModel::factory();
					
					$arr = $pjCourseModel
						->select("t1.*, t2.content as title, t3.content as description, t4.content as duration")
						->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
						->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCourse' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
						->join('pjMultiLang', "t4.foreign_id = t1.id AND t4.model = 'pjCourse' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'duration'", 'left')
						->find($course_id)->getData();
					$class_arr = $pjClassModel->find($class_id)->getData();
					
					$price_arr = pjAppController::calPrice($course_id, $this->option_arr);
					
					$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')
						->findAll()
						->getData();
                                        
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
					
					$this->set('arr', $arr);
					$this->set('class_arr', $class_arr);
					$this->set('price_arr', $price_arr);
					$this->set('country_arr', $country_arr);
					$this->set('terms_conditions', $terms_conditions);
				}
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				exit;
			}
		}
	}
	public function pjActionPreview()
	{
		if($this->isXHR())
		{
			if (isset($_SESSION[$this->defaultStore]) &&
					count($_SESSION[$this->defaultStore]) > 0 &&
					isset($_SESSION[$this->defaultStore]['course_id']) &&
					isset($_SESSION[$this->defaultStore]['class_id']))
			{
				$course_id = $this->_get('course_id');
				$class_id = $this->_get('class_id');
					
				$pjCourseModel = pjCourseModel::factory();
				$pjClassModel = pjClassModel::factory();
					
				$arr = $pjCourseModel
					->select("t1.*, t2.content as title, t3.content as description, t4.content as duration")
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCourse' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
					->join('pjMultiLang', "t4.foreign_id = t1.id AND t4.model = 'pjCourse' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'duration'", 'left')
					->find($course_id)->getData();
				$class_arr = $pjClassModel->find($class_id)->getData();
					
				$price_arr = pjAppController::calPrice($course_id, $this->option_arr);
				if(isset($_SESSION[$this->defaultForm]['c_country']) && (int) $_SESSION[$this->defaultForm]['c_country'] > 0)
				{
					$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->find($_SESSION[$this->defaultForm]['c_country'])
						->getData();
					$this->set('country_arr', $country_arr);
				}
				$this->set('arr', $arr);
				$this->set('class_arr', $class_arr);
				$this->set('price_arr', $price_arr);
			}else{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => ''));
				exit;
			}
		}
	}
	public function pjActionGetPaymentForm()
	{
		if ($this->isXHR())
		{
			$arr = pjBookingModel::factory()->find($_GET['booking_id'])->getData();
				
			if (!empty($arr))
			{
				switch ($arr['payment_method'])
				{
					case 'paypal':
						$this->set('params', array(
							'name' => 'cssPaypal',
							'id' => 'cssPaypal',
							'business' => $this->option_arr['o_paypal_address'],
							'item_name' => pjSanitize::html($arr['uuid']),
							'custom' => $arr['id'],
							'amount' => $arr['deposit'],
							'currency_code' => $this->option_arr['o_currency'],
							'return' => $this->option_arr['o_thankyou_page'],
							'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionConfirmPaypal',
							'target' => '_self',
							'charset' => 'utf-8'
						));
						break;
					case 'authorize':
						$this->set('params', array(
							'name' => 'cssAuthorize',
							'id' => 'cssAuthorize',
							'target' => '_self',
							'timezone' => $this->option_arr['o_authorize_timezone'],
							'transkey' => $this->option_arr['o_authorize_transkey'],
							'x_login' => $this->option_arr['o_authorize_merchant_id'],
							'x_description' => pjSanitize::html($arr['uuid']),
							'x_amount' => $arr['deposit'],
							'x_invoice_num' => $arr['id'],
							'x_receipt_link_url' => $this->option_arr['o_thankyou_page'],
							'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionConfirmAuthorize'
						));
						break;
				}
			}
			$this->set('arr', $arr);
			$this->set('get', $_GET);
		}
	}
	
}
?>