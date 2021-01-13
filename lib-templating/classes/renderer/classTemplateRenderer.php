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


class TemplateRenderer {
	
	/**
	 * @var TemplatingEngineApplicationComponent
	 */
	private $oComponent = null;
	
	private $allowedNativeFunctions = array();
	
	/**
	 * @var Closure[]
	 */
	private $customFunctions = array();
	
	private $variables = array();
	
	/**
	 * @var TemplateRendererState
	 */
	private $oState = null;
	
	//************************************************************************************
	/**
	 * @return TemplatingEngineApplicationComponent
	 */
	public function getComponent() { return $this->oComponent; }
	
	//************************************************************************************
	/**
	 * @throws IllegalStateException
	 * @return TemplateRendererState
	 */
	public function getState() {
		if (!$this->oState) {
			throw new IllegalStateException('TemplateRenderer is in invalid state');
		}
		return $this->oState;
	}
	
	//************************************************************************************
	public function __construct($oComponent) {
		if (!($oComponent instanceof TemplatingEngineApplicationComponent)) throw new InvalidArgumentException('oComponent is not TemplatingEngineApplicationComponent');
		$this->oComponent = $oComponent;
		$this->variables = array();
	}
	
	//************************************************************************************
	public function assignVars($arr) {
		if (is_array($arr)) {
			foreach($arr as $k => $v) {
				$this->variables[$k] = $v;
			}
		}
	}
	
	//************************************************************************************
	public function assignVar($name, $value) {
		$this->variables[$name] = $value;
	}
	
	//************************************************************************************
	/**
	 * Registers custom function
	 * @param string $name
	 * @param Closure $func
	 */
	public function registerFunction($name, $func) {
		if (!$name) throw new InvalidArgumentException('name is empty');
		if (!($func instanceof Closure)) throw new InvalidArgumentException('func is not Closure');
		$this->customFunctions[$name] = $func;
	}
	
	//************************************************************************************
	public function allowNativeFunction($name) {
		if (!$name) throw new InvalidArgumentException('name is empty');
		$this->allowedNativeFunctions[] = $name;
	}

	//************************************************************************************
	public function callFunc() {
		$args = func_get_args();
		$name = array_shift($args);

		if (in_array($name, $this->allowedNativeFunctions)) {
			return call_user_func_array($name, $args);
		}
		
		if ($func = $this->customFunctions[$name]) {
			array_unshift($args, $this);
			return call_user_func_array($func, $args);
		} else {
			throw new TemplateException($this->getState()->getFileName(), $this->getState()->getLineNr(), sprintf('Function %s not found', $name));
		}
	}
	
	//************************************************************************************
	public function processRawFile($__tplFilePath) {
		if (!file_exists($__tplFilePath)) {
			throw new TemplateException('', 0, sprintf('Template file %s not exists', $__tplFilePath));
		}
		if ($this->oState) {
			throw new IllegalStateException('Invalid renderer state');
		}
		
		$this->oState = new TemplateRendererState(basename($__tplFilePath));
		$AllTemplateVars = $this->variables;
		extract($this->variables);
		include($__tplFilePath);
		
		$res = $this->getState()->getOut();
		$this->oState = null;
		return $res;
	}
	
	//************************************************************************************
	public function processRawMemory($__tplCode) {
		if ($this->oState) {
			throw new IllegalStateException('Invalid renderer state');
		}
		
		$this->oState = new TemplateRendererState('<memory>');
		$AllTemplateVars = $this->variables;
		extract($this->variables);
		eval($__tplCode);
		
		$res = $this->getState()->getOut();
		$this->oState = null;
		return $res;
	}
	
}

?>