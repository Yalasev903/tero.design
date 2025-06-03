<?php
include_once './include_a/header.php';

$sql = "SELECT 
			   `col_id`,
			   `col_meta_title`,
			   `col_meta_description`,
			   `col_meta_keywords`,
			   `col_title`,
			   `col_text`
		  FROM  
			   `tbl_pages`
		 WHERE 
			   `col_id` = ". (int)$_GET['id'];
$query = mysqli_query($link, $sql);
	
if (mysqli_num_rows($query) > 0) {
	$row = mysqli_fetch_assoc($query);
}
?>

<div id="wrap-content" class="settings">
	<div class="header">
		<a href="./pages.php"><?=$icons['prev']?></a>
    	<h1><?=$row['col_title']?></h1>
    </div>
    <div class="content content-form fs-input fs-form">
		
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
		
		<?php
		if ($row['col_id'] == 4) {
			
			$text = $row['col_text'] ?? '';
			
			echo '<div class="input">
				<label class="input__label">Описание</label>
				<div class="fs-editor" id="js-text">'. $text .'</div>
			</div>';
		}
		?>
		
		<div class="action">
			<a href="#" class="button" id="js-pages-save" data-id="<?=$row['col_id']?>">Сохранить</a>
		</div>

    </div> <!-- END class="content" -->
</div> <!-- END id="wrap-content" -->

<?php include_once './include_a/footer.php';