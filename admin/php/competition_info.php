<?php
/**
*  COMPETITION INFO CLASS
*/
class CompetitionInfo extends BasicTimeStateEditor
{
	public function getCompetitionID($linkedTo)
	{
		if ($stmt = $this->db->prepare("SELECT linked_id FROM competition_map WHERE linked_to = ? AND act = '1'"))
		{
			$stmt->bind_param('i', $linkedTo);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			return $row["linked_id"];
		}
		else return -1;
			
	}

	public function setBasicTimeStateEditor($linkedTo, $ID = "", $linkedID = "")
	{
		$MainID = $this->getBasicTimeStateEditorMainId($ID);

		if ($stmt = $this->db->prepare("UPDATE ".$this->TableName." SET act = 0 WHERE linked_to = ?"))
		{
			$stmt->bind_param('s', $linkedTo);
			$stmt->execute();
		}
		else return -1;

		if ($stmt = $this->db->prepare("SELECT * FROM ".$this->TableNameLang." WHERE main_id = ?"))
		{
			$stmt->bind_param('i', $MainID);
			$stmt->execute();
			$result = $stmt->get_result();
			
			while($row = $result->fetch_assoc())
			{
				if ($stmt = $this->db->prepare("UPDATE ".$this->TableName." SET act = 1, linked_id = ? WHERE id = ?"))
				{
					$stmt->bind_param('ii', $linkedID, $row["".$this->LangTableId.""]);
					$stmt->execute();
				}
				else return -1;
			}
		}
		else return -1;
		return $this -> getBasicTimeStateEditorComboBox($linkedTo);
	}

	public function addBasicTimeStateEditor($content, $linkedTo = "", $linkedID = "")
	{	
		$MainID = $this->getBasicTimeStateEditorMainId();
		
		if ($stmt = $this->db->prepare("UPDATE ".$this->TableName." SET act = 0 WHERE linked_to = ?"))
		{
			$stmt->bind_param('s', $linkedTo);
			$stmt->execute();
		}
		else return -1;

		foreach ($this->LANGUAGE as $lang => $value) 
		{
			if ($stmt = $this->db->prepare("INSERT INTO ".$this->TableName." (content, linked_to, create_date, lang, act, linked_id) VALUES(?, ?, ?, ?, 1,?)"))
			{
				$date = date("Y-m-d h:i:s");
				$stmt->bind_param('ssssi', $content[$lang], $linkedTo, $date, $lang, $linkedID);
				$stmt->execute();
				$id = $stmt->insert_id;
				if($stmt = $this->db->prepare("INSERT INTO ".$this->TableNameLang." (	
																main_id, 
																".$this->LangTableId.", 
																lang
															)
													VALUES(?,?,?)"))
				{
					$stmt->bind_param('iis', 	
									$MainID,
									$id,
									$lang);
					$stmt->execute();
					if($lang == $this->LANG_DEFAULT) $html =  $this->getBasicTimeStateEditorComboBox($linkedTo);
				}
				else return -1;		
			}
			else return -1;				
		}
		return $html;
		
	}
}

?>