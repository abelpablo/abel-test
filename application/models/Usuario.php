<?php
class Application_Model_Usuario extends Zend_Db_Table_Abstract
{
	protected $_name = 'user';
	protected $_primary = 'usrid';
	
	const NOT_IDENTITY = 'notIdentity';
	const INVALID_CREDENTIAL = 'invalidCredential';
	const INVALID_USER = 'invalidUser';
	const INVALID_LOGIN = 'invalidLogin';
	
	/**
	 * Mensajes de validaciones por defecto
	 * @var array
	 */
	protected $_messages = array(
			self::NOT_IDENTITY	=> "Wrong data",
			self::INVALID_CREDENTIAL => "Wrong data",
			self::INVALID_USER => "Invalid user",
			self::INVALID_LOGIN => "Invalid email"
	);
	
	
	/**
	 * Asigna un mensaje a una clave de mensaje en un array asociativo de mensajes.
	 * @param string $messageString
	 * @param string $messageKey	OPTIONAL
	 * @return UserModel
	 * @throws Exception
	*/
	public function setMessage($messageString, $messageKey = null)
	{
		if ($messageKey === null) {
			$keys = array_keys($this->_messages);
			$messageKey = current($keys);
		}
		if (!isset($this->_messages[$messageKey])) {
			throw new Exception("No message exists for key '$messageKey'");
		}
		$this->_messages[$messageKey] = $messageString;
		return $this;
	}
	
	/**
	 * Agrega un array de mensajes.
	 * @param array $messages
	 * @return UserModel
	 */
	public function setMessages(array $messages)
	{
		foreach ($messages as $key => $message) {
			$this->setMessage($message, $key);
		}
		return $this;
	}
	
	/**
	 * Autentifica al usuario
	 * @param string $nick nombre de usuario
	 * @param string $password contraseña
	 * @throws Excepción
	 * @return UsuarioModel
	 */
	public function login($email, $clave)
	{
	
		if(!empty($email) && !empty($clave))
		{
			$db = Zend_Db_Table_Abstract::getDefaultAdapter();;
			$autAdapter = new Zend_Auth_Adapter_DbTable(
					$db,
					'user', 
					'usrEmail',
					'usrPassword'
			);
			$autAdapter->setIdentity($email)->setCredential( md5($clave) );
				
			$aut = Zend_Auth::getInstance();
				
			$result = $aut->authenticate($autAdapter);
			//echo '<pre>'; print_r($autAdapter); exit();
			switch ($result->getCode())
			{
				case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
					throw new Exception($this->_messages[self::NOT_IDENTITY]);
					break;
				case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
					throw new Exception($this->_messages[self::INVALID_CREDENTIAL]);
					break;
				case Zend_Auth_Result::SUCCESS:
					if ($result->isValid()) {
						$data = $autAdapter->getResultRowObject();
						$aut->getStorage()->write($data);
					} else {
						throw new Exception($this->_messages[self::INVALID_USER]);
					}
					break;
				default:
					throw new Exception($this->_messages[self::INVALID_LOGIN]);
					break;
			}
		} else {
			throw new Exception($this->_messages[self::INVALID_LOGIN]);
		}
		return $this;
	}
	/**
	 * Elimina la session, desloguea al usuario
	 * @return UsuarioModel
	 */
	public function logout()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$usersNs = new Zend_Session_NameSpace("members");
		$usersNs->unsetAll();
		return $this;
	}
	/**
	 * Obtiene la información del usuario logueado
	 * @return mixed si esta logueado obtiene un objeto Zend_Auth con la información del usuario, de lo contrario null
	 */
	public static function getIdentity()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			return $auth->getIdentity();
		}
		return null;
	}
	
	/**
	 * Verifica si esta logueado
	 * @return boolean
	 */
	public static function isLoggedIn()
	{
		return Zend_Auth::getInstance()->hasIdentity();
	}
	
}
