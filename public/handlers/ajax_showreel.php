<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	include_once '../include/config.inc.php';
	include_once '../include/db.php';
	include_once '../include/functions.php';
	header("Cache-Control: no-store"); //полный запрет кэширования
	header("Content-Type: text/html; charset=utf-8");
	
	$showreel = '';
	
	$sql = "SELECT
	               `col_showreel_poster`,
				   `col_showreel_video`
			  FROM  
				   `tbl_settings` ";
	$query = mysqli_query($link, $sql);

	if (mysqli_num_rows($query) > 0) {
		$row = mysqli_fetch_assoc($query);
		
		$showreel = '<div class="player" id="js-player">
			<img src="multimedia/'. $row['col_showreel_poster'] .'" alt="" class="player-poster">
			<a href="#" class="player-play" id="js-video-play">
				<img src="img/play.png" alt="">
			</a>
			<video src="multimedia/'. $row['col_showreel_video'] .'" controls class="player-video" id="js-video"></video>
		</div>
		<a href="#" class="showreel-close" id="js-showreel-close"></a>';
	}
	
	$data['showreel'] = $showreel;
	exit(json_encode($data));
}