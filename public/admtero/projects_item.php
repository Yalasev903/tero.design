<?php
include_once './include_a/header.php';

$fields = ''; //поля формы

if (! empty($_GET['act']) && $_GET['act'] == 'edit') {
	$sql = "SELECT 
			       `col_id`,
				   `col_meta_title`,
				   `col_meta_description`,
				   `col_meta_keywords`,
				   `col_title`,
				   `col_text1`,
				   `col_text2`
		      FROM 
			       `tbl_projects`
		     WHERE 
				   `col_id` = ". (int)$_GET['id'];
	$query = mysqli_query($link, $sql);

	if (mysqli_num_rows($query) > 0) {
		$row = mysqli_fetch_assoc($query);
		
		//поля формы
		$multimedia = explode('(())', $row['col_multimedia']);
		$i = 1;
		
		while ($value = current($multimedia)) {
			$value .= ':*!'. $i;
			$fields .= templateFields($value);
			$i ++;
			next($multimedia);
		}
		//__________________
	}
	
	$action = 'edit';
	$button = 'Сохранить';
	$title = 'Редактирование проекта '. $row['col_title'];
	
} else {
	$action = 'add';
	$button = 'Добавить';
	$title = 'Новый проект';
}
?>

<div id="wrap-content" class="settings">
	<div class="header">
		<a href="./projects.php"><?=$icons['prev']?></a>
    	<h1><?=$title?></h1>
    </div>
    <div class="content content-form fs-form">
		
		<div class="input">
			<label class="input__label">Title (тег)</label>
			<input class="input__field" type="text" id="js-meta-title" value="<?=$row['col_meta_title'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Description (метатег)</label>
			<input class="input__field" type="text" id="js-meta-description" value="<?=$row['col_meta_description'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Keywords (метатег)</label>
			<input class="input__field" type="text" id="js-meta-keywords" value="<?=$row['col_meta_keywords'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Название</label>
			<input class="input__field" type="text" id="js-title" value="<?=$row['col_title'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Описание 1</label>
			<div class="fs-editor" id="js-text1"><?=$row['col_text1'] ?? ''?></div>
		</div>
		
		<div class="input">
			<label class="input__label">Описание 2</label>
			<div class="fs-editor" id="js-text2"><?=$row['col_text2'] ?? ''?></div>
		</div>
		
		<div class="action">
			<a href="#" class="button" id="js-projects-save" data-id="<?=$row['col_id'] ?? ''?>" data-action="<?=$action?>"><?=$button?></a>
		</div>

    </div> <!-- END class="content" -->
</div> <!-- END id="wrap-content" -->


<form id="upload" method="post" action="./handlers_a/upload.php" enctype="multipart/form-data">
	<input type="file" name="files" id="photoupload" class="js-form" />
</form>

<?php include_once './include_a/footer.php';