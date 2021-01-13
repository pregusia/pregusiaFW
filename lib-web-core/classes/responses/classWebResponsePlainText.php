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


class WebResponsePlainText extends WebResponseContent {

	//************************************************************************************
	public function __construct($content='') {
		parent::__construct('text/plain; charset=UTF-8',$content);
	}
	
	//************************************************************************************
	/**
	 * @param Exception $e
	 * @return WebResponsePlainText
	 */
	public static function CreateExceptionReport($e) {
		$oResponse = new WebResponsePlainText();

		if ($e) {
			if ($e instanceof Exception) {
				$oResponse->appendLine("Uncaught exception occured");
				$oResponse->appendLine('');
				$oResponse->appendLine('');
				
				foreach(UtilsExceptions::toArray($e) as $k => $v) {
					$oResponse->appendLine('[' . $k . ']: ');
					$oResponse->appendLine(UtilsString::indent($v, $n, ' '));
					$oResponse->appendLine('');
				}
			} else {
				$oResponse->appendLine('[WebResponsePlainText::CreateExceptionReport] argument is not Exception');
			}
		} else {
			$oResponse->appendLine('[WebResponsePlainText::CreateExceptionReport] null argument');
		}
		
		return $oResponse;
	}
	
}

?>