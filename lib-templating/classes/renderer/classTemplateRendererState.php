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


class TemplateRendererState {
	
	private $out = '';
	private $lineNr = 0;
	private $fileName = '';
	
	//************************************************************************************
	public function getFileName() { return $this->fileName; }
	public function getOut() { return $this->out; }
	public function getLineNr() { return $this->lineNr; }
		
	//************************************************************************************
	public function __construct($fileName) {
		$this->fileName = $fileName;
	}
	
	//************************************************************************************
	public function push($str) {
		$this->out .= UtilsString::toString($str);
	}
	
	//************************************************************************************
	public function setLineNr($nr) {
		$this->lineNr = $nr;
	}

	// ************************************************************************************
	public function removeRightNewLine() {
		if (substr($this->out,-1,1) == "\n") {
			$this->out = substr($this->out,0,strlen($this->out)-1);
		}
	}
	
}

?>