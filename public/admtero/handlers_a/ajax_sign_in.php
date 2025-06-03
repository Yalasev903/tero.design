<?php
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	include_once '../include_a/config.inc.php';
	include_once '../include_a/db.php';
	header("Cache-Control: no-store"); //повна заборона кешування
	header("Content-Type: text/html; charset=utf-8");

	$login = trim(($_POST["login"]));
	$password = trim(($_POST["password"]));
		
	if ($login == "") { 
		$error["name"] = "login";
		$error["error"] = "Необходимо заполнить поле";
		exit(json_encode($error));
	}
	
	if ($password == "") {
		$error["name"] = "password";
		$error["error"] = "Необходимо заполнить поле";
		exit(json_encode($error));
	}
			
	// Если пароли совпали то создами переменные сессии (вход да)
	if ($login == $config['admin_login'] && $password == $config['admin_pass']) {
		$_SESSION["profile"] = "admin";
		
		$error["name"] = "signin";
		$error["redirect"] = "./index.php";
		exit(json_encode($error));	
	} else {
		$error["name"] = "password";
		$error["error"] = "Неверный логин или пароль";
		exit(json_encode($error));	
	}
}
exit();