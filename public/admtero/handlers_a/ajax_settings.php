<?php
/* Настройки
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
	
	$behance = mysqli_real_escape_string($link, trim($_POST['behance']));
	$facebook = mysqli_real_escape_string($link, trim($_POST['facebook']));
	$instagram = mysqli_real_escape_string($link, trim($_POST['instagram']));
	$pinterest = mysqli_real_escape_string($link, trim($_POST['pinterest']));
	$linkedin = mysqli_real_escape_string($link, trim($_POST['linkedin']));
	$youtube = mysqli_real_escape_string($link, trim($_POST['youtube']));
	$email = mysqli_real_escape_string($link, trim($_POST['email']));
	$tel = mysqli_real_escape_string($link, trim($_POST['tel']));
	$google_tm = mysqli_real_escape_string($link, trim($_POST['google_tm']));
	
	$update = "UPDATE 
					  `tbl_settings`
				  SET 
					  `col_jivochat` = ". (int)$_POST['jivochat'] .",
					  `col_behance` = '$behance',
					  `col_facebook` = '$facebook',
					  `col_instagram` = '$instagram',
					  `col_pinterest` = '$pinterest',
					  `col_linkedin` = '$linkedin',
					  `col_youtube` = '$youtube',
					  `col_email` = '$email',
					  `col_tel` = '$tel',
					  `col_google_tm` = '$google_tm' ";
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