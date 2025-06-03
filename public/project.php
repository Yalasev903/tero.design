<?php
include_once './include/header.php';

$text2 = (! empty($row['col_text2'])) ? '<div class="project-description-col">'. $row['col_text2'] .'</div>' : '';
?>


<div class="project">
	<div class="project-head row2">
		<h1 class="project-title"><?=$row['col_title']?></h1>
		<a href="index.php#js-grid-item<?=$row['col_id']?>" class="project-prev row">
			<svg class="project-prev-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
				<path d="M136.97 380.485l7.071-7.07c4.686-4.686 4.686-12.284 0-16.971L60.113 273H436c6.627 0 12-5.373 12-12v-10c0-6.627-5.373-12-12-12H60.113l83.928-83.444c4.686-4.686 4.686-12.284 0-16.971l-7.071-7.07c-4.686-4.686-12.284-4.686-16.97 0l-116.485 116c-4.686 4.686-4.686 12.284 0 16.971l116.485 116c4.686 4.686 12.284 4.686 16.97-.001z"></path>
			</svg>
			<span class="project-prev-label">back to portfolio</span>
		</a>
	</div> <!-- /.project-head -->
	<div class="project-description">
		<div class="project-description-col"><?=$row['col_text1']?></div>
		<?=$text2?>
	</div> <!-- /.project-description -->
</div> <!-- /.project -->

<div class="grid" id="js-gallery"><?=gridPageProject($row)?></div>

<?php include_once './include/footer.php';