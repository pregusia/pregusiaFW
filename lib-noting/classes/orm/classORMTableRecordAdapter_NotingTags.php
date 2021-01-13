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

class ORMTableRecordAdapter_NotingTags extends ORMTableRecordAdapter_LinkedTable {

	//************************************************************************************
	public function __construct($oRecord) {
		if (!($oRecord instanceof INotableObject)) throw new InvalidArgumentException('oRecord is not INotableObject');
		
		parent::__construct($oRecord, 'noting_tags', function() use ($oRecord) {
			$arr = array();
			$arr[] = new NameValuePair('objectID', $oRecord->notingGetObjectID());
			$arr[] = new NameValuePair('objectType', $oRecord->notingGetObjectType());
			return $arr;
		});
		
		$self = $this;
		$oRecord->getEvents(ORMTableRecord::EVENTS_TPL_RENDER)->add(function($key, $oContext) use ($self) {
			if ($key == 'tags') return implode(',', $self->getTags());
			if ($key == 'TagsArray') return $self->getTags();
			return null;
		});
	}
	
	//************************************************************************************
	/**
	 * Zwraca wartosc ktora nalezy dodac do $this->values
	 * Jesli zwroci false to znaczy ze nic nie dodawac
	 * @param SQLResultsRow $oRow
	 * @return mixed
	 */
	protected function internalParseValue($oRow) {
		$tag = trim($oRow->getColumn('tag')->getValueRaw());
		if ($tag) {
			return $tag;
		} else {
			return false;
		}
	}
	
	//************************************************************************************
	/**
	 * @return array[] Tablica wartosci do zapisania, kazda pozycja to tablica par k=>v
	 */
	protected function internalGetSQLValues() {
		$res = array();
		foreach($this->internalGetValues() as $tag) {
			$res[] = array(
				'tag' => $tag,
			);
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @return string[]
	 */
	public function getTags() {
		return $this->internalGetValues();
	}
	
	//************************************************************************************
	/**
	 * @param string $tag
	 */
	public function add($tag) {
		if ($tag = trim($tag)) {
			if (!in_array($tag, $this->getTags())) {
				$this->internalAddValue($tag);
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param string[] $arr
	 */
	public function addAll($arr) {
		if (UtilsArray::isIterable($arr)) {
			foreach($arr as $v) {
				if ($v = trim($v)) {
					$this->add($v);
				}
			}
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $tag
	 */
	public function remove($tag) {
		if ($this->has($tag)) {
			$arr = array();
			foreach($this->getTags() as $t) {
				if ($t != $tag) {
					$arr[] = $t;
				}
			}
			$this->replace($arr);
		}
	}	
	
	//************************************************************************************
	public function removeAll($tags) {
		$arr = array();
		foreach($this->getTags() as $t) {
			if (!in_array($t, $tags)) {
				$arr[] = $t;
			}
		}
		$this->replace($arr);
	}
	
	//************************************************************************************
	/**
	 * @param string[] $arr
	 */
	public function replace($arr) {
		$this->clear();
		$this->addAll($arr);
	}
	
	//************************************************************************************
	/**
	 * @param string[] $arr
	 */
	public function set($arr) {
		$this->clear();
		$this->addAll($arr);
	}
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @param string $sep
	 */
	public function replaceFromString($str, $sep=',') {
		$this->clear();
		$this->setFromString($str, $sep);
	}
	
	//************************************************************************************
	public function has($tag) {
		return in_array($tag, $this->getTags());
	}
	
	//************************************************************************************
	/**
	 * @param string $str
	 * @param string $sep
	 */
	public function setFromString($str,$sep=',') {
		$this->clear();
		foreach(explode($sep,$str) as $v) {
			if ($v = trim($v)) {
				$this->add($v);
			}
		}
	}
	
}

?>