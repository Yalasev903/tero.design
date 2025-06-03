<?php include_once './include_a/header.php';

$sql_p = "SELECT
                 `col_id`,
				 `col_title`
		    FROM 
			     `tbl_projects`
	    ORDER BY 
			     `col_id` ";
$query_p = mysqli_query($link, $sql_p);

$options = '';
$opt_firts_id = 0;
$opt_firts_title = '';
$i = 1;

if (mysqli_num_rows($query_p) > 0) {
	while ($row_p = mysqli_fetch_assoc($query_p)) {
		
		if ($i == 1) {
			$opt_firts_id = $row_p['col_id'];
			$opt_firts_title = $row_p['col_title'];
		}
		
		$options .= '<a href="#" class="js-option" data-value="'. $row_p['col_id'] .'">'. $row_p['col_title'] .'</a>';
		$i ++;
	}
}

$sql = "SELECT 
			   `col_multimedia`
		  FROM 
			   `tbl_grid` ";
$query = mysqli_query($link, $sql);
mysqli_close($link);

$data = '';

if (mysqli_num_rows($query) > 0) {

	$row = mysqli_fetch_assoc($query);
	$data .= gridPageProject($row);
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
	
	$pos = strpos($part[0], 'mp4');
			
	if ($pos === false) {
		$media = '<img src="/multimedia/'. $part[0] .'" alt="">';
	} else {
		$media = '<video src="/multimedia/'. $part[0] .'" playsinline="" muted="" loop="" autoplay="autoplay"></video>';
	}
	
	$class = ($part[4] == 1) ? 'active' : '';

	return $grid_row_html.'<div class="grid-item js-grid-item" data-url="'. htmlspecialchars($part[0]) .'" data-seo="'. $part[1] .'" data-row="'. $part[2] .'" data-project="'. $part[3] .'" data-mob="'. $part[4] .'">
		'. $media .'
		<a href="#" class="grid-item-mob '. $class .' js-grid-item-mob">'. $icons['mob'] .'</a>
		<span class="grid-item-handle">'. $icons['handle'] .'</span>
		<div class="wrap-grid-item-edit">
			<a href="#" class="grid-item-edit js-open-context-menu">'. $icons['menu'] .'</a>
			<ul class="context-menu js-context-menu">
				<li class="context-menu-item js-grid-project-edit">'. $icons['edit'] .' Редактировать проект</li>
				<li class="context-menu-item js-grid-item-del">'. $icons['del'] .' Удалить проект</li>
			</ul>
		</div>
	</div>';
}


?>
<div id="wrap-content" class="settings">
	<div class="header">
		<h1>Обложки проектов</h1>
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
			<a href="#" class="button" id="js-grid-home-save">Сохранить</a>
		</div>
		
    </div> <!-- /#content -->
</div> <!-- /#wrap-content -->


<div id="js-modal" class="fs-box fs-box1">
	<section>
		<div class="fs-form fs-form2 js-form">
			<div class="fs-form-container">
				<header class="header">
					<h3 id="js-modal-title">Новый проект в обложку</h3>
					<i class="fa fa-close fs-close js-close" data-modal-id="#js-modal"></i>
				</header>
				<div class="fs-input">
					<div class="input">
						<label class="input__label">Выберите проект</label>
						<div class="input__field input__select js-open-form-select" id="js-project-id" data-value="<?=$opt_firts_id?>">
							<span><?=$opt_firts_title?></span>
							<div class="button-group button-group3 js-form-select"><?=$options?></div>
						</div>
					</div> <!-- /.input -->

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
						<label class="input__label">Подпись для изображение или видео</label>
						<input class="input__field" type="text" id="js-media-seo">
					</div>

				</div> <!-- /.fs-input -->
				<div class="action">
					<a href="#" class="button" id="js-grid-home-add" data-action="">Добавить</a>
				</div>
			</div> <!-- /.fs-form-container -->
		</div> <!-- /.fs-form -->
	</section>
</div> <!-- END id="js-modal" -->


<form id="upload" method="post" action="./handlers_a/upload.php" enctype="multipart/form-data">
	<input type="file" name="files" id="photoupload" class="js-form" />
</form>


<?php include_once './include_a/footer.php';