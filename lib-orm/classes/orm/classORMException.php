<?php

class ORMException extends SQLException {
	
	//************************************************************************************
	public function __construct($message='') {
		parent::__construct($message);
	}
	
}

?>