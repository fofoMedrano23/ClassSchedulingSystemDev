<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminHistory extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			if (isset($_GET['student_id']) && (int) $_GET['student_id'] > 0)
			{
				$pjStudentModel = pjStudentModel::factory();
				$arr = $pjStudentModel->find($_GET['student_id'])->getData();
			
				if(!empty($arr))
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
							$err = 'AP04';
						}
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminHistory&action=pjActionIndex&student_id=".$_POST['student_id']."&err=$err");
					} else {
						$class_arr = pjClassModel::factory()
							->select("t1.*, t2.content AS course")
							->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
							->where("(t1.id IN(SELECT `TB`.class_id FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.student_id='".$_GET['student_id']."'))")
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
						
						$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
						$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
						$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
						$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
						$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
						$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
						$this->appendJs('pjAdminHistory.js');
					}
				}else{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminStudents&action=pjActionIndex&err=AS08");
				}
			}else{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminStudents&action=pjActionIndex&err=AS08");
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeletePayment()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjStudentPaymentModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjPayment')->where('foreign_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeletePaymentBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjStudentPaymentModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjPayment')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportTeacher()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjTeacherModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("History-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetHistory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjStudentPaymentModel = pjStudentPaymentModel::factory()
				->join("pjClass", "t1.class_id=t2.id", 'left')
				->join('pjMultiLang', "t3.foreign_id = t2.course_id AND t3.model = 'pjCourse' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'title'", 'left')
				->join("pjStudent", "t1.student_id=t4.id", 'left');
			
			if (isset($_GET['student_id']) && (int) $_GET['student_id'] > 0)
			{
				$pjStudentPaymentModel->where('t1.student_id', $_GET['student_id']);
			}
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjStudentPaymentModel->where('t3.content LIKE', "%$q%");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('paid', 'refund', 'due')))
			{
				$pjStudentPaymentModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjStudentPaymentModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjStudentPaymentModel
				->select("t1.*, t3.content AS course, t4.name")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			foreach($data as $k => $v)
			{
				$v['amount'] = pjUtil::formatCurrencySign($v['amount'], $this->option_arr['o_currency']);
				$v['created'] = date($this->option_arr['o_date_format'], strtotime($v['created'])) . ', ' . date($this->option_arr['o_time_format'], strtotime($v['created']));
				$data[$k] = $v;
			}	
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isStudent())
		{
			if (isset($_GET['student_id']) && (int) $_GET['student_id'] > 0)
			{
				if($this->isStudent() && $_GET['student_id'] != $this->getUserId())
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminStudents&action=pjActionIndex&err=AS08");
				}
				$arr = pjStudentModel::factory()->find($_GET['student_id'])->getData();
				
				if(!empty($arr))
				{
					$this->set('arr', $arr);
					$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
					$this->appendJs('pjAdminHistory.js');
				}else{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminStudents&action=pjActionIndex&err=AS08");
				}
			}else{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminStudents&action=pjActionIndex&err=AS08");
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveTeacher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjTeacherModel = pjTeacherModel::factory();
			
			$pjTeacherModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionStatusTeacher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjTeacherModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
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
		if (isset($_GET['student_id']) && (int) $_GET['student_id'] > 0)
			{
				$pjStudentModel = pjStudentModel::factory();
				$arr = $pjStudentModel->find($_GET['student_id'])->getData();
			
				if(!empty($arr))
				{
					if (isset($_POST['payment_update']))
					{
						pjStudentPaymentModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll($_POST);
						if (isset($_POST['i18n']))
						{
							pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjPayment', 'data');
						}
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminHistory&action=pjActionIndex&student_id=".$_POST['student_id']."&err=ASP01");
					} else {
						$arr = pjStudentPaymentModel::factory()->find($_GET['id'])->getData();
						if (count($arr) === 0)
						{
							pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminHistory&action=pjActionIndex&student_id".$_GET['student_id']."&err=ASP08");
						}
						$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjPayment');
						$this->set('arr', $arr);
						
						$class_arr = pjClassModel::factory()
							->select("t1.*, t2.content AS course")
							->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
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
						
						$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
						$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
						$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
						$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
						$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
						$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
						$this->appendJs('pjAdminHistory.js');
					}
				}else{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminStudents&action=pjActionIndex&err=AS08");
				}
			}else{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminStudents&action=pjActionIndex&err=AS08");
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>