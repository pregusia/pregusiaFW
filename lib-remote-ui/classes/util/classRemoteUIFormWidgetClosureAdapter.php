<?php

final class RemoteUIFormWidgetClosureAdapter implements IRemoteUIFormWidget {
	
	private $renderFunc = null;
	private $readFunc = null;
	
	
	//************************************************************************************
	public function __construct($renderFunc, $readFunc) {
		$this->renderFunc = $renderFunc;
		$this->readFunc = $readFunc;
		
		if (!($renderFunc instanceof Closure)) throw new InvalidArgumentException('renderFunc is not Closure');
		if (!($readFunc instanceof Closure)) throw new InvalidArgumentException('readFunc is not Closure');
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function renderRemoteWidget() {
		$func = $this->renderFunc;
		return $func();
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 */
	public function readRemoteWidgetValue($value) {
		$func = $this->readFunc;
		return $func($value);
	}
	
}

?>