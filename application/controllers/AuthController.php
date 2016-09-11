<?php
class AuthController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    	$this->_helper->layout()->setLayout('login');
    	
    }
    
    public function indexAction()
    {
        // redirecciona a login
        $this->_forward('login');
    }
    /**
     * 
     * Formulario de Login
     */
	public function loginAction()
	{
		
	    if(Application_Model_Usuario::isLoggedIn()){
	    	$this->_redirect('/users/');
        }
    	$this->view->assign('identity', Application_Model_Usuario::getIdentity() );
    	if ($this->getRequest()->isPost()) {
    		$filter = new Zend_Filter_StripTags();
    		$email = trim($filter->filter($this->getRequest()->getPost('usrEmail')));
    		$password = trim($filter->filter($this->getRequest()->getPost('usrPassword')));
    			
    		try{
    			$user = new Application_Model_Usuario();
    			$user->login($email, $password);
    			$userLoggedIn = Application_Model_Usuario::getIdentity();
    			$Log = new Application_Model_Log();
    			$Log->insert(array('usrid'=> $userLoggedIn->usrid , 'logType'=>'login'));
    			$this->_redirect('/users/');
    		} catch(Exception $e){
    			$responseLogin = $e->getMessage();
    			$this->view->assign( 'email', $this->getRequest()->getPost('usrEmail') );
    			$this->view->error = $responseLogin;
    		}
    	}
		
	}
	/**
	 * 
	 * Logout
	 */
	public function logoutAction()
	{
    	$user = new Application_Model_Usuario();
    	$user->logout();
    	$this->_forward('index', 'auth');
	}
	public function noauthAction(){
		$this->getResponse()->setHttpResponseCode(401);
	}
}