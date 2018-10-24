<?php
session_start();

include '../../php/phpmailer/src/PHPMailer.php';
include '../../php/phpmailer/src/Exception.php';
include '../../php/phpmailer/src/SMTP.php';

require_once "config.php";
require_once "connect.php";
require_once "news.php";
require_once "users.php";
require_once "uploadmanager.php";
require_once "colleague.php";
require_once "basic_timestate_editor.php";
require_once "aboutus.php";
require_once "rules.php";
require_once "contacts.php";
require_once "story.php";
require_once "competition_obstacles.php";
require_once "competition_field_description.php";
require_once "competition_info.php";
require_once "competition.php";
require_once "competition_map.php";
require_once "competition_approach.php";
require_once "competition_entry.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = Database::getConnection();
$user = new User($db);

if(isset($_SESSION["logedin"]) && $_SESSION["logedin"] == 1)
{
	
	if (isset($_POST) && !empty($_POST)) 
		$menu = isset($_POST["menu"])?$_POST["menu"]:"";
	elseif (isset($_GET) && !empty($_GET))
		$menu = isset($_GET["menu"])?$_GET["menu"]:"";
	
	//echo "menu: ".$menu;
	if (isset($_POST) && !empty($_POST)) 
		$linkedTo = isset($_POST["linked_to"])?$_POST["linked_to"]:"";
	elseif (isset($_GET) && !empty($_GET))
		$linkedTo = isset($_GET["linked_to"])?$_GET["linked_to"]:"";

	if (isset($_POST) && !empty($_POST)) 
		$instanceOf = isset($_POST["instanceOf"])?$_POST["instanceOf"]:"";
	elseif (isset($_GET) && !empty($_GET))
		$instanceOf = isset($_GET["instanceOf"])?$_GET["instanceOf"]:"";
	
	$user ->userID = isset($_SESSION["userID"])?$_SESSION["userID"]:0;
	if ($menu == "login")
	{
		$returnArray["content"] = $user->getUserMenu();
		$returnArray["logout"] = $user->getLogoutButton();	
		$returnArray["message"] = ($returnArray["content"] == -1 || $returnArray["logout"] == -1)?
		"failed_database":"success_logedin";
	}
	else if ($menu == "logout")
	{
		$returnArray["message"] = $user->logoutUser();
	}
	else
	if ($user -> check_authority($menu) || $menu == 'Home')
	{
		$returnArray["languages"] = $user->LANGUAGE;

		if(!$user -> createFolders($linkedTo)) return $returnArray["message"] = "failed_create_start_folder";
		
		$uploadRootFolderID = (isset($_POST["upload_root_folder"]) && $_POST["upload_root_folder"])?
		$_POST["upload_root_folder"]:$_SESSION["uploadRootFolderID"];


		$_SESSION["menu"] = $menu;
		$_SESSION["linkedTo"] = $linkedTo;
		$_SESSION["uploadRootFolderID"] = $uploadRootFolderID;
		$_SESSION["instanceOf"] = $instanceOf;
		
		switch ($menu)
		{	
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
				
				if(isset($_POST["action"]))
				{
					switch($_POST["action"])
					{
						case "checkUserExists":
							echo $user->checkUsername($_POST["username"]);
							return;
						break;
						case "addUser":
							$returnArray["message"] = $user -> addUser(	
																			$_POST["lastname"],
																			$_POST["firstname"],
																			$_POST["phone"],
																			$_POST["username"],
																			$_POST["password"],
																			$_POST["confirm_password"]
																		);
							$user->userID = 0;
						break;
					}
				}
				
				$returnArray["content"] = $user->getUserForm("Új felhasználó","Mentés");
				
			break;

			case $linkedTo."UsersEdit".$instanceOf:
				$user->userID = isset($_POST["userID"])?$_POST["userID"]:0;
				
				if(isset($_POST["action"]))
				{
					switch($_POST["action"])
					{
						case "checkUserExists":
							echo $user->checkUsername($_POST["username"],1);
							return;
						break;
						case "editUser":
							$returnArray["message"] = $user->updateUser(
																			$_POST["lastname"],
																			$_POST["firstname"],
																			$_POST["phone"],
																			$_POST["username"],
																			$_POST["password"],
																			$_POST["confirm_password"]
																);
							$user->userID = 0;
						break;
					}
				}

				$returnArray["content"] = $user->getUserForm("Felhasználók szerkesztése","Mentés", 1, 1, 1);
			break;

			case $linkedTo."UsersDelete".$instanceOf:
				$user->userID = isset($_POST["userID"])?$_POST["userID"]:0;
				if(isset($_POST["delete"]))
				{
					$returnArray["message"] = $user->deleteUser();
				}
				$returnArray["content"] = $user->getUserForm("Felhasználó törlése","Törlés", 0, 1, 1);
			break;

			case $linkedTo."UsersAuthority".$instanceOf:
				$user->userID = isset($_POST["userID"])?$_POST["userID"]:0;
				if(isset($_POST["authority"]))
				{
					$returnArray["message"] = $user->saveUserAuthority(
																		$_POST["menu_name"],
																		$_POST["menuID"], 
																		$_POST["authority"]
																		);
					$returnArray["ontop"] = 0;
				}
				$returnArray["content"] = $user->getAuthorityUserForm();
			break;

	// ******************************************************************************
	// *                              Versenykezelés                             *
	// ******************************************************************************
			case $linkedTo."Entry".$instanceOf:
				$competition = new Competition($db);
				$competition_entry = new CompetitionEntry($db);

				$competition -> competitionID = isset($_POST["competitionID"])?$_POST["competitionID"]:0;
				$returnArray["content"] = $competition -> getCompetitionComboBox($linkedTo);
			
				if (isset($_POST["action"]))
				{
					switch ($_POST["action"]) {
						case 'accept':
							if(isset($_POST["accepted"]))
							{
									$returnArray["message"] = $competition_entry -> acceptEntry($_POST["accepted"]);
							}
							break;
						case 'delete':
							if(isset($_POST["competitionRegID"]))
							{
									$returnArray["message"] = $competition_entry ->  deleteEntry($_POST["competitionRegID"]);
							}
							break;
						case 'remove':
							if(isset($_POST["competitionRegID"]))
							{
									$returnArray["message"] = $competition_entry ->  removeEntry($_POST["competitionRegID"]);
							}
							break;
						case 'send_conf_competition_email':	
							if(isset($_POST["competitionRegID"]))
							{
									
									$returnArray["message"] = $competition_entry ->  sendMailConfirmRegistration($_POST["competitionRegID"], 
																												 $_POST["competitionID"],
																												 1,
																												 $_POST["lang"]);
							}
							break;
					}
				}
				
			break;
			case $linkedTo."CompetitionCall".$instanceOf:
				$returnArray["content"] = "Versenyek kiírás oldal";
			break;
			
			case $linkedTo."CompetitionHandler".$instanceOf:
				$returnArray["content"] = "Versenyek kezelése oldal";
			break;

			case $linkedTo."AddCompetition".$instanceOf:
				$competition = new Competition($db);
				if(isset($_POST["method"]) && $_POST["method"] = "save")
				{
					$returnArray["message"] = $competition -> addCompetition(
																				$_POST["start_date"],
																				$_POST["start_hour"],
																				$_POST["start_minute"],
																				$_POST["reg_start_date"],
																				$_POST["reg_end_date"],
																				$_POST["max_reg_number"],
																				$_POST["reg_type"],
																				$_POST["teamate_number"],
																				isset($_POST["comp_dist_1"])?$_POST["comp_dist_1"]:0,
																				isset($_POST["comp_dist_2"])?$_POST["comp_dist_2"]:0,
																				isset($_POST["comp_dist_3"])?$_POST["comp_dist_3"]:0,
																				$linkedTo
																			);
				}
				$returnArray["content"] = $competition -> getCompetitionForm("Új verseny létrehozása", $linkedTo);	
			break;	

			case $linkedTo."ModifyCompetition".$instanceOf:
				$competition = new Competition($db);
				if(isset($_POST["method"]) && $_POST["method"] = "save" && isset($_POST["competitionID"]) && $_POST["competitionID"]!=0)
				{
					$competition -> competitionID = $_POST["competitionID"];
					$returnArray["message"] = $competition -> updateCompetition(
																				$_POST["start_date"],
																				$_POST["start_hour"],
																				$_POST["start_minute"],
																				$_POST["reg_start_date"],
																				$_POST["reg_end_date"],
																				$_POST["max_reg_number"],
																				$_POST["reg_type"],
																				$_POST["teamate_number"],
																				isset($_POST["comp_dist_1"])?$_POST["comp_dist_1"]:0,
																				isset($_POST["comp_dist_2"])?$_POST["comp_dist_2"]:0,
																				isset($_POST["comp_dist_3"])?$_POST["comp_dist_3"]:0
																			);
				}
				else if(isset($_POST["competitionID"]) && $_POST["competitionID"]!=0 )
				{
					$competition -> competitionID = $_POST["competitionID"];
					$returnArray["content"] = $competition -> getCompetitionForm("Verseny módosítása", $linkedTo, 1, 1, 1);	
				}
				else $returnArray["content"] = $competition -> getCompetitionForm("Verseny módosítása", $linkedTo, 0, 1, 1);	
			break;	

			case $linkedTo."DeleteCompetition".$instanceOf:
				$competition = new Competition($db);

				if(isset($_POST["method"]) && $_POST["method"] = "delete" && isset($_POST["competitionID"]) && $_POST["competitionID"]!=0)
				{
					$competition -> competitionID = $_POST["competitionID"];
					$returnArray["message"] = $competition -> deleteCompetition();
				}
				else if(isset($_POST["competitionID"]) && $_POST["competitionID"]!=0 )
				{
					$competition -> competitionID = $_POST["competitionID"];			
				}
				$returnArray["content"] = $competition -> getCompetitionForm("Verseny törlése", $linkedTo, 0, 1, 1);
			break;	


	// ******************************************************************************
	// *                             Versenyinformációk menü                        *
	// ******************************************************************************

			case $linkedTo."CompetitionInfoWrite".$instanceOf:
			case $linkedTo."CompetitionInfo".$instanceOf:
				$competition = new Competition($db);			
				$information = new CompetitionInfo($db, "competition_info", "competition_info_language", "competition_info_id");
				$competition -> competitionID = $information->getCompetitionID($linkedTo);
				$returnArray["competition_combobox"] = $competition -> getCompetitionComboBox($linkedTo);
				if (($returnArray["information_combobox"] = $information->getBasicTimeStateEditorComboBox($linkedTo)) == -1 || 
					($returnArray["information_content"] = $information->getBasicTimeStateEditor($linkedTo)) == -1 )
				{ 
					$returnArray['message'] = "failed_database";
					$returnArray["information_combobox"] = $returnArray["information_content"] = "";
				}
				
			break;

			case $linkedTo."CompetitionInfoSaveBtn".$instanceOf:
				$competition = new Competition($db);
				$information = new CompetitionInfo($db, "competition_info", "competition_info_language", "competition_info_id");
				$uploadManagerObj = new UploadManager($db);

				if (isset($_POST["action"]) && $_POST["action"] == "set_other" && $_POST["information_id"]!="-1")
				{
					if (($returnArray["information_combobox"] = $information->setBasicTimeStateEditor($linkedTo, $_POST["information_id"], $_POST["competitionID"])) != -1 &&
						($returnArray["information_content"] = $information->getBasicTimeStateEditor($linkedTo, $_POST["information_id"] )) != -1 )
					{
						$returnArray["message"] = "success_other_save";
					}
					else $returnArray["message"] = "failed_database"; 

				}
				else
				
					if (isset($_POST["file_id"]))
					{
						if ($uploadManagerObj -> updateDocumentAttachment( isset($_POST["file_id"])?$_POST["file_id"]:"",
																$linkedTo,
																$_POST["document_instance_of"]) != -1 )
							{
								$returnArray["message"] = "success_save";
							}
							else $returnArray["message"] = "failed_database";
					}
					else{

						if (($returnArray["information_combobox"] = $information->addBasicTimeStateEditor($_POST["information_content"], $linkedTo, $_POST["competitionID"])) != -1)
						{		
							$uploadManagerObj -> deleteDocumentAttachment( $_POST["document_linked_to"], $_POST["document_instance_of"]);
							$returnArray["information_content"] = $information->getBasicTimeStateEditor($linkedTo);
							$returnArray["message"] = "success_save";							
		                   
						} 
						else 	$returnArray["message"] = "failed_database";
					}
				$competition -> competitionID = $information->getCompetitionID($linkedTo);
				$returnArray["competition_combobox"] = $competition -> getCompetitionComboBox($linkedTo);
							
			break;

            case $linkedTo."CompetitionInfoDelete".$instanceOf:

				$information = new CompetitionInfo($db, "competition_info", "competition_info_language", "competition_info_id");
				if (isset($_POST["information_id"]) && $_POST["information_id"]!="-1")
				{
					if 	($information->deleteBasicTimeStateEditor($_POST["information_id"]) != -1 && 
						($returnArray["information_combobox"] = $information->getBasicTimeStateEditorComboBox($linkedTo)) != -1 &&
						($returnArray["information_content"] = $information->getBasicTimeStateEditor($linkedTo)) != -1 )
					{
						$returnArray["message"] = "success_delete";
					}
					else $returnArray["message"] = "failed_database"; 
				}
			break;

	// ******************************************************************************
	// *                             Versenytérkép menü                             *
	// ******************************************************************************

			case $linkedTo."CompetitionMapWrite".$instanceOf:
			case $linkedTo."CompetitionMap".$instanceOf:
				$competition = new Competition($db);
				$competition_map = new CompetitionMap($db, "competition_map", "competition_map_language", "competition_map_id");
				$competition -> competitionID = $competition_map->getCompetitionID($linkedTo);
				$returnArray["competition_combobox"] = $competition -> getCompetitionComboBox($linkedTo);
				if (($returnArray["competition_map_combobox"] = $competition_map->getBasicTimeStateEditorComboBox($linkedTo)) == -1 || 
					($returnArray["competition_map_content"] = $competition_map->getBasicTimeStateEditor($linkedTo)) == -1 )
				{ 
					$returnArray['message'] = "failed_database";
					$returnArray["competition_map_combobox"] = $returnArray["competition_map_content"] = "";
				}
				
			break;

			case $linkedTo."CompetitionMapSaveBtn".$instanceOf:

				$competition_map = new CompetitionMap($db, "competition_map", "competition_map_language", "competition_map_id");
				$competition = new Competition($db);
				
				$uploadManagerObj = new UploadManager($db);

				if (isset($_POST["action"]) && $_POST["action"] == "set_other" && $_POST["competition_map_id"]!="-1")
				{
					if (($returnArray["competition_map_combobox"] = $competition_map->setBasicTimeStateEditor($linkedTo, $_POST["competition_map_id"], $_POST["competitionID"])) != -1 &&
						($returnArray["competition_map_content"] = $competition_map->getBasicTimeStateEditor($linkedTo, $_POST["competition_map_id"] )) != -1 )
					{
						$returnArray["message"] = "success_other_save";
					}
					else $returnArray["message"] = "failed_database"; 

				}
				else
				
					if (isset($_POST["file_id"]))
					{
						if ($uploadManagerObj -> updateDocumentAttachment( isset($_POST["file_id"])?$_POST["file_id"]:"",
																	$linkedTo,
																	$_POST["document_instance_of"]) != -1 )
						{
							$returnArray["message"] = "success_save";
						}
						else $returnArray["message"] = "failed_database";
					}
					else
					{				

						if (($returnArray["competition_map_combobox"] = $competition_map->addBasicTimeStateEditor($_POST["competition_map_content"], $linkedTo, $_POST["competitionID"])) != -1)
						{		
							$uploadManagerObj -> deleteDocumentAttachment( $_POST["document_linked_to"], $_POST["document_instance_of"]);
							$returnArray["competition_map_content"] = $competition_map->getBasicTimeStateEditor($linkedTo);
							$returnArray["message"] = "success_save";							
		                   
						} 
						else 	$returnArray["message"] = "failed_database";
					}

				$competition -> competitionID = $competition_map->getCompetitionID($linkedTo);
				$returnArray["competition_combobox"] = $competition -> getCompetitionComboBox($linkedTo);
							
			break;

            case $linkedTo."CompetitionMapDelete".$instanceOf:

				$competition_map = new CompetitionMap($db, "competition_map", "competition_map_language", "competition_map_id");
				if (isset($_POST["competition_map_id"]) && $_POST["competition_map_id"]!="-1")
				{
					if 	($competition_map->deleteBasicTimeStateEditor($_POST["competition_map_id"]) != -1 && 
						($returnArray["competition_map_combobox"] = $competition_map->getBasicTimeStateEditorComboBox($linkedTo)) != -1 &&
						($returnArray["competition_map_content"] = $competition_map->getBasicTimeStateEditor($linkedTo)) != -1 )
					{
						$returnArray["message"] = "success_delete";
					}
					else $returnArray["message"] = "failed_database"; 
				}
			break;

	// ******************************************************************************
	// *                         Megközelíthetőség menü                             *
	// ******************************************************************************

			case $linkedTo."CompetitionApproachWrite".$instanceOf:
			case $linkedTo."CompetitionApproach".$instanceOf:
				$competition = new Competition($db);
				$competition_approach = new CompetitionApproach($db, "competition_approach", "competition_approach_language", "competition_approach_id");
				$competition -> competitionID = $competition_approach->getCompetitionID($linkedTo);
				$returnArray["competition_combobox"] = $competition -> getCompetitionComboBox($linkedTo);
				if (($returnArray["competition_approach_combobox"] = $competition_approach->getBasicTimeStateEditorComboBox($linkedTo)) == -1 || 
					($returnArray["competition_approach_content"] = $competition_approach->getBasicTimeStateEditor($linkedTo)) == -1 )
				{ 
					$returnArray['message'] = "failed_database";
					$returnArray["competition_approach_combobox"] = $returnArray["competition_approach_content"] = "";
				}
				
			break;

			case $linkedTo."CompetitionApproachSaveBtn".$instanceOf:
				$competition = new Competition($db);
				$competition_approach = new CompetitionApproach($db, "competition_approach", "competition_approach_language", "competition_approach_id");
				$uploadManagerObj = new UploadManager($db);

				if (isset($_POST["action"]) && $_POST["action"] == "set_other" && $_POST["competition_approach_id"]!="-1")
				{
					if (($returnArray["competition_approach_combobox"] = $competition_approach->setBasicTimeStateEditor($linkedTo, $_POST["competition_approach_id"], $_POST["competitionID"])) != -1 &&
						($returnArray["competition_approach_content"] = $competition_approach->getBasicTimeStateEditor($linkedTo, $_POST["competition_approach_id"] )) != -1 )
					{
						$returnArray["message"] = "success_other_save";
					}
					else $returnArray["message"] = "failed_database"; 

				}
				else
					if (isset($_POST["file_id"]))
					{
					
						if ($uploadManagerObj -> updateDocumentAttachment( isset($_POST["file_id"])?$_POST["file_id"]:"",
																$linkedTo,
																$_POST["document_instance_of"]) != -1 )
							{
								$returnArray["message"] = "success_save";
							}
							else $returnArray["message"] = "failed_database";
					}
					else
					{
				
						if (($returnArray["competition_approach_combobox"] = $competition_approach->addBasicTimeStateEditor($_POST["competition_approach_content"], $linkedTo, $_POST["competitionID"])) != -1)
						{		
							$uploadManagerObj -> deleteDocumentAttachment( $_POST["document_linked_to"], $_POST["document_instance_of"]);
							$returnArray["competition_approach_content"] = $competition_approach->getBasicTimeStateEditor($linkedTo);
							$returnArray["message"] = "success_save";							
		                   
						} 
						else 	$returnArray["message"] = "failed_database";
					}
					$competition -> competitionID = $competition_approach->getCompetitionID($linkedTo);
					$returnArray["competition_combobox"] = $competition -> getCompetitionComboBox($linkedTo);		
			break;

            case $linkedTo."CompetitionApproachDelete".$instanceOf:

				$competition_approach = new CompetitionApproach($db, "competition_approach", "competition_approach_language", "competition_approach_id");
				if (isset($_POST["competition_approach_id"]) && $_POST["competition_approach_id"]!="-1")
				{
					if 	($competition_approach->deleteBasicTimeStateEditor($_POST["competition_approach_id"]) != -1 && 
						($returnArray["competition_approach_combobox"] = $competition_approach->getBasicTimeStateEditorComboBox($linkedTo)) != -1 &&
						($returnArray["competition_approach_content"] = $competition_approach->getBasicTimeStateEditor($linkedTo)) != -1 )
					{
						$returnArray["message"] = "success_delete";
					}
					else $returnArray["message"] = "failed_database"; 
				}
			break;
	// ******************************************************************************
	// *                              Hírek kezelés                              	*
	// ******************************************************************************

			case $linkedTo."News".$instanceOf:
				$returnArray["content"] = "Hírek kezdőoldal";	
			break;

			case $linkedTo."NewsDeleteSearch".$instanceOf:
			case $linkedTo."NewsSearch".$instanceOf:
				$news = new News($db);
				if(isset($_POST["action"]) && $_POST["action"] == "showNewsResult")
				{

					$returnArray["content"] = $news->getNewsSearchResult(	
																		htmlentities($_POST["title"]), 
																		htmlentities($_POST["author"]), 
																		$_POST["start_date"], 
																		$_POST["end_date"], 
																		isset($_POST["visiblity"])?1:0, 
																		htmlentities($_POST["keyword"]), 
																		$linkedTo
																	);
					if(!$returnArray["content"])
					{
						$returnArray["message"] = "empty_news_search";
					}
					$returnArray["ontop"] = 1;
				}
				else $returnArray["content"] = $news->getNewsSearchForm($_POST["linked_to"]);	
				
			break;

			case $linkedTo."NewsModifySave".$instanceOf:
			case $linkedTo."NewsSave".$instanceOf:
				$news = new News($db);
				$uploadManagerObj = new UploadManager($db);

				if(!$_POST["addNews"])
				{
					if ( $news->updateNews(																									$_POST["newsid"],
										$_POST["title"], 
										$_POST["author"], 
										$_POST["start_date"], 
										$_POST["end_date"], 
										isset($_POST["visiblity"])?$_POST["visiblity"]:"",
										isset($_POST["main_visiblity"])?$_POST["main_visiblity"]:"", 
										$_POST["content"], 
										$linkedTo
									) != -1)
					{
						
						if ($uploadManagerObj -> updateDocumentAttachment(	isset($_POST["file_id"])?$_POST["file_id"]:"",
																			$_POST["document_linked_to"],
																			$_POST["document_instance_of"],
																			$_POST["newsid"] ) != -1 )
						{
							$returnArray["message"] = "success_news_modify";
						}
						else $returnArray["message"] = "failed_database"; 
						
					}
					else $returnArray["message"] = "failed_database";						
				}
				else
				{

					$newsID = $news->addNews(																									$_POST["title"], 
											$_POST["author"], 
											$_POST["start_date"], 
											$_POST["end_date"], 
											isset($_POST["visiblity"])?$_POST["visiblity"]:"",
											isset($_POST["main_visiblity"])?$_POST["main_visiblity"]:"", 
											$_POST["content"], 
											$linkedTo
										);

					if($newsID != -1 && $uploadManagerObj -> addDocumentAttachment(	$_POST["file_id"],
																					$_POST["document_linked_to"],
																					$_POST["document_instance_of"],
																					$newsID ) != -1 )
					{
						$returnArray["message"] = "success_news_add";
					}
					else $returnArray["message"] = "failed_database";
				}
							
			break;

			case $linkedTo.'NewsDeleteItem'.$instanceOf:
				$news = new News($db);
				$uploadManagerObj = new UploadManager($db);
				if(isset($_POST["newsID"]))
				{
					if( $news->deleteNews($_POST["newsID"]) != -1 && 
						$uploadManagerObj -> deleteDocumentAttachment(	$_POST["document_linked_to"],
																		$_POST["document_instance_of"],
																		$_POST["newsID"]) != -1)
					{
						$returnArray["message"] = "success_news_delete";
					}
					else $returnArray["message"] = "failed_database";
				}
				else $returnArray["message"] = "empty_news_assign";

			break;

			case $linkedTo."NewsDeleteContent".$instanceOf:
			case $linkedTo."NewsModify".$instanceOf:
				$news = new News($db);
				if(isset($_POST["newsID"])) 
				{
					$returnArray["news_content"] = $news->getNews($_POST["newsID"]);
				}
			break;

			case $linkedTo."NewsWrite".$instanceOf:
			break;

			case $linkedTo."NewsAdd".$instanceOf:
			case $linkedTo."NewsDeleteMeta".$instanceOf:
			case $linkedTo."NewsModifyMeta".$instanceOf: 	
			case $linkedTo."NewsMeta".$instanceOf:
				if(isset($_POST["action"]) && $_POST["action"] == "create")
				{
					$news = new News($db);
					$returnArray["content"] = $news->getNewsMetaDataForm(	isset($_POST["newsid"])?
																			$_POST["newsid"]:"", 
																			$linkedTo);
					if($menu != $linkedTo."NewsAdd".$instanceOf) $returnArray["append"] = 1;
				}

			break;

			case $linkedTo."NewsEdit".$instanceOf:	
			break;

	// ******************************************************************************
	// *                              Dokumentum csatolás                           *                             
	// ******************************************************************************


			case $linkedTo."DocumentEditAttachment".$instanceOf:
			case $linkedTo."DocumentAttachment".$instanceOf:
				if(isset($_POST["action"]) && $_POST["action"] == "create")
				{
					$uploadManagerObj = new UploadManager($db);
					$returnArray = $uploadManagerObj -> getDocumentAttachmentForm($linkedTo, $instanceOf, $_POST["instance_id"]);
					$returnArray["append"] = 1;
				}
			break;

	// ******************************************************************************
	// *                              Mappakezelés                                  *
	// ******************************************************************************

			case $linkedTo."UploadManager".$instanceOf:
				$returnArray['content'] = "Kezdő oldal";
			break;

			case $linkedTo."FolderDeleteBtn".$instanceOf:	
				$uploadManagerObj = new UploadManager($db);
				$returnArray['message'] = $uploadManagerObj -> delFolder($_POST['folders'], $linkedTo, $uploadRootFolderID);

			case $linkedTo."FolderDeleteForm".$instanceOf:
				if (!isset($uploadManagerObj)) $uploadManagerObj = new UploadManager($db);
				$returnArray['content'] = $uploadManagerObj -> getFolders(1, 1, $linkedTo, $uploadRootFolderID, $instanceOf);
				if(!$returnArray['content'])
				{
					$returnArray['message'] = "empty_uploadmanager_folders";
				}
			break;

			
			case $linkedTo."FolderModifyForm".$instanceOf:
				$uploadManagerObj = new UploadManager($db);
				if(isset($_POST["action"]))
				{
					switch($_POST["action"])
					{
						case "modifyFolderForm":
							$returnArray['content'] = $uploadManagerObj -> getFolderForm($uploadRootFolderID, $_POST["id"]);
						break;
						case "modifySaveFolder":
							$returnArray["message"] = $uploadManagerObj -> modifyFolderData($_POST["folder_id"],$_POST["folder_name"],$linkedTo,$_POST["visiblity"],$uploadRootFolderID);
							$returnArray['content'] = $uploadManagerObj -> getFolders(1, 0, $linkedTo, $uploadRootFolderID, $instanceOf);
							
						break;
					}
				}
				else
				{
					$returnArray['content'] = $uploadManagerObj -> getFolders(1, 0, $linkedTo, $uploadRootFolderID, $instanceOf);
					if (!$returnArray["content"]) 
					{ 
						$returnArray["message"] = "empty_uploadmanager_folders";
						$returnArray["content"] = "";
					}
				}
			break;

			case $linkedTo."FolderAdd".$instanceOf:
				$uploadManagerObj = new UploadManager($db);

			 	if ( isset($_POST["action"]) && $_POST["action"] == "addFolder" )
			 	{
		 			$returnArray["message"] = $uploadManagerObj -> addFolder(	$user ->userID, 
																				$_POST["folder_name"], 
																				isset($_POST["visiblity"])?
																				$_POST["visiblity"]:"", 
																				$linkedTo,
																				$uploadRootFolderID,
																				$instanceOf);
			 	}
			 	else 
			 	{
			 		$returnArray["content"] = $uploadManagerObj -> getFolderForm($uploadRootFolderID);
			 	}
			break;

	// ******************************************************************************
	// *                             Fájlkezelés                                    *
	// ******************************************************************************

			case $linkedTo.'FileModifyForm'.$instanceOf:
			case $linkedTo.'FileUploadForm'.$instanceOf:
				$uploadManagerObj = new UploadManager($db);

				if(isset($_POST["action"]))
				{
					switch ($_POST["action"]) {
						case 'subtitle-modify-form':
							$returnArray["content"] = $uploadManagerObj -> getFileSubtitleForm($_POST["folderId"],$_POST["fileId"]);
						break;

						case 'subtitle-modify':
							$returnArray["message"] = $uploadManagerObj -> setFileSubtitle($_POST["file_subtitle"],$_POST["visiblity"], $_POST["file_id"]);
						case 'upload-form-dir':
							$returnArray = $uploadManagerObj -> getUploadFileForm(	$_POST["folderId"], 
																					0, 
																					$linkedTo, 
																					$uploadRootFolderID,
																					$instanceOf);
						break;

					}
				}
				else
				{
					$returnArray = $uploadManagerObj -> getUploadFileForm(-1, 0, $linkedTo, $uploadRootFolderID, $instanceOf);
				}
			break;

			case $linkedTo.'FileUploadBtn'.$instanceOf:
				$uploadManagerObj = new UploadManager($db);	
				$returnArray["message"] = $uploadManagerObj -> addFiles($_FILES["file"], 
																		$_GET["folderId"],
																		$_SESSION["userID"], 
																		$linkedTo, 
																		$uploadRootFolderID,
																		1);
			break;

			case $linkedTo."FileDeleteBtn".$instanceOf:	
				$uploadManagerObj = new UploadManager($db);
				$returnArray['message'] = $uploadManagerObj -> delFile($_POST['files'], $linkedTo, $uploadRootFolderID);

			case $linkedTo."FileDeleteForm".$instanceOf:
				if (!isset($uploadManagerObj)) $uploadManagerObj = new UploadManager($db);
				if (isset($_POST["folderId"]) ) $returnArray = $uploadManagerObj -> getUploadFileForm($_POST["folderId"], 1, $linkedTo, $uploadRootFolderID, $instanceOf);
				else $returnArray = $uploadManagerObj -> getUploadFileForm(-1, 1, $linkedTo, $uploadRootFolderID, $instanceOf);
			break;

	// ******************************************************************************
	// *                              Rólunk                                        *
	// ******************************************************************************

			case $linkedTo."AboutUsWrite".$instanceOf:
			case $linkedTo."AboutUs".$instanceOf:
				$aboutus = new AboutUs($db, "aboutus", "aboutus_language", "aboutus_id");
				if (($returnArray["aboutus_combobox"] = $aboutus->getBasicTimeStateEditorComboBox($linkedTo)) == -1 || 
					($returnArray["aboutus_content"] = $aboutus->getBasicTimeStateEditor($linkedTo)) == -1 )
				{ 
					$returnArray['message'] = "failed_database";
					$returnArray["aboutus_combobox"] = $returnArray["aboutus_content"] = "";
				}
				
			break;	
			case $linkedTo."AboutUsSaveBtn".$instanceOf:
				$aboutus = new AboutUs($db, "aboutus", "aboutus_language", "aboutus_id");
				$uploadManagerObj = new UploadManager($db);

				if (isset($_POST["action"]) && $_POST["action"] == "set_other" && $_POST["aboutus_id"]!="-1")
				{
					if (($returnArray["aboutus_combobox"] = $aboutus->setBasicTimeStateEditor($linkedTo, $_POST["aboutus_id"])) != -1 &&
						($returnArray["aboutus_content"] = $aboutus->getBasicTimeStateEditor($linkedTo, $_POST["aboutus_id"] )) != -1 )
					{
						$returnArray["message"] = "success_other_save";
					}
					else $returnArray["message"] = "failed_database"; 

				}
				else
					if (isset($_POST["file_id"]))
					{
						 if ($uploadManagerObj -> updateDocumentAttachment( isset($_POST["file_id"])?$_POST["file_id"]:"",
																$_POST["document_linked_to"],
																$_POST["document_instance_of"]) != -1 )
							{
								$returnArray["message"] = "success_save";
							}
							else $returnArray["message"] = "failed_database";
					}
					else
					{
						if (($returnArray["aboutus_combobox"] = $aboutus->addBasicTimeStateEditor($_POST["aboutus_content"], $linkedTo)) != -1)
						{		
							$uploadManagerObj -> deleteDocumentAttachment( $_POST["document_linked_to"], $_POST["document_instance_of"]);
							$returnArray["aboutus_content"] = $aboutus->getBasicTimeStateEditor($linkedTo);							
		                   	$returnArray["message"] = "success_save";
						} 
						else 	$returnArray["message"] = "failed_database";
					}	
			break;

            case $linkedTo."AboutUsDelete".$instanceOf:
				$aboutus = new AboutUs($db, "aboutus", "aboutus_language", "aboutus_id");
				if (isset($_POST["aboutus_id"]) && $_POST["aboutus_id"]!="-1")
				{
					if 	($aboutus->deleteAboutUs($_POST["aboutus_id"]) != -1 && 
						($returnArray["aboutus_combobox"] = $aboutus->getBasicTimeStateEditorComboBox($linkedTo)) != -1 &&
						($returnArray["aboutus_content"] = $aboutus->getBasicTimeStateEditor($linkedTo)) != -1 )
					{
						$returnArray["message"] = "success_delete";
					}
					else $returnArray["message"] = "failed_database"; 
				}
			break;

	// ******************************************************************************
	// *                           Verseny szabályok                                *
	// ******************************************************************************

			case $linkedTo."RulesWrite".$instanceOf:
			case $linkedTo."Rules".$instanceOf:
				$rules = new Rules($db, "rules", "rules_language", "rules_id");
				if (($returnArray["rules_combobox"] = $rules->getBasicTimeStateEditorComboBox($linkedTo)) == -1 || 
					($returnArray["rules_content"] = $rules->getBasicTimeStateEditor($linkedTo)) == -1 )
				{ 
					$returnArray['message'] = "failed_database";
					$returnArray["rules_combobox"] = $returnArray["rules_content"] = "";
				}
				
			break;	

	

			case $linkedTo."RulesSaveBtn".$instanceOf:
				$rules = new Rules($db, "rules", "rules_language", "rules_id");
				$uploadManagerObj = new UploadManager($db);

				if (isset($_POST["action"]) && $_POST["action"] == "set_other" && $_POST["rules_id"]!="-1")
				{
					if (($returnArray["rules_combobox"] = $rules->setBasicTimeStateEditor($linkedTo, $_POST["rules_id"])) != -1 &&
						($returnArray["rules_content"] = $rules->getBasicTimeStateEditor($linkedTo, $_POST["rules_id"] )) != -1 )
					{
						$returnArray["message"] = "success_other_save";
					}
					else $returnArray["message"] = "failed_database"; 

				}
				else
					if (isset($_POST["file_id"]))
					{
						if ($uploadManagerObj -> updateDocumentAttachment( isset($_POST["file_id"])?$_POST["file_id"]:"",
																$_POST["document_linked_to"],
																$_POST["document_instance_of"]) != -1 )
							{
								$returnArray["message"] = "success_save";
							}
							else $returnArray["message"] = "failed_database";
					}
					else
					{
						if (($returnArray["rules_combobox"] = $rules->addBasicTimeStateEditor($_POST["rules_content"], $linkedTo)) != -1)
						{		
							$uploadManagerObj -> deleteDocumentAttachment( $_POST["document_linked_to"], $_POST["document_instance_of"]);
							$returnArray["rules_content"] = $rules->getBasicTimeStateEditor($linkedTo);							
		                   	$returnArray["message"] = "success_save";
						} 
						else 	$returnArray["message"] = "failed_database";
					}
							
			break;

            case $linkedTo."RulesDelete".$instanceOf:
				$rules = new Rules($db, "rules", "rules_language", "rules_id");
				if (isset($_POST["rules_id"]) && $_POST["rules_id"]!="-1")
				{
					if 	($rules->deleteBasicTimeStateEditor($_POST["rules_id"]) != -1 && 
						($returnArray["rules_combobox"] = $rules->getBasicTimeStateEditorComboBox($linkedTo)) != -1 &&
						($returnArray["rules_content"] = $rules->getBasicTimeStateEditor($linkedTo)) != -1 )
					{
						$returnArray["message"] = "success_delete";
					}
					else $returnArray["message"] = "failed_database"; 
				}
			break;

	// ******************************************************************************
	// *                             Kapcsolat menü                                 *
	// ******************************************************************************

			case $linkedTo."ContactsWrite".$instanceOf:
			case $linkedTo."Contacts".$instanceOf:
				
				$contacts = new Contacts($db, "contacts", "contacts_language", "contacts_id");
				if (($returnArray["contacts_combobox"] = $contacts->getBasicTimeStateEditorComboBox($linkedTo)) == -1 || 
					($returnArray["contacts_content"] = $contacts->getBasicTimeStateEditor($linkedTo)) == -1 )
				{ 
					$returnArray['message'] = "failed_database";
					$returnArray["contacts_combobox"] = $returnArray["contacts_content"] = "";
				}
				
			break;

			case $linkedTo."ContactsSaveBtn".$instanceOf:

				$contacts = new Contacts($db, "contacts", "contacts_language", "contacts_id");
				$uploadManagerObj = new UploadManager($db);

				if (isset($_POST["action"]) && $_POST["action"] == "set_other" && $_POST["contacts_id"]!="-1")
				{
					if (($returnArray["contacts_combobox"] = $contacts->setBasicTimeStateEditor($linkedTo, $_POST["contacts_id"])) != -1 &&
						($returnArray["contacts_content"] = $contacts->getBasicTimeStateEditor($linkedTo, $_POST["contacts_id"] )) != -1 )
					{
						$returnArray["message"] = "success_other_save";
					}
					else $returnArray["message"] = "failed_database"; 

				}
				else

					if (isset($_POST["file_id"]))
					{
						if ($uploadManagerObj -> updateDocumentAttachment( isset($_POST["file_id"])?$_POST["file_id"]:"",
																$_POST["document_linked_to"],
																$_POST["document_instance_of"]) != -1 )
							{
								$returnArray["message"] = "success_save";
							}
							else $returnArray["message"] = "failed_database";

					}
					else
					{

						if (($returnArray["contacts_combobox"] = $contacts->addBasicTimeStateEditor($_POST["contacts_content"], $linkedTo)) != -1)
						{
							$uploadManagerObj -> deleteDocumentAttachment( $_POST["document_linked_to"], $_POST["document_instance_of"]);
							$returnArray["contacts_content"] = $contacts->getBasicTimeStateEditor($linkedTo);							
		                   	$returnArray["message"] = "success_save";
						} 
						else 	$returnArray["message"] = "failed_database";
					}
							
			break;

            case $linkedTo."ContactsDelete".$instanceOf:

				$contacts = new Contacts($db, "contacts", "contacts_language", "contacts_id");
				if (isset($_POST["contacts_id"]) && $_POST["contacts_id"]!="-1")
				{
					if 	($contacts->deleteContacts($_POST["contacts_id"]) != -1 && 
						($returnArray["contacts_combobox"] = $contacts->getBasicTimeStateEditorComboBox($linkedTo)) != -1 &&
						($returnArray["contacts_content"] = $contacts->getBasicTimeStateEditor($linkedTo)) != -1 )
					{
						$returnArray["message"] = "success_delete";
					}
					else $returnArray["message"] = "failed_database"; 
				}
			break;

	// ******************************************************************************
	// *                             Történetünk menü                               *
	// ******************************************************************************

			case $linkedTo."StoryWrite".$instanceOf:
			case $linkedTo."Story".$instanceOf:
				
				$story = new Story($db, "story", "story_language", "story_id");
				if (($returnArray["story_combobox"] = $story->getBasicTimeStateEditorComboBox($linkedTo)) == -1 || 
					($returnArray["story_content"] = $story->getBasicTimeStateEditor($linkedTo)) == -1 )
				{ 
					$returnArray['message'] = "failed_database";
					$returnArray["story_combobox"] = $returnArray["story_content"] = "";
				}
				
			break;

			case $linkedTo."StorySaveBtn".$instanceOf:

				$story = new Story($db, "story", "story_language", "story_id");
				$uploadManagerObj = new UploadManager($db);

				if (isset($_POST["action"]) && $_POST["action"] == "set_other" && $_POST["story_id"]!="-1")
				{
					if (($returnArray["story_combobox"] = $story->setBasicTimeStateEditor($linkedTo, $_POST["story_id"])) != -1 &&
						($returnArray["story_content"] = $story->getBasicTimeStateEditor($linkedTo, $_POST["story_id"] )) != -1 )
					{
						$returnArray["message"] = "success_other_save";
					}
					else $returnArray["message"] = "failed_database"; 

				}
				else

					if (isset($_POST["file_id"]))
					{
						if ($uploadManagerObj -> updateDocumentAttachment( isset($_POST["file_id"])?$_POST["file_id"]:"",
																$_POST["document_linked_to"],
																$_POST["document_instance_of"]) != -1 )
							{
								$returnArray["message"] = "success_save";
							}
							else $returnArray["message"] = "failed_database";

					}
					else
					{

						if (($returnArray["story_combobox"] = $story->addBasicTimeStateEditor($_POST["story_content"], $linkedTo)) != -1)
						{		
							$uploadManagerObj -> deleteDocumentAttachment( $_POST["document_linked_to"], $_POST["document_instance_of"]);
							$returnArray["story_content"] = $story->getBasicTimeStateEditor($linkedTo);
							$returnArray["message"] = "success_save";							
		                   
						} 
						else 	$returnArray["message"] = "failed_database";
					}
							
			break;

            case $linkedTo."StoryDelete".$instanceOf:

				$story = new Story($db, "story", "story_language", "story_id");
				if (isset($_POST["story_id"]) && $_POST["story_id"]!="-1")
				{
					if 	($story->deleteBasicTimeStateEditor($_POST["story_id"]) != -1 && 
						($returnArray["story_combobox"] = $story->getBasicTimeStateEditorComboBox($linkedTo)) != -1 &&
						($returnArray["story_content"] = $story->getBasicTimeStateEditor($linkedTo)) != -1 )
					{
						$returnArray["message"] = "success_delete";
					}
					else $returnArray["message"] = "failed_database"; 
				}
			break;

	// ******************************************************************************
	// *                             Akadályok menü                               *
	// ******************************************************************************

			case $linkedTo."CompetitionObstaclesWrite".$instanceOf:
			case $linkedTo."CompetitionObstacles".$instanceOf:
				
				$obstacles = new CompetitionObstacles($db, "obstacles", "obstacles_language", "obstacles_id");
				if (($returnArray["obstacles_combobox"] = $obstacles->getBasicTimeStateEditorComboBox($linkedTo)) == -1 || 
					($returnArray["obstacles_content"] = $obstacles->getBasicTimeStateEditor($linkedTo)) == -1 )
				{ 
					$returnArray['message'] = "failed_database";
					$returnArray["obstacles_combobox"] = $returnArray["obstacles_content"] = "";
				}
				
			break;

			case $linkedTo."CompetitionObstaclesSaveBtn".$instanceOf:

				$obstacles = new CompetitionObstacles($db, "obstacles", "obstacles_language", "obstacles_id");
				$uploadManagerObj = new UploadManager($db);

				if (isset($_POST["action"]) && $_POST["action"] == "set_other" && $_POST["obstacles_id"]!="-1")
				{
					if (($returnArray["obstacles_combobox"] = $obstacles->setBasicTimeStateEditor($linkedTo, $_POST["obstacles_id"])) != -1 &&
						($returnArray["obstacles_content"] = $obstacles->getBasicTimeStateEditor($linkedTo, $_POST["obstacles_id"] )) != -1 )
					{
						$returnArray["message"] = "success_other_save";
					}
					else $returnArray["message"] = "failed_database"; 

				}
				else

					if (isset($_POST["file_id"]))
					{
						if ($uploadManagerObj -> updateDocumentAttachment( isset($_POST["file_id"])?$_POST["file_id"]:"",
																$_POST["document_linked_to"],
																$_POST["document_instance_of"]) != -1 )
							{
								$returnArray["message"] = "success_save";
							}
							else $returnArray["message"] = "failed_database";

					}
					else
					{

						if (($returnArray["obstacles_combobox"] = $obstacles->addBasicTimeStateEditor($_POST["obstacles_content"], $linkedTo)) != -1)
						{		
							$uploadManagerObj -> deleteDocumentAttachment( $_POST["document_linked_to"], $_POST["document_instance_of"]);
							$returnArray["obstacles_content"] = $obstacles->getBasicTimeStateEditor($linkedTo);
							$returnArray["message"] = "success_save";							
		                   
						} 
						else 	$returnArray["message"] = "failed_database";
					}
							
			break;

            case $linkedTo."CompetitionObstaclesDelete".$instanceOf:

				$obstacles = new CompetitionObstacles($db, "obstacles", "obstacles_language", "obstacles_id");
				if (isset($_POST["obstacles_id"]) && $_POST["obstacles_id"]!="-1")
				{
					if 	($obstacles->deleteBasicTimeStateEditor($_POST["obstacles_id"]) != -1 && 
						($returnArray["obstacles_combobox"] = $obstacles->getBasicTimeStateEditorComboBox($linkedTo)) != -1 &&
						($returnArray["obstacles_content"] = $obstacles->getBasicTimeStateEditor($linkedTo)) != -1 )
					{
						$returnArray["message"] = "success_delete";
					}
					else $returnArray["message"] = "failed_database"; 
				}
			break;

	// ******************************************************************************
	// *                             Pályavázlat menü                               *
	// ******************************************************************************

			case $linkedTo."CompetitionFieldDescriptionWrite".$instanceOf:
			case $linkedTo."CompetitionFieldDescription".$instanceOf:
				
				$field_description = new CompetitionFieldDescription($db, "field_description", "field_description_language", "field_description_id");
				if (($returnArray["field_description_combobox"] = $field_description->getBasicTimeStateEditorComboBox($linkedTo)) == -1 || 
					($returnArray["field_description_content"] = $field_description->getBasicTimeStateEditor($linkedTo)) == -1 )
				{ 
					$returnArray['message'] = "failed_database";
					$returnArray["field_description_combobox"] = $returnArray["field_description_content"] = "";
				}
				
			break;

			case $linkedTo."CompetitionFieldDescriptionSaveBtn".$instanceOf:

				$field_description = new CompetitionFieldDescription($db, "field_description", "field_description_language", "field_description_id");
				$uploadManagerObj = new UploadManager($db);

				if (isset($_POST["action"]) && $_POST["action"] == "set_other" && $_POST["field_description_id"]!="-1")
				{
					if (($returnArray["field_description_combobox"] = $field_description->setBasicTimeStateEditor($linkedTo, $_POST["field_description_id"])) != -1 &&
						($returnArray["field_description_content"] = $field_description->getBasicTimeStateEditor($linkedTo, $_POST["field_description_id"] )) != -1 )
					{
						$returnArray["message"] = "success_other_save";
					}
					else $returnArray["message"] = "failed_database"; 

				}
				else

					if (isset($_POST["file_id"]))
					{
						if ($uploadManagerObj -> updateDocumentAttachment( isset($_POST["file_id"])?$_POST["file_id"]:"",
																$_POST["document_linked_to"],
																$_POST["document_instance_of"]) != -1 )
							{
								$returnArray["message"] = "success_save";
							}
							else $returnArray["message"] = "failed_database";

					}
					else
					{

						if (($returnArray["field_description_combobox"] = $field_description->addBasicTimeStateEditor($_POST["field_description_content"], $linkedTo)) != -1)
						{		
							$uploadManagerObj -> deleteDocumentAttachment( $_POST["document_linked_to"], $_POST["document_instance_of"]);
							$returnArray["field_description_content"] = $field_description->getBasicTimeStateEditor($linkedTo);
							$returnArray["message"] = "success_save";							
		                   
						} 
						else 	$returnArray["message"] = "failed_database";
					}
							
			break;

            case $linkedTo."CompetitionFieldDescriptionDelete".$instanceOf:

				$field_description = new CompetitionFieldDescription($db, "field_description", "field_description_language", "field_description_id");
				if (isset($_POST["field_description_id"]) && $_POST["field_description_id"]!="-1")
				{
					if 	($field_description->deleteBasicTimeStateEditor($_POST["field_description_id"]) != -1 && 
						($returnArray["field_description_combobox"] = $field_description->getBasicTimeStateEditorComboBox($linkedTo)) != -1 &&
						($returnArray["field_description_content"] = $field_description->getBasicTimeStateEditor($linkedTo)) != -1 )
					{
						$returnArray["message"] = "success_delete";
					}
					else $returnArray["message"] = "failed_database"; 
				}
			break;
	

	// ******************************************************************************
	// *                             Támogatók menü                                 *
	// ******************************************************************************

			case $linkedTo."Colleague".$instanceOf:
			break;

			case $linkedTo."ColleagueAdd".$instanceOf:
				$colleague = new Colleague($db);
				if(isset($_POST["action"]))
				{
					switch($_POST["action"])
					{
						case "addColleague":
							$returnArray["message"] = $colleague -> addColleague(	
																			$_POST["lastname"],
																			$_POST["firstname"],
																			$_POST["phone"],
																			$_POST["email"],
																			$_POST["scope"],
																			isset($_POST["photo_id"])?$_POST["photo_id"]:0
																		);
							$colleague->colleagueID = 0;
						break;
					}
				}
				$returnArray["content"] = $colleague->getColleagueForm("Új munkatárs","Mentés", $uploadRootFolderID, $linkedTo);
				
			break;

			case $linkedTo."ColleagueEdit".$instanceOf:
				$colleague = new Colleague($db);
				$colleague->colleagueID = isset($_POST["colleagueID"])?$_POST["colleagueID"]:0;
				
				if(isset($_POST["action"]))
				{
					switch($_POST["action"])
					{
						case "checkColleagueExists":
							echo $colleague->checkColleaguename($_POST["colleaguename"],1);
							return;
						break;
						case "editColleague":
							$returnArray["message"] = $colleague->updateColleague(
																			$_POST["lastname"],
																			$_POST["firstname"],
																			$_POST["phone"],
																			$_POST["email"],
																			$_POST["scope"],
																			isset($_POST["photo_id"])?$_POST["photo_id"]:0
																);
							$colleague->colleagueID = 0;
						break;
					}
				}

				$returnArray["content"] = $colleague->getColleagueForm("Munkatársak adatainak szerkesztése","Mentés",$uploadRootFolderID, $linkedTo, 1, 1, 1);
			break;

			case $linkedTo."ColleagueDelete".$instanceOf:
				$colleague = new Colleague($db);
				$colleague->colleagueID = isset($_POST["colleagueID"])?$_POST["colleagueID"]:0;
				if(isset($_POST["delete"]))
				{
					$returnArray["message"] = $colleague->deleteColleague();
				}
				$returnArray["content"] = $colleague->getColleagueForm("Munkatárs törlése","Törlés", $uploadRootFolderID, $linkedTo, 0, 1, 1);
			break;

			default:			
				
			break;
		}
	}
	else
	{
		$returnArray["message"] = "failed_user_authority";
		$returnArray["content"] = "";
	}

	if(isset($returnArray["content"]) && $returnArray["content"] == -1)
	{
		$returnArray["content"] = "";
		$returnArray["message"] = "failed_database";
	}
	echo json_encode($returnArray, JSON_FORCE_OBJECT);
	unset($db);
}
else
{
	if (isset($_POST["login"]) && $_POST["login"] == "1")
	{
		$returnArray["message"] = $user->userLogin($_POST["username"],$_POST["password"]);
	}
	else
	{
		$returnArray["state"] = "login";
		$returnArray["content"] = $user->getUserLoginForm();
	}
	echo json_encode($returnArray, JSON_FORCE_OBJECT);
}
?>