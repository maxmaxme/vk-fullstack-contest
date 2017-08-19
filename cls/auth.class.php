<?php

class auth
{

	public $user = 0;
	public $name = 0;
	public $photo = 0;

	function __construct($hash)
	{
		global $db;

		$userInfo = $db->getRow('select
 									u.ID, u.Name, u.Photo
								from users u
								where `Hash`=?s', $hash);

		if ($hash && $userInfo) {
			$this->user = $userInfo['ID'];
			$this->name = $userInfo['Name'];
			$this->photo = $userInfo['Photo'];
		}

	}

	static public function checkHash($hash)
	{
		global $db;

		$result = [];
		$error = '';


		if (!$hash || !$result = $db->getRow('select Name, Photo from users where `Hash`=?s', $hash))
			$error = 'Error';

		return [$result, $error];

	}
}