<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminSchedule extends pjAdmin
{
	public function pjActionCheckCreate()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();

			$response['status'] = 'OK';
			$pjScheduleModel = pjScheduleModel::factory();

			$class_id = $_POST['class_id'];
			
			$teacher_arr = array();
			$class_arr = array();
			foreach($_POST['date'] as $index => $date)
			{
				$start_arr = pjUtil::convertDateTime($date . ' ' . $_POST['start_time'][$index] , $this->option_arr['o_date_format'], $this->option_arr['o_time_format']);
				$end_arr = pjUtil::convertDateTime($date . ' ' . $_POST['end_time'][$index] , $this->option_arr['o_date_format'], $this->option_arr['o_time_format']);
				
				if($start_arr['ts'] >= $end_arr['ts'])
				{
					$response['status'] = 'ERR';
					$response['code'] = 100;
					$response['text'] = __('lblEndTimeError', true);
					$response['index'] = $index;
					pjAppController::jsonResponse($response);
				}
				$teacher_id = $_POST['teacher_id'][$index];
				if(array_key_exists($teacher_id, $teacher_arr))
				{
					foreach($teacher_arr[$teacher_id] as $k => $v)
					{
						if( ($start_arr['ts'] >= $v['start_ts'] && $start_arr['ts'] < $v['end_ts']) || ($end_arr['ts'] <= $v['end_ts'] && $end_arr['ts'] > $v['start_ts']) )
						{
							
							$response['status'] = 'ERR';
							$response['code'] = 101;
							$response['text'] = __('lblTeacherError', true);
							$response['index'] = $index;
							pjAppController::jsonResponse($response);
						}
					}
					$teacher_arr[$teacher_id][] = array('start_ts' => $start_arr['ts'], 'end_ts' => $end_arr['ts']);
				}else{
					$pjScheduleModel->reset();
					
					if(strpos($index, 'cp_') === false)
					{
						$pjScheduleModel->where('t1.id <>', $index);
					}
					
					$pjScheduleModel->where('t1.teacher_id', $teacher_id);
					$pjScheduleModel->where("( (t1.start_ts <= '".$start_arr['iso_date_time']."' AND '".$start_arr['iso_date_time']."' < t1.end_ts) OR (t1.end_ts >= '".$end_arr['iso_date_time']."' AND '".$end_arr['iso_date_time']."' > t1.start_ts) )");
					
					$cnt_teachers = $pjScheduleModel->findCount()->getData();
					if($cnt_teachers > 0)
					{
						$response['status'] = 'ERR';
						$response['code'] = 101;
						$response['text'] = __('lblTeacherError', true);
						$response['index'] = $index;
						pjAppController::jsonResponse($response);
					}else{
						$teacher_arr[$teacher_id][] = array('start_ts' => $start_arr['ts'], 'end_ts' => $end_arr['ts']);
					}
				}
				
				if(array_key_exists($class_id, $class_arr))
				{
					foreach($class_arr[$class_id] as $k => $v)
					{
						if( ($start_arr['ts'] >= $v['start_ts'] && $start_arr['ts'] < $v['end_ts']) || ($end_arr['ts'] <= $v['end_ts'] && $end_arr['ts'] > $v['start_ts']) )
						{
							$response['status'] = 'ERR';
							$response['code'] = 102;
							$response['text'] = __('lblClassError', true);
							$response['index'] = $index;
							pjAppController::jsonResponse($response);
						}
					}
					$class_arr[$class_id][] = array('start_ts' => $start_arr['ts'], 'end_ts' => $end_arr['ts']);
				}else{
					$pjScheduleModel->reset();
					if(strpos($index, 'cp_') === false)
					{
						$pjScheduleModel->where('t1.id <>', $index);
					}	
					$pjScheduleModel->where('t1.class_id', $class_id);
					$pjScheduleModel->where("( (t1.start_ts <= '".$start_arr['iso_date_time']."' AND '".$start_arr['iso_date_time']."' < t1.end_ts) OR (t1.end_ts >= '".$end_arr['iso_date_time']."' AND '".$end_arr['iso_date_time']."' > t1.start_ts) )");
						
					$cnt_classes = $pjScheduleModel->findCount()->getData();
					if($cnt_classes > 0)
					{
						$response['status'] = 'ERR';
						$response['code'] = 102;
						$response['text'] = __('lblTeacherError', true);
						$response['index'] = $index;
						pjAppController::jsonResponse($response);
					}else{
						$class_arr[$class_id][] = array('start_ts' => $start_arr['ts'], 'end_ts' => $end_arr['ts']);
					}
				}
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionCheckSchedule()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$response = array();
			
			$response['status'] = 'OK';
			$pjScheduleModel = pjScheduleModel::factory();
			
			$start_arr = pjUtil::convertDateTime($_POST['date'] . ' ' . $_POST['start_time'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format']);
			$end_arr = pjUtil::convertDateTime($_POST['date'] . ' ' . $_POST['end_time'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format']);
			
			if($end_arr['ts'] <= $start_arr['ts'])
			{
				$response['status'] = 'ERR';
				$response['text'] = __('lblEndTimeInvalid', true);
				pjAppController::jsonResponse($response);
			}
			
			if(isset($_POST['id']))
			{
				$pjScheduleModel->where('t1.id <>', $_POST['id']);
			}
			$pjScheduleModel->where('t1.class_id', $_POST['class_id']);
			$pjScheduleModel->where("( (t1.start_ts <= '".$start_arr['iso_date_time']."' AND '".$start_arr['iso_date_time']."' < t1.end_ts) OR (t1.end_ts >= '".$end_arr['iso_date_time']."' AND '".$end_arr['iso_date_time']."' > t1.start_ts) )");
			
			$cnt_classes = $pjScheduleModel->findCount()->getData();
			if($cnt_classes > 0)
			{
				$response['status'] = 'ERR';
				$response['text'] = __('lblClassError', true);
				pjAppController::jsonResponse($response);
			}
			$pjScheduleModel->reset();
			if(isset($_POST['id']))
			{
				$pjScheduleModel->where('t1.id <>', $_POST['id']);
			}
			$pjScheduleModel->where('t1.teacher_id', $_POST['teacher_id']);
			$pjScheduleModel->where("( (t1.start_ts <= '".$start_arr['iso_date_time']."' AND '".$start_arr['iso_date_time']."' < t1.end_ts) OR (t1.end_ts >= '".$end_arr['iso_date_time']."' AND '".$end_arr['iso_date_time']."' > t1.start_ts) )");
				
			$cnt_teachers = $pjScheduleModel->findCount()->getData();
			if($cnt_teachers > 0)
			{
				$response['status'] = 'ERR';
				$response['text'] = __('lblTeacherError', true);
				pjAppController::jsonResponse($response);
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	public function pjActionCreate()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			if (isset($_POST['schedule_create']))
			{
				$pjScheduleModel = pjScheduleModel::factory();
				$pjMultiLangModel = pjMultiLangModel::factory();
				$schedule_arr = $pjScheduleModel->where('class_id', $_POST['class_id'])->findAll()->getData();
				$remove_id_arr = array();
				foreach($schedule_arr as $schedule)
				{
					if(!isset($_POST['schedule_id'][$schedule['id']]))
					{
						$remove_id_arr[] = $schedule['id'];
					}
				}
				
				$class_id = $_POST['class_id'];
				foreach($_POST['date'] as $index => $date)
				{
					$data = array();
					$data['class_id'] = $class_id;
					$start_arr = pjUtil::convertDateTime($date . ' ' . $_POST['start_time'][$index] , $this->option_arr['o_date_format'], $this->option_arr['o_time_format']);
					$end_arr = pjUtil::convertDateTime($date . ' ' . $_POST['end_time'][$index] , $this->option_arr['o_date_format'], $this->option_arr['o_time_format']);
					$data['start_ts'] = $start_arr['iso_date_time'];
					$data['end_ts'] = $end_arr['iso_date_time'];
					$data['teacher_id'] = $_POST['teacher_id'][$index];

					if (isset($_POST['schedule_id'][$index]))
					{
						$pjScheduleModel->reset()->where('id', $_POST['schedule_id'][$index])->limit(1)->modifyAll($data);
						
						foreach ($_POST['i18n'] as $locale => $locale_arr)
						{
							foreach ($locale_arr as $field => $content)
							{
								if(is_array($content))
								{
									$sql = sprintf("INSERT INTO `%1\$s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
											VALUES (NULL, :foreign_id, :model, :locale, :field, :update_content, :source)
											ON DUPLICATE KEY UPDATE `content` = :update_content, `source` = :source;",
											$pjMultiLangModel->getTable()
									);
									$foreign_id = $_POST['schedule_id'][$index];
									$model = 'pjSchedule';
									$source = 'data';
									$update_content = $content[$index];
									$modelObj = $pjMultiLangModel->reset()->prepare($sql)->exec(compact('foreign_id', 'model', 'locale', 'field', 'update_content', 'source'));
								}
							}
						}
					}else{
						$id = $pjScheduleModel->reset()->setAttributes($data)->insert()->getInsertId();
						if ($id !== false && (int) $id > 0)
						{
							if (isset($_POST['i18n']))
							{
								foreach ($_POST['i18n'] as $locale => $locale_arr)
								{
									foreach ($locale_arr as $field => $content)
									{
										if(is_array($content))
										{
											$insert_id = $pjMultiLangModel->reset()->setAttributes(array(
													'foreign_id' => $id,
													'model' => 'pjSchedule',
													'locale' => $locale,
													'field' => $field,
													'content' => $content[$index],
													'source' => 'data'
											))->insert()->getInsertId();
										}
									}
								}
							}
						}
					}
				}
				
				if(!empty($remove_id_arr))
				{
					$pjScheduleModel->reset()->whereIn('id', $remove_id_arr)->eraseAll();
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminSchedule&action=pjActionCreate&class_id=".$_POST['class_id']."&err=ASC09");
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
				
				$class_arr = pjClassModel::factory()
					->select("t1.*, t2.content AS course")
					->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->orderBy("course ASC, start_date ASC")
					->findAll()
					->getData();
				$this->set('class_arr', $class_arr);
				
				$teacher_arr = pjTeacherModel::factory()->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				$this->set('teacher_arr', $teacher_arr);
				
				$this->appendCss('jquery.ui.timepicker.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery.ui.timepicker.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminSchedule.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteSchedule()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjScheduleModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjSchedule')->where('foreign_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteScheduleBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjScheduleModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjSchedule')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportSchedule()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjScheduleModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Schedule-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetSchedule()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjScheduleModel = pjScheduleModel::factory()
				->join('pjClass', 't1.class_id=t2.id', 'left')
				->join('pjMultiLang', "t3.foreign_id = t2.course_id AND t3.model = 'pjCourse' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'title'", 'left')
				->join('pjTeacher', 't1.teacher_id=t4.id', 'left')
				->join('pjMultiLang', "t5.foreign_id = t1.id AND t5.model = 'pjSchedule' AND t5.locale = '".$this->getLocaleId()."' AND t5.field = 'venue'", 'left');
			$today = date("Y-m-d 00:00:00");
			$pjScheduleModel->where("(t1.start_ts >= '".$today."')");
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjScheduleModel->where('t3.content LIKE', "%$q%");
			}

			if(isset($_GET['course_id']) && (int) $_GET['course_id'] > 0)
			{
				$pjScheduleModel->where("t2.course_id", $_GET['course_id']);
			}
			if(isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
			{
				$pjScheduleModel->where("t1.class_id", $_GET['class_id']);
			}
			if(isset($_GET['teacher_id']) && (int) $_GET['teacher_id'] > 0)
			{
				$pjScheduleModel->where("t1.teacher_id", $_GET['teacher_id']);
			}
			if($this->isTeacher())
			{
				$pjScheduleModel->where("t1.teacher_id", $this->getUserId());
			}
			if($this->isStudent())
			{
				$pjScheduleModel->where("(t1.class_id IN (SELECT `TB`.class_id FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.student_id='".$this->getUserId()."') )");
			}
			
			$column = 'start_ts';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjScheduleModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjScheduleModel
				->select("t1.*, t2.start_date, t3.content AS class, t4.name AS teacher, t5.content as venue")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();

			foreach($data as $k => $v)
			{
				$v['class'] = $v['class'] . ' ('.date($this->option_arr['o_date_format'], strtotime($v['start_date'])).')';
				$v['date'] = date($this->option_arr['o_date_format'], strtotime($v['start_ts'])) ;
				$v['time'] = date($this->option_arr['o_time_format'], strtotime($v['start_ts'])) . ' - ' . date($this->option_arr['o_time_format'], strtotime($v['end_ts'])) ;
				$data[$k] = $v;
			}
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isTeacher() || $this->isStudent())
		{
			$pjClassModel = pjClassModel::factory();
			if($this->isTeacher())
			{
				$pjClassModel->where("(t1.id IN(SELECT `TS`.class_id FROM `".pjScheduleModel::factory()->getTable()."` AS `TS` WHERE `TS`.teacher_id='".$this->getUserId()."'))");
			}
			if($this->isStudent())
			{
				$pjClassModel->where("(t1.id IN(SELECT `TB`.class_id FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.student_id='".$this->getUserId()."'))");
			}
			$class_arr = $pjClassModel
				->select("t1.*, t2.content AS course")
				->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->orderBy("course ASC, start_date ASC")
				->findAll()
				->getData();
			$this->set('class_arr', $class_arr);
			
			$teacher_arr = pjTeacherModel::factory()->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
			$this->set('teacher_arr', $teacher_arr);
			
			$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminSchedule.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveSchedule()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjScheduleModel = pjScheduleModel::factory();
			
			$pjScheduleModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionStatusSchedule()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjScheduleModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
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
			if (isset($_POST['schedule_update']))
			{
				$data = array();
				$start_arr = pjUtil::convertDateTime($_POST['date'] . ' ' . $_POST['start_time'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format']);
				$end_arr = pjUtil::convertDateTime($_POST['date'] . ' ' . $_POST['end_time'], $this->option_arr['o_date_format'], $this->option_arr['o_time_format']);
				
				$data['start_ts'] = $start_arr['iso_date_time'];
				$data['end_ts'] = $end_arr['iso_date_time'];
				pjScheduleModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjSchedule', 'data');
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminSchedule&action=pjActionIndex&err=ASC01");
				
			} else {
				$arr = pjScheduleModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminSchedule&action=pjActionIndex&err=ASC08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjSchedule');
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
				
				$class_arr = pjClassModel::factory()
					->select("t1.*, t2.content AS course")
					->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->orderBy("course ASC, start_date ASC")
					->findAll()
					->getData();
				$this->set('class_arr', $class_arr);
				
				$teacher_arr = pjTeacherModel::factory()->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				$this->set('teacher_arr', $teacher_arr);
				
				$this->appendCss('jquery.ui.timepicker.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery.ui.timepicker.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminSchedule.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionEdit()
	{
		$this->checkLogin();
	
		if ($this->isAdmin())
		{
			if(isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$arr = pjClassModel::factory()->find($_GET['id'])->getData();
				if(!empty($arr))
				{
					if(isset($_POST['schedule_edit']))
					{
						
						$pjScheduleModel = pjScheduleModel::factory();
						$pjMultiLangModel = pjMultiLangModel::factory();
						$schedule_arr = $pjScheduleModel->where('class_id', $_POST['class_id'])->findAll()->getData();
						$remove_id_arr = array();
						foreach($schedule_arr as $schedule)
						{
							if(!isset($_POST['schedule_id'][$schedule['id']]))
							{
								$remove_id_arr[] = $schedule['id'];
							}
						}
						
						$class_id = $_POST['class_id'];
						foreach($_POST['date'] as $index => $date)
						{
							$data = array();
							$data['class_id'] = $class_id;
							$start_arr = pjUtil::convertDateTime($date . ' ' . $_POST['start_time'][$index] , $this->option_arr['o_date_format'], $this->option_arr['o_time_format']);
							$end_arr = pjUtil::convertDateTime($date . ' ' . $_POST['end_time'][$index] , $this->option_arr['o_date_format'], $this->option_arr['o_time_format']);
							$data['start_ts'] = $start_arr['iso_date_time'];
							$data['end_ts'] = $end_arr['iso_date_time'];
							$data['teacher_id'] = $_POST['teacher_id'][$index];

							if (isset($_POST['schedule_id'][$index]))
							{
								$pjScheduleModel->reset()->where('id', $_POST['schedule_id'][$index])->limit(1)->modifyAll($data);
								
								foreach ($_POST['i18n'] as $locale => $locale_arr)
								{
									foreach ($locale_arr as $field => $content)
									{
										if(is_array($content))
										{
											$sql = sprintf("INSERT INTO `%1\$s` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`)
													VALUES (NULL, :foreign_id, :model, :locale, :field, :update_content, :source)
													ON DUPLICATE KEY UPDATE `content` = :update_content, `source` = :source;",
													$pjMultiLangModel->getTable()
											);
											$foreign_id = $_POST['schedule_id'][$index];
											$model = 'pjSchedule';
											$source = 'data';
											$update_content = $content[$index];
											$modelObj = $pjMultiLangModel->reset()->prepare($sql)->exec(compact('foreign_id', 'model', 'locale', 'field', 'update_content', 'source'));
										}
									}
								}
							}else{
								$id = $pjScheduleModel->reset()->setAttributes($data)->insert()->getInsertId();
								if ($id !== false && (int) $id > 0)
								{
									if (isset($_POST['i18n']))
									{
										foreach ($_POST['i18n'] as $locale => $locale_arr)
										{
											foreach ($locale_arr as $field => $content)
											{
												if(is_array($content))
												{
													$insert_id = $pjMultiLangModel->reset()->setAttributes(array(
															'foreign_id' => $id,
															'model' => 'pjSchedule',
															'locale' => $locale,
															'field' => $field,
															'content' => $content[$index],
															'source' => 'data'
													))->insert()->getInsertId();
												}
											}
										}
									}
								}
							}
						}
						
						if(!empty($remove_id_arr))
						{
							$pjScheduleModel->reset()->whereIn('id', $remove_id_arr)->eraseAll();
						}
						
						pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminSchedule&action=pjActionEdit&id=" . $_GET['id']);
					}else{
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
						
						$pjClassModel = pjClassModel::factory();
						$class_arr = $pjClassModel
							->select("t1.*, t2.content AS course, t3.size, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.class_id=t1.id) AS cnt_students")
							->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
							->join('pjCourse', 't3.id=t1.course_id', 'left')
							->find($_GET['id'])
							->getData();
						$this->set('class_arr', $class_arr);
						
						$teacher_arr = pjTeacherModel::factory()->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
						$this->set('teacher_arr', $teacher_arr);
						
						$schedule_arr = pjScheduleModel::factory()->where("class_id", $_GET['id'])->findAll()->getData();
						foreach($schedule_arr as $k => $v)
						{
							$schedule_arr[$k]['i18n'] = pjMultiLangModel::factory()->getMultiLang($schedule_arr[$k]['id'], 'pjSchedule');
						}
						$this->set('schedule_arr', $schedule_arr);
						
						$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
						$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
						$this->appendCss('jquery.ui.timepicker.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
						$this->appendJs('jquery.ui.timepicker.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
						$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
						$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
						$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
						$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
						$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
						$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
						$this->appendJs('pjAdminSchedule.js');
					}
				}else{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminSchedule&action=pjActionIndex&err=ASC08");
				}
			}else{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminSchedule&action=pjActionIndex&err=ASC08");
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
			$pjBookingModel = pjBookingModel::factory()->join('pjStudent', "t2.id=t1.student_id", 'left');
	
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('confirmed','cancelled','pending')))
			{
				$pjBookingModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjBookingModel->where("(t2.name LIKE '%$q%' OR t2.email LIKE '%$q%' OR t2.phone LIKE '%$q%')");
			}
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjBookingModel->where('t1.class_id', $_GET['id']);
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
				->select("t1.*, t2.name, t2.email, t2.phone")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			foreach($data as $k => $v)
			{
				$v['deposit'] = pjUtil::formatCurrencySign($v['deposit'], $this->option_arr['o_currency']);
				$data[$k] = $v;
			}	
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionPrintStudents()
	{
		$this->checkLogin();
		
		$this->setLayout('pjActionPrint');
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if(isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
			{
				$arr = pjClassModel::factory()->find($_GET['class_id'])->getData();
				if(!empty($arr))
				{
					$class_arr = pjClassModel::factory()
					->select("t1.*, t2.content AS course, t3.size, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.class_id=t1.id) AS cnt_students")
					->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->join('pjCourse', 't3.id=t1.course_id', 'left')
					->find($_GET['class_id'])
					->getData();
					$this->set('class_arr', $class_arr);
					
					$booking_arr = pjBookingModel::factory()
						->select("t1.*, t2.name, t2.email, t2.phone")
						->join('pjStudent', "t2.id=t1.student_id", 'left')
						->where('t1.class_id', $_GET['class_id'])
						->findAll()
						->getData();
						
					$this->set('booking_arr', $booking_arr );
					$this->set('status', 0);
				}else{
					$this->set('status', 1);
				}
			}else{
				$this->set('status', 1);
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionLoadSchedule()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if(isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
			{
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
				
				$class_arr = pjClassModel::factory()
				->select("t1.*, t2.content AS course, t3.size, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.class_id=t1.id) AS cnt_students")
				->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
				->join('pjCourse', 't3.id=t1.course_id', 'left')
				->find($_GET['class_id'])
				->getData();
				$this->set('class_arr', $class_arr);
				
				$teacher_arr = pjTeacherModel::factory()->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				$this->set('teacher_arr', $teacher_arr);
				
				$schedule_arr = pjScheduleModel::factory()->where("class_id", $_GET['class_id'])->findAll()->getData();
				foreach($schedule_arr as $k => $v)
				{
					$schedule_arr[$k]['i18n'] = pjMultiLangModel::factory()->getMultiLang($schedule_arr[$k]['id'], 'pjSchedule');
				}
				$this->set('schedule_arr', $schedule_arr);
			}
		}
	}
	public function pjActionPrintClass()
	{
		$this->checkLogin();
	
		$this->setLayout('pjActionPrint');
	
		if ($this->isAdmin() || $this->isTeacher() || $this->isStudent())
		{
			if(isset($_GET['class_id']) && (int) $_GET['class_id'] >0)
			{
				$pjClassModel = pjClassModel::factory();
				$class_arr = $pjClassModel
					->select("t1.*, t2.content AS course, t3.size, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.class_id=t1.id) AS cnt_students")
					->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->join('pjCourse', 't3.id=t1.course_id', 'left')
					->find($_GET['class_id'])
					->getData();
				if(!empty($class_arr))
				{
					$this->set('class_arr', $class_arr);
					
					$schedule_arr = pjScheduleModel::factory()
						->select("t1.*, t2.content as venue, t3.name")
						->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjSchedule' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'venue'", 'left')
						->join('pjTeacher', 't3.id=t1.teacher_id', 'left')
						->where("class_id", $_GET['class_id'])
						->findAll()
						->getData();
					$this->set('schedule_arr', $schedule_arr);
					$this->set('status', 0);
				}else{
					$this->set('status', 1);
				}
			}else{
				$this->set('status', 1);
			}
		} else {
			$this->set('status', 2);
		}
	}
	public function pjActionPrintSchedule()
	{
		$this->checkLogin();
	
		$this->setLayout('pjActionPrint');
	
		if ($this->isAdmin() || $this->isTeacher() || $this->isStudent())
		{
			$pjScheduleModel = pjScheduleModel::factory()
				->join('pjClass', 't1.class_id=t2.id', 'left')
				->join('pjMultiLang', "t3.foreign_id = t2.course_id AND t3.model = 'pjCourse' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'title'", 'left')
				->join('pjTeacher', 't1.teacher_id=t4.id', 'left')
				->join('pjMultiLang', "t5.foreign_id = t1.id AND t5.model = 'pjSchedule' AND t5.locale = '".$this->getLocaleId()."' AND t5.field = 'venue'", 'left');
			$today = date("Y-m-d 00:00:00");
			$pjScheduleModel->where("(t1.start_ts >= '".$today."')");
			
			if(isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
			{
				$pjScheduleModel->where("t1.class_id", $_GET['class_id']);
			}
			if(isset($_GET['teacher_id']) && (int) $_GET['teacher_id'] > 0)
			{
				$pjScheduleModel->where("t1.teacher_id", $_GET['teacher_id']);
			}
			if($this->isTeacher())
			{
				$pjScheduleModel->where("t1.teacher_id", $this->getUserId());
			}
			if($this->isStudent())
			{
				$pjScheduleModel->where("(t1.class_id IN (SELECT `TB`.class_id FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.student_id='".$this->getUserId()."') )");
			}	
			$column = 'start_ts';
			$direction = 'ASC';
							
			$arr = $pjScheduleModel
				->select("t1.*, t2.start_date, t3.content AS class, t4.name AS teacher, t5.content as venue")
				->orderBy("$column $direction")
				->findAll()
				->getData();
			$this->set('arr', $arr);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionNextPeriod()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$next_date = null;
			$next_start_time = null;
			$next_end_time = null;
			$next_teacher_id = null;
			$key = null;
			$day = 1;
			if($_GET['period'] == 'week')
			{
				$day = 7;
			}
			foreach($_POST['date'] as $index => $date)
			{
				$next_date = date($this->option_arr['o_date_format'],strtotime(pjUtil::formatDate($date, $this->option_arr['o_date_format'])) + $day * 86400);
				$next_start_time = $_POST['start_time'][$index];
				$next_end_time = $_POST['end_time'][$index];
				$next_teacher_id = $_POST['teacher_id'][$index];
				$key= $index;
			}
			
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
			
			$teacher_arr = pjTeacherModel::factory()->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
			$this->set('teacher_arr', $teacher_arr);
			
			$this->set('key', $key);
			$this->set('next_date', $next_date);
			$this->set('next_start_time', $next_start_time);
			$this->set('next_end_time', $next_end_time);
			$this->set('next_teacher_id', $next_teacher_id);
		}
	}
	
	public function pjActionEmailTeacher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['send_schedule']))
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
	
				foreach($_POST['email'] as $index => $to)
				{
					$name = @$_POST['name'][$index];
					$send_message = str_replace("{NAME}", $name, $message);
					$Email
						->setTo($to)
						->setFrom($_POST['from'])
						->setSubject($subject)
						->send($send_message);
				}
					
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
			}
	
			if (isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
			{
				
				$pjClassModel = pjClassModel::factory();
				$class_arr = $pjClassModel
					->select("t1.*, t2.content AS course, t3.size, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.class_id=t1.id) AS cnt_students")
					->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->join('pjCourse', 't3.id=t1.course_id', 'left')
					->find($_GET['class_id'])
					->getData();
				$schedule_arr = pjScheduleModel::factory()
					->select("t1.*, t2.content as venue, t3.name")
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjSchedule' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'venue'", 'left')
					->join('pjTeacher', 't3.id=t1.teacher_id', 'left')
					->where("class_id", $_GET['class_id'])
					->findAll()
					->getData();
				$this->set('class_arr', $class_arr);
				$this->set('schedule_arr', $schedule_arr);
				
				$teacher_id_arr = pjScheduleModel::factory()->where("class_id", $_GET['class_id'])->findAll()->getDataPair(null, 'teacher_id');
				if(!empty($teacher_id_arr))
				{
					$teacher_arr = pjTeacherModel::factory()->whereIn('id', $teacher_id_arr)->orderBy('name ASC')->findAll()->getData();
					$this->set('teacher_arr', $teacher_arr);
				}
				$this->set('arr', array(
						'from' => $this->getAdminEmail(),
						'subject' => __('lblClassScheduleSubject', true)
				));
			} else {
				exit;
			}
		}
	}
	
	public function pjActionEmailStudent()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['send_schedule']))
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
	
				foreach($_POST['email'] as $index => $to)
				{
					$name = @$_POST['name'][$index];
					$send_message = str_replace("{NAME}", $name, $message);
					$Email
					->setTo($to)
					->setFrom($_POST['from'])
					->setSubject($subject)
					->send($send_message);
				}
					
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
			}
	
			if (isset($_GET['class_id']) && (int) $_GET['class_id'] > 0)
			{
				$pjClassModel = pjClassModel::factory();
				$class_arr = $pjClassModel
					->select("t1.*, t2.content AS course, t3.size, (SELECT COUNT(`TB`.id) FROM `".pjBookingModel::factory()->getTable()."` AS `TB` WHERE `TB`.class_id=t1.id) AS cnt_students")
					->join('pjMultiLang', "t2.foreign_id = t1.course_id AND t2.model = 'pjCourse' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->join('pjCourse', 't3.id=t1.course_id', 'left')
					->find($_GET['class_id'])
					->getData();
				$this->set('class_arr', $class_arr);
				
				$student_arr = pjBookingModel::factory()
					->select("t1.*, t2.name, t2.email, t2.phone")
					->join('pjStudent', "t2.id=t1.student_id", 'left')
					->where('t1.class_id', $_GET['class_id'])
					->findAll()
					->getData();	
				$this->set('student_arr', $student_arr);
				
				$teacher_id_arr = pjScheduleModel::factory()->where("class_id", $_GET['class_id'])->findAll()->getDataPair(null, 'teacher_id');
				if(!empty($teacher_id_arr))
				{
					$teacher_arr = pjTeacherModel::factory()->whereIn('id', $teacher_id_arr)->orderBy('name ASC')->findAll()->getData();
					$this->set('teacher_arr', $teacher_arr);
				}
				$this->set('arr', array(
						'from' => $this->getAdminEmail(),
						'subject' => __('lblStudentsListSubject', true)
				));
			} else {
				exit;
			}
		}
	}
}
?>