<?php

class StoredTweet extends Tweet implements iTethysBase,Schedulable {
	
	/* PROPERTIES */
	
	private $id;
	private $uid;
	private $user;
	
	/* METHODS */
	
	public function __construct() {}
	public static function search() {
	}
	
	public static function getById($id) {
		if (!is_numeric($id) || $id < 1) {
			return false;
		}
		
		$tempArr = StoredTweet::search(array('id' => $id));
		
		return (is_array($tempArr)) ? $tempArr[0] : false;
	}
	
	public function __get($var) {
		return (isset($this->$var)) ? $this->$var : parent::__get($var);
	}
	
	public function __set() {
	}
	public static function add() {
	}
	public function delete() {
	}
	
	public function canEdit() {
	}
	
	public static function getList() {
	}
	public static function getNameById() {
	}
}

?>