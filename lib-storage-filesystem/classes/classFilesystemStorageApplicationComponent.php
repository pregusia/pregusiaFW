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

class FilesystemStorageApplicationComponent extends ApplicationComponent {
	
	const STAGE = 40;

	/**
	 * @var FilesystemStorage[]
	 */
	private $storages = array();
	
	//************************************************************************************
	/**
	 * @return FilesystemStorage[]
	 */
	public function getStorages() { return $this->storages; }
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return FilesystemStorage
	 */
	public function getStorage($name) {
		return $this->storages[$name];
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() { return 'storage.fs'; }
	
	//************************************************************************************
	/**
	 * @return int[]
	 */
	public function getStages() {
		return array(self::STAGE);
	}
	
	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onInit($stage) {
		
	}

	//************************************************************************************
	/**
	 * @param int $stage
	 */
	public function onProcess($stage) {
		if ($stage == self::STAGE) {
			
			foreach($this->getConfig()->getRootArray() as $name => $path) {
				if (!is_dir($path)) {
					Logger::warn(sprintf('Could not create fs storage on %s - not exists', $path));
					continue;
				}
				
				$this->storages[$name] = new FilesystemStorage($name, $path);
			}
		}
	}
	
	
}

?>