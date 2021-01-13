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

class HTTPMethod extends Enum {
	
	const UNKNOWN = 0;
	const GET = 1;
	const POST = 2;
	const PUT = 3;
	const HEAD = 4;
	const DELETE = 5;
	const PATCH = 6;	

	//************************************************************************************
	public function __construct() {
		parent::__construct(array(
			self::UNKNOWN => 'UNKNOWN',
			self::GET => 'GET',
			self::POST => 'POST',
			self::PUT => 'PUT',
			self::HEAD => 'HEAD',
			self::DELETE => 'DELETE',
			self::PATCH => 'PATCH'
		));
	}	
	
}

?>