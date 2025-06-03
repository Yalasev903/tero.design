<?php include_once './include_a/header.php';

$sql = "SELECT 
			   `col_id`,
			   `col_title`,
			   `col_description`,
			   `col_video`
		  FROM 
			   `tbl_services`
	  ORDER BY 
			   `col_id` ";
$query = mysqli_query($link, $sql);
mysqli_close($link);

$data = '';

if (mysqli_num_rows($query) > 0) {

	while ($row = mysqli_fetch_assoc($query)) {
		
		$data .= '<tr id="js-item'. $row['col_id'] .'">
			<td class="column">'. $row['col_id'] .'</td>
			<td class="column">'. $row['col_title'] .'</td>
			<td class="column">
				<video src="/multimedia/'. $row['col_video'] .'" playsinline="" muted="" loop="" autoplay="autoplay"></video>
			</td>
			<td class="column">
				<a href="./services_item.php?id='. $row['col_id'] .'&act=edit" class="icon2">'. $icons['edit'] .'</a>
				<a href="#" class="icon2 js-services-delete" data-id="'. $row['col_id'] .'">'. $icons['del'] .'</a>
			</td>
		</tr>';
	}
}
?>
<div id="wrap-content">
	<div class="header">
    	<h1>Услуги</h1>
		<a href="./services_item.php" class="button">Новая услуга</a>
    </div>
    <div id="content">
        <table id="js-data-table" class="data-table js-table3">
			<thead class="head">
				<tr>
					<th class="column">ID услуги</th>
					<th class="column">Название</th>
					<th class="column">Видео</th>
					<th class="column">Действие</th>
				</tr>
			</thead>
			<tbody><?=$data?></tbody>
		</table>
    </div> <!-- /#content -->
</div> <!-- /#wrap-content -->

<?php include_once './include_a/footer.php';