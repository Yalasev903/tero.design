<?php
include_once './include_a/header.php';

if (! empty($_GET['act']) && $_GET['act'] == 'edit') {
	$action = 'edit';
	$button = 'Сохранить';
	$title = 'Редактирование услуги';

	$sql = "SELECT 
			       `col_id`,
				   `col_title`,
				   `col_description`,
				   `col_video`
		      FROM 
			       `tbl_services`
		     WHERE 
				   `col_id` = ". (int)$_GET['id'];
	$query = mysqli_query($link, $sql);

	if (mysqli_num_rows($query) > 0) {
		$row = mysqli_fetch_assoc($query);
	}
	
} else {
	$action = 'add';
	$button = 'Добавить';
	$title = 'Новая услуга';
}
?>

<div id="wrap-content" class="settings">
	<div class="header">
		<a href="./services.php"><?=$icons['prev']?></a>
    	<h1><?=$title?></h1>
    </div>
    <div class="content content-form fs-form">
		
		
		<div class="input">
			<label class="input__label">Название</label>
			<input class="input__field" type="text" id="js-title" value="<?=$row['col_title'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Описание</label>
			<textarea class="input__field input__text" id="js-description"><?=$row['col_description'] ?? ''?></textarea>
		</div>
		
		<div class="input">
			<label class="input__label">
				<div>Видео</div>
				<div>
					<a href="#" class="icon2 js-file" data-wrap="#js-video" data-folder="video"><?=$icons['upload']?></a>
					<a href="#" class="icon2 js-reset-file" data-wrap="#js-video"><?=$icons['reset']?></a>
				</div>
			</label>
			<div id="js-video" data-value="<?=$row['col_video'] ?? ''?>">
			<?php
			if (! empty($row['col_video'])) {
				echo '<video src="/multimedia/'. $row['col_video'] .'" playsinline="" muted="" loop="" autoplay="autoplay"></video>';
			}
			?>
			</div>
		</div> <!-- /.input -->
		
		
		<div class="action">
			<a href="#" class="button" id="js-services-save" data-id="<?=$row['col_id'] ?? ''?>" data-action="<?=$action?>"><?=$button?></a>
		</div>

    </div> <!-- END class="content" -->
</div> <!-- END id="wrap-content" -->


<form id="upload" method="post" action="./handlers_a/upload.php" enctype="multipart/form-data">
	<input type="file" name="files" id="photoupload" class="js-form" />
</form>

<?php include_once './include_a/footer.php';