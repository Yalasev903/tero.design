<?php
include_once './include/header.php';

/* Вывод команды
------------------------------------------------------- */
$team = ''; //команда

$sql_t = "SELECT 
				 `col_media`
			FROM 
				 `tbl_team`  ";
$query_t = mysqli_query($link, $sql_t);
if (mysqli_num_rows($query_t) > 0) {
	$row_t = mysqli_fetch_assoc($query_t);
	$team .= gridTeam($row_t);
}
?>


<div class="team">
	<h1 class="team-title">Meet us</h1>
	<div class="team-description"><?=$row['col_text']?></div>
</div> <!-- /.team -->


<div class="grid" id="js-gallery"><?=$team?></div>
<?php include_once './include/footer.php';