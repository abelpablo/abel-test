<?php
class UsuarioModel extends Zend_Db_Table_Abstract
{
	protected $_name = 'user';
	protected $_primary = 'usrid';
	
	public function getUsuariosList(){
		$sql = "SELECT
						  t0.usrName
						, t0.usrLastName
						, t0.usrPhone
						, t0.usrEmail
					FROM user t0 ";
		return $this->getAdapter()->fetchAll($sql);
		
	}
	
}