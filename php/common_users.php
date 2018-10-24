<?php
/*
***************************************
*  CUOMMON USERS CLASS
***************************************
function __construct($db,$userLevel)
public function getUserMenuForContainer($container, $lang='hu')
public function getUserMenu($lang="hu")
public function getUserData($userID)
public function getUserLoginForm($lang="hu")
public function getUserRegisterForm($lang="hu", $userDataArray="")
public function sendMailForgotPassword($username, $lang="hu")
public function sendMailRegistration($username, $hashStr, $lang="hu" )
public function confirmRegistration($username, $hash, $lang="hu" )
public function getForgotPasswordForm($username, $hash, $lang="hu")
public function changePassword($userID, $newPassword, $hash)
public function addUser($lastname,
private function sendMail($address, $subject, $message)
public function getLogoutButton($lang="hu")
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CommonUser extends User
{

	function __construct($db,$userLevel)
	{
		$this->db = $db;
		$this->userLevel = $userLevel;
		//$this->userID = $_SESSION["userID"];
		
		parent::__construct($db, $userLevel);

	}

	public function commonUserLogin($userID, $password)
	{
		if ($userID == "" || $password == "") return "username_required";

		$userIdType = !filter_var($userID, FILTER_VALIDATE_EMAIL) ? "username" : "email";
		
		$password = crypt($password,$this->SALT);
		$sql = "SELECT id FROM user WHERE ".$userIdType."=? AND password=? AND reg_confirm = '1'";
		
		if($stmt = $this->db->prepare($sql))
		{
			$stmt->bind_param('ss', $userID, $password);

			$stmt->execute();
			$result = $stmt->get_result();
			if ($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();
				$_SESSION["userID"] = $this->userID = $row["id"];
				$_SESSION["logedin"] = 1;
				return "success_front_login";
			}
			return "failed_login";
		}
		return "failed_database";
	}
	public function getUserMenuForContainer($container, $lang='hu')
	{
	//	if ($stmt = $this->db->prepare("SELECT menu.* FROM menu WHERE  menu.container = ? AND user_level >= ? ORDER BY menu_order"))
	if ($stmt = $this->db->prepare("SELECT menu_language.name AS caption, menu.id, menu.upload_folder_id, menu.name FROM menu 
									LEFT JOIN menu_language ON menu_language.menu_id=menu.id 
									WHERE menu.container = ? AND  user_level >= ?  AND menu_language.lang=? 
									ORDER BY frontend_menu_order") )
		{
			$html = "";
			$stmt->bind_param("sis", $container, $this->userLevel, $lang);
			$stmt->execute();
			$result = $stmt->get_result();							
			$i = 0;
			$fileName = "menu_cont_".$lang."_inc.php";
			while ($menu = $result->fetch_assoc())
			{
				include($fileName);
			}
			return $html;
		}
		else return -1;
	}

	public function getUserMenu($lang="hu")
	{
		if ($stmt = $this->db->prepare("SELECT menu.container from menu  WHERE user_level >= ? GROUP BY menu.container"))
		
		{
			$html = "";
			$stmt->bind_param("i", $this->userLevel);
			$stmt->execute();
			$result = $stmt->get_result();

			while ($row = $result->fetch_assoc())
			{
					$menu = $this->getUserMenuForContainer($row["container"], $lang);

					if(!$menu) return 0;
			 		
			 		$html .= "<div class='menu-container' menu='".$row["container"]."'>";
			 		$html .= "<div class='tile-container' >";
			 		$html .= $menu;
			 		$html .= "</div>";
			 		$html .= "</div>";
			 	
			}
			return $html;
		}
		else return -1;
	}

	public function updateCommonUser(
										$lastname,
										$firstname,
										$phone,
										$password,
										$confirm_password,
										$pid,
										$country,
										$zip,
										$city,
										$address,
										$mother_name,
										$born_date,
										$sex,
										$email
									)
	{
		if($password == $confirm_password)
		{
			$password = crypt($password,$this->SALT);
			if($stmt = $this->db->prepare("	
											UPDATE user SET 
												password = ?,
												firstname = ?,
												lastname = ?,
												phone = ? ,
												pid = ?,
												country = ?,
												zip = ?,
												city = ?,
												address = ?,
												mother_name = ?,
												born_date = ?,
												sex = ?,
												email = ?
											WHERE id = ?"
					 ))
			{
				$stmt->bind_param('sssssssssssisi', 	
							$password,
							$firstname,
							$lastname,
							$phone,
							$pid,
							$country,
							$zip,
							$city,
							$address,
							$mother_name,
							$born_date,
							$sex,
							$email,
							$this->userID);
				$stmt->execute();
				return "success_user_update";
			}
			else
			{
				return "failed_database";
			}
		}
		else
		{
			return "failed_add_user";
		}	
	}

	public function getUserData($userID)
	{
		if ($stmt = $this->db->prepare("SELECT * from user  WHERE id = ?"))
		{
			$html = "";
			$stmt->bind_param("i", $userID);
			$stmt->execute();
			$result = $stmt->get_result();

			$row = $result->fetch_assoc();
			return $row;
		}
	}	

	public function getUserLoginForm($lang="hu")
	{
		
		$fileName = "login_cont_".$lang."_inc.php";
		include($fileName);
		return $html;
	}

	public function getCompetitionDistanceComboBox($competitionID)
	{
		$html = "<select id='comp_dist' name='comp_dist'>";
		if ($stmt = $this->db->prepare("SELECT * FROM competition WHERE id = ? AND deleted = '0'"))
		{
			$stmt->bind_param("i", $competitionID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			if ($row["comp_dist_1"]=="1")
			{
				$html .= "<option value='1'>5+ km</option>";
			}
			if ($row["comp_dist_2"]=="1")
			{
				$html .= "<option value='2'>10+ km</option>";
			}
			if ($row["comp_dist_3"]=="1")
			{
				$html .= "<option value='3'>15+ km</option>";
			}
		}
		else return -1;
		$html .= "</select>";
		return $html;
	}
	public function getUserRegisterForm($lang="hu", $userDataArray="", $isCompetitionRegister="", $CompetitionType="", $competitionID="")
	{
		
		$fileName = "registration_form_".$lang."_inc.php";
		include($fileName);	
		return $html;
	}

	public function sendMailForgotPassword($username, $lang="hu")
	{
		if ($username == "") return "warning_username_required";
		if ($lang == "") return "language_required";

		if ($stmt = $this->db->prepare("SELECT concat(firstname, ' ',lastname ) as name, username, email FROM user WHERE username = ?"))
		{
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();

			if($result->num_rows) 
			{
				include("forgot_password_mail_".$lang."_inc.php");
				if ($this->sendMail($row["email"], $subject, $message) ) 
				{
					if ($stmt = $this->db->prepare("INSERT INTO validator_code (username, create_date, expiration_date, hash) VALUES (?,?,?,?)"))
					{
						$expirationDate = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . ' +3 day'));
						$createDate = date('Y-m-d H:i:s');
						$stmt->bind_param("ssss", $username, $createDate, $expirationDate, $hashStr);
						$stmt->execute(); 
						
						if ($stmt->sqlstate == "00000") return "success_forgotpassword";
						else return $stmt->sqlstate;
					}
					
				}
			}
			else return "warning_username_not_found";
			
		}
		else return -1;
	}

	public function sendMailRegistration($username, $hashStr, $lang="hu" )
	{
		if ($username == "") return "warning_username_required";
		if ($lang == "") return "language_required";

		if ($stmt = $this->db->prepare("SELECT concat(firstname, ' ',lastname ) as name, username, email FROM user WHERE username = ?"))
		{
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();

			if($result->num_rows) 
			{
				include("registration_mail_".$lang."_inc.php");
				if ($this->sendMail($row["email"], $subject, $message) ) return "reg_mail_sent";
				
			}
			else return "warning_username_not_found";
			
		}
		else return -1;
	}

	public function confirmRegistration($username, $hash, $lang="hu" )
	{
		$sql = "UPDATE user AS u LEFT JOIN validator_code AS v ON u.username=v.username SET u.reg_confirm = 1 WHERE v.username = ? AND v.hash=? AND v.expiration_date >= NOW()";

		if ($stmt = $this->db->prepare($sql))
		{
			$stmt->bind_param("ss", $username, $hash);
			$stmt->execute();

			if ($stmt->affected_rows > 0) return "success_reg_confirm";
			else return "warning_invalid_hash";
		}
		else return -1;

	}

	public function getForgotPasswordForm($username, $hash, $lang="hu")
	{
		
		$fileName = "forgot_password_form_".$lang."_inc.php";
		include($fileName);
		return $html;
	}

	public function changePassword($userID, $newPassword, $hash)
	{
		if ($userID == "" || $newPassword == "") return 0;
		
		if ($stmt = $this->db->prepare("SELECT * FROM validator_code  
										WHERE username = ? AND 
										hash = ? AND 
										expiration_date >= NOW()"))
		{
			$stmt->bind_param("ss", $userID, $hash);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if ($result->num_rows) 
			{
				$userIdType = !filter_var($userID, FILTER_VALIDATE_EMAIL) ? "username" : "email";

				$newPassword = crypt($newPassword,$this->SALT);
				$sql = "UPDATE user SET password = ? WHERE ".$userIdType." = ?";

				if ($stmt = $this->db->prepare($sql))
				{
					$stmt->bind_param("ss", $newPassword, $userID);
					$stmt->execute();

					if ($stmt->affected_rows > 0)
					{ 
						return "success_password_change";
					}

				}
				else return -1;
			}
			else return "warning_invalid_hash";
		} 
		else return -1;
		
	}

	public function checkEmail($email)
	{
		
		if ($stmt = $this->db->prepare("SELECT email FROM user WHERE email = ?"))
		{
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$stmt->store_result();
			return ($stmt->num_rows > 0)?"false":"true";
		}
		return -1;
	}

	public function checkEmailInCompetititon($email, $competitionID)
	{
		
		if ($stmt = $this->db->prepare("SELECT email FROM competition_registration WHERE email = ? AND competition_id = ?"))
		{
			$stmt->bind_param("si", $email,$competitionID);
			$stmt->execute();
			$stmt->store_result();
			return ($stmt->num_rows > 0)?"false":"true";
		}
		return -1;
	}

	public function addCommonUser(	
									$lastname,
									$firstname,
									$phone,
									$username,
									$password,
									$confirm_password,
									$email,
									$pid,
									$country,
									$zip,
									$city,
									$address,
									$mother_name,
									$born_date,
									$sex
								)
	{
		if($this->checkUsername($username) == "true" && $password == $confirm_password)
		{
			$password = crypt($password,$this->SALT);
			if($stmt = $this->db->prepare("INSERT INTO user (
									 username,
									 password,
									 firstname,
									 lastname,
									 phone,
									 email,
									 pid,
									 country,
									 zip,
									 city,
									 address,
									 mother_name,
									 born_date,
									 sex
									) 
								VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
					 ))
			{
				$stmt->bind_param('sssssssssssssi', 	
							$username,
							$password,
							$firstname,
							$lastname,
							$phone,
							$email,
							$pid,
							$country,
							$zip,
							$city,
							$address,
							$mother_name,
							$born_date,
							$sex
						);
				$stmt->execute();
					if ($stmt->affected_rows > 0)
					{
						if ($stmt = $this->db->prepare("INSERT INTO validator_code (username, create_date, expiration_date, hash) VALUES (?,?,?,?)"))
						{
							$this->clearValidatorCode();
							$hashStr = htmlspecialchars(uniqid('',true));
							$create_date = date('Y-m-d H:i:s');
							$expiration_date = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . ' +3 day'));
							$stmt->bind_param("ssss", $username, $create_date , $expiration_date, $hashStr);
							
							$stmt->execute();
							if ($stmt->affected_rows > 0)
							{
								if ($this->sendMailRegistration($_GET["reg_username"], $hashStr, $_SESSION["lang"]) == "reg_mail_sent")
								{
									return "success_reg_email_send";
								}
								else
								{
									return "failed_mail";
								}
							} 
						} 
						else return "failed_add_hash";
						
					} 
					else return "failed_add_user";
			}
			else return "failed_database";
		}
		else return "failed_add_user";
	}

	private function clearValidatorCode()
	{
		echo "ok";
		if ($stmt = $this->db->prepare("DELETE FROM validator_code WHERE expiration_date<NOW()"))
		{
			$stmt->execute();
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
			echo 'failed_mail'.$e;
			return 0;
		}
	}

	public function getLogoutButton($lang="hu")
	{
		if ($stmt = $this->db->prepare("SELECT username from user WHERE id = ?"))
		{
			$stmt->bind_param("i", $this->userID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
		}
		else return -1;
		$html = "<div id='logout-container'>";

		switch ($lang) {
			case 'hu':
				$html .= "<span user_id='".$this->userID."' class='logout'>Kijelentkezés: ".$row["username"]."</span>";
				break;
			case 'en':
				$html .= "<span user_id='".$this->userID."' class='logout'>Logout: ".$row["username"]."</span>";
				break;
			case 'de':
				$html .= "<span user_id='".$this->userID."' class='logout'>Ausmelden: ".$row["username"]."</span>";
				break;
		}
		$html .= "<span id='user-settings'><img src='img/tooth-wheel.png'></span>";
		$html .= "</div>";
		
		return $html;
	}
}

	
?>
