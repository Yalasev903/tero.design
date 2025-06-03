<?php
/* Страницы
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
	
	$meta_title = mysqli_real_escape_string($link, trim($_POST['meta_title']));
	$meta_description = mysqli_real_escape_string($link, trim($_POST['meta_description']));
	$meta_keywords = mysqli_real_escape_string($link, trim($_POST['meta_keywords']));
	
	$array = array('<p><br></p>', '<h2><br></h2>', '<p></p>', '<h2></h2>', '<br></p>', '<br></h2>', 'style="width: 300px;"');
	$text = mysqli_real_escape_string($link, trim($_POST['text']));
	$text = str_replace($array, '', $text);
		
	$update = "UPDATE 
	                  `tbl_pages`
				  SET 
				      `col_meta_title` = '$meta_title',
					  `col_meta_description` = '$meta_description',
					  `col_meta_keywords` = '$meta_keywords',
					  `col_text` = '$text'
			    WHERE 
				      `col_id` = ". (int)$_POST['id'];
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