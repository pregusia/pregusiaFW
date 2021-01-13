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


class TemplatingEngineApplicationComponent extends ApplicationComponent {
	
	/**
	 * @var TemplateRenderer[]
	 */
	private $renderersStack = array();
	
	/**
	 * @var CacheMechanism_FileSystem
	 */
	private $oCache = null;
	
	//************************************************************************************
	public function getName() { return 'templating'; }
	public function getStages() { return array(20); }
	
	//************************************************************************************
	/**
	 * @return ITemplatingEngineExtension[]
	 */
	public function getEngineExtensions() { return $this->getExtensions('ITemplatingEngineExtension'); }
	
	//************************************************************************************
	/**
	 * @return ITemplateRenderableMod[]
	 */
	public function getRenderableMods() { return $this->getExtensions('ITemplateRenderableMod'); }
	
	//************************************************************************************
	/**
	 * @return CacheMechanism_FileSystem
	 */
	public function getCache() { return $this->oCache; }
	
	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param int $stage
	 */
	public function onInit($stage) {
		CodeBase::ensureLibrary('lib-cache', 'lib-templating');
		CodeBase::ensureLibrary('lib-utils', 'lib-templating');
		
		$oCache = $this->getApplicationContext()->getComponent('cache')->getCache('templating');
		if (!($oCache instanceof CacheMechanism_FileSystem)) throw new IllegalStateException('Cache "templating" is not CacheMechanism_FileSystem'); 
		$this->oCache = $oCache; 
		
		foreach($this->getEngineExtensions() as $oExtension) {
			$oExtension->onAdaptComponent($this);
		}
	}

	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param int $stage
	 */
	public function onProcess($stage) {
		
	}
	
	//************************************************************************************
	/**
	 * @return TemplateRenderer
	 */
	public function getCurrentRenderer() {
		if ($this->renderersStack) {
			return UtilsArray::getLast($this->renderersStack);
		}
		return null;
	}
	
	//************************************************************************************
	private function pushRenderer($oRenderer) {
		if (!($oRenderer instanceof TemplateRenderer)) throw new InvalidArgumentException('oRenderer is not TemplateRenderer');
		$this->renderersStack[] = $oRenderer;
	}
	
	//************************************************************************************
	/**
	 * @throws IllegalStateException
	 * @return TemplateRenderer
	 */
	private function popRenderer() {
		if (!$this->renderersStack) throw new IllegalStateException('Renderers stack is empty');
		return array_pop($this->renderersStack);
	}
	
	//************************************************************************************
	/**
	 * @return TemplateRenderer
	 */
	private function createRenderer() {
		$oRenderer = new TemplateRenderer($this);
		$this->pushRenderer($oRenderer);
		
		foreach($this->getEngineExtensions() as $oExtension) {
			$oExtension->onAdaptRenderer($oRenderer);
		}
		
		$oRenderer->allowNativeFunction('sprintf');
		$oRenderer->allowNativeFunction('round');
		$oRenderer->allowNativeFunction('number_format');
		$oRenderer->allowNativeFunction('date');
		$oRenderer->allowNativeFunction('htmlspecialchars');
		$oRenderer->allowNativeFunction('count');
		$oRenderer->allowNativeFunction('array_keys');
		$oRenderer->allowNativeFunction('array_reverse');
		$oRenderer->allowNativeFunction('range');
		$oRenderer->allowNativeFunction('in_array');
		$oRenderer->allowNativeFunction('strtoupper');
		$oRenderer->allowNativeFunction('strtolower');
		$oRenderer->allowNativeFunction('trim');
		$oRenderer->allowNativeFunction('nl2br');
		$oRenderer->allowNativeFunction('floor');
		$oRenderer->allowNativeFunction('ceil');
		// TODO: others?

		$oRenderer->registerFunction('arrayColumnize', function($r,$arr,$num){
			return UtilsArray::columnize($arr, $num);
		});
		$oRenderer->registerFunction('arrayFirst', function($r,$arr){
			return UtilsArray::getFirst($arr);
		});
		$oRenderer->registerFunction('arrayLast', function($r,$arr){
			return UtilsArray::getLast($arr);
		});
		$oRenderer->registerFunction('arrayRandom', function($r,$arr){
			return UtilsArray::getRandom($arr);
		});
		$oRenderer->registerFunction('startsWith', function($r,$str,$test){
			return UtilsString::startsWith($str, $test);
		});		
		$oRenderer->registerFunction('endsWith', function($r,$str,$test){
			return UtilsString::endsWith($str, $test);
		});
		$oRenderer->registerFunction('shorten', function($r,$str,$length){
			return UtilsString::shorten($str, $length);
		});		
		
		return $oRenderer;
	}
	
	//************************************************************************************
	private static function genTemplatePaths($tplName) {
		$arr = explode('.', $tplName);
		$res = array();
		
		if (count($arr) == 1) {
			$res[] = sprintf('./templates/%s.tpl', $arr[0]);
		}
		if (count($arr) > 1) {
			$res[] = sprintf('./templates/%s.tpl', implode('.',$arr));
			$res[] = sprintf('./templates/%s/%s.tpl', $arr[0], implode('.',array_slice($arr, 1)));
		}
		if (count($arr) > 2) {
			$res[] = sprintf('./templates/%s.tpl', implode('.',$arr));
			$res[] = sprintf('./templates/%s/%s.tpl', $arr[0], implode('.',array_slice($arr, 1)));
			$res[] = sprintf('./templates/%s/%s/%s.tpl', $arr[0], $arr[1], implode('.',array_slice($arr, 2)));
		}
		if (count($arr) > 3) {
			$res[] = sprintf('./templates/%s.tpl', implode('.',$arr));
			$res[] = sprintf('./templates/%s/%s.tpl', $arr[0], implode('.',array_slice($arr, 1)));
			$res[] = sprintf('./templates/%s/%s/%s.tpl', $arr[0], $arr[1], implode('.',array_slice($arr, 2)));
			$res[] = sprintf('./templates/%s/%s/%s/%s.tpl', $arr[0], $arr[1], $arr[2], implode('.',array_slice($arr, 3)));
		}
		return $res;
	}
	
	//************************************************************************************
	private function cacheTplLocation($location) {
		list($libName,$tplName) = explode(':',$location,2);
		$paths = self::genTemplatePaths($tplName);
		$oResource = null;

		foreach($paths as $path) {
			$oTmpResource = CodeBase::getResource(sprintf('%s:%s', $libName, $path), false);
			if ($oTmpResource->exists()) {
				$oResource = $oTmpResource;
				break;
			}
		}
		if (!$oResource) {
			throw new IOException(sprintf('Could not find template %s in lib %s', $tplName, $libName));
		}
		
		$cacheKey = sprintf('res-%s-%d-%d.php',
			str_replace(array('/','-','.','$','#','@','!'), '', $oResource->realPath()),
			$oResource->size(),
			$oResource->mtime()
		);
		$genFunc = function() use ($oResource) {
			$code = TemplateCompiler::compile($oResource->contents());
			return '<?php ' . $code . ' ?>';
		};

		return $this->getCache()->getPath($cacheKey, 0, $genFunc);
	}
	
	//************************************************************************************
	public function renderTemplateFromLocation($location, $vars) {
		$location = trim($location);
		if (!$location) return '';
		
		try {
			$oRenderer = $this->createRenderer();
			$oRenderer->assignVars($vars);
			return $oRenderer->processRawFile($this->cacheTplLocation($location));
			
		} catch(TemplateException $e) {
			$e->setLocation($location);
			throw $e;
		} finally {
			$this->popRenderer();
		}
	}
	
	//************************************************************************************
	public function renderTemplateFromMemory($tplContent, $vars) {
		$tplContent = trim($tplContent);
		if (!$tplContent) return '';
		
		try {
			$code = TemplateCompiler::compile($tplContent);
			
			$oRenderer = $this->createRenderer();
			$oRenderer->assignVars($vars);
			return $oRenderer->processRawMemory($code);
			
		} catch(TemplateException $e) {
			$e->setLocation('<memory>');
			throw $e;
		} finally {
			$this->popRenderer();
		}
	}
	
}

?>