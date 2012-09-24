<?php

abstract class Tweet implements iTethysBase {

	/* PROPERTIES */

	private $twitterId;
	private $date;
	private $status;
	private $lat;
	private $long;
	
	// returns latest tweet data stored in db (may be stale)
	final public function getTweet() {
		
	}
	
	// retrieve updated tweet stats from twitter
	final public function pullTweet() {
		
	}
	
	// stores twitter response in db
	final private function saveTweet() {
		
	}
}
?>