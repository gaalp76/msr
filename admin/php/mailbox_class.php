<?php 

/**
 * Mailbox
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailboxClass extends Config
{
	private $db;	
	public $competitionID = 0;
	
	function __construct($db, $userLevel = 0)
	{
		$this->db = $db;
		parent::__construct();
	}

	public function getCompetitionComboBox($linkedTo)
	{
		$competitionComboBox = "<select id='competition-combobox' name='competitionID' class='competition-combobox'>";
		$competitionComboBox .= "<option value='0'>Válasszon a versenyek közül</option>";
		if ($stmt = $this->db->prepare("SELECT id,start_date FROM competition WHERE deleted = 0 AND linked_to = ? ORDER BY start_date DESC"))
		{
			$stmt->bind_param("s",$linkedTo);
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc()) 
			{
				$selected = ($this->competitionID == $row["id"])?"selected":"";
				$competitionComboBox .= "<option competition_id='".$row["id"]."' value='".$row["id"]."' $selected>".			
											$row["start_date"].
									"</option>";				
			}
		}
		$competitionComboBox .= '</select>';
		return $competitionComboBox;
	}

	public function getAddresses($competitionID)
	{
		if ($stmt = $this->db->prepare("SELECT id,
											   admin_reg_confirm,
											   firstname,
											   lastname,
											   email,
											   reg_confirm,
											   lang
												
										FROM competition_registration										 
										WHERE competition_id = ? 
							 "))
		{
			$stmt->bind_param("i", $competitionID);
			
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($competitionRegID, $admin_reg_confirm, $firstname, $lastname, $email, $reg_confirm, $lang);

			$html = '<table id="tableAdresses">';
			while($stmt -> fetch())
			{
				if ($reg_confirm) 
				{
					$status = 'regConfirmed';
				}
				if ($admin_reg_confirm) 
				{
					$status = 'adminConfirmed';
				}

				$html .= '<tr class="'.$status.'" lang="'.$lang.'">';
				$html .=    '<td><input type="checkbox" name="compregID[]" value="'.$competitionRegID.'"></td>';
				$html .= 	'<td title="'.$email.'">'.$competitionRegID.'</td>';
				$html .= 	'<td title="'.$email.'">'.$lastname.' '.$firstname.'</td>';
				$html .= '</tr>';

			}
			$html .= '</table>';

			return $html;
		}
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
			//return 0;
		}
	}

	public function sendMultipleEmail($subjectArray, $emailTextArray, $compregIDArray)
	{
		if (is_array($compregIDArray)) $compregIDStr = implode(",", $compregIDArray);
		else $compregIDStr = $compregIDArray;
		
		if ($stmt = $this->db->prepare("SELECT id,
											   firstname,
											   lastname,
											   email,
											   lang
												
										FROM competition_registration
										WHERE id IN (".$compregIDStr.")"
									  )
			)
		{
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($competitionRegID, $firstname, $lastname, $email, $lang);

			while ($stmt->fetch()) 
			{
				if ($this->sendMail($email, $subjectArray[$lang], $emailTextArray[$lang]))
					return 'success_sent_custom_email';
			}			
		}
		
	}
}
 ?>