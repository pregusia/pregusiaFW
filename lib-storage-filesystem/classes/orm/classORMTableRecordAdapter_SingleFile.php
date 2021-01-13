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
 * Adapter do przechowywania pojedynczego pliku
 * 
 * @author pregusia
 * @NeedLibrary lib-orm
 */
class ORMTableRecordAdapter_SingleFile extends ORMTableRecordAdapter {
	
	/**
	 * @var FilesystemStorage
	 */
	private $oStorage = null;
	
	private $contents = '';
	private $changed = false;
	private $loaded = false;
	
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
		if ($this->changed) {
			if ($this->contents) {
				$oContainer = $this->getContainer();
				if ($oContainer) {
					$oContainer->putContents('data', $this->contents);
				}
			} else {
				$this->getStorage()->removeContainer($this->getContainerName());
			}
		}
		
		$this->changed = false;
		$this->loaded = true;
	}
	
	//************************************************************************************
	public function doDelete() {
		if ($name = $this->getContainerName()) {
			$this->getStorage()->removeContainer($name);
		}
	}
	
	//************************************************************************************
	public function doLoad() {
		$oContainer = $this->getContainer();
		if ($oContainer) {
			if ($oContainer->contains('data')) {
				$this->contents = $oContainer->getContents('data');
			}
		}
		
		$this->loaded = true;
		$this->changed = false;
	}
	
	//************************************************************************************
	/**
	 * @return bool
	 */
	public function hasContents() {
		if (!$this->loaded) $this->doLoad();
		return $this->contents ? true : false;
	}
	
	//************************************************************************************
	/**
	 * @return string
	 */
	public function getContents() {
		if (!$this->loaded) $this->doLoad();
		return $this->contents;
	}
	
	//************************************************************************************
	/**
	 * @param string $contents
	 */
	public function setContents($contents) {
		$this->changed = true;
		$this->loaded = true;
		$this->contents = strval($contents);
	}
	
	//************************************************************************************
	public function clearContents() {
		$this->changed = true;
		$this->loaded = true;
		$this->contents = '';
	}
	
}

?>