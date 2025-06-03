<?php include_once './include_a/header.php';

$sql = "SELECT 
			   `col_id`,
			   `col_question`,
			   `col_answer`
		  FROM 
			   `tbl_faq`
	  ORDER BY 
			   `col_id` ";
$query = mysqli_query($link, $sql);
mysqli_close($link);

$data = '';

if (mysqli_num_rows($query) > 0) {

	while ($row = mysqli_fetch_assoc($query)) {
		
		$data .= '<tr id="js-item'. $row['col_id'] .'">
			<td class="column">'. $row['col_id'] .'</td>
			<td class="column">'. $row['col_question'] .'</td>
			<td class="column">
				<a href="./faq_item.php?id='. $row['col_id'] .'&act=edit" class="icon2">'. $icons['edit'] .'</a>
				<a href="#" class="icon2 js-faq-delete" data-id="'. $row['col_id'] .'">'. $icons['del'] .'</a>
			</td>
		</tr>';
	}
}
?>
<div id="wrap-content">
	<div class="header">
    	<h1>FAQ</h1>
		<a href="./faq_item.php" class="button">Новый вопрос</a>
    </div>
    <div id="content">
        <table id="js-data-table" class="data-table js-table8">
			<thead class="head">
				<tr>
					<th class="column">ID вопроса</th>
					<th class="column">Вопрос</th>
					<th class="column">Действие</th>
				</tr>
			</thead>
			<tbody><?=$data?></tbody>
		</table>
    </div> <!-- /#content -->
</div> <!-- /#wrap-content -->

<?php include_once './include_a/footer.php';