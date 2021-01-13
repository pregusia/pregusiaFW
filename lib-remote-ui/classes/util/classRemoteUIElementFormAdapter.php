<?php

abstract class RemoteUIElementFormAdapter implements IRemoteUIElement {
	
	/**
	 * @var IRemoteUIFormWidget[]
	 */
	protected $widgets = array();
	
	/**
	 * @var UIForm
	 */
	protected $oForm = null;
	
	//************************************************************************************
	/**
	 * @param string[] $pathParts
	 * @return bool
	 */
	public abstract function matches($pathParts);
	
	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param string[] $pathParts
	 * @param mixed $authData
	 */
	public abstract function onBeforeAction($oContext, $pathParts, $authData);

	//************************************************************************************
	/**
	 * @return UIForm
	 */
	protected abstract function createFormInstance();
	
	//************************************************************************************
	/**
	 * @return UIForm
	 */
	protected function getForm() {
		if (!$this->oForm) {
			$oForm = $this->createFormInstance();
			if (!($oForm instanceof UIForm)) throw new IllegalStateException('createFormInstance() returned not UIForm');
			
			$this->oForm = $oForm;
			foreach($oForm->getAllWidgets() as $oWidget) {
				// TODO: fill widgets
			}
		}
		return $this->oForm;
	}
	
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function render() {
		
	}
	
	//************************************************************************************
	/**
	 * @param string $formName
	 * @param NameValuePair[] $widgetsValues
	 * @return bool
	 */
	public function validateForm($formName, $widgetsValues) {
		
	}
	
	//************************************************************************************
	/**
	 * @param string $formName
	 * @param NameValuePair[] $widgetsValues
	 * @return bool
	 */
	public function submitForm($formName, $widgetsValues) {
		
	}
	
}

?>