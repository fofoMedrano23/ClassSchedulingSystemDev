<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminStudents extends pjAdmin
{
	public function pjActionCheckEmail()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_GET['email']) || empty($_GET['email']))
			{
				echo 'false';
				exit;
			}
			$pjStudentModel = pjStudentModel::factory()->where('t1.email', $_GET['email']);
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjStudentModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjStudentModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['student_create']))
			{
				$data = array();
				$data['created'] = date('Y-m-d H:i:s');
				$id = pjStudentModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AS03';
				} else {
					$err = 'AS04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminStudents&action=pjActionIndex&err=$err");
			} else {
				
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
	
	public function pjActionDeleteStudent()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjStudentModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
				{
					$pjBookingModel = pjBookingModel::factory();
					pjStudentPaymentModel::factory()->where('student_id', $_GET['id'])->eraseAll();
				$booking_id_arr = $pjBookingModel->where('student_id', $_GET['id'])->findAll()->getDataPair(null, 'id');
				if(!empty($booking_id_arr))
				{
					pjBookingPaymentModel::factory()->whereIn('booking_id', $booking_id_arr)->eraseAll();
				}
				$pjBookingModel->reset()->where('student_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteStudentBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjStudentModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				$pjBookingModel = pjBookingModel::factory();
				$booking_id_arr = $pjBookingModel->whereIn('student_id', $_POST['record'])->findAll()->getDataPair(null, 'id');
				if(!empty($booking_id_arr))
				{
					pjBookingPaymentModel::factory()->whereIn('booking_id', $booking_id_arr)->eraseAll();
				}
				$pjBookingModel->reset()->whereIn('student_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportStudent()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjStudentModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Students-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetStudent()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjStudentModel = pjStudentModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjStudentModel->where('t1.email LIKE', "%$q%");
				$pjStudentModel->orWhere('t1.name LIKE', "%$q%");
			}
			if($this->isTeacher())
			{
				$class_id_arr = pjScheduleModel::factory()->where('teacher_id', $this->getUserId())->findAll()->getDataPair(null, 'class_id');
				if(!empty($class_id_arr))
				{
					$pjStudentModel->where("(t1.id IN (SELECT TB.student_id FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE `TB`.class_id IN(".join(",", $class_id_arr).") ) )");
				}
			}
			if(isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
			{
				$pjStudentModel->where("(t1.id IN (SELECT TB.student_id FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE `TB`.class_id='".$_GET['class_id']."') )");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjStudentModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['name']) && !empty($_GET['name']))
			{
				$q = pjObject::escapeString($_GET['name']);
				$pjStudentModel->where('t1.name LIKE', "%$q%");
			}
			if (isset($_GET['email']) && !empty($_GET['email']))
			{
				$q = pjObject::escapeString($_GET['email']);
				$pjStudentModel->where('t1.email LIKE', "%$q%");
			}
			if (isset($_GET['phone']) && !empty($_GET['phone'])){
				$q = pjObject::escapeString($_GET['phone']);
				$pjStudentModel->where('t1.phone LIKE', "%$q%");
			}
			if (isset($_GET['from_date']) && !empty($_GET['from_date']) && isset($_GET['to_date']) && !empty($_GET['to_date']))
			{
				$from_date = pjUtil::formatDate($_GET['from_date'], $this->option_arr['o_date_format']);
				$to_date = pjUtil::formatDate($_GET['to_date'], $this->option_arr['o_date_format']);
				$pjStudentModel->where("(t1.created BETWEEN '$from_date' AND '$to_date')");
			}elseif(isset($_GET['from_date']) && !empty($_GET['from_date']) && isset($_GET['to_date']) && empty($_GET['to_date'])){
				$from_date = pjUtil::formatDate($_GET['from_date'], $this->option_arr['o_date_format']);
				$pjStudentModel->where("(`created` >= '$from_date')");
			}elseif(isset($_GET['from_date']) && empty($_GET['from_date']) && isset($_GET['to_date']) && !empty($_GET['to_date'])){
				$to_date = pjUtil::formatDate($_GET['to_date'], $this->option_arr['o_date_format']);
				$pjStudentModel->where("(`created` <= '$to_date')");
			}

			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjStudentModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjStudentModel
				->select("*, (SELECT SUM(`TSP`.amount) FROM `".pjStudentPaymentModel::factory()->getTable()."` AS `TSP` WHERE `TSP`.student_id=t1.id) AS amount")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			foreach($data as $k => $v)
			{
				$v['amount'] = !empty($v['amount']) ? pjUtil::formatCurrencySign($v['amount'], $this->option_arr['o_currency']) : "";
				$v['name'] = pjSanitize::html($v['name']);
				$v['email'] = pjSanitize::html($v['email']);
				$v['phone'] = pjSanitize::html($v['phone']);
				$data[$k] = $v;
			}	
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isTeacher())
		{
			$pjClassModel = pjClassModel::factory();
			if($this->isTeacher())
			{
				$pjClassModel->where("(t1.id IN(SELECT `TS`.class_id FROM `".pjScheduleModel::factory()->getTable()."` AS `TS` WHERE `TS`.teacher_id='".$this->getUserId()."'))");
			}
			$class_arr = $pjClassModel
				->select("t1.*, t2.content AS course")
				->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->orderBy("course ASC, start_date ASC")
				->findAll()
				->getData();
			$this->set('class_arr', $class_arr);
			
			$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminStudents.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveStudent()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjStudentModel = pjStudentModel::factory();
			
			$pjStudentModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionStatusStudent()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjStudentModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
					'status' => ":IF(`status`='F','T','F')"
				));
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['student_update']))
			{
				pjStudentModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll($_POST);
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminStudents&action=pjActionIndex&err=AS01");
				
			} else {
				$arr = pjStudentModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminStudents&action=pjActionIndex&err=AS08");
				}
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
}
?>