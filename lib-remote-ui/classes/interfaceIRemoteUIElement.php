<?php

interface IRemoteUIElement extends IRemoteUIService {
	
	/**
	 * @param string[] $pathParts
	 * @return bool
	 */
	public function matches($pathParts);
	
	/**
	 * @param ApplicationContext $oContext
	 * @param string[] $pathParts
	 * @param mixed $authData
	 */
	public function onBeforeAction($oContext, $pathParts, $authData);
	
}

?>