<?php

interface IRemoteUIExtension extends IApplicationComponentExtension {

	/**
	 * @param UIWidget $oWidget
	 * @return string
	 */
	public function onRenderWidget($oWidget);
	
	/**
	 * @param CodeBaseDeclaredClass $oClass
	 * @return bool
	 */
	public function isEnumerableExported($oClass);
	
}

?>