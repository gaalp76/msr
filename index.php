<?php 
	session_start();
	require_once("admin/php/config.php");
	$config = new Config();
?>
<!doctype html>
<html lang="hu">
<head>
	
	<title><?php echo $config->BUSS_NAME?></title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" type="text/css" media="screen,projection" href="gallery/css/unite-gallery.css"  />
	<link rel='stylesheet' type='text/css' media="screen,projection" href='gallery/themes/video/skin-right-no-thumb.css' />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="css/styles.css?v=1.1.4"  />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="css/jquery-ui.css"  />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="css/jquery-confirm.css"  />
	<link href="https://fonts.googleapis.com/css?family=Rajdhani" rel="stylesheet">
	


	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/main.js?v=1.1.2"></script>

	<script src="js/jquery-ui.js"></script>

	<script src="js/jquery.validate.js"></script>
	<script src="js/jquery-confirm.js"></script>
	<script src="js/jquery.maskedinput.js"></script>
	<script src="gallery/js/unitegallery.min.js"></script>
	<script src='gallery/themes/tiles/ug-theme-tiles.js' type='text/javascript' ></script>
	
	<?php
		if ( isset($_SESSION['lang']))
		{
			echo '<script src="js/messages_'.$_SESSION["lang"].'.js" id="lang_script"></script>';
		}
		else 
		{
			$config = new Config();
			echo '<script src="js/messages_'.$config->LANG_DEFAULT.'.js" id="lang_script"></script>';
		}
	?>

	
</head>

<body>
	
	<div id="wrapper">
		
		<nav>
			<div id="hamburger-menu">
				<div class="line"></div>
				<div class="line"></div>
				<div class="line"></div>
			</div>
			<div id="menu-box">
				
			</div>
			<div id="flag-container">
				<span class="flag" id="hu"><img src="img/hun_flag.jpg" id="hun-flag" alt="Magyar" title="Magyar"></span>
				<!--
				<span class="flag" id="en"><img src="img/en_flag.jpg" id="en-flag" alt="English" title="English"></span>
				<span class="flag" id="de"><img src="img/de_flag.jpg" id="de-flag"></span> -->
				<input type="hidden" name="lang" id="lang" value="<?php echo $_SESSION["lang"] ?>">
			</div>
			
		</nav>
		<div id="message"></div>	
		
		<div id="north-pointer"><img src="img/north-pointer.png"></div>
		<div id="logo" style="color:white"></div>
		<div id="information-box">
			<div class='head'>Aktualitások</div>
			<div class="content">
				<p>A honlap egyes részeinek tartalma feltöltés alatt van...<br>
				Megértését köszönjük!</p>
			</div>
			<div class="open-close-container">
					<div class="open-close open">
					</div>
					<div class="head-right">
						Aktualitások
					</div>
			</div>
		</div>
		<div id="soldier"></div>
		<div id="box-container">
			<div id="box1">
				<!--<video autoplay loop muted>
					<source src="video/MilitarySurvivalRun-2017.mp4" type="video/mp4">
				</video>-->
				<img src="img/box-img5.jpg">
			</div>	
			<div id="box2"><img src="img/box-img2.jpg"></div>
			<div id="box3"><img src="img/box-img4.jpg"></div>
		</div>
		<div id="hun-text">HUNGARY</div>
		<!--<div id="papa_cont">
			<span id="papa_label" class="city_label">Pápa</span>
			<div id="papa" class="city_marker"></div>
		</div>
		<div id="celldomolk_cont">
			<div id="celldomolk" class="city_marker"></div>
			<span id="celldomolk_label" class="city_label">Celldömölk</span>
		</div>
		-->
		<div id="msr-text"></div>

		<main >

			<div id="content">
				
			</div>

			
		</main>
		<footer>
			<div class="sponsors">
				<a href="www.vasuttechnika.hu"><img src="img/sponsor/vasuttechnika_logo.jpg"></a>
				<a href="http://www.gesztelyirt.hu/index.php?type=webpage&id=1"><img src="img/sponsor/gesztelyi_logo.jpg"></a>
				<a href="http://www.celldomolk.hu"><img src="img/sponsor/celldomolk_logo.jpg"></a>
				<a href="https://www.facebook.com/pages/Magyar-Honv%C3%A9ds%C3%A9g/107121199322175?fref=ts"><img src="img/sponsor/mh_logo.jpg"></a>
				<a href="www.honvedelem.hu"><img src="img/sponsor/bazisrepuloter_logo.jpg"></a>
				<a href="https://www.hososz.hu/"><img src="img/sponsor/hososz_logo.jpg"></a>
				<a href="https://www.scitecnutrition.com/hu/home.php"><img src="img/sponsor/scitec_logo.jpg"></a>
				<a href="http://www.hellenergy.com/hu/kezdolap/"><img src="img/sponsor/hell_logo.jpg"></a>
				<a href="http://www.tommarket.hu/"><img src="img/sponsor/tommarket_logo.jpg"></a>
				<a href="https://www.freskazone.net/"><img src="img/sponsor/freska_logo.jpg"></a>
				<a href="https://tesco.hu/"><img src="img/sponsor/tesco_logo.jpg"></a>
				<a href="http://watchman.hu/hu/"><img src="img/sponsor/wm_logo.jpg"></a>
				<a href="https://soproni.hu/age-gate?"><img src="img/sponsor/soproni_logo.jpg"></a>	
				<a href="http://ocrworldchampionships.com/"><img src="img/sponsor/ocr_logo.jpg"></a>
				<a href="http://www.tefutsz.hu/"><img src="img/sponsor/hocra_logo.jpg"></a>
				<a href="https://akadalyfutas.hu/"><img src="img/sponsor/tefutsz_logo.jpg"></a>
				<a href="https://www.decathlon.hu/"><img src="img/sponsor/decathlon_logo.png"></a>
				<a href="https://www.jufa.eu/hu/"><img src="img/sponsor/jufa_logo.jpg"></a>
				<a href="https://www.elte.hu/"><img src="img/sponsor/elte_logo.jpg"></a>
				<a href="https://prakticell.hu/"><img src="img/sponsor/prakticell_logo.jpg"></a>
				<a href="http://saghegykft.hu/"><img src="img/sponsor/saghegykft_logo.jpg"></a>
				<a href="http://vulkanfurdo.hu/"><img src="img/sponsor/vulkan_logo.jpg"></a>
			</div>
			<div class="copyright">© copyright E-Gaal Software.</div>
		</footer>
	</div>
	
</body>
</html>

