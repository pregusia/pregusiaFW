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


class UISorterHelper implements ITemplateRenderableSupplier {
	
	const ORDER_ASC = 1;
	const ORDER_DESC = 2;
	
	private $sortColumn = '';
	private $sortOrder = 1;
	private $sqlColumnsMapping = array();
	
	private $oLinkBuilder = null;
	private $sortVariableName = '';
	
	//************************************************************************************
	public function getSortColumn() { return $this->sortColumn; }
	public function getSortOrder() { return $this->sortOrder; }
	
	//************************************************************************************
	/**
	 * @return WebLinkBuilder
	 */
	public function getLinkBuilder() { return $this->oLinkBuilder; }
	
	//************************************************************************************
	public function __construct($sort, $oLinkBuilder, $sortVariableName='sort') {
		if (!($oLinkBuilder instanceof WebLinkBuilder)) throw new InvalidArgumentException('oLinkBuilder is not WebLinkBuilder');
		if (!$sortVariableName) throw new InvalidArgumentException('Empty sort variable name');
		
		$this->oLinkBuilder = $oLinkBuilder;
		$this->sortVariableName = $sortVariableName;

		$this->sortColumn = '';
		$this->sortOrder = self::ORDER_ASC;
		
		$sort = trim($sort);
		$pos = strrpos($sort, '_');
		if ($pos !== false) {
			$this->sortColumn = substr($sort, 0, $pos);
			$dir = substr($sort, $pos + 1);
			
			if ($dir == 'asc') $this->sortOrder = self::ORDER_ASC;
			if ($dir == 'desc') $this->sortOrder = self::ORDER_DESC;
		}
	}
	
	//************************************************************************************
	public function registerColumnMapping($name, $sql) {
		if ($sql) {
			$this->sqlColumnsMapping[$name] = $sql;
		}
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getSQLOrder() {
		$sql = $this->sqlColumnsMapping[$this->sortColumn];
		if ($sql) {
			if ($this->sortOrder == self::ORDER_ASC) {
				return sprintf('%s ASC', $sql);
			}
			if ($this->sortOrder == self::ORDER_DESC) {
				return sprintf('%s DESC', $sql);
			}
		}
		return '';		
	}
	
	//************************************************************************************
	/**
	 * @param ORMQuerySelect $oSelect
	 * @return bool
	 */
	public function apply($oSelect) {
		if (!($oSelect instanceof ORMQuerySelect)) throw new InvalidArgumentException('oSelect is not ORMQuerySelect');
		
		$sql = $this->sqlColumnsMapping[$this->sortColumn];
		if ($sql) {
			if ($this->sortOrder == self::ORDER_ASC) {
				$oSelect->setOrder(sprintf('%s ASC', $sql));
				return true;
			}
			if ($this->sortOrder == self::ORDER_DESC) {
				$oSelect->setOrder(sprintf('%s DESC', $sql));
				return true;
			}
		}
		
		return false;
	}	
	
	//************************************************************************************
	/**
	 * @param string $column
	 * @param int $dir
	 * @return string
	 */
	public function createLink($column, $dir) {
		if ($dir == self::ORDER_DESC) {
			return $this->getLinkBuilder()->create($this->sortVariableName, sprintf('%s_desc', $column));
		} else {
			return $this->getLinkBuilder()->create($this->sortVariableName, sprintf('%s_asc', $column));
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $key
	 * @param TemplateRenderableProxyContext $oContext
	 */
	public function tplRender($key,$oContext) {
		if ($this->sqlColumnsMapping[$key]) {
			$arr = array();
			
			if ($key == $this->sortColumn) {
				if ($this->sortOrder == self::ORDER_ASC) {
					$arr['direction'] = 'asc';
					$arr['link'] = $this->createLink($this->sortColumn, self::ORDER_DESC);
				} else {
					$arr['direction'] = 'desc';
					$arr['link'] = $this->createLink($this->sortColumn, self::ORDER_ASC);
				}
				
			} else {
				$arr['direction'] = 'none';
				$arr['link'] = $this->createLink($key, self::ORDER_ASC);
			}
			
			return $arr;
		}
		return array();
	}
	
}

?>