<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdmin extends pjAppController
{
	private $imageSizes = array(144, 155);
	
	public $defaultUser = 'admin_user';
	
	public $requireLogin = true;
	
	public function __construct($requireLogin=null)
	{
		$this->setLayout('pjActionAdmin');
		
		if (!is_null($requireLogin) && is_bool($requireLogin))
		{
			$this->requireLogin = $requireLogin;
		}
		
		if ($this->requireLogin)
		{
			if (!$this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin', 'pjActionTeacherLogin', 'pjActionStudentLogin', 'pjActionForgot', 'pjActionTeacherForgot', 'pjActionStudentForgot', 'pjActionPreview')))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
			}
		}
	}
	
	public function afterFilter()
	{
		parent::afterFilter();
		if ($this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin')))
		{
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		}
	}
	
	public function beforeRender()
	{
		
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$pjBookingModel = pjBookingModel::factory();
			$pjTeacherModel = pjTeacherModel::factory();
			$pjStudentModel = pjStudentModel::factory();
			$pjCourseModel = pjCourseModel::factory();
			$pjScheduleModel = pjScheduleModel::factory();
			$pjClassModel = pjClassModel::factory();
			
			$cnt_active_classes = $pjClassModel->where("(t1.start_date <= CURDATE() AND CURDATE() <= t1.end_date)")->findCount()->getData();
			$cnt_bookings_received = $pjBookingModel->where('t1.status <>', 'cancelled')->findCount()->getData();
			$cnt_active_teachers = $pjTeacherModel->where('t1.status', 'T')->findCount()->getData();
			
			$upcoming_classes = $pjScheduleModel
				->reset()
				->select("t1.*, t3.content AS class_name, t4.name AS teacher_name, t5.content AS venue")
				->join("pjClass", 't1.class_id=t2.id', 'left')
				->join('pjMultiLang', "t3.foreign_id = t2.course_id AND t3.model = 'pjCourse' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'title'", 'left')
				->join("pjTeacher", "t1.teacher_id=t4.id", "left")
				->join('pjMultiLang', "t5.foreign_id = t1.id AND t5.model = 'pjSchedule' AND t5.locale = '".$this->getLocaleId()."' AND t5.field = 'venue'", 'left')
				->where("t1.start_ts > NOW()")
				->orderBy("t1.start_ts ASC")
				->limit(3)
				->findAll()
				->getData();
			
			$latest_bookings = $pjBookingModel
				->reset()
				->select("t1.*, t2.start_date, t2.end_date, t3.content as class_name, t4.name as student_name")
				->join("pjClass", 't1.class_id=t2.id', 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.course_id AND t3.model = 'pjCourse' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'title'", 'left')
				->join("pjStudent", "t1.student_id=t4.id", "left")
				->orderBy("t1.created DESC")
				->limit(3)
				->findAll()
				->getData();
			
			$this->set('cnt_active_classes', $cnt_active_classes);
			$this->set('cnt_bookings_received', $cnt_bookings_received);
			$this->set('cnt_active_teachers', $cnt_active_teachers);
			
			$this->set('upcoming_classes', $upcoming_classes);
			$this->set('latest_bookings', $latest_bookings);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionForgot()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['forgot_user']))
		{
			if (!isset($_POST['forgot_email']) || !pjValidation::pjActionNotEmpty($_POST['forgot_email']) || !pjValidation::pjActionEmail($_POST['forgot_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			}
			$pjUserModel = pjUserModel::factory();
			$user = $pjUserModel
				->where('t1.email', $_POST['forgot_email'])
				->limit(1)
				->findAll()
				->getData();
				
			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			} else {
				$user = $user[0];
				$from_email = $this->getAdminEmail();
				$Email = new pjEmail();
				$Email
					->setTo($user['email'])
					->setFrom($from_email)
					->setSubject(__('emailForgotSubject', true));
				
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
						->setSender($this->option_arr['o_smtp_user'])
					;
				}
				
				$body = str_replace(
					array('{Name}', '{Password}'),
					array($user['name'], $user['password']),
					__('emailForgotBody', true)
				);

				if ($Email->send($body))
				{
					$err = "AA11";
				} else {
					$err = "AA12";
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=$err");
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	public function pjActionTeacherForgot()
	{
		$this->setLayout('pjActionAdminLogin');
	
		if (isset($_POST['forgot_teacher']))
		{
			if (!isset($_POST['forgot_email']) || !pjValidation::pjActionNotEmpty($_POST['forgot_email']) || !pjValidation::pjActionEmail($_POST['forgot_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionTeacherForgot&err=AA10");
			}
			$pjTeacherModel = pjTeacherModel::factory();
			$teacher = $pjTeacherModel
				->where('t1.email', $_POST['forgot_email'])
				->limit(1)
				->findAll()
				->getData();
	
			if (count($teacher) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionTeacherForgot&err=AA10");
			} else {
				$teacher = $teacher[0];
	
				$from_email = $this->getAdminEmail();
				$Email = new pjEmail();
				$Email
					->setTo($teacher['email'])
					->setFrom($from_email)
					->setSubject(__('emailForgotSubject', true));
	
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
					->setTransport('smtp')
					->setSmtpHost($this->option_arr['o_smtp_host'])
					->setSmtpPort($this->option_arr['o_smtp_port'])
					->setSmtpUser($this->option_arr['o_smtp_user'])
					->setSmtpPass($this->option_arr['o_smtp_pass'])
					->setSender($this->option_arr['o_smtp_user'])
					;
				}
	
				$body = str_replace(
						array('{Name}', '{Password}'),
						array($teacher['name'], $teacher['password']),
						__('emailForgotBody', true)
				);
				if ($Email->send($body))
				{
					$err = "AA11";
				} else {
					$err = "AA12";
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionTeacherForgot&err=$err");
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	public function pjActionStudentForgot()
	{
		$this->setLayout('pjActionAdminLogin');
	
		if (isset($_POST['forgot_student']))
		{
			if (!isset($_POST['forgot_email']) || !pjValidation::pjActionNotEmpty($_POST['forgot_email']) || !pjValidation::pjActionEmail($_POST['forgot_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionStudentForgot&err=AA10");
			}
			$pjStudentModel = pjStudentModel::factory();
			$student = $pjStudentModel
				->where('t1.email', $_POST['forgot_email'])
				->limit(1)
				->findAll()
				->getData();
	
			if (count($student) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionStudentForgot&err=AA10");
			} else {
				$student = $student[0];
	
				$from_email = $this->getAdminEmail();
				$Email = new pjEmail();
				$Email
					->setTo($student['email'])
					->setFrom($from_email)
					->setSubject(__('emailForgotSubject', true));
	
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
					->setTransport('smtp')
					->setSmtpHost($this->option_arr['o_smtp_host'])
					->setSmtpPort($this->option_arr['o_smtp_port'])
					->setSmtpUser($this->option_arr['o_smtp_user'])
					->setSmtpPass($this->option_arr['o_smtp_pass'])
					->setSender($this->option_arr['o_smtp_user'])
					;
				}
	
				$body = str_replace(
						array('{Name}', '{Password}'),
						array($student['name'], $student['password']),
						__('emailForgotBody', true)
				);
				if ($Email->send($body))
				{
					$err = "AA11";
				} else {
					$err = "AA12";
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionStudentForgot&err=$err");
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	public function pjActionMessages()
	{
		$this->setAjax(true);
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLogin()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['login_user']))
		{
			if (!isset($_POST['login_email']) || !isset($_POST['login_password']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_email']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_password']) ||
				!pjValidation::pjActionEmail($_POST['login_email']))
			{				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=4");
			}
			$pjUserModel = pjUserModel::factory();

			$user = $pjUserModel
				->where('t1.email', $_POST['login_email'])
				->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", pjObject::escapeString($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();

			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=1");
			} else {
				$user = $user[0];
				unset($user['password']);
															
				if (!in_array($user['role_id'], array(1,2,3)))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['role_id'] == 3 && $user['is_active'] == 'F')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['status'] != 'T')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=3");
				}
				
				# Login succeed
				$last_login = date("Y-m-d H:i:s");
				if($user['last_login'] == $user['created'])
				{
					$user['last_login'] = date("Y-m-d H:i:s");
				}
    			$_SESSION[$this->defaultUser] = $user;
    			
    			$data = array();
    			$data['last_login'] = $last_login;
    			$pjUserModel->reset()->setAttributes(array('id' => $user['id']))->modify($data);

    			if ($this->isAdmin() || $this->isEditor())
    			{
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	public function pjActionTeacherLogin()
	{
		$this->setLayout('pjActionAdminLogin');
	
		if (isset($_POST['login_teacher']))
		{
			if (!isset($_POST['login_email']) || !isset($_POST['login_password']) ||
					!pjValidation::pjActionNotEmpty($_POST['login_email']) ||
					!pjValidation::pjActionNotEmpty($_POST['login_password']) ||
					!pjValidation::pjActionEmail($_POST['login_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionTeacherLogin&err=4");
			}
			$pjTeacherModel = pjTeacherModel::factory();
	
			$teacher = $pjTeacherModel
				->where('t1.email', $_POST['login_email'])
				->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", $pjTeacherModel->escapeStr($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();
	
			if (count($teacher) != 1)
			{
				# Login failed
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionTeacherLogin&err=1");
			} else {
				$teacher = $teacher[0];
				$teacher['is_teacher'] = true;
				unset($teacher['password']);
	
				if ($teacher['status'] != 'T')
				{
					# Login forbidden
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionTeacherLogin&err=3");
				}
					
				# Login succeed
				$last_login = date("Y-m-d H:i:s");
				$_SESSION[$this->defaultUser] = $teacher;
					
				# Update
				$data = array();
				$data['last_login'] = $last_login;				
				$pjTeacherModel->reset()->setAttributes(array('id' => $teacher['id']))->modify($data);
	
				if ($this->isTeacher())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminSchedule&action=pjActionIndex");
				}
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	public function pjActionStudentLogin()
	{
		$this->setLayout('pjActionAdminLogin');
	
		if (isset($_POST['login_student']))
		{
			if (!isset($_POST['login_email']) || !isset($_POST['login_password']) ||
					!pjValidation::pjActionNotEmpty($_POST['login_email']) ||
					!pjValidation::pjActionNotEmpty($_POST['login_password']) ||
					!pjValidation::pjActionEmail($_POST['login_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionStudentLogin&err=4");
			}
			$pjStudentModel = pjStudentModel::factory();
	
			$student = $pjStudentModel
				->where('t1.email', $_POST['login_email'])
				->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", $pjStudentModel->escapeStr($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();
	
			if (count($student) != 1)
			{
				# Login failed
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionStudentLogin&err=1");
			} else {
				$student = $student[0];
				$student['is_student'] = true;
				unset($student['password']);
	
				if ($student['status'] != 'T')
				{
					# Login forbidden
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionStudentLogin&err=3");
				}
			
				# Login succeed
				$last_login = date("Y-m-d H:i:s");
				$_SESSION[$this->defaultUser] = $student;
						
				# Update
				$data = array();
				$data['last_login'] = $last_login;
				$pjStudentModel->reset()->setAttributes(array('id' => $student['id']))->modify($data);
	
				if ($this->isStudent())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminSchedule&action=pjActionIndex");
				}
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	public function pjActionLogout()
	{
       	$is_teacher = false;
       	$is_student = false;
       	if ($this->isLoged())
       	{
       		$is_teacher = $this->isTeacher();
       		$is_student = $this->isStudent();
       		unset($_SESSION[$this->defaultUser]);
       	}
       	if($is_teacher == true)
       	{
       		pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionTeacherLogin");
       	}elseif($is_student == true){
       		pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionStudentLogin");
       	}else{
       		pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
       	}
	}
	
	public function pjActionProfile()
	{
		$this->checkLogin();
		
		if ($this->isTeacher())
		{
			if (isset($_POST['profile_update']))
			{
				$pjTeacherModel = pjTeacherModel::factory();
				$arr = $pjTeacherModel->find($this->getUserId())->getData();
				$data = array();
				$data['status'] = $arr['status'];
				if (isset($_FILES['image']))
				{
					if($_FILES['image']['error'] == 0)
					{
						if(getimagesize($_FILES['image']["tmp_name"]) != false)
						{
							if(!empty($arr['image']))
							{
								@unlink(PJ_INSTALL_PATH . $arr['image']);
							}
							$Image = new pjImage();
							if ($Image->getErrorCode() !== 200)
							{
								$Image->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
								if ($Image->load($_FILES['image']))
								{
									$resp = $Image->isConvertPossible();
									if ($resp['status'] === true)
									{
										$hash = md5(uniqid(rand(), true));
										$image = PJ_UPLOAD_PATH . 'teachers/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
				
										$Image->loadImage($_FILES['image']["tmp_name"]);
										$Image->resizeSmart($this->imageSizes[0], $this->imageSizes[1]);
										$Image->saveImage($image);
											
										$data['image'] = $image;
									}
								}
							}
						}else{
							$err = 'AT10';
						}
					}else if($_FILES['image']['error'] != 4){
						$err = 'AT10';
					}
				}
				$post = array_merge($_POST, $data);
				if (!$pjTeacherModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA14");
				}
				$pjTeacherModel->set('id', $this->getUserId())->modify($post);
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $arr['id'], 'pjTeacher', 'data');
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA13");
			} else {
				$arr = pjTeacherModel::factory()->find($this->getUserId())->getData();
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjTeacher');
				$this->set('arr', $arr);
								
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
				
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminTeachers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionStudentProfile()
	{
		$this->checkLogin();
	
		if ($this->isStudent())
		{
			if (isset($_POST['profile_update']))
			{
				$pjStudentModel = pjStudentModel::factory();
				$arr = $pjStudentModel->find($this->getUserId())->getData();
				$data = array();
				$data['status'] = $arr['status'];
				
				$post = array_merge($_POST, $data);
				if (!$pjStudentModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionStudentProfile&err=AA14");
				}
				$pjStudentModel->set('id', $this->getUserId())->modify($post);
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $arr['id'], 'pjStudent', 'data');
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionStudentProfile&err=AA13");
			} else {
				$arr = pjStudentModel::factory()->find($this->getUserId())->getData();
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjStudent');
				$this->set('arr', $arr);
	
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
				$this->appendJs('pjAdminStudents.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>