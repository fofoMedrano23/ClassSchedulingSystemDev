<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjController
{
	public $models = array();
	
	public $defaultLocale = 'admin_locale_id';
  
	public $defaultFields = 'fields';
	
	public $defaultFieldsIndex = 'fields_index';
  
	protected function loadSetFields($force=FALSE, $locale_id=NULL, $fields=NULL)
	{
		if (is_null($locale_id))
		{
			$locale_id = $this->getLocaleId();
		}
		
		if (is_null($fields))
		{
			$fields = $this->defaultFields;
		}
		
		$registry = pjRegistry::getInstance();
		if ($force
				|| !isset($_SESSION[$this->defaultFieldsIndex])
				|| $_SESSION[$this->defaultFieldsIndex] != $this->option_arr['o_fields_index']
				|| !isset($_SESSION[$fields])
				|| empty($_SESSION[$fields]))
		{
			pjAppController::setFields($locale_id);
	
			# Update session
			if ($registry->is('fields'))
			{
				$_SESSION[$fields] = $registry->get('fields');
			}
			$_SESSION[$this->defaultFieldsIndex] = $this->option_arr['o_fields_index'];
		}
	
		if (isset($_SESSION[$fields]) && !empty($_SESSION[$fields]))
		{
			# Load fields from session
			$registry->set('fields', $_SESSION[$fields]);
		}
		
		return TRUE;
	}
	
	public function isCountryReady()
    {
    	return $this->isAdmin();
    }
    
	public function isOneAdminReady()
    {
    	return $this->isAdmin();
    }
	
	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
		pjAppModel::factory()->prepare("SET SESSION time_zone = :offset;")->exec(compact('offset'));
    }
    
	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}
	
			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}
    
    public function beforeFilter()
    {
    	$this->appendJs('jquery.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
		$dm = new pjDependencyManager(PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		$this->appendJs('jquery-migrate.min.js', $dm->getPath('jquery_migrate'), FALSE, FALSE);
		$this->appendJs('pjAdminCore.js');
		$this->appendCss('reset.css');
		 
		$this->appendJs('js/jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
		$this->appendCss('css/smoothness/jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
				
		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('admin.css');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
			$this->set('option_arr', $this->option_arr);
			$this->setTime();
			
			if (!isset($_SESSION[$this->defaultLocale]))
			{
				$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
				if (count($locale_arr) === 1)
				{
					$this->setLocaleId($locale_arr[0]['id']);
				}
			}
			$this->loadSetFields();
		}
    }
    
    public function isEditor()
    {
    	return $this->getRoleId() == 2;
    }
    public function isTeacher()
    {
    	if(isset($_SESSION[$this->defaultUser]))
    	{
    		$teacher = $_SESSION[$this->defaultUser];
    		if(isset($teacher['is_teacher']))
    		{
    			return true;
    		}else{
    			return false;
    		}
    	}else{
    		return false;
    	}
    }
    public function isStudent()
    {
    	if(isset($_SESSION[$this->defaultUser]))
    	{
    		$student = $_SESSION[$this->defaultUser];
    		if(isset($student['is_student']))
    		{
    			return true;
    		}else{
    			return false;
    		}
    	}else{
    		return false;
    	}
    }
    public function getForeignId()
    {
    	return 1;
    }
    
    public static function setFields($locale)
    {
    if(isset($_SESSION['lang_show_id']) && (int) $_SESSION['lang_show_id'] == 1)
		{
			$fields = pjMultiLangModel::factory()
				->select('CONCAT(t1.content, CONCAT(":", t2.id, ":")) AS content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}else{
			$fields = pjMultiLangModel::factory()
				->select('t1.content, t2.key')
				->join('pjField', "t2.id=t1.foreign_id", 'inner')
				->where('t1.locale', $locale)
				->where('t1.model', 'pjField')
				->where('t1.field', 'title')
				->findAll()
				->getDataPair('key', 'content');
		}
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $key => $value)
		{
			if (strpos($key, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $key);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $value;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array(
							'app/web/upload'
						);
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}
		
		return $result;
	}
	
	public function friendlyURL($str, $divider='-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str));
		$str = trim($str);
		$str = preg_replace('/[_|\s]+/', $divider, $str);
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str);
		$str = preg_replace('/[-]+/', $divider, $str);
		$str = preg_replace('/^-+|-+$/', '', $str);
		return $str;
	}
	
	public function getAdminEmail()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? $arr[0]['email'] : null;	
	}
	
	public function getAllAdminEmails()
	{
		$arr = pjUserModel::factory()
			->where('role_id', 1)
			->orderBy("t1.id ASC")
			->findAll()
			->getDataPair(null, 'email');
		return $arr;
	}
	
	public function getAdminPhone()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? (!empty($arr[0]['phone']) ? $arr[0]['phone'] : null) : null;	
	}
	
	public static function calPrice($course_id, $option_arr)
	{
		$subtotal = 0;
		$tax = 0;
		$total = 0;
		$deposit = 0;
		
		$arr = pjCourseModel::factory()->find($course_id)->getData();
		$subtotal += (float) $arr['price'];
		$tax = $subtotal * (float) $option_arr['o_tax_payment'] / 100;
		$total = $subtotal + $tax;
		$deposit = $total * (float) $option_arr['o_deposit_payment'] / 100;
		
		return compact('subtotal', 'tax', 'total', 'deposit');
	}
	
	public static function getTokens($booking_id, $option_arr, $salt, $locale_id)
	{
		$country = NULL;
		$start_date = NULL;
		$end_date = NULL;
		
		$data = pjBookingModel::factory()->find($booking_id)->getData();
		$student = pjStudentModel::factory()->find($data['student_id'])->getData();
		$class = pjClassModel::factory()->find($data['class_id'])->getData();
		$course = pjCourseModel::factory()
			->select("t1.*, t2.content as title")
			->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCourse' AND t2.locale = '".$locale_id."' AND t2.field = 'title'", 'left')
			->find($data['course_id'])
			->getData();
		
		$personal_titles = __('personal_titles', true, false);
		$payment_methods = __('payment_methods', true, false);
		
		$title = $personal_titles[$student['title']];
		$payment_method = !empty($data['payment_method']) ? $payment_methods[$data['payment_method']]: NULL;
		
		$start_date = date($option_arr['o_date_format'], strtotime($class['start_date']));
		$end_date = date($option_arr['o_date_format'], strtotime($class['end_date']));
		
		if(isset($student['country_id']) && (int) $student['country_id'] > 0)
		{
			$country_arr = pjCountryModel::factory()
				->select('t1.id, t2.content AS country_title')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
				->find($student['country_id'])
				->getData();
			if (!empty($country_arr))
			{
				$country = $country_arr['country_title'];
			}
		}
		
		$subtotal = pjUtil::formatCurrencySign($data['subtotal'], $option_arr['o_currency']);
		$tax = pjUtil::formatCurrencySign($data['tax'], $option_arr['o_currency']);
		$deposit = pjUtil::formatCurrencySign($data['deposit'], $option_arr['o_currency']);
		$total = pjUtil::formatCurrencySign($data['total'], $option_arr['o_currency']);
		
		$cancelURL = PJ_INSTALL_URL . 'index.php?controller=pjFrontEnd&action=pjActionCancel&id='.@$data['id'].'&hash='.sha1(@$data['id'].@$data['created'].$salt);
		$cancelURL = '<a href="'.$cancelURL.'">' . $cancelURL . '</a>';
		
		$cc_exp = '';
		if(!empty($data['payment_method']) && $data['payment_method'] == 'creditcard')
		{
			$cc_exp = @$data['cc_exp_month'] . '-' . @$data['cc_exp_year'];
		}
		
		$search = array(
				'{Class}',
				'{StartDate}',
				'{EndDate}',
				'{UniqueID}',
				'{Title}',
				'{Name}',
				'{Email}',
				'{Phone}',
				'{Country}',
				'{City}',
				'{State}',
				'{Zip}',
				'{Address}',
				'{Company}',
				'{Notes}',
				'{SubTotal}',
				'{Total}',
				'{Tax}',
				'{Deposit}',
				'{PaymentMethod}',
				'{CCType}',
				'{CCNum}',
				'{CCExp}',
				'{CCSec}',
				'{CancelURL}'
		);
		$replace = array(
				$course['title'],
				$start_date,
				$end_date,
				@$data['uuid'],
				$title,
				@$student['name'],
				@$student['email'],
				@$student['phone'],
				$country,
				@$student['city'],
				@$student['state'],
				@$student['zip'],
				@$student['address'],
				@$student['company'],
				@$data['notes'],
				$subtotal,
				$total,
				$tax,
				$deposit,
				$payment_method,
				@$data['cc_type'],
				@$data['cc_num'],
				$cc_exp,
				@$data['cc_code'],
				$cancelURL
		);
		return compact('search', 'replace');
	}
	public static function getStudentTokens($student, $option_arr, $salt, $locale_id)
	{
		$country = NULL;
		
		$personal_titles = __('personal_titles', true, false);
		$title = $personal_titles[$student['title']];
		
		if(isset($student['country_id']) && (int) $student['country_id'] > 0)
		{
			$country_arr = pjCountryModel::factory()
				->select('t1.id, t2.content AS country_title')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
				->find($student['country_id'])
				->getData();
			if (!empty($country_arr))
			{
				$country = $country_arr['country_title'];
			}
		}
	
		$search = array(
				'{Title}',
				'{Name}',
				'{Email}',
				'{Password}',
				'{Phone}',
				'{Country}',
				'{City}',
				'{State}',
				'{Zip}',
				'{Address}',
				'{Company}'
		);
		$replace = array(
				$title,
				@$student['name'],
				@$student['email'],
				@$student['password'],
				@$student['phone'],
				$country,
				@$student['city'],
				@$student['state'],
				@$student['zip'],
				@$student['address'],
				@$student['company']
		);
		return compact('search', 'replace');
	}
}
?>