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

class ContentRendererApplicationComponent extends ApplicationComponent {

	const STAGE = 35;

	/**
	 * @var IContentRenderer[]
	 */
	private $renderers = array();
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() {
		return 'content-renderer';
	}
	
	//************************************************************************************
	/**
	 * @return int[]
	 */
	public function getStages() {
		return array(self::STAGE);
	}
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onInit($stage) {
		
	}

	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onProcess($stage) {
		if ($stage == self::STAGE) {
			
			foreach($this->getConfig()->getRootArray() as $rendererName => $config) {
				$oConfig = $this->getConfig()->getSubConfig($rendererName);
				
				$oClass = CodeBase::getClass($oConfig->getValue('className'), false);
				if ($oClass) {
					if (!$oClass->isImplementing('IContentRenderer')) {
						throw new ConfigEntryInvalidValueException('',
							sprintf('Renderer "%s" class "%s" not implements IContentRenderer',
								$rendererName,
								$oClass->getName()
							)
						);
					}
					
					$oRenderer = $oClass->getInstance();
					false && $oRenderer = new IContentRenderer();
					
					$oRenderer->onInit($this->getApplicationContext(), $oConfig);
					$this->renderers[$rendererName] = $oRenderer;
				}
			}
			
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $rendererName
	 * @return IContentRenderer
	 */
	public function getRenderer($rendererName) {
		$oRenderer = $this->renderers[$rendererName];
		if (!$oRenderer) throw new ContentRendererException(sprintf('Content renderer "%s" not found', $rendererName));
		return $oRenderer;
	}
	
	//************************************************************************************
	/**
	 * @param string $rendererName
	 * @param string $content
	 * @param array $config
	 * @return string
	 */
	public function renderRaw($rendererName, $content, $config=array()) {
		return $this->getRenderer($rendererName)->render($content, $config);
	}
	
	//************************************************************************************
	/**
	 * @param string $rendererName
	 * @param string $tplLoc
	 * @param array $vars
	 * @param array $config
	 * @return string
	 */
	public function renderTemplated($rendererName, $tplLoc, $vars, $config=array()) {
		CodeBase::ensureLibrary('lib-templating', 'lib-content-renderer');
		
		$oRenderer = $this->getRenderer($rendererName);
		
		$oEngine = $this->getApplicationContext()->getComponent('templating');
		false && $oEngine = new TemplatingEngineApplicationComponent();
		
		$content = $oEngine->renderTemplateFromLocation($tplLoc, $vars);
		return $oRenderer->render($content, $config);
	}
	
	
}

?>