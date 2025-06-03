<footer id="footer">© Панель управления</footer>
</section> <!-- END id="main" -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	
	<?php
	if (in_array(PAGE_URL, $config['pages'])) {
		/* загрузка картинки*/
		echo '<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>';
		echo '<script src="./js_a/jquery.ui.widget.js"></script>';
		echo '<script src="./js_a/jquery.iframe-transport.js"></script>';
		echo '<script src="./js_a/jquery.fileupload.js"></script>';
		
		/* текстовый редактор*/
		echo '<script src="./js_a/froala_editor.min.js" ></script>';
		echo '<script src="./js_a/plugins/draggable.min.js"></script>';
		echo '<script src="./js_a/plugins/image.min.js"></script>';
		echo '<script src="./js_a/plugins/link.min.js"></script>'; //редактирование ссылок
		echo '<script src="./js_a/plugins/line_breaker.min.js"></script>';
		echo '<script src="./js_a/plugins/lists.min.js"></script>';
		echo '<script src="./js_a/plugins/paragraph_format.min.js"></script>';
		echo '<script src="./js_a/plugins/entities.min.js"></script>';//список с символами, которые зарезервированы в HTML
		echo '<script src="./js_a/plugins/quick_insert.min.js"></script>';
		echo '<script src="./js_a/plugins/video.min.js"></script>';
		echo '<script src="./js_a/ru.js"></script>';
	} else {
		echo '<script src="./js_a/datatables.min.js"></script>';
		echo '<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>';
	}
	?>
	
    <script src="./js_a/main.js"></script>
</body>
</html>