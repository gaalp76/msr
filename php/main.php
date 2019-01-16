<?php
session_start();

include 'phpmailer/src/PHPMailer.php';
include 'phpmailer/src/Exception.php';
include 'phpmailer/src/SMTP.php';


require_once "../admin/php/config.php";
require_once "../admin/php/connect.php";
require_once "../admin/php/news.php";
require_once "../admin/php/users.php";
require_once "common_users.php";
require_once "../admin/php/uploadmanager.php";
require_once "../admin/php/colleague.php";
require_once "../admin/php/basic_timestate_editor.php";
require_once "../admin/php/aboutus.php";
require_once "../admin/php/rules.php";
require_once "../admin/php/donation.php";
require_once "../admin/php/contacts.php";
require_once "../admin/php/story.php";
require_once "../admin/php/competition_obstacles.php";
require_once "../admin/php/competition_field_description.php";
require_once "../admin/php/competition_info.php";
require_once "../admin/php/competition_map.php";
require_once "../admin/php/competition_approach.php";
require_once "../admin/php/competition_entry.php";
require_once "../admin/php/competition.php";
require_once "gallery.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$db = Database::getConnection();
$user = new CommonUser($db,2);
$returnArray["languages"] = $user->LANGUAGE;

$menu = isset($_GET["menu"])?$_GET["menu"]:"";
$linkedTo = isset($_GET["linked_to"])?$_GET["linked_to"]:"";
$instanceOf = isset($_GET["instanceOf"])?$_GET["instanceOf"]:"";
$_SESSION["lang"]=isset($_SESSION["lang"])?$_SESSION["lang"]:$user->LANG_DEFAULT;
	
//$_GET["login"] = 1;
if (isset($_GET["menu"]) && $_GET["menu"] == "Login")
{
	
	$returnArray["message"] = $user->commonUserLogin($_GET["username"],$_GET["password"]);
}
else
{
	//$returnArray["state"] = "login";
	//$returnArray["content"] = $user->getUserLoginForm();
}

if(isset($_SESSION["logedin"]) && $_SESSION["logedin"] == 1)
{
	$user = new CommonUser($db,1);
	$user ->userID = isset($_SESSION["userID"])?$_SESSION["userID"]:0;
	$returnArray["logout"] = $user->getLogoutButton($_SESSION["lang"]);
	if ($menu == "logout") {
		$returnArray["message"] = $user->logoutUser();
	}		

	/*		$returnArray["message"] = $user->logoutUser();
			break;
		
		default:
			# code...
			break;
	}
	*/

}
if(isset($_GET["upload_root_folder"]) && $_GET["upload_root_folder"])
{
	$uploadRootFolderID =$_GET["upload_root_folder"];
}
else if(isset($_SESSION["uploadRootFolderID"]))
{
	$uploadRootFolderID = $_SESSION["uploadRootFolderID"];
}

/*
$_SESSION["menu"] = $menu;
$_SESSION["linkedTo"] = $linkedTo;
$_SESSION["uploadRootFolderID"] = $uploadRootFolderID;
$_SESSION["instanceOf"] = $instanceOf;
*/

switch ($menu)
{	
	case $linkedTo."ChangeLanguage".$instanceOf:
		$_SESSION["lang"] = $_GET["lang"];
	break;

	case "CheckOpenCompetitions":
		$competition = new Competition($db);
		$returnArray["content"] = $competition -> getOpenCompetitions($_SESSION["lang"]);
	break;

	case $linkedTo."LoadMenuItems".$instanceOf:
		
		$returnArray["content"] = $user->getUserMenu($_SESSION["lang"]);
		//if (isset($_SESSION["logedin"]) && $_SESSION["logedin"] == 1) $returnArray["login_container"] = $user->getLogoutButton();
		//else 

		$returnArray["login_container"] = $user->getUserLoginForm($_SESSION["lang"]);
	break;

	case $linkedTo."ForgotPassword".$instanceOf:
		$returnArray["message"] = $user->sendMailForgotPassword($_GET["username"], $_SESSION["lang"]);
	break;

	case $linkedTo."ForgotPasswordForm".$instanceOf:
		$returnArray["content"] = $user->getForgotPasswordForm($_GET["username"], $_GET["hash"], $_GET["lang"]);
	break;

	case $linkedTo."ChangePassword".$instanceOf:
		$returnArray["message"] = $user->changePassword($_GET["forgot_pw_username"], $_GET["new_password"], $_GET["hash"]);
	break;

	case $linkedTo."ConfirmRegistration".$instanceOf:
		$returnArray["message"] = $user->ConfirmRegistration($_GET["username"], $_GET["hash"], $_GET["lang"]);
	break;

	case $linkedTo."ConfirmCompetitionRegistration".$instanceOf:
					$CompetitionEntry = new CompetitionEntry($db);
					
					$returnArray["message"] = $CompetitionEntry->confirmCompRegistration($_GET["comp_reg_id"], $_GET["competitionID"], $_GET["lang"]);
				break;

	case $linkedTo."Home".$instanceOf:
	break;

	case $linkedTo."Msr".$instanceOf:
	break;

// ******************************************************************************
// *                              Felhasználókezlés                             *                              
// ******************************************************************************

	case $linkedTo."Users".$instanceOf:
	break;

	case $linkedTo."UsersAdd".$instanceOf:
		
		if(isset($_GET["action"]))
		{
			switch($_GET["action"])
			{
				case "checkUserExists":
					echo $user->checkUsername($_GET["reg_username"]);
					return;
				break;
				case "checkUserEmailExists":
					echo $user->checkEmail($_GET["email"]);
					return;
				break;
				case "checkEmailInCompetititon":
					echo $user->checkEmailInCompetititon($_GET["email"],$_GET["competition_id"]);
					return;
				break;
				case "addCommonUser":
					$returnArray["message"] = $user -> addCommonUser(	
																$_GET["lastname"],
																$_GET["firstname"],
																$_GET["phone"],
																$_GET["reg_username"],
																$_GET["reg_password"],
																$_GET["confirm_reg_password"],
																$_GET["email"],
																isset($_GET["pid"])?$_GET["pid"]:"",
																$_GET["country"],
																$_GET["mailing-zip"],
																$_GET["mailing-city"],
																$_GET["mailing-address"],
																$_GET["mother_name"],
																$_GET["born_date"],
																$_GET["sex"]
																);

					$user->userID = 0;
					
				break;
			}
		}
		
		$returnArray["content"] = $user->getUserRegisterForm($_SESSION["lang"]);
		
	break;

	case $linkedTo."UserRemove".$instanceOf:
		if (isset($user ->userID) && $user->userID > 0)
		{
			$returnArray["message"] = $user->removeUser( $user->userID);
			$user->logoutUser();
			$returnArray["login_container"] = $user->getUserLoginForm($_SESSION["lang"]);
		}
	break;

	case $linkedTo."UsersEdit".$instanceOf:
		
		if(isset($_GET["action"]))
		{
			switch($_GET["action"])
			{
				case "editUser":
					$returnArray["message"] = $user->updateCommonUser(
																	$_GET["lastname"],
																	$_GET["firstname"],
																	$_GET["phone"],
																	$_GET["reg_password"],
																	$_GET["confirm_reg_password"],
																	isset($_GET["pid"])?$_GET["pid"]:"",
																	$_GET["country"],
																	$_GET["mailing-zip"],
																	$_GET["mailing-city"],
																	$_GET["mailing-address"],
																	$_GET["mother_name"],
																	$_GET["born_date"],
																	$_GET["sex"],
																	$_GET["email"]

														);
					$user->userID = 0;
				break;
				case "showRegisterForm":
					$returnArray["content"] = $user->getUserRegisterForm($_SESSION["lang"],$user->getUserData($user->userID));
				break;
			}
		}

		
	break;

	case $linkedTo."UsersDelete".$instanceOf:
		$user->userID = isset($_GET["userID"])?$_GET["userID"]:0;
		if(isset($_GET["delete"]))
		{
			$returnArray["message"] = $user->deleteUser();
		}
		$returnArray["content"] = $user->getUserForm("Felhasználó törlése","Törlés", 0, 1, 1);
	break;

	case $linkedTo."UsersAuthority".$instanceOf:
		$user->userID = isset($_GET["userID"])?$_GET["userID"]:0;
		if(isset($_GET["authority"]))
		{
			$returnArray["message"] = $user->saveUserAuthority(
																$_GET["menu_name"],
																$_GET["menuID"], 
																$_GET["authority"]
																);
			$returnArray["ontop"] = 0;
		}
		$returnArray["content"] = $user->getAuthorityUserForm();
	break;

/******************************************************************************
 *                              Hírek                                         *
 ******************************************************************************/
			case $linkedTo."NewsView".$instanceOf:
				$uploadManagerObj = new UploadManager($db);
				$news = new News($db);
				if(isset($_SESSION["newsID"]))
				{
					$mainID = $news->getNewsMainID($_SESSION["newsID"]);
					$_SESSION["newsID"] = $news -> getNewsIDFromMainNews($mainID,$_SESSION["lang"]);
					$_SESSION["newsID"] = isset($_GET["newsID"])?$_GET["newsID"]:$_SESSION["newsID"];
				}
				else
				{
					$_SESSION["newsID"] = $_GET["newsID"];
				}
				$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "OfNews", $_SESSION["newsID"]);

				$returnArray["news_search_form"] 	= $news -> getNewsSearchForm($linkedTo,1);
				$returnArray["news_search_icon"] 	= $news -> getNewsSearchIcon(); 
				$returnArray["content"] 			= $news -> getNewsView( $_SESSION["newsID"]); 
			break;
			case $linkedTo."News".$instanceOf:
				$_SESSION["newsID"] = 0;
			case $linkedTo."NewsSearch".$instanceOf:
				$news = new News($db);
				$returnArray["news_search_form"] 	= $news -> getNewsSearchForm($linkedTo,1);
				$returnArray["banner"] = "news-banner";
				
				if(isset($_GET["action"]) && $_GET["action"] == "showNewsResult")
				{

					$returnArray["content"] .= $news->getNewsSearchResult(	
																		htmlentities($_GET["title"]), 
																		htmlentities($_GET["author"]), 
																		$_GET["start_date"], 
																		$_GET["end_date"], 
																		1, 
																		htmlentities($_GET["keyword"]), 
																		$linkedTo,
																		$_SESSION["lang"]
																	);
					if(!$returnArray["content"])
					{
						$returnArray["message"] = "empty_news_search";
					}
					$returnArray["ontop"] = 1;
				}
				else
				{
					$returnArray["content"] = $news->getNewsSearchResult(	
																		"", 
																		"", 
																		date("Y-m-d", strtotime("-6 months")), 
																		"", 
																		1, 
																		"", 
																		$linkedTo,
																		$_SESSION["lang"]
																	);
				}	
			break;


// ******************************************************************************
// *                              Dokumentumok                                  *                             
// ******************************************************************************

	case $linkedTo."Documents".$instanceOf:
				$uploadManagerObj = new UploadManager($db);
				$returnArray["banner"] = "documents-banner";
				$returnArray["content"] = $uploadManagerObj -> getDocumentAttachments($linkedTo,"%","%",$_SESSION["lang"]);
			break;


// ******************************************************************************
// *                           Verseny szabályok                                *
// ******************************************************************************

	case $linkedTo."Rules".$instanceOf:
		$rules = new Rules($db, "rules", "rules_language", "rules_id");
		$uploadManagerObj = new UploadManager($db);
		if ( ($returnArray["content"] = $rules->getBasicTimeStateEditor($linkedTo)) == -1 )
		{ 
			$returnArray['message'] = "failed_database";
			$returnArray["content"] = "";
		}
		else 
		{
			$response = $rules->getBasicTimeStateEditor($linkedTo);
			$returnArray["content"] = $response[$_SESSION["lang"]];
			$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "Of".$menu,"%", $_SESSION["lang"]);
		}
		
		
	break;	

	
// ******************************************************************************
// *                             Kapcsolat menü                                 *
// ******************************************************************************

	case $linkedTo."Contacts".$instanceOf:
		
		$contacts = new Contacts($db, "contacts", "contacts_language", "contacts_id");
		$returnArray["banner"] = "contacts-banner";
		$uploadManagerObj = new UploadManager($db);
		$x=$contacts->getBasicTimeStateEditor($linkedTo);
		if ($contacts->getBasicTimeStateEditor($linkedTo) == -1 )
		{ 
			$returnArray['message'] = "failed_database";
			$returnArray["content"] = "";
		}
		else 
		{
			$response = $contacts->getBasicTimeStateEditor($linkedTo);
			$returnArray["content"] = $response[$_SESSION["lang"]];
			$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "Of".$menu,"%", $_SESSION["lang"]);
		}

	break;


	// ******************************************************************************
	// *                             Támogatók menü                                 *
	// ******************************************************************************

	case $linkedTo."Donation".$instanceOf:
		
		$donation = new Donation($db, "donation", "donation_language", "donation_id");
		//$returnArray["banner"] = "donation-banner";
		$uploadManagerObj = new UploadManager($db);
		$x=$donation->getBasicTimeStateEditor($linkedTo);
		if ($donation->getBasicTimeStateEditor($linkedTo) == -1 )
		{ 
			$returnArray['message'] = "failed_database";
			$returnArray["content"] = "";
		}
		else 
		{
			$response = $donation->getBasicTimeStateEditor($linkedTo);
			$returnArray["content"] = $response[$_SESSION["lang"]];
			$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "Of".$menu,"%", $_SESSION["lang"]);
		}

	break;

// ******************************************************************************
// *                              Rólunk                              
// ******************************************************************************

	case $linkedTo."AboutUs".$instanceOf:
		
		$aboutus = new AboutUs($db, "aboutus", "aboutus_language", "aboutus_id");
		$uploadManagerObj = new UploadManager($db);
		if (($returnArray["aboutus_content"] = $aboutus->getBasicTimeStateEditor($linkedTo)) == -1 )
		{ 
			$returnArray['message'] = "failed_database";
			$returnArray["content"] = "";
		}
		else 
		{
			$response = $aboutus->getBasicTimeStateEditor($linkedTo);
			$returnArray["content"] = $response[$_SESSION["lang"]];
			$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "Of".$menu,"%", $_SESSION["lang"]);
		}
		
	break;

// ******************************************************************************
// *                             Történetünk menü                               *
// ******************************************************************************

	case $linkedTo."Story".$instanceOf:
		
		$story = new Story($db, "story", "story_language", "story_id");
		$uploadManagerObj = new UploadManager($db);
		if (($returnArray["story_content"] = $story->getBasicTimeStateEditor($linkedTo)) == -1 )
		{ 
			$returnArray['message'] = "failed_database";
			$returnArray["content"] = "";
		}
		else
		{
			$response = $story->getBasicTimeStateEditor($linkedTo);
			$returnArray["content"] = $response[$_SESSION["lang"]];
			$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "Of".$menu,"%", $_SESSION["lang"]);
		}
		
	break;


// ******************************************************************************
// *                             Akadályok menü                               *
// ******************************************************************************

	case $linkedTo."CompetitionObstacles".$instanceOf:
		
		
		$obstacles = new CompetitionObstacles($db, "obstacles", "obstacles_language", "obstacles_id");
		$uploadManagerObj = new UploadManager($db);
		if (($returnArray["obstacles_content"] = $obstacles->getBasicTimeStateEditor($linkedTo)) == -1 )
		{ 
			$returnArray['message'] = "failed_database";
			$returnArray["content"] = "";
		}
		else
		{
			$response = $obstacles->getBasicTimeStateEditor($linkedTo);
			$returnArray["content"] = $response[$_SESSION["lang"]];
			$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "Of".$menu,"%", $_SESSION["lang"]);
		}
		
	break;


// ******************************************************************************
// *                             Pályavázlat menü                               *
// ******************************************************************************

	case $linkedTo."CompetitionFieldDescription".$instanceOf:
		
		$field_description = new CompetitionFieldDescription($db, "field_description", "field_description_language", "field_description_id");
		$uploadManagerObj = new UploadManager($db);
		if (($returnArray["field_description_content"] = $field_description->getBasicTimeStateEditor($linkedTo)) == -1 )
		{ 
			$returnArray['message'] = "failed_database";
			$returnArray["content"] = "";
		}
		else
		{
			$response = $field_description->getBasicTimeStateEditor($linkedTo);
			$returnArray["content"] = $response[$_SESSION["lang"]];
			$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "Of".$menu,"%", $_SESSION["lang"]);
		}
		
	break;

	

// ******************************************************************************
// *                             Információk menü                               *
// ******************************************************************************

	case $linkedTo."CompetitionInfo".$instanceOf:
		
		$competition_info = new CompetitionInfo($db, "competition_info", "competition_info_language", "competition_info_id");
		$uploadManagerObj = new UploadManager($db);
		$competition_info_view = $competition_info->getBasicTimeStateEditor($linkedTo);
		if ($competition_info_view == -1 )
		{ 
			$returnArray['message'] = "failed_database";
			$returnArray["content"] = "";
		}
		else 
		{
			$returnArray["content"] = $competition_info_view[$_SESSION["lang"]];
			$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "Of".$menu,"%", $_SESSION["lang"]);
		}
	break;

// ******************************************************************************
// *                             Versenytérkép menü                             *
// ******************************************************************************
	case $linkedTo."CompetitionMap".$instanceOf:
		
		$competition_map = new CompetitionMap($db, "competition_map", "competition_map_language", "competition_map_id");
		$uploadManagerObj = new UploadManager($db);
		$competition_map_view = $competition_map->getBasicTimeStateEditor($linkedTo);
		if ($competition_map_view == -1 )
		{ 
			$returnArray['message'] = "failed_database";
			$returnArray["content"] = "";
		}
		else 
		{
			$returnArray["content"] = $competition_map_view[$_SESSION["lang"]];
			$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "Of".$menu,"%", $_SESSION["lang"]);
		}
	break;

	

// ******************************************************************************
// *                         Megközelíthetőség menü                             *
// ******************************************************************************

	case $linkedTo."CompetitionApproach".$instanceOf:
		
		$competition_approach = new CompetitionApproach($db, "competition_approach", "competition_approach_language", "competition_approach_id");
		$uploadManagerObj = new UploadManager($db);
		$competition_approach_view = $competition_approach->getBasicTimeStateEditor($linkedTo);
		if ($competition_approach_view == -1 )
		{ 
			$returnArray['message'] = "failed_database";
			$returnArray["content"] = "";
		}
		else 
		{
			$returnArray["content"] = $competition_approach_view[$_SESSION["lang"]];
			$returnArray["document"] = $uploadManagerObj -> getDocumentAttachments($linkedTo, "Of".$menu,"%", $_SESSION["lang"]);
		}
	break;

/******************************************************************************
 *                              Gallery				                          *
 ******************************************************************************/
			case $linkedTo."Gallery".$instanceOf:
				$gallery = new Gallery($db);
				$returnArray["content"] = $gallery -> getAlbums($linkedTo,"OfGallery", $_SESSION["lang"]);
			break;

			case $linkedTo."GalleryAlbum".$instanceOf:
				$gallery = new Gallery($db);
				if(isset($_SESSION["albumID"]))
				{
					$_SESSION["albumID"] = isset($_GET["albumID"])?$_GET["albumID"]:$_SESSION["albumID"];
				}
				else
				{
					$_SESSION["albumID"] = $_GET["albumID"];
				}
				$returnArray["content"] = $gallery -> getAlbumPictures($linkedTo, $_SESSION["albumID"], $_SESSION["lang"]);
			break;

// ******************************************************************************
// *                             Nevezés menü                     		        *
// ******************************************************************************
	case $linkedTo."Entry".$instanceOf:
		
		$competition_entry = new CompetitionEntry($db);
		if(isset($_GET["action"]))
		{
			switch($_GET["action"])
			{
				case "reSend":
					$competitionRegID = $competition_entry -> getCompetitionRegID($_SESSION["userID"],$_GET["competition_id"]);
					if(isset($_SESSION["userID"]) && $competitionRegID)
					{
					$returnArray["message"] = $competition_entry -> sendMailRegistration($competitionRegID,$_GET["competition_id"],0,$_SESSION["lang"] );
					}
				break;
				
				case "addEntry":
					if ($user->checkEmail($_GET["email"] == "true"))
					{
						$returnArray["message"] = $competition_entry -> addEntry(	
																					$_GET["competitionID"],
																					$_GET["lastname"],
																					$_GET["firstname"],
																					$_GET["email"],
																					$_GET["phone"],
																					$_GET["country"],
																					$_GET["mailing-zip"],
																					$_GET["mailing-city"],
																					$_GET["mailing-address"],
																					isset($_GET["pid"])?$_GET["pid"]:"",
																					$_GET["mother_name"],
																					$_GET["born_date"],
																					$_GET["sex"],
																					$_GET["er_name"],
																					$_GET["er_phone"],
																					$_GET["t_shirt"],
																					$_GET["comp_dist"],
																					isset($_GET["guest_data"])?$_GET["guest_data"]:"",
																					$linkedTo,
																					$_SESSION["lang"]
																				);
					}
					else $returnArray["message"] = "exist_email";
				break;
				case "checkTeamateValidity":
					$reg_process = $competition_entry->checkTeamateValidity($_GET["teamates"][0],$_GET["competition_id"]);
					echo $result = ($reg_process == 3 ? "true" : "false");
					
					return;

				break;

				case "checkTeamNameExists":

					echo $competition_entry->checkTeamNameExists($_GET["team_name"],$_GET["competition_id"]);
					return;

				break;

				case "addTeam":
					if($competitionID = $competition_entry->registrationIsActive($linkedTo))
					{
						$competitionRegID = $competition_entry -> getCompetitionRegID($_SESSION["userID"],$_GET["competitionID"]);
						$returnArray["message"] = $competition_entry->addTeam(	
																				$competitionID,
																				$_GET["team_name"],
																				$_GET["teamates"],
																				$competitionRegID,
																				$linkedTo, 
																				$_SESSION["lang"]
																		   );
					}
				break;



				case "confirmAddTeamate":
														 
					$returnArray["message"] = $competition_entry->addTeamateToTeam($_GET["comp_reg_id"], $_GET["competitionID"], $_GET["teamID"], $linkedTo, $_SESSION["lang"]);

				break;

				case "deleteTeam":
														 
					$returnArray["message"] = $competition_entry->deleteTeam($_GET["teamID"]);

				break;

			}
		}

		if($competitionID = $competition_entry->registrationIsActive($linkedTo))
		{
			if($competition_entry->registrationIsFull($competitionID))
			{
				$returnArray["content"] = $competition_entry->registrationIsFullText($_SESSION["lang"]);
			}
			else
			{
				$returnArray["content"] = $competition_entry->getRegistrationTimeLeft($competitionID,$linkedTo,$_SESSION["lang"]);

				if($reg_process = $competition_entry->userIsRegistered(isset($_SESSION["userID"])?$_SESSION["userID"]:"",$competitionID))
				{
					switch ($reg_process)
					{
						case "1":
							$returnArray["content"] = $competition_entry->userRegisterProcessText($_SESSION["lang"],$competitionID);
						break;
						case "2":
							$returnArray["content"] = $competition_entry->userRegisterAdminProcessText($_SESSION["lang"]);
						break;
						case "3":
							$returnArray["content"] = $competition_entry->userIsRegisteredText($_SESSION["lang"]);
							include("add_team_form_".$_SESSION["lang"]."_inc.php");
							$returnArray["content"] .= $html; 
						break;
					}
					
				}
				else
				{
					$returnArray["content"] .= $user->getUserRegisterForm($_SESSION["lang"],isset($user->userID)?$user->getUserData($user->userID):"",1, $competition_entry->getCompetitionType($competitionID), $competitionID);
				}
			}
		}
		else
		{
			$returnArray["content"] = $competition_entry->registrationInActiveText($_SESSION["lang"]);
		}
	break;

// ******************************************************************************
// *                             Támogatók menü                                 *
// ******************************************************************************

	case $linkedTo."Colleague".$instanceOf:
	break;

	case $linkedTo."ColleagueAdd".$instanceOf:
		$colleague = new Colleague($db);
		if(isset($_GET["action"]))
		{
			switch($_GET["action"])
			{
				case "addColleague":
					$returnArray["message"] = $colleague -> addColleague(	
																	$_GET["lastname"],
																	$_GET["firstname"],
																	$_GET["phone"],
																	$_GET["email"],
																	$_GET["scope"],
																	isset($_GET["photo_id"])?$_GET["photo_id"]:0
																);
					$colleague->colleagueID = 0;
				break;
			}
		}
		$returnArray["content"] = $colleague->getColleagueForm("Új munkatárs","Mentés", $uploadRootFolderID, $linkedTo);
		
	break;

	case $linkedTo."ColleagueEdit".$instanceOf:
		$colleague = new Colleague($db);
		$colleague->colleagueID = isset($_GET["colleagueID"])?$_GET["colleagueID"]:0;
		
		if(isset($_GET["action"]))
		{
			switch($_GET["action"])
			{
				case "checkColleagueExists":
					echo $colleague->checkColleaguename($_GET["colleaguename"],1);
					return;
				break;
				case "editColleague":
					$returnArray["message"] = $colleague->updateColleague(
																	$_GET["lastname"],
																	$_GET["firstname"],
																	$_GET["phone"],
																	$_GET["email"],
																	$_GET["scope"],
																	isset($_GET["photo_id"])?$_GET["photo_id"]:0
														);
					$colleague->colleagueID = 0;
				break;
			}
		}

		$returnArray["content"] = $colleague->getColleagueForm("Munkatársak adatainak szerkesztése","Mentés",$uploadRootFolderID, $linkedTo, 1, 1, 1);
	break;

	case $linkedTo."ColleagueDelete".$instanceOf:
		$colleague = new Colleague($db);
		$colleague->colleagueID = isset($_GET["colleagueID"])?$_GET["colleagueID"]:0;
		if(isset($_GET["delete"]))
		{
			$returnArray["message"] = $colleague->deleteColleague();
		}
		$returnArray["content"] = $colleague->getColleagueForm("Munkatárs törlése","Törlés", $uploadRootFolderID, $linkedTo, 0, 1, 1);
	break;

	default:			
		
	break;
}


if(isset($returnArray["content"]) && $returnArray["content"] == -1)
{
$returnArray["content"] = "";
$returnArray["message"] = "failed_database";
}
echo json_encode($returnArray, JSON_FORCE_OBJECT);
unset($db);

?>