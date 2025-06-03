<?php
include_once './include/header.php';


/* Вывод описания и видео рабочего процесса
------------------------------------------------------- */
$sql_w = "SELECT
				  `col_description`,
				  `col_poster`,
				  `col_video`
		     FROM 
		          `tbl_workflow` ";
$query_w = mysqli_query($link, $sql_w);
$row_w = mysqli_fetch_assoc($query_w);


/* Вывод вопросов-ответов
------------------------------------------------------- */
$questions = ''; //вопросы-ответы

$sql_f = "SELECT 
				 `col_question`,
				 `col_answer`
			FROM 
				 `tbl_faq`
		ORDER BY 
				 `col_id` ";
$query_f = mysqli_query($link, $sql_f);
if (mysqli_num_rows($query_f) > 0) {
	while ($row_f = mysqli_fetch_assoc($query_f)) {
		$questions .= templateFAQ($row_f);
	}
}
?>


<div class="workflow">
	<div class="workflow-container">
		<h1 class="workflow-title">Workflow</h1>
		<div class="workflow-description"><?=$row_w['col_description']?></div>
	</div> <!-- /.workflow-container -->
	
	<div class="workflow-player js-player">
		<img src="multimedia/<?=$row_w['col_poster']?>" alt="" class="workflow-player-poster b-lazy">
		<a href="#" class="workflow-player-play js-video-play">
			<img src="img/play.png" alt="" class="b-lazy">
		</a>
		<video src="multimedia/<?=$row_w['col_video']?>" controls class="workflow-player-video js-video"></video>
	</div> <!-- /.workflow-player -->
	
	<div class="faq">
		<h2 class="workflow-title">Questions</h2>
		<?=$questions?>
	</div> <!-- /.faq -->
</div> <!-- /.workflow -->


<?php include_once './include/footer.php';