<?php include_once './include_a/header.php';

$sql = "SELECT
			   `col_showreel_poster`,
			   `col_showreel_video`
		  FROM 
		       `tbl_settings` ";
$query = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($query);
?>

<div id="wrap-content" class="settings">
	<div class="header">
    	<h1>Видео в модальном окне</h1>
    </div>
    <div class="content content-form fs-form">
		
		<div class="input">
			<label class="input__label">
				<div>Постер к видео</div>
				<div>
					<a href="#" class="icon2 js-file" data-wrap="#js-poster" data-folder="posters"><?=$icons['upload']?></a>
					<a href="#" class="icon2 js-reset-file" data-wrap="#js-poster"><?=$icons['reset']?></a>
				</div>
			</label>
			<div id="js-poster" data-value="<?=$row['col_showreel_poster'] ?? ''?>">
				<?=(! empty($row['col_showreel_poster'])) ? '<img src="/multimedia/'. $row['col_showreel_poster'] .'" alt="">' : ''?>
			</div>
		</div> <!-- /.input -->
		
		<div class="input">
			<label class="input__label">
				<div>Файл видео</div>
				<div>
					<a href="#" class="icon2 js-file" data-wrap="#js-video" data-folder="video"><?=$icons['upload']?></a>
					<a href="#" class="icon2 js-reset-file" data-wrap="#js-video"><?=$icons['reset']?></a>
				</div>
			</label>
			<div id="js-video" data-value="<?=$row['col_showreel_video'] ?? ''?>">
			<?php
			if (! empty($row['col_showreel_video'])) {
				echo '<video src="/multimedia/'. $row['col_showreel_video'] .'" playsinline="" muted="" loop="" autoplay="autoplay"></video>';
			}
			?>
			</div>
		</div> <!-- /.input -->
		
		
		<div class="action">
			<a href="#" class="button" id="js-showreel-save">Сохранить</a>
		</div>

    </div> <!-- END class="content" -->
</div> <!-- END id="wrap-content" -->


<form id="upload" method="post" action="./handlers_a/upload.php" enctype="multipart/form-data">
	<input type="file" name="files" id="photoupload" class="js-form" />
</form>

<?php include_once './include_a/footer.php';