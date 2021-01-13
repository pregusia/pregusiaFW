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
 * Rozdziela zapytania typu select/update na dwa osobne wezly clustra
 * TODO: do zrobienia
 * @author pregusia
 *
 */
class SQLConnector_mysqli_cluster implements ISQLConnector {

	
	/**
	 * @var mysqli
	 */
	private $db = null;
	
	//************************************************************************************
	public function isConnected() {
		return $this->db ? true : false;
	}
	
	//************************************************************************************
	public function start($config) {
		if ($this->db) throw new IllegalStateException('Already connected');
		
		if (!$config['host']) throw new ConfigEntryInvalidValueException('host', 'Storage configuration entry "host" has invalid value');
		if (!$config['user']) throw new ConfigEntryInvalidValueException('user', 'Storage configuration entry "user" has invalid value');
		if (!$config['database']) throw new ConfigEntryInvalidValueException('database', 'Storage configuration entry "database" has invalid value');
		
		mysqli_report(MYSQLI_REPORT_OFF);
		
		$this->db = new mysqli();
		$this->db->report_mode = MYSQLI_REPORT_OFF;
		@$this->db->connect(
			$config['host'],
			$config['user'],
			$config['password'],
			$config['database'],
			isset($config['port']) ? intval($config['port']) : null
		);
		
		if ($this->db->connect_error) {
			throw new SQLException(sprintf('Could not connect to SQLStorage - %s', $this->db->connect_error));
		}
		
		$this->db->query("SET CHARACTER SET 'utf8'");
		$this->db->query("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");
		
		// conn timezone
		if (true) {
			$n = new DateTime();
			$mins = $n->getOffset() / 60;
			
			$sgn = ($mins < 0 ? -1 : 1);
			$mins = abs($mins);
			$hrs = floor($mins / 60);
			$mins -= $hrs * 60;

			$this->db->query(sprintf("SET time_zone='%+d:%02d'", $hrs*$sgn, $mins));
		}
	}
	
	//************************************************************************************
	public function stop() {
		if ($this->db) {
			$this->db->close();
		}
		$this->db = null;
	}
	
	//************************************************************************************
	public function getLastInsertID() {
		if (!$this->db) throw new IllegalStateException('Not connected');
		return $this->db->insert_id;
	}
	
	//************************************************************************************
	public function getAffectedRows() {
		if (!$this->db) throw new IllegalStateException('Not connected');
		return $this->db->affected_rows;
	}
	
	//************************************************************************************
	/**
	 * @param string $query
	 * @return ISQLResultsSet
	 */
	public function query($query) {
		$query = trim($query);
		if (!$query) throw new InvalidArgumentException('query is empty');
		if (!$this->db) throw new IllegalStateException('Not connected');
		
		$res = $this->db->query($query);
		
		if ($this->db->error) {
			switch($this->db->errno) {
				case 1062: throw new SQLQueryUniqueException($this->db->errno, $this->db->error, $query);
				case 1452: throw new SQLQueryForeignKeyException($this->db->errno, $this->db->error, $query);
				default: throw new SQLQueryErrorException($this->db->errno, $this->db->error, $query);
			}
		}
		
		return new SQLConnector_mysqli_resultsset($this, $res);
	}
	
	//************************************************************************************
	/**
	 * Escapuje podana wartosc na potrzeby uzycia w zapytaniu SQL
	 * @param string $val
	 * @return string
	 */
	public function escapeString($val) {
		if (!$this->db) throw new IllegalStateException('Not connected');
		return $this->db->escape_string($val);
	}
	
	//************************************************************************************
	/**
	 * Escapuje podana wartosc na potrzeby uzycia w zapytaniu SQL
	 * @param string $val
	 * @return string
	 */
	public function escapeBinary($val) {
		if (!$this->db) throw new IllegalStateException('Not connected');
		
		$len = strlen($val);
		if ($len > 0) {
			$res = '';
			for($i=0;$i<$len;++$i) {
				$ch = ord(substr($val,$i,1));
				$res .= sprintf('%02X', $ch);
			}
			return '0x' . $res;
		} else {
			return '0x0';
		}
	}
	
	//************************************************************************************
	public function autocommit($enabled) {
		if (!$this->db) throw new IllegalStateException('Not connected');
		$this->db->autocommit($enabled);
	}
	
	//************************************************************************************
	public function commit() {
		if (!$this->db) throw new IllegalStateException('Not connected');
		$this->db->commit();
	}
	
	//************************************************************************************
	public function rollback() {
		if (!$this->db) throw new IllegalStateException('Not connected');
		$this->db->rollback();
	}
	
}

?>