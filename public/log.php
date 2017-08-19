<?php

require_once '../config.php';

$log = json_decode(file_get_contents(SITE_PATH . 'log.txt'), true);
$log = array_reverse($log);

foreach ($log as $item) {
echo 	'<div class="mySQLerror">' .
	date('d.m.Y H:i', $item['time']) .
	'<br>UserID=' . $item['userID'] .
	'<br>URL=' . $item['url'] .
	'</div>';
echo '<div class="mySQLerror">' . nl2br(htmlspecialchars($item['error'])) . '</div>';
echo '<br>';
}

?>

<style>
	.mySQLerror {
		background: rgba(255, 255, 0, 0.44);
		border: 1px solid black;
		padding: 5px;
	}
</style>
