<?php
if (!empty($_FILES)) {
	
	include_once '../include_a/config.inc.php';
	
	$tempFile = $_FILES['file']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . '/img/'. $_POST['folder'] .'/';
	$ext = mb_strtolower(strrchr($_FILES['file']['name'], ".")); //определяем расширение файла
	
	$allowed = array('.jpg', '.jpeg', '.gif', '.png');
	
	if (!in_array($ext, $allowed))
		exit('2');
		
	$mimetype = array('image/jpeg', 'image/gif', 'image/png');
	$imageinfo = getimagesize($tempFile);
	
	if (!in_array($imageinfo["mime"], $mimetype))
	    exit('2');

	$unic_name  = time().'_'.rand(0,1000).$ext;	
    $targetFile =  str_replace('//','/',$targetPath) . $unic_name;
	move_uploaded_file($tempFile, $targetFile);
	
    //генерация ответа
    $response = new StdClass;
    $response->link = $config['url'] .'img/'. $_POST['folder'] .'/'. $unic_name;
    echo stripslashes(json_encode($response));
}