<?php
/**
*  BasicTimeStateEditor CLASS
		Methodes
			function __construct($db, $tableName, $tableNameLang, $langTableId)
			public function getBasicTimeStateEditorMainId($ID="")
			private function getBasicTimeStateEditorIDFromMainBasicTimeStateEditor($MainID, $lang)
			public function getBasicTimeStateEditor($linkedTo, $ID = "")
			public function getBasicTimeStateEditorComboBox($linkedTo)
			public function deleteBasicTimeStateEditor($ID)
			public function setBasicTimeStateEditor($linkedTo, $ID = "")
			public function addBasicTimeStateEditor($content, $linkedTo)
*/

class BasicTimeStateEditor extends Config
{
	protected $db;
	protected $TableName;
	protected $TableNameLang;	
	protected $LangTableId;

	function __construct($db, $tableName, $tableNameLang, $langTableId)
	{
		$this->db = $db;
		parent::__construct();
		$this->TableName = $tableName;
		$this->TableNameLang = $tableNameLang;	
		$this->LangTableId = $langTableId;
	}

	public function getBasicTimeStateEditorMainId($ID="")
	{
		if(!empty($ID))
		{
			if ($stmt = $this->db->prepare("SELECT main_id from ".$this->TableNameLang." WHERE ".$this->LangTableId." = ?"))
			{
				$stmt->bind_param('i',$ID);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				return $row["main_id"];
			}
			else return -1;
		}
		else if ($stmt = $this->db->prepare("SELECT MAX(main_id) AS main_id from ".$this->TableNameLang.""))
		{
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();

			return $row["main_id"]>0?++$row["main_id"]:1;	
		}
		else return -1;
	}

	private function getBasicTimeStateEditorIDFromMainBasicTimeStateEditor($MainID, $lang)
	{
		if ($stmt = $this->db->prepare("SELECT ".$this->LangTableId." from ".$this->TableNameLang." WHERE main_id = ? AND lang = ?"))
		{
			$stmt->bind_param("is", $MainID, $lang);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			return $row["".$this->LangTableId.""];
		}
		else return -1;
	}

	public function getBasicTimeStateEditor($linkedTo, $ID = "")
	{
		$return_array = array();

		$mainID = !empty($ID) ? $this->getBasicTimeStateEditorMainId($ID) : "";

		foreach ($this->LANGUAGE as $lang => $value) 
		{
			$params = [];
			if ( !empty($ID) ) 
			{
				$idCondition = "id= ? AND";	
				$params[] = "iss";

				$id = $this->getBasicTimeStateEditorIDFromMainBasicTimeStateEditor($mainID, $lang);
				$params[] = &$id;
			}
			else
			{
				$idCondition = "";
				$params[] = "ss";
			}
			$params[] = &$linkedTo;
			$params[] = &$lang;

			if ($stmt = $this->db->prepare("SELECT content FROM ".$this->TableName." WHERE ".$idCondition." linked_to = ? AND lang = ? AND  act = 1"))
			{
				call_user_func_array(array($stmt, 'bind_param'), $params);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				$return_array[$lang] = $row["content"];
			}
			else return -1;
		}
		return $return_array;
	}

	public function getBasicTimeStateEditorComboBox($linkedTo)
	{
		$sql = "SELECT ".$this->TableName.".act, ".$this->TableName.".id, ".$this->TableName.".create_date FROM ".$this->TableName." INNER JOIN ".$this->TableNameLang." ON ".$this->TableName.".id = ".$this->TableNameLang.".".$this->LangTableId." WHERE ".$this->TableName.".linked_to = ? AND ".$this->TableNameLang.".lang = ? ORDER BY  create_date DESC LIMIT 0, 20";

		if ($stmt = $this->db->prepare($sql))
		{
			$html = "<form>";
			$html .= "Másik időpontra állás: <select id='time-combobox'>";
			$html.="<option value='-1'>Válasszon</option>";
			$stmt->bind_param('ss',$linkedTo, $this->LANG_DEFAULT);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_assoc())
			{
				$marked = $row["act"]?"marked":"un-marked"; 
				$selected = $row["act"]?"selected":"";
				$html.="<option class='".$marked."' value=".$row["id"]." ".$selected.">".$row["create_date"]."</option>";
			}
			$html .= "</select>";
			$html .= "</form>";
		}
		else return -1;
		return $html;
	}

	public function deleteBasicTimeStateEditor($ID)
	{
		$MainID = $this->getBasicTimeStateEditorMainId($ID);
		
		if ($stmt = $this->db->prepare("SELECT ".$this->TableName.".id FROM ".$this->TableName." INNER JOIN ".$this->TableNameLang." ON ".$this->TableName.".id = ".$this->TableNameLang.".".$this->LangTableId." WHERE main_id = ?"))
		{
			$stmt->bind_param('i', $MainID);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_assoc())
			{
				if ($stmt = $this->db->prepare("DELETE FROM ".$this->TableName." WHERE id = ?"))
				{
					$stmt->bind_param('i', $row["id"]);
					$stmt->execute();
				}
				else return -1;
			}
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
				if ($stmt = $this->db->prepare("UPDATE ".$this->TableName." SET act = 1 WHERE id = ?"))
				{
					$stmt->bind_param('i', $row["".$this->LangTableId.""]);
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
			if ($stmt = $this->db->prepare("INSERT INTO ".$this->TableName." (content, linked_to, create_date, lang, act) VALUES(?, ?, ?, ?, 1)"))
			{
				$date = date("Y-m-d h:i:s");
				$stmt->bind_param('ssss', $content[$lang], $linkedTo, $date, $lang);
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