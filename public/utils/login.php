<?php

require_once '../../config.php';

$code = $_REQUEST['code'];

if ($code) {
	$url = 'https://oauth.vk.com/access_token?client_id=6154721&client_secret=' . $cfg['vk_client_secret'] . '&redirect_uri=http://vk/login&code=' . $code;

	$result = json_decode(curl($url), true);

	if ($result['error']) {
		header('Location: /');
		die();
	}



	$vkID = $result['user_id'];

	$userInfo = vkAPI('users.get', [
		'user_ids' => $vkID,
		'fields' => 'photo_100',
		'v' => '5.68'
	])['response'][0];

	$name = $userInfo['first_name'] . ' ' . $userInfo['last_name'];
	$photo = $userInfo['photo_100'];


	if (!$hash = $db->getOne("SELECT Hash FROM users WHERE VkID=?i", $vkID)) {
		$hash = md5(rand(0, 2018) .  time() . rand(666, 22222));
		$db->query('insert into users set VkID=?i, Name=?s, Photo=?s, Hash=?s', $vkID, $name, $photo, $hash);
	}

	setcookie('hash', $hash, time() + 7 * 24 * 60 * 60, '/');
	header('Location: /');

} else {
	setcookie('hash', '', 0, '/');
	header('Location: /');
}
