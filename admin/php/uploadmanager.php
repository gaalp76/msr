<?php 
/**
* UploadManager Class

	Properties
		$db
	Methodes
				function __construct($db)
		private function getFolderIdFromMainFolder($folderMainId, $lang){
		private function getRootFolderCaption($uploadRootFolderID)
		private function getRootFolderName($rootFolderID)
		private function getFolderName($folderID)
		private function getFolderDBName($folderID)
				function getFolderForm($uploadRootFolderID,$folderID="")
				function checkFolderNameExists($folderName, $linkedTo, $lang)
		private function createFolderName($folderNameInDB)
		private function getRelativeRootPath($linkedTo, $rootFolderName)
		private function getAbsoluteRootPath($linkedTo, $rootFolderName)
				function addDocumentAttachment($fileID, $linkedTo, $instanceOf, $instanceID)
		public 	function updateDocumentAttachment($fileID, $linkedTo, $instanceOf, $instanceID)
		public 	function deleteDocumentAttachment($linkedTo, $instanceOf, $instanceID)
		private function fileIsAttached($fileID, $linkedTo, $instanceOf, $instanceID)
				function getDocumentAttachmentForm($linkedTo, $instanceOf, $instanceID)
				function addFolder($userId, $folderNameDB, $visiblity, $linkedTo, $uploadRootFolderID, $insctanceOf)
				function getFolderData($id)
				function getFileData($id)
		private function delAllFilesFromFolder($PathOfFolder)
				function delFolder($folderArray, $linkedTo, $rootFolderID)
				function modifyFolderData($id, $name, $linkedTo, $visiblity, $uploadRootFolderID)
				function getFolders($showAll=0, $adminTools=0, $linkedTo, $uploadRootFolderID, $instanceOf)
		public 	function getFileMainId($fileId="")
		public 	function getFolderMainId($folderId="")
				function getUploadFileForm($folderId=-1, $selectable=0, $linkedTo, $uploadRootFolderID, $instanceOf)
		private function getFileCover($fileName)
		private function checkMIME($_file)
				function getFolderFiles($folderId, $selectable=0, $folderPath)
		public 	function setFileSubtitle($fileSubtitle, $visiblity, $fileID)
		public 	function getFileSubtitleForm($folderID, $fileID)
				function addFiles($file, $folderId, $userId, $linkedTo, $uploadRootFolderID)
				function delFile($arrayFiles, $linkedTo, $uploadRootFolderID)
				function modifyPictSubscribe($pictId)
				function getFiles($folderId)


*/
class UploadManager extends Config
{
	private $db;
	
	function __construct($db)
	{
		$this->db = $db;
		parent::__construct();
	}


	// ********* A mainId alapján visszaadja az folder Id-ját.

	private function getFolderIdFromMainFolder($folderMainId, $lang){
		if ($stmt = $this->db->prepare("SELECT folder_id from folder_language WHERE main_id = ? AND lang = ?"))
			{
				$stmt->bind_param("is", $folderMainID, $lang);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				return $row["folder_id"];
			}
			else return -1;
	}

	private function getRootFolderCaption($uploadRootFolderID)
	{
		if ( $stmt = $this->db->prepare("SELECT caption FROM upload_folder_root WHERE id=?") )
		{
			$stmt->bind_param("i",$uploadRootFolderID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
		} else return -1;
		return $row["caption"];
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

	private function getFolderName($folderID)
	{
		if ( $stmt = $this->db->prepare("SELECT folder_name FROM uploadmanager_folder WHERE id = ? ") )
		{
			$stmt->bind_param("i", $folderID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();	
		} else return -1;
		return $row["folder_name"];
	}

	private function getFolderDBName($folderID)
	{
		if ( $stmt = $this->db->prepare("SELECT name FROM uploadmanager_folder WHERE id = ? ") )
		{
			$stmt->bind_param("i", $folderID);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();	
		} else return -1;
		return $row["name"];
	}

	function getFolderForm($uploadRootFolderID,$folderID="")
	{
		if ( !($caption = $this->getRootFolderCaption($uploadRootFolderID))) return -1;

		if(!empty($folderID)) $folderMaindID = $this->getFolderMainId($folderID);

		$html = '<div id="add_folder_tab" class="formContainer">
				  <form id="add_folder_frm">
					<input type="hidden" id="method_type" name="method" value="'.($val=empty($folderID)?"add":"update").'">';			  
		$html .= "<ul>";
		foreach ($this->LANGUAGE as $key => $value) {
			$html .= "<li><a href='#".$key."'>".$value."</a></li>";
		}
		$html .= "</ul>";
		
		foreach ($this->LANGUAGE as $key => $value) 
		{
			$visiblity = "checked";
			if(!empty($folderMaindID))
			{
				if ( $stmt = $this->db->prepare("SELECT folder_id FROM folder_language WHERE main_id = ? AND lang = ?") )
				{
					$stmt->bind_param("is",$folderMaindID, $key);
					$stmt->execute();
					$result = $stmt->get_result();
					$row = $result->fetch_assoc();
					$folderID = $row["folder_id"];
					$folderData = $this->getFolderData($folderID);	
					$visiblity = $folderData["visible"]?"checked":"";
				} else return -1;
			}

			$html .= "<div id='".$key."'>";
			$html .= '<fieldset>
				  		<legend>'.($val=empty($folderID)?'Új '.$caption:strtoupper($caption[0]).substr($caption, 1).' módosítása ').'</legend>
				  		<label for="folder_name">'.$caption.' neve</label>
				  		<input type="hidden" name="folder_id['.$key.']" value="'.($val=!empty($folderID)?$folderID:"").'">
						<input type="text" id="folder_name['.$key.']" maxlength="50" name="folder_name['.$key.']" plcaceholder="A(z) '.$caption.' neve" value="'.($val=isset($folderData["name"])?$folderData["name"]:"").'""></input>
						<label for="visiblity_'.$key.'">Látható</label>
						<input type="checkbox" name="visiblity['.$key.']" id="visiblity_'.$key.'" '.$visiblity.'>
						<button id="add_folder_btn">Ok</button>
				  	</fieldset>';
			$html .= '</div>';
		}
				  	
		$html .= '</form></div>';


		return $html;
	}

	function checkFolderNameExists($folderName, $linkedTo, $lang)
	{
		if (!empty($folderName)) 
		{
			if ( $stmt = $this->db->prepare("SELECT * FROM uploadmanager_folder 
											 INNER JOIN folder_language ON folder_language.folder_id = uploadmanager_folder.id
											 WHERE uploadmanager_folder.linked_to=? AND uploadmanager_folder.name=? AND folder_language.lang=?") )
			{

				$stmt->bind_param("sss", $linkedTo, $folderName, $lang);
				$stmt->execute();
				$result = $stmt->get_result();

				return $result->num_rows;
			}
			else return "failed_database";
		}
		else return 0;
	}

	private function createFolderName($folderNameInDB)
	{
		$folderNameInDB = str_replace(' ', '_', $folderNameInDB);
		$specialCharArrayFrom = array("á","é","í","ó","ö","ő","ú","ü","ű","Á","É","Í","Ó","Ö","Ő","Ú","Ü","Ű");
		$specialCharArrayTo = array("a","e","i","o","o","o","u","u","u","a","e","i","o","o","o","u","u","u");
		
		foreach($specialCharArrayFrom as $index=>$value) 
		{
			$folderNameInDB = str_replace(	$specialCharArrayFrom[$index],
											$specialCharArrayTo[$index], 
											$folderNameInDB);
		}
		$folderNameInDB = strtolower($folderNameInDB);
		return preg_replace('/[^a-z0-9\_]/', '', $folderNameInDB);

	}

	private function getRelativeRootPath($linkedTo, $rootFolderName)
	{
		return $this->SITE_ROOT.$this->UPLOAD_FOLDER."/".(empty($linkedTo)?$this->LINKED_TO_DEFAULT:$linkedTo)."/".$rootFolderName."/";
	}

	private function getAbsoluteRootPath($linkedTo, $rootFolderName)
	{
		return $this->ABSOLUTE_UPLOAD_FOLDER."/".($dir=empty($linkedTo)?$this->LINKED_TO_DEFAULT:$linkedTo)."/".$rootFolderName."/";
	}

	function addDocumentAttachment($fileID, $linkedTo, $instanceOf='', $instanceID='')
	{
		
		if (!empty($fileID))
		{
			if(!empty($instanceID))
			{
				foreach ($instanceID as $ikey => $iID) 
				{
					foreach ($fileID as $key => $fID) 
					{
						if ( $stmt = $this->db->prepare("INSERT INTO document_attachment (file_id, linked_to, instance_of, instance_id) VALUES (?,?,?,?)") )
							{

								$stmt->bind_param("issi", $fID, $linkedTo, $instanceOf, $iID) ;
								$stmt->execute();
							}
							else return -1;
					}
				}
			}
			else
			{
				foreach ($fileID as $key => $id) 
				{
					$mainID = $this->getFileMainId($id);
					if ( $stmt = $this->db->prepare("SELECT * FROM file_language WHERE main_id=?") )
					{

						$stmt->bind_param("i", $mainID) ;
						$stmt->execute();
						$result = $stmt->get_result();

						while ( $row = $result->fetch_assoc() )
						{
							if ( $stmt = $this->db->prepare("INSERT INTO document_attachment (file_id, linked_to, instance_of, instance_id) VALUES (?,?,?,?)") )
							{

								$stmt->bind_param("issi", $row["file_id"], $linkedTo, $instanceOf, $instanceID) ;
								$stmt->execute();
							}
							else return -1;
						}

					}
				}
			}
		}
		return "success_document_attachment";
		/*
		if (!empty($fileID))
		{
			foreach ($fileID as $key => $id) 
			{

				//echo $id." ". $linkedTo." ".$instanceOf." ".$instanceID;
				if ( $stmt = $this->db->prepare("INSERT INTO document_attachment (file_id, linked_to, instance_of, instance_id) VALUES (?,?,?,?)") )
				{

					$stmt->bind_param("issi", $id, $linkedTo, $instanceOf, $instanceID) ;
					$stmt->execute();
					echo $stmt->error;
				}
				else return -1;
			}
		}
		return "success_document_attachment";
		*/
	}

	public function updateDocumentAttachment($fileID, $linkedTo, $instanceOf, $instanceID='')
	{
		if ( $stmt = $this->db->prepare("DELETE FROM document_attachment WHERE linked_to=? AND instance_of=? AND instance_id=?") )
		{
			$stmt->bind_param("ssi",$linkedTo, $instanceOf, $instanceID) ;
			$stmt->execute();											
		}
		else return -1;
		return $this -> addDocumentAttachment($fileID, $linkedTo, $instanceOf, $instanceID);
	}

	public function deleteDocumentAttachment($linkedTo, $instanceOf, $instanceID='')
	{
		if ( $stmt = $this->db->prepare("DELETE FROM document_attachment WHERE linked_to=? AND instance_of=? AND instance_id=?") )
		{
			$stmt->bind_param("ssi",$linkedTo, $instanceOf, $instanceID) ;
			$stmt->execute();											
		}
		else return -1;
		return "success_document_attachment_delete";
	}
	private function fileIsAttached($fileID, $linkedTo, $instanceOf, $instanceID)
	{
		if ( $stmt = $this->db->prepare("SELECT instance_id FROM document_attachment WHERE file_id=? AND linked_to=? AND instance_of=? AND instance_id=?") )
		{
			$stmt->bind_param("issi", $fileID, $linkedTo, $instanceOf, $instanceID);
			$stmt->execute();
			$result = $stmt->get_result();
			return ($result->num_rows > 0)?1:0;
		}
		else return -1;
	}

	function getDocumentAttachmentForm($linkedTo, $instanceOf, $instanceID)
	{
		$returnArray = array();
		if ( $stmt = $this->db->prepare("SELECT uploadmanager_folder.id, uploadmanager_folder.name FROM uploadmanager_folder INNER JOIN folder_language ON uploadmanager_folder.id = folder_language.folder_id  WHERE folder_language.lang = ? AND instance_of = 'OfFileHandler' AND linked_to = ? AND visible = 1") )
		{
			$stmt->bind_param("ss", $this->LANG_DEFAULT, $linkedTo);
			$stmt->execute();
			$result = $stmt->get_result();

			if($result->num_rows > 0) 
			{
				$returnArray["content"] = "<form id='documentForm' instance_of='".$instanceOf."' linked_to='".$linkedTo."'>";
				$returnArray["content"] .= "<ul>";
				while($row = $result->fetch_assoc())
				{
					$returnArray["content"] .= "<li class='document-folder'>".$row["name"]."</li>";
					$returnArray["content"] .= "<ul>";
					if ( $fileStmt = $this->db->prepare("SELECT uploadmanager_file.id, uploadmanager_file.filename, uploadmanager_file.subtitle, uploadmanager_file.original_file_name  FROM uploadmanager_file INNER JOIN file_language ON uploadmanager_file.id = file_language.file_id WHERE uploadmanager_file.folder_id = ? AND file_language.lang = ? ORDER BY uploadmanager_file.subtitle, uploadmanager_file.filename "))
					{
						$fileStmt->bind_param("is", $row["id"], $this->LANG_DEFAULT);
						$fileStmt->execute();
						$fileResult = $fileStmt->get_result();

						if($fileResult->num_rows > 0) 
						{
							while($fileRow = $fileResult->fetch_assoc())
							{	
								
								$checked = $this->fileIsAttached($fileRow["id"], $linkedTo, $instanceOf, $instanceID);	

								if ($checked == -1){ $returnArray["message"] = "failed_database"; return  $returnArray; }
								$checked = $checked?"checked":"";
								$fileName = !empty($fileRow["subtitle"])?$fileRow["subtitle"]:'Nincs cím';
								$returnArray["content"] .= "<li><input type='checkbox' ".$checked." file_id='".$fileRow["id"]."' class='document_attachment'><span class='subtitle'>".$fileName."</span>: ".$fileRow["original_file_name"]."</li>"; 
							}
						}
						
					}
					else { $returnArray["message"] = "failed_database"; return  $returnArray; }
					$returnArray["content"] .= "</ul>";
				}
				$returnArray["content"] .= "</ul>";
				$returnArray["content"] .= "</form>";
			}
			else { $returnArray["message"] = "empty_document_handler";  return  $returnArray; }
		}
		else { $returnArray["message"] = "failed_database";  return  $returnArray; }
		return $returnArray;
	}

	function addFolder($userId, $folderNameDB, $visiblity, $linkedTo, $uploadRootFolderID, $insctanceOf)
	{
		if ( !empty($folderNameDB) && !empty($uploadRootFolderID) ) 
		{
			if(!($rootFolderName = $this->getRootFolderName($uploadRootFolderID))) return -1;
			$folderName = $this->createFolderName($folderNameDB[$this->LANG_DEFAULT]);
			$rootPath =  $this->getAbsoluteRootPath($linkedTo, $rootFolderName);

			if (!file_exists($rootPath.$folderName)) 
			{

			    if(!mkdir( $rootPath.$folderName, 0777)) return "failed_create_folder";
			}

			$folderMainId = $this->getFolderMainId();

			foreach ($this->LANGUAGE as $key => $value) 
			{
				$visiblity[$key] = !empty($visiblity[$key])?1:0;

				if ( !$this->checkFolderNameExists($folderNameDB[$key], $linkedTo, $key) ) 
				{

					$sql = "INSERT INTO uploadmanager_folder (user_id, name, linked_to, visible, folder_name, instance_of) VALUES (?,?,?,?,?,?)";

					if ( $stmt = $this->db->prepare($sql) )
					{

						$stmt->bind_param("sssiss",$userId, $folderNameDB[$key], $linkedTo, $visiblity[$key], $folderName, $insctanceOf) ;
						$stmt->execute();

						$id = $stmt->insert_id;

						if($stmt = $this->db->prepare("INSERT INTO folder_language (	
																		main_id, 
																		folder_id, 
																		lang
																	)
															VALUES(?,?,?)"))
						{
							$stmt->bind_param('iis', 	
											$folderMainId,
											$id,
											$key);
							$stmt->execute();
						}
						else
						{
							return "failed_database";
						}
					}
					else return "failed_database";
				}
				else return "failed_create_folder_".$key;
			}
			return "success_folder_insert";
		} 
		return "empty_folder_name";
	}

	function getFolderData($id)
	{
		if ( isset($id) )
		{
			if ( $stmt = $this->db->prepare("SELECT * FROM uploadmanager_folder WHERE id=?") )
			{
				$stmt->bind_param("i",$id);
				$stmt->execute();
				$result = $stmt->get_result();

				if ($result->num_rows > 0) 
				{
					$row = $result->fetch_assoc();

					return $row;
				}
				else return -1;
			}
		}
	}

	function getFileData($id)
	{
		if ( isset($id) )
		{
			if ( $stmt = $this->db->prepare("SELECT * FROM uploadmanager_file WHERE id=?") )
			{
				$stmt->bind_param("i",$id);
				$stmt->execute();
				$result = $stmt->get_result();

				if ($result->num_rows > 0) 
				{
					$row = $result->fetch_assoc();

					return $row;
				}
				else return -1;
			}
		}
	}

	private function delAllFilesFromFolder($PathOfFolder)
	{
		if ( file_exists($PathOfFolder))
		{

			$files = glob($PathOfFolder.'/*');
			foreach($files as $file)
			{ 
				if(is_file($file) && !unlink($file)) return -1;
			}
		}
	}

	function delFolder($folderArray, $linkedTo, $rootFolderID)
	{

		if ( is_array($folderArray) )
		{
			foreach ($folderArray as $key => $value)
			{
				$mainID = $this->getFolderMainId($value);
				$rootFolderName = $this->getRootFolderName($rootFolderID);
				$rootPath = $this->getAbsoluteRootPath($linkedTo, $rootFolderName);
				if(($folderName = $this->getFolderName($value)) == -1) return "failed_database";
				$fullPath = $rootPath.$folderName;

				if($this->delAllFilesFromFolder($fullPath) == -1) return "failed_delete_files"; 
				if (!rmdir($fullPath)) return "failed_delete_folder";
					
				if ( $stmt = $this->db->prepare("SELECT folder_id FROM folder_language WHERE main_id = ?") )
				{
					$stmt->bind_param("i",$mainID);
					$stmt->execute();
					$result = $stmt->get_result();
					while($row = $result->fetch_assoc())
					{
						if ( $stmt = $this->db->prepare("DELETE FROM uploadmanager_folder WHERE id = ?"))
						{
							$stmt->bind_param("i",$row["folder_id"]);
							$stmt->execute();
						} 
						else return "failed_database";
					}	
				} 
				else return "failed_database";
			}

			if ( $stmt = $this->db->prepare("DELETE * FROM uploadmanager_file 
												WHERE folder_id IN "."(".implode(",", $folderArray).")"))
			{
				$stmt->execute();
			}			
		} 
		return "success_delete";
	}

	function modifyFolderData($id, $name, $linkedTo, $visiblity, $uploadRootFolderID)
	{

		foreach ($this->LANGUAGE as $key => $value) 
		{
			if ( isset($id) && !empty($name[$key]) ) 
			{
				$visiblity[$key] = isset($visiblity[$key]) ? 1 : 0;
				if ( $stmt = $this->db->prepare("SELECT * FROM uploadmanager_folder INNER JOIN folder_language ON uploadmanager_folder.id = folder_language.folder_id WHERE name=? AND linked_to=? AND id<>? AND lang=? AND name<>'' ") )
				{
					$stmt->bind_param("ssis",$name[$key], $linkedTo, $id[$key], $key);
					$stmt->execute();
					$result = $stmt->get_result();

					if (!$result->num_rows) 
					{
						$newFolderName = $this->createFolderName($name[$key]);
						if(!empty($newFolderName) && strlen(trim($name[$key])) != 0 )
						{
							if(($oldFolderName = $this->getFolderName($id[$key])) == -1) return "failed_database";
							
							if($key==$this->LANG_DEFAULT && $oldFolderName!=$newFolderName)
							{
								
								$rootFolderPath = $this->getAbsoluteRootPath($linkedTo,$this->getRootFolderName($uploadRootFolderID));
								
								if(file_exists($rootFolderPath.$oldFolderName) && !file_exists($rootFolderPath.$newFolderName))
								{	
									if(!rename($rootFolderPath.$oldFolderName,$rootFolderPath.$newFolderName)) 
									{
									    return 'failed_folder_create';
									}
								} 
								else return 'failed_folder_create';
							}
						
							$sql = "UPDATE uploadmanager_folder SET name=?, visible=?, linked_to=?, folder_name=? WHERE id=?";
							
							if ( $stmt = $this->db->prepare($sql) )
							{
								$stmt->bind_param("sissi",$name[$key], $visiblity[$key], $linkedTo, $newFolderName, $id[$key]) ;
								$stmt->execute();											
							}
							else return "failed_database";
						}
						else return "failed_invalid_folder_name";
					}
					else return "failed_create_folder_".$key;
				}
				else return "failed_database";	
			}
		}
		return "success_folder_modify";
	}

	

	function getFolders($showAll=0, $adminTools=0, $linkedTo, $uploadRootFolderID, $instanceOf)
	{

		$return_str = "";
		$uploadFolder = $this->getRootFolderName($uploadRootFolderID);
		$sql = "SELECT id, name, folder_name, visible FROM uploadmanager_folder INNER JOIN folder_language ON uploadmanager_folder.id = folder_language.folder_id WHERE uploadmanager_folder.linked_to = ? AND folder_language.lang = ? AND uploadmanager_folder.instance_of = ?";

		if ( !$showAll ) $sql .= " AND uploadmanager_folder.visible=1";

		if ( $stmt = $this->db->prepare($sql) )
		{
			$stmt->bind_param("sss", $linkedTo, $this->LANG_DEFAULT, $instanceOf);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $name, $folderName, $visible);

			if ( $stmt->num_rows > 0 )
			{
				$return_str = '<form id="folderListForm">';
	
				$return_str .= '<div id="folder_container">';
				if ($adminTools) 
					$return_str .= '<div id="toggle_all_container"><input type="checkbox" id="toggle_all"><span>Összes kijelöl</span></div>';

				while ( $stmt->fetch() ) 
				{
					if ( $stmt_uploadmanager = $this->db->prepare("SELECT filename FROM uploadmanager_file WHERE folder_id=?") )
					{
						$stmt_uploadmanager->bind_param("i", $id);
						$stmt_uploadmanager->execute();
						$stmt_uploadmanager->store_result();
						$stmt_uploadmanager->bind_result($fileName);

						if ($stmt_uploadmanager->num_rows > 0) 
						{
							$stmt_uploadmanager->fetch();
							$fileName = $this->getRelativeRootPath($linkedTo, $uploadFolder).$folderName."/".$fileName;
						}
						else $fileName = "";
					}
					else return -1;
					$folderPath =  $this->getAbsoluteRootPath(	$linkedTo,
														$this->getRootFolderName($uploadRootFolderID).'/'.$this->getFolderName($id));
					$return_str .=  '<div class="folder-item-container" id="'.$id.'">';
					
					if ($adminTools) {$return_str .= '<input type="checkbox" name="folders[]" class="delete_selector" value="'.$id.'">';}
					$return_str .= '<div class="file-container '.($error = !file_exists($folderPath)?'error-border':'').'">';
					
					$return_str .= 		'<div class="folder-item">'.(!empty($fileName)?'<img src="'.$fileName.'">':'').
									'</div></div>
									
									<div class="folder_name">'.$name.'</div>
								</div>';
				}

				$return_str .= '</div>';
				$return_str .= '</form>';
			}

			return $return_str;
			
		}
		else return -1;
	}

	// ********** a praméterben szereplő file mainId-ját adja vissza, ha nincs paraméter akkor új mainId-vel tér vissza

	public function getFileMainId($fileId="")
	{
		if(!empty($fileId))
		{
			if ($stmt = $this->db->prepare("SELECT main_id from file_language WHERE file_id = ?"))
			{
				$stmt->bind_param('i',$fileId);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				return $row["main_id"];
			}
			else return -1;
		}
		else if ($stmt = $this->db->prepare("SELECT MAX(main_id) AS main_id from file_language"))
		{
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();

			return $row["main_id"]>0?++$row["main_id"]:1;	
		}
		else return -1;
	}

	// ********** a praméterben szereplő folder mainId-ját adja vissza, ha nincs paraméter akkor új mainId-vel tér vissza

	public function getFolderMainId($folderId="")
	{
		if(!empty($folderId))
		{
			if ($stmt = $this->db->prepare("SELECT main_id from folder_language WHERE folder_id = ?"))
			{
				$stmt->bind_param('i',$folderId);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				return $row["main_id"];
			}
			else return -1;
		}
		else if ($stmt = $this->db->prepare("SELECT MAX(main_id) AS main_id from folder_language"))
		{
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();

			return $row["main_id"]>0?++$row["main_id"]:1;	
		}
		else return -1;
	}
															
	function getUploadFileForm($folderId=-1, $selectable=0, $linkedTo, $uploadRootFolderID, $instanceOf)
	{
		$returnArray = array();
		$html = '<select name="folder_selector" id="folder_selector" folder_id="'.$folderId.'">';
		$html .= '<option value="-1">Válasszon</option>';

		if ( $stmt = $this->db->prepare("SELECT uploadmanager_folder.id, uploadmanager_folder.name FROM uploadmanager_folder INNER JOIN folder_language ON uploadmanager_folder.id = folder_language.folder_id WHERE uploadmanager_folder.linked_to=? AND folder_language.lang=? AND uploadmanager_folder.instance_of=?") )
		{
			$stmt->bind_param("sss", $linkedTo, $this->LANG_DEFAULT, $instanceOf);
			$stmt->execute();
			$result = $stmt->get_result();			
			
			if ($result->num_rows > 0) 
			{
				while ( $row = $result->fetch_assoc() )
				{
					if ( $row["id"] == $folderId ) $html .= '<option value="'.$row["id"].'" selected>'.$row["name"].'</option>';
					else $html .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
				}
			}			
			$html .= '</select>';
		}

		if($folderId != -1 )
		{
			if(!($rootFolderName = $this->getRootFolderName($uploadRootFolderID))) return -1;
			if(!($folderName = $this->getFolderName($folderId)) == -1 ) return -1;
			$rootPath =  $this->getRelativeRootPath($linkedTo, $rootFolderName);
			$files = $this->getFolderFiles($folderId, $selectable, $rootPath.$folderName);
			if(empty($files)) $returnArray["message"] = "empty_uploadmanager_folders";
		}
		else $files = '';

		$html .= '<div id="file_cont" class="dropzone">';
		if ($selectable) $html .= '<div id="toggle_all_container"><input type="checkbox" id="toggle_all"><span>Összes kijelöl</span></div>';
		$html .= $files.'</div>';

		$returnArray["content"] = $html;
		return $returnArray;
	}

	private function getFileCover($fileName)
	{
		$ext = pathinfo($fileName, PATHINFO_EXTENSION);
		$isPicture = array('jpg', 'jpeg', 'png' );

		return (isset($this->FILE_COVER[$ext]["icon"]) && !in_array($ext, $isPicture))?$this->FILE_COVER[$ext]["icon"]:$fileName;
	}

	private function checkMIME($_file)
	{
		$fileExt = explode(".", $_file);
		$fileActualExt = strtolower(end($fileExt));
		return (isset($this->FILE_COVER[$fileActualExt]["mime"]) && $this->FILE_COVER[$fileActualExt]["mime"] == mime_content_type($_file) );
	}

	function getFolderFiles($folderId, $selectable=0, $folderPath)
	{

		if ( $stmt = $this->db->prepare("SELECT * FROM uploadmanager_file INNER JOIN file_language ON uploadmanager_file.id = file_language.file_id WHERE folder_id=? AND file_language.lang=?") )
		{
			$stmt->bind_param("is", $folderId, $this->LANG_DEFAULT);
			$stmt->execute();
			$result = $stmt->get_result();
			$html = "";
			
			if ($result->num_rows > 0) 
			{	
				$html .= '<form id="fileListForm">';
				while ( $row = $result->fetch_assoc() )
				{
					$html .= '<div class="dz-preview dz-image-preview">  
								<div class="dz-image">
									<img src="'.substr($this->getFileCover($folderPath."/".$row["filename"]),1).'" alt="'.$row["original_file_name"].'" title="'.$row["original_file_name"].'">
									
								</div>
								<div class="original_file_name">'.$row["original_file_name"].'</div>
								<div class="subtitle">'.(empty($row["subtitle"])?'Nincs cím':$row["subtitle"]).'</div>';
								if(!$selectable)
								{
									$html .= '<div folder_id="'.$folderId.'" file_id="'.$row["id"].'" class="subtitle-modify-form-btn">Átnevez</div>';
								}
					if ($selectable) $html .= '<div class="file_selector_cont"><input type="checkbox" class="file_selector" name="files[]" value="'.$row["id"].'"></div>';
					$html .= '</div>';
				}
				$html .= '</form>';
			}
			return $html;
		}
	}

	public function setFileSubtitle($fileSubtitle, $visiblity, $fileID)
	{
		foreach ($this->LANGUAGE as $key => $value) 
		{
			$visiblity[$key] = isset($visiblity[$key]) ? 1 : 0;

			if ( $stmt = $this->db->prepare("UPDATE uploadmanager_file SET subtitle=?, visible=? WHERE id=? ") )
			{
				$stmt->bind_param("sii",$fileSubtitle[$key], $visiblity[$key], $fileID[$key]);
				$stmt->execute();				
			}
			else return "failed_database";
		}
		return "success_subtitle_modify";
	}

	public function getFileSubtitleForm($folderID, $fileID)
	{
		if(!empty($fileID)) $fileMaindID = $this->getFileMainId($fileID);

		$html = '<div id="fileSubtitleTab" class="formContainer">
				  <form id="fileSubtitleForm">';
		$html .= '<input type="hidden" name="folderId" value="'.($val=!empty($folderID)?$folderID:"").'">';
								  
		$html .= "<ul>";
		foreach ($this->LANGUAGE as $key => $value) {
			$html .= "<li><a href='#".$key."'>".$value."</a></li>";
		}
		$html .= "</ul>";
		
		foreach ($this->LANGUAGE as $key => $value) 
		{
			$visiblity = "checked";
			if(!empty($fileMaindID))
			{
				if ( $stmt = $this->db->prepare("SELECT file_id FROM file_language WHERE main_id = ? AND lang = ?") )
				{
					$stmt->bind_param("is",$fileMaindID, $key);
					$stmt->execute();
					$result = $stmt->get_result();
					$row = $result->fetch_assoc();
					$fileID = $row["file_id"];
					$fileData = $this->getFileData($fileID);	
					$visiblity = $fileData["visible"]?"checked":"";
				} else return -1;
			}

			$html .= "<div id='".$key."'>";
			$html .= '<fieldset>
				  		<legend>Felirat módosítása</legend>
				  		<label for="file_name">Felirat</label>
				  		<input type="hidden" name="file_id['.$key.']" value="'.($val=!empty($fileID)?$fileID:"").'">
						<input type="text" id="file_subtitle['.$key.']" maxlength="50" name="file_subtitle['.$key.']" plcaceholder="Felirat" value="'.($val=isset($fileData["subtitle"])?$fileData["subtitle"]:"").'""></input>
						<label for="visiblity-'.$key.'">Látható</label>
						<input type="checkbox" name="visiblity['.$key.']" id="visiblity-'.$key.'" '.$visiblity.'>
						<button id="subtitle-modify-btn">Módosít</button>
				  	</fieldset>';
			$html .= '</div>';
		}
		$html .= '</form>';
		$html .= '</div>';
		return $html;
	}

	function addFiles($file, $folderId, $userId, $linkedTo, $uploadRootFolderID,$addAttachment = 0)
	{
		if ( is_array($file) && isset($folderId) &&  isset($userId))
		{
			$fileName = $file["name"];
			$fileTmpName = $file["tmp_name"];
			$fileSize = $file["size"];
			$fileExt = explode(".", $fileName);
			$fileError = $file["error"];
			$fileActualExt = strtolower(end($fileExt));
			//$newFilename = isset($_POST["newFilename"]) ? $_POST["newFilename"]: "";

			$isPicture = array('jpg', 'jpeg', 'png' );
			
			if ($fileError === 0) 
			{
				$upload_limit = $this->MAX_FILE_SIZE * 1048576;
				
				if ($fileSize < $upload_limit) 
				{
					if(!($rootFolderName = $this->getRootFolderName($uploadRootFolderID))) return -1;
					if(!($folderName = $this->getFolderName($folderId)) == -1 ) return -1;
					$rootPath =  $this->getAbsoluteRootPath($linkedTo, $rootFolderName);

					$fileNewName = uniqid('',true).".".$fileActualExt;
					$fileDestination = $rootPath.$folderName."/".$fileNewName;

					move_uploaded_file($fileTmpName, $fileDestination);

				/*	if (!$this->checkMIME($fileDestination))
					{
						unlink($fileDestination);
						return "failed_upload_file";

					}
				*/
					if (in_array($fileActualExt, $isPicture) ) 
					{
						list($imgWidth, $imgHeight) = getimagesize($fileDestination);

						// ****** új kép méret
						$difference = $imgHeight / $this->IMAGE_HEIGHT;
						$calculatedWidth = $imgWidth / $difference;

						// ******** új thumbnail méret
						$difference = $imgHeight / $this->THUMB_IMAGE_HEIGHT;
						$calculatedThumbWidth = $imgWidth / $difference;

						$image = imagecreatetruecolor(intval($calculatedWidth), $this->IMAGE_HEIGHT);
						$thumbImage = imagecreatetruecolor($calculatedThumbWidth, $this->THUMB_IMAGE_HEIGHT);

						if ($fileActualExt == "jpg" ||$fileActualExt == "jpeg")  {
							$sourceImage = imagecreatefromjpeg($fileDestination);
							$sourceImageThumb = imagecreatefromjpeg($fileDestination);

						}
						if ($fileActualExt == "png") {

							$sourceImage = imagecreatefrompng($fileDestination);
							$sourceImageThumb = imagecreatefrompng($fileDestination);

							$background1 = imagecolorallocate($image , 0, 0, 0);
					        imagecolortransparent($image, $background1);

					        $background2 = imagecolorallocate($thumbImage , 0, 0, 0);
					        imagecolortransparent($thumbImage, $background2);				

							imagealphablending($image, FALSE);
	        				imagesavealpha($image, TRUE);		

	        				imagealphablending($thumbImage, FALSE);
	        				imagesavealpha($thumbImage, TRUE);				

						}

						if(!unlink($fileDestination)) return "failed_upload_file";

						imagecopyresampled($image,$sourceImage,0,0,0,0,intval($calculatedWidth),$this->IMAGE_HEIGHT,$imgWidth,$imgHeight);
						imagecopyresampled($thumbImage,$sourceImageThumb,0,0,0,0,intval($calculatedThumbWidth),$this->THUMB_IMAGE_HEIGHT,$imgWidth,$imgHeight);

						$pictureName = $fileNewName;
						$pictureThumbName = "thumb_".$fileNewName;

						$fullPath = $rootPath.$folderName."/".$pictureName;
						$thumbFullPath = $rootPath.$folderName."/".$pictureThumbName;

						if ($fileActualExt == "jpg" || $fileActualExt == "jpeg") {
						 	imagejpeg($image, $fullPath);
							imagejpeg($thumbImage, $thumbFullPath);
						} 
						if ($fileActualExt == "png"){
						 	imagepng($image, $fullPath);
							imagepng($thumbImage, $thumbFullPath);
						}
						 
						imagedestroy($image);
						imagedestroy($thumbImage);
						
					}
					
					$mainID = $this->getFileMainId();
					$folderMainID = $this->getFolderMainId($folderId);
					foreach ($this->LANGUAGE as $key => $value) 
					{
						
						if ($stmt = $this->db->prepare("SELECT * FROM folder_language WHERE main_id=? AND lang=?") )
						{
							$stmt->bind_param("is", $folderMainID, $key);
							$stmt->execute();
							$result = $stmt->get_result();
							$row = $result->fetch_assoc();
							$folderIdLang = $row["folder_id"];
						}
						if ($stmt = $this->db->prepare("INSERT INTO uploadmanager_file (filename, original_file_name, folder_id, user_id) VALUES (?,?,?,?)") )
						{
							$stmt->bind_param("ssii", $fileNewName, $fileName, $folderIdLang, $userId);
							$stmt->execute();
							
							$fileID = $stmt->insert_id;

							if ($stmt = $this->db->prepare("INSERT INTO file_language (main_id, file_id, lang) VALUES (?,?,?)") )
							{
								$stmt->bind_param("iis", $mainID, $fileID, $key);
								$stmt->execute();
							}
							else return "failed_database";
						}
						else return "failed_database";
					}
					
				}
			}
			else return "failed_upload_file_ext";
		}
		else return "failed_upload_file";
		return "success_pict_upload";
	}

	function delFile($arrayFiles, $linkedTo, $uploadRootFolderID)
	{

		if ( is_array($arrayFiles) )
		{

			$inStr = "(";
			$inStr .= implode(",", $arrayFiles);
			$inStr .= ")";

			$sql = "SELECT * FROM uploadmanager_file WHERE id IN ".$inStr;

			if ( $stmt = $this->db->prepare($sql) )
			{
				$stmt->execute();
				$result = $stmt->get_result();

				if ( $result->num_rows > 0 )
				{
					while ( $row = $result->fetch_assoc() )
					{
						if(!($rootFolderName = $this->getRootFolderName($uploadRootFolderID))) return -1;
						if(!($folderName = $this->getFolderName($row["folder_id"])) == -1 ) return -1;
						$rootPath =  $this->getAbsoluteRootPath($linkedTo, $rootFolderName);
						
						$filePath = $rootPath.$folderName."/".$row["filename"];
						$filePathThumb = $rootPath.$folderName."/"."thumb_".$row["filename"];

						if (file_exists($filePath))
						{
							if ( !unlink($filePath)) return "file_delete_error";
						}

						if (file_exists($filePathThumb))
						{
							if ( !unlink($filePathThumb)) return "file_delete_error";
						}						
					}
				}
			} else return "failed_database";

			foreach ($arrayFiles as $key => $value) 
			{
				$mainID = $this->getFileMainId($value);
				
				if ( $stmt = $this->db->prepare("SELECT file_id FROM file_language WHERE main_id=?"))
				{	
					$inStr = "";
					$stmt->bind_param("i", $mainID);
					$stmt->execute();
					$result = $stmt->get_result();
					
					while($row = $result->fetch_assoc())
					{		
						$inStr .= $row["file_id"]."," ;
					}

					$inStr = "(".rtrim($inStr, ',').")";
					if ( $stmt = $this->db->prepare("DELETE FROM uploadmanager_file WHERE id IN ".$inStr))
					{
						$stmt->execute();
					}
					else return "failed_database";
				}
				else return "failed_database";
				
			}
			return "success_delete";
		}
	}

	function getDocumentAttachments($linkedTo, $instanceOf = "%", $instanceID = "%", $lang="hu")
	{

		if ( $stmt = $this->db->prepare("
			SELECT	uploadmanager_folder.name,
					uploadmanager_folder.folder_name,
					uploadmanager_file.filename, 
			        uploadmanager_file.original_file_name, 
			        uploadmanager_file.subtitle, 
			        uploadmanager_file.visible 
			FROM uploadmanager_folder 
			INNER JOIN folder_language ON uploadmanager_folder.id = folder_language.folder_id
			INNER JOIN uploadmanager_file ON uploadmanager_folder.id = uploadmanager_file.folder_id 
			INNER JOIN file_language ON uploadmanager_file.id = file_language.file_id 
			INNER JOIN document_attachment ON document_attachment.file_id = uploadmanager_file.id 
			WHERE folder_language.lang = ? AND 
			file_language.lang = ? AND 
			document_attachment.linked_to = ? AND 
			uploadmanager_file.visible = 1 AND 
			document_attachment.instance_of LIKE ? AND 
			document_attachment.instance_id LIKE ? 
			GROUP BY document_attachment.file_id
			ORDER BY uploadmanager_folder.name, uploadmanager_file.subtitle, uploadmanager_file.original_file_name
			") )
		{
			$stmt->bind_param("sssss", $lang, $lang, $linkedTo, $instanceOf, $instanceID);
			$stmt->execute();
			$stmt->store_result();
			$result = $stmt->bind_result(	
											$name,
											$folder_name,
											$filename, 
									        $original_file_name, 
									        $subtitle, 
									        $visible
									    );
			$html = "";
			if($stmt->num_rows > 0) 
			{
				$d_name = "";
				
				$html .= "<div class='document-container'>";
				$html .= "<table class='documents'>";
				while($stmt->fetch())
				{			
					
					$html .= "<tr>";
					if($instanceOf == "%" && $d_name != $name)
					{
						$d_name = $name;
						$html .= "<tr>";
						$html .= "<td class='group-title'>";// colspan='2'>";
						$html .= $name;
						$html .= "</td>";
						$html .= "</tr>";
					}
					/*$html .= "<td>";
					$html .= "<span class='group-item'>".$subtitle."</span>";
					$html .= "</td>";*/
					$html .= "<td>";
					$html .= "<a href='".substr($this->getRelativeRootPath($linkedTo, "docs"),1).$folder_name."/".$filename."'  class='doc-list-item' target='_blank'>".$filename=$subtitle==""?$original_file_name:$subtitle."</a>";
					$html .= "</td>";
					$html .= "</tr>"; 
				}
				$html .= "</table>";
				$html .= "</div>";
			}
		}
		else  return  -1; 
		return $html;
	}

	function modifyPictSubscribe($pictId)
	{

	}


	function getFiles($folderId)
	{

	}


}
?>