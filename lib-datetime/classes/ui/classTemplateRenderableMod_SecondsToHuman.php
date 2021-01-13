<?php

class TemplateRenderableMod_SecondsToHuman implements ITemplateRenderableMod {
	
	//************************************************************************************
	/**
	 * @return int
	 */
	public function getPriority() {
		return 10;
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationComponent $oComponent
	 */
	public function onInit($oComponent) {
		
	}	
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() {
		return 'secondsToHuman';
	}
	
	//************************************************************************************
	/**
	 * @param TemplateRenderableProxyContext $oContext
	 * @param mixed $value
	 * @param array $params
	 */
	public function apply($oContext, $value, $params) {
		$value = intval($value);
		
		$days = floor($value / 86400);
		$value = $value % 86400;
		
		$hours = floor($value / 3600);
		$value = $value % 3600;
		
		$minutes = floor($value / 60);
		$seconds = $value % 60;
		
		$arr = array();
		if ($days > 0) $arr[] = sprintf('%dd', $days);
		if ($hours > 0) $arr[] = sprintf('%dh', $hours);
		if ($minutes > 0) $arr[] = sprintf('%dmin', $minutes);
		if ($seconds > 0) $arr[] = sprintf('%dsec', $seconds);
		
		return implode(' ',$arr);
	}
	
}

?>