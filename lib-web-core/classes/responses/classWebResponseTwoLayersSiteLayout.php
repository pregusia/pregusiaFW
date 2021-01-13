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


/**
 * 
 * @author pregusia
 * @NeedLibrary lib-templating
 *
 */
class WebResponseTwoLayersSiteLayout extends WebResponseHtml {
	
	use TTagsContainer;
	
	protected $siteTitle = '';
	protected $siteId = '';
	protected $siteClasses = array();
	
	protected $mainTemplateLocation = '';
	protected $contentTemplateLocation = '';
	protected $templateVars = array();
	
	protected $jsURLs = array();
	protected $jsContent = array();
	
	protected $cssURLs = array();
	protected $cssContent = array();
	
	//************************************************************************************
	public function getSiteTitle() { return $this->siteTitle; }
	public function setSiteTitle($v) { $this->siteTitle = $v; }
	
	//************************************************************************************
	public function getSiteId() { return $this->siteId; }
	public function setSiteId($v) { $this->siteId= $v; }

	//************************************************************************************
	public function getSiteClasses() { return $this->siteClasses; }
	public function addSiteClass($c) { $this->siteClasses[] = $c; }

	
	
	//************************************************************************************
	public function getMainTemplateLocation() { return $this->mainTemplateLocation; }
	public function setMainTemplateLocation($v) { $this->mainTemplateLocation = trim($v); }
	
	//************************************************************************************
	public function getContentTemplateLocation() { return $this->contentTemplateLocation; }
	public function setContentTemplateLocation($v) { $this->contentTemplateLocation = trim($v); }

	//************************************************************************************
	public function assignVar($name, $value) {
		$this->templateVars[$name] = $value;
	}
	
	
	
	//************************************************************************************
	public function addStylesheetURL($url) {
		$key = sprintf('css.url.%s', $url);
		if (!$this->getTag($key)) {
			$this->cssURLs[] = $url;
			$this->setTag($key, 1);
		}
	}
	
	//************************************************************************************
	public function addStylesheetContentRaw($content) {
		$this->cssContent[] = $content;
	}
	
	//************************************************************************************
	public function addStylesheetContentLocation($loc) {
		$key = sprintf('css.loc.%s', $loc);
		if (!$this->getTag($key)) {
			$this->cssContent[] = CodeBase::getResource($loc)->contents();
			$this->setTag($key, 1);
		}
	}
	
	//************************************************************************************
	public function addJavaScriptURL($url) {
		$key = sprintf('js.url.%s', $url);
		if (!$this->getTag($key)) {
			$this->jsURLs[] = $url;
			$this->setTag($key, 1);
		}
	}
	
	//************************************************************************************
	public function addJavaScriptContentRaw($content) {
		$this->jsContent[] = $content;
	}
	
	//************************************************************************************
	public function addJavaScriptContentLoc($loc) {
		$key = sprintf('js.loc.%s', $loc);
		if (!$this->getTag($key)) {
			$this->jsContent[] = CodeBase::getResource($loc)->contents();
			$this->setTag($key, 1);
		}
	}
	
	
	//************************************************************************************
	protected function onBeforeRender() {

	}

	//************************************************************************************
	public function __construct($tplLocation='') {
		parent::__construct();
		$this->contentTemplateLocation = $tplLocation;
	}
	
	//************************************************************************************
	private function renderContent() {
		$oTemplatingEngine = ApplicationContext::getCurrent()->getComponent('templating');
		false && $oTemplatingEngine = new TemplatingEngineApplicationComponent();
		
		$this->content = '';
		$this->assignVar('siteId', $this->siteId);
		$this->assignVar('siteClasses', implode(' ',$this->siteClasses));
		$this->assignVar('siteTitle', $this->siteTitle);

		foreach(ApplicationContext::getCurrent()->getComponent('web.core')->getExtensions('IWebCoreExtension') as $oExtension) {
			false && $oExtension = new IWebCoreExtension();
			$oExtension->onBeforeRenderTemplatedResponse($this);
		}
		
		$this->onBeforeRender();
		
		// css
		if (true) {
			$arr = array();
			foreach($this->cssURLs as $url) {
				$arr[] = sprintf('<link type="text/css" href="%s" rel="stylesheet" media="screen" />',$url);
			}
			$this->assignVar('cssURLs', $arr);
			$this->assignVar('cssContent', $this->cssContent);
		}
		
		// js
		if (true) {
			$arr = array();
			foreach($this->jsURLs as $url) {
				$arr[] = sprintf('<script src="%s" type="text/javascript"></script>',$url);
			}
			$this->assignVar('jsURLs', $arr);
			$this->assignVar('jsContent', $this->jsContent);
		}
		
		// rendering
		if ($this->mainTemplateLocation) {
			if ($this->contentTemplateLocation) {
				$content = $oTemplatingEngine->renderTemplateFromLocation($this->contentTemplateLocation, $this->templateVars);
				$this->assignVar('siteBody', $content);
			}
			
			$this->content = $oTemplatingEngine->renderTemplateFromLocation($this->mainTemplateLocation, $this->templateVars);
		} else {
			if ($this->contentTemplateLocation) {
				$content = $oTemplatingEngine->renderTemplateFromLocation($this->contentTemplateLocation, $this->templateVars);
				$this->content = $content . $this->content;
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param IHTTPServerResponse $oHttpResponse
	 */
	public function finish($oHttpResponse) {
		$this->renderContent();
		parent::finish($oHttpResponse);
	}

}

?>