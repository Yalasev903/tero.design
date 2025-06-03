<?php
/* Карта Google
------------------------------------------------------- */
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	include_once '../include_a/config.inc.php';
	include_once '../include_a/db.php';
	header("Cache-Control: no-store"); //полный запрет кэширования
	header("Content-Type: text/html; charset=utf-8");
	
	if($_SESSION['profile'] != 'admin') {
		$error['name'] = 'error';
		$error['error'] = 'Авторизуйтесь';
		exit(json_encode($error));
	}
	
	$lat = mysqli_real_escape_string($link, trim($_POST['lat']));
	$lng = mysqli_real_escape_string($link, trim($_POST['lng']));
	$zoom = mysqli_real_escape_string($link, trim($_POST['zoom']));
	$marker = mysqli_real_escape_string($link, trim($_POST['marker']));
	$key = mysqli_real_escape_string($link, trim($_POST['key']));
	
	$update = "UPDATE 
					  `tbl_map`
				  SET 
					  `col_lat` = '$lat',
					  `col_lng` = '$lng',
					  `col_zoom` = '$zoom',
					  `col_marker` = '$marker',
					  `col_key` = '$key' ";
	$query = mysqli_query($link, $update);
	mysqli_close($link);

	if ($query) {
		$error['name'] = 'success';
		exit(json_encode($error));
	} else {
		$error['name'] = 'error';
		$error['error'] = 'Возникла ошибка';
		exit(json_encode($error));
	}
}