<?php include_once './include_a/header.php';

$sql = "SELECT 
			   `col_media`
		  FROM 
			   `tbl_team` ";
$query = mysqli_query($link, $sql);
mysqli_close($link);

$data = '';

if (mysqli_num_rows($query) > 0) {
	$row = mysqli_fetch_assoc($query);
	$data .= gridTeam($row);
}


/* Перебор мадиа файлов
------------------------------------------------------- */
function gridTeam($arr) {
	$dat = '';
	$media = explode('(())', $arr['col_media']);

	while ($value = current($media)) {
		$dat .= templateCell($value);
		next($media);
	}
	
	return $dat.'</div>';
}


/* Шаблон мадиа файлов
------------------------------------------------------- */
$grid_row_i = 0;

function templateCell($value) {
	
	global $icons, $grid_row_i;
	
	$part = explode(':*!', $value);
	
	$grid_row_html = '';
	
	if ($grid_row_i != $part[3]) {
		$grid_row_html = ($grid_row_i == 0) ? '' : '</div>';
		$grid_row_html .= '<div class="grid-row js-grid-row" id="js-grid-row'. $part[3] .'" data-row="'. $part[3] .'">
			<a href="#" class="grid-row-move grid-row-up js-grid-row-move" data-action="up">'. $icons['arrowUp'] .'</a>
			<a href="#" class="grid-row-move grid-row-down js-grid-row-move" data-action="down">'. $icons['arrowDown'] .'</a>
			<span class="grid-row-label">Блок '. $part[3] .'</span>
			<a href="#" class="grid-row-btn js-open-modal" data-modal-id="#js-modal" data-row="'. $part[3] .'">'. $icons['add2'] .'</a>';
	}
	
	$grid_row_i = $part[3];

	return $grid_row_html.'<div class="grid-item js-grid-item" data-type="'. $part[0] .'" data-url="'. $part[1] .'" data-seo="'. $part[2] .'" data-row="'. $part[3] .'" data-name="'. $part[4] .'" data-profession="'. $part[5] .'" data-description="'. htmlspecialchars($part[6]) .'">
		<img src="/multimedia/'. $part[1] .'" alt="">
		<span class="grid-item-handle">'. $icons['handle'] .'</span>
		<div class="wrap-grid-item-edit">
			<a href="#" class="grid-item-edit js-open-context-menu">'. $icons['menu'] .'</a>
			<ul class="context-menu js-context-menu">
				<li class="context-menu-item js-grid-team-edit">'. $icons['edit'] .' Редактировать элемент</li>
				<li class="context-menu-item js-grid-item-del">'. $icons['del'] .' Удалить элемент</li>
			</ul>
		</div>
	</div>';
}


?>
<div id="wrap-content" class="settings">
	<div class="header">
		<h1>Команда</h1>
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
			<a href="#" class="button" id="js-team-save">Сохранить</a>
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
						<div class="input__field input__select js-open-form-select" id="js-team-type" data-value="0">
							<span>Сотрудник</span>
							<div class="button-group button-group3 js-form-select"><?=selectOptions($team_types, 'js-option2')?></div>
						</div>
					</div> <!-- /.input -->

					<div class="media-section js-media-section" id="js-media-section0">
						<div class="input">
							<label class="input__label">
								<div>Фото</div>
								<div>
									<a href="#" class="icon2 js-file" data-wrap="#js-photo"><?=$icons['upload']?></a>
									<a href="#" class="icon2 js-reset-file" data-wrap="#js-photo"><?=$icons['reset']?></a>
								</div>
							</label>
							<div id="js-photo" data-value=""></div>
						</div> <!-- /.input -->

						<div class="input">
							<label class="input__label">Имя</label>
							<input class="input__field" type="text" id="js-name">
						</div>
						
						<div class="input">
							<label class="input__label">Профессия</label>
							<input class="input__field" type="text" id="js-profession">
						</div>
						
						<div class="input">
							<label class="input__label">Описание</label>
							<textarea class="input__field input__text" id="js-description"></textarea>
						</div>
					</div> <!-- /.media-section -->
					
					<div class="media-section hidden js-media-section" id="js-media-section1">
						<div class="input">
							<label class="input__label">
								<div>Изображение</div>
								<div>
									<a href="#" class="icon2 js-file" data-wrap="#js-media-url"><?=$icons['upload']?></a>
									<a href="#" class="icon2 js-reset-file" data-wrap="#js-media-url"><?=$icons['reset']?></a>
								</div>
							</label>
							<div id="js-media-url" data-value=""></div>
						</div> <!-- /.input -->

						<div class="input">
							<label class="input__label">Подпись для изображения</label>
							<input class="input__field" type="text" id="js-media-seo">
						</div>
					</div> <!-- /.media-section -->

				</div> <!-- /.fs-input -->
				<div class="action">
					<a href="#" class="button" id="js-grid-team-add" data-action="">Добавить</a>
				</div>
			</div> <!-- /.fs-form-container -->
		</div> <!-- /.fs-form -->
	</section>
</div> <!-- END id="js-modal" -->


<form id="upload" method="post" action="./handlers_a/upload.php" enctype="multipart/form-data">
	<input type="file" name="files" id="photoupload" class="js-form" />
</form>


<?php include_once './include_a/footer.php';