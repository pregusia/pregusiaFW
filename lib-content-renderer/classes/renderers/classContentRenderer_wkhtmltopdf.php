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

class ContentRenderer_wkhtmltopdf implements IContentRenderer {

	const OPT_MARGIN_BOTTOM = '--margin-bottom';
	const OPT_MARGIN_TOP = '--margin-top';
	const OPT_MARGIN_LEFT = '--margin-left';
	const OPT_MARGIN_RIGHT = '--margin-right';
	const OPT_FOOTER_CENTER = '--footer-center';
	const OPT_HEADER_CENTER = '--header-center';
	
	private $path = '';
	private $args = array();
	
	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param Configuration $oConfig
	 */
	public function onInit($oContext, $oConfig) {
		$path = $oConfig->getPath('path');
		if (!$path) throw new ConfigEntryInvalidValueException('',sprintf('Path is empty in renderer of class ContentRenderer_wkhtmltopdf'));
		if (!file_exists($path)) throw new ConfigEntryInvalidValueException('',sprintf('File denoted by path not exists in renderer of class ContentRenderer_wkhtmltopdf'));
		
		$this->path = $path;
		$this->args = $oConfig->getArray('args');
	}
	
	//************************************************************************************
	/**
	 * @param string $content
	 * @param array $config
	 * @return string
	 */
	public function render($content, $config) {
		$args = $this->args;
		foreach($config as $k => $v) $args[$k] = $v;
		
		$cmdLine = array();
		$cmdLine[] = $this->path;
		
		foreach($args as $k => $v) {
			if ($v) {
				$cmdLine[] = $k;
				$cmdLine[] = sprintf('"%s"', $v);
			}
		}
		
		$cmdLine[] = '-q';
		
		
		$tmpFile = '/tmp/ContentRenderer_wkhtmltopdf_' . uniqid() . '.html';
		file_put_contents($tmpFile, $content);
		
		$cmdLine[] = $tmpFile;
		$cmdLine[] = '-';
		
		$descriptorSpec = array(
			0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("file", "/dev/null", "a") // stderr is a file to write to
		);
		$pipes = array();
		$process = proc_open(implode(" ",$cmdLine), $descriptorSpec, $pipes);
		
		if (is_resource($process)) {
			$stdInFd = $pipes[0];
			$stdOutFd = $pipes[1];

			$res = stream_get_contents($stdOutFd);
			
			fclose($stdInFd);
			fclose($stdOutFd);
			
			@unlink($tmpFile);
			return $res;
		} else {
			@unlink($tmpFile);
			throw new ContentRendererException('Could not spawn wkhtmltopdf');
		}
	}
	
}

?>