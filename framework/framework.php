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

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

require_once __DIR__ . '/exceptions/exceptions.php';
require_once __DIR__ . '/exceptions/classUtilsExceptions.php';

require_once __DIR__ . '/config/classConfiguration.php';
require_once __DIR__ . '/config/classConfigurationException.php';
require_once __DIR__ . '/config/classConfigEntryInvalidValueException.php';

require_once __DIR__ . '/codebase/classCodeBaseAnnotation.php';
require_once __DIR__ . '/codebase/classCodeBaseAnnotationsCollection.php';
require_once __DIR__ . '/codebase/classCodeBaseException.php';
require_once __DIR__ . '/codebase/classTypeNotFoundException.php';
require_once __DIR__ . '/codebase/classCodeBaseDeclaredType.php';
require_once __DIR__ . '/codebase/classCodeBaseDeclaredClass.php';
require_once __DIR__ . '/codebase/classCodeBaseDeclaredInterface.php';
require_once __DIR__ . '/codebase/classCodeBaseDeclaredTrait.php';
require_once __DIR__ . '/codebase/classCodeBaseLibraryResource.php';
require_once __DIR__ . '/codebase/classCodeBaseLibrary.php';
require_once __DIR__ . '/codebase/interfaceIClassInstantinatorAdapter.php';
require_once __DIR__ . '/codebase/classCodeBase.php';

require_once __DIR__ . '/application/classApplicationContext.php';
require_once __DIR__ . '/application/classApplicationComponent.php';
require_once __DIR__ . '/application/interfaceIApplicationComponentExtension.php';
require_once __DIR__ . '/application/interfaceIApplicationAutoLocalService.php';

require_once __DIR__ . '/traits.php';
require_once __DIR__ . '/classDebugger.php';
require_once __DIR__ . '/classLogger.php';




function __autoload($typeName) {
	CodeBase::LoadType($typeName);
}



class Framework {
	
	//************************************************************************************
	public static function fatalErrorFallbackDisplay($v) {
		// nie mozemy uzyc tutaj zadnych z funkcji, bo jeszcze wsio nie jest stworzone
		// musimy w tradycyjny sposob
		header('Content-Type: text/plain');
		http_response_code(500);

		if ($v instanceof Exception) {
			$arr = UtilsExceptions::toArray($v);
			$res = '';
			foreach($arr as $k => $v) {
				$res .= sprintf("%s:\n", $k);
				
				foreach(explode("\n",$v) as $a) {
					$res .= sprintf("     %s\n", $a);
				}
				
				$res .= "\n\n";
			}
		} else {
			$res = strval($v);
		}
		
		error_log($res);
		printf("%s\n",$res);
		exit(1);
	}
	
	//************************************************************************************
	public static function Init() {
		$path = rtrim(getcwd(),'/') . '/libraries/';
		if (!is_dir($path)) throw new FrameworkException(sprintf('Path %s is invalid', $path));
		
		foreach(glob($path . '*') as $file) {
			CodeBase::LoadLibrary($file);
		}
		
		CodeBase::Sort();
		CodeBase::CheckRequirements();
	}
	
	//************************************************************************************
	public static function Run($tags = array()) {
		$oContext = new ApplicationContext($tags);

		$oContext->forEachComponent(function($stage, $oComponent) use($oContext) {
			if ($oContext->shouldProcess($oComponent)) {
				$oComponent->onProcess($stage);
			}
		});
		
		exit(0);
	}
	
} 
	
?>