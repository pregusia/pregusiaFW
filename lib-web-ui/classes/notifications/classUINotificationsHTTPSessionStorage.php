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


class UINotificationsHTTPSessionStorage {

	const LIMIT = 5;
	
	/**
	 * @var HTTPSession
	 */
	private $oSession = null;
	
	//************************************************************************************
	/**
	 * @return HTTPSession
	 */
	public function getSession() { return $this->oSession; }
	
	//************************************************************************************
	public function __construct($oSession) {
		if ($oSession) {
			if (!($oSession instanceof HTTPSession)) throw new InvalidArgumentException('oSession is not HTTPSession');
		}
		$this->oSession = $oSession;
	}
	
	//************************************************************************************
	/**
	 * @return UINotification[]
	 */
	public function getAll() {
		$res = array();
		$arr = array();
		
		if ($this->getSession()) {
			$arr = @json_decode($this->getSession()->get('UINotificationsHTTPSessionStorage'), true);
		} 
		
		if (is_array($arr)) {
			foreach($arr as $v) {
				if ($oNotification = UINotification::jsonUnserialize($v)) {
					$res[] = $oNotification;
				}
			}
		}
		
		return $res;
	}
	
	//************************************************************************************
	/**
	 * @param UINotification[] $notifications
	 */
	private function save($notifications) {
		$arr = array();
		$num = 0;
		foreach($notifications as $oNotification) {
			$arr[] = $oNotification->jsonSerialize();
			$num += 1;
			if ($num >= self::LIMIT) break;
		}
		if ($this->getSession()) {
			$this->getSession()->set('UINotificationsHTTPSessionStorage', json_encode($arr));
		}
	}
	
	//************************************************************************************
	public function add($oNotification) {
		if (!($oNotification instanceof UINotification)) throw new InvalidArgumentException('oNotification is not UINotification');
		$arr = $this->getAll();
		$arr[] = $oNotification;
		$this->save($arr);
	}
	
	//************************************************************************************
	public function clear() {
		$this->save(array());
	}
	
}