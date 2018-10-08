<?php 

	include "../admin/php/config.php";
	include "../admin/php/connect.php";

	$db = Database::getConnection();
	$zip = isset($_GET["zip"])?$_GET["zip"]:"";
	
	if ($stmt = $db->prepare("SELECT name FROM city WHERE zip = ?"))
	{
		$stmt->bind_param("i", $zip);
		$stmt->execute();
		$stmt->store_result();
		$result = $stmt->bind_result($city);
		$stmt->fetch();
		echo $city;
	}
	else return -1;

?>