<?php
include_once './include/header.php';
date_default_timezone_set('Europe/Warsaw');
?>

		
<div class="contact">
	<div class="contact-map" id="js-map"></div>
	<div class="contact-left">
		<div class="contact-top">
			<div class="contact-location">POLAND, Kraków</div>
			<div class="contact-time">Current time <span id="js-time"><?=date('H:i')?></span></div>
		</div> <!-- /.contact-top -->
		<ul class="contact-list">
			<li><a href="mailto:<?=$row_st['col_email']?>"><?=$row_st['col_email']?></a></li>
			<li><a href="tel:<?=$tel_href?>"><?=$row_st['col_tel']?></a></li>
		</ul>
	</div> <!-- /.contact-left -->
</div> <!-- /.contact -->
		
		
<?php include_once './include/footer.php';