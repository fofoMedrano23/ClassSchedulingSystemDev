<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjStudentPaymentModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'students_payments';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'student_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'class_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'amount', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'paid')
	);
	
	public $i18n = array('description');
	
	public static function factory($attr=array())
	{
		return new pjStudentPaymentModel($attr);
	}
}
?>