<?php
class Common_Controller_Helper_Acl
{
    public $acl;

    public function __construct()
    {
        $this->acl = new Zend_Acl();
    }

    /**
    * @desc Setea en las ACL el listado de roles activos para el sistema
    * @return unknown_type
    */
    public function setRoles()
    {    	
    	try
    	{
    		$model = new Application_Model_Acl();
    		$roles = $model->getRoles();
    		//meto el grupo anonimo por defecto
    		$this->acl->addRole(new Zend_Acl_Role('anonimus'));
    	
    		// recorro los grupos cargados en la DB
    	
    		foreach ($roles as $rol)
    		{
    			$this->acl->addRole(new Zend_Acl_Role( $rol->nombre ) );
    		}
    	}
    	catch (Exception $e)
    	{
    		throw new Exception('Ocurri&oacute; el siguiente error al traer los grupos:'. $e->getMessage());
    	}
    }

    /**
    * @desc Setea en las ACL el listado de recursos para el sistema
    * @return unknown_type
    */
    public function setResources()
    {
    	try
    	{
    		$model = new Application_Model_Acl();
    		$recursos = $model->getRecursos();
    		foreach($recursos as $recurso)
    		{
    			$this->acl->addResource(new Zend_Acl_Resource( $recurso->nombre ));
    		}
    	}
    	catch (Exception $e)
    	{
    		throw new Exception('Ocurri&oacute; el siguiente error al traer los recursos:'. $e->getMessage());
    	}
    	
    }

    /**
    * @desc Setea en las ACL el listado de privilegios
    * @return unknown_type
    */
    public function setPrivilages()
    {         
    	$model = new Application_Model_Acl();
    	$reglas = $model->getRecursosXgrupos();
    	foreach ($reglas as $key => $value)
    	{
    		if(count($value) > 0 )
    		{
    			$this->acl->allow($key,null,$value);
    		}
    	}
    }

    /**
    * @desc Setea en las ACL los datos necesarios para verificación de permisos
    * @return unknown_type
    */
    public function setAcl()
    {
        $cache = Zend_Registry::get('cache');

        if ( ($rules = $cache->load('acl')) === false )
        { 
            // no hay cache
            $this->setRoles();
            $this->setResources();
            $this->setPrivilages();
            $rules = $this->acl;
            $cache->save($rules);
        }
    }
}

?>