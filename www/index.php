<?php

  include_once('../config.php');

  use LearnositySdk\Request\Init as LearnosityInit;
  use LearnositySdk\Utils\Uuid as LearnosityUuid;

  if ($_GET['activity_template_id']) {
        $actTempID = $_GET['activity_template_id'];
  } else {
      $actTempID = 'AD_ACTIVITY_1';
  };

  //show in initial state to start and, when reloaded with session id, show in review mode
  if (isset($_GET['session_id'])) {
    $session_id = $_GET['session_id'];
    $session_state = 'resume';
  } else {
    $session_id = LearnosityUuid::generate();
    $session_state = 'initial';
  }

  $request = [
    'user_id'               => 'tutorial_student',
    'session_id'            => $session_id,
    'activity_template_id'  => $actTempID,
    'rendering_type'        => 'assess',
    'state'                 => $session_state,
    'type'                  => 'submit_practice',
    'activity_id'           => 'tutorial_activity',
    'name'                  => 'Self Calculating Rubric',
    'course_id'             => 'tutorial_course',
    "config"          =>  [
      "ui_style" => "horizontal",
      "ignore_question_attributes" => [
        // "instant_feedback"
      ],
      "configuration" => [
        "onsubmit_redirect_url" => 'feedback.php?session_id='. $session_id
      ],
      "navigation" => [
        "show_intro" => true
      ],
      "regions" => "main"
    ],
  ];

  $Init = new LearnosityInit(
    'items',
    $security,
    $consumer_secret,
    $request
  );
  $signedRequest = $Init->generate();

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <div class="section">
      <h1>Self Calculating Rubric Demo</h1>
    <div style="width:1050px;">
      <div id="learnosity_assess"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="//items.learnosity.com/"></script>
    <script>
      var init = function() {
        console.log("Test Ready for more methods");
      };

      var addHistory = {
        readyListener: function () {
          //add to history to support back button and show in resume mode
          history.pushState({}, '', window.location.pathname + '?session_id=<?php echo $session_id; ?>');
        }
      }

      var eventOptions = {
        readyListener : init
      };

      var itemsApp = LearnosityItems.init(<?php echo $signedRequest; ?>, eventOptions);

      addHistory.readyListener();

    </script>
  </body>

</html>
