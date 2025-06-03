<?php
function selectOptions($arr, $class = '') {
	$html = '';
	
	while ($value = current($arr)) {	
		$html .= '<a href="#" class="js-option '. $class .'" data-value="'. key($arr) .'">'. $value .'</a>';
		next($arr);
	}
	return $html;
}

/* Шаблон полей формы
------------------------------------------------------- */
function templateFields($value) {
	
	global $icons;
	
	$part = explode(':*!', $value);
	
	$pos = strpos($part[0], 'mp4');
				
	if ($pos === false) {
		$media = '<img src="/multimedia/'. $part[0] .'" alt="">';
	} else {
		$media = '<video src="/multimedia/'. $part[0] .'" playsinline="" muted="" loop="" autoplay="autoplay"></video>';
	}
	
	return '<div class="input-group js-group" id="js-group'. $part[2] .'" data-value="'. $part[2] .'">
		<div class="wrap-inputs">
				<div class="input">
				<label class="input__label">
					<div>Изображение или видео</div>
					<div>
						<a href="#" class="icon2 js-file" data-wrap="#js-media-url'. $part[2] .'">'. $icons['upload'] .'</a>
					</div>
				</label>
				<div class="js-media-url" id="js-media-url'. $part[2] .'" data-value="'. $part[0] .'">'. $media .'</div>
			</div>
			<div class="input">
				<label class="input__label">Подпись для изображения или видео</label>
				<input class="input__field js-media-seo" type="text" value="'. $part[1] .'" id="js-media-seo'. $part[2] .'">
			</div>
		</div>
		<a href="#" class="icon2 js-fields-del" data-value="'. $part[2] .'">'. $icons['del'] .'</a>
	</div>';
}