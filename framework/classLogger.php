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

class Logger {

	/**
	 * @var Closure[]
	 */
	private static $adapters = array();
	
	//************************************************************************************
	public static function registerAdapter($func) {
		if (!($func instanceof Closure)) throw new InvalidArgumentException('func is not Closure');
		self::$adapters[] = $func;
	}
	
	//************************************************************************************
	private static function indent($str, $spacesNum) {
		$arr = array();
		foreach(explode("\n", $str) as $l) {
			$arr[] = str_repeat(' ', $spacesNum) . $l;
		}
		return implode("\n",$arr);
	}
	
	//************************************************************************************
	private static function ensureLength($str, $len) {
		if (strlen($str) >= $len) return $str;
		if (strlen($str) == 0) return str_repeat(' ', $len);
		$l = $len - strlen($str);
		return $str . str_repeat(' ',$l);
	}
	
	//************************************************************************************
	public static function info($msg, $obj=null,$fields=array()) {
		self::append('INFO', $msg, $obj, $fields);
	}
	
	//************************************************************************************
	public static function warn($msg, $e=null,$fields=array()) {
		self::append('WARN', $msg, $e, $fields);
	}
	
	//************************************************************************************
	public static function error($msg, $e=null,$fields=array()) {
		self::append('ERROR', $msg, $e, $fields);
	}	

	//************************************************************************************
	public static function debug($msg, $obj=null,$fields=array()) {
		self::append('DEBUG', $msg, $obj, $fields);
	}
	
	//************************************************************************************
	public static function fatal($msg, $obj=null,$fields=array()) {
		self::append('FATAL', $msg, $obj, $fields);
	}
	
	//************************************************************************************
	private static function append($type, $msg, $obj, $fields) {
		if (!is_array($fields)) $fields = array();
		
		$fields['log.date'] = date(DATE_ATOM);
		$fields['log.type'] = $type;
		$fields['log.message'] = $msg;
		
		$str = '';
		$str .= sprintf('[%s] ', self::ensureLength(date(DATE_ATOM), 16));
		$str .= sprintf('[%s] ', $type);
		
		if (strpos($msg, "\n") !== false) {
			$str .= "\n";
			$str .= self::indent($msg, 5);
		} else {
			$str .= $msg;
		}
		$str .= "\n";
		
		if ($obj) {
			if ($obj instanceof Exception) {
				$fields['exception.type'] = get_class($obj);
				$fields['exception.code'] = $obj->getCode();
				$fields['exception.message'] = $obj->getMessage();
				$fields['exception.trace'] = $obj->getTraceAsString();
				UtilsExceptions::getInfoFields($obj, $fields);
			} else {
				ob_start();
				var_dump($obj);
				$fields['obj'] = ob_get_clean();
			}
		}
		
		foreach($fields as $k => $v) {
			if (!is_string($v)) {
				ob_start();
				var_dump($v);
				$v = ob_get_clean();
			}
			
			if (strpos($v, "\n") !== false) {
				$str .= sprintf('  %s:', $k) . "\n";
				$str .= self::indent($v, 4);
				$str .= "\n";
			} else {
				$str .= sprintf('  %s: %s', $k, $v) . "\n"; 
			}
		}
		
		foreach(self::$adapters as $func) {
			$func($str, $type, $msg, $obj, $fields);
		}
	}
	
}

?>