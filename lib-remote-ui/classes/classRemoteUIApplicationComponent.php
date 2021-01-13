<?php

class RemoteUIApplicationComponent extends ApplicationComponent {
	
	const STAGE = 92;
	
	private $elements = null;
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() { return 'remote.ui'; }
	
	//************************************************************************************
	/**
	 * @return int[]
	 */
	public function getStages() {
		return array(self::STAGE);
	}
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onInit($stage) {
		
	}

	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onProcess($stage) {
		
	}	
	
	//************************************************************************************
	/**
	 * @return IRemoteUIExtension[]
	 */
	public function getRemoteUIExtensions() {
		return $this->getExtensions('IRemoteUIExtension');
	}
	
	//************************************************************************************
	/**
	 * @return IRemoteUIElement[]
	 */
	public function getRemoteUIElements() {
		if ($this->elements === null) {
			$this->elements = array();
			
			foreach(CodeBase::getClassesImplementing('IRemoteUIElement') as $oClass) {
				if ($oClass->isAbstract()) continue;
				
				$this->elements[] = $oClass->ctorCreate();
			}
		}
		return $this->elements;
	}
	
	
}

?>