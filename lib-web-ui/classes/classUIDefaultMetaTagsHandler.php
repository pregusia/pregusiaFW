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


class UIDefaultMetaTagshandler implements IMetaStringTagsHandler {

	//************************************************************************************
	public function getTags() {
		return array(
			'fmt.nbsp',			
			'fmt.strong',
			'fmt.color',
			'ui.option.disabled',
			'ui.icon.delete',
			'ui.icon.add',
			'ui.icon.list',
			'ui.icon.edit',
			'ui.icon.info',
			'ui.icon.work',
			'ui.icon.back',
			'ui.icon.save',
			'ui.icon.download',
			'ui.icon.pdf',
			'ui.icon.accept',
			'ui.icon.mail',
			'ui.icon.log',
			'ui.icon.send',
			'ui.icon.chart',
			'ui.icon.eye',
			'ui.icon.web',
			'ui.icon.map',
			'ui.icon.refresh',
			'ui.icon.print',
			'ui.icon.lock',
			'ui.icon.unlock',
			'ui.icon.clone',
			'ui.icon.import',
			'ui.icon.upload',
			'ui.icon.export',
			'ui.icon.accept',
			'ui.icon.reject',
			'ui.icon.filter',
			'ui.icon.clear',
			'ui.icon.tag',
			'ui.icon.tags',
			'ui.icon.cross',
			'ui.icon.cancel',
			'ui.icon.search',
			'ui.icon.key',
			'ui.icon.calculator',
			'ui.icon.cash',
			'ui.icon.clock',
			'ui.icon.picture',
			'ui.icon.image',
				
			'ui.spinner.loading',
			'ui.spinner.cogs',
			'ui.spinner.refresh',
			
			'bootstrap.label',
		);
	}

	//************************************************************************************
	public function getPriority() {
		return 1;
	}
	
	//************************************************************************************
	/**
	 * @param object $ctx
	 * @param string $tagName
	 * @param string $tagParam
	 * @param string $innerText
	 */
	public function parse($ctx, $tagName, $tagParam, $innerText) {
		switch($tagName) {
			case 'fmt.strong': return sprintf('<strong>%s</strong>', $innerText);
			case 'fmt.color': return sprintf('<span style="color: %s;">%s</span>', $tagParam, $innerText);
			case 'fmt.nbsp': return '&nbsp;';
			
			case 'bootstrap.label': {
				if (UtilsString::startsWith($tagParam, '#')) {
					return sprintf('<span class="label label-primary" style="background-color: %s;">%s</span>', $tagParam, $innerText);
				} else {
					return sprintf('<span class="label label-%s">%s</span>', $tagParam, $innerText);
				}
			}
			
			case 'ui.option.disabled': return '';
			case 'ui.icon.add': return '<i class="fa fa-plus"></i>';
			case 'ui.icon.list': return '<i class="fa fa-list"></i>';
			case 'ui.icon.edit': return '<i class="fa fa-edit"></i>';
			case 'ui.icon.delete': return '<i class="fa fa-trash-o"></i>';
			case 'ui.icon.info': return '<i class="fa fa-info"></i>';
			case 'ui.icon.work': return '<i class="fa fa-cog"></i>';
			case 'ui.icon.back': return '<i class="fa fa-angle-double-left"></i>';
			case 'ui.icon.save': return '<i class="fa fa-floppy-o"></i>';
			case 'ui.icon.download': return '<i class="fa fa-download"></i>';
			case 'ui.icon.pdf': return '<i class="fa fa-file-pdf-o"></i>';
			case 'ui.icon.accept': return '<i class="fa fa-check"></i>';
			case 'ui.icon.mail': return '<i class="fa fa-envelope"></i>';
			case 'ui.icon.log': return '<i class="fa fa-history"></i>';
			case 'ui.icon.send': return '<i class="fa fa-paper-plane"></i>';
			case 'ui.icon.chart': return '<i class="fa fa-bar-chart"></i>';
			case 'ui.icon.eye': return '<i class="fa fa-eye"></i>';
			case 'ui.icon.web': return '<i class="fa fa-globe"></i>';
			case 'ui.icon.map': return '<i class="fa fa-map"></i>';
			case 'ui.icon.refresh': return '<i class="fa fa-refresh"></i>';
			case 'ui.icon.print': return '<i class="fa fa-print"></i>';
			case 'ui.icon.lock': return '<i class="fa fa-lock"></i>';
			case 'ui.icon.unlock': return '<i class="fa fa-unlock"></i>';
			case 'ui.icon.clone': return '<i class="fa fa-clone"></i>';
			case 'ui.icon.import': return '<i class="fa fa-upload"></i>';
			case 'ui.icon.upload': return '<i class="fa fa-upload"></i>';
			case 'ui.icon.export': return '<i class="fa fa-download"></i>';
			case 'ui.icon.accept': return '<i class="fa fa-check"></i>';
			case 'ui.icon.reject': return '<i class="fa fa-ban"></i>';
			case 'ui.icon.filter': return '<i class="fa fa-filter"></i>';
			case 'ui.icon.clear': return '<i class="fa fa-eraser"></i>';
			case 'ui.icon.tag': return '<i class="fa fa-tag"></i>';
			case 'ui.icon.tags': return '<i class="fa fa-tags"></i>';
			case 'ui.icon.cross': return '<i class="fa fa-times"></i>';
			case 'ui.icon.cancel': return '<i class="fa fa-ban"></i>';
			case 'ui.icon.search': return '<i class="fa fa-search"></i>';
			case 'ui.icon.key': return '<i class="fa fa-key"></i>';
			case 'ui.icon.calculator': return '<i class="fa fa-calculator"></i>';
			case 'ui.icon.cash': return '<i class="fa fa-usd"></i>';
			case 'ui.icon.clock': return '<i class="fa fa-clock-o"></i>';
			case 'ui.icon.picture': return '<i class="fa fa-picture-o"></i>';
			case 'ui.icon.image': return '<i class="fa fa-picture-o"></i>';
			
			case 'ui.spinner.cogs': return '<i class="fa fa-cog fa-spin"></i>';
			case 'ui.spinner.refresh': return '<i class="fa fa-refresh fa-spin"></i>';
			case 'ui.spinner.loading': return '<i class="fa fa-spinner fa-spin"></i>';
			
			default: return '';
		}
	}
	
}

?>