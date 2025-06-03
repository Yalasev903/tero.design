let $selected = 0; //открытый select в данный момент
let	$form_data = {}; //параметры для отправки файлов
let $sending_data = true; // true - отправка данных, false - нет

const $icons = {
	'upload': '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M990,917.1H10V512.6h269c10.6,0,19.2,8.6,19.2,19.2c0,10.6-8.6,19.2-19.2,19.2H48.4v327.7h903.2V551.1h-221c-10.7,0-19.2-8.6-19.2-19.2c0-10.6,8.6-19.2,19.2-19.2H990C990,512.6,990,917.1,990,917.1z"/><path d="M494.1,82.9c3,0,6,0.6,8.8,1.7c8.6,3.6,14.2,11.9,14.2,21.2v677.8c0,12.7-10.3,23-23,23c-12.7,0-22.9-10.3-22.9-23V161.3l-89.1,89.1c-8.9,9-23.5,9-32.4,0c-9.1-9-9.1-23.5,0-32.5L477.9,89.6C482.3,85.2,488.2,82.9,494.1,82.9z"/><path d="M494.9,82.9c5.8,0,11.7,2.2,16.2,6.7l128.3,128.3c9,9,9,23.5,0,32.5c-9,9-23.5,9-32.5,0L478.7,122.1c-9-9-9-23.5,0-32.5C483.2,85.1,489.1,82.9,494.9,82.9z"/></svg>',
	
	'reset': '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M990,213.3H826.7v714.5c0,22.6-18.3,40.9-40.9,40.9H214.2c-22.5,0-40.9-18.3-40.9-40.9V213.3H10v-39.1h285.8V72.1c0-22.6,18.3-40.8,40.9-40.8h326.6c22.5,0,40.9,18.3,40.9,40.8v102.1H990V213.3z M664.3,92c0-11.3-9.1-20.4-20.4-20.4H357.1c-11.3,0-20.4,9.1-20.4,20.4v82.2h327.6V92z M785.8,213.3H214.2v694.1c0,11.3,9.1,20.4,20.4,20.4h530.9c11.2,0,20.4-9.1,20.4-20.4V213.3z M581.7,376.6h40.8v387.9h-40.8V376.6z M377.5,376.6h40.8v387.9h-40.8V376.6z"></path></svg>',
	
	'edit': '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M990,418.3v462.8C990,941,941,990,881.1,990H118.9C59,990,10,941,10,881.1V118.9C10,59,59,10,118.9,10h462.7v40.8H130.3c-54.9,0-79.4,24.5-79.4,79.4v739.6c0,54.9,24.5,79.4,79.4,79.4h739.5c54.9,0,79.4-24.5,79.4-79.4V418.3H990z M882.7,59.5l57.7,57.7c15.9,15.9,15.9,41.8,0,57.7l-28.9,28.9L367.2,748.2v0H251.7V632.8L796.1,88.4l0,0L825,59.5C840.9,43.6,866.8,43.6,882.7,59.5z M796.1,146.1L295,647.2l-2,59.7l59.7-2v0l501.1-501L796.1,146.1z M853.8,88.4L825,117.2l57.8,57.8l28.9-28.9L853.8,88.4z"/></svg>',
	
	'del': '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M990,213.3H826.7v714.5c0,22.6-18.3,40.9-40.9,40.9H214.2c-22.5,0-40.9-18.3-40.9-40.9V213.3H10v-39.1h285.8V72.1c0-22.6,18.3-40.8,40.9-40.8h326.6c22.5,0,40.9,18.3,40.9,40.8v102.1H990V213.3z M664.3,92c0-11.3-9.1-20.4-20.4-20.4H357.1c-11.3,0-20.4,9.1-20.4,20.4v82.2h327.6V92z M785.8,213.3H214.2v694.1c0,11.3,9.1,20.4,20.4,20.4h530.9c11.2,0,20.4-9.1,20.4-20.4V213.3z M581.7,376.6h40.8v387.9h-40.8V376.6z M377.5,376.6h40.8v387.9h-40.8V376.6z"/></svg>',
	
	'add': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M480.4,764.2c-16.7,0-30.2-13.5-30.2-30.2V530.4H246.6c-16.7,0-30.2-13.5-30.2-30.2c0-16.7,13.5-30.2,30.2-30.2h203.6V266.4c0-16.7,13.5-30.2,30.2-30.2c16.7,0,30.2,13.5,30.2,30.2v203.6h203.6c16.7,0,30.2,13.5,30.2,30.2c0,16.7-13.5,30.2-30.2,30.2H510.6V734C510.6,750.7,497,764.2,480.4,764.2z M826.6,846.5c-191.3,191.4-501.6,191.4-693,0c-45.6-45.6-80-98.1-103.8-153.9l37.4-28.1C89,719.1,121.8,770.4,166,814.6c173.6,173.6,455.1,173.6,628.8,0c173.6-173.6,173.6-455.2,0-628.8C621.1,12.2,339.6,12.2,166,185.8c-39.2,39.2-69.1,84-90.5,131.8l-41.7-18.5c23.7-52.7,56.7-102.3,100-145.5c191.4-191.4,501.6-191.4,693,0C1018,344.9,1018,655.1,826.6,846.5z"/></svg>',
	
	'add2': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><g transform="translate(0.000000,511.000000) scale(0.100000,-0.100000)"><path d="M5045.9,4863.5c-34.6-8.1-83.4-40.7-105.7-69.1c-42.7-54.9-42.7-63.1-42.7-2206.8V433.6l-2341-4.1l-2341-6.1l-59-67.1C62.8,252.6,89.3,85.8,209.3,30.9C249.9,12.6,825.5,6.5,2578.8,6.5h2318.7v-2269.8v-2269.9l59-59c46.8-46.8,77.3-59,144.4-59c105.8,0,154.6,26.4,193.2,101.7c26.4,50.8,30.5,341.7,30.5,2308.5V6.5h2206.8c2400,0,2292.2-6.1,2345.1,111.9c40.7,89.5,28.5,166.8-34.6,238l-59,67.1l-2229.2,6.1l-2229.2,4.1v2141.7c0,2097,0,2141.7-40.7,2206.8c-22.4,34.6-42.7,65.1-44.7,65.1c-4.1,0-34.6,8.1-67.1,16.3C5137.4,4873.7,5082.5,4873.7,5045.9,4863.5z"></path></g></svg>',
	
	'mob': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><g transform="translate(0.000000,511.000000) scale(0.100000,-0.100000)"><path d="M3269.6,4833.7c-360.8-94.3-662.2-178.4-670.4-186.6c-8.2-8.2-102.5-178.4-211.2-375.2l-196.8-358.8V110v-3803.1l196.8-358.8c108.7-196.8,203-364.9,211.2-375.2c8.2-8.2,317.8-94.3,684.8-188.6l670.4-174.3H5000h1045.6l670.4,174.3c367,94.3,676.6,180.4,684.8,188.6c8.2,10.3,102.5,178.4,211.2,377.2l196.8,360.8V110v3799L7612,4269.9c-108.7,198.9-203,367-211.2,377.2c-8.2,8.2-315.7,94.3-684.8,188.6L6047.7,5010l-1062-2.1l-1062-2.1L3269.6,4833.7z M6543.8,4518c317.8-80,590.5-151.7,606.9-159.9c14.3-8.2,86.1-121,157.8-250.1c121-219.4,131.2-244,131.2-362.9v-129.2H5000H2560.2V3745c0,116.9,10.3,143.5,125.1,352.6c67.7,125.1,133.3,235.8,143.5,246c12.3,12.3,280.9,86.1,598.7,168.1l578.2,145.6l980,2.1l980,2.1L6543.8,4518z M7439.7,110v-3157.3H5000H2560.2V110v3157.3H5000h2439.7V110z M7439.7-3523c0-121-10.2-145.6-131.2-364.9c-71.8-129.2-143.5-241.9-157.8-250.1c-16.4-8.2-289.1-79.9-606.9-159.9l-578.2-143.5H5000h-963.6l-590.5,145.6c-426.4,106.6-598.7,157.9-623.3,186.6c-18.4,20.5-86.1,133.3-147.6,250.1c-102.5,192.7-114.8,225.5-114.8,338.3v125H5000h2439.7V-3523z"/><path d="M4302.9,4138.7v-174.3H5000h697.1v174.3v174.3H5000h-697.1V4138.7z"/><path d="M4651.5-3918.7v-174.3H5000h348.5v174.3v174.3H5000h-348.5V-3918.7z"/></g></svg>',
	
	'handle': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><g transform="translate(0.000000,511.000000) scale(0.100000,-0.100000)"><path d="M4881.1,4968.2c-34-26-244.1-248.1-468.2-496.2c-222.1-248.1-572.2-636.2-780.3-864.3c-370.1-412.1-374.1-416.1-374.1-508.2c0-190.1,200.1-278.1,344.1-150.1c36,32,314.1,332.1,616.2,668.2l550.2,608.2l6-1940.7c2-1068.4,2-1944.7-2-1948.7c-4-4-880.3-4-1948.7-2l-1940.7,6l600.2,540.2c724.3,654.2,734.3,664.2,734.3,768.3c0,144.1-140,242.1-274.1,194.1c-68-24-1728.6-1512.5-1800.6-1616.6c-54-74-58-146-10-220.1c18-30,428.2-410.1,910.3-844.3l878.3-792.3h90c162,0,256.1,156,184.1,304.1c-12,26-314.1,310.1-670.2,632.2L877.7-109.6h1950.7h1950.7v-1940.7c0-1066.4-4-1940.7-8-1940.7c-6,0-268.1,286.1-584.2,636.2c-318.1,348.1-590.2,644.2-608.2,658.2c-16,14-68,26-116,26c-124,0-204.1-80-204.1-206.1v-90l800.3-888.3c870.3-962.3,888.3-978.3,1018.3-916.3c32,16,420.1,428.1,860.3,916.3l802.3,888.3v90c0,126-80,206.1-204.1,206.1c-48,0-100-12-116.1-26c-18-14-292.1-312.1-610.2-664.2L5229.2-3999l-6,1936.7c-2,1066.4-2,1942.7,2,1946.7c4,4,880.3,4,1946.7,2l1936.7-6l-628.2-568.2c-346.1-312.1-646.2-586.2-664.2-610.2c-22-26-36-76-36-128c0-124.1,80-204.1,206.1-204.1h90l876.3,792.3c484.2,434.2,894.3,814.3,912.4,844.3c42,62,46,122.1,14,190.1c-34,76-1750.6,1620.6-1832.6,1648.6c-56,22-80,20-134.1-2c-142-60-178-220.1-74-336.1c32-36,332.1-314.1,668.2-616.2l608.2-550.2l-1940.7-6c-1068.4-2-1944.7-2-1948.7,2c-4,4-4,880.3-2,1948.7l6,1940.7l540.2-600.2c656.3-726.3,664.3-734.3,772.3-734.3c118,0,198.1,84,198.1,208.1c0,90-4,96-384.1,516.2c-212.1,234.1-564.2,624.2-782.3,866.3c-420.1,466.2-488.2,530.2-574.2,530.2C4969.1,5012.2,4917.1,4992.2,4881.1,4968.2z"/></g></svg>',

	'arrowUp': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M180.573 448C169.211 448 160 438.789 160 427.428V255.991H43.021c-7.125 0-10.695-8.612-5.66-13.653L209.444 70.035c8.036-8.046 21.076-8.047 29.112 0L410.64 242.338c5.035 5.041 1.464 13.653-5.66 13.653H288v171.437C288 438.79 278.789 448 267.427 448h-86.854m0 32h86.855C296.416 480 320 456.416 320 427.428V287.991h84.979c35.507 0 53.497-43.04 28.302-68.266L261.198 47.422c-20.55-20.576-53.842-20.58-74.396 0L14.719 219.724c-25.091 25.122-7.351 68.266 28.302 68.266H128v139.437C128 456.416 151.584 480 180.573 480z"></path></svg>',
	
	'arrowDown': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M267.427 64C278.789 64 288 73.211 288 84.572v171.437h116.979c7.125 0 10.695 8.612 5.66 13.653L238.556 441.965c-8.036 8.046-21.076 8.047-29.112 0L37.36 269.662c-5.035-5.041-1.464-13.653 5.66-13.653H160V84.572C160 73.211 169.211 64 180.573 64h86.854m0-32h-86.855C151.584 32 128 55.584 128 84.572v139.437H43.021c-35.507 0-53.497 43.04-28.302 68.266l172.083 172.303c20.55 20.576 53.842 20.58 74.396 0l172.083-172.303c25.091-25.122 7.351-68.266-28.302-68.266H320V84.572C320 55.584 296.416 32 267.427 32z"></path></svg>',
	
	'menu': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M941,402h-98c-27.1,0-49,21.9-49,49v98c0,27.1,21.9,49,49,49h98c27.1,0,49-21.9,49-49v-98C990,423.9,968.1,402,941,402z M549,402h-98c-27.1,0-49,21.9-49,49v98c0,27.1,21.9,49,49,49h98c27.1,0,49-21.9,49-49v-98C598,423.9,576.1,402,549,402z M157,402H59c-27.1,0-49,21.9-49,49v98c0,27.1,21.9,49,49,49h98c27.1,0,49-21.9,49-49v-98C206,423.9,184.1,402,157,402z"/></svg>'
};

$(function() {
	'use strict';
	
	/* Модальные окна
	------------------------------------------------------- */
	$('#page').on('click', '.js-open-modal', function(){
		let $d = $(this).data();
		$('#page').addClass('form-open');
		$($d.modalId).show().removeClass('bounceOutDown').addClass('bounceInUp');
		$('#js-grid-team-add').data('row', $d.row);
		$('#js-grid-item-add').data('row', $d.row);
		$('#js-grid-home-add').data('row', $d.row);
		return false;
	});
	
	
	$('.js-close').on('click', function(){
		var $modal = $($(this).data('modalId'));
		$modal.removeClass('bounceInUp').addClass('bounceOutDown');
		setTimeout(function() {
			$modal.hide();
			$('#page').removeClass('form-open');
		}, 700);
		
		resetForm();
		return false;
	});
	
	
	$(function(){
		$(document).on('click touchstart', function(event) {
			if ($('.fs-box1').is(':visible')) {
				if ($(event.target).closest('.js-form').length) { return; }
				var $modal = $('.fs-box1:visible');
				$modal.removeClass('bounceInUp').addClass('bounceOutDown');
				setTimeout(function() {
					$modal.hide();
					$('#page').removeClass('form-open');
				}, 700);
				resetForm();
				event.stopPropagation();
			}
		});
	});
	
	
	function resetForm() {
		let $modal_title = ($('#js-grid-home-save').length) ? 'Новый проект в обложку' : 'Новый медиа';
		
		$('#js-modal-title').text($modal_title);
		$('.js-media-section').addClass('hidden');
		$('#js-media-section0').removeClass('hidden');
		
		$('#js-media-type').data('value', 0).children('span').text('Изображение или видео');
		$('#js-media-url').data('value', '').empty();
		$('#js-media-seo').val('');
		$('#js-code').val('');
		$('#js-curtain-type').data('value', 0).children('span').text('Горизонтальная');
		$('#js-img1').data('value', '').empty();
		$('#js-img2').data('value', '').empty();
		$('#js-grid-item-add').data('action', '');
		$('#js-grid-home-add').data('action', '');
		
		$('#js-team-type').data('value', 0).children('span').text('Сотрудник');
		$('#js-photo').data('value', '').empty();
		$('#js-name').val('');
		$('#js-profession').val('');
		$('#js-description').val('');
		$('#js-grid-team-add').data('action', '');
	}
	
	
	if ($('#js-data-table').length) {
		
		let $table_settings = {};
		
		
		if ($('#js-data-table').hasClass('js-table3')) {
			$table_settings = {
				paging: false,
				info: false,
				"order": [[ 0, "desc" ]],
				"autoWidth": false,
				columnDefs: [
					{width: 200, targets: 0}, //ID
					{width: 300, targets: 1},
					{width: 200, orderable: false, targets: [2,3]}
				]
			};
		}
		
		
		if ($('#js-data-table').hasClass('js-table4')) {
			$table_settings = {
				paging: false,
				info: false,
				"order": [[ 0, "desc" ]],
				"autoWidth": false,
				columnDefs: [
					{width: 160, targets: 0}, //ID
					{width: 200, targets: [1,2]},
					{width: 300, targets: 3},
					{width: 200, orderable: false, targets: [4,5]}
				]
			};
		}
		
		
		if ($('#js-data-table').hasClass('js-table8')) {
			$table_settings = {
				paging: false,
				info: false,
				"order": [[ 0, "desc" ]],
				columnDefs: [
					{width: 200, targets: 0}, //ID вопроса
					{width: 200, orderable: false, targets: 2} //изображение, действие
				]
			};
		}
		
		
		var $t = $('#js-data-table').DataTable($table_settings);
		$('#js-data-table_length, #js-data-table_filter').wrapAll('<div class="table-container">');
		$('#js-data-table_info, #js-data-table_paginate').wrapAll('<div class="table-container">');
		
		var $form_select = '<div class="numrows">'+
			'<div class="sort-container js-write-form-select0" id="js-numrows-selected">10</div>'+
			'<div id="js-numrows-form" class="button-group js-form-select">'+
				'<a href="#" class="js-switch" data-value="0">10</a>'+
				'<a href="#" class="js-switch" data-value="1">25</a>'+
				'<a href="#" class="js-switch" data-value="2">50</a>'+
				'<a href="#" class="js-switch" data-value="3">100</a>'+
			'</div>'+
		'</div>';
		
		var $wrap_select_length = $('#js-data-table_length'),
			$select_length = $wrap_select_length.find('select');
		
		$select_length.hide().before($form_select);
		
		$('#js-numrows-form').children('.js-switch').on('click', function(){
			var $this = $(this),
				$value = $this.data('value');

			$('#js-numrows-selected').text($this.text());
			$('#js-numrows-form').hide();
			$select_length.children('option').eq($value).prop('selected', true);
			$select_length.change();
			return false;
		});
	}
	
	/* SELECT
	------------------------------------------------------- */
	$('#page').on('click', '.js-write-form-select0', function(){
		$('.js-form-select').hide();
		$(this).next('.button-group').show();
		return false;
	});
	
	
	$('#page').on('click', '.js-open-form-select', function(){
		$('.js-form-select').hide();
		$selected = $(this);
		$selected.children('.js-form-select').show();
		return false;
	});
	
	
	$(function(){
		$(document).on('click touchstart', function(event) {
			if ($('.js-form-select').is(':visible')) {
				if ($(event.target).closest('.js-form-select').length) { return; }
				$('.js-form-select').hide();
				event.stopPropagation();
			}
		});
	});
	
	
	$('#page').on('click', '.js-option', function(){
		var $this = $(this);
		$('.js-form-select').hide();
		$selected.data('value', $this.data('value')).children('span').text($this.text());
		
		//тип медиа
		if ($this.hasClass('js-option2')) {
			$('.js-media-section').addClass('hidden');
			$('#js-media-section'+ $this.data('value')).removeClass('hidden');
		}
		
		return false;
	});
	
	
	/* Вход в панель управления 
	------------------------------------------------------- */
	$('#js-login').on('click', function(){
		var $container = $('#page'),
			$password = $('#password-l'),
			$data = {}; //Ответ от сервера
		
		$.post('./handlers_a/ajax_sign_in.php',
			{
				login: $('#login-l').val(),
				password: $password.val()
			},
			function(data){ // Обработчик ответа от сервера
				$data = $.parseJSON(data);
				if ($data.name === 'signin') {
					window.location.href = $data.redirect;
				} else {
					$password.val('').blur();
					var $field = $('#'+ $data.name +'-l'),
						$offset = $field.offset(),
						$icon = $('<i class="fa fa-exclamation-circle icon-error"></i>');
					$container.children('.icon-error').remove();		
					$icon.css({top: $offset.top + 12, left: $offset.left + $field.width()});
					$container.prepend($icon);
					$container.children('.icon-error').delay(7000).fadeOut(500);
					popupInfo($data.error, true);
				}
  			});
		return false; 
	});
	
	
	/* Редактирование сетки проекта
	------------------------------------------------------- */
	$('#js-grid-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = $(this).data(); //передаем на сервер
		let	$data = {}; //ответ от сервера
		let $multimedia_str = '';
		let $elements = $('.js-grid-item');
		
		$elements.each(function(){
			let $this = $(this);
			let $this_d = $this.data();
			
			if ($multimedia_str !== '') { $multimedia_str += '(())'; }
			
			$multimedia_str += $this_d.url +':*!'+ $this_d.seo +':*!'+ $this_d.row;
		});
		
		$d.multimedia = $multimedia_str;
		
		$.post('./handlers_a/ajax_grid.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo('Изменения сохранены');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Добавить ряд в сетку
	------------------------------------------------------- */
	$('#js-grid-row-add').on('click', function (){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $row_html = '';
		let $rows = $('.js-grid-row');
		let $num = ($rows.length) ? parseInt($rows.last().data('row')) + 1 : 1;
		
		$row_html = '<div class="grid-row js-grid-row" id="js-grid-row'+ $num +'" data-row="'+ $num +'">'+
			'<a href="#" class="grid-row-move grid-row-up js-grid-row-move" data-action="up">'+ $icons.arrowUp +'</a>'+
			'<a href="#" class="grid-row-move grid-row-down js-grid-row-move" data-action="down">'+ $icons.arrowDown +'</a>'+			
			'<span class="grid-row-label">Блок '+ $num +'</span>'+
			'<a href="#" class="grid-row-btn js-open-modal" data-modal-id="#js-modal" data-row="'+ $num +'">'+ $icons.add2 +'</a>'+
		'</div>';
		
		$('#js-grid').append($row_html);
		$('.js-grid-row').sortable($sortable_opt).disableSelection();
		return false;
	});
	
	
	/* Редактирование элемента сетки
	------------------------------------------------------- */
	let $item_edit = '';

	$('#page').on('click', '.js-grid-item-edit', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $item = $(this).parents('.js-grid-item');
		let $d = $item.data();
		
		$item_edit = $item;

		if (($d.url).indexOf('curtain') + 1) {
			let $type_value = (($d.url).indexOf('curtain-v') + 1) ? 1 : 0;
			let $type_label = ($type_value === 1) ? 'Вертикальная' : 'Горизонтальная';
			let $img1 = $item.find('img').eq(0).attr('src');
			let $img2 = $item.find('img').eq(1).attr('src');
			
			$('#js-media-type').data('value', 2).children('span').text('Шторка');
			$('.js-media-section').addClass('hidden');
			$('#js-media-section2').removeClass('hidden');
			
			$('#js-curtain-type').data('value', $type_value).children('span').text($type_label);
			$('#js-img1').html('<img src="'+ $img1 +'" alt="">').data('value', $img1);
			$('#js-img2').html('<img src="'+ $img2 +'" alt="">').data('value', $img2);
		} else if (($d.url).indexOf('kuula') + 1) {
			$('#js-media-type').data('value', 1).children('span').text('Встроенный код панорамы');
			$('.js-media-section').addClass('hidden');
			$('#js-media-section1').removeClass('hidden');
			
			$('#js-code').val($d.url);
		} else {
			let $media = '';
			
			if (($d.url).indexOf('mp4') + 1) {
				$media = '<video src="/multimedia/'+ $d.url +'" playsinline="" muted="" loop="" autoplay="autoplay"></video>';
			} else {
				$media = '<img src="/multimedia/'+ $d.url +'" alt="">';
			}
			
			$('#js-media-type').data('value', 0).children('span').text('Изображение или видео');
			$('.js-media-section').addClass('hidden');
			$('#js-media-section0').removeClass('hidden');
			
			$('#js-media-url').html($media).data('value', $d.url);
			$('#js-media-seo').val($d.seo);
		}

		$('#js-modal-title').text('Редактирование медиа');
		$('#js-grid-item-add').data({
			'row': $d.row,
			'action': 'edit'
		});
		
		$('#page').addClass('form-open');
		$('#js-modal').show().removeClass('bounceOutDown').addClass('bounceInUp');
		
		return false;
	});
	
	
	/* Добавить элемент в ряд сетки
	------------------------------------------------------- */
	$('#js-grid-item-add').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = $(this).data();
		let $media_type = $('#js-media-type').data('value');
		let $item_html = '';
		let $item_html_static = '<span class="grid-item-handle">'+ $icons.handle +'</span>'+
		'<div class="wrap-grid-item-edit">'+
			'<a href="#" class="grid-item-edit js-open-context-menu">'+ $icons.menu +'</a>'+
			'<ul class="context-menu js-context-menu">'+
				'<li class="context-menu-item js-grid-item-edit">'+ $icons.edit +' Редактировать элемент</li>'+
				'<li class="context-menu-item js-grid-item-del">'+ $icons.del +' Удалить элемент</li>'+
			'</ul>'+
		'</div>';
		
		if ($media_type === 0) {
			let $media_url = $('#js-media-url');
			let $media_url_value = $media_url.data('value');
			let $media_url_html = $media_url.html();
			let $media_seo = $('#js-media-seo').val();
			
			if ($media_url_value === '') {
				popupInfo('Заполните данные', true);
				return false;
			}

			$item_html = '<div class="grid-item js-grid-item" data-url="'+ $media_url_value +'" data-seo="'+ htmlspecialchars($media_seo) +'" data-row="'+ $d.row +'">'+
				$media_url_html +
				$item_html_static +
			'</div>';
		} else if ($media_type === 1) {
			let $code = $('#js-code').val();
			
			if ($code === '') {
				popupInfo('Заполните данные', true);
				return false;
			}

			$item_html = '<div class="grid-item js-grid-item" data-url="'+ htmlspecialchars($code) +'" data-seo="" data-row="'+ $d.row +'">'+
				$code +
				$item_html_static +
			'</div>';   
		} else {
			let $type = $('#js-curtain-type').data('value');
			let $curtain_class = ($type === 1) ? 'curtain-v' : 'curtain-h';
			let $img1 = $('#js-img1').html();
			let $img2 = $('#js-img2').html();
			let $code = '<div class="curtain '+ $curtain_class +'">'+ $img1 + $img2 +'</div>';
			
			if ($img1 === '' || $img2 === '') {
				popupInfo('Заполните данные', true);
				return false;
			}

			$item_html = '<div class="grid-item js-grid-item" data-url="'+ htmlspecialchars($code) +'" data-seo="" data-row="'+ $d.row +'">'+
				$code +
				$item_html_static +
			'</div>';
		}
		
		if ($d.action === 'edit') {
			$item_edit.before($item_html);
			$item_edit.remove();
		} else {
			$('#js-grid-row'+ $d.row).append($item_html);
		}
		
		$('#js-modal').find('.js-close').click();
		return false;
	});
	
	
	/* Открыть меню элемента сетки
	------------------------------------------------------- */
	$('#page').on('click', '.js-open-context-menu', function (){
		let $this = $(this);
		$this.next('.js-context-menu').show();
		return false;
	});
	
	
	$(function(){
		$(document).on('click touchstart', function(event) {
			if ($('.js-context-menu').is(':visible')) {
				if ($(event.target).closest('.js-context-menu').length) { return; }
				$('.js-context-menu').hide();
				event.stopPropagation();
			}
		});
	});
	
	
	$('#page').on('mouseleave', '.js-grid-item', function (){
		$('.js-context-menu').hide();
	});
	
	
	/* Изменить порядок строки
	------------------------------------------------------- */
	$('#page').on('click', '.js-grid-row-move', function (){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $this = $(this);
		let $action = $this.data('action');
		let $row = $this.parents('.js-grid-row');
		
		if ($action === 'down') {
			
			if ($row.next('.js-grid-row').length) {
				let $next = $row.next('.js-grid-row');
				$next.after($row);
			}
		} else {
			if ($row.prev('.js-grid-row').length) {
				let $prev = $row.prev('.js-grid-row');
				$prev.before($row);
			}
		}
		
		sortGrid();
		return false;
	});
	
	
	function sortGrid() {
		let $i = 1;
		
		$('.js-grid-row').each(function(){
			let $row = $(this);
			
			$row.data('row', $i).attr('id', 'js-grid-row'+ $i);
			$row.find('.js-open-modal').data('row', $i);
			$row.find('.grid-row-label').text('Блок '+ $i);
			
			$row.find('.js-grid-item').each(function(){
				$(this).data('row', $i);
			});
		
			$i ++;
		});
	}
	
	
	/* sortable
	------------------------------------------------------- */
	const $sortable_opt = {
		handle: '.grid-item-handle',
		items: '.js-grid-item',
		placeholder: 'sortable-placeholder'
	};
	
	$('.js-grid-row').sortable($sortable_opt).disableSelection();
	
	
	/* Удаление элемента из сетки
	------------------------------------------------------- */
	$('#page').on('click', '.js-grid-item-del', function (){
		$(this).parents('.js-grid-item').remove();
		return false;
	});
	
	
	/* ячейка для мобильных устройств
	------------------------------------------------------- */
	$('#page').on('click', '.js-grid-item-mob', function (){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $this = $(this);
		let $grid_item = $this.parents('.js-grid-item');
		
		if ($this.hasClass('active')) {
			$this.removeClass('active');
			$grid_item.data('mob', 0);
		} else {
			$this.addClass('active');
			$grid_item.data('mob', 1);
		}
		
		return false;
	});
	
	
	/* Редактирование проекта в обложке проектов
	------------------------------------------------------- */
	$('#page').on('click', '.js-grid-project-edit', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $item = $(this).parents('.js-grid-item');
		let $d = $item.data();
		let $media = '';
		let $project_title = $('.js-option[data-value="'+ $d.project +'"]').text();
		
		$item_edit = $item;

		if (($d.url).indexOf('mp4') + 1) {
			$media = '<video src="/multimedia/'+ $d.url +'" playsinline="" muted="" loop="" autoplay="autoplay"></video>';
		} else {
			$media = '<img src="/multimedia/'+ $d.url +'" alt="">';
		}

		$('#js-project-id').data('value', $d.project).children('span').text($project_title);
		$('#js-media-url').html($media).data('value', $d.url);
		$('#js-media-seo').val($d.seo);

		$('#js-modal-title').text('Редактирование проекта');
		$('#js-grid-home-add').data({
			'row': $d.row,
			'action': 'edit'
		});
		
		$('#page').addClass('form-open');
		$('#js-modal').show().removeClass('bounceOutDown').addClass('bounceInUp');
		
		return false;
	});
	
	
	/* Добавление проекта в обложку проектов
	------------------------------------------------------- */
	$('#js-grid-home-add').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = $(this).data();
		let $item_html = '';
		let $project_id = $('#js-project-id').data('value');
		let $media_url = $('#js-media-url');
		let $media_url_value = $media_url.data('value');
		let $media_url_html = $media_url.html();
		let $media_seo = $('#js-media-seo').val();
		
		if ($media_url_value === '') {
			popupInfo('Заполните данные', true);
			return false;
		}
		
		$item_html = '<div class="grid-item js-grid-item" data-url="'+ $media_url_value +'" data-seo="'+ htmlspecialchars($media_seo) +'" data-row="'+ $d.row +'" data-project="'+ $project_id +'" data-mob="0">'+
			$media_url_html +
			'<a href="#" class="grid-item-mob js-grid-item-mob">'+ $icons.mob +'</a>'+
			'<span class="grid-item-handle">'+ $icons.handle +'</span>'+
			'<div class="wrap-grid-item-edit">'+
				'<a href="#" class="grid-item-edit js-open-context-menu">'+ $icons.menu +'</a>'+
				'<ul class="context-menu js-context-menu">'+
					'<li class="context-menu-item js-grid-project-edit">'+ $icons.edit +' Редактировать проект</li>'+
					'<li class="context-menu-item js-grid-item-del">'+ $icons.del +' Удалить проект</li>'+
				'</ul>'+
			'</div>';
		'</div>';
		
		if ($d.action === 'edit') {
			$item_edit.before($item_html);
			$item_edit.remove();
		} else {
			$('#js-grid-row'+ $d.row).append($item_html);
		}
		
		$('#js-modal').find('.js-close').click();
		return false;
	});
	
	
	/* Изменение порядка проектов в обложке проектов
	------------------------------------------------------- */
	$('#js-grid-home-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = $(this).data(); //передаем на сервер
		let	$data = {}; //ответ от сервера
		let $multimedia_str = '';
		let $elements = $('.js-grid-item');
		
		$elements.each(function(){
			let $this = $(this);
			let $this_d = $this.data();
			
			if ($multimedia_str !== '') { $multimedia_str += '(())'; }
			
			$multimedia_str += $this_d.url +':*!'+ $this_d.seo +':*!'+ $this_d.row +':*!'+ $this_d.project +':*!'+ $this_d.mob;
		});
		
		$d.multimedia = $multimedia_str;
		
		$.post('./handlers_a/ajax_grid_home.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo('Изменения сохранены');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Добавление / редактирование проекта
	------------------------------------------------------- */
	$('#js-projects-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = $(this).data(); //передаем на сервер
		let $title = $('#js-title').val();
		let	$success = 'Проект добавлен'; //сообщение в случае успеха
		let	$data = {}; //ответ от сервера
		
		if ($title === '') {
			popupInfo('Заполните данные', true);
			return false;
		}
	
		if ($d.action === 'edit') {
			$success = 'Изменения сохранены'; //сообщение в случае успеха
		}
		
		$d.meta_title = $('#js-meta-title').val();
		$d.meta_description = $('#js-meta-description').val();
		$d.meta_keywords = $('#js-meta-keywords').val();
		$d.title = $title;
		$d.text1 = $('#js-text1').find('.fr-element').html();
		$d.text2 = $('#js-text2').find('.fr-element').html();
		
		$.post('./handlers_a/ajax_projects.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo($success);
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});
		
		return false;
	});


	/* Удаление проекта
	------------------------------------------------------- */
	$('#page').on('click', '.js-projects-delete', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $id = $(this).data('id');
		let	$data = {}; //ответ от сервера

		$.post('./handlers_a/ajax_projects.php',
			{
				action: 'delete',
				id: $id
			}, 
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					$t.row('#js-item'+ $id).remove().draw(false);
					popupInfo('Проект удален');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Добавление редактирование вопроса
	------------------------------------------------------- */
	$('#js-faq-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = $(this).data(); //передаем на сервер
		let $question = $('#js-question').val();
		let	$success = 'Вопрос добавлен'; //сообщение в случае успеха
		let	$data = {}; //ответ от сервера
		
		if ($question === '') {
			popupInfo('Заполните данные', true);
			return false;
		}
		
		if ($d.action === 'edit') {
			$success = 'Изменения сохранены'; //сообщение в случае успеха
		}
		
		$d.question = $question;
		$d.answer = $('#js-answer').find('.fr-element').html();
		
		$.post('./handlers_a/ajax_faq.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo($success);
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});


	/* Удаление вопроса
	------------------------------------------------------- */
	$('#page').on('click', '.js-faq-delete', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $id = $(this).data('id');
		let	$data = {}; //ответ от сервера

		$.post('./handlers_a/ajax_faq.php',
			{
				action: 'delete',
				id: $id
			}, 
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					$t.row('#js-item'+ $id).remove().draw(false);
					popupInfo('Вопрос удален');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Добавление / редактирование услуги
	------------------------------------------------------- */
	$('#js-services-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = $(this).data(); //передаем на сервер
		let $title = $('#js-title').val();
		let	$success = 'Услуга добавлена'; //сообщение в случае успеха
		let	$data = {}; //ответ от сервера
		
		if ($title === '') {
			popupInfo('Заполните данные', true);
			return false;
		}
		
		if ($d.action === 'edit') {
			$success = 'Изменения сохранены'; //сообщение в случае успеха
		}
		
		$d.title = $title;
		$d.description = $('#js-description').val();
		$d.video = $('#js-video').data('value');

		$.post('./handlers_a/ajax_services.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo($success);
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});


	/* Удаление услуги
	------------------------------------------------------- */
	$('#page').on('click', '.js-services-delete', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $id = $(this).data('id');
		let	$data = {}; //ответ от сервера

		$.post('./handlers_a/ajax_services.php',
			{
				action: 'delete',
				id: $id
			}, 
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					$t.row('#js-item'+ $id).remove().draw(false);
					popupInfo('Услуга удалена');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Редактирование элемента сетки на странице "команда"
	------------------------------------------------------- */
	$('#page').on('click', '.js-grid-team-edit', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $item = $(this).parents('.js-grid-item');
		let $d = $item.data();
		
		$item_edit = $item;

		if ($d.type === 'employee') {
			let $media = '<img src="/multimedia/'+ $d.url +'" alt="">';
			
			$('#js-team-type').data('value', 0).children('span').text('Сотрудник');
			$('.js-media-section').addClass('hidden');
			$('#js-media-section0').removeClass('hidden');
			
			$('#js-photo').html($media).data('value', $d.url);
			$('#js-name').val($d.name);
			$('#js-profession').val($d.profession);
			$('#js-description').val($d.description);
		} else {
			let $media = '<img src="/multimedia/'+ $d.url +'" alt="">';
			
			$('#js-team-type').data('value', 1).children('span').text('Изображение');
			$('.js-media-section').addClass('hidden');
			$('#js-media-section1').removeClass('hidden');
			
			$('#js-media-url').html($media).data('value', $d.url);
			$('#js-media-seo').val($d.seo);
		}

		$('#js-modal-title').text('Редактирование медиа');
		$('#js-grid-team-add').data({
			'row': $d.row,
			'action': 'edit'
		});
		
		$('#page').addClass('form-open');
		$('#js-modal').show().removeClass('bounceOutDown').addClass('bounceInUp');
		
		return false;
	});
	
	
	/* Добавить элемент в ряд сетки на странице "команда"
	------------------------------------------------------- */
	$('#js-grid-team-add').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = $(this).data();
		let $team_type = $('#js-team-type').data('value');
		let $type = '';
		let $media_url = '';
		let $name = '';
		let $profession = '';
		let $description = '';
		let $media_seo = '';
		let $item_html = '';
		let $item_html_static = '<span class="grid-item-handle">'+ $icons.handle +'</span>'+
		'<div class="wrap-grid-item-edit">'+
			'<a href="#" class="grid-item-edit js-open-context-menu">'+ $icons.menu +'</a>'+
			'<ul class="context-menu js-context-menu">'+
				'<li class="context-menu-item js-grid-team-edit">'+ $icons.edit +' Редактировать элемент</li>'+
				'<li class="context-menu-item js-grid-item-del">'+ $icons.del +' Удалить элемент</li>'+
			'</ul>'+
		'</div>';
		
		if ($team_type === 0) {
			$type = 'employee';
			$media_url = $('#js-photo');
			$name = $('#js-name').val();
			$profession = $('#js-profession').val();
			$description = $('#js-description').val();
		} else {
			$type = 'img';
			$media_url = $('#js-media-url');
			$media_seo = $('#js-media-seo').val();
		}
		
		let $media_url_value = $media_url.data('value');
		let $media_url_html = $media_url.html();
		
		if ($media_url_value === '') {
			popupInfo('Заполните данные', true);
			return false;
		}
		
		$item_html = '<div class="grid-item js-grid-item" data-type="'+ $type +'" data-url="'+ $media_url_value +'" data-seo="'+ htmlspecialchars($media_seo) +'" data-row="'+ $d.row +'" data-name="'+ htmlspecialchars($name) +'" data-profession="'+ htmlspecialchars($profession) +'" data-description="'+ htmlspecialchars($description) +'">'+
			$media_url_html +
			$item_html_static +
		'</div>';
		
		if ($d.action === 'edit') {
			$item_edit.before($item_html);
			$item_edit.remove();
		} else {
			$('#js-grid-row'+ $d.row).append($item_html);
		}
		
		$('#js-modal').find('.js-close').click();
		return false;
	});


/* Редактирование сетки на странице "команда"
	------------------------------------------------------- */
	$('#js-team-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = $(this).data(); //передаем на сервер
		let	$data = {}; //ответ от сервера
		let $media_str = '';
		let $elements = $('.js-grid-item');
		
		$elements.each(function(){
			let $this = $(this);
			let $this_d = $this.data();
			
			if ($media_str !== '') { $media_str += '(())'; }
			
			$media_str += $this_d.type +':*!'+ $this_d.url +':*!'+ $this_d.seo +':*!'+ $this_d.row +':*!'+ $this_d.name +':*!'+ $this_d.profession   +':*!'+ $this_d.description;
		});
		
		$d.media = $media_str;
		
		$.post('./handlers_a/ajax_team.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo('Изменения сохранены');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Рабочий процесс
	------------------------------------------------------- */
	$('#js-workflow-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = {}; //передаем на сервер
		let	$data = {}; //ответ от сервера
		
		$d.description = $('#js-description').find('.fr-element').html();
		$d.poster = $('#js-poster').data('value');
		$d.video = $('#js-video').data('value');
		
		$.post('./handlers_a/ajax_workflow.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo('Изменения сохранены');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Видео в модальном окне
	------------------------------------------------------- */
	$('#js-showreel-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = {}; //передаем на сервер
		let	$data = {}; //ответ от сервера
		
		$d.poster = $('#js-poster').data('value');
		$d.video = $('#js-video').data('value');
		
		$.post('./handlers_a/ajax_showreel.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo('Изменения сохранены');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Карта Google
	------------------------------------------------------- */
	$('#js-map-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = {}; //передаем на сервер
		let	$data = {}; //ответ от сервера
		
		$d.lat = $('#js-lat').val();
		$d.lng = $('#js-lng').val();
		$d.zoom = $('#js-zoom').val();
		$d.marker = $('#js-marker').data('value');
		$d.key = $('#js-key').val();
		
		$.post('./handlers_a/ajax_map.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo('Изменения сохранены');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Privacy Policy
	------------------------------------------------------- */
	$('#js-privacy-policy-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = {}; //передаем на сервер
		let	$data = {}; //ответ от сервера
		
		$d.privacy_policy_page = $('#js-privacy-policy-page').find('.fr-element').html();
		$d.privacy_policy_modal = $('#js-privacy-policy-modal').find('.fr-element').html();
		
		$.post('./handlers_a/ajax_privacy_policy.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo('Изменения сохранены');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Страницы SEO
	------------------------------------------------------- */
	$('#js-pages-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = $(this).data(); //передаем на сервер
		let	$data = {}; //ответ от сервера
		
		$d.meta_title = $('#js-meta-title').val();
		$d.meta_description = $('#js-meta-description').val();
		$d.meta_keywords = $('#js-meta-keywords').val();
		$d.text = $('#js-text').find('.fr-element').html();

		$.post('./handlers_a/ajax_pages.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo('Изменения сохранены');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Настройки
	------------------------------------------------------- */
	$('#js-settings-save').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $d = {}; //передаем на сервер
		let	$data = {}; //ответ от сервера
		
		$d.jivochat = ($('#js-jivochat').hasClass('checked')) ? 1 : 0;
		$d.behance = $('#js-behance').val();
		$d.facebook = $('#js-facebook').val();
		$d.instagram = $('#js-instagram').val();
		$d.pinterest = $('#js-pinterest').val();
		$d.linkedin = $('#js-linkedin').val();
		$d.youtube = $('#js-youtube').val();
		$d.email = $('#js-email').val();
		$d.tel = $('#js-tel').val();
		$d.google_tm = $('#js-google-tm').val();
		
		$.post('./handlers_a/ajax_settings.php', $d,
			function(data) { // Обработчик ответа от сервера

				$data = $.parseJSON(data);

				if ($data.name === 'success') {
					popupInfo('Изменения сохранены');
				} else if ($data.name === 'error') {
					popupInfo($data.error, true);
				} else {
					popupInfo('Возникла ошибка', true);
				}
		});		
		return false;
	});
	
	
	/* Переключатель JivoChat
	------------------------------------------------------- */
	$('#js-jivochat').on('click', function (){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		let $this = $(this);
		
		if ($this.hasClass('checked')) {
			$this.removeClass('checked').children('.js-label').text('выкл');
		} else {
			$this.addClass('checked').children('.js-label').text('вкл');
		}
		
		return false;
	});
	
	
	/* Шаблон полей
	------------------------------------------------------- */
	function templateFields($value) {
		
		return '<div class="input-group js-group" id="js-group'+ $value +'" data-value="'+ $value +'">'+
			'<div class="wrap-inputs">'+
					'<div class="input">'+
					'<label class="input__label">'+
						'<div>Изображение или видео</div>'+
						'<div>'+
							'<a href="#" class="icon2 js-file" data-wrap="#js-media-url'+ $value +'">'+
								'<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M990,917.1H10V512.6h269c10.6,0,19.2,8.6,19.2,19.2c0,10.6-8.6,19.2-19.2,19.2H48.4v327.7h903.2V551.1h-221c-10.7,0-19.2-8.6-19.2-19.2c0-10.6,8.6-19.2,19.2-19.2H990C990,512.6,990,917.1,990,917.1z"/><path d="M494.1,82.9c3,0,6,0.6,8.8,1.7c8.6,3.6,14.2,11.9,14.2,21.2v677.8c0,12.7-10.3,23-23,23c-12.7,0-22.9-10.3-22.9-23V161.3l-89.1,89.1c-8.9,9-23.5,9-32.4,0c-9.1-9-9.1-23.5,0-32.5L477.9,89.6C482.3,85.2,488.2,82.9,494.1,82.9z"/><path d="M494.9,82.9c5.8,0,11.7,2.2,16.2,6.7l128.3,128.3c9,9,9,23.5,0,32.5c-9,9-23.5,9-32.5,0L478.7,122.1c-9-9-9-23.5,0-32.5C483.2,85.1,489.1,82.9,494.9,82.9z"/></svg>'+
							'</a>'+
						'</div>'+
					'</label>'+
					'<div class="js-media-url" id="js-media-url'+ $value +'" data-value=""></div>'+
				'</div>'+
				'<div class="input">'+
					'<label class="input__label">Подпись для изображения или видео</label>'+
					'<input class="input__field js-media-seo" type="text" value="" id="js-media-seo'+ $value +'">'+
				'</div>'+
			'</div>'+	
				
			'<a href="#" class="icon2 js-fields-del" data-value="'+ $value +'">'+
				'<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><path d="M990,213.3H826.7v714.5c0,22.6-18.3,40.9-40.9,40.9H214.2c-22.5,0-40.9-18.3-40.9-40.9V213.3H10v-39.1h285.8V72.1c0-22.6,18.3-40.8,40.9-40.8h326.6c22.5,0,40.9,18.3,40.9,40.8v102.1H990V213.3z M664.3,92c0-11.3-9.1-20.4-20.4-20.4H357.1c-11.3,0-20.4,9.1-20.4,20.4v82.2h327.6V92z M785.8,213.3H214.2v694.1c0,11.3,9.1,20.4,20.4,20.4h530.9c11.2,0,20.4-9.1,20.4-20.4V213.3z M581.7,376.6h40.8v387.9h-40.8V376.6z M377.5,376.6h40.8v387.9h-40.8V376.6z"></path></svg>'+
			'</a>'+
		'</div>';
	}
	
	
	/* Добавить поля
	------------------------------------------------------- */
	$('#js-fields-add').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		
		let $value = $('.js-group').length ? $('.js-group').first().data('value') : 1;
		
		$('#js-fields-group').prepend(templateFields($value));
		return false;
	});
	
	
	/* Удалить поля
	------------------------------------------------------- */
	$('#page').on('click', '.js-fields-del', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		var $value = $(this).data('value');
		$('#js-group'+ $value).remove();
		return false;
	});

	
	/* Загрузка файлов
	------------------------------------------------------- */
	if ($('#upload').length) {
		var $file_u; //загружаемый файл в данный момент

		$('#page').on('click', '.js-file', function(){
			
			if (! $sending_data) {
				return false;
			}

			$sending_data = false;

			setTimeout(function(){
				$sending_data = true;
			}, 1000);
			
			$form_data = $(this).data(); //параметры для отправки файлов
			$('#photoupload').click();
			return false;
		});


		$('#photoupload').change(function() {
			$file_u = this.files[0]; //загружаемый файл в данный момент
		});


		$('#upload').fileupload({
			change: function (e, data) {
				if (upFileValidate($file_u) === 'error') {
					
					popupInfo('Этот тип файла не поддерживается (разрешено MP4 JPG, PNG или GIF)', true);
					return false;
				}
			},

			add: function (e, data) {
				data.formData = $form_data; //параметры для отправки файлов
				var jqXHR = data.submit();
			},
			success: function (result, textStatus, jqXHR) {

				var $result = $.parseJSON(result);

				if ($result.code === '3') {
					let $file = $result.file;
					let	$wrap = $($file.wrap);
					let	$src = '/multimedia/'+ $file.name;
					
					$wrap.data('value', $file.name);
					
					if ($result.type === '.mp4') {
						popupInfo('Видео загружено');
						$wrap.html('<video src="'+ $src +'" playsinline="" muted="" loop="" autoplay="autoplay"></video>');
					} else {
						popupInfo('Изображение загружено');
						$wrap.html('<img src="'+ $src +'" alt="">');
					}
				} else {
					popupInfo('Возникла ошибка', true);	   
				}
			}
		});
	}
	
	
	/* Очистка загруженного файла
	------------------------------------------------------- */
	$('.js-reset-file').on('click', function(){
		
		if (! $sending_data) {
			return false;
		}

		$sending_data = false;

		setTimeout(function(){
			$sending_data = true;
		}, 1000);
		
		var $wrap = $($(this).data('wrap'));
		$wrap.empty().data('value', '');
		return false;
	});
	
	
	if ($('.fs-editor').length) {
		
		var $folder = 'other';
		
		if ($('#js-service-save').length) {
			$folder = 'services/other';
		}
		
		$('.fs-editor').froalaEditor({
			editorClass: 'froala-element',
			heightMin: 300,
			heightMax: 500,
			zIndex: 12,
			paragraphFormat: {
				N: 'Normal',
				H1: 'Heading 1',
				H2: 'Heading 2',
				H3: 'Heading 3',
				H4: 'Heading 4'
			},		
			toolbarButtons: [
				'bold', 'italic', 'paragraphFormat', 'formatOL', 'formatUL',
				'insertLink', 'undo', 'redo', 'selectAll'
			],
			htmlAllowedTags: ['p', 'h1', 'h2', 'h3', 'h4', 'br', 'img', 'ol', 'ul', 'li', 'a', 'strong', 'iframe'],
			linkInsertButtons: ['linkBack'],
			linkEditButtons: ['linkOpen', 'linkEdit', 'linkRemove'],
			language: 'ru'
		});
		
		
		$('.fs-editor').each(function(){
			$(this).children('div').last().remove();
		});
	}
	
});


function popupInfo($text, $error){
	'use strict';
	
	if ($('.popup-info').length) { 
		$('.popup-info').remove();
	}
		
	var $class = 'popup-info',
		$icon = 'fa fa-check-circle',
		$popupInfo = '';
		
	if ($error) {
		$class = 'popup-info popup-error';
		$icon = 'fa fa-exclamation-circle';
	}
		
	$popupInfo = $('<div><i class="'+ $icon +'"></i><p>'+ $text +'</p></div>')
	.addClass($class);
	$('#page').prepend($popupInfo);
	$('.popup-info').delay(7000).fadeOut(500);
}


/* Проверка файла перед загрузкой
------------------------------------------------------- */
function upFileValidate($file) {
	'use strict';
	
	let $allowed = ['mp4', 'jpg', 'jpeg', 'gif', 'png']; //список расширений файлов разрешенных для загрузки
	let	$parts = $file.name.split('.'); //разбиваем имя по разделителю "."
	let	$ext = ($parts.length > 1) ? $parts.pop() : ''; //расширение файла

	return ($.inArray($ext, $allowed) === -1) ? 'error' : '';
}


function htmlspecialchars(text) {
	var chars = Array("&", "<", ">", '"', "'");
	var replacements = Array("&amp;", "&lt;", "&gt;", "&quot;", "'");
	for (var i = 0; i < chars.length; i++) {
		var re = new RegExp(chars[i], "gi");
		if (re.test(text)) {
			text = text.replace(re, replacements[i]);
		}
	}
	return text;
}