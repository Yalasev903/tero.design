<?php include_once './include_a/header.php';

$sql_w = "SELECT
			     `col_poster`,
				 `col_video`
		    FROM 
		         `tbl_workflow` ";
$query_w = mysqli_query($link, $sql_w);
if (mysqli_num_rows($query_w) > 0) {
	$row_w = mysqli_fetch_assoc($query_w);
}


$sql_t = "SELECT 
			     `col_media`
		    FROM 
			     `tbl_team` ";
$query_t = mysqli_query($link, $sql_t);
if (mysqli_num_rows($query_t) > 0) {
	$row_t = mysqli_fetch_assoc($query_t);
}


$sql_s = "SELECT
			     `col_showreel_poster`,
				 `col_showreel_video`
		    FROM 
		         `tbl_settings` ";
$query_s = mysqli_query($link, $sql_s);
if (mysqli_num_rows($query_s) > 0) {
	$row_s = mysqli_fetch_assoc($query_s);
}


$services_arr = array();

$sql_v = "SELECT 
			     `col_video`
		    FROM 
			     `tbl_services` ";
$query_v = mysqli_query($link, $sql_v);
if (mysqli_num_rows($query_v) > 0) {
	while ($row_v = mysqli_fetch_assoc($query_v)) {
		$services_arr[] = $row_v['col_video'];
	}
}


$projects_arr = array();

$sql_p = "SELECT 
			     `col_multimedia`
		    FROM 
			     `tbl_projects` ";
$query_p = mysqli_query($link, $sql_p);

if (mysqli_num_rows($query_p) > 0) {
	while ($row_p = mysqli_fetch_assoc($query_p)) {
		$projects_arr[] = $row_p['col_multimedia'];
	}
}


$sql_m = "SELECT 
			     `col_marker`
		    FROM 
		         `tbl_map` ";
$query_m = mysqli_query($link, $sql_m);
if (mysqli_num_rows($query_m) > 0) {
	$row_m = mysqli_fetch_assoc($query_m);
}


$sql_g = "SELECT 
			     `col_multimedia`
		    FROM 
			     `tbl_grid` ";
$query_g = mysqli_query($link, $sql_g);
if (mysqli_num_rows($query_g) > 0) {
	$row_g = mysqli_fetch_assoc($query_g);
}


$all_count = 0; //осталось файлов
$delete_count = 0; //удалено файлов

if (file_exists('../multimedia/')) {
	foreach (glob('../multimedia/*') as $file) {
		
		$delete = true;
		$file_name =  str_replace('../multimedia/', '', $file);

		//`tbl_workflow`
		if ($row_w['col_poster'] == $file_name || $row_w['col_video'] == $file_name) {
			$delete = false;
		}


		//`tbl_team`
		$pos_t = strpos($row_t['col_media'], $file_name);

		if ($pos_t !== false) {
			$delete = false;
		}


		//`tbl_settings`
		if ($row_s['col_showreel_poster'] == $file_name || $row_s['col_showreel_video'] == $file_name) {
			$delete = false;
		}

		
		//`tbl_services`
		if (array_search($file_name, $services_arr) !== false) {
			$delete = false;
		}
		
		
		//`tbl_projects`
		foreach ($projects_arr as $value) {
			$pos_p = strpos($value, $file_name);

			if ($pos_p !== false) {
				$delete = false;
			}
		}
		
		
		//`tbl_map`
		if ($row_m['col_marker'] == $file_name) {
			$delete = false;
		}
		
		
		//`tbl_grid`
		$pos_g = strpos($row_g['col_multimedia'], $file_name);

		if ($pos_g !== false) {
			$delete = false;
		}
		

		if ($delete) {
			unlink($file);
			$delete_count ++;
		}

		$all_count ++;
	}
}

$all_count -= $delete_count;

?>

<div id="wrap-content" class="settings">
	<div class="header">
    	<h1>Удаление неиспользуемых файлов</h1>
    </div>
    <div class="content content-form fs-form">
		<p class="delete-files">Удалено файлов: <?=$delete_count?></p>
		<p>Осталось файлов: <?=$all_count?></p>
    </div> <!-- END class="content" -->
</div> <!-- END id="wrap-content" -->

<?php include_once './include_a/footer.php';