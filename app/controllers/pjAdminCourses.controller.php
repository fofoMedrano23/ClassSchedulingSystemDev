<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminCourses extends pjAdmin
{
	private $imageSizes = array(350, 230);
	
	public function pjActionCheckCourse()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && isset($_POST['locale']))
		{
			$locale = $_POST['locale'];
				
			$value = pjObject::escapeString($_POST['i18n'][$locale]['name']);
				
			$pjCourseModel = pjCourseModel::factory();
				
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$pjCourseModel->where('t1.id !=', $_POST['id']);
			}
			$pjCourseModel->where("t1.id IN(SELECT TL.foreign_id FROM `".pjMultiLangModel::factory()->getTable()."` AS TL WHERE TL.model='pjCourse' AND TL.field='name' AND TL.content = '".$value."' AND TL.locale='$locale')");
			echo $pjCourseModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCourses&action=pjActionIndex&err=ACR05");
			}
			if (isset($_POST['course_create']))
			{
				$pjCourseModel = pjCourseModel::factory();
				$data = array();
				
				$id = $pjCourseModel->setAttributes($_POST)->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					$err = 'ACR03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjCourse', 'data');
					}
					$pjClassModel = pjClassModel::factory();
						
					foreach ($_POST['start_date'] as $index => $start_date)
					{
						if (!empty($_POST['start_date'][$index]) && !empty($_POST['end_date'][$index]))
						{
							$cdata = array();
							$cdata['course_id'] = $id;
							$cdata['start_date'] = pjUtil::formatDate($_POST['start_date'][$index], $this->option_arr['o_date_format']);
							$cdata['end_date'] = pjUtil::formatDate($_POST['end_date'][$index], $this->option_arr['o_date_format']);
								
							$pjClassModel->reset()->setAttributes($cdata)->insert();
						}
					}
					if (isset($_FILES['image']))
					{
						if($_FILES['image']['error'] == 0)
						{
							if(getimagesize($_FILES['image']["tmp_name"]) != false)
							{
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
											
											$source_path = PJ_UPLOAD_PATH . 'classes/source/' . $id . '_' . $hash . '.' . $Image->getExtension();
											$thumb_path = PJ_UPLOAD_PATH . 'classes/thumb/' . $id . '_' . $hash . '.' . $Image->getExtension();
											
											if ($Image->save($source_path))
											{
												$Image->loadImage($source_path);
												$Image->resizeSmart($this->imageSizes[0], $this->imageSizes[1]);
												$Image->saveImage($thumb_path);
											
												$data = array();
												$data['source_path'] = $source_path;
												$data['thumb_path'] = $thumb_path;
												$pjCourseModel->reset()->where('id', $id)->limit(1)->modifyAll($data);
											}
										}
									}
								}
								pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCourses&action=pjActionUpdate&id=$id&err=ACR03");
							}else{
								pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCourses&action=pjActionUpdate&id=$id&err=ACR11");
							}
						}else if($_FILES['image']['error'] != 4){
							pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCourses&action=pjActionUpdate&id=$id&err=ACR09");
						}
					}
					
				} else {
					$err = 'ACR04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCourses&action=pjActionIndex&err=$err");
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
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCourses.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeleteCourse()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjCourseModel = pjCourseModel::factory();
			$pjMultiLangModel = pjMultiLangModel::factory();
			$arr = $pjCourseModel->find($_GET['id'])->getData();
				
			if ($pjCourseModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				if (file_exists(PJ_INSTALL_PATH . $arr['source_path'])) {
					@unlink(PJ_INSTALL_PATH . $arr['source_path']);
				}
				if (file_exists(PJ_INSTALL_PATH . $arr['thumb_path'])) {
					@unlink(PJ_INSTALL_PATH . $arr['thumb_path']);
				}
				$pjMultiLangModel->where('model', 'pjCourse')->where('foreign_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteCourseBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjCourseModel = pjCourseModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				
				$arr = $pjCourseModel->whereIn('id', $_POST['record'])->findAll()->getData();
				foreach($arr as $v)
				{
					if (file_exists(PJ_INSTALL_PATH . $v['source_path'])) {
						@unlink(PJ_INSTALL_PATH . $v['source_path']);
					}
					if (file_exists(PJ_INSTALL_PATH . $v['thumb_path'])) {
						@unlink(PJ_INSTALL_PATH . $v['thumb_path']);
					}
				}
				$pjCourseModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				$pjMultiLangModel->reset()->where('model', 'pjCourse')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetCourse()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCourseModel = pjCourseModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCourse' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'duration'", 'left')
				->join('pjMultiLang', "t4.foreign_id = t1.id AND t4.model = 'pjCourse' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'description'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjCourseModel->where('t2.content LIKE', "%$q%");
				$pjCourseModel->orWhere('t3.content LIKE', "%$q%");
				$pjCourseModel->orWhere('t4.content LIKE', "%$q%");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjCourseModel->where('t1.status', $_GET['status']);
			}

			$column = 'title';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			$total = count($pjCourseModel->findAll()->getData());
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjCourseModel
				->select("t1.*, t2.content as title, t3.content as duration")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			
			$class_arr = array();
			$course_id_arr = $pjCourseModel->findAll()->getDataPair(null, 'id');
			if(!empty($course_id_arr))
			{
				$temp_class_arr = pjClassModel::factory()->whereIn('course_id', $course_id_arr)->orderBy("start_date ASC")->findAll()->getData();
				foreach($temp_class_arr as $k => $v)
				{
					$class_arr[$v['course_id']][] = '<a href="index.php?controller=pjAdminSchedule&action=pjActionEdit&id='.$v['id'].'">' . date($this->option_arr['o_date_format'], strtotime($v['start_date'])) . ' - ' . date($this->option_arr['o_date_format'], strtotime($v['end_date'])) . '</a>';
				}
			}
			foreach($data as $k => $v)
			{
				$v['price'] = pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']);
				$v['periods'] = isset($class_arr[$v['id']]) && !empty($class_arr[$v['id']]) ? join("<br/>", $class_arr[$v['id']]) : '';
				$data[$k] = $v;
			}	
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminCourses.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveCourse()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCourseModel = pjCourseModel::factory();
			if (!in_array($_POST['column'], $pjCourseModel->getI18n()))
			{
				$pjCourseModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjCourse', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		$post_max_size = pjUtil::getPostMaxSize();
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
		{
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCourses&action=pjActionIndex&err=ACR06");
		}
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['course_update']))
			{
				$pjCourseModel = pjCourseModel::factory();
				
				$err = 'ACR01';
				
				$arr = $pjCourseModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCourses&action=pjActionIndex&err=ACR08");
				}
				
				$data = array();
				if (isset($_FILES['image']))
				{
					if($_FILES['image']['error'] == 0)
					{
						if(getimagesize($_FILES['image']["tmp_name"]) != false)
						{
							if(!empty($arr['source_path']))
							{
								@unlink(PJ_INSTALL_PATH . $arr['source_path']);
							}
							if(!empty($arr['thumb_path']))
							{
								@unlink(PJ_INSTALL_PATH . $arr['thumb_path']);
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
										$source_path = PJ_UPLOAD_PATH . 'classes/source/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										$thumb_path = PJ_UPLOAD_PATH . 'classes/thumb/' . $_POST['id'] . '_' . $hash . '.' . $Image->getExtension();
										
										if ($Image->save($source_path))
										{
											$Image->loadImage($source_path);
											$Image->resizeSmart($this->imageSizes[0], $this->imageSizes[1]);
											$Image->saveImage($thumb_path);
										
											$data['source_path'] = $source_path;
											$data['thumb_path'] = $thumb_path;
										}
									}
								}
							}
						}else{
							$err = 'ACR12';
						}
					}else if($_FILES['image']['error'] != 4){
						$err = 'ACR10';
					}
				}
				
				$pjCourseModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjCourse', 'data');
				}
				
				$pjClassModel = pjClassModel::factory();
				$class_arr = $pjClassModel->where('course_id', $_POST['id'])->findAll()->getData();
				$remove_id_arr = array();
				foreach($class_arr as $class)
				{
					if(!isset($_POST['class_id'][$class['id']]))
					{
						$remove_id_arr[] = $class['id'];
					}
				}
				
				foreach ($_POST['start_date'] as $index => $start_date)
				{
					if (!empty($_POST['start_date'][$index]) && !empty($_POST['end_date'][$index]))
					{
						$cdata = array();
						$cdata['course_id'] = $_POST['id'];
						$cdata['start_date'] = pjUtil::formatDate($_POST['start_date'][$index], $this->option_arr['o_date_format']);
						$cdata['end_date'] = pjUtil::formatDate($_POST['end_date'][$index], $this->option_arr['o_date_format']);
				
						if (isset($_POST['class_id'][$index]))
						{
							$pjClassModel->reset()->where('id', $_POST['class_id'][$index])->limit(1)->modifyAll($cdata);
						}else{
							$pjClassModel->reset()->setAttributes($cdata)->insert();
						}
					}
				}
				if(!empty($remove_id_arr))
				{
					$pjClassModel->reset()->whereIn('id', $remove_id_arr)->eraseAll();
					pjScheduleModel::factory()->whereIn('class_id', $remove_id_arr)->eraseAll();
					$pjBookingModel = pjBookingModel::factory();
					$booking_id_arr = $pjBookingModel->whereIn('class_id', $remove_id_arr)->findAll()->getDataPair(null, 'id');
					if(!empty($booking_id_arr))
					{
						pjBookingPaymentModel::factory()->whereIn('booking_id', $booking_id_arr)->eraseAll();
						pjStudentPaymentModel::factory()->whereIn('class_id', $remove_id_arr)->eraseAll();
						$pjBookingModel->reset()->whereIn('class_id', $remove_id_arr)->eraseAll();
					}
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCourses&action=pjActionUpdate&id=".$_POST['id']."&err=" . $err);
				
			} else {
				$arr = pjCourseModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCourses&action=pjActionIndex&err=ACR08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjCourse');
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
				
				$class_arr = pjClassModel::factory()->select("t1.*, (SELECT COUNT(TB.id) FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE TB.class_id=t1.id) AS cnt_students")->where('course_id', $_GET['id'])->findAll()->getData();
				$this->set('class_arr', $class_arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCourses.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
				
			$pjCourseModel = pjCourseModel::factory();
			$arr = $pjCourseModel->find($_GET['id'])->getData();
				
			if(!empty($arr))
			{
				if(!empty($arr['source_path']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['source_path']);
				}
				if(!empty($arr['thumb_path']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['thumb_path']);
				}
				$data = array();
				$data['source_path'] = ':NULL';
				$data['thumb_path'] = ':NULL';
				$pjCourseModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
	
				$response['code'] = 200;
			}else{
				$response['code'] = 100;
			}
				
			pjAppController::jsonResponse($response);
		}
	}
	
	public function pjActionPeriods()
	{
		$this->checkLogin();
	
		if ($this->isAdmin() || $this->isEditor())
		{
			if(isset($_GET['course_id']) && (int) $_GET['course_id'] > 0)
			{
				$pjCourseModel = pjCourseModel::factory();
				$arr = $pjCourseModel->find($_GET['course_id'])->getData();
				if(!empty($arr))
				{
					if(isset($_POST['period_update']))
					{
						$pjClassModel = pjClassModel::factory();
						$class_arr = $pjClassModel->where('course_id', $_GET['course_id'])->findAll()->getData();
						$remove_id_arr = array();
						foreach($class_arr as $class)
						{
							if(!isset($_POST['id'][$class['id']]))
							{
								$remove_id_arr[] = $class['id'];
							}
						}
						
						foreach ($_POST['start_date'] as $index => $start_date)
						{
							if (!empty($_POST['start_date'][$index]) && !empty($_POST['end_date'][$index]))
							{
								$data = array();
								$data['course_id'] = $_GET['course_id'];
								$data['start_date'] = pjUtil::formatDate($_POST['start_date'][$index], $this->option_arr['o_date_format']);
								$data['end_date'] = pjUtil::formatDate($_POST['end_date'][$index], $this->option_arr['o_date_format']);
								
								if (isset($_POST['id'][$index]))
								{
									$pjClassModel->reset()->where('id', $_POST['id'][$index])->limit(1)->modifyAll($data);
								}else{
									$pjClassModel->reset()->setAttributes($data)->insert();
								}
							}
						}
						if(!empty($remove_id_arr))
						{
							$pjClassModel->reset()->whereIn('id', $remove_id_arr)->eraseAll();
						}
						
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCourses&action=pjActionPeriods&course_id=".$_GET['course_id']."&err=AP01");
					}else{
						$this->set('arr', $arr);
						
						$class_arr = pjClassModel::factory()->where('course_id', $_GET['course_id'])->findAll()->getData();
						$this->set('class_arr', $class_arr);
						
						$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
						$this->appendJs('pjAdminCourses.js');
					}
				}else{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCourses&action=pjActionIndex&err=ACR08");
				}
			}else{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCourses&action=pjActionIndex&err=ACR08");
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	
	public function pjActionGetStudents()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory()->join('pjStudent', "t2.id = t1.student_id", 'left');

			if(isset($_GET['course_id']) && (int)$_GET['course_id'] > 0 )
			{
				$pjBookingModel->where('t1.course_id', $_GET['course_id']);
			}
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjBookingModel->where('t2.name LIKE', "%$q%");
				$pjBookingModel->orWhere('t2.email LIKE', "%$q%");
			}
			
			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			$total = count($pjBookingModel->findAll()->getData());
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
				
			$data = $pjBookingModel
			->select("t2.*")
			->orderBy("$column $direction")
			->limit($rowCount, $offset)
			->findAll()
			->getData();
	
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	public function pjActionStudents()
	{
		$this->checkLogin();
	
		if ($this->isAdmin() || $this->isEditor())
		{
			if(isset($_GET['course_id']) && (int) $_GET['course_id'] > 0)
			{
				$pjCourseModel = pjCourseModel::factory();
				$arr = $pjCourseModel->find($_GET['course_id'])->getData();
				if(!empty($arr))
				{
					$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
					$this->appendJs('pjAdminCourses.js');
				}else{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCourses&action=pjActionIndex&err=ACR08");
				}
			}else{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCourses&action=pjActionIndex&err=ACR08");
			}
			
		} else {
			$this->set('status', 2);
		}
	}
}
?>