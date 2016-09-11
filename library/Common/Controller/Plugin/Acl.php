<?php

class Common_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    private $_whitelist;

    public function __construct()
    {
        // ACCTIONES PERMITIDAS POR LISTA BLANCA //
        $this->_whitelist = array
        (
            'default_auth'
        );
    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $exist      = false;
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        //$acl = Zend_Registry::get('acl');
        // Comprueba existencia de recurso //
        if($dispatcher->isDispatchable($request))
        {
            $class  = $dispatcher->loadClass($dispatcher->getControllerClass($request));
            $method = $dispatcher->formatActionName($request->getActionName());
            $exist  = is_callable(array($class, $method));
        }
        if ( $request->getModuleName() === 'default'){ return;  } // el módulo default no tiene seguridad
        if($exist)
        {
            $permitido  = false;

            // Obtengo a donde se quiere acceder y se compone el recurso //
            $recurso = $request->getModuleName().'_'.$request->getControllerName();

            // Si se encuentra en White list lo dejamos pasar //
            if (in_array($recurso, $this->_whitelist))
            {
                return true;
            }
            else
            {
                $permitido = false;
            }

           
            require_once APPLICATION_PATH . '/../application/models/Usuario.php';
            $us = new Application_Model_Usuario();
            
            $usuario = $us::getIdentity();//UsuarioModel::getIdentity();
           
           
            $cache  = Zend_Registry::get('cache');
            $acl    = $cache->load('acl');
           
                     
            if(is_object($usuario)){ // hago esta verificación por que si se pierde la session $usuario no es un objeto válido
            	$roles = explode(',', $usuario->rol_nombre );
            	$permitido = false;
            	foreach ($roles as $rol) {
            		if($acl->isAllowed(trim($rol) , null, $recurso)){
            			$permitido = true;
            			break;
            		}
            	}
            	if(!$permitido) {
            		$request->setModuleName('default');
            		$request->setControllerName('auth');
            		$request->setActionName('noauth');
            	}
            } else {
            	$request->setModuleName('default');
            	$request->setControllerName('auth');
            	$request->setActionName('noauth');
            }
        }
    }
}

?>