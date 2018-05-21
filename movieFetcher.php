<?php 

	include_once('src/vendor/imdb/IMDbapi.php'); //Contains class IMDbapi

	class MovieFetcher {

		public function fetchMovie(){
			$imdb = new IMDbapi('Vh54D1Kl02SjOJ6Y3VxhjTzTHYJxLr');

			$idDoesNotExist = true;
			$randomMovieInfo = 'error';
			while($idDoesNotExist == true){
				$randomNumber = rand(1000000, 9999999);
				$fullId = "tt".$randomNumber;
				$randomMovieInfo = json_decode($imdb->get($fullId,'json'));

				print_r($randomMovieInfo);


				//THESE LINES FOR DEBUGGING////////////////////////////
				echo($fullId."<br />");
				echo (" status: ".$randomMovieInfo->status."<br />");//
				echo (" type: ".$randomMovieInfo->type."<br />");    //
 				///////////////////////////////////////////////////////


				if($randomMovieInfo->status=='true' && ($randomMovieInfo->type == 'feature' || $randomMovieInfo->type == 'movie')){
					$idDoesNotExist = false;
				}
			}

			return $randomMovieInfo;
		} 
	}
?>