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


class TemplateRenderableProxy implements ArrayAccess {
	
	private $func = null;
	private $supplier = null;
	private $additional = array();
	private $argument = null;
	
	/**
	 * @var ReflectionMethod
	 */
	private $methods = array();
	
	//************************************************************************************
	/**
	 * @return ITemplateRenderableSupplier
	 */
	public function getSupplier() { return $this->supplier; }
	
	//************************************************************************************
	public function getArgument() { return $this->argument; }
	
	//************************************************************************************
	/**
	 * @param ITemplateRenderableSupplier|Closure $arg
	 */
	public function __construct($arg, $additional=array()) {
		$this->additional = $additional;
		$this->argument = $arg;
		$this->supplier = null;
		
		if ($arg === null) {
			$this->func = function($key,$ctx) use($arg) { return ''; };
			return;
		}
		elseif ($arg instanceof Closure) {
			$this->func = $arg;
			return;
		}
		elseif (is_array($arg)) {
			$this->func = function($key,$ctx) use($arg) { return $arg[$key]; };
			return;
		}
		elseif (is_object($arg)) {
			$oClass = new ReflectionClass($arg);
			
			if ($arg instanceof ITemplateRenderableSupplier) {
				$this->supplier = $arg;
				$this->func = function($key,$ctx) use($arg) { return $arg->tplRender($key, $ctx); };
			}
			elseif ($oClass->hasMethod('tplRender')) {
				$oMethod = $oClass->getMethod('tplRender');
				$oMethod->setAccessible(true);
				
				$this->func = function($key,$ctx) use($oMethod, $arg) {
					return $oMethod->invokeArgs($arg, array($key, $ctx));
				};
			}
			
			foreach($oClass->getMethods() as $oMethod) {
				false && $oMethod = new ReflectionMethod();
				$oAnnotations = CodeBaseAnnotationsCollection::ParseDocComment($oMethod->getDocComment());
				if ($oAnno = $oAnnotations->getFirst('TemplateRenderable')) {
					if ($oAnno->getParam()) {
						$oMethod->setAccessible(true);
						$this->methods[$oAnno->getParam()] = array($oMethod, $oAnnotations);
					}
				}
			}
			
			return;
		}
		
		throw new InvalidArgumentException('arg is invalid');
	}
	
	//************************************************************************************
	private function call($key,$ctx) {
		if ($arr = $this->methods[$key]) {
			list($oMethod, $oAnnotations) = $arr;
			
			$res = $oMethod->invoke($this->argument);
			if ($res) {
				if ($oAnno = $oAnnotations->getFirst('TemplateRenderableWrap')) {
					if (is_array($res)) {
						$res = TemplateRenderableProxy::wrap($res);
					} else {
						$res = new TemplateRenderableProxy($res);
					}
				}
				return $res;
			}
		}
		
		if ($this->func instanceof Closure) {
			$f = $this->func;
			return $f($key,$ctx);
		} else {
			return '';
		}
	}
	
	//************************************************************************************
	public function offsetExists($offset) {
		return true;
	}
	
	//************************************************************************************
	private static function parseMods($mods) {
		$mods = trim($mods);
		if (!$mods) return array();
		
		$aMods = array();
		$matches = array();
		preg_match_all('/[A-Za-z0-9\.]+(\([A-Za-z0-9\,\.]+\))?/',$mods,$matches);
		
		foreach($matches[0] as $m) {
			if (strpos($m, '(') !== false) {
				$aMods[] = array(
					'name' => strstr($m,'(',true),
					'params' => explode(',',trim(strstr($m,'('),'()'))
				);
			} else {
				$aMods[] = array(
					'name' => $m,
					'params' => array()
				);
			}
		}
		
		return $aMods;
	}

	//************************************************************************************
	public function offsetGet($offset) {
		$oComponent = ApplicationContext::getCurrent()->getComponent('templating');
		false && $oComponent = new TemplatingEngineApplicationComponent();
		
		$oRenderer = $oComponent->getCurrentRenderer();
		if (!$oRenderer) throw new IllegalStateException('Could not use TemplateRenderableProxy in not TemplateRenderer context');
		
		if ($offset == ':argClass') {
			if (is_object($this->argument)) {
				return get_class($this->argument);
			} else {
				return '';
			}
		}
		
		
		$offset = trim($offset);
		if (!$offset) return '';
		list($key,$mods) = explode(':',$offset,2);
		$aMods = self::parseMods($mods);
		
		// creating context
		$oContext = new TemplateRenderableProxyContext($oComponent, $oRenderer);
		foreach($aMods as $mod) {
			$oContext->setTag('mod.' . $mod['name'], 1);
		}

		// processing
		$value = null;
		if (isset($this->additional[$key])) {
			$value = $this->additional[$key];
		} else {
			$value = $this->call($key, $oContext);
		}
		
		foreach($aMods as $mod) {
			$oMod = null;
			foreach($oComponent->getRenderableMods() as $v) {
				if ($v->getName() == $mod['name']) {
					$oMod = $v;
					break;
				}
			}
			
			if ($oMod) {
				$value = $oMod->apply($oContext, $value, $mod['params']);
			}
		}
		
		if ($value && ($value instanceof ComplexString)) {
			$value = $value->render($oContext);
		}
		
		return $value;
	}

	//************************************************************************************
	public function offsetSet($offset,$value) {
		throw new UnsupportedOperationException();
	}

	//************************************************************************************
	public function offsetUnset($offset) {
		throw new UnsupportedOperationException();
	}

	//************************************************************************************
	public function __toString() {
		return 'TemplateRenderableProxy';
	}	
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return TemplateRenderableProxy[]
	 */
	public static function wrap($arr) {
		if (is_array($arr) || ($arr instanceof Traversable)) {
			$res = array();
			foreach($arr as $k => $v) {
				$res[$k] = new TemplateRenderableProxy($v);
			}
			return $res;
		} else {
			return array();
		}
	}
	
}

?>