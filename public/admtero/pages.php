<?php 
include_once './include_a/header.php';

$sql = "SELECT `col_id`, `col_title` FROM `tbl_pages` ORDER BY `col_id` ";
$query = mysqli_query($link, $sql);
mysqli_close($link);

$data = '';

if (mysqli_num_rows($query) > 0) {

	while ($row = mysqli_fetch_assoc($query)) {
		
		if ($row['col_id'] != 1) {
			$url = '/page_edit.php?id='. $row['col_id'];
		} else {
			$url = '/index_edit.php';
		}
		
		$data .= '<li class="item">
			<div class="title">'. $row['col_title'] .'</div>
			<div class="action">
				<a href="./pages_item.php?id='. $row['col_id'] .'" class="icon2">'. $icons['edit'] .'</a>
			</div>
		</li>';
	}
}
?>
<div id="wrap-content">
	<div class="header">
    	<h1>Страницы</h1>
    </div>
    <div id="content" class="pages">
        <div class="head">
			<div class="title">Страница</div>
			<div class="action">Редактировать</div>
		</div>
        <ul class="wrap-items"><?=$data?></ul>
    </div> <!-- /#content -->
</div> <!-- /#wrap-content -->

<?php include_once './include_a/footer.php';