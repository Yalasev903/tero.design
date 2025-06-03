<?php
/* Шаблон вопросов и ответов
------------------------------------------------------- */
function templateFAQ($arr) {
	return '<div class="faq-item">
		<a href="#" class="faq-question js-question">
			<svg class="faq-question-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.2 11.6">
				<path d="M10.6 9.6L20.2 0l1 1-10.6 10.6L0 1l1-1 9.6 9.6z" />
			</svg>
			'. $arr['col_question'] .'
		</a>
		<div class="faq-answer js-answer">'. $arr['col_answer'] .'</div>
	</div>';
}


/* Шаблон услуг
------------------------------------------------------- */
function templateService($arr) {
	return '<div class="services-item">
		<div class="'. $arr['col_class'] .'">
			<h2 class="services-item-title">'. $arr['col_title'] .'</h2>
			<p class="services-item-description">'. $arr['col_description'] .'</p>
		</div>
		<video src="multimedia/'. $arr['col_video'] .'" playsinline="" muted="" loop="" autoplay="autoplay" class="services-item-video"></video>
	</div>';
}


/* Сетка на странице "команда"
------------------------------------------------------- */
$grid_row_i3 = 0;

function gridTeam($arr) {
	$dat = '';
	$media = explode('(())', $arr['col_media']);
	
	while ($value = current($media)) {
		$dat .= templateTeam($value);
		next($media);
	}
	
	return $dat.'</div>';
}

/* Шаблон элемента сетки на странице "команда"
------------------------------------------------------- */
function templateTeam($media) {
	global $grid_row_i3;
	
	$part = explode(':*!', $media);
	
	$grid_row_html = '';
	
	if ($grid_row_i3 != $part[3]) {
		$grid_row_html = ($grid_row_i3 == 0) ? '<div class="grid-row">' : '</div><div class="grid-row">';
	}
	
	$grid_row_i3 = $part[3];
	
	if ($part[0] == 'employee') {
		$sub_html = "<div class='team-item-container'>
			<h2 class='team-item-name'>". htmlspecialchars($part[4]) ."</h2>
			<div class='team-item-profession'>". htmlspecialchars($part[5]) ."</div>
			<div class='team-item-description'>". htmlspecialchars($part[6]) ."</div>
		</div>";
		
		$media = '<a href="multimedia/'. $part[1] .'" class="grid-item grid-item-img js-img" data-sub-html="'. $sub_html .'">
			<img src="multimedia/'. $part[1] .'" alt="" class="js-grid-item-media">
			<div class="team-item-container">
				<h2 class="team-item-name">'. htmlspecialchars($part[4]) .'</h2>
				<div class="team-item-profession">'. htmlspecialchars($part[5]) .'</div>
			</div>
		</a>';
	} else {
		$media = '<div class="grid-item">
			<img src="multimedia/'. $part[1] .'" alt="'. $part[2] .'" class="js-grid-item-media">
		</div>';
	}
	
	return $grid_row_html.$media;
}

/* Перебор файлов на странице проекта
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


/* Шаблон медиа на странице проекта
------------------------------------------------------- */
function templateCell($value) {
	
	global $grid_row_i; 
	
	$part = explode(':*!', $value);
	$grid_row_html = '';
	
	if ($grid_row_i != $part[2]) {
		$grid_row_html = ($grid_row_i == 0) ? '<div class="grid-row">' : '</div><div class="grid-row">';
	}
	
	$grid_row_i = $part[2];
	
	$media = '<a href="multimedia/'. $part[0] .'" class="grid-item grid-item-img js-img">
			<img src="multimedia/'. $part[0] .'" alt="'. $part[1] .'" class="js-grid-item-media">
		</a>';
	
	$pos = strpos($part[0], 'mp4');
			
	if ($pos !== false) {
		$media = '<div class="grid-item">
			<video src="multimedia/'. $part[0] .'" playsinline="" muted="" loop="" autoplay="autoplay" onloadeddata="loadedVideo()" class="js-grid-item-media"></video>
		</div>';
	}
	
	$pos = strpos($part[0], 'curtain');
		
	if ($pos !== false) {
		$media = '<div class="grid-item grid-item-curtain">'. $part[0] .'</div>';
	}
	
	$pos = strpos($part[0], 'kuula');
		
	if ($pos !== false) {
		$media = '<div class="grid-item grid-item-360">'. $part[0] .'</div>';
	}
	
	return $grid_row_html.$media;
}


/* Перебор файлов обложки проектов
------------------------------------------------------- */
$grid_row_i2 = 0;
$project_id = 0;

function multimediaHomeFiles($arr) {
	$dat = '';
	$media = explode('(())', $arr['col_multimedia']);
	
	while ($value = current($media)) {
		$dat .= templateProject($value);
		next($media);
	}
	
	return $dat.'</div>';
}

/* Шаблон медиа на главной странице
------------------------------------------------------- */
function templateProject($media) {
	global $grid_row_i2, $project_id, $projects;
	
	$part = explode(':*!', $media);
	
	$grid_row_html = '';
	
	if ($grid_row_i2 != $part[2]) {
		$grid_row_html = ($grid_row_i2 == 0) ? '<div class="grid-row">' : '</div><div class="grid-row">';
	}
	
	$grid_row_i2 = $part[2];
	
	//Если проектов с одинаковым ID несколько, пропускаем все кроме первого
	$project_id_html = ($project_id != $part[3]) ? 'id="js-grid-item'. $part[3] .'"' : '';
	$project_id = $part[3];
	
	$pos = strpos($part[0], 'mp4');
	$class = ($part[4] == 0) ? 'grid-item-desktop' : '';
				
	if ($pos === false) {
		$media = '<a href="project.php?id='. $part[3] .'" class="grid-item '. $class .' grid-item-img" '. $project_id_html .'>
			<img src="multimedia/'. $part[0] .'" alt="'. $part[1] .'" class="js-grid-item-media">
			<h3 class="grid-item-title">'. $projects[$part[3]] .'</h3>
		</a>';
	} else {
		$media = '<a href="project.php?id='. $part[3] .'" class="grid-item '. $class .' grid-item-video" '. $project_id_html .'>
			<video src="multimedia/'. $part[0] .'" playsinline="" muted="" loop="" autoplay="autoplay" onloadeddata="loadedVideo()" class="js-grid-item-media"></video>
			<h3 class="grid-item-title">'. $projects[$part[3]] .'</h3>
		</a>';
	}
	
	//class="b-lazy"
	
	return $grid_row_html.$media;
}