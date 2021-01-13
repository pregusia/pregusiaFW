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

class UtilsJSON {
	
	private function __construct() { }
	
	
	//************************************************************************************
	/**
	 * @param JsonSerializable $obj
	 * @return array
	 */
	public static function serializeAbstraction($obj) {
		if (!$obj) return null;
		if (!($obj instanceof JsonSerializable)) throw new InvalidArgumentException('obj is not JsonSerializable');
		return array(
			'__className' => get_class($obj),
			'data' => $obj->jsonSerialize()	
		);
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @param CodeBaseDeclaredType $oBase
	 * @return object
	 */
	public static function unserializeAbstraction($arr, $oBase=null) {
		if ($oBase && (!$oBase instanceof CodeBaseDeclaredType)) throw new InvalidArgumentException('oBase is not CodeBaseDeclaredType');
		
		if (is_array($arr) && $arr['__className']) {
			try {
				$oClass = CodeBase::getClass($arr['__className']);
				if (!$oClass->hasStaticMethod('jsonUnserialize')) throw new SerializationException(sprintf('Class %s has not jsonUnserialize', $oClass->getName()));
				
				$obj = $oClass->callStaticMethod('jsonUnserialize', array($arr['data']));
				if ($obj && $oBase) {
					if (!$oBase->isInstanceOf($obj)) throw new SerializationException(sprintf('Unserialized object of type %s is not instance of %s', get_class($obj), $oBase->getName()));
				}
				
				return $obj;
			} catch(Exception $e) {
				Logger::warn('UtilsJSON::unserializeAbstraction', $e);
			}
		}
		
		return null;
	}
	
	//************************************************************************************
	/**
	 * @param JsonSerializable[] $arr
	 * @return array
	 */
	public static function serializeArray($arr) {
		$res = array();
		if (is_array($arr)) {
			foreach($arr as $v) {
				if (!($v instanceof JsonSerializable)) throw new InvalidArgumentException('array element is not JsonSerializable');
				$res[] = $v->jsonSerialize();
			}
		}
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @param CodeBaseDeclaredType $oType
	 * @return object[]
	 */
	public static function unserializeArray($arr, $oType) {
		if (!$oType instanceof CodeBaseDeclaredType) throw new InvalidArgumentException('oType is not CodeBaseDeclaredType');
		if (!$oType->hasStaticMethod('jsonUnserialize')) throw new SerializationException(sprintf('Type %s dont have static jsonUnserialize', $oType->getName()));
		
		$res = array();
		if (is_array($arr)) {
			foreach($arr as $v) {
				$obj = $oType->callStaticMethod('jsonUnserialize', array($v));
				if ($obj) {
					$res[] = $obj;
				}
			}
		}
		return $res;
	}
	
	
	//************************************************************************************
	/**
	 * @param ReflectionFunction $oFunction
	 * @return string
	 */
	private static function serializeClosure_extractFunction($oFunction) {
		$content = '';
		$file = file($oFunction->getFileName());
		for($i=$oFunction->getStartLine();$i<=$oFunction->getEndLine();++$i) $content .= $file[$i - 1];
	
		$start = strpos($content,'function(');
		if ($start === false) return '';
		
		$len = strlen($content);
		$stop = 0;
		
		$bracketsNum = 0;
		$inString = 0;
		for($i=$start;$i<$len;++$i) {
			$ch = substr($content,$i,1);
			if ($inString == 1) {
				if ($ch == '"' && substr($content, $i - 1, 1) != '\\') { $inString = 0; continue; }
			} elseif ($inString == 2) {
				if ($ch == "'" && substr($content, $i - 1, 1) != '\\') { $inString = 0; continue; }
			} else {
				if ($ch == '"' && substr($content, $i - 1, 1) != '\\') { $inString = 1; continue; }
				if ($ch == "'" && substr($content, $i - 1, 1) != '\\') { $inString = 2; continue; }
				
				if ($ch == '{') $bracketsNum += 1;
				if ($ch == '}') {
					$bracketsNum -= 1;
					if ($bracketsNum == 0) {
						$stop = $i + 1;
						break;
					}
				}
				
			}
		}
		
		return substr($content, $start, $stop - $start);		
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @return mixed
	 */
	private static function serializeClosure_serializeCaptured($value) {
		if (is_object($value)) {
			if (!($value instanceof JsonSerializable)) throw new SerializationException(sprintf('Captured variable of class %s is not JsonSerializable', get_class($value)));
			return UtilsJSON::serializeAbstraction($value);
		}
		elseif (is_string($value)) return $value;
		elseif (is_bool($value)) return $value;
		elseif (is_double($value)) return $value;
		elseif (is_float($value)) return $value;
		elseif (is_int($value)) return $value;
		elseif (is_null($value)) return $value;
		elseif (is_array($value)) {
			$res = array();
			foreach($value as $k => $v) {
				$res[$k] = self::serializeClosure_serializeCaptured($v);
			}
			return $res;
		}
		else {
			throw new SerializationException(sprintf('Captured variable of type %s could not be serialized', gettype($value)));
		}		
	}
	
	//************************************************************************************
	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public static function serializeClosure_unserializeCaptured($value) {
		if (is_array($value)) {
			if (isset($value['data']) && isset($value['__className'])) {
				return self::unserializeAbstraction($value);
			} else {
				return $value;
			}
		} else {
			return $value;
		}
	}
	
	//************************************************************************************
	/**
	 * @param Closure $func
	 * @return array
	 */
	public static function serializeClosure($func) {
		if (!($func instanceof Closure)) throw new InvalidArgumentException('func is not Closure');
		
		$oFunction = new ReflectionFunction($func);
		$code = self::serializeClosure_extractFunction($oFunction);
		if (!$code) throw new SerializationException('Could not determine closure code');
		
		$arr = array();
		$arr['code'] = base64_encode($code);
		$arr['static'] = array();
		foreach($oFunction->getStaticVariables() as $name => $value) {
			$arr['static'][$name] = self::serializeClosure_serializeCaptured($value);
		}
		
		return $arr;		
	}
	
	//************************************************************************************
	/**
	 * @param array $arr
	 * @return Closure
	 */
	public static function unserializeClosure($arr) {
		if (!(is_array($arr) && isset($arr['code']) && isset($arr['static']) && is_array($arr['static']))) return null;
		
		$code = base64_decode($arr['code']);
		if (!$code) return null;
		
		$content = array();
		
		if ($arr['static']) {
			$content[] = sprintf('$___vars = (%s);', var_export($arr['static'], true));
			$content[] = 'foreach($___vars as $k => $v) {';
			$content[] = 'if (is_array($v) && isset($v["__className"])) {';
			$content[] = '$___vars[$k] = UtilsJSON::serializeClosure_unserializeCaptured($v);';
			$content[] = '}';
			$content[] = '}';
			$content[] = 'extract($___vars);';
		}
		
		$content[] = sprintf('return (%s);', $code);
		
		return eval(implode("\n", $content));
	}
	
}

?>