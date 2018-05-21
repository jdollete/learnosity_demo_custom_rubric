<?php
$consumer_key = 'yis0TYCu7U9V4o7M';
$consumer_secret = '74c5fd430cf1242a527f6223aebd42d30464be22';
$domain = $_SERVER["SERVER_NAME"];

$security = array(
	'consumer_key' => $consumer_key,
	'domain'       => $domain
);

//Learnosity Items API:
$url_items = 'https://items.learnosity.com';
$url_reports = 'https://reports.learnosity.com';
$url_questions = 'https://questions.learnosity.com/';

//Just throwing this link down here in case we want to link to a specific or self-hosted version:
$url_jquery = 'src/js/vendor/jquery.min.js';

include_once 'src/vendor/autoload.php';
?>