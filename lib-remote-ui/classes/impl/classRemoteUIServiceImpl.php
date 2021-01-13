<?php

class RemoteUIServiceImpl extends RemoteServiceSupplier implements IRemoteUIService {
	
	/**
	 * @var IRemoteUIElement
	 */
	private $oFoundElement = null;
	
	//************************************************************************************
	/**
	 * Stwierdza czy ten supplier pasuje do wywolania
	 * @param string[] $pathParts
	 * @param mixed $authData
	 * @return bool
	 */
	public function supplierMatches($pathParts, $authData) {
		if ($pathParts[0] == 'IRemoteUIService') return true;
		return false;
	}
	
	//************************************************************************************
	protected function onInit($oComponent, $pathParts, $authData) {
		$oRemoteUIComponent = $this->getApplicationContext()->getComponent('remote.ui');
		false && $oRemoteUIComponent = new RemoteUIApplicationComponent();
		
		
		$this->oFoundElement = null;
		foreach($oRemoteUIComponent->getRemoteUIElements() as $oElement) {
			if ($oElement->matches($pathParts)) {
				$this->oFoundElement = $oElement;
				break;
			}
		}
		
		if (!$this->oFoundElement) {
			throw new ObjectNotFoundException('Could not find RemoteUIElement');
		}
		
		$this->oFoundElement->onBeforeAction($this->getApplicationContext(), $pathParts, $authData);
	}

	//************************************************************************************
	/**
	 * @return string
	 */
	public function render() {
		if (!$this->oFoundElement) throw new IllegalStateException('oFoundElement is null');
		return $this->oFoundElement->render();
	}
	
	//************************************************************************************
	/**
	 * @param string $formName
	 * @param NameValuePair[] $widgetsValues
	 * @return bool
	 */
	public function validateForm($formName, $widgetsValues) {
		if (!$this->oFoundElement) throw new IllegalStateException('oFoundElement is null');
		
		$formName = trim($formName);
		if (!$formName) throw new InvalidArgumentException('Empty formName');
		UtilsArray::checkArgument($widgetsValues, 'NameValuePair');
		
		return $this->oFoundElement->validateForm($formName, $widgetsValues);
	}
	
	//************************************************************************************
	/**
	 * @param string $formName
	 * @param NameValuePair[] $widgetsValues
	 * @return bool
	 */
	public function submitForm($formName, $widgetsValues) {
		if (!$this->oFoundElement) throw new IllegalStateException('oFoundElement is null');
		
		$formName = trim($formName);
		if (!$formName) throw new InvalidArgumentException('Empty formName');
		UtilsArray::checkArgument($widgetsValues, 'NameValuePair');
		
		return $this->oFoundElement->submitForm($formName, $widgetsValues);
	}
	
	
}

?>