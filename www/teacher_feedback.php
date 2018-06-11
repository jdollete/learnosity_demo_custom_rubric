<?php

  include_once '../config.php';

  use LearnositySdk\Request\Init as LearnosityInit;
  use LearnositySdk\Utils\Uuid as LearnosityUuid;

  $session_id   = $_GET['session_id'];
  $activity_id  = $_GET['activity_id'];

  $studentid = 'tutorial_student';

  $request = [
    'reports' => [
      [
        'id'          => 'report-1',
        'type'        => 'session-detail-by-item',
        'user_id'     => $studentid,
        'session_id'  => $session_id
      ]
    ]
  ];

  $Init = new LearnosityInit('reports', $security, $consumer_secret, $request);
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
      <!-- Containers for the reports api to load into -->
      <div class="row">
        <div class="col-md-6">
          <h1>Student Review</h1> <?php echo $session_id; ?>
        </div>
        <div class="col-md-6">
          <h1>Teacher Feedback</h1>
        </div>
      </div>
      <span class="learnosity-report" id="report-1"></span>
      <div class="row">
        <div class="col-md-10"></div>
        <div class="col-md-2 pull-right">
          <span class="learnosity-save-button"></span>
        </div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src='<?php echo $url_reports; ?>'></script>
    <script type="text/javascript">

      var getSum = function(total, num) {
          return total + num;
      };

      var calculateRubricScore = function(itemsActivity) {
        var responses = [];
        var $rubricContainers = $( 'div.item-rubric' );
        var index = 0;

        $.each($rubricContainers, function(index, value) {
          var $responseId = $($($($($($( this )).closest('.lrn-item-processed')).parent().parent())[0]).find('.lrn_widget')[0]).attr('id');
          var $selectedScores = $(this).find(".lrn_selected");
          var max_score = $(this).find('.lrn_option').length
          var rubricScores = {};
          var currentRubricScores = [];
          var totalPoints;

          $.each($selectedScores, function(index, value) {
            currentRubricScores.push(parseInt(this.attributes[1].nodeValue) + 1);
          });

          totalPoints = currentRubricScores.reduce(getSum);

          responses.push({
            'response_id': $responseId,
            'score': totalPoints,
            'max_score': max_score
          });

          index += 1;
        });

        postScores(responses, itemsActivity);
      };

      var postScores = function(responses, itemsActivity) {
        var endpoint = '<?php echo $url_data; ?>/latest/sessions/responses/scores';
        var request = {
          'sessions': [
            {
              'session_id': '<?php echo $session_id; ?>',
              'user_id': '<?php echo $studentid; ?>',
              'responses': responses
            }
          ]
        };

        $.ajax({
            url: '/xhr.php',
            data: {'request': JSON.stringify(request), 'endpoint': endpoint, 'action': 'update'},
            dataType: 'json',
            type: 'POST'
        })
        .fail(function(xhr, status, data) {
            console.log(xhr.responseText, null, null);
        })
        .done(function(data, status, xhr) {
          // The only reason we wait # seconds _after_ the Data API update is due to a latency
          // retrieving responses that have been immediately set/updated
          window.setTimeout(function () {
            window.location = './feedback_results.php?session_id=<?php echo $session_id; ?>&feedback_session_id=' + itemsActivity.request.session_id;
          }, 2000);
        });
      }

      var init = function() {
        var itemReferences = [];
        var report1 = reportsApp.getReport('report-1');

        report1.on('ready:itemsApi', function(itemsApp) {
          // Build the 2 columns, left is Reports API (student in review) and the right is Items API
          // for the teacher to add feedback.
          $('.lrn_widget').wrap('<div class="row"></div>').wrap('<div class="col-md-6"></div>');
          itemsApp.getQuestions(function(questions) {
            $.each(questions, function(index, element) {
              if(element.metadata.rubric_reference !== undefined) {
                var itemId = element.response_id + '_' + element.metadata.rubric_reference;
                // Added 'item-rubric' as a class for easier targeting
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

          var itemsActivity = {
            'domain': location.hostname,
            'request': {
              'user_id': '<?php echo $studentid; ?>',
              'rendering_type': 'inline',
              'name': 'Items API demo - feedback activity.',
              'state': 'initial',
              'activity_id': 'feedback_test_1',
              'session_id': '<?php echo LearnosityUuid::generate(); ?>',
              'items': itemReferences,
              'type': 'feedback',
              'config': {
                'renderSaveButton' : true
              }
            }
          };

          $.post('endpoint.php', itemsActivity, function(data, status) {
            itemsApp = LearnosityItems.init(data, {
              readyListener: function() {
                $('.lrn_save_button').click(function() {
                  calculateRubricScore(itemsActivity);
                });
              }
            });
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
      .lrn-circle {
        display: none;
      }
    </style>

  </body>
</html>
