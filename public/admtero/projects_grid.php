<?php include_once './include_a/header.php';

$sql = "SELECT 
			   `col_id`,
			   `col_title`,
			   `col_multimedia`
		  FROM 
			   `tbl_projects`
	     WHERE 
			   `col_id` = ". (int)$_GET['id'];
$query = mysqli_query($link, $sql);
mysqli_close($link);

$data = '';

if (mysqli_num_rows($query) > 0) {
	$row = mysqli_fetch_assoc($query);
	$data = gridPageProject($row);
}


/* Перебор мадиа файлов
------------------------------------------------------- */
$grid_row_i = 0;

function gridPageProject($arr) {
	$dat = '';
	$media = explode('(())', $arr['col_multimedia']);

	while ($value = current($media)) {
		$dat .= templateCell($value);
		next($media);
	}
	
	return $dat.'</div>';
}


/* Шаблон мадиа файлов
------------------------------------------------------- */
function templateCell($value) {
	
	global $icons, $grid_row_i;
	
	$part = explode(':*!', $value);
	
	$grid_row_html = '';
	
	if ($grid_row_i != $part[2]) {
		$grid_row_html = ($grid_row_i == 0) ? '' : '</div>';
		$grid_row_html .= '<div class="grid-row js-grid-row" id="js-grid-row'. $part[2] .'" data-row="'. $part[2] .'">
			<a href="#" class="grid-row-move grid-row-up js-grid-row-move" data-action="up">'. $icons['arrowUp'] .'</a>
			<a href="#" class="grid-row-move grid-row-down js-grid-row-move" data-action="down">'. $icons['arrowDown'] .'</a>
			<span class="grid-row-label">Блок '. $part[2] .'</span>
			<a href="#" class="grid-row-btn js-open-modal" data-modal-id="#js-modal" data-row="'. $part[2] .'">'. $icons['add2'] .'</a>';
	}
	
	$grid_row_i = $part[2];
	
	$media = '<img src="/multimedia/'. $part[0] .'" alt="">';
	
	$pos = strpos($part[0], 'mp4');
			
	if ($pos !== false) {
		$media = '<video src="/multimedia/'. $part[0] .'" playsinline="" muted="" loop="" autoplay="autoplay"></video>';
	}
	
	$pos = strpos($part[0], 'curtain');
		
	if ($pos !== false) {
		$media = $part[0];
	}
	
	$pos = strpos($part[0], 'kuula');
		
	if ($pos !== false) {
		$media = $part[0];
	}
	
	return $grid_row_html.'<div class="grid-item js-grid-item" data-url="'. htmlspecialchars($part[0]) .'" data-seo="'. $part[1] .'" data-row="'. $part[2] .'">
		'. $media .'
		<span class="grid-item-handle">'. $icons['handle'] .'</span>
		<div class="wrap-grid-item-edit">
			<a href="#" class="grid-item-edit js-open-context-menu">'. $icons['menu'] .'</a>
			<ul class="context-menu js-context-menu">
				<li class="context-menu-item js-grid-item-edit">'. $icons['edit'] .' Редактировать элемент</li>
				<li class="context-menu-item js-grid-item-del">'. $icons['del'] .' Удалить элемент</li>
			</ul>
		</div>
	</div>';
}


?>
<div id="wrap-content" class="settings">
	<div class="header">
		<a href="./projects.php"><?=$icons['prev']?></a>
    	<h1>Редактирование сетки <?=$row['col_title']?></h1>
    </div>
    <div id="content">
		<div class="grid" id="js-grid">
			<?php
			if (empty($data)) {
				echo '<div class="grid-row js-grid-row" id="js-grid-row1" data-row="1">
					<a href="#" class="grid-row-move grid-row-up js-grid-row-move" data-action="up">'. $icons['arrowUp'] .'</a>
					<a href="#" class="grid-row-move grid-row-down js-grid-row-move" data-action="down">'. $icons['arrowDown'] .'</a>
					<span class="grid-row-label">Блок 1</span>
					<a href="#" class="grid-row-btn js-open-modal" data-modal-id="#js-modal" data-row="1">'. $icons['add2'] .'</a>
				</div>';
			} else {
				echo $data;
			}
			?>
		</div> <!-- /.grid -->
		
		<div class="action2">
			<a href="#" class="button button2" id="js-grid-row-add">Добавить блок</a>
			<a href="#" class="button" id="js-grid-save" data-id="<?=$row['col_id']?>">Сохранить</a>
		</div>
    </div> <!-- /#content -->
</div> <!-- /#wrap-content -->


<div id="js-modal" class="fs-box fs-box1">
	<section>
		<div class="fs-form fs-form2 js-form">
			<div class="fs-form-container">
				<header class="header">
					<h3 id="js-modal-title">Новый медиа</h3>
					<i class="fa fa-close fs-close js-close" data-modal-id="#js-modal"></i>
				</header>
				<div class="fs-input">
					<div class="input">
						<label class="input__label">Выберите тип данных</label>
						<div class="input__field input__select js-open-form-select" id="js-media-type" data-value="0">
							<span>Изображение или видео</span>
							<div class="button-group button-group3 js-form-select"><?=selectOptions($media_types, 'js-option2')?></div>
						</div>
					</div> <!-- /.input -->
					
					<div class="media-section js-media-section" id="js-media-section0">
						<div class="input">
							<label class="input__label">
								<div>Изображение или видео</div>
								<div>
									<a href="#" class="icon2 js-file" data-wrap="#js-media-url"><?=$icons['upload']?></a>
									<a href="#" class="icon2 js-reset-file" data-wrap="#js-media-url"><?=$icons['reset']?></a>
								</div>
							</label>
							<div id="js-media-url" data-value=""></div>
						</div> <!-- /.input -->

						<div class="input">
							<label class="input__label">Подпись для изображения или видео</label>
							<input class="input__field" type="text" id="js-media-seo">
						</div>
					</div> <!-- /.media-section -->
					
					<div class="media-section hidden js-media-section" id="js-media-section1">
						<div class="input">
							<label class="input__label">Код</label>
							<textarea class="input__field input__text" id="js-code"></textarea>
						</div>
					</div> <!-- /.media-section -->
					
					<div class="media-section hidden js-media-section" id="js-media-section2">
						<div class="input">
							<label class="input__label">Тип шторки</label>
							<div class="input__field input__select js-open-form-select" id="js-curtain-type" data-value="0">
								<span>Горизонтальная</span>
								<div class="button-group button-group3 js-form-select"><?=selectOptions($curtain_types)?></div>
							</div>
						</div> <!-- /.input -->
						
						<div class="input">
							<label class="input__label">
								<div>Изображение 1</div>
								<div>
									<a href="#" class="icon2 js-file" data-wrap="#js-img1"><?=$icons['upload']?></a>
									<a href="#" class="icon2 js-reset-file" data-wrap="#js-img1"><?=$icons['reset']?></a>
								</div>
							</label>
							<div id="js-img1" data-value=""></div>
						</div> <!-- /.input -->
						
						<div class="input">
							<label class="input__label">
								<div>Изображение 2</div>
								<div>
									<a href="#" class="icon2 js-file" data-wrap="#js-img2"><?=$icons['upload']?></a>
									<a href="#" class="icon2 js-reset-file" data-wrap="#js-img2"><?=$icons['reset']?></a>
								</div>
							</label>
							<div id="js-img2" data-value=""></div>
						</div> <!-- /.input -->
					</div> <!-- /.media-section -->
				</div> <!-- /.fs-input -->
				<div class="action">
					<a href="#" class="button" id="js-grid-item-add" data-action="">Добавить</a>
				</div>
			</div> <!-- /.fs-form-container -->
		</div> <!-- /.fs-form -->
	</section>
</div> <!-- END id="js-modal" -->


<form id="upload" method="post" action="./handlers_a/upload.php" enctype="multipart/form-data">
	<input type="file" name="files" id="photoupload" class="js-form" />
</form>

<?php include_once './include_a/footer.php';