<?php
//данные для подключения к базе данных узнаем у своего хостинг провайдера
$db_user = 'tero_db'; //имя пользователя базы данных
$db_password = 'dhdqRTLx13'; //пароль пользователя базы данных
$db_database = 'tero_db'; //название базы данных
$db_server = 'localhost'; //имя сервера для подключения к базе данных

define('PAGE_URL', str_replace(['/', '.php'], '', $_SERVER['PHP_SELF'])); //URL текущей страницы

//дополнительные страницы
$config['pages'] = array(
	'1' => 'index',
	'2' => 'services',
	'3' => 'workflow',
	'4' => 'team',
	'5' => 'contact',
	'6' => 'privacy_policy'
);