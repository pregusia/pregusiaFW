<?php

interface IRemoteUIService extends IRemoteService {
	
	/**
	 * @return string
	 */
	public function render();
	
	/**
	 * @param string $formName
	 * @param NameValuePair[] $widgetsValues
	 * @return bool
	 */
	public function validateForm($formName, $widgetsValues);
	
	/**
	 * @param string $formName
	 * @param NameValuePair[] $widgetsValues
	 * @return bool
	 */
	public function submitForm($formName, $widgetsValues);
	
}

?>