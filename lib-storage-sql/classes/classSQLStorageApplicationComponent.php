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


class SQLStorageApplicationComponent extends ApplicationComponent {
	
	/**
	 * @var SQLTypesMapper
	 */
	private $oTypesMapper = null;
	
	/**
	 * @var SQLStorage[]
	 */
	private $storages = array();
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getName() {
		return 'storage.sql';
	}
	
	//************************************************************************************
	/**
	 * @return int[]
	 */
	public function getStages() {
		return array(20);
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return SQLStorage
	 */
	public function getStorage($name) {
		return $this->storages[$name];
	}
	
	//************************************************************************************
	/**
	 * @return SQLTypesMapper
	 */
	public function getTypesMapper() {
		return $this->oTypesMapper;
	}
	
	//************************************************************************************
	/**
	 * @return SQLStorage[]
	 */
	public function getStorages() {
		return $this->storages;
	}
	
	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param int $stage
	 */
	public function onInit($stage) {
		CodeBase::ensureLibrary('lib-utils', 'lib-storage-sql');
		$this->oTypesMapper = $this->registerService('SQLTypesMapper', '', new SQLTypesMapper());
		
		if (true) {
			foreach($this->getConfig()->getRootArray() as $name => $config) {
				try {
					$oStorage = SQLStorage::FromConfig($this, $name, $config);
					if ($oStorage) {
						$this->storages[$oStorage->getName()] = $oStorage;
						$this->registerService('SQLStorage', $name, $oStorage);
					}
				} catch(Exception $e) {
					Logger::warn(sprintf('Could not instantinate SQLStorage %s', $name), $e);
				}
			}
		}
	}

	//************************************************************************************
	/**
	 * @param ApplicationContext $oContext
	 * @param int $stage
	 */
	public function onProcess($stage) {
		
	}
	
}

?>