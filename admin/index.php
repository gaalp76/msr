<?php 
	session_start();
	require_once "php/config.php"; 
	$config = new Config();
?>
<!doctype html>
<html lang="hu">
<head>
	
	<title><?php echo $config->BUSS_NAME?></title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" type="text/css" media="screen,projection" href="css/styles.css?v=1.0.0.3"  />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="css/jquery-ui-admin.css"  />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="css/jquery-confirm.css"  />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="css/open-data-table.css?v=1.0.0.1"  />
	<link rel="stylesheet" type="text/css" media="screen,projection" href="css/tableexport.css">
	<link rel="stylesheet" type="text/css" media="screen,projection" href="css/hide_cols.css">
	<link href="https://fonts.googleapis.com/css?family=Rajdhani" rel="stylesheet">
    <link href="css/dropzone.css" rel="stylesheet">

	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/main.js?v=1.0.0.4"></script>

	<script src="js/jquery-ui.js"></script>

	<script src="js/jquery.validate.js"></script>
	<script src="js/jquery-confirm.js"></script>
	<script src="js/jquery.maskedinput.js"></script>
	<script src="js/open-data-table.js"></script>
	<script src="ckeditor/ckeditor.js"></script>
	<script src="js/dropzone.js"></script>
	<script src="js/messages_hu.js"></script>
	<script src="js/filesaver.min.js"></script>
	<script src="js/xlsx.core.min.js"></script>
	<script src="js/tableexport.js"></script>
	<script src="js/tableheadfixer.js"></script>
	<script src="js/hide_cols.js"></script>
	
</head>

<body>
	<header>
		<span class="title"><?php echo $config->BUSS_NAME?></span>
	</header>

	<nav>
	
	</nav>
	<div id="message"></div>
	<main>
		
	</main>
	<footer>© copyright E-Gaal Software.</footer>
</body>
</html>