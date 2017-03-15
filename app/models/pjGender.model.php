<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjGenderModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'gender';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'gender', 'type' => 'varchar', 'default' => ':NULL'),
		
	);
	
	
	public static function factory($attr=array())
	{
		return new pjGenderModel($attr);
	}
}
?>
