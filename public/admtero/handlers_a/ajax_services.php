<?php
/* Услуги
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
		$title = mysqli_real_escape_string($link, trim($_POST['title']));
		$description = mysqli_real_escape_string($link, trim($_POST['description']));
		$video = mysqli_real_escape_string($link, trim($_POST['video']));
	}
	
	
	/* Новая услуга
	------------------------------------------------------- */
	if ($_POST['action'] == 'add') {
		
		$insert = "INSERT INTO 
		                       `tbl_services`
						VALUES 
						       (NULL,
							   '$title',
							   '$description',
							   '$video') ";
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
	
	
	/* Редактирование услуги
	------------------------------------------------------- */
	if ($_POST['action'] == 'edit') {
		
		$update = "UPDATE 
		                  `tbl_services`
					  SET 
					      
						  `col_title` = '$title',
						  `col_description` = '$description',
						  `col_video` = '$video'
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
	
	
	/* Удаление услуги
	------------------------------------------------------- */
	if ($_POST['action'] == 'delete') {
		
		$delete = "DELETE FROM `tbl_services` WHERE `col_id` = ". (int)$_POST['id'] ;
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