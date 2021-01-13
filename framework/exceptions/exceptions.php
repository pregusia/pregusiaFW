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


class FrameworkException extends Exception {
	public function __construct($msg,$code=0) { parent::__construct($msg, $code); }
}

class IllegalStateException extends Exception {
	public function __construct($msg='', $code=0) { parent::__construct($msg, $code); }
}

class IOException extends Exception {
	public function __construct($arg='', $code=0) {
		if ($arg instanceof Exception) {
			parent::__construct('IOException',$arg->getCode(),$arg);
		} else {
			parent::__construct($arg, $code);
		}
	}
}

class SerializationException extends Exception {
	public function __construct($arg='', $code=0) {
		if ($arg instanceof Exception) {
			parent::__construct('SerializationException',$arg->getCode(),$arg);
		} else {
			parent::__construct($arg, $code);
		}
	}	
}

class NotImplementedException extends Exception {
	public function __construct($msg='',$code=0) {
		parent::__construct($msg ? $msg : 'This method is not implemented !', $code);
	}
}

class UnsupportedOperationException extends Exception {
	public function __construct($opName='',$code=0) {
		if ($opName) {
			parent::__construct('Operation ' . $opName . ' is not supported', $code);
		} else {
			parent::__construct('Operation is not supported', $code);
		}
	}
}

class SecurityException extends Exception {
	public function __construct($msg='',$code=0) {
		parent::__construct($msg ? $msg : 'Not sufficient rights',$code);
	}
}

class RequirementException extends Exception {
	public function __construct($msg='',$code=0) {
		parent::__construct($msg ? $msg : 'Required element not found',$code);
	}
}

?>