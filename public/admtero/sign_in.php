<?php 
include_once './include_a/config.inc.php';
include_once './include_a/db.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?=$config['name']?> | Вход в панель управления</title>
<link rel="stylesheet" href="./css_a/style.css" />
</head>

<body id="page" class="sign-page">

	<div class="fs-box-login">
        <section>
            <div id="login-form" class="fs-form js-form">
                <div class="input">
					<label class="input__label">Логин</label>
					<input type="text" class="input__field" id="login-l">
				</div>
				<div class="input">
					<label class="input__label">Пароль</label>
					<input type="password" class="input__field" id="password-l">
				</div>
				<div class="action">
					<a href="#" class="button" id="js-login">Войти</a>
				</div>
            </div>
        </section>
    </div> <!-- class="fs-box" -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="./js_a/main.js"></script>
</body>
</html>