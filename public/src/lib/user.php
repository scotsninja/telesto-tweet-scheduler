<?php

class User {

	/* PROPERTIES */
	
	private $id;
	private $email;
	private $twitterId;
	private $twitterName;
	private $defaultLat;
	private $defaultLong;
	private $dateAuthorized;
	private $active;
	
	private $authToken;
	
	/* METHODS */
	
	public function __construct($id = 0, $email = null, $twitterId = 0, $twitterName = null, $defaultLat = null, $defaultLong = null, $dateAuthorized = null, $active = 0) {
		$this->id = $id;
		$this->email = $email;
		$this->twitterId = $twitterId;
		$this->twitterName = $twitterName;
		$this->defaultLat = $defaultLat;
		$this->defaultLong = $defaultLong;
		$this->dateAuthorized = $dateAuthorized;
		$this->active = $active;
		
		$this->authToken = $this->getAuthToken();
	}
	
	/* SEARCH */
	
	// search user accounts based on (id, email, level)
	// returns an array of User objects
	public static function search(array $params = null, $perPage = 20, $pageNum = 1, &$totalResults = 0, $sort = 'userTwitterName ASC') {
		// if search parameters are set, validate the specific parameters
		if (is_array($params)) {
			if (isset($params['id']) && is_numeric($params['id']) && $params['id'] > 0) {
				$whereParams['id'] = $params['id'];
				$where[] = "`userId`=:id";
			}
			if (isset($params['email'])) {
				$whereParams['email'] = $params['email'];
				$where[] = "`userEmail`=:email";
			}
			if (isset($params['active'])) {
				$whereParams['active'] = $params['active'];
				$where[] = "`userActive`=:active";
			}
		}
		
		// build the where clause
		$whereClause = addQueryWhere($where);
		
		// build the search query
		$totQ = "SELECT COUNT(`userId`) AS result FROM `users`";
		$query = "SELECT `userId` AS id, ".
				"`userEmail` AS email, ".
				"`userTwitterId` AS tid, ".
				"`userTwitterName` AS tname, ".
				"`userDefaultLat` AS lat, ".
				"`userDefaultLong` AS long, ".
				"`userDateAuthorized` AS da, ".
				"`userActive` AS active ".
				"FROM `users`";
				
		$query .= $whereClause . addQuerySort($sort) . addQueryLimit($perPage, $pageNum);
		$totQ .= $whereClause;

		// execute the query
		$results = $GLOBALS['dbObj']->select($query, $whereParams);
		
		if (!$results) {
			$totalResults = 0;
			return false;
		}

		$totalResults = $GLOBALS['dbObj']->fetchResult($totQ, $whereParams);
		
		foreach ($results as $obj) {
			$resultArr[] = new User($obj['id'], $obj['email'], $obj['tid'], $obj['tname'], $obj['lat'], $obj['long'], $obj['da'], $obj['active']);
		}
		
		return $resultArr;
	}
	
	// returns User object associated to specific userId
	public static function getById($id) {
		if (!is_numeric($id) || $id < 1) {
			return false;
		}
		
		$tempArr = User::search(array('id' => $id));

		return (is_array($tempArr)) ? $tempArr[0] : false;
	}
	
	// returns User object associated to specific email
	public static function getByEmail($email) {
		if ($email == '' || !isValidEmail($email)) {
			return false;
		}
		
		$tempArr = User::search(array('email' => $email));

		return (is_array($tempArr)) ? $tempArr[0] : false;
	}
	
	/* CREATE/EDIT/DELETE */
	
	public function __get($var) {
		return (isset($this->$var)) ? $this->$var : parent::__get($var);
	}
	
	// creates a new user account
	// only admins can use this function; use User::register() when creating new accounts for active users
	public static function register($email, $tId, $tName, $accessKey = null) {
		// validate the account information
		if (!isValidEmail($email)) {
			//throw new Exception('Username must be a valid email address');
			SystemMessage::save(MSG_WARNING, 'Invalid email address');
			$fail[] = true;
		}
		if ($tId == '') {
			$fail[] = true;
		}
		if ($tName == '') {
			$fail[] = true;
		}
		
		if (CORE_REQUIRE_ACCESS_KEY) {
			if (!isValidAccessKey($accessKey)) {
				SystemMessage::save(MSG_WARNING, 'Access key not valid');
				$fail[] = true;
			}
		}
		
		if (is_array($fail) && in_array(true, $fail)) {
			return false;
		}
		
		// insert user info into db
		$query = "INSERT INTO `users` (`userEmail`, `userTwitterId`, `userTwitterName`, `userDateAuthorized`, `userActive`) VALUES (:email, :tid, :tname, :date, :active)";

		$params = array(
			'email' => $email,
			'tid' => $tId,
			'name' => $tName,
			'date' => $GLOBALS['dtObj']->format('now',DATE_SQL_FORMAT),
			'active' => 1
		);

		try {
			return $GLOBALS['dbObj']->insert($query, $params);
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	// remove a user account
	public function delete() {
		if (!$this->canEdit(__FUNCTION__)) {
			throw new Exception('You do not have permission to perform that action');
		}
		
		$query = "DELETE FROM `users` WHERE `userId`=:id";
		$params = array('id' => $this->id);

		try {
			return $GLOBALS['dbObj']->delete($query, $params);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	// set the user level of the specific user account
	public function setLocation($lat = null, $long = null) {
		if (!$this->canEdit(__FUNCTION__)) {
			throw new Exception('You do not have permission to modify this account');
		}
		
		// build the query and pass to the db
		$query = "UPDATE `users` SET `userDefaultLat`=:lat, `userDefaultLong`=:long WHERE `userId`=:id";
		$params = array('id' => $this->id,
			'lat' => $lat,
			'long' => $long
		);
		
		try {
			return $GLOBALS['dbObj']->update($query, $params);
		} catch(Exception $e) {
			throw new Exceptino('Error updating user level');
		}
	}
	
	/* LOGIN/LOGOUT */
	
	// takes the userId stored in the session, loads the associated user and performs verification functions
	// returns User object if verification passes
	// otherwise, returns false
	public static function loadBySession() {
		if (!(is_numeric($_SESSION['userId']) && $_SESSION['userId'] > 0)) {
			return false;
		}
		
		$userObj = User::getById($_SESSION['userId']);

		if (!$userObj) {
			clearSession();
			return false;
		}
		
		// generate hash to verify user is correct user
		$currentUserHash = md5($_SERVER['HTTP_USER_AGENT']);

		try {
			$query = "SELECT `uaClient` AS result FROM `user_activity` WHERE `uaAction`=:action AND `uaUserId`=:user".addQuerySort('uaDate DESC').addQueryLimit(1);
			$params = array('action' => 'success', 'user' => $userObj->id);
			
			$client = $GLOBALS['dbObj']->fetchResult($query, $params);
			
			if (!$client) {
				throw new Exception('Could not retrieve user client information');
			}
			
			$originalUserHash = md5($client);
		} catch(Exception $e) {
			SystemMessage::log(MSG_ERROR, 'Error validating user account: ' . $e->getMessage());
			clearSession();
			unset($userObj);
			return false;
		}

		if ($currentUserHash == $originalUserHash) {
			return $userObj;
		} else {
			clearSession();
			unset($userObj);
			return false;
		}
	}
	
	public static function login($twitterId) {
		if (!is_numeric($twitterId) || $twitterId < 1) {
			return false;
		}
		
		$query = "SELECT `userId` AS id, ".
				"`userEmail` AS email, ".
				"`userTwitterId` AS tid, ".
				"`userTwitterName` AS tname, ".
				"`userDefaultLat` AS lat, ".
				"`userDefaultLong` AS long, ".
				"`userDateAuthorized` AS da, ".
				"`userActive` AS active ".
				"FROM `users` WHERE `userTwitterId`=:tid";
		$params = array('tid' => $twitterId);
		
		try {
			$results = $GLOBALS['dbObj']->select($query, $params);
			
			if (!$results) {
				throw new Exception('No results returned');
			}
			
			$obj = $results[0];
			$tempUser = new User($obj['id'], $obj['email'], $obj['tid'], $obj['tname'], $obj['lat'], $obj['long'], $obj['da'], $obj['active']);
			
			$_SESSION['userId'] = $tempUser->id;
			
			User::recordLogin($tempUser->email, $tempUser->id, 'success');
			
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/* PERMISSIONS */
	
	// returns true if active user has permission to perform passed function
	public function canEdit($function = null) {
		if (!User::isLoggedIn()) {
			return false;
		}
		
		switch($function) {
			// admin-only
			case 'setActive':
			case 'delete':
				$ret = false;
			break;
			// is admin or the associated user 
			case 'setLocation':
			default:
				$ret = ($this->id == $GLOBALS['userObj']->id);
			break;
		}
		
		return $ret;
	}
	
	private function getAuthToken() {
		$query = "SELECT `uaAuthToken` AS result FROM `user_activity` WHERE `uaUserId`=:id AND `uaAction`=:action".addQuerySort('uaDate DESC').addQueryLimit(1);
		$params = array('id' => $this->id, 'action' => 'success');
	
		return $GLOBALS['dbObj']->fetchResult($query, $params);
	}
	
	/* UTILITIES */
	
	public static function getNameById($id) {
		return null;
	}
	
	public static function getList($sort = 'userEmail', $showInactive = false) {
		return null;
	}
	
	// returns true if the active user is logged in
	public static function isLoggedIn() {
		return ($GLOBALS['userObj'] && ($GLOBALS['userObj'] instanceof User));
	}
	
	// returns true if the active user is an admin (or super-admin)
	public static function isAdmin() {
		return false;
	}
	
	// set the minimum user level required to view the page
	// $redirect sets the page to redirect the user to a specific page
	// $message is the error message displayed to the user after the redirect
	public static function requireLogin($level = null, $redirect = null, $message = null) {
		if ($redirect == '' || !file_exists($redirect)) {
			$redirect = '/login.php';
			$_SESSION['redirect'] = urlencode($_SERVER['PHP_SELF']);
		}
		
		if ($message == '') {
			$message = 'You do not have permission to view that page.';
		}
		
		if ($level != '') {
			switch ($level) {
				case 'super-admin':
					$pass = $GLOBALS['userObj']->level == 'super-admin';
				break;
				case 'admin':
					$pass = User::isAdmin();
				break;
				case 'moderator':
					$pass = $GLOBALS['userObj']->level != 'user';
				break;
				case 'user':
				default:
					$pass = User::isLoggedIn();
				break;
			}
		}
		
		if ($pass) {
			return true;
		} else {
			if ($message != '') {
				SystemMessage::save(MSG_ERROR, $message);
			}
			
			header('Location: ' . $redirect);
			exit();
		}
	}
	
	// returns the date of the last successful user login
	// or, false if no logins are recorded
	public function getLastLogin() {
		$query = "SELECT `uaDate` AS result FROM `user_activity` WHERE `uaUserId`=:id ORDER BY `uaDate` DESC LIMIT 1";
		$params = array('id' => $this->id);
		
		try {
			return $GLOBALS['dbObj']->fetchResult($query, $params);
		} catch(Exception $e) {
			return null;
		}
	}
	
	// store user information at time of login
	public static function recordLogin($uname = null, $uid = 0, $action = 'failure') {
		if (!is_numeric($uid) || $uid < 1) { $action = 'failure'; }
		$authToken = ($action == 'success') ? md5('Epimetheus'.$_SERVER['HTTP_USER_AGENT'].$uid) : null;
		
		$query = "INSERT INTO `user_activity` (`uaAction`, `uaUserId`, `uaUsername`, `uaIPAddress`, `uaClient`, `uaAuthToken`, `uaDate`) VALUES (:action, :id, :uname, :ip, :client, :token, :date)";
		
		$params = array(
			'action' => $action,
			'id' => $uid,
			'uname' => $uname,
			'ip' => $_SERVER['REMOTE_ADDR'],
			'client' => $_SERVER['HTTP_USER_AGENT'],
			'token' => $authToken,
			'date' => $GLOBALS['dtObj']->format('now', DATE_SQL_FORMAT)
		);
		
		try {
			return $GLOBALS['dbObj']->insert($query, $params);
		} catch(Exception $e) {
			return false;
		}
	}
}

?>