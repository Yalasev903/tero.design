<?php
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	header("Cache-Control: no-store"); //полный запрет кэширования
	header("Content-Type: text/html; charset=utf-8");
	date_default_timezone_set('Europe/Warsaw');
	exit(date('H:i'));
}