<?php
/* Статус
------------------------------------------------------- */
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	include_once '../include_a/config.inc.php';
	include_once '../include_a/db.php';
	header("Cache-Control: no-store"); //повна заборона кешування
	header("Content-Type: text/html; charset=utf-8");
	
	if ($_SESSION["profile"] != "admin") {
		$error['name'] = "error";
		$error["error"] = 'Авторизуйтесь';
		exit(json_encode($error));
	}
	
	
	$tbl = mysqli_real_escape_string($link, $_POST['action']);
		
	//обновляем статус
	$update = "UPDATE 
					  `tbl_". $tbl ."`
				  SET 
					  `col_status` = ". (int)$_POST['status'] ."
				WHERE 
					  `col_id` = ". (int)$_POST['id'];
	$query = mysqli_query($link, $update);

	if ($query) {
		$error['name'] = 'success';
		exit(json_encode($error));
	} else {
		$error['name'] = 'error';
		$error["error"] = 'Возникла ошибка';
		exit(json_encode($error));
	}
}