<?php
/* Рабочий процесс
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
	
	$description = mysqli_real_escape_string($link, trim($_POST['description']));
	$poster = mysqli_real_escape_string($link, trim($_POST['poster']));
	$video = mysqli_real_escape_string($link, trim($_POST['video']));
	
	$update = "UPDATE 
					  `tbl_workflow`
				  SET 
					  `col_description` = '$description',
					  `col_poster` = '$poster',
					  `col_video` = '$video'";
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