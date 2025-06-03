<?php
include_once './include/header.php';

/* Вывод услуг
------------------------------------------------------- */
$services = ''; //услуги
$i = 0;

$sql_s = "SELECT 
				 `col_title`,
				 `col_description`,
				 `col_video`
			FROM 
				 `tbl_services`
		ORDER BY 
				 `col_id` ";
$query_s = mysqli_query($link, $sql_s);
if (mysqli_num_rows($query_s) > 0) {
	while ($row_s = mysqli_fetch_assoc($query_s)) {
		$row_s['col_class'] = ($i % 2 === 0) ? 'services-item-left' : 'services-item-right';
		$services .= templateService($row_s);
		$i ++;
	}
}
?>
<div class="services"><?=$services?></div>
<?php include_once './include/footer.php';