<?php

include_once('../config.php');
include_once('../movieFetcher.php');

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
  	<h4>RANDOM PULL BELOW:</h4>
  	<?php
  		$randomMovieInfo = MovieFetcher::fetchMovie();
  	 ?>
  	 <img src=<?php echo $randomMovieInfo->poster; ?>></img>
  	 <h1><?php echo $randomMovieInfo->title;?></h1>


     <script src=<?php echo $url_questions ?>></script>
  </body>

</html>
