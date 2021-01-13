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


class UIPageSelector implements IUIRenderable {
	
	use TUIComponentHaving;
	
	private $page = 0;
	private $recordsNum = 0;
	private $perPage = 0;
	private $oLinkBuilder = null;
	private $pageVariableName = 'page';

	

	//************************************************************************************
	/**
	 * @param int $page
	 * @param int $perPage
	 * @param int $num
	 * @param WebLinkBuilder $oLinkBuilder
	 * @param string $pageVariableName
	 */
	public function __construct($page, $perPage, $num, $oLinkBuilder, $pageVariableName='page') {
		if ($page < 1) $page = 1;
		if ($perPage < 1) throw new InvalidArgumentException('perPage has invalid value');
		if ($num < 0) $num = 0;
		if (!($oLinkBuilder instanceof WebLinkBuilder)) throw new InvalidArgumentException('oLinkBuilder is not WebLinkBuilder');
		
		$this->page = $page;
		$this->perPage = $perPage;
		$this->recordsNum = $num;
		$this->oLinkBuilder = $oLinkBuilder;
		$this->pageVariableName = $pageVariableName;
	}

	//************************************************************************************
	public function getPagesNum() { return max(1, ceil($this->recordsNum / $this->perPage)); }
	public function isNext() { return $this->getPage() < $this->getPagesNum(); }
	public function isPrev() { return $this->getPage() > 1; }
	public function isValid($page) { return ($page >= 1 && $page <= $this->getPagesNum()); }
	public function getLast() { return $this->getPagesNum(); }

	//************************************************************************************
	/**
	 * @return WebLinkBuilder
	 */
	public function getLinkBuilder() { return $this->oLinkBuilder; }
	
	//************************************************************************************
	/**
	 * @param int $page
	 * @return string
	 */
	public function genPageLink($page) {
		return $this->getLinkBuilder()->create($this->pageVariableName, $page);
	}
	
	//************************************************************************************
	public function getPage() {
		if ($this->page <= 0) return 1;
		if ($this->page > $this->getPagesNum()) return $this->getPagesNum();
		return $this->page;
	}
	
	//************************************************************************************
	public function getSQLLimitStart() {
		return ($this->getPage() - 1) * $this->perPage;
	}
	
	//************************************************************************************
	public function getSQLLimitCount() {
		return $this->perPage;
	}

	//************************************************************************************
	/**
	 * @param ORMQuerySelect $oSelect
	 */
	public function apply($oSelect) {
		if (!($oSelect instanceof ORMQuerySelect)) throw new InvalidArgumentException('oSelect is not ORMQuerySelect');
		$oSelect->setLimit($this->getSQLLimitStart(), $this->getSQLLimitCount());
	}

	//************************************************************************************
	/**
	 * Zwraca liste stron do ktorych mozna utworzyc linki
	 * @return int[]
	 */
	public function getPages() {
		$genPages = array();
		$activePage = $this->getPage();
		$pagesNum = $this->getPagesNum();

		for($I=0;$I<7;$I++)
		{
			$p = 1 + round( ($activePage-1)*($I/7) );
			if ( !$genPages[$p] ) $genPages[$p] = 1;
		}
		for($I=$activePage - 5; $I < $activePage + 5;$I++)
		{
			if ( !$genPages[$I] ) $genPages[$I] = 1;
		}
		for($I=1;$I<=7;$I++)
		{
			$p = $activePage + round( ($pagesNum - $activePage)*($I/7) );
			if ( !$genPages[$p] ) $genPages[$p] = 1;
		}

		$pages = array_keys($genPages);
		sort($pages);

		$arr = array();
		foreach($pages as $i) {
			if ($this->isValid($i)) $arr[] = $i;
		}
		return $arr;
	}
	
	//************************************************************************************
	private function tplRenderPage($page) {
		return array(
			"page" => $page,
			"link" => $this->genPageLink($page),
			"active" => ($page == $this->getPage())
		);
	}
	
	//************************************************************************************
	public function tplRender($key,$oContext) {
		switch($key) {
			case 'All': return array_map(function($a){ return $this->tplRenderPage($a); }, $this->getPages());
			case 'First': return $this->tplRenderPage(1);
			case 'Last': return $this->tplRenderPage($this->getLast());
			case 'Prev': return $this->isPrev() ? $this->tplRenderPage($this->getPage() - 1) : array();
			case 'Next': return $this->isNext() ? $this->tplRenderPage($this->getPage() + 1) : array();
			
			case 'current': return $this->getPage();
			case 'render': return UtilsWebUI::render($this, $oContext);
			
			default: return '';
		}
	}
	
	//************************************************************************************
	public function uiRenderGetVariableName() { return 'PageSelector'; }
	public function uiRenderGetTemplateLocation($ctx=null) { return ''; }

}


?>