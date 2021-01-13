<?php

interface ICacheMechanism {
	
	/**
	 * @return ApplicationContext
	 */
	public function getApplicationContext();
	
	/**
	 * @return string
	 */
	public function getScope();
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function delete($key);

	/**
	 * @return int
	 */
	public function clear();
	
	/**
	 * @param string $key
	 * @param int $ttl
	 * @param Closure $oGenerator
	 * @return string
	 */
	public function get($key, $ttl=0, $oGenerator=null);
	
	/**
	 * @param string $key
	 * @param int $ttl
	 * @return bool
	 */
	public function contains($key, $ttl=0);
	
	/**
	 * @param string $key
	 * @param string $value
	 * @param int $ttl
	 */
	public function set($key, $value, $ttl=0);
	
}

?>