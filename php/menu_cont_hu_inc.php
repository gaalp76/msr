<?php 
if (++$i == 1 && $container != "Home")
{
	$html .= "<div class='tile home' menu_id='0' menu='Home' style='background-image: url(\"img/common/home.png\")'>";
	$html .= "<span class='caption'>Kezdőoldal</span>";
	$html .= "</div>";
	$html .= "<div class='tile level-up' menu_id='0' menu='up' style='background-image: url(\"img/common/level-up.png\")'>";
	$html .= "<span class='caption'>Fel</span>";
	$html .= "</div>";
}
$html .= "<div class='tile' menu_id='".$menu["id"]."' upload_root_folder='".$menu["upload_folder_id"]."' menu='".$menu["name"]."'>";
$html .= "<span class='caption'>".$menu["caption"]."</span>";
$html .= "</div>";
?>
