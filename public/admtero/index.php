<?php include_once './include_a/header.php';

$sql = "SELECT
			   `col_jivochat`,
			   `col_behance`,
			   `col_facebook`,
			   `col_instagram`,
			   `col_pinterest`,
			   `col_linkedin`,
			   `col_youtube`,
			   `col_email`,
			   `col_tel`,
			   `col_google_tm`
		  FROM 
		       `tbl_settings` ";
$query = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($query);
?>

<div id="wrap-content" class="settings">
	<div class="header">
    	<h1>Настройки</h1>
    </div>
    <div class="content content-form fs-form">
		
		<div class="input-switch">
			<label class="input-switch-label">JivoChat на всех страницах</label>
			<a href="#" class="switch <?=($row['col_jivochat']) ? 'checked' : ''?>" id="js-jivochat">
				<span class="js-label"><?=($row['col_jivochat']) ? 'вкл' : 'выкл'?></span>
				<span class="lever"></span>
			</a> <!-- /.switch -->
		</div>
		
		<div class="input">
			<label class="input__label">Behance</label>
			<input class="input__field" type="text" id="js-behance" value="<?=$row['col_behance'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Facebook</label>
			<input class="input__field" type="text" id="js-facebook" value="<?=$row['col_facebook'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Instagram</label>
			<input class="input__field" type="text" id="js-instagram" value="<?=$row['col_instagram'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Pinterest</label>
			<input class="input__field" type="text" id="js-pinterest" value="<?=$row['col_pinterest'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">LinkedIn</label>
			<input class="input__field" type="text" id="js-linkedin" value="<?=$row['col_linkedin'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">YouTube</label>
			<input class="input__field" type="text" id="js-youtube" value="<?=$row['col_youtube'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Email</label>
			<input class="input__field" type="text" id="js-email" value="<?=$row['col_email'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Телефон</label>
			<input class="input__field" type="text" id="js-tel" value="<?=$row['col_tel'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Google Tag Manager</label>
			<textarea class="input__field input__text" id="js-google-tm"><?=$row['col_google_tm'] ?? ''?></textarea>
		</div>
		
		<div class="action">
			<a href="#" class="button" id="js-settings-save">Сохранить</a>
		</div>

    </div> <!-- END class="content" -->
</div> <!-- END id="wrap-content" -->

<?php include_once './include_a/footer.php';