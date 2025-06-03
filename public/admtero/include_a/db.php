<?php
ini_set('session.gc_maxlifetime', 2629743);
ini_set('session.cookie_lifetime', 2629743);
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'].'/'.$config['admin_folder'] .'/sessions_a');
session_start();

if (! isset($_SESSION['profile'])) {
	$_SESSION['profile'] = 'user';
}

$link = mysqli_connect($db_server, $db_user, $db_password, $db_database);
mysqli_query($link, "SET NAMES 'utf8'");

// Отслеживаем ошибки при соединении
if( !$link ){
	echo 'Ошибка: '
		. mysqli_connect_errno()
		. ':' 
		. mysqli_connect_error();
}