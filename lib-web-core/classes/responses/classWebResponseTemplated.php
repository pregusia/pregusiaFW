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
class WebResponseTemplated extends WebResponseBase {
	
	private $templateLocation = '';
	private $templateVars = array();
	private $contentType = '';
	
	//************************************************************************************
	public function getTemplateLocation() { return $this->templateLocation; }
	public function setTemplateLocation($v) { $this->templateLocation = trim($v); }
	
	//************************************************************************************
	public function getContentType() { return $this->contentType; }
	public function setContentType($v) { $this->contentType = $v; }
	
	//************************************************************************************
	public function assignVar($name, $value) {
		$this->templateVars[$name] = $value;
	}
	
	//************************************************************************************
	public function __construct($tplLocation) {
		parent::__construct();
		$this->templateLocation = $tplLocation;
		$this->contentType = 'text/html; charset=UTF-8';
	}
	
	//************************************************************************************
	/**
	 * @param IHTTPServerResponse $oHttpResponse
	 */
	public function finish($oHttpResponse) {
		$oComponent = ApplicationContext::getCurrent()->getComponent('templating');
		false && $oComponent = new TemplatingEngineApplicationComponent();
		
		foreach(ApplicationContext::getCurrent()->getComponent('web.core')->getExtensions('IWebCoreExtension') as $oExtension) {
			false && $oExtension = new IWebCoreExtension();
			$oExtension->onBeforeRenderTemplatedResponse($this);
		}
		
		$content = $oComponent->renderTemplateFromLocation($this->templateLocation, $this->templateVars);
		
		$oHttpResponse->setContentType($this->contentType);
		$oHttpResponse->pushOutputFunction(function() use ($content){
			print($content);
		});
	}

}

?>