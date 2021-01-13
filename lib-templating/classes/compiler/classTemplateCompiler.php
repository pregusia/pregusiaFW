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


class TemplateCompiler {
	
	//************************************************************************************
	private static function mapFunctions($code) {
		$res = preg_replace_callback('/([A-Za-z]{1}[A-Za-z0-9\_]*)\(/i', function($matches){
			$n = $matches[1];
			return sprintf('$this->callFunc("%s",', $n);
		}, $code);
		return str_replace(',)',')',$res);
	}
	
	//************************************************************************************
	public static function compile($templateCode) {
		$sOut = '';
		$parsingEnabled = true;
		$lineNr = 1;
		$state = array();
		
		foreach(explode("\n", $templateCode) as $sLine) {
			$sOut .= sprintf('$this->getState()->setLineNr(%d);' . "\n", $lineNr);
			
			foreach(preg_split( '/([\{]{1}[^\{\}\n]+[\}]{1})/', $sLine, -1, PREG_SPLIT_DELIM_CAPTURE ) as $token) {
				if ($token == '{enableparsing}' && !$parsingEnabled) {
					$parsingEnabled = true;
					continue;
				}
				if (substr($token,0,1) == '{' && $parsingEnabled) {
					$token = trim($token,'{}');
					$aToken = explode(' ', $token);
					$tokenName = strtolower(array_shift($aToken));

					if ($tokenName == 'foreach') {
						$state[] = '{foreach}';
						
						$sOut .= ' $this->getState()->removeRightNewLine(); ';
						$vars = array();
						foreach($aToken as $sParam) {
							list($paramName, $paramValue) = explode('=', $sParam);
							$vars[trim($paramName)] = trim($paramValue);
						}
						if ($vars['from'] && $vars['item']) {
							$vars['from'] = self::mapFunctions($vars['from']);
							if ($vars['key']) {
								if ($vars['index']) {
									$sOut .= sprintf('if(%s && is_array(%s)) { %s=0; foreach(%s as %s => %s) { ++%s; ',
										$vars['from'],$vars['from'], $vars['index'], $vars['from'], $vars['key'], $vars['item'],$vars['index']);
								} else {
									$sOut .= sprintf('if(%s && is_array(%s)) { foreach(%s as %s => %s) { ', $vars['from'], $vars['from'], $vars['from'], $vars['key'], $vars['item']);
								}
							} else {
								if ($vars['index']) {
									$sOut .= sprintf('if(%s && is_array(%s)) { %s=0; foreach(%s as %s) { ++%s; ',
										$vars['from'],$vars['from'], $vars['index'], $vars['from'], $vars['item'],$vars['index']);
								} else {
									$sOut .= sprintf('if(%s && is_array(%s)) { foreach(%s as %s) { ', $vars['from'], $vars['from'], $vars['from'], $vars['item']);
								}
							}
						}
					}
					elseif ($tokenName == 'elseforeach') {
						if (UtilsArray::getLast($state) != '{foreach}') {
							throw new TemplateException('', $lineNr, 'Invalid token {elseforeach}');
						}
						
						array_pop($state);
						$state[] = '{elseforeach}';
						
						$sOut .= sprintf(' }} else {{ ');
					}
					elseif ($tokenName == 'endforeach') {
						if (UtilsArray::getLast($state) != '{foreach}' && UtilsArray::getLast($state) != '{elseforeach}') {
							throw new TemplateException('', $lineNr, 'Invalid token {endforeach}');
						}
						array_pop($state);
						$sOut .= sprintf(' $this->getState()->removeRightNewLine(); }} ');
					}
					elseif ($tokenName == 'if') {
						$sOut .= sprintf(' $this->getState()->removeRightNewLine(); if (%s) { ', self::mapFunctions(implode(' ', $aToken)));
						$state[] = '{if}';
					}
					elseif ($tokenName == 'elseif') {
						if (UtilsArray::getLast($state) != '{if}' && UtilsArray::getLast($state) != '{elseif}') {
							throw new TemplateException('', $lineNr, 'Invalid token {elseif}');
						}
						array_pop($state);
						$state[] = '{elseif}';
						$sOut .= sprintf(' } elseif (%s) { ', self::mapFunctions(implode(' ', $aToken)));
					}
					elseif ($tokenName == 'else') {
						if (UtilsArray::getLast($state) != '{if}' && UtilsArray::getLast($state) != '{elseif}') {
							throw new TemplateException('', $lineNr, 'Invalid token {else}');
						}
						array_pop($state);
						$state[] = '{else}';
						$sOut .= sprintf(' } else { ');
					}
					elseif ($tokenName == 'endif') {
						if (UtilsArray::getLast($state) != '{if}' && UtilsArray::getLast($state) != '{elseif}' && UtilsArray::getLast($state) != '{else}') {
							throw new TemplateException('', $lineNr, 'Invalid token {endif}');
						}
						array_pop($state);
						$sOut .= sprintf(' } $this->getState()->removeRightNewLine(); ');
					}
					elseif ($tokenName == 'include') {
						$l = $aToken[0];
						if ($l) {
							if (strpos($l, ':') === false) {
								$l = 'local:' . $l;
							}
							$sOut .= sprintf(' $this->getState()->push($this->getComponent()->renderTemplateFromLocation("%s", $AllTemplateVars)); ', $l);
						}
					}
					elseif ($tokenName == 'disableparsing') {
						$parsingEnabled = false;
					}
					else {
						// do wyswietlenia
						$sOut .= sprintf('$this->getState()->push(%s);', self::mapFunctions($token));
					}

				} else {
					$token = str_replace( array("\t","\n",'"','$',"\r"), array('\t','\n','\"','\$',''), $token );
					$sOut .= sprintf('$this->getState()->push("%s");', $token);
				}
			}
			$sOut .= sprintf('$this->getState()->push("\n");', $token);
			$sOut .= "\n";
			$lineNr += 1;
		}
		
		if ($state) {
			throw new TemplateException('', $lineNr, 'Not all tokens ended - last is ' . UtilsArray::getLast($state));
		}

		return $sOut;
	}	
	
}

?>