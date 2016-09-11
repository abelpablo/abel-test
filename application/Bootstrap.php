<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initApplication()
	{
		$this->bootstrap('frontController');
		date_default_timezone_set("America/Argentina/Buenos_Aires");
		Zend_Loader::loadClass('Zend_Session_Namespace');
		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->registerNamespace('Common_');
	}
	
	protected function _initPlugins()
	{
		$auth = new Common_Controller_Plugin_Authentication();
		$this->frontController->registerPlugin($auth);
	}
}

