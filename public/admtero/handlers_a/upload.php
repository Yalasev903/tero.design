<?php
if (!empty($_FILES)) {
	$tempFile = $_FILES['files']['tmp_name'];
	$ext = mb_strtolower(strrchr($_FILES['files']['name'], ".")); //расширение файла
	
	$file = $_POST;
	$file['name'] = time() .'_'. rand(0, 1000).$ext; //уникальное имя файла
	
	if (move_uploaded_file($_FILES['files']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] .'/multimedia/'. $file['name'])){
		$error['code'] = '3';
		$error['file'] = $file;
		$error['type'] = $ext;
		exit(json_encode($error));
	}
}