<?php
/* Privacy Policy
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
	
	$array = array('<p><br></p>', '<h2><br></h2>', '<p></p>', '<h2></h2>', '<br></p>', '<br></h2>', 'style="width: 300px;"');
	
	$privacy_policy_page = mysqli_real_escape_string($link, trim($_POST['privacy_policy_page']));
	$privacy_policy_page = str_replace($array, '', $privacy_policy_page);
	$privacy_policy_modal = mysqli_real_escape_string($link, trim($_POST['privacy_policy_modal']));
	$privacy_policy_modal = str_replace($array, '', $privacy_policy_modal);
	
	$update = "UPDATE 
					  `tbl_settings`
				  SET 
					  `col_privacy_policy_page` = '$privacy_policy_page',
					  `col_privacy_policy_modal` = '$privacy_policy_modal'";
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