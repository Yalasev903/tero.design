<?php
include_once './include/sessions.php';
include_once './include/config.inc.php';
include_once './include/db.php';
include_once './include/functions.php';

/* Вывод настроек
------------------------------------------------------- */
$sql_st = "SELECT
				  `col_jivochat`,
				  `col_behance`,
				  `col_facebook`,
				  `col_instagram`,
				  `col_pinterest`,
				  `col_linkedin`,
				  `col_youtube`,
				  `col_email`,
				  `col_tel`,
				  `col_showreel_poster`,
				  `col_showreel_video`,
				  `col_privacy_policy_page`,
				  `col_privacy_policy_modal`,
				  `col_google_tm`
		     FROM 
		          `tbl_settings` ";
$query_st = mysqli_query($link, $sql_st);
$row_st = mysqli_fetch_assoc($query_st);
$tel_href = str_replace(['-', '(', ')', ' '], '', $row_st['col_tel']);


if (PAGE_URL == 'project') {
	/* Вывод проекта
	------------------------------------------------------- */
	$sql = "SELECT
				   `col_id`,
				   `col_meta_title`,
				   `col_meta_description`,
				   `col_meta_keywords`,
				   `col_title`,
				   `col_text1`,
				   `col_text2`,
				   `col_multimedia`
			  FROM 
			       `tbl_projects`
		     WHERE 
				   `col_id` = ". (int)$_GET['id'];
	$query = mysqli_query($link, $sql);
	$row = mysqli_fetch_assoc($query);
	
	$row['col_meta_title'] = (empty($row['col_meta_title'])) ? $row['col_title'] : $row['col_meta_title'];
	
} else {
	/* Вывод текста для SEO
	------------------------------------------------------- */
	$sql = "SELECT
				   `col_meta_title`,
				   `col_meta_description`,
				   `col_meta_keywords`,
				   `col_text`
			  FROM 
				   `tbl_pages`
			 WHERE 
				   `col_id` = ". (int)array_search(PAGE_URL, $config['pages']);
	$query = mysqli_query($link, $sql);
	$row = mysqli_fetch_assoc($query);
}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0, user-scalable=no" name="viewport">
	<title><?=$row['col_meta_title']?></title>
	<meta name="description" content="<?=$row['col_meta_description']?>">
	<meta name="keywords" content="<?=$row['col_meta_keywords']?>">
	<link rel="stylesheet" href="css/style.css" />
	
	<link rel='shortlink' href='https://tero.design/' />
	<link rel="icon" href="https://tero.design/img/tero200-200-150x150.png" sizes="32x32" />
	<link rel="icon" href="https://tero.design/img/tero200-200.png" sizes="192x192" />
	<link rel="apple-touch-icon-precomposed" href="https://tero.design/img/tero200-200.png" />
	<meta name="msapplication-TileImage" content="https://tero.design/img/tero200-200.png" />
	
	<meta name="description" content="<?=$row['col_meta_description']?>"/>
	<link rel="canonical" href="https://tero.design/" />
	<meta property="og:locale" content="ru_RU" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?=$row['col_meta_title']?>" />
	<meta property="og:description" content="<?=$row['col_meta_description']?>" />
	<meta property="og:url" content="https://tero.design/" />
	<meta property="og:site_name" content="Tero" />
	<meta property="og:image" content="https://tero.design/img/promo.jpg" />
	<meta property="og:image:secure_url" content="https://tero.design/img/promo.jpg" />
	<meta property="og:image:width" content="1280" />
	<meta property="og:image:height" content="768" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:description" content="<?=$row['col_meta_description']?>" />
	<meta name="twitter:title" content="<?=$row['col_meta_title']?>" />
	<meta name="twitter:image" content="https://tero.design/img/promo.jpg" />
	<script type='application/ld+json' class='yoast-schema-graph yoast-schema-graph--main'>{"@context":"https://schema.org","@graph":[{"@type":"WebSite","@id":"https://tero.design/#website","url":"https://tero.design/","name":"Tero","potentialAction":{"@type":"SearchAction","target":"https://tero.design/?s={search_term_string}","query-input":"required name=search_term_string"}},{"@type":"ImageObject","@id":"https://tero.design/#primaryimage","url":"https://tero.design/img/promo.jpg","width":1280,"height":768},{"@type":"WebPage","@id":"https://tero.design/#webpage","url":"https://tero.design/","inLanguage":"ru-RU","name":"<?=$row['col_meta_title']?>","isPartOf":{"@id":"https://tero.design/#website"},"primaryImageOfPage":{"@id":"https://tero.design/#primaryimage"},"datePublished":"2019-04-26T08:49:42+00:00","dateModified":"2020-07-22T16:31:30+00:00","description":"<?=$row['col_meta_description']?>"}]}</script>
	<!-- / Yoast SEO plugin. -->
</head>
<body id="page" class="loading">
	
	<div class="loader">
		<p>loading</p>
		<span></span>
	</div>
	
	<script>
		let $videoLoaded = 0;

		function loadedVideo() {
			$videoLoaded ++;
		}
	</script>
	
	<div class="wrapper">
		<div class="wrap-header">
			<header class="header row2" id="js-header">
				<a href="/" class="header-logo row">
					<img src="img/logo.png" alt="" class="header-logo-img">
					<span class="header-logo-text">Terodesign <span>studio</span></span>
				</a>
				<div class="header-right row">
					<nav class="header-nav row">
						<a href="#" class="header-link js-showreel-open">Showreel</a>
						<a href="services.php" class="header-link">Services</a>
						<a href="workflow.php" class="header-link">Workflow</a>
						<a href="team.php" class="header-link">Team</a>
						<a href="contact.php" class="header-link">Contact</a>
					</nav>

					<div class="header-share">
						<a href="#" class="header-share-toggle" id="js-share-toggle">
							<svg class="header-share-toggle-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 481.6 481.6">
								<path d="M381.6 309.4c-27.7 0-52.4 13.2-68.2 33.6l-132.3-73.9c3.1-8.9 4.8-18.5 4.8-28.4 0-10-1.7-19.5-4.9-28.5l132.2-73.8c15.7 20.5 40.5 33.8 68.3 33.8 47.4 0 86.1-38.6 86.1-86.1S429 0 381.5 0s-86.1 38.6-86.1 86.1c0 10 1.7 19.6 4.9 28.5l-132.1 73.8c-15.7-20.6-40.5-33.8-68.3-33.8-47.4 0-86.1 38.6-86.1 86.1s38.7 86.1 86.2 86.1c27.8 0 52.6-13.3 68.4-33.9l132.2 73.9c-3.2 9-5 18.7-5 28.7 0 47.4 38.6 86.1 86.1 86.1s86.1-38.6 86.1-86.1-38.7-86.1-86.2-86.1zm0-282.3c32.6 0 59.1 26.5 59.1 59.1s-26.5 59.1-59.1 59.1-59.1-26.5-59.1-59.1 26.6-59.1 59.1-59.1zM100 299.8c-32.6 0-59.1-26.5-59.1-59.1s26.5-59.1 59.1-59.1 59.1 26.5 59.1 59.1-26.6 59.1-59.1 59.1zm281.6 154.7c-32.6 0-59.1-26.5-59.1-59.1s26.5-59.1 59.1-59.1 59.1 26.5 59.1 59.1-26.5 59.1-59.1 59.1z" />
							</svg>
						</a>
						<div class="header-share-popup" id="js-share-popup">
							<a href="https://www.behance.net/terodesign" class="header-share-popup-link" target="_blank">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 11">
									<path d="M844.039 1368.96a3.016 3.016 0 0 0 .684-.07 1.678 1.678 0 0 0 .574-.23 1.138 1.138 0 0 0 .4-.43 1.477 1.477 0 0 0 .146-.7 1.384 1.384 0 0 0-.481-1.19 2.067 2.067 0 0 0-1.271-.35h-2.65v2.97h2.6zm-.134-4.6a1.818 1.818 0 0 0 1.073-.3 1.123 1.123 0 0 0 .416-.98 1.254 1.254 0 0 0-.136-.62 1.023 1.023 0 0 0-.376-.38 1.716 1.716 0 0 0-.536-.19 3.225 3.225 0 0 0-.627-.05h-2.281v2.52h2.467zm.315-4.36a6.483 6.483 0 0 1 1.445.14 3.3 3.3 0 0 1 1.118.44 2.121 2.121 0 0 1 .723.83 2.815 2.815 0 0 1 .253 1.26 2.3 2.3 0 0 1-.379 1.36 2.678 2.678 0 0 1-1.13.89 2.641 2.641 0 0 1 1.521 1.01 3.265 3.265 0 0 1 .178 3.15 2.876 2.876 0 0 1-.876.98 4.181 4.181 0 0 1-1.259.56 5.8 5.8 0 0 1-1.447.18H839V1360h5.22zm10.057 4.82a1.643 1.643 0 0 0-1.246-.44 1.9 1.9 0 0 0-.876.18 1.681 1.681 0 0 0-.558.43 1.46 1.46 0 0 0-.295.55 2.339 2.339 0 0 0-.1.51h3.583a2.025 2.025 0 0 0-.508-1.23zm-2.53 4.1a1.962 1.962 0 0 0 1.424.47 1.985 1.985 0 0 0 1.15-.32 1.375 1.375 0 0 0 .588-.7h1.94a3.588 3.588 0 0 1-1.43 2.02 4.21 4.21 0 0 1-2.322.61 4.649 4.649 0 0 1-1.71-.3 3.437 3.437 0 0 1-1.294-.84 3.742 3.742 0 0 1-.815-1.3 4.66 4.66 0 0 1-.288-1.67 4.454 4.454 0 0 1 .3-1.63 3.723 3.723 0 0 1 .839-1.31 3.913 3.913 0 0 1 1.3-.87 4.118 4.118 0 0 1 1.672-.32 4 4 0 0 1 1.782.38 3.622 3.622 0 0 1 1.25 1.04 4.206 4.206 0 0 1 .7 1.48 5.487 5.487 0 0 1 .153 1.75H851.2a2.144 2.144 0 0 0 .545 1.51zm-1-7.13h4.489v-1.07h-4.489v1.07z" transform="translate(-839 -1360)" />
								</svg>
							</a>
							<a href="https://www.facebook.com/terodesign" class="header-share-popup-link" target="_blank">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 18">
									<path d="M889 1361.95h1.714v-1.81a5.207 5.207 0 0 1 .553-2.79 2.981 2.981 0 0 1 2.66-1.35 10.115 10.115 0 0 1 3.073.33l-.429 2.76a5.462 5.462 0 0 0-1.381-.22 1.054 1.054 0 0 0-1.263.98v2.1h2.733l-.191 2.69h-2.542v9.36h-3.213v-9.36H889v-2.69z" transform="translate(-889 -1356)" />
								</svg>
							</a>
							<a href="https://www.instagram.com/t.e.r.o.d.e.s.i.g.n/" class="header-share-popup-link" target="_blank">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 169.063 169.063">
									<path d="M122.406 0H46.654C20.929 0 0 20.93 0 46.655v75.752c0 25.726 20.929 46.655 46.654 46.655h75.752c25.727 0 46.656-20.93 46.656-46.655V46.655C169.063 20.93 148.133 0 122.406 0zm31.657 122.407c0 17.455-14.201 31.655-31.656 31.655H46.654C29.2 154.063 15 139.862 15 122.407V46.655C15 29.201 29.2 15 46.654 15h75.752c17.455 0 31.656 14.201 31.656 31.655v75.752z" />
									<path d="M84.531 40.97c-24.021 0-43.563 19.542-43.563 43.563 0 24.02 19.542 43.561 43.563 43.561s43.563-19.541 43.563-43.561c0-24.021-19.542-43.563-43.563-43.563zm0 72.123c-15.749 0-28.563-12.812-28.563-28.561 0-15.75 12.813-28.563 28.563-28.563s28.563 12.813 28.563 28.563c0 15.749-12.814 28.561-28.563 28.561zM129.921 28.251c-2.89 0-5.729 1.17-7.77 3.22a11.053 11.053 0 0 0-3.23 7.78c0 2.891 1.18 5.73 3.23 7.78 2.04 2.04 4.88 3.22 7.77 3.22 2.9 0 5.73-1.18 7.78-3.22 2.05-2.05 3.22-4.89 3.22-7.78 0-2.9-1.17-5.74-3.22-7.78-2.04-2.05-4.88-3.22-7.78-3.22z" />
								</svg>
							</a>
							<a href="https://www.pinterest.com/terodesign" class="header-share-popup-link" target="_blank">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18">
									<path d="M983 1356a9 9 0 0 0-3.613 17.24 7.862 7.862 0 0 1 .157-2.06l1.158-4.91a3.418 3.418 0 0 1-.288-1.42c0-1.34.773-2.33 1.736-2.33a1.2 1.2 0 0 1 1.214 1.35 19.408 19.408 0 0 1-.8 3.19 1.4 1.4 0 0 0 1.421 1.74c1.705 0 2.854-2.19 2.854-4.79 0-1.97-1.329-3.45-3.745-3.45a4.264 4.264 0 0 0-4.431 4.31 2.6 2.6 0 0 0 .593 1.77.433.433 0 0 1 .129.5l-.183.72a.314.314 0 0 1-.451.23 3.473 3.473 0 0 1-1.843-3.44c0-2.56 2.156-5.62 6.433-5.62a5.374 5.374 0 0 1 5.7 5.15c0 3.53-1.963 6.17-4.856 6.17a2.572 2.572 0 0 1-2.2-1.12s-.522 2.07-.633 2.47a7.436 7.436 0 0 1-.906 1.93A9 9 0 1 0 983 1356z" transform="translate(-974 -1356)" />
								</svg>
							</a>
							<a href="https://www.linkedin.com/company/terodesign" class="header-share-popup-link" target="_blank">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17 17">
									<path d="M1022.04 1359.96a2.026 2.026 0 1 0-.03 0h.03zm1.82 13.04v-11.47h-3.64V1373h3.64zm2.02 0h3.64v-6.41a2.659 2.659 0 0 1 .12-.93 2 2 0 0 1 1.87-1.39c1.32 0 1.85 1.05 1.85 2.59v6.14h3.64v-6.58c0-3.52-1.8-5.16-4.2-5.16a3.608 3.608 0 0 0-3.3 1.93h.02v-1.66h-3.64c.04 1.08 0 11.47 0 11.47z" transform="translate(-1020 -1356)" />
								</svg>
							</a>
							<a href="https://www.youtube.com/channel/UCC0fmFeHb7rY35-_mCyzUXA" class="header-share-popup-link" target="_blank">
								 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 90">
									<path d="M70.9 65.8H66v-2.9c0-1.3 1-2.3 2.3-2.3h.3c1.3 0 2.3 1 2.3 2.3v2.9zm-18.5-6.1c-1.3 0-2.3.8-2.3 1.9v14c0 1 1 1.9 2.3 1.9 1.3 0 2.3-.8 2.3-1.9v-14c0-1.1-1-1.9-2.3-1.9zm30.1-7.8v26.5C82.5 84.8 77 90 70.2 90H19.8C13 90 7.5 84.8 7.5 78.4V51.9c0-6.4 5.5-11.6 12.3-11.6h50.5c6.7 0 12.2 5.2 12.2 11.6zM23.1 81.3v-28h6.3v-4.1H12.7v4.1h5.2v28h5.2zm18.8-23.8h-5.2v14.9c0 2.2.1 3.2 0 3.6-.4 1.2-2.3 2.4-3.1.1-.1-.4 0-1.6 0-3.6v-15h-5.2v14.8c0 2.3-.1 4 0 4.7.1 1.4.1 2.9 1.3 3.8 2.3 1.7 6.8-.3 8-2.7v3.1h4.2V57.5zm16.7 17.1V62.2c0-4.7-3.5-7.6-8.4-3.7v-9.2H45v31.9l4.3-.1.4-2c5.4 5 8.9 1.5 8.9-4.5zM74.9 73H71v2.7c0 1.2-1 2.1-2.1 2.1h-.8c-1.2 0-2.1-1-2.1-2.1V70h9v-3.4c0-2.5-.1-4.9-.3-6.3-.6-4.5-6.9-5.2-10.1-2.9-1 .7-1.7 1.7-2.2 2.9-.4 1.3-.7 3-.7 5.3V73c0 12.3 15 10.6 13.2 0zM54.8 32.7c.3.7.7 1.2 1.3 1.6.6.4 1.3.6 2.1.6s1.4-.2 2-.6c.6-.4 1.1-1 1.5-1.9l-.1 2h5.8V9.7h-4.6V29c0 1-.9 1.9-1.9 1.9-1 0-1.9-.9-1.9-1.9V9.7h-4.8v16.7c0 2.1 0 3.5.1 4.3.1.7.3 1.4.5 2zM37.2 18.8c0-2.4.2-4.2.6-5.6.4-1.3 1.1-2.4 2.1-3.2 1-.8 2.3-1.2 3.9-1.2 1.3 0 2.5.3 3.5.8s1.7 1.2 2.2 2c.5.8.9 1.7 1.1 2.6.2.9.3 2.2.3 4v6.3c0 2.3-.1 4-.3 5.1-.2 1.1-.6 2.1-1.1 3-.6.9-1.3 1.6-2.2 2.1-.9.4-2 .7-3.2.7-1.3 0-2.4-.2-3.4-.6-.9-.4-1.6-1-2.1-1.7-.5-.8-.9-1.7-1.1-2.8-.2-1.1-.3-2.7-.3-4.9v-6.6zm4.6 9.8c0 1.4 1 2.5 2.3 2.5 1.3 0 2.3-1.1 2.3-2.5V15.4c0-1.4-1-2.5-2.3-2.5-1.3 0-2.3 1.1-2.3 2.5v13.2zm-16.1 6.6h5.5v-19L37.7 0h-6l-3.4 12.1L24.7 0h-5.9l6.9 16.3v18.9z" />
								</svg>
							</a>
						</div> <!-- /.header-share-popup -->
					</div> <!-- /.header-share -->
				</div> <!-- /.column-right -->
				<a href="#" class="header-menu-trigger" id="js-menu-trigger"><span></span></a>
			</header> <!-- /.header -->
		</div> <!-- /.wrap-header -->