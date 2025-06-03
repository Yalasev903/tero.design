<?php
include_once './include_a/config.inc.php';
ini_set('session.gc_maxlifetime', 2629743); //Количество секунд, после чего данные будут считаться 'мусором' и зачищаться (місяць)
ini_set('session.cookie_lifetime', 2629743); //Период хранения куки в секундах (місяць)
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'].'/'.$config['admin_folder'] .'/sessions_a');
session_start();
		
// Для выхода нужно уничтожить переменные
unset($_SESSION['profile']);
session_destroy(); // разрушаем сессию

//Обновим страничку автоматом для того чтобы обновился блок входа.
$url = str_replace('http://' . $_SERVER['HTTP_HOST'], '', $_SERVER['HTTP_REFERER']);
if (empty($_SERVER['HTTP_REFERER']))
	$url = './index.php';
header("Location: ". $url);