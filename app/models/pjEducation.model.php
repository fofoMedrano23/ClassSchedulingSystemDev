<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjEducationModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'education';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'education', 'type' => 'varchar', 'default' => ':NULL'),
		
	);
	
	
	public static function factory($attr=array())
	{
		return new pjEducationModel($attr);
	}
}
?>
