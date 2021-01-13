<?php

abstract class AuthServiceLocalSession implements IAuthService {
	
	/**
	 * @var HTTPSession
	 */
	private $oSession = null;
	
	/**
	 * @var IAuthorizedUser
	 */
	private $oLoggedUser = false;
	
	/**
	 * @var AuthApplicationComponent
	 */
	private $oComponent = null;
	
	//************************************************************************************
	/**
	 * @return HTTPSession
	 */
	public function getSession() { return $this->oSession; }

	//************************************************************************************
	/**
	 * @return AuthApplicationComponent
	 */
	public function getComponent() { return $this->oComponent; }
	
	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext() { return $this->getComponent()->getApplicationContext(); }
	
	//************************************************************************************
	/**
	 * @param AuthApplicationComponent $oComponent
	 */
	public function onInit($oComponent) {
		$oRequest = $oComponent->getService('IHTTPServerRequest');
		false && $oRequest = new IHTTPServerRequest();
		
		if ($oRequest) {
			$this->oSession = $oRequest->getSession();
		}
		
		$this->oComponent = $oComponent;
	}
	
	//************************************************************************************
	/**
	 * @param IAuthorizedUser $oUser
	 */
	private function saveToSession($oUser) {
		if (!$this->getSession()) return;
		
		if ($oUser) {
			$this->getSession()->set('AuthServiceLocalSession.userID', $oUser->authGetUserID());
			$this->oLoggedUser = $oUser;
		} else {
			$this->getSession()->set('AuthServiceLocalSession.userID', '');
			$this->oLoggedUser = null;
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $plainPassword
	 * @return IAuthorizedUser
	 */
	public function doLogin($name, $plainPassword) {
		if (!$name) return null;
		if (!$plainPassword) return null;
		
		$oUser = $this->getUserByLoginData($name, $plainPassword);
		if ($oUser) {
			$this->saveToSession($oUser);
			return $oUser;
		}
		
		return null;
	}
	
	//************************************************************************************
	public function doLogout() {
		$this->saveToSession(null);
	}
	
	//************************************************************************************
	/**
	 * @return IAuthorizedUser
	 */
	public function getLoggedUser() {
		if ($this->oLoggedUser === false) {
			$this->oLoggedUser = null;
			if ($this->getSession()) {
				$id = $this->getSession()->get('AuthServiceLocalSession.userID');
				$this->oLoggedUser = $this->getUserByID($id);
			}
		}
		return $this->oLoggedUser;
	}		
	
	
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $plainPassword
	 * @return IAuthorizedUser
	 */
	protected abstract function getUserByLoginData($name, $plainPassword);
	
	//************************************************************************************
	/**
	 * @param string $userID
	 * @return IAuthorizedUser
	 */
	protected abstract function getUserByID($userID);
	
}

?>