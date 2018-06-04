<?php
$consumer_key = 'yis0TYCu7U9V4o7M';
$consumer_secret = '74c5fd430cf1242a527f6223aebd42d30464be22';
$domain = $_SERVER["SERVER_NAME"];

$security = array(
	'consumer_key' => $consumer_key,
	'domain'       => $domain
);

//Learnosity Items API:
$url_assess                = 'https://assess-va.learnosity.com';
  $url_authorapi             = 'https://authorapi.learnosity.com?v1.30';
  $url_data                  = 'https://data-va.learnosity.com';
  $url_events                = 'https://events-va.learnosity.com';
  $url_items                 = 'https://items-va.learnosity.com';
  $url_questioneditor        = 'https://questioneditor.learnosity.com?v2';
  $url_questioneditor_v3     = 'https://questioneditor.learnosity.com?v3';
  $url_questions             = 'https://questions-va.learnosity.com';
  $url_reports               = 'https://reports-va.learnosity.com';
  $version_assessapi         = 'v2';
  $version_dataapi           = 'v1';
  $version_questionsapi      = 'v2';
  $version_questioneditorapi = 'v3';

//Just throwing this link down here in case we want to link to a specific or self-hosted version:
$url_jquery = 'src/js/vendor/jquery.min.js';

include_once "src/vendor/learnosity/learnosity-sdk-php/src/LearnositySdk/autoload.php";
?>
