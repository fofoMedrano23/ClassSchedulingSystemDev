<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminTeachers extends pjAdmin
{
	private $imageSizes = array(144, 155);
	
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
			$pjTeacherModel = pjTeacherModel::factory()->where('t1.email', $_GET['email']);
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjTeacherModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjTeacherModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminTeachers&action=pjActionIndex&err=AT05");
			}
			if (isset($_POST['teacher_create']))
			{
				$id = pjTeacherModel::factory($_POST)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjTeacher', 'data');
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
												
											$image = PJ_UPLOAD_PATH . 'teachers/' . $id . '_' . $hash . '.' . $Image->getExtension();
												
											
											$Image->loadImage($_FILES['image']["tmp_name"]);
											$Image->resizeSmart($this->imageSizes[0], $this->imageSizes[1]);
											$Image->saveImage($image);
												
											$data = array();
											$data['image'] = $image;
											pjTeacherModel::factory()->where('id', $id)->limit(1)->modifyAll($data);
											
										}
									}
								}
								pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTeachers&action=pjActionIndex&id=$id&err=AT03");
							}else{
								pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTeachers&action=pjActionUpdate&id=$id&err=AT11");
							}
						}else if($_FILES['image']['error'] != 4){
							pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTeachers&action=pjActionUpdate&id=$id&err=AT09");
						}
					}
					$err = 'AT03';
				} else {
					$err = 'AT04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTeachers&action=pjActionIndex&err=$err");
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
	
	public function pjActionDeleteTeacher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjTeacherModel = pjTeacherModel::factory();
			$arr = $pjTeacherModel->find($_GET['id'])->getData();
			if ($pjTeacherModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				if (file_exists(PJ_INSTALL_PATH . $arr['image'])) 
				{
					@unlink(PJ_INSTALL_PATH . $arr['image']);
				}
				pjMultiLangModel::factory()->where('model', 'pjTeacher')->where('foreign_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteTeacherBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$arr = $pjTeacherModel->whereIn('id', $_POST['record'])->findAll()->getData();
				foreach($arr as $k => $v)
				{
					if (file_exists(PJ_INSTALL_PATH . $v['image']))
					{
						@unlink(PJ_INSTALL_PATH . $v['image']);
					}
				}
				$pjTeacherModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjTeacher')->whereIn('foreign_id', $_POST['record'])->eraseAll();
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
				->setName("Teachers-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetTeacher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjTeacherModel = pjTeacherModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjTeacherModel->where('t1.email LIKE', "%$q%");
				$pjTeacherModel->orWhere('t1.name LIKE', "%$q%");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjTeacherModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjTeacherModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjTeacherModel
				->select("t1.*, (SELECT COUNT(TS.id) FROM `".pjScheduleModel::factory()->getTable()."` AS `TS` WHERE `TS`.teacher_id=t1.id) AS classes")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
				
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminTeachers.js');
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
			$post_max_size = pjUtil::getPostMaxSize();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $post_max_size)
			{
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminTeachers&action=pjActionIndex&err=AT06");
			}
			if (isset($_POST['teacher_update']))
			{
				$arr = pjTeacherModel::factory()->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTeachers&action=pjActionIndex&err=AT08");
				}
				$data = array();
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
							$err = 'AT12';
						}
					}else if($_FILES['image']['error'] != 4){
						$err = 'AT10';
					}
				}
				
				pjTeacherModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjTeacher', 'data');
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminTeachers&action=pjActionIndex&err=AT01");
				
			} else {
				$arr = pjTeacherModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminTeachers&action=pjActionIndex&err=AT08");
				}
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
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
	
			$pjTeacherModel = pjTeacherModel::factory();
			$arr = $pjTeacherModel->find($_GET['id'])->getData();
	
			if(!empty($arr))
			{
				if(!empty($arr['image']))
				{
					@unlink(PJ_INSTALL_PATH . $arr['image']);
				}
				$data = array();
				$data['image'] = ':NULL';
				$pjTeacherModel->reset()->where(array('id' => $_GET['id']))->limit(1)->modifyAll($data);
	
				$response['code'] = 200;
			}else{
				$response['code'] = 100;
			}
	
			pjAppController::jsonResponse($response);
		}
	}
	
}
?>