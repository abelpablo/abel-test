<?php
include_once 'UsuarioModel.php';

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		$this->_redirect('/users');   			
												
    }
	public function logoutAction(){
		// no necesita vista para renderizarse
		$this->_helper->viewRenderer->setNoRender();

		$this->_forward('index', 'index'); 
				
	}
	
	public function noauthAction(){
		
	}

}

