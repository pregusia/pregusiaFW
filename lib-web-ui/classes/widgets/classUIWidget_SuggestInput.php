<?php
/**
 *  This file is part of PREGUSIA-PHP-FRAMEWORK.
 *  PREGUSIA-PHP-FRAMEWORK is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation; either version 2.1 of the License, or
 *  (at your option) any later version.
 *  
 *  PREGUSIA-PHP-FRAMEWORK is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.
 *  
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with PREGUSIA-PHP-FRAMEWORK; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *  
 *  @author pregusia
 *
 */


class UIWidget_SuggestInput extends UIWidgetWithValue {

	/**
	 * @var IEnumerable
	 */
	private $oValuesSource = null;
	
	//************************************************************************************
	/**
	 * @return IEnumerable
	 */
	public function getValuesSource() { return $this->oValuesSource; }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $caption
	 * @param IEnumerable $oValuesSource
	 */
	public function __construct($name, $caption, $oValuesSource) {
		if (!($oValuesSource instanceof IEnumerable)) throw new InvalidArgumentException('oValuesSource is not IEnumerable');
		if ($oValuesSource->enumerableUsageType() != IEnumerable::USAGE_SUGGEST) throw new InvalidArgumentException('Given enumerable usage is not USAGE_SUGGEST');
		
		$this->oValuesSource = $oValuesSource;
		
		parent::__construct($name, $caption);
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 */
	protected function onRead($oRequest) {
		$this->value = $oRequest->getString($this->getName());
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'enumerableRef': return UtilsEnumerable::serializeRef($this->oValuesSource);
			case 'valueText': return $this->oValuesSource->enumerableToString($this->value);
			default: return parent::tplRender($key, $oContext);
		}
	}
	
}

?>