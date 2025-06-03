<?php
$link = mysqli_connect($db_server, $db_user, $db_password, $db_database);
mysqli_query($link, "SET NAMES 'utf8'");

// Отслеживаем ошибки при соединении
if( !$link ){
	echo 'Ошибка: '
		. mysqli_connect_errno()
		. ':' 
		. mysqli_connect_error();
}