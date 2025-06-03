<?php include_once './include_a/header.php';

$sql = "SELECT 
			   `col_id`,
			   `col_title`,
			   `col_multimedia`
		  FROM 
			   `tbl_projects`
	  ORDER BY 
			   `col_id` ";
$query = mysqli_query($link, $sql);
mysqli_close($link);

$data = '';

if (mysqli_num_rows($query) > 0) {

	while ($row = mysqli_fetch_assoc($query)) {
		
		$part = explode('(())', $row['col_multimedia']);
		$part2 = explode(':*!', $part[0]);
		
		$pos = strpos($part2[0], 'mp4');
				
		if ($pos === false) {
			$media = '<img src="/multimedia/'. $part2[0] .'" alt="">';
		} else {
			$media = '<video src="/multimedia/'. $part2[0] .'" playsinline="" muted="" loop="" autoplay="autoplay"></video>';
		}
		
		$data .= '<tr id="js-item'. $row['col_id'] .'">
			<td class="column">'. $row['col_id'] .'</td>
			<td class="column">'. $row['col_title'] .'</td>
			<td class="column">'. $media .'</td>
			<td class="column">
				<a href="./projects_grid.php?id='. $row['col_id'] .'" class="icon2">'. $icons['grid'] .'</a>
				<a href="./projects_item.php?id='. $row['col_id'] .'&act=edit" class="icon2">'. $icons['edit'] .'</a>
				<a href="#" class="icon2 js-projects-delete" data-id="'. $row['col_id'] .'">'. $icons['del'] .'</a>
			</td>
		</tr>';
	}
}
?>
<div id="wrap-content">
	<div class="header">
    	<h1>Проекты</h1>
		<a href="./projects_item.php" class="button">Новый проект</a>
    </div>
    <div id="content">
        <table id="js-data-table" class="data-table js-table3">
			<thead class="head">
				<tr>
					<th class="column">ID проекта</th>
					<th class="column">Название</th>
					<th class="column">Мультимедиа</th>
					<th class="column">Действие</th>
				</tr>
			</thead>
			<tbody><?=$data?></tbody>
		</table>
    </div> <!-- /#content -->
</div> <!-- /#wrap-content -->

<?php include_once './include_a/footer.php';