<?php include_once './include_a/header.php';

$sql = "SELECT
			   `col_privacy_policy_page`,
			   `col_privacy_policy_modal`
		  FROM 
		       `tbl_settings` ";
$query = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($query);
?>

<div id="wrap-content" class="settings">
	<div class="header">
    	<h1>Privacy Policy</h1>
    </div>
    <div class="content content-form fs-form">
		
		<div class="input">
			<label class="input__label">Страница</label>
			<div class="fs-editor" id="js-privacy-policy-page"><?=$row['col_privacy_policy_page'] ?? ''?></div>
		</div>
		
		<div class="input">
			<label class="input__label">Модальное окно</label>
			<div class="fs-editor" id="js-privacy-policy-modal"><?=$row['col_privacy_policy_modal'] ?? ''?></div>
		</div>
		
		<div class="action">
			<a href="#" class="button" id="js-privacy-policy-save">Сохранить</a>
		</div>

    </div> <!-- END class="content" -->
</div> <!-- END id="wrap-content" -->


<form id="upload" method="post" action="./handlers_a/upload.php" enctype="multipart/form-data">
	<input type="file" name="files" id="photoupload" class="js-form" />
</form>

<?php include_once './include_a/footer.php';