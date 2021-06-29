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


final class UIController extends WebController {
	
	//************************************************************************************
	/**
	 * @WebAction ui.suggest.search
	 * @param string $source
	 */
	public function webSearch($source) {
		$Items = array();
		$term = $this->getWebRequest()->getString('term');
		
		try {
			$oEnumerable = UtilsEnumerable::unserializeRef($source);
			if ($oEnumerable instanceof IEnumerable) {
				if ($oEnumerable->enumerableUsageType() == IEnumerable::USAGE_SUGGEST) {
					$oEnum = $oEnumerable->enumerableSuggest($term);
					foreach($oEnum->getItems() as $k => $v) {
						$Items[] = array(
							'id' => $k,
							'text' => UtilsString::toString($v),	
						);
					}
				}
				if ($oEnumerable->enumerableUsageType() == IEnumerable::USAGE_SIMPLE) {
					foreach($oEnumerable->enumerableGetAllEnum()->getItems() as $value => $caption) {
						$caption = UtilsString::toString($caption);
						if (stripos($caption, $term) !== false) {
							$Items[] = array(
								'id' => $value,
								'text' => $caption,	
							);
						}
					}
				}
			}
		} catch(Exception $e) {
			
		}
		
		return new WebResponseJson(array(
			'results' => $Items,
			'more' => false,
		));
	}
	
	//************************************************************************************
	/**
	 * @WebAction ui.suggest.idToJSON
	 * @param string $source
	 */
	public function webIdToJSON($source) {
		$id = $this->getWebRequest()->getString('id');
		
		
		try {
			$oEnumerable = UtilsEnumerable::unserializeRef($source);
			if ($oEnumerable) {
				return new WebResponseJson(array(
					'id' => $id,
					'text' => UtilsString::toString($oEnumerable->enumerableToString($id))	
				));
			}
		} catch(Exception $e) {
			
		}

		return new WebResponseJson(array(
			'id' => 0,
			'text' => '[not-found]'
		));
	}
	
	//************************************************************************************
	/**
	 * @return UIAssetsCollection
	 */
	private function getAssets() {
		$oAssets = new UIAssetsCollection();
		
		$oAssets->addCSSLocation('lib-web-ui:css/select2.min.css');
		$oAssets->addCSSLocation('lib-web-ui:css/select2-bootstrap.css');
		$oAssets->addCSSLocation('lib-web-ui:css/checkboxes.css');
		$oAssets->addCSSLocation('lib-web-ui:css/widgets.css');
		
		$oAssets->addJSLocation('lib-web-ui:js/select2.full.min.js');
		$oAssets->addJSLocation('lib-web-ui:js/jquery.numeric.js');
		$oAssets->addJSLocation('lib-web-ui:js/handlebars.min.js');
		$oAssets->addJSLocation('lib-web-ui:js/utils.js');
		$oAssets->addJSLocation('lib-web-ui:js/ui.widget.DecimalInput.js');
		$oAssets->addJSLocation('lib-web-ui:js/ui.widget.IntegerInput.js');
		$oAssets->addJSLocation('lib-web-ui:js/ui.widget.SelectInput.js');
		$oAssets->addJSLocation('lib-web-ui:js/ui.widget.PropertiesMap.js');
		$oAssets->addJSLocation('lib-web-ui:js/ui.widget.SuggestInput.js');
		$oAssets->addJSLocation('lib-web-ui:js/ui.widget.TagsInput.js');
		$oAssets->addJSLocation('lib-web-ui:js/ui.widget.IntCollectionEnumerableInput.js');
		$oAssets->addJSLocation('lib-web-ui:js/ui.widget.StringCollectionEnumerableInput.js');
		
		foreach($this->getUIComponent()->getUIExtensions() as $oExtension) {
			$oExtension->prepareAssets($oAssets);
		}
		
		return $oAssets;
	}
	
	//************************************************************************************
	/**
	 * @WebAction ui.js
	 */
	public function webJS() {
		$content = $this->getAssets()->getJSContent();
		$oResponse = new WebResponseContent('application/javascript', $content);
		$oResponse->addHTTPHeader('Cache-Control', 'public');
		$oResponse->addHTTPHeader('ETag', sprintf('"%s"', md5($content)));
		
		return $oResponse;
	}

	//************************************************************************************
	/**
	 * @WebAction ui.css
	 */
	public function webCSS() {
		$content = $this->getAssets()->getCSSContent();
		$oResponse = new WebResponseContent('text/css', $content);
		$oResponse->addHTTPHeader('Cache-Control', 'public');
		$oResponse->addHTTPHeader('ETag', sprintf('"%s"', md5($content)));
		
		return $oResponse;
	}
	
	//************************************************************************************
	/**
	 * @WebAction ui.dynamic-content
	 * @WebActionWrapper WebActionWrapper_JSON 
	 * @param encarr $ref
	 */
	public function webDynamicContent($ref) {
		$suppliers = CodeBase::getInterface('IWebUIDynamicContentSupplier')->getAllInstances();
		$oSupplier = null;
		
		foreach($suppliers as $e) {
			if ($e->matches($ref)) {
				$oSupplier = $e;
				break;
			}
		}
		
		false && $oSupplier = new IWebUIDynamicContentSupplier();
		
		if (!$oSupplier) throw new ObjectNotFoundException('Could not find matching supplier');
		
		$oSupplier->onInit($this->getApplicationContext(), $this->getHTTPRequest(), $ref);
		
		return new WebResponseJson(array(
			'status' => 'ok',
			'action' => 'render',
			'content' => UtilsWebUI::render($oSupplier)
		));
	}
	
}

?>