<?php

class Campaign implements iTethysBase,Schedulable {
	
	/* PROPERTIES */
	
	private $id;
	private $uid;
	private $userId;
	private $name;
	private $description;
	private $type;
	private $status;
	private $dateStart;
	private $dateEnd;
	
	public function __construct() {}
	public static function search() {}
	public static function getById() {}
	public function __get() {}
	public function __set() {}
	public static function add() {}
	public function delete() {}
	public function archive() {}
	public function setName() {}
	public function setStartDate() {}
	public function setDescription() {}
	public function setEndDate() {}
	public function setType() {}
	public function canEdit() {}
	
	public function addList() {}
	public function removeList() {}
	public function addUrl() {}
	public function removeUrl() {}
	private function addSource() {}
	private function removeSource() {}
	
	public static function getList() {}
	public static function getNameById() {}
}
?>