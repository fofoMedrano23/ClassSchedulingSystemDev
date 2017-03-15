<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjScheduleModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'schedule';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'class_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'teacher_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'start_ts', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'end_ts', 'type' => 'datetime', 'default' => ':NULL')
	);
	
	public $i18n = array('venue');
	
	public static function factory($attr=array())
	{
		return new pjScheduleModel($attr);
	}
}
?>