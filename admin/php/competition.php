<?php
/**
*  COLLEAGUE COMPETITION
*/
class Competition extends Config
{
	private $db;	
	private $menu;
	public $competitionID = 0;

	function __construct($db)
	{
		$this->db = $db;
		parent::__construct();
	}

	public function getCompetitionModeComboBox($linkedTo)
	{
		$competitionModeComboBox = "<select id='competition-mode-combobox' name='competition-mode' class='competition-mode-combobox'>";
		$competitionModeComboBox .= "<option  value='1' >Egyéni</option>";
		$competitionModeComboBox .= "<option  value='2' >Csapat</option>";
		$competitionModeComboBox .= "</select>";
		return $competitionModeComboBox;
	}

	public function getOpenCompetitions($lang)
	{
		if ($stmt = $this->db->prepare("SELECT id,start_date, reg_start_date, reg_end_date, linked_to FROM competition WHERE deleted = 0 AND reg_start_date <= '".date("Y-m-d")."' AND reg_end_date >= '".date("Y-m-d")."' ORDER BY start_date DESC"))
		{

			$stmt->execute();
			$result = $stmt->get_result();
			$html = "";
			while ($row = $result->fetch_assoc()) 
			{
				$html = "";
				switch ($lang)
				{
					case "hu":
						switch ($row["linked_to"])
						{
							case "vulcanrun":
								$html .= "<p><b>VulcanRun-terepfutó verseny nevezése megnyílt</b><br>";	
							break;
							case "vulcanobstacle":
								$html .= "<p><b>VulcanRun-akadályfutó verseny nevezése megnyílt</b><br>";
							break;
							case "msr":
								$html .= "<p><b>Military Survival Run - Military verseny nevezése megnyílt</b><br>";
							break;
							case "sr":
								$html .= "<p><b>Military Survival Run - Open verseny nevezése megnyílt</b><br>";
							break;
						}

						$start_date = date_create($row["start_date"]);
						$reg_start_date = date_create($row["reg_start_date"]);
						$reg_end_date = date_create($row["reg_end_date"]);

						$html .= "A verseny időpontja: ".date_format($start_date,"Y.m.d H:i")."<br>";
						$html .= "A nevezési időszak: ".date_format($reg_start_date,"Y.m.d")." - ".date_format($reg_end_date,"Y.m.d")."<br>";
						$html .= "<span class='info-entry' linkedto='".$row["linked_to"]."'>Nevezek</span></p>";
					break;
					/* en de */
				}
				
			}
			return $html; 
		}
		return -1;
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

	public function addCompetition(
									$start_date,
									$start_hour,
									$start_minute,
									$reg_start_date,
									$reg_end_date,
									$max_reg_number,
									$reg_type,
                                                                        $teamate_number,
									$comp_dist_1,
									$comp_dist_2,
									$comp_dist_3,
									$linkedTo
								)
	{

		if ($stmt = $this->db->prepare("INSERT INTO competition (
																	start_date,
																	reg_start_date,
																	reg_end_date,
																	max_reg_number,
																	reg_type,
																	linked_to,
																	teamate_number,
																	comp_dist_1,
																	comp_dist_2,
																	comp_dist_3

																)
										VALUES 
										(?,?,?,?,?,?,?,?,?,?)
										"))
		{
			$start_date = $start_date." ".$start_hour."-".$start_minute;
			$stmt->bind_param("sssissiiii",
										$start_date,
										$reg_start_date,
										$reg_end_date,
										$max_reg_number,
										$reg_type,
										$linkedTo,
										$teamate_number,
										$comp_dist_1,
										$comp_dist_2,
										$comp_dist_3
							);
			$stmt->execute();
			if ($stmt->affected_rows > 0)
			{
				return "success_save";
			}
			
		}
		else return "failed_database";
	}

	public function deleteCompetition()
	{
		
		if ($stmt = $this->db->prepare("UPDATE competition SET
																	deleted = 1					
										WHERE id = ?
										"))
		{
			
			$stmt->bind_param("i",$this-> competitionID
							);
			$stmt->execute();
			return "success_delete";
		}
		else return "failed_database";
	}
	public function updateCompetition(
									$start_date,
									$start_hour,
									$start_minute,
									$reg_start_date,
									$reg_end_date,
									$max_reg_number,
									$reg_type,
									$teamate_number,
									$comp_dist_1,
									$comp_dist_2,
									$comp_dist_3
									)
	{
		
		if ($stmt = $this->db->prepare("UPDATE competition SET
																	start_date = ?,
																	reg_start_date = ?,
																	reg_end_date = ?,
																	max_reg_number = ?,
																	reg_type = ?					
										WHERE id = ?
										"))
		{
			$start_date = $start_date." ".$start_hour."-".$start_minute;
			$stmt->bind_param("sssisiiiii",
										$start_date,
										$reg_start_date,
										$reg_end_date,
										$max_reg_number,
										$reg_type,
										$teamate_number,
										$comp_dist_1,
										$comp_dist_2,
										$comp_dist_3,
										$this-> competitionID
							);
			$stmt->execute();
			return "success_save";
		}
		else return "failed_database";
	}

	public function getRegistrationTypeComboBox($registrationType, $disabled)
	{
		$html = "<select id='reg_type' name='reg_type'".$disabled.">";
		if ($stmt = $this->db->prepare("SELECT * FROM competition_registration_type"))
		{
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc() ) 
			{
				$selected = $registrationType==$row["reg_type"]?"selected":"";
				$html .= "<option value='".$row["reg_type"]."' ".$selected.">".$row["name"]."</option>";
			}
		}
		else return -1;
		$html .= "</select>";
		return $html;
	}

	public function getCompetitionForm($title, $linkedTo, $dataEdit=false, $dataShow=false, $competitionComboBox = false) 
	{
		if($dataShow)
		{
			if ($stmt = $this->db->prepare("SELECT * FROM competition WHERE id = ? AND deleted = 0"))
			{
				$stmt->bind_param("i", $this->competitionID);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
			}
			else return -1;
			
			$start_date = $this->competitionID?date("Y-m-d",strtotime($row["start_date"])):"";
			$start_hour = $this->competitionID?date("H",strtotime($row["start_date"])):"";
			$start_minute = $this->competitionID?date("i",strtotime($row["start_date"])):"";
		}

		$disabled = $dataShow&&!$dataEdit?"disabled":""; 

		$html =  "<div class='formContainer'>";
		$html .= "<form id='competitionForm' name='competitionForm' method='post'>";
		$html .= "<fieldset>";
		$html .= "<legend>".$title."</legend>";
		$html .= $competitionComboBox?"<label for='competition'>Versenyek:</label>".$this->getCompetitionComboBox($linkedTo):"";
		
		$html .= "<label for='start_date'>Verseny napja</label>";
		$html .= "<input type='text' id='start_date' name='start_date' ".$disabled." value='".($val=$dataShow?$start_date:"")."' placeholder='Verseny napja..'>";
		
		$html .= "<label for='start_hour'>Verseny kezdete (óra)</label>";
		$html .= "<input type='text' id='start_hour' name='start_hour' ".$disabled." value='".($val=$dataShow?$start_hour:"")."' placeholder='Verseny kezdete (óra)..'>";

		$html .= "<label for='start_minute'>Verseny kezdete (perc)</label>";
		$html .= "<input type='text' id='start_minute' name='start_minute' ".$disabled." value='".($val=$dataShow?$start_minute:"")."' placeholder='Verseny kezdete (perc)..'>";

		$html .= "<label for='reg_start_date'>Regsztrációs időszak kezdete</label>";
		$html .= "<input type='text' id='reg_start_date' name='reg_start_date' ".$disabled." value='".($val=$dataShow?$row["reg_start_date"]:"")."' placeholder='Regsztrációs időszak kezdete..'>";
		$html .= "<label for='reg_start_date'>Regsztrációs időszak vége</label>";
		$html .= "<input type='text' id='reg_end_date' name='reg_end_date' ".$disabled." value='".($val=$dataShow?$row["reg_end_date"]:"")."' placeholder='Regsztrációs időszak vége..'>";
		$html .= "<label for='max_reg_number'>Nevezés limit</label>";
		$html .= "<input type='text' id='max_reg_number' name='max_reg_number' ".$disabled." value='".($val=$dataShow?$row["max_reg_number"]:"")."' placeholder='Nevezés limit..'>";
                $html .= "<label for='teamate_number'>Csapat létrehozása (fő)</label>";
		$html .= "<input type='text' id='teamate_number' name='teamate_number' ".$disabled." value='".($val=$dataShow?$row["teamate_number"]:"")."' placeholder='Csapat létrehozása..'>";
		$html .= "<label for='req_type'>Verseny típusa</label>";
		$html .= $this->getRegistrationTypeComboBox($dataShow?$row["reg_type"]:"", $disabled);
                $html .= "<fieldset>";
    	$html .= 	"<legend>Versenytávok</legend>";
		$html .= "<label for='comp_dist_1'>5+ km</label>";
		$html .= "<input type='checkbox' id='comp_dist_1' name='comp_dist_1' value='1'  ".$disabled."  ".(($dataShow && $row["comp_dist_1"]=="1")?"checked":"")."/>";
		$html .= "<label for='comp_dist_2'>10+ km</label>";
		$html .= "<input type='checkbox' id='comp_dist_2' name='comp_dist_2' value='1'  ".$disabled."  ".(($dataShow && $row["comp_dist_2"]=="1")?"checked":"")."/>";
		$html .= "<label for='comp_dist_3'>15+ km</label>";
		$html .= "<input type='checkbox' id='comp_dist_3' name='comp_dist_3' value='1'  ".$disabled."  ".(($dataShow && $row["comp_dist_3"]=="1")?"checked":"")."/>";
		$html .= "</fieldset>";
                $html .= "</fieldset>";
		$html .= "</form>";
		$html .= "</div>";

		return $html;
	}

}

	
?>