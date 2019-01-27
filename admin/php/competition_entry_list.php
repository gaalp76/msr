<?php
session_start();
include "config.php";
include "connect.php";

$db = Database::getConnection();

$str_length = 5;
$competitionID = $_GET['param'];
$odt_search = (!empty($_GET['odtSearch']))?$_GET['odtSearch']:"";
$sort_col = (!empty($_GET['sortCol']))?$_GET['sortCol']:2;	
$sort_type = (!empty($_GET['sortType']))?$_GET['sortType']:"ASC";
$search_col = array(
						"team_name",
						"admin_reg_confirm",
						"id",
						"lastname",
						"reg_date",
						"born_date",
						"mother_name",
						"comp_dist",
						"t_shirt",
						"email",
						"sex",
						"conf_email_sent",
						"city",
						"identity_card",
 						"phone",
						"er_name", 
						"er_phone",
						"reg_confirm",
						"admin_reg_confirm"
					);

$order_types = array("ASC" => "ASC","DESC" => "DESC");

$search_firstname = '%'.$odt_search.'%';
$search_lastname = '%'.$odt_search.'%';
$search_email = '%'.$odt_search.'%';
$competitionRegID = '%'.$odt_search.'%';
$search_phone = '%'.$odt_search.'%';
$search_identity_card = '%'.$odt_search.'%';
$search_team_name = '%'.$odt_search.'%';

$odt_start = $_GET['odt_Start'];
$odt_stop = $_GET['odt_Stop'];

$orderby = $search_col[$sort_col];
$order_type = $order_types[$sort_type];

if ($stmt = $db->prepare("SELECT 		competition_registration.id,
										competition_registration.admin_reg_confirm,
										competition_team.name as team_name,
										competition_registration.firstname,
										competition_registration.lastname,
										competition_registration.email,
										competition_registration.phone,
										competition_registration.identity_card,
										competition_registration.team_id as accepted_team_id,
										competition_team.id as team_id,
										competition_registration.invited_team_id,
										competition_registration.mother_name,
										competition_registration.comp_dist,
										competition_registration.sex,
										competition_registration.born_date,
										competition_registration.er_name,
										competition_registration.er_phone,
										competition_registration.t_shirt,
										competition_registration.conf_email_sent,
										competition_registration.lang,
										competition_registration.reg_confirm,
										competition_registration.city,
										competition_registration.reg_date
										FROM competition_registration
										LEFT JOIN competition_team
										ON competition_registration.invited_team_id = competition_team.id  
										WHERE competition_registration.competition_id = ? 
										
										AND (	competition_registration.firstname LIKE ? 
												OR competition_registration.lastname LIKE ? 
												OR competition_registration.email LIKE ? 
												OR competition_registration.phone LIKE ? 
												OR competition_registration.identity_card LIKE ?
												OR competition_team.name LIKE ?
												OR competition_registration.id LIKE ?
												)
										/* AND competition_registration.reg_confirm = '1' */
										ORDER BY $orderby $order_type 
										LIMIT ?,? 
							 "))
{
	$stmt->bind_param("isssssssii", 
							$competitionID,
							$search_firstname,
							$search_lastname,
							$search_email,
							$search_phone,
							$search_identity_card,
							$search_team_name,
							$competitionRegID,
							$odt_start,
							$odt_stop
						);
	
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($competitionRegID,$admin_reg_confirm,$team_name,$firstname,$lastname,$email,$phone,$indetity_card,$accepted_team_id,$team_id, $invited_team_id, $mother_name, $comp_dist, $sex, $born_date, $er_name, $er_phone, $t_shirt, $conf_email_sent, $lang, $reg_confirm, $city, $reg_date);
	
	

	while($stmt -> fetch())
	{
		switch ($t_shirt)
		{
			case "0":
				$t_shirt = "-";
				break;
			case "1":
				$t_shirt = "XS";
				break;
			case "2":
				$t_shirt = "S";
				break;
			case "3":
				$t_shirt = "M";
				break;
			case "4":
				$t_shirt = "L";
				break;
			case "5":
				$t_shirt = "XL";
				break;
			case "6":
				$t_shirt = "XXL";
				break;
			case "7":
				$t_shirt = "3XL";
				break;
			case "8":
				$t_shirt = "4XL";
				break;
			case "9":
				$t_shirt = "5XL";
				break;
		}
		
		if ($t_shirt == "4") $t_shirt_s = "XXL";
		
		if ($distance_stmt = $db->prepare("SELECT 	distance.name AS distance_name	FROM 
										competition_registration INNER JOIN distance
										ON competition_registration.comp_dist = distance.id
										WHERE competition_registration.id = ?
									"))
		{
			$distance_stmt->bind_param("i",$competitionRegID);
			$distance_stmt->execute();
			$distance_result = $distance_stmt->get_result();
			$distance_row = $distance_result->fetch_assoc();
			$comp_dist = $distance_row["distance_name"] ;
			
			
		}
		else return -1;

		

		
		if ($reg_confirm == "1") 
		{
			$reg_confirm = "igen";
		}
		else
		{
			$reg_confirm = "nem";
			//$reg_confirm = "<button class='send_competition_email_btn' comp_reg_id='".$competitionRegID."' competition_id='".$competitionID."' lang='".$lang."'>Elküld</button>";
		}

		if ($conf_email_sent == 0) 
		{
			$conf_email_sent = "nem";
			//$conf_email_sent = "<button class='send_conf_competition_email_btn' comp_reg_id='".$competitionRegID."' competition_id='".$competitionID."' lang='".$lang."'>Elküld</button>";
		}
		

		
		$dataRaw = array(	
							$team_name.($invited_team_id?($accepted_team_id?" - <span style='color:#a9bc87; font-weight:bold'>elfogadva</span>":" - <span style='color:#bfac70'>folyamatban</span>"):"Nincs csapatfelkérés"),
							"<input type='checkbox' ".($checked=$admin_reg_confirm == '1'?'checked':'')." ".($disabled=$admin_reg_confirm == '1'?'disabled':'')." name='accepted' class='accepted' value='".$competitionRegID."'>",

							substr(str_repeat(0, $str_length) . $competitionRegID, -$str_length),
							$lastname." ".$firstname,
							$reg_date,
							$born_date,
							$mother_name,
							$comp_dist,
							$t_shirt,
							$email,
							($sex == '1')?'férfi':'nő',
							$conf_email_sent,
							$city,
							$indetity_card,
							$phone,
							$er_name, 
							$er_phone,
							$reg_confirm,
							($admin_reg_confirm == '1')?'igen':'nem',			
							($admin_reg_confirm == '1')?"<div class='delete-entry' competitionregid='".$competitionRegID."'>Visszavonás</div>":"",
							($admin_reg_confirm == '1')?"Nem törölhető":"<img competitionregid='".$competitionRegID."' class='remove-entry' src='../img/common/recyclebin.png' alt='Törlés'>"
						);
    	$data['row'][] = $dataRaw;
	}
	$data['num_rec'] = $stmt->num_rows;

	$stmt->close();

	if ($stmt = $db->prepare("SELECT 	id	
										FROM competition_registration 
											WHERE competition_id = ? 
									"))
	{
		$stmt->bind_param("i",$competitionID);
		$stmt->execute();
		$stmt->store_result();
		
		$data['num_rows'] = $stmt->num_rows;
		
	}
	else return -1;

	if ($stmt = $db->prepare("SELECT 	id	
										FROM competition_registration 
											WHERE competition_id = ? AND admin_reg_confirm = '1' 
									"))
	{
		$stmt->bind_param("i",$competitionID);
		$stmt->execute();
		$stmt->store_result();
		
		$data['num_reg_confirm'] = $stmt->num_rows;
		
	}
	else return -1;

	if ($stmt = $stmt = $db->prepare("SELECT reg_type FROM competition WHERE id = ?"))
		{

			$stmt->bind_param("i", $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();

			if($row["reg_type"] == "military")
			{
				if ($stmt = $db->prepare("SELECT competition_guest_registration.*, competition_registration.lastname as competitior_lastname, competition_registration.firstname as competitior_firstname FROM competition_guest_registration INNER JOIN competition_registration ON competition_guest_registration.comp_reg_id = competition_registration.id WHERE competition_registration.competition_id = ? ORDER BY competitior_lastname, competitior_firstname, lastname, firstname"))
				{
					$stmt->bind_param("i", $competitionID);
					$stmt->execute();
					$result = $stmt->get_result();
					while($row = $result->fetch_assoc())
					{
						$row["comp_reg_id"] = substr(str_repeat(0, $str_length) . $competitionRegID, -$str_length);
						$dataRaw = array($row);
						$data['guest_row'][] = $dataRaw;
					}
				}
				else return -1;
			}
		}
		else return -1;
}
else return -1;
echo json_encode($data);
?>