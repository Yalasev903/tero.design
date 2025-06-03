<?php
/* FAQ
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
		$question = mysqli_real_escape_string($link, trim($_POST['question']));
		
		$array = array('<p><br></p>', '<h2><br></h2>', '<p></p>', '<h2></h2>', '<br></p>', '<br></h2>', 'style="width: 300px;"');
		$answer = mysqli_real_escape_string($link, trim($_POST['answer']));
		$answer = str_replace($array, '', $answer);
	}
	
	
	/* Новый вопрос
	------------------------------------------------------- */
	if ($_POST['action'] == 'add') {		
		
		$insert = "INSERT INTO 
		                       `tbl_faq`
						VALUES 
						       (NULL,
							   '$question',
							   '$answer') ";
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
	
	
	/* Редактирование вопроса
	------------------------------------------------------- */
	if ($_POST['action'] == 'edit') {
		
		$update = "UPDATE 
		                  `tbl_faq`
					  SET 
						  `col_question` = '$question',
						  `col_answer` = '$answer'
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
	
	
	/* Удаление вопроса
	------------------------------------------------------- */
	if ($_POST['action'] == 'delete') {
		
		$delete = "DELETE FROM `tbl_faq` WHERE `col_id` = ". (int)$_POST['id'] ;
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