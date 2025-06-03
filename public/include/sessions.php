<?php
$session_max_lifetime = 60 * 60 * 24 * 7;
ini_set('session.gc_maxlifetime', $session_max_lifetime);
ini_set('session.cookie_lifetime', $session_max_lifetime);
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .'/sessions');
session_start();

if (! isset($_SESSION['privacy_policy'])) {
	$_SESSION['privacy_policy'] = 1;
}