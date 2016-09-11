<?php
/**
 * 
 * @author damills
 *
 */
class Common_Controller_Plugin_Authentication extends Zend_Controller_Plugin_Abstract
{
    private $_whitelist;

    public function __construct()
    {
        $this->_whitelist = array
        (
            'auth/login',
            'auth/logout',
        	'auth/index',
        	'users/add'
        );
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $controller = strtolower($request->getControllerName());
        $action = strtolower($request->getActionName());
        $route = $controller . '/' . $action; 

        if (in_array($route, $this->_whitelist)){ return; }
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) { return; }
        $request->setActionName('login');
        $request->setControllerName('auth');
        self::setRequest($request);
    }

    public function  routeShutdown (Zend_Controller_Request_Abstract $request)
    {

    }
}
?>