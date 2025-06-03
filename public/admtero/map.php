<?php include_once './include_a/header.php';

$sql = "SELECT
			   `col_lat`,
			   `col_lng`,
			   `col_zoom`,
			   `col_marker`,
			   `col_key`
		  FROM 
		       `tbl_map` ";
$query = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($query);
?>

<div id="wrap-content" class="settings">
	<div class="header">
    	<h1>Карта Google</h1>
    </div>
    <div class="content content-form fs-form">
		
		<div class="input">
			<label class="input__label">Координаты Lat</label>
			<input class="input__field" type="text" id="js-lat" value="<?=$row['col_lat'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Координаты Lng</label>
			<input class="input__field" type="text" id="js-lng" value="<?=$row['col_lng'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">Zoom</label>
			<input class="input__field" type="text" id="js-zoom" value="<?=$row['col_zoom'] ?? ''?>">
		</div>
		
		<div class="input">
			<label class="input__label">
				<div>Маркер</div>
				<div>
					<a href="#" class="icon2 js-file" data-wrap="#js-marker" data-folder="map"><?=$icons['upload']?></a>
					<a href="#" class="icon2 js-reset-file" data-wrap="#js-marker"><?=$icons['reset']?></a>
				</div>
			</label>
			<div id="js-marker" data-value="<?=$row['col_marker'] ?? ''?>">
				<?=(! empty($row['col_marker'])) ? '<img src="/multimedia/'. $row['col_marker'] .'" alt="">' : ''?>
			</div>
		</div> <!-- /.input -->
		
		<div class="input">
			<label class="input__label">Ключ Google</label>
			<input class="input__field" type="text" id="js-key" value="<?=$row['col_key'] ?? ''?>">
		</div>
		
		<div class="action">
			<a href="#" class="button" id="js-map-save">Сохранить</a>
		</div>

    </div> <!-- END class="content" -->
</div> <!-- END id="wrap-content" -->


<form id="upload" method="post" action="./handlers_a/upload.php" enctype="multipart/form-data">
	<input type="file" name="files" id="photoupload" class="js-form" />
</form>

<?php include_once './include_a/footer.php';