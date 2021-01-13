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


abstract class UIForm implements ITemplateRenderableSupplier, IValidatable {
	
	/**
	 * @var WebController
	 */
	private $oController = null;
	
	/**
	 * @var UIWidgetsGroup[]
	 */
	private $widgetsGroups = array();
	
	/**
	 * @var ValidationErrorsCollection
	 */
	private $oErrors = null;
	
	/**
	 * @var string
	 */
	private $backURL = '';
	
	private $tabIndex = 1;
	
	/**
	 * @var ReflectionMethod[]
	 */
	private $ajaxMethods = array();

	//************************************************************************************
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext() {
		if ($this->getWebController()) {
			return $this->getWebController()->getApplicationContext();
		} else {
			return ApplicationContext::getCurrent();
		}
		
	}
	
	//************************************************************************************
	/**
	 * @return WebController
	 */
	public function getWebController() {
		return $this->oController;
	}
	
	//************************************************************************************
	/**
	 * @return WebRequest
	 */
	public function getWebRequest() {
		return $this->getWebController() ? $this->getWebController()->getWebRequest() : null;
	}
	
	//************************************************************************************
	/**
	 * @return WebDispatcher
	 */
	public function getWebDispatcher() {
		return $this->getWebController() ? $this->getWebController()->getDispatcher() : null;
	}
	
	//************************************************************************************
	/**
	 * @return WebApplicationComponent
	 */
	public function getWebComponent() {
		return $this->getWebDispatcher() ? $this->getWebDispatcher()->getComponent() : null;
	}	
	
	//************************************************************************************
	/**
	 * @return WebUIApplicationComponent
	 */
	public function getUIComponent() {
		return $this->getApplicationContext()->getComponent('web.ui');
	}
	
	//************************************************************************************
	public function __construct($oController) {
		$this->oErrors = new ValidationErrorsCollection();
		$this->widgetsGroups['default'] = new UIWidgetsGroup('default', 'default');
		
		if ($oController) {
			if (!($oController instanceof WebController)) throw new InvalidArgumentException('oController is not WebController');
			$this->oController = $oController;
				
			if ($oController->getHTTPRequest()->getMethod() == HTTPMethod::POST) {
				$this->backURL = $oController->getHTTPRequest()->getPOSTParameter('back');
			} else {
				$this->backURL = $oController->getHTTPRequest()->getReferer();
			}
			
		} else {
			$this->oController = null;
			$this->backURL = null;
		}
		
		
		$oClass = new ReflectionClass($this);
		foreach($oClass->getMethods() as $oMethod) {
			false && $oMethod = new ReflectionMethod();
			$oAnnotations = CodeBaseAnnotationsCollection::ParseDocComment($oMethod->getDocComment());
			if ($oAnnotations->has('AjaxMethod')) {
				$name = trim($oAnnotations->getFirst('AjaxMethod')->getParam());
				if ($name) {
					$this->ajaxMethods[$name] = $oMethod;
				}
			}
		}
		
	}
	
	//************************************************************************************
	/**
	 * @param string $groupName
	 * @return UIWidgetsGroup
	 */
	public function getWidgetsGroup($groupName) {
		return $this->widgetsGroups[$groupName];
	}
	
	//************************************************************************************
	/**
	 * @return UIWidgetsGroup[]
	 */
	public function getWidgetsGroups() {
		return $this->widgetsGroups;
	}
	
	//************************************************************************************
	/**
	 * @param UIWidgetsGroup $oGroup
	 */
	public function addWidgetsGroup($oGroup) {
		if (!($oGroup instanceof UIWidgetsGroup)) throw new InvalidArgumentException('oGroup is not UIWidgetsGroup');
		if ($this->widgetsGroups[$oGroup->getName()]) throw new IllegalStateException(sprintf('group with name "%s" already exists', $oGroup->getName()));
		$this->widgetsGroups[$oGroup->getName()] = $oGroup;
	}
	
	//************************************************************************************
	/**
	 * @param UIWidget $oWidget
	 * @param string $groupName
	 * @return UIWidget
	 */
	public function addWidget($oWidget, $groupName='') {
		if (!($oWidget instanceof UIWidget)) throw new InvalidArgumentException('oWidget is not UIWidget');
		
		$oGroup = $this->getWidgetsGroup('default');
		if ($groupName) {
			if (!$this->widgetsGroups[$groupName]) $this->widgetsGroups[$groupName] = new UIWidgetsGroup($groupName, $groupName);
			$oGroup = $this->getWidgetsGroup($groupName);
		}
		
		$oGroup->addWidget($oWidget);
		
		if (!$oWidget->getIndex()) {
			$oWidget->setIndex($this->tabIndex);
			$this->tabIndex += 1;
		}
		$oWidget->onAddedToForm($this);
		return $oWidget;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $caption
	 * @return UIWidget
	 */
	public function addButton($name, $caption) {
		return $this->addWidget(new UIWidget_Button($name, $caption));
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $groupName
	 * @return UIWidget
	 */
	public function getWidget($name, $groupName='') {
		if ($groupName) {
			$oGroup = $this->getWidgetsGroup($groupName);
			if ($oGroup) {
				return $oGroup->getWidget($name);
			} else {
				return null;
			}			
		} else {
			$arr = explode('/', $name);
			if (count($arr) == 2) {
				$groupName = $arr[0];
				$name = $arr[1];
			}
			
			if (!$groupName) $groupName = 'default';
			
			$oGroup = $this->getWidgetsGroup($groupName);
			if ($oGroup) {
				return $oGroup->getWidget($name);
			} else {
				return null;
			}				
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $groupName
	 * @return UIWidget[]
	 */
	public function getWidgets($groupName='') {
		if (!$groupName) $groupName = 'default';
		
		$oGroup = $this->getWidgetsGroup($groupName);
		if ($oGroup) {
			return $oGroup->getWidgets();
		} else {
			return array();
		}
	}
	
	//************************************************************************************
	/**
	 * @return UIWidget[]
	 */
	public function getAllWidgets() {
		$arr = array();
		foreach($this->widgetsGroups as $oGroup) {
			foreach($oGroup->getWidgets() as $oWidget) {
				$arr[] = $oWidget;
			}
		}
		return $arr;
	}
	
	//************************************************************************************
	public function addError($arg,$groupName='') {
		if ($arg instanceof ValidationError) {
			if ($oWidget = $this->getWidget($arg->getFieldName(), $groupName)) {
				$arg->setFieldCaption($oWidget->getCaption());
				
				if ($oWidget instanceof UIWidgetWithValue) {
					$oWidget->getErrors()->add($arg);
				}
			} else {
				$this->oErrors->add($arg);
			}
		}
		elseif ($arg instanceof Exception) {
			$nr = count($this->getAllErrors()) + 1;
			$oError = new ValidationError(sprintf('exception/e%d', $nr), 1, UtilsExceptions::toString($arg));
			$this->oErrors->add($oError);
		} else {
			$this->oErrors->add($arg);
		}
	}
	
	//************************************************************************************
	/**
	 * @param ValidationErrorsCollection $oErrors
	 */
	protected function processErrors($oErrors) {
		if (!($oErrors instanceof ValidationErrorsCollection)) throw new InvalidArgumentException('oErrors is not ValidationErrorsCollection');
		foreach($oErrors->getErrors() as $oError) {
			if ($oWidget = $this->getWidget($oError->getFieldName())) {
				$oError->setFieldCaption($oWidget->getCaption());
				
				if ($oWidget instanceof UIWidgetWithValue) {
					$oWidget->getErrors()->add($oError);
					continue;
				}
			}
			
			$this->oErrors->add($oError);
		}
	}
	
	//************************************************************************************
	/**
	 * @param ValidationProcess $oProcess
	 */
	public function validationFill($oProcess) {
		foreach($this->getAllWidgets() as $oWidget) {
			if ($oWidget instanceof IValidatable) {
				$oWidget->validationFill($oProcess);
			}
		}
	}
	
	//************************************************************************************
	public function clearErrors() {
		$this->oErrors->clear();
		foreach($this->getAllWidgets() as $oWidget) {
			if ($oWidget instanceof IUIValidatable) {
				$oWidget->getErrors()->clear();
			}
		}
	}
	
	//************************************************************************************
	public function hasAnyError() {
		if ($this->oErrors->hasAny()) return true;
		foreach($this->getAllWidgets() as $oWidget) {
			if ($oWidget instanceof IUIValidatable) {
				if ($oWidget->getErrors()->hasAny()) return true;
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @return ValidationError[]
	 */
	public function getAllErrors() {
		$arr = array();
		foreach($this->oErrors as $oError) $arr[] = $oError;
		foreach($this->getAllWidgets() as $oWidget) {
			if ($oWidget instanceof IUIValidatable) {
				foreach($oWidget->getErrors() as $oError) {
					$arr[] = $oError;
				}
			}
		}
		return $arr;		
	}
	
	//************************************************************************************
	/**
	 * @param string $filter
	 * @return ValidationError[]
	 */
	public function getFilteredErrors($filter) {
		$filter = trim($filter);
		if (!$filter) return array();
		
		$arr = array();
		
		foreach(explode(',',$filter) as $f) {
			$f = trim($f);
			if (!$f) continue;
			
			if ($f == '*') {
				foreach($this->getAllErrors() as $oError) {
					$arr[] = $oError;
				}
				continue;
			}
			if ($f == ':unnamed:') {
				foreach($this->getAllErrors() as $oError) {
					if (!$oError->getFieldName()) {
						$arr[] = $oError;
					}
				}
				continue;
			}
			if ($f == ':uncaptioned:') {
				foreach($this->getAllErrors() as $oError) {
					if (!$oError->getFieldCaption()) {
						$arr[] = $oError;
					}
				}
				continue;				
			}
			
			if (strpos($f, '/') !== false) {
				list($groupName, $selector) = explode('/',$f);
				if ($groupName && $selector) {
					if ($selector == '*') {
						foreach($this->getWidgets($groupName) as $oWidget) {
							if ($oWidget instanceof IUIValidatable) {
								foreach($oWidget->getErrors() as $oError) {
									$arr[] = $oError;
								}
							}
						}
						foreach($this->oErrors as $oError) {
							if (UtilsString::startsWith($oError->getFieldName(), sprintf('%s/', $groupName))) {
								$arr[] = $oError;
							}
						}
					} else {
						$oWidget = $this->getWidget($selector, $groupName);
						if ($oWidget instanceof IUIValidatable) {
							foreach($oWidget->getErrors() as $oError) {
								$arr[] = $oError;
							}							
						}
						foreach($this->oErrors as $oError) {
							if ($oError->getFieldName() == $f) {
								$arr[] = $oError;
							}
						}
					}
				}
				continue;
			}
			
			// teraz $f moze byc albo nazwa widgeta, albo nazwa fieldName w oErrors
			if ($oWidget = $this->getWidget($f)) {
				if ($oWidget instanceof IUIValidatable) {
					foreach($oWidget->getErrors() as $oError) {
						$arr[] = $oError;
					}							
				}
				continue;
			}
			
			foreach($this->oErrors->getErrors() as $oError) {
				if ($oError->getFieldName() == $f) {
					$arr[] = $oError;
				}
			}
		}
		
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param WebRequest $oRequest
	 * @return WebResponseBase
	 */
	public function doProcess() {
		$this->clearErrors();
		
		if ($this->getWebRequest()) {
			foreach($this->getAllWidgets() as $oWidget) {
				if ($oWidget instanceof IUIReadable) {
					$oWidget->readFromWebRequest($this->getWebRequest());
				}
			}		
			
			if ($this->getWebRequest()->getString('ajaxMethod')) {
				$name = $this->getWebRequest()->getString('ajaxMethod');
				$oMethod = $this->ajaxMethods[$name];
				if ($oMethod) {
					try {
						$res = $oMethod->invoke($this);
						
						if ($res instanceof WebResponseBase) {
							return $res;
						} else {
							return new WebResponseJson(array(
								'status' => 'error',
								'errorText' => 'Invalid return type'
							));
						}
						
					} catch(Exception $e) {
						return new WebResponseJson(array(
							'status' => 'error',
							'errorText' => UtilsExceptions::toString($e)	
						));
					}
					
				} else {
					return new WebResponseJson(array(
						'status' => 'error',
						'errorText' => 'Method not found'
					));
				}
			}
			
		}
		
		return $this->onProcess();
	}
	
	//************************************************************************************
	/**
	 * @return WebResponseBase
	 */
	protected abstract function onProcess();
	
	//************************************************************************************
	/**
	 * @param WebResponseBase $oResponse
	 */
	public final function adaptResponse($oResponse) {
		if (!($oResponse instanceof WebResponseBase)) throw new InvalidArgumentException('oResponse is not WebResponseBase');
		foreach($this->getAllWidgets() as $oWidget) {
			$oWidget->adaptResponse($oResponse);
		}
		$this->onAdaptResponse($oResponse);
	}
	
	//************************************************************************************
	/**
	 * @param WebResponseBase $oResponse
	 */
	protected abstract function onAdaptResponse($oResponse);

	//************************************************************************************
	/**
	 * @param string $key
	 * @param TemplateRenderableProxyContext $oContext
	 */
	public function tplRender($key,$oContext) {
		$self = $this;
		
		if ($key == 'Widgets') {
			return new TemplateRenderableProxy(function($key,$ctx) use($self) {
				$oWidget = $self->getWidget($key);
				if ($oWidget) {
					return new TemplateRenderableProxy($oWidget);
				} else {
					return array();
				}
			});
		}
		
		if ($key == 'RenderedWidgets') {
			return new TemplateRenderableProxy(function($key,$ctx) use($self) {
				
				if (strpos($key, '/') !== false) {
					list($groupName, $selector) = explode('/',$key);
					if ($groupName && $selector == '*') {
						$oGroup = $this->getWidgetsGroup($groupName);
						if ($oGroup) {
							$html = array();
							foreach($oGroup->getWidgets() as $oWidget) {
								$html[] = UtilsWebUI::render($oWidget, $ctx);
							}
							return implode("\n", $html);
						} else {
							return sprintf('[Group %s not found]', $groupName);
						}
					}	
				}			
				
				$oWidget = $self->getWidget($key);
				if ($oWidget) {
					return UtilsWebUI::render($oWidget, $ctx);
				} else {
					return sprintf('[Widget %s not found]', $key);
				}
			});
		}
		
		if ($key == 'WidgetGroupNames') {
			return new TemplateRenderableProxy(function($key,$ctx) use($self) {
				$names = array();
				foreach($this->getWidgets($key) as $oWidget) {
					$names[] = $oWidget->getName();
				}
				return $names;
			});
		}
		
		if ($key == 'WidgetsGroups') {
			return TemplateRenderableProxy::wrap($this->widgetsGroups);
		}
		
		if ($key == 'Errors') {
			return TemplateRenderableProxy::wrap($this->getAllErrors());
		}
		
		if ($key == 'RenderedErrors') {
			return new TemplateRenderableProxy(function($key,$ctx) use($self) {
				$oErrors = new ValidationErrorsCollection();
				foreach($self->getFilteredErrors($key) as $oError) {
					$oErrors->add($oError);
				}
				
				$oRenderer = new UIValidationErrorsRenderer($oErrors);
				return UtilsWebUI::render($oRenderer, $ctx);
			});
		}
		
		if ($key == 'action') {
			if ($this->getWebRequest()) {
				return $this->getWebRequest()->getHTTPRequest()->getRequestURL();
			} else {
				return '';
			}
		}
		
		if ($key == 'back') {
			return $this->backURL;
		}
		
		return '';
	}
	
	//************************************************************************************
	protected function redirectBack($msg='') {
		if ($msg) {
			$this->getUIComponent()->getNotificationsStorage()->add(new UINotification($msg));
		}
		
		return new WebResponseRedirect($this->backURL);
	}
	
	//************************************************************************************
	protected function redirectTo() {
		$args = func_get_args();
		return new WebResponseRedirect(call_user_func_array('genLink', $args));
	}
	
}

?>