<?php
/* Проекты
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
	
	if ($_POST['action'] == 'add' || $_POST['action'] == 'edit') {
		$meta_title = mysqli_real_escape_string($link, trim($_POST['meta_title']));
		$meta_description = mysqli_real_escape_string($link, trim($_POST['meta_description']));
		$meta_keywords = mysqli_real_escape_string($link, trim($_POST['meta_keywords']));
		$title = mysqli_real_escape_string($link, trim($_POST['title']));

		$array = array('<p><br></p>', '<h2><br></h2>', '<p></p>', '<h2></h2>', '<br></p>', '<br></h2>', 'style="width: 300px;"');
		
		$text1 = mysqli_real_escape_string($link, trim($_POST['text1']));
		$text1 = str_replace($array, '', $text1);
		
		$text2 = mysqli_real_escape_string($link, trim($_POST['text2']));
		$text2 = str_replace($array, '', $text2);	
	}
	
	
	/* Новый проект
	------------------------------------------------------- */
	if ($_POST['action'] == 'add') {
		
		$insert = "INSERT INTO 
		                       `tbl_projects`
						VALUES 
						       (NULL,
							   '$meta_title',
							   '$meta_description',
							   '$meta_keywords',
							   '$title',
							   '$text1',
							   '$text2',
							   '') ";
		$query = mysqli_query($link, $insert);
		$id = mysqli_insert_id($link);
		mysqli_close($link);
		
		if ($query) {
			$error['name'] = 'success';
			$error['id'] = $id;
			exit(json_encode($error));
		} else {
			$error["name"] = "error";
			$error["error"] = 'Возникла ошибка';
			exit(json_encode($error));
		}
	} //END 'add'
	
	
	/* Редактирование проекта
	------------------------------------------------------- */
	if ($_POST['action'] == 'edit') {
		
		$update = "UPDATE 
		                  `tbl_projects`
					  SET 
						  `col_meta_title` = '$meta_title',
						  `col_meta_description` = '$meta_description',
						  `col_meta_keywords` = '$meta_keywords',
						  `col_title` = '$title',
						  `col_text1` = '$text1',
						  `col_text2` = '$text2'
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
	} //END 'edit'
	
	
	/* Удаление проекта
	------------------------------------------------------- */
	if ($_POST['action'] == 'delete') {
		
		$delete = "DELETE FROM `tbl_projects` WHERE `col_id` = ". (int)$_POST['id'] ;
		$query = mysqli_query($link, $delete);
		mysqli_close($link);

		if ($query) {
			$error['name'] = 'success';
			exit(json_encode($error));
		} else {
			$error['name'] = 'error';
			$error['error'] = 'Возникла ошибка';
			exit(json_encode($error));
		}
	} //END 'delete'
}