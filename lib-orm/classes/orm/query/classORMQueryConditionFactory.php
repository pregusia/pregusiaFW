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


class ORMQueryConditionFactory {

	//************************************************************************************
	/**
	 * @param mixed $val
	 * @return IORMQueryCondition
	 */
	public static function makeEqual($val) {
		if ($val === null) {
			return new ORMQueryConditionClosureProxy(function($name, $oORM) {
				return sprintf('%s IS NULL', $name);
			});
		} else {
			return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($val) {
				return sprintf('%s = %s', $name, $oORM->toSQL($val));
			});
		}
	}
	
	//************************************************************************************
	/**
	 * @param number $val
	 * @return IORMQueryCondition
	 */
	public static function makeGreaterThan($val) {
		return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($val) {
			return sprintf('%s > %s', $name, $oORM->toSQL($val));
		});
	}
	
	//************************************************************************************
	/**
	 * @param number $val
	 * @return IORMQueryCondition
	 */
	public static function makeLowerThan($val) {
		return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($val) {
			return sprintf('%s < %s', $name, $oORM->toSQL($val));
		});
	}

	//************************************************************************************
	/**
	 * @param mixed $val
	 * @return IORMQueryCondition
	 */
	public static function makeNotEqual($val) {
		if ($val === null) {
			return new ORMQueryConditionClosureProxy(function($name, $oORM) {
				return sprintf('%s IS NOT NULL', $name);
			});
		} else {
			return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($val) {
				return sprintf('%s <> %s', $name, $oORM->toSQL($val));
			});
		}		
	}

	//************************************************************************************
	/**
	 * @param number $l
	 * @param number $h
	 * @return IORMQueryCondition
	 */
	public static function makeBetween($l,$h) {
		return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($l, $h) {
			return sprintf('%s BETWEEN %d AND %d', $name, $l, $h);
		});
	}
	
	//************************************************************************************
	/**
	 * @param DatesRange $oRange
	 * @param DateAndTimeRange $oRange
	 * @return IORMQueryCondition
	 */
	public static function makeBetweenDatesRange($oRange) {
		if ($oRange instanceof DatesRange) {
			return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($oRange) {
				return sprintf('%s BETWEEN "%s 00:00:00" AND "%s 23:59:59"', $name, $oRange->getStart()->toString(), $oRange->getStop()->toString());
			});			
		}
		elseif ($oRange instanceof DateAndTimeRange) {
			return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($oRange) {
				return sprintf('%s BETWEEN "%s" AND "%s"', $name, $oRange->getStart()->toString(), $oRange->getStop()->toString());
			});
		}
		else {
			throw new InvalidArgumentException('oRange is not DatesRange nor DateAndTimeRange');
		}
	}

	//************************************************************************************
	/**
	 * @param string $text
	 * @return IORMQueryCondition
	 */
	public static function makeLike($text) {
		$text = '%' . strval($text) . '%';
		
		return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($text) {
			return sprintf('%s LIKE %s', $name, $oORM->toSQL($text));
		});
	}
	
	//************************************************************************************
	/**
	 * @param string $text
	 * @return IORMQueryCondition
	 */
	public static function makeLikeRaw($text) {
		$text = strval($text);
		
		return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($text) {
			return sprintf('%s LIKE %s', $name, $oORM->toSQL($text));
		});
	}	
	
	//************************************************************************************
	/**
	 * @param string $text
	 * @return IORMQueryCondition
	 */
	public static function makeStartsWith($text) {
		$text = strval($text);
		if ($text) {
			$text = $text . '%';
			
			return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($text) {
				return sprintf('%s LIKE %s', $name, $oORM->toSQL($text));
			});
		} else {
			return new ORMQueryConditionReturnValue('1');
		}
	}
	
	//************************************************************************************
	/**
	 * @param string $text
	 * @return IORMQueryCondition
	 */
	public static function makeEndsWith($text) {
		$text = strval($text);
		if ($text) {
			$text = '%' . $text;
			
			return new ORMQueryConditionClosureProxy(function($name, $oORM) use ($text) {
				return sprintf('%s LIKE %s', $name, $oORM->toSQL($text));
			});
		} else {
			return new ORMQueryConditionReturnValue('1');
		}
	}

	//************************************************************************************
	/**
	 * @param array $arr
	 * @return IORMQueryCondition
	 */
	public static function makeIn($arr) {
		if (is_array($arr) && !empty($arr)) {
			return new ORMQueryConditionClosureProxy(function($name, $oORM) use($arr) {
				$tmp = array();
				foreach($arr as $v) $tmp[] = $oORM->toSQL($v);
				
				return sprintf('%s IN (%s)', $name, implode(',', $tmp));
			});
		} else {
			return new ORMQueryConditionReturnValue('0');
		}
	}

	//************************************************************************************
	/**
	 * @param array $arr
	 * @return IORMQueryCondition
	 */
	public static function makeNotIn($arr) {
		if (is_array($arr) && !empty($arr)) {
			return new ORMQueryConditionClosureProxy(function($name, $oORM) use($arr) {
				
				$tmp = array();
				foreach($arr as $v) $tmp[] = $oORM->toSQL($v);
				
				return sprintf('%s NOT IN (%s)', $name, implode(',', $tmp));
			});
		} else {
			return new ORMQueryConditionReturnValue('1');
		}
	}


	//************************************************************************************
	/**
	 * @param int $v
	 * @return IORMQueryCondition
	 */
	public static function makeFlagSet($v) {
		$v = intval($v);
		return new ORMQueryConditionClosureProxy(function($name, $oORM) use($v) {
			return sprintf('(%s & %d) = %d', $name, $v, $v);
		});
	}

	//************************************************************************************
	/**
	 * @param int $v
	 * @return IORMQueryCondition
	 */
	public static function makeFlagNotSet($v) {
		$v = intval($v);
		return new ORMQueryConditionClosureProxy(function($name, $oORM) use($v) {
			return sprintf('(%s & %d) <> %d', $name, $v, $v);
		});
	}

}

?>