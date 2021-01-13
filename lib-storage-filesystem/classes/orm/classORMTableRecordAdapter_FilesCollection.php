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

/**
 * Adapter do przechowywania wielu plikow
 * 
 * @author pregusia
 * @NeedLibrary lib-orm
 */
class ORMTableRecordAdapter_FilesCollection extends ORMTableRecordAdapter implements IFilesystemStorageContainer {
	
	/**
	 * @var FilesystemStorage
	 */
	private $oStorage = null;
	
	private $operations = array();
	
	//************************************************************************************
	/**
	 * @return FilesystemStorage
	 */
	public function getStorage() { return $this->oStorage; }

	//************************************************************************************
	/**
	 * @param ORMTableRecord $oRecord
	 * @param string $storageName
	 */
	public function __construct($oRecord, $storageName) {
		parent::__construct($oRecord);
		
		$this->oStorage = ApplicationContext::getCurrent()->getComponent('storage.fs')->getStorage($storageName);
		if (!$this->oStorage) throw new InvalidArgumentException(sprintf('Could not find FilesystemStorage with name %s', $storageName));
		
		if (true) {
			$self = $this;
			$oRecord->getEvents(ORMTableRecord::EVENTS_AFTER_ADD)->add(function($oRecord) use ($self) { $self->doSave(); });
			$oRecord->getEvents(ORMTableRecord::EVENTS_AFTER_UPDATE)->add(function($oRecord) use ($self) { $self->doSave(); });		
			$oRecord->getEvents(ORMTableRecord::EVENTS_AFTER_DELETE)->add(function($oRecord) use ($self) { $self->doDelete(); });
		}
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	private function getContainerName() {
		$pk = $this->getRecord()->getPrimaryKeyField()->get();
		if ($pk) {
			return sprintf('%s.%d', $this->getRecord()->getTable()->getRecordClass()->getName(), $pk);
		}
		return '';
	}
	
	//************************************************************************************
	/**
	 * @return FilesystemStorageContainer
	 */
	private function getContainer() {
		$name = $this->getContainerName();
		if ($name) {
			return $this->getStorage()->ensureContainer($name);
		}
		return null;
	}
	
	//************************************************************************************
	public function doSave() {
		if ($this->operations) {
			if ($oContainer = $this->getContainer()) {
				
				foreach($this->operations as $op) {
					if ($op['type'] == 'set') {
						$oContainer->putContents($op['name'], $op['contents']);
					}
					if ($op['type'] == 'remove') {
						$oContainer->remove($op['name']);
					}
				}
				
			}
		}
		
		$this->operations = array();
	}
	
	//************************************************************************************
	public function doDelete() {
		if ($name = $this->getContainerName()) {
			$this->getStorage()->removeContainer($name);
		}
	}
	
	//************************************************************************************
	/**
	 * @return string[]
	 */
	public function listFiles() {
		$arr = array();
		$toRemove = array();
		
		foreach($this->operations as $op) {
			if ($op['type'] == 'remove') {
				$toRemove[] = $op['name'];
			}
			if ($op['type'] == 'set') {
				$arr[] = $op['name'];
			}
		}
		
		if ($oContainer = $this->getContainer()) {
			foreach($oContainer->listFiles() as $file) {
				if (!in_array($file, $toRemove)) {
					$arr[] = $file;
				}
			}
		}
		
		return $arr;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return bool
	 */
	public function contains($name) {
		$name = FilesystemStorageContainer::prepareFileName($name);
		return in_array($name, $this->listFiles());
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @param string $contents
	 */
	public function putContents($name, $contents) {
		$name = FilesystemStorageContainer::prepareFileName($name);
		if (!$name) throw new InvalidArgumentException('Empty name given');
		
		$this->operations[] = array(
			'type' => 'set',
			'name' => $name,
			'contents' => $contents	
		);
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return string
	 */
	public function getContents($name) {
		foreach(array_reverse($this->operations) as $op) {
			if ($op['name'] == $name) {
				if ($op['type'] == 'remove') {
					throw new IOException(sprintf('Could not find file %s', $name));
				}
				if ($op['type'] == 'set') {
					return $op['contents'];
				}
			}
		}
		
		if ($oContainer = $this->getContainer()) {
			if ($oContainer->contains($name)) {
				return $oContainer->getContents($name);
			}
		}
		
		throw new IOException(sprintf('Could not find file %s', $name));
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 */
	public function remove($name) {
		$this->operations[] = array(
			'type' => 'remove',
			'name' => $name	
		);
	}
	
	//************************************************************************************
	public function removeAll() {
		$this->operations = array();
		if ($oContainer = $this->getContainer()) {
			foreach($oContainer->listFiles() as $name) {
				$this->operations[] = array(
					'type' => 'remove',
					'name' => $name
				);
			}
		}		
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getPath() {
		if ($oContainer = $this->getContainer()) {
			return $oContainer->getPath();
		}
		return '';
	}
	
}

?>