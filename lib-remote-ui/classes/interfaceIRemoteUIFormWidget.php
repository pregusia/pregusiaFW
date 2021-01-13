<?php

interface IRemoteUIFormWidget {
	
	/**
	 * @return string
	 */
	public function renderRemoteWidget();
	
	/**
	 * @param mixed $value
	 */
	public function readRemoteWidgetValue($value);
	
}

?>