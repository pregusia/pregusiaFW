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


class UIWidget_SourceCodeTextInput extends UIWidget_TextInput {
	
	private $theme = '';
	private $mode = '';
	
	//************************************************************************************
	public function __construct($name, $caption, $mode, $theme='chrome') {
		parent::__construct($name, $caption);
		
		$mode = trim($mode);
		$theme = trim($theme);
		
		if (!$mode) throw new InvalidArgumentException('Empty mode');
		if (!$theme) throw new InvalidArgumentException('Empty theme');
		
		if (!CodeBase::getResource(sprintf('*:public/ace/modes/mode-%s.js', $mode), false)->exists()) {
			throw new InvalidArgumentException(sprintf('Mode %s not found', $mode));
		}
		if (!CodeBase::getResource(sprintf('*:public/ace/themes/theme-%s.js', $theme), false)->exists()) {
			throw new InvalidArgumentException(sprintf('Theme %s not found', $theme));
		}
		
		$this->mode = $mode;
		$this->theme = $theme;
	}
	
	//************************************************************************************
	public function uiRenderGetTemplateLocation($ctx=null) {
		if ($ctx instanceof TemplateRenderableProxyContext) {
			if ($ctx->getTag('mod.full2')) {
				return 'lib-sourcecode-widget:UIWidget.SourceCodeTextInput.full2';
			}
		}
		
		return 'lib-sourcecode-widget:UIWidget.SourceCodeTextInput.normal';
	}
	
	//************************************************************************************
	public function tplRender($key, $oContext) {
		switch($key) {
			case 'theme': return $this->theme;
			case 'mode': return $this->mode;
			default: return parent::tplRender($key, $oContext);
		}
	}

}

?>