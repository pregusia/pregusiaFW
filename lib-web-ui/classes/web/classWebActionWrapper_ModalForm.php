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


class WebActionWrapper_ModalForm implements IWebActionWrapper {
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oActionDef
	 * @param string[] $params
	 */
	public function onBegin($oActionDef, &$params) {
		
	}
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oActionDef
	 * @param mixed $returnValue
	 * @return WebResponseBase
	 */
	public function onEnd($oActionDef, $returnValue) {
		if ($returnValue instanceof WebResponseRedirect) {
			return new WebResponseJson(array(
				'status' => 'ok',
				'action' => 'close'	
			));
		}
		
		if ($returnValue instanceof WebResponseJson) {
			return $returnValue;
		}
		
		if ($returnValue instanceof WebResponseTwoLayersSiteLayout) {
			$returnValue->setMainTemplateLocation('lib-web-ui:WebActionWrapper.ModalForm.main');
			$returnValue->setTag('WebActionWrapper_ModalForm', $this);
			
			$oCapturer = new HTTPServerResponseCapturing();
			$returnValue->finish($oCapturer);
			
			$oCapturer->finish();
			
			return new WebResponseJson(array(
				'status' => 'ok',
				'action' => 'render',
				'content' => $oCapturer->getContentString()
			));
		}
				
		if ($returnValue instanceof WebResponseContentAppendable) {
			$oCapturer = new HTTPServerResponseCapturing();
			$returnValue->finish($oCapturer);
			
			$oCapturer->finish();
			
			return new WebResponseJson(array(
				'status' => 'ok',
				'action' => 'render',
				'content' => $oCapturer->getContentString()
			));
		}
		
		throw new IllegalStateException(sprintf('Action %s returned invalid value for wrapper WebActionWrapper_ModalForm', $oActionDef->getFirstName()));
	}
	
	//************************************************************************************
	/**
	 * @param WebActionDefinition $oActionDef
	 * @param Exception $oException
	 * @return WebResponseBase
	 */
	public function onException($oActionDef, $oException) {
		return new WebResponseJson(array(
			'status' => 'error',
			'errorText' => UtilsExceptions::toString($oException)	
		));
	}
	
}


?>