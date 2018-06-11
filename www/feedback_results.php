<?php

  include_once '../config.php';

  use LearnositySdk\Request\Init;
  use LearnositySdk\Utils\Uuid;
  //session ids for student and teacher sessions
  $session_id = $_GET['session_id'];
  $feedback_session_id = $_GET['feedback_session_id'];

  $studentid = 'tutorial_student';

  $security = [
    'user_id'      => $studentid,
    'domain'       => $domain,
    'consumer_key' => $consumer_key,
  ];
  $request = array(
    'reports' => array(
      array(
        'id' => 'report-1',
        'type' => 'session-detail-by-item',
        'user_id' => $studentid,
        'session_id' => $session_id
      )
    ),
    'configuration' => array(
      'questionsApiVersion' => 'v2',
      'itemsApiVersion' => 'v1'
    )
  );

  $Init = new Init('reports', $security, $consumer_secret, $request);
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
    <!-- Container for the items api to load into -->
      <div class="row">
        <div class="col-md-6">
          <h1>Student Review</h1>
        </div>
        <div class="col-md-6">
          <h1>Teacher Feedback</h1>
        </div>
      </div>
      <span class="learnosity-report" id="report-1"></span>
      <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6">
          <span class="learnosity-save-button"></span>
        </div>
      </div>
    </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="<?php echo $url_reports; ?>"></script>
  <script>
    var init = function() {
      var itemReferences = [];
      var report1 = reportsApp.getReport('report-1');
      report1.on('ready:itemsApi', function(itemsApp) {
            // Build the 2 columns, left is Reports API (student in review) and the right is Items API
            // showing the teacher feedback.
            $('.lrn_widget').wrap('<div class="row"></div>').wrap('<div class="col-md-6"></div>');
            itemsApp.getQuestions(function(questions) {
              $.each(questions, function(index, element) {
                if(element.metadata.rubric_reference !== undefined) {
                  var itemId = element.response_id + '_' + element.metadata.rubric_reference;
                  $('<span class="learnosity-item item-rubric" data-reference="' + itemId + '">')
                  .appendTo($('#' + element.response_id).closest('.row'))
                  .wrap('<div class="col-md-6"></div>');
                  itemReferences.push({
                    'id' : itemId,
                    'reference' : element.metadata.rubric_reference
                  });
                }
              });
            });
            console.log(itemReferences);
            var itemsActivity = {
              'domain': location.hostname,
              'request': {
                'user_id': '<?php echo $studentid; ?>',
                'rendering_type': 'inline',
                'name': 'Items API demo - feedback activity.',
                'state': 'review',
                'activity_id': 'feedback_test_1',
                'session_id': '<?php echo $feedback_session_id; ?>',
                'items': itemReferences,
                'type': 'feedback'
              }
            };
            $.post("endpoint.php", itemsActivity, function(data, status) {
              console.log("endpoint response", data);
              itemsApp = LearnosityItems.init(data);
            });
          });
    };
    var eventOptions = {
      readyListener : init
    };
    reportsApp = LearnosityReports.init(<?php echo $signedRequest; ?>, eventOptions);
  </script>

  <style type="text/css">
    .lrn .row {
      border-bottom: 1px solid #eee;
      margin-bottom: 20px;
      margin-top: 20px;
    }
    .learnosity-report h3 {
      font-weight: 400;
    }
  </style>

  </body>
</html>
