<?php
include_once './include_a/config.inc.php';
include_once './include_a/db.php';
include_once './include_a/functions.php';

if($_SESSION["profile"] != "admin") {
	header("Location: sign_in.php");
}
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$config['name']?> | Панель управления</title>
	<link rel="stylesheet" href="./css_a/style.css" />
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css" />
	<?php
	if (in_array(PAGE_URL, $config['pages'])) {
		echo '<link rel="stylesheet" href="./css_a/font-awesome.css">';
		echo '<link rel="stylesheet" href="./css_a/froala_editor.css">';
		echo '<link rel="stylesheet" href="./css_a/froala_style.css">';
		echo '<link rel="stylesheet" href="./css_a/plugins/image.css">';
		echo '<link rel="stylesheet" href="./css_a/plugins/line_breaker.css">';
		echo '<link rel="stylesheet" href="./css_a/plugins/quick_insert.css">';
		echo '<link rel="stylesheet" href="./css_a/plugins/video.css">';
	} else {
		echo '<link rel="stylesheet" href="./css_a/datatables.css" />';
	}
	?>
</head>
<body id="page">
	<section id="main">
		<header id="header">
			<div class="container">
				<div class="column">
					<div class="bars">
						<span class="lines"></span>
						<div class="navbar js-navbar">
							<a href="./index.php">Настройки</a>
							<a href="./pages.php">Страницы (SEO)</a>
							<a href="./showreel.php">Видео в модальном окне</a>
							<a href="./grid.php">Обложки проектов</a>
							<a href="./projects.php">Проекты</a>
							<a href="./services.php">Услуги</a>
							<a href="./workflow.php">Рабочий процесс</a>
							<a href="./faq.php">Вопросы-ответы</a>
							<a href="./team.php">Команда</a>
							<a href="./map.php">Карта Google</a>
							<a href="./privacy_policy.php">Privacy Policy</a>
							<a href="./clear_files.php">Удаление неиспользуемых файлов</a>
						</div>
					</div>
				</div> <!-- /.column -->
				<div class="column">
					<a href="<?=$config['url']?>" class="link get-site">Перейти на сайт</a>
					<a href="./exit.php" class="link">Выйти</a>
				</div> <!-- /.column -->
			</div>
		</header> <!-- END id="header" -->