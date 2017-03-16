<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjStudentModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'students';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'password', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'genero', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'experiencia', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'country_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'last_login', 'type' => 'datetime', 'default' => ':NULL'),
	);
	
	public static function factory($attr=array())
	{
		return new pjStudentModel($attr);
	}
}
?>