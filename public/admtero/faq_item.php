<?php
include_once './include_a/header.php';

if (! empty($_GET['act']) && $_GET['act'] == 'edit') {
	$action = 'edit';
	$button = 'Сохранить';
	$title = 'Редактирование вопроса';

	$sql = "SELECT 
				   `col_id`,
				   `col_question`,
				   `col_answer`
			  FROM 
				   `tbl_faq`
		     WHERE 
				   `col_id` = ". (int)$_GET['id'];
	$query = mysqli_query($link, $sql);
	mysqli_close($link);
	

	if (mysqli_num_rows($query) > 0) {
		$row = mysqli_fetch_assoc($query);
	}
	
} else {
	$action = 'add';
	$button = 'Добавить';
	$title = 'Новый вопрос';
}

?>

<div id="wrap-content" class="settings">
	<div class="header">
		<a href="./faq.php"><?=$icons['prev']?></a>
    	<h1><?=$title?></h1>
    </div>
    <div class="content content-form fs-form">
		
		<div class="input">
			<label class="input__label">Вопрос</label>
			<input class="input__field" type="text" id="js-question" value="<?=$row['col_question'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Ответ</label>
			<div class="fs-editor" id="js-answer"><?=$row['col_answer'] ?? ''?></div>
		</div>

		<div class="action">
			<a href="#" class="button" id="js-faq-save" data-id="<?=$row['col_id'] ?? ''?>" data-action="<?=$action?>"><?=$button?></a>
		</div>

    </div> <!-- END class="content" -->
</div> <!-- END id="wrap-content" -->

<?php include_once './include_a/footer.php';