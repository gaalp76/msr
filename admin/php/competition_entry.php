<?php
/**
*  COMPETITION ENTRY CLASS
	function __construct($db)
	public function registrationIsActive($linkedTo)
	public function registrationIsFull($competitionID)
	public function getRegistrationTimeLeft($competitionID, $linkedTo, $lang)
	public function registrationIsFullText($lang)
	public function registrationInActiveText($lang)
	public function getCompetitionType($competitionID)
	public function addGuests($guestData,$competitionRegistrationID)
	public function addEntry($competitionID, $lastname, $firstname, $email, $phone, $country, $zip, $city, $address, $pid = "", $linkedTo = "", 							 $lang ="hu")
	public function addTeam($competitionID,	$teamName, $teamates_array, $teamLeaderID, $lang ="hu")
	private function sendMail($address, $subject, $message)
	public function sendMailRegistration($competitionRegID, $competitionID, $lang="hu" )
	public function getCompetitionRegID($userID, $competitionID)
	public function userIsRegistered($userID, $competitionID)
	public function userRegisterProcessText($lang,$competitionID)
	public function userRegisterAdminProcessText($lang)
	public function userIsRegisteredText($lang)
	public function confirmCompRegistration($id )
	public function acceptEntry($accepted_array)
	public function deleteEntry($competitionRegID)
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CompetitionEntry extends Config
{
	private $db;	
	private $menu;
	public $competitionEntryID = 0;

	function __construct($db)
	{
		$this->db = $db;
		parent::__construct();
	}

	public function registrationIsActive($linkedTo)
	{ 
		if ( $stmt = $this->db->prepare("SELECT id FROM competition 
										 WHERE reg_start_date <= '".date("Y-m-d H:i:s")."' 
										 AND reg_end_date >='".date("Y-m-d H:i:s")."' AND linked_to = ? ORDER BY start_date") )
		{
			$stmt->bind_param("s", $linkedTo);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($competitionID);
			$stmt->fetch();
			return($competitionID);
		} 
		else return -1;

	}

	public function registrationIsFull($competitionID)
	{
		if ( $stmt = $this->db->prepare("SELECT COUNT(*) as registered FROM competition_registration  
													WHERE competition_id = ? AND reg_confirm = '1' GROUP BY competition_id") )
		{
			$stmt->bind_param("i", $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$registered = isset($row["registered"])?$row["registered"]:0;

			if ( $stmt = $this->db->prepare("SELECT max_reg_number FROM competition  
													WHERE id = ? ") )
			{
				$stmt->bind_param("i", $competitionID);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				$max_reg_number = $row["max_reg_number"];	
			}
			else return -1;
		}
		else return -1;

		return $max_reg_number <= $registered;
	}

	public function getRegistrationTimeLeft($competitionID, $linkedTo, $lang)
	{
		$registrationTimeLeftLang = array("hu" => " nap van hátra a nevezés lezárásáig.",
										  "en" => " days to registration has been closed.");
		$html = "<div class='registration-time-left-container'>";
		if ( $stmt = $this->db->prepare("SELECT reg_end_date FROM competition 
													WHERE id = ?") )
		{
			$stmt->bind_param("i", $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$date1 = date_create($row["reg_end_date"]);
			$date2 = date_create(date("Y-m-d"));
			$diff = date_diff($date1,$date2);
			$html .= $diff ->format("%a".$registrationTimeLeftLang[$lang]);
		} 
		else return -1;
		$html .= "</div>";
		return $html;
	}

	public function registrationIsFullText($lang)
	{
		$registrationIsFull = array("hu" => "A nevezések száma elérte a maximumot.",
										  "en" => "Registration limit is over.");
		$html = "<div class='registration-limit-over-container'>";
		$html .= $registrationIsFull[$lang];
		$html .= "</div>";
		return $html;
	}

	public function registrationInActiveText($lang)
	{
		$registrationInActiveLang = array("hu" => "Jelenleg nincs nevezési időszak.",
										  "en" => "There is not active registration period.");
		$html = "<div class='registration-not-active-container'>";
		$html .= $registrationInActiveLang[$lang];
		$html .= "</div>";
		return $html;
	}

	public function getCompetitionType($competitionID)
	{
		if ( $stmt = $this->db->prepare("SELECT reg_type FROM competition  
												WHERE id = ? ") )
		{
			$stmt->bind_param("i", $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			return $row["reg_type"];	
		}
		else return -1;
	}

	public function addGuests($guestData,$competitionRegistrationID,$competitionID)
	{
		

		foreach ($guestData as $data)
    	{

			if ( $stmt = $this->db->prepare("INSERT INTO competition_guest_registration (
																comp_reg_id,
																competition_id,
																email,
																lastname,
																firstname,
																bornname,
																mothername,
																borndate,
																sex,
																nationality,
																pid,
																pid_type,
																phone,
																auto,
																zip,
																city,
																address,
																er_name,
																er_phone
																)
													VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)") )
			
			{

				$stmt->bind_param("iissssssissssssssss", 
												$competitionRegistrationID,
												$competitionID,	
												$data["email"],
												$data["lastname"],
												$data["firstname"],
												$data["bornname"],
												$data["mothername"],
												$data["borndate"],
												$data["gender"],
												$data["nationality"],
												$data["pid"],
												$data["pid_type"],
												$data["phone"],
												$data["auto_data"],
												$data["zip"],
												$data["city"],
												$data["address"],
												$data["er_name"],
												$data["er_phone"]
									);
				$stmt->execute();
				
			}		
			else return "failed_database";
			
		}
	}
	

	public function addEntry(	
								$competitionID,	
								$lastname,
								$firstname,
								$email,
								$phone,
								$country,
								$zip,
								$city,
								$address,
								$pid = "",
								$mother_name,
								$born_date,
								$sex,
								$er_name,
								$er_phone,
								$t_shirt,
								$comp_dist = 0,
								$guest_data,
								$linkedTo = "",
								$lang ="hu"

							)
	{		

		if ($stmt = $this->db->prepare("SELECT id FROM competition_registration WHERE email = ? AND competition_id=?"))
		{

			$stmt->bind_param("si", $email, $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows > 0) 
			{
				return 0;//"failed_reg_comp_email_exists";
				
			}
			else 
			{

				if ( $stmt = $this->db->prepare("INSERT INTO competition_registration (
															competition_id,
															lastname,
															firstname,
															email,
															phone,
															country,
															zip,
															city,
															address,
															identity_card,
															comp_dist,
															linked_to,
															lang,
															mother_name,
															born_date,
															sex,
															er_name,
															er_phone,
															t_shirt
															)
												VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)") )
		
				{
					$stmt->bind_param("isssssssssissssssss", $competitionID,	
													$lastname,
													$firstname,
													$email,
													$phone,
													$country,
													$zip,
													$city,
													$address,
													$pid,
													$comp_dist,
													$linkedTo,
													$lang,
													$mother_name,
													$born_date,
													$sex,
													$er_name,
													$er_phone,
													$t_shirt
										);

					$stmt->execute();

					if ($stmt->affected_rows > 0)
					{

						if(is_array($guest_data)) 
						{
							$this -> addGuests($guest_data, $stmt->insert_id, $competitionID);
						}

						switch ($this->sendMailRegistration($stmt->insert_id, $competitionID, 0, $lang ))
						{
							case 0: 
								return "failed_email_sent";
							break;
							case -1:
								return "failed_database";
							break;
							case 1:
								return "success_register";
							break;
					
						}		
					}
				}
				else return "failed_database";
			}
		}
		else return "failed_database";

		
		
	}

	public function addTeam(	
								$competitionID,	
								$teamName,
								$teamatesIDArray,
								$teamLeaderID,
								$linkedTo, 
								$lang ="hu"
							)
	{								

		if ( $stmt = $this->db->prepare("INSERT INTO competition_team (
															name,
															create_date,
															competition_id,
															creator_id
															)
												VALUES (?,?,?,?)") )
		{
			$createDate = date('Y-m-d H:i:s');
			$stmt->bind_param("ssii", 	$teamName,
										$createDate,
										$competitionID,
										$teamLeaderID
								);

			$stmt->execute();
			$last_insert_id = $stmt->insert_id;

			if ($stmt->affected_rows > 0) 
			{	
				foreach ($teamatesIDArray as $teamateID) 
				{
					if ( $stmt = $this->db->prepare("UPDATE competition_registration SET invited_team_id = ? WHERE id = ?") )
					{
						$stmt->bind_param("ii", $last_insert_id, $teamateID);
						$stmt->execute();
						$result = $stmt->get_result();

						if ($stmt->affected_rows > 0) 
						{
							$this->sendMailTeamAdded($teamateID, $competitionID, $teamName, $last_insert_id, $linkedTo, $lang);
						}
						else return -2;
						
					}	
					
				}
				$this->addTeamateToTeam($teamLeaderID, $competitionID, $last_insert_id, $linkedTo, $lang);
				
				return "success_add_team";
			}
			else return "failed_add_team";
		}
		else return -1;
		
	}

	public function addTeamateToTeam($competitionRegID, $competitionID, $teamID, $lang="hu")
	{
			
		if (is_array($competitionRegID)) $teamates_str = implode(",", $competitionRegID);
		else $teamates_str = $competitionRegID;

		if ( $stmt = $this->db->prepare("SELECT name FROM competition_team
													WHERE id = ? ") )
		{
			$stmt->bind_param("i", $teamID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$teamName = $row["name"];
		}
			
		$sql = "UPDATE competition_registration SET team_id = ? WHERE id IN (".$teamates_str.") AND competition_id=? AND reg_confirm=1 AND admin_reg_confirm=1";
		
		if ($stmt = $this->db->prepare($sql))
		{
			$stmt->bind_param("ii", $teamID, $competitionID);
			$stmt->execute();

			if ($stmt->affected_rows > 0) 
			{
				$this->sendMailTeamateAdded($competitionRegID, $competitionID, $teamName, $teamID, $lang );
				return "success_add_teamate";
				
			}
			else return -2;
		}
		else return -1;
	}

	public function deleteTeam($teamID)
	{
		if ($stmt = $this->db->prepare("DELETE FROM competition_team WHERE id = ?"))
		{
			$stmt->bind_param("i", $teamID);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if ($stmt->affected_rows > 0) 
			{
				if ($stmt = $this->db->prepare("UPDATE competition_registration SET team_id = 0, invited_team_id = 0 WHERE invited_team_id = ?"))
				{
					$stmt->bind_param("i", $teamID);
					$stmt->execute();
					$result = $stmt->get_result();
					
					if ($stmt->affected_rows > 0) 
					{
						return "success_team_delete";
					}
					else return -2;
				}
				else return -1;
			}
			else return -2;
		}
		else return -1;	
	}

	public function regUserIsMember($competitionRegID, $competitionID)
	{
		if ($stmt = $this->db->prepare("SELECT team_id FROM competition_registration WHERE id = ? AND competition_id = ?"))
		{
			$stmt->bind_param("ii", $competitionRegID, $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			
			if ($stmt->affected_rows > 0) return $row["team_id"];
			else return -2;
		}
		else return -1;
	}

	public function regUserIsinvitedIntoTeam($competitionRegID, $competitionID)
	{
		if ($stmt = $this->db->prepare("SELECT invited_team_id FROM competition_registration WHERE id = ? AND competition_id = ?"))
		{
			$stmt->bind_param("ii", $competitionRegID, $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			
			if ($stmt->affected_rows > 0) return $row["invited_team_id"];
			else return -2;
		}
		else return -1;
	}

	public function getTeamNameFromID($teamID, $competitionID)
	{
		if ($stmt = $this->db->prepare("SELECT name FROM competition_team WHERE id = ? AND competition_id = ?"))
		{
			$stmt->bind_param("ii", $teamID, $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			
			if ($stmt->affected_rows > 0) return $row["name"];
			else return -2;
		}
		else return -1;
	}

	public function getTeamMembersID($teamID, $competitionID)
	{
		if ($stmt = $this->db->prepare("SELECT id FROM competition_registration WHERE team_id = ? AND competition_id = ?"))
		{
			$stmt->bind_param("ii", $teamID, $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if ($stmt->affected_rows > 0)
			{
				while ( $row = $result->fetch_assoc() ) 
				{
					$teamatesIDArray[] = $row["id"];
				}
				return $teamatesIDArray;	
			}			 
			else return -2;
		}
		else return -1;
	}

	public function getIntoTeamInvitedUsers($teamID, $competitionID)
	{
		if ($stmt = $this->db->prepare("SELECT id FROM competition_registration WHERE invited_team_id = ? AND competition_id = ?"))
		{
			$stmt->bind_param("ii", $teamID, $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if ($stmt->affected_rows > 0)
			{
				while ( $row = $result->fetch_assoc() ) 
				{
					$teamatesIDArray[] = $row["id"];
				}
				return $teamatesIDArray;	
			}			 
			else return -2;
		}
		else return -1;
	}

	public function getTeamMembersCount($competitionID)
	{
		if ($stmt = $this->db->prepare("SELECT teamate_number FROM competition WHERE id = ?"))
		{
			$stmt->bind_param("i", $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			
			if ($stmt->affected_rows > 0) return $row["teamate_number"];	
			else return -2;
		}
		else return -1;
	}

	private function sendMail($address, $subject, $message)
	{
		
		if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
		  return 0; 
		}

		$mail = new PHPMailer(true);                            // Passing `true` enables exceptions

		try {
			//Server settings
			$mail->SMTPDebug = 0;                               // Enable verbose debug output
			$mail->isSMTP();                                    // Set mailer to use SMTP
			$mail->Host = $this->MAIL_SMTP;  					// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                             // Enable SMTP authentication
			$mail->Username = $this->MAIL_USERNAME;             // SMTP username
			$mail->Password = $this->MAIL_PASSWORD;             // SMTP password
			$mail->SMTPSecure = 'ssl';                          // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 465;                                  // TCP port to connect to
			$mail->CharSet = 'UTF-8';
			
			//Recipients
			$mail->setFrom($this->MAIL_SENDER);
			$mail->addAddress($address);//$lastname." ".$firstname);     // Add a recipient
		
			//Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = $message;
			$mail->AltBody = 'Nem támogatott levelezőrendszer!';
			
			$mail->send();
			return 1;
		}
		catch (Exception $e) 
		{		
			//echo 'failed_mail'.$e;

			return 0;
		}
	}

	public function sendMailRegistration($competitionRegID, $competitionID, $admin="", $lang="hu" )
	{

		if ($stmt = $this->db->prepare("SELECT concat(firstname, ' ',lastname ) as name, email FROM competition_registration WHERE id = ?"))
		{
			$stmt->bind_param("i", $competitionRegID);
			$stmt->execute();
			$result = $stmt->get_result();
			$rowCompReg = $result->fetch_assoc();

			if($result->num_rows) 
			{

				if ($stmt = $this->db->prepare("SELECT * FROM competition WHERE id = ?"))
				{
					$stmt->bind_param("i", $competitionID);
					$stmt->execute();
					$result = $stmt->get_result();
					$rowComp = $result->fetch_assoc();
				}
				else return -1;

				//include("comp_registration_mail_".$lang."_inc.php");
				if ($admin)	include("../../php/comp_registration_mail_".$lang."_inc.php");
				else include("comp_registration_mail_".$lang."_inc.php");

				if ($this->sendMail($rowCompReg["email"], $subject, $message) ) 
				{
					if ($stmt = $this->db->prepare("UPDATE competition_registration SET email_sent = ? WHERE id = ? "))
					{
						$today = date("Y-m-d H:i:s");
						$stmt->bind_param("si", $today, $competitionRegID);
						$stmt->execute();
						return "success_send_comp_reg_mail";	
					}
					else return -1;
				}
				else return 0;
				
			}
			
		}
		else return -1;
	}

	public function sendMailConfirmRegistration($competitionRegID, $competitionID, $admin, $lang="hu" )
	{
		$where = "";

		if ($distance_stmt =  $this->db->prepare("SELECT 	distance.name AS distance_name	FROM 
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
	
		if ($competitionRegID != '*') $where = " WHERE id = ?";

		if ($stmt = $this->db->prepare("SELECT id, concat(firstname, ' ',lastname ) as name, email FROM competition_registration".$where))
		{
			if ($competitionRegID != "*") $stmt->bind_param("i", $competitionRegID);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if($result->num_rows) 
			{
				if ($stmtComp = $this->db->prepare("SELECT * FROM competition WHERE id = ?"))
				{
					$stmtComp->bind_param("i", $competitionID);
					$stmtComp->execute();
					$resultComp = $stmtComp->get_result();
					$rowComp = $resultComp->fetch_assoc();
				}
				else return -1;
				
				while ( $rowCompReg = $result->fetch_assoc() ) 
				{

					//echo "http://".$this->BASE_URL."/php/confirm_comp_registration_mail_".$lang."_inc.php";
					if ($admin)	include("../../php/confirm_comp_registration_mail_".$lang."_inc.php");
					else include("confirm_comp_registration_mail_".$lang."_inc.php");

					if ($this->sendMail($rowCompReg["email"], $subject, $message) ) 
					{
						if ($stmtConfMail = $this->db->prepare("UPDATE competition_registration SET conf_email_sent = ? WHERE id = ? "))
						{
							$today = date("Y-m-d H:i:s");
							$stmtConfMail->bind_param("si", $today, $competitionRegID);
							$stmtConfMail->execute();
							return "sent_confirm_comp_reg_mail";
						}
						else return -1;
					}	
				} // end while
				
			}
			
		}
		else return -1;			
	}

	public function sendMailTeamAdded($competitionRegID, $competitionID, $teamName, $teamID, $linkedTo, $lang="hu" )
	{

		if ($stmt = $this->db->prepare("SELECT concat(firstname, ' ',lastname ) as name, email, competition.start_date 
										FROM competition_registration 
										INNER JOIN competition ON competition_registration.competition_id=competition.id 
										WHERE competition_registration.id=? AND competition_registration.competition_id=?"))
		{
			$stmt->bind_param("ii", $competitionRegID, $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();

			include("team_added_mail_".$lang."_inc.php");
			
			if ($this->sendMail($row["email"], $subject, $message))	return "team_add_mail_sent";
			
		}
		else return -1;
	}

	public function sendMailTeamateAdded($competitionRegID, $competitionID, $teamName, $teamID, $linkedTo, $lang="hu" )
	{

		if ($stmt = $this->db->prepare("SELECT concat(firstname, ' ',lastname ) as name, email, competition.start_date 
										FROM competition_registration 
										INNER JOIN competition ON competition_registration.competition_id=competition.id 
										WHERE competition_registration.id=? AND competition_registration.competition_id=?"))
		{
			$stmt->bind_param("ii", $competitionRegID, $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();

			include("teamate_added_mail_".$lang."_inc.php");
			
			if ($this->sendMail($row["email"], $subject, $message))	return "teamate_add_mail_sent";
			
		}
		else return -1;
	}

	public function getCompetitionRegID($userID, $competitionID)
	{
		if ($stmt = $this->db->prepare("SELECT email FROM user WHERE id = ?"))
		{
			$stmt->bind_param("i", $userID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$email = $row["email"];
			if ($stmt = $this->db->prepare("SELECT id FROM competition_registration WHERE email = ? AND competition_id = ?"))
			{
				$stmt->bind_param("si", $email, $competitionID);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				return $row["id"];
			}
			else returm -1;
		}
		else return -1;
	}

	public function userIsRegistered($userID, $competitionID)
	{
		if ($stmt = $this->db->prepare("SELECT email FROM user WHERE id = ?"))
		{
			$stmt->bind_param("i", $userID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$email = $row["email"];

			if ($stmt = $this->db->prepare("SELECT email, reg_confirm, admin_reg_confirm FROM competition_registration WHERE email = ? AND competition_id = ?"))
			{
				$stmt->bind_param("si", $email, $competitionID);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				if (!$stmt->affected_rows) return "0";
				if($row["reg_confirm"] == "0") return "1";
				if($row["admin_reg_confirm"] == "0") return "2";
				return 3;

			}
			else return -1;
		}
		else return -1;
	}

	public function checkTeamateValidity($teamateID, $competitionID)
	{

		if ($stmt = $this->db->prepare("SELECT reg_confirm, admin_reg_confirm FROM competition_registration WHERE id = ? AND competition_id = ?"))
		{
			$stmt->bind_param("ii", $teamateID, $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			if (!$stmt->affected_rows) return "0";
			if($row["reg_confirm"] == "0") return "1";
			if($row["admin_reg_confirm"] == "0") return "2";
			return 3;

		}
		else return -1;
		
	}

	public function checkTeamNameExists($teamName, $competitionID)
	{

		if ($stmt = $this->db->prepare("SELECT name FROM competition_team WHERE name = ? AND competition_id = ?"))
		{
			$stmt->bind_param("si", $teamName, $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($stmt->affected_rows) return "false"; 
			else return "true";

		}
		else return -1;
		
	}

	public function userRegisterProcessText($lang,$competitionID)
	{
		$registration = array("hu" => "Nevezése folyamatban, felhasználói megerősítésre vár.",
							  "en" => "Registration in process, waiting for user confirm.");
		$html = "<div class='registration-waiting-container'>";
		$html .= $registration[$lang];
		
		$html .= "</div>";
		$html .= "<button id='resend_competition_registration_email' competition_id='".$competitionID."' type='button' class='blue1'>Megerősítő email újraküldése</button>";
		return $html;
	}

	public function userRegisterAdminProcessText($lang)
	{
		$registration = array("hu" => "Nevezése folyamatban, a szervezők megerősítésére vár.",
							  "en" => "Registration in process, waiting for staff confirm.");
		$html = "<div class='registration-waiting-container'>";
		$html .= $registration[$lang];
		$html .= "</div>";
		return $html;
	}
	public function userIsRegisteredText($lang)
	{
		$registration = array("hu" => "Nevezése elfogadva.",
							  "en" => "Registration accepted.");
		$html = "<div class='registration-success-container'>";
		$html .= $registration[$lang];
		$html .= "</div>";
		return $html;
	}

	public function confirmCompRegistration($id, $competitionID, $lang="hu")
	{

		if ($stmt = $this->db->prepare("UPDATE competition_registration SET reg_confirm = 1 WHERE id = ?"))
		{
			$stmt->bind_param("i", $id);
			$stmt->execute();

			//if ($stmt->affected_rows > 0)
			//{
				$this->sendMailConfirmRegistration($id, $competitionID, 0, $lang);
				return "success_reg_comp";	
			//} 
			//else return "failed_reg_comp";
		}
		else return -1;

	}

	public function acceptEntry($accepted_array)
	{
		$add_address = array();
		foreach ($accepted_array as $id)
		{

			if ($stmt = $this->db->prepare("SELECT id,firstname,lastname,admin_reg_confirm,email,lang FROM competition_registration WHERE id = ?"))
			{

				$stmt->bind_param("i", $id);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				if($row["admin_reg_confirm"] == "0")
				{
					$str_length = 5;
					switch($row["lang"])
					{
						case "hu":
							$subject = "Nevezés elfogadva.";
							$message = "<h1>Kedves ".$row["firstname"]." ".$row["lastname"]."! </h1>";
							$message .= "<p>Nevezését elfogadtuk.</p>";
							$message .= '<p><b>Az Ön regisztrációs kódja:'.$str = substr(str_repeat(0, $str_length) . $id, -$str_length).'</b></p>';
							$message .= "<p>Üdvözlettel:</br>'.$this->BUSS_NAME.'</br> csapata</p>";
						break ;
						case "en":
							$subject = "Nevezés elfogadva.";
							$message = "<h1>Kedves ".$row["firstname"]." ".$row["lastname"]."! </h1>";
							$message .= "<p>Nevezését elfogadtuk.</p>";
							$message .= "<p>Üdvözlettel:</br>'.$this->BUSS_NAME.'</br> csapata</p>";
						break;
					}
					$this->sendMail($row["email"],$subject,$message);
				}
			}

			if ($stmt = $this->db->prepare("UPDATE competition_registration SET admin_reg_confirm = 1 WHERE id = ?"))
			{

				$stmt->bind_param("i", $id);
				$stmt->execute();
				
			}
			else return -1;

		}
		return 'success_save';
	}

	public function deleteEntry($competitionRegID)
	{

		if ($stmt = $this->db->prepare("SELECT firstname,lastname,admin_reg_confirm,email,lang FROM competition_registration WHERE id = ?"))
		{
			$stmt->bind_param("i", $competitionRegID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			if($row["admin_reg_confirm"] == "1")
			{
				switch($row["lang"])
				{
					case "hu":
						$subject = "Nevezés törlése";
						$message = "<h1>Kedves ".$row["firstname"]." ".$row["lastname"]."! </h1>";
						$message .= "<p>Nevezésed töröltük, melynek lehetséges okai a következők:</p>";
						$message .= "<ul>";
						$message .= "<li>Nevezését regisztrációja után 5 nappal sem erősített meg.</li>";
						$message .= "<li>Utalása bankszámlaszámunkra 5 nap elteltével nem érkezett meg.</li>";
						$message .= "<li>A versenyszervezők nem járultak hozzá nevezéséhez.</li>";
						
						if($row["linked_to"] == "msr")
						{
							$message .= "<li>A Magyar Honvédség nem járult hozzá MH Pápa Bázisrepülőtér területére történő belépéséhez.</li>";
						}
						$message .= "</ul>";
						$message .= "<p>Üdvözlettel:</br>'.$this->BUSS_NAME.'</br> csapata</p>";
					break ;
					case "en":
						$subject = "Nevezés törlése";
						$message = "<h1>Kedves ".$row["firstname"]." ".$row["lastname"]."! </h1>";
						$message .= "<p>Nevezésed technikai okok miatt töröltük.</p>";
						$message .= "<p>Üdvözlettel:</br>'.$this->BUSS_NAME.'</br> csapata</p>";
					break;
				}

				$this->sendMail($row["email"],$subject,$message);
			}
		}

		if ($stmt = $this->db->prepare("UPDATE competition_registration SET admin_reg_confirm = '0' WHERE id = ?"))
		{

			$stmt->bind_param("i", $competitionRegID);
			$stmt->execute();
			
		}
		else return -1;

		return 'success_delete';
	}

	public function removeEntry($competitionRegID)
	{

		if ($stmt = $this->db->prepare("SELECT firstname,lastname,admin_reg_confirm,email,lang FROM competition_registration WHERE id = ?"))
		{
			$stmt->bind_param("i", $competitionRegID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			//if($row["admin_reg_confirm"] == "1")
			//{
				switch($row["lang"])
				{
					case "hu":
						$subject = "Nevezés törlése";
						$message = "<h1>Kedves ".$row["firstname"]." ".$row["lastname"]."! </h1>";
						$message .= "<p>Nevezésed töröltük, melynek lehetséges okai a következők:</p>";
						$message .= "<ul>";
						$message .= "<li>Nevezését regisztrációja után 5 nappal sem erősített meg.</li>";
						$message .= "<li>Utalása bankszámlaszámunkra 5 nap elteltével nem érkezett meg.</li>";
						$message .= "<li>A versenyszervezők nem járultak hozzá nevezéséhez.</li>";

						if($row["linked_to"] == "msr")
						{
							$message .= "<li>A Magyar Honvédség nem járult hozzá MH Pápa Bázisrepülőtér területére történő belépéséhez.</li>";
						}
						$message .= "</ul>";
						$message .= "<p>Üdvözlettel:</br>'.$this->BUSS_NAME.'</br> csapata</p>";
					break ;
					case "en":
						$subject = "Nevezés törlése";
						$message = "<h1>Kedves ".$row["firstname"]." ".$row["lastname"]."! </h1>";
						$message .= "<p>Nevezésed technikai okok miatt töröltük.</p>";
						$message .= "<p>Üdvözlettel:</br>'.$this->BUSS_NAME.'</br> csapata</p>";
					break;
				}

				$this->sendMail($row["email"],$subject,$message);
			//}
		}

		if ($stmt = $this->db->prepare("DELETE FROM competition_registration WHERE id = ?"))
		{

			$stmt->bind_param("i", $competitionRegID);
			$stmt->execute();
			
		}
		else return -1;

		return 'success_delete';
	}
	
	
}

?>