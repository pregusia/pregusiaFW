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


class UtilsNet {
	
	private function __construct() { }
	
	//************************************************************************************
	/**
	 * @param string $addr
	 * @throws InvalidArgumentException
	 */
	public static function validateIPv4Address($addr) {
		if (ip2long($addr) === false) {
			throw new InvalidArgumentException(sprintf('"%s" is not valid IPv4 address', $addr));
		}
	}

	//************************************************************************************
	/**
	 * @param string $addr
	 * @return boolean
	 */
	public static function isIPv4Addr($addr) {
		$addr = trim($addr);
		if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/i', $addr)) {
			foreach(explode('.',$addr) as $v) {
				$v = intval($v);
				if ($v < 0) return false;
				if ($v > 255) return false;
			}
			return true;
		}
		return false;
	}	
	
	//************************************************************************************
	/**
	 * Zapewnia ze podana wartosc jest podsiecia - czyli X.X.X.X/YY
	 * Jesli nie sprecyzowano YY to automatycznie tworzy 32
	 * 
	 * @param string $subnet
	 * @return string
	 */
	public static function ensureSubnet($subnet) {
		$subnet = trim($subnet);
		if (!$subnet) throw new InvalidArgumentException('Empty subnet');
		
		if (strpos($subnet, '/') !== false) {
			list($addr, $maskBits) = explode('/',$subnet,2);

			self::validateIPv4Address($addr);
			$maskBits = intval($maskBits);
			if ($maskBits < 0 || $maskBits > 32) throw new InvalidArgumentException('Invalid mask length');
			
			return sprintf('%s/%d', $addr, $maskBits);
		} else {
			self::validateIPv4Address($subnet);
			return sprintf('%s/32', $subnet);
		}
	}

	//************************************************************************************
	/**
	 * Zwraca adres IP bedacy maska z podanej wartosci bitow
	 * czyli dla 24 zwroci 255.255.255.0 itp
	 * Potrzebne zeby uzywac w operacjach bitowych
	 * 
	 * @param int $maskBits
	 * @return string
	 */
	public static function maskToIPv4Address($maskBits) {
		if ($maskBits < 0 || $maskBits > 32) throw new InvalidArgumentException('Invalid mask length');
		
		$res = 0;
		$d = 1;
		for($i=0;$i<$maskBits;++$i) {
			$res |= $d;
			$d *= 2;
		}
		return long2ip($res << (32 - $maskBits));		
	}
	
	//************************************************************************************
	/**
	 * @param string $subnet
	 * @param bool $excludeFirstAndLast
	 * @return string[]
	 */
	public static function enumerateIPv4AddressesFromNetwork($subnet, $excludeFirstAndLast=true) {
		$subnet = trim($subnet);
		if (!$subnet) throw new InvalidArgumentException('Empty subnet');
		
		if (strpos($subnet, '/') === false) throw new InvalidArgumentException('Not network given');
		
		list($addr, $maskBits) = explode('/',$subnet,2);

		self::validateIPv4Address($addr);
		$maskBits = intval($maskBits);
		if ($maskBits < 0 || $maskBits > 32) throw new InvalidArgumentException('Invalid mask length');
		
		return self::enumerateIPv4Addresses($addr, $maskBits, $excludeFirstAndLast);
	}

	//************************************************************************************
	/**
	 * Enumeruje adresy IP w podanej podsieci
	 * 
	 * @param string $ipAddress
	 * @param int $maskBits
	 * @param bool $excludeFirstAndLast
	 * @return string[]
	 */
	public static function enumerateIPv4Addresses($ipAddress, $maskBits, $excludeFirstAndLast=true) {
		self::validateIPv4Address($ipAddress);
		
		$maskBits = intval($maskBits);
		if ($maskBits < 0 || $maskBits > 32) throw new InvalidArgumentException('Invalid mask length');
		if ($maskBits < 16) throw new IllegalStateException('Given to wide mask for enumeration');
		
		
		$iAddress = ip2long($ipAddress);
		$iMaskAddress = ip2long(self::maskToIPv4Address($maskBits));
		$iNetworkAdderss = $iAddress & $iMaskAddress;
		
		$arr = array();
		$num = intval(pow(2, 32 - $maskBits));
		for($i=0;$i<$num;++$i) {
			$arr[] = long2ip($iNetworkAdderss | $i);
		}
		
		if (count($arr) > 2 && $excludeFirstAndLast) {
			array_shift($arr); // adres sieci
			array_pop($arr); // broadcast sieci
		}
		
		return $arr;
	}	
	
	//************************************************************************************
	/**
	 * Zwraca adres IP sieci dla podanego adresu IP i CIDR
	 * @param string $ipAddress
	 * @param int $maskBits
	 * @return string
	 */
	public static function getIPv4NetworkAddress($ipAddress, $maskBits) {
		self::validateIPv4Address($ipAddress);
		
		$maskBits = intval($maskBits);
		if ($maskBits < 0 || $maskBits > 32) throw new InvalidArgumentException('Invalid mask length');
		
		$iAddress = ip2long($ipAddress);
		$iMaskAddress = ip2long(self::maskToIPv4Address($maskBits));
		$iNetworkAdderss = $iAddress & $iMaskAddress;
		
		return long2ip($iNetworkAdderss);
	}	
	
	//************************************************************************************
	/**
	 * Sprawdza czy dany adres IP jest w podanej podsieci
	 * @param string $ip Adres IP w formacie x.x.x.x
	 * @param string $network Podsiec w formacie x.x.x.x/y
	 * @throws InvalidArgumentException
	 * @return boolean
	 */
	public static function CIDRMatch($ip, $network) {
		list($subnet, $bits) = explode('/', $network);
		$ipStr = $ip;
		$ip = ip2long($ip);
		$bits = intval($bits);
		if ($bits <= 1 || $bits > 32) throw new InvalidArgumentException('Invalid network');
		
		if ($ip === false) throw new InvalidArgumentException('Invalid IP');
		if (!$subnet) throw new InvalidArgumentException('Invalid network');
		
		if ($bits == 32) {
			return $ipStr == $subnet;
		} else {
			$subnet = ip2long($subnet);
			$mask = -1 << (32 - $bits);
			$subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
			return ($ip & $mask) == $subnet;
		}
	}	
	
	//************************************************************************************
	/**
	 * @param string $mac
	 * @return string
	 */
	public static function normalizeMacAddr($mac) {
		if (!self::isMacAddr($mac)) return '';
		
		$mac = trim($mac);
		if (strlen($mac) == 17) {
			if (preg_match('/^[0-9A-F]{2}\:[0-9A-F]{2}\:[0-9A-F]{2}\:[0-9A-F]{2}\:[0-9A-F]{2}\:[0-9A-F]{2}$/i', $mac)) {
				return strtoupper($mac);
			}
			if (preg_match('/^[0-9A-F]{2}\-[0-9A-F]{2}\-[0-9A-F]{2}\-[0-9A-F]{2}\-[0-9A-F]{2}\-[0-9A-F]{2}$/i', $mac)) {
				$arr = explode('-',$mac);
				return strtoupper(implode(':',$mac));
			}
			return $mac;
		}
		if (strlen($mac) == 12) {
			return strtoupper(implode(':', str_split($mac,2)));
		}
		return '';
	}
	
	//************************************************************************************
	/**
	 * @param string $mac
	 * @return bool
	 */
	public static function isMacAddr($mac) {
		$mac = trim($mac);
		if (strlen($mac) == 17) {
			if (preg_match('/^[0-9A-F]{2}\:[0-9A-F]{2}\:[0-9A-F]{2}\:[0-9A-F]{2}\:[0-9A-F]{2}\:[0-9A-F]{2}$/i', $mac)) return true;
			if (preg_match('/^[0-9A-F]{2}\-[0-9A-F]{2}\-[0-9A-F]{2}\-[0-9A-F]{2}\-[0-9A-F]{2}\-[0-9A-F]{2}$/i', $mac)) return true;
		}
		if (strlen($mac) == 12) {
			if (preg_match('/^[0-9A-F]{12}$/i', $mac)) return true;
		}
		return false;
	}	
	
	//**************************************************************
	/**
	 * Normalizuje nazwe w widzeniu opisu do urzazden sieciowych
	 * @param $value
	 */
	public static function normalizeNetworkString($value) {
		$n = '';
		$valid = '0123456789abcdefghijklmnopqrstuwvxyz_!#$%()*+,-.:;<=>@[\]^_{|}~';
		for($i=0;$i<strlen($value);++$i) {
			$ch = substr($value, $i, 1);
			if (strpos($valid, strtolower($ch)) !== false) {
				$n .= $ch;
			} else {
				$n .= '_';
			}
		}
		if (!$n) $n = 'empty';
		return $n;
	}		
	
}

?>