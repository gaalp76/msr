<?php
/**
*  COLLEAGUE CLASS
*/
class Colleague extends Config
{
	private $db;	
	private $menu;
	public $colleagueID = 0;

	function __construct($db)
	{
		$this->db = $db;
		parent::__construct();
	}

	
	public function deleteColleague()
	{
		if ($stmt = $this->db->prepare("DELETE from colleague WHERE id = ?"))
		{
			$stmt->bind_param("i", $this->colleagueID);
			$stmt->execute();
			return "success_colleague_delete";
		}
		return "failed_database";
	}

	public function updateColleague($lastname,$firstname,$phone,$email,$scope,$photoID)
	{	
		if($stmt = $this->db->prepare("	
										UPDATE colleague SET 
											firstname = ?,
											lastname = ?,
											phone = ?,
											email = ?,
											scope = ?,
											photo_id = ?
										WHERE id = ?"
				 ))
		{
			$stmt->bind_param('sssssii', 	
						$firstname,
						$lastname,
						$phone,
						$email,
						$scope,
						$photoID,
						$this->colleagueID);
			$stmt->execute();
			return "success_colleague_update";
		}
		else
		{
			return "failed_database";
		}
	}

	public function addColleague($lastname,$firstname,$phone,$email,$scope,$photoID)
	{
		if($stmt = $this->db->prepare("INSERT INTO colleague (
								 firstname,
								 lastname,
								 phone,
								 email,
								 scope,
								 photo_id
								) 
							VALUES (?,?,?,?,?,?)"
				 ))
		{
			$stmt->bind_param('sssssi', 	
						$firstname,
						$lastname,
						$phone,
						$email,
						$scope,
						$photoID);
			$stmt->execute();
			return "success_colleague_add";
		}
		else
		{
			return "failed_database";
		}
	}

	private function getColleagueComboBox($toID)
	{
		$colleagueComboBox = "<select id='colleague-combobox-".$toID."' class='colleague-combobox'>";
		$colleagueComboBox .= "<option value='0'>Válasszon</option>";
		if ($stmt = $this->db->prepare("SELECT *, CONCAT(lastname,firstname) AS name from colleague ORDER BY name"))
		{
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc()) 
			{
				$selected = ($this->colleagueID == $row["id"])?"selected":"";
				$colleagueComboBox .= "<option colleague_id='".$row["id"]."' value='".$row["id"]."' $selected>".
											$row["lastname"]." ".
											$row["firstname"].
									"</option>";				
			}
		}
		$colleagueComboBox .= '</select>';
		return $colleagueComboBox;
	}

	private function has_children($name) 
	{
	  foreach ($this->menuArray as $item) 
	  {
	    if ($item['container'] == $name)
	      return true;
	  }
	  return false;
	}

	private function getRelativeRootPath($linkedTo, $rootFolderName)
	{
		return $this->SITE_ROOT.$this->UPLOAD_FOLDER."/".(empty($linkedTo)?$this->LINKED_TO_DEFAULT:$linkedTo)."/".$rootFolderName."/";
	}

	private function getAbsoluteRootPath($linkedTo, $rootFolderName)
	{
		return $this->ABSOLUTE_UPLOAD_FOLDER."/".($dir=empty($linkedTo)?$this->LINKED_TO_DEFAULT:$linkedTo)."/".$rootFolderName."/";
	}

	private function getRootFolderName($rootFolderID)
	{
		if ( $stmt = $this->db->prepare("SELECT name FROM upload_folder_root WHERE id = ? ") )
		{
			$stmt->bind_param("i", $rootFolderID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();	
		} else return -1;
		return $row["name"];
	}

	public function getPhotoComboBox($linkedTo, $uploadRootFolderID, $disabled, $photoID)
	{
		$uploadFolder = $this->getRootFolderName($uploadRootFolderID);

		$sql = "SELECT id, name, folder_name, visible FROM uploadmanager_folder INNER JOIN folder_language ON uploadmanager_folder.id = folder_language.folder_id WHERE uploadmanager_folder.linked_to = ? AND folder_language.lang = ? AND instance_of = 'OfColleagueGallery' AND uploadmanager_folder.visible = 1";

		if ( $stmt = $this->db->prepare($sql) )
		{
			$stmt->bind_param("ss", $linkedTo, $this->LANG_DEFAULT);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $name, $folderName, $visible);

			$html = "<select id='photo-selector' $disabled>";
			$html .= "<option value='-1'>Válasszon</option>";
			while ( $stmt->fetch() ) 
			{
				if ( $stmt_photo = $this->db->prepare("SELECT uploadmanager_file.id, uploadmanager_file.filename, uploadmanager_file.original_file_name, uploadmanager_file.subtitle FROM uploadmanager_file INNER JOIN file_language ON uploadmanager_file.id = file_language.file_id WHERE uploadmanager_file.folder_id=? AND file_language.lang=?") )
				{
					$stmt_photo->bind_param("is", $id, $this->LANG_DEFAULT);
					$stmt_photo->execute();
					$stmt_photo->store_result();
					$stmt_photo->bind_result($fileID, $fileName, $originalFileName, $subtitle);
					if ($stmt_photo->num_rows > 0) 
					{
						while($stmt_photo->fetch())
						{
							$listitem = !empty($subtitle)?$subtitle:$originalFileName;
							$selected = ($photoID == $fileID)?"selected":"";
							$html .= "<option photo_url='".$this->getRelativeRootPath($linkedTo, $uploadFolder).$folderName."/".$fileName."' photo_id='".$fileID."' value='".$fileID."' ".$selected.">".$listitem."</option>";
						}
					}
					
				}
			}
			$html .= "</select>";
		}
		else return -1;
		return $html;
	}

	private function getPhotoURL($searchedFileID, $linkedTo, $uploadRootFolderID, $showAll = 1)
	{

		$uploadFolder = $this->getRootFolderName($uploadRootFolderID);

		$sql = "SELECT id, name, folder_name, visible FROM uploadmanager_folder INNER JOIN folder_language ON uploadmanager_folder.id = folder_language.folder_id WHERE uploadmanager_folder.linked_to = ? AND folder_language.lang = ? AND instance_of = 'OfColleagueGallery'";
		if ( !$showAll ) $sql .= " AND uploadmanager_folder.visible=1";

		if ( $stmt = $this->db->prepare($sql) )
		{
			$stmt->bind_param("ss", $linkedTo, $this->LANG_DEFAULT);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $name, $folderName, $visible);
			
			while ( $stmt->fetch() ) 
			{
				if ( $stmt_photo = $this->db->prepare("SELECT id, filename, original_file_name, subtitle FROM uploadmanager_file WHERE folder_id=?") )
				{
					$stmt_photo->bind_param("i", $id);
					$stmt_photo->execute();
					$stmt_photo->store_result();
					$stmt_photo->bind_result($fileID, $fileName, $originalFileName, $subtitle);
					if ($stmt_photo->num_rows > 0) 
					{
						while ($stmt_photo->fetch())
						{
							if ($fileID == $searchedFileID) return $this->getRelativeRootPath($linkedTo, $uploadFolder).$folderName."/".$fileName;
						}
					}
					
				}
			}
		}
		else return -1;
		return "";
	}

	public function getColleagueForm($title, $btnCaption, $uploadRootFolderID, $linkedTo, $dataEdit=false, $dataShow=false, $colleagueComboBox = false) 
	{
		if($dataShow)
		{
			if ($stmt = $this->db->prepare("SELECT * from colleague WHERE id = ?"))
			{
				$stmt->bind_param("i", $this->colleagueID);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
			}
			else return -1;
		}

		$disabled = $dataShow&&!$dataEdit?"disabled":""; 

		$html =  "<div class='formContainer'>";
		$html .= "<form id='colleagueForm'>";
		$html .= "<fieldset>";
		$html .= "<legend>".$title."</legend>";
		$html .= $colleagueComboBox?"<label for='colleague'>Munkatársak:</label>".$this->getColleagueComboBox("data"):"";
		$html .= "<label for='first_name'>Fénykép</label>";
		$html .= $this->getPhotoComboBox($linkedTo, $uploadRootFolderID, $disabled, isset($row["photo_id"])?$row["photo_id"]:"");
		$html .= "<div class='flex-container'>";
		$html .= "<div>";
		$html .= "<label for='last_name'>Vezetéknév</label>";
		$html .= "<input type='text' id='lastname' name='lastname' ".$disabled." value='".($val=$dataShow?$row["lastname"]:"")."' placeholder='Vezetéknév..'>";
		$html .= "<label for='first_name'>Keresztnév</label>";
		$html .= "<input type='text' id='firstname' name='firstname' ".$disabled." value='".($val=$dataShow?$row["firstname"]:"")."' placeholder='Keresztnév..'>";
		$html .= "</div>";

		$html .= "<div class='photo-container'><img class='photo' src='".($url=$dataShow?$this->getPhotoURL($row["photo_id"],$linkedTo, $uploadRootFolderID):"")."'/>";

		$html .= "</div>";
		$html .= "</div>";
		
		$html .= "<label for='scope'>Munkakör</label>";
		$html .= "<input type='text' id='scope' name='scope' ".$disabled." value='".($val=$dataShow?$row["scope"]:"")."' placeholder='Munkakör..'>";
		$html .= "<label for='phone'>Telefonszám</label>";
		$html .= "<input type='text' id='phone' name='phone' ".$disabled." value='".($val=$dataShow?$row["phone"]:"")."' placeholder='Telefonszám..'>";
		$html .= "<label for='email'>Email cím</label>";
		$html .= "<input type='text' id='email' name='email' ".$disabled." value='".($val=$dataShow?$row["email"]:"")."' placeholder='Email cím..'>";
		
		$html .= "<input type='submit' value='".$btnCaption."'>";
		$html .= "</fieldset>";
		$html .= "</form>";
		$html .= "</div>";

		return $html;
	}

}

	
?>