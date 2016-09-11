<?php
require_once 'UsuarioModel.php';
class UsersController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */

    }

    public function indexAction()
    {
        // listado de Usuarios del sistema  
		$Usuario = new UsuarioModel();
		$this->view->assign("usuarios", $Usuario->getUsuariosList());
			
    }
    
    public function addAction(){
    	// enviar la lista de personas
        if( $this->getRequest()->isPost() ){
	    		try {
		    		//guardar el usuario
		    		$usuario = new UsuarioModel();
		    		$data = array( 
		    						  'usrName' => $this->getRequest()->getParam('usrName')
		    						, 'usrLastName' => $this->getRequest()->getParam('usrLastName')
		    						, 'usrPhone' => $this->getRequest()->getParam('usrPhone')
		    						, 'usrEmail' => $this->getRequest()->getParam('usrEmail')
		    						, 'usrPassword' => md5($this->getRequest()->getParam('usrPassword'))
		    					 );
		    		$usuario->insert($data);
		    		$this->view->assign('msgok', 'User added successfully');
		    		$this->_forward('auth', 'login');
	    		} catch (Exception $e ) {
	    			$this->view->assign('msgerr', 'ERROR!!: ' . $e->getMessage() );
		    		$this->_forward('add', 'user');  
	    		}
    	}     	
    }
}