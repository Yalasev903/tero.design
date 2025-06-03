<?php
/* Сетка команда
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
	
	
	$media = mysqli_real_escape_string($link, trim($_POST['media']));
	
	$update = "UPDATE 
					  `tbl_team`
				  SET 
					  `col_media` = '$media' ";
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