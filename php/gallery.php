<?php 
/**
* Gallery Class
*/
class Gallery extends Config
{
	private $db;
	
	function __construct($db)
	{
		$this->db = $db;
		parent::__construct();
	}

	public function getAlbums($linkedTo, $instanceOf, $lang){
		$subdir = $linkedTo == "" ? "main" : $linkedTo;
		$html = "";
		if ($stmt = $this->db->prepare("SELECT uploadmanager_folder.id, uploadmanager_folder.name, uploadmanager_folder.folder_name FROM
										uploadmanager_folder INNER JOIN folder_language
										ON uploadmanager_folder.id = folder_language.folder_id  WHERE linked_to = ? AND 
										instance_of = ? AND lang = ?"))
		{
			$html .= "<div class='gallery-container'>";
			$html .= "<div class='title'>Galéria</div>";
			$stmt->bind_param("sss",$linkedTo, $instanceOf, $lang);
			$stmt->execute();
			$stmt->store_result();
			$result = $stmt->bind_result($id, $name, $folder_name);
			
			if ( $stmt->num_rows > 0 )
			{
				while ($stmt->fetch())
				{
					$html .= "<div class='album-container' albumID=".$id.">";
					$html .= "<div class='icon'>";
					$startPicture = $this->getAlbumStartPicture($id);
					if($startPicture)
					{
						$html .= "<img src='uploads/".$subdir."/albums/".$this->getAlbumFolderName($id)."/".$startPicture."'>";
					}
					$html .= "</div>";
					$html .= "<div class='subtitle' >".($name?$name:$folder_name)."</div>";
					$html .= "</div>";
				}
				$html .= "</div>";
			}
			else $html = "<div class='message'>Nincs album.</div>";
			return $html;
		}
		else return -1;
	}

	private function getAlbumName($albumID)
	{
		if ($stmt = $this->db->prepare("SELECT name FROM uploadmanager_folder WHERE id = ?"))
		{
			$stmt->bind_param("i", $albumID);
			$stmt->execute();
			$stmt->store_result();
			$result = $stmt->bind_result($name);
			$stmt->fetch();
			return $name;
		}
		else return -1;
	}

	private function getAlbumFolderName($albumID)
	{
		if ($stmt = $this->db->prepare("SELECT 	folder_name FROM uploadmanager_folder WHERE id = ?"))
		{
			$stmt->bind_param("i", $albumID);
			$stmt->execute();
			$stmt->store_result();
			$result = $stmt->bind_result($folder_name);
			$stmt->fetch();
			return $folder_name;
		}
		else return -1;
	}

	public function getAlbumVideos($albumID){
		$html = "<img alt='Videó'
					 data-type='html5video'
					 data-image='../img/common/video.png?".time()."'
					 data-videomp4='../uploads/main/albums/".$this->getAlbumFolderName($albumID)."/iskola_film.mp4'
			    	 data-title='Birds'
			    	 data-description='html5 video description'>";
		return $html;

	}


	public function getAlbumPictures($linkedTo, $albumID, $lang="hu"){
		$html = "";
		$subdir = $linkedTo == "" ? "main" : $linkedTo;
		$html .= "<div class='gallery-container'>";
		$html .= "<div class='title'>Galéria - ".$this->getAlbumName($albumID)."</div>";
		$html .= "<div id='gallery' style='display:block;'>";
		//$html .= $this->getAlbumVideos($albumID);
		if ($stmt = $this->db->prepare("SELECT 	uploadmanager_file.id, 
												uploadmanager_file.filename, 
												uploadmanager_file.original_file_name,
												uploadmanager_file.subtitle FROM
												uploadmanager_file INNER JOIN file_language
												ON uploadmanager_file.id = file_language.file_id  
												WHERE 	file_language.lang = ? AND 
														uploadmanager_file.folder_id = ?"
										))
		{
			
			
			
			$stmt->bind_param("si", $lang, $albumID);
			$stmt->execute();
			$stmt->store_result();
			$result = $stmt->bind_result(	
											$id, 
											$filename, 
											$original_file_name,
											$subtitle
										);
			if ( $stmt->num_rows > 0 )
			{
				while ($stmt->fetch())
				{
					$html .= "<img alt='".$subtitle."' src='uploads/".$subdir."/albums/".$this->getAlbumFolderName($albumID)."/thumb_".$filename."' data-image='uploads/".$subdir."/albums/".$this->getAlbumFolderName($albumID)."/".$filename."' data-description='".$subtitle."'>";
				}
				
			}
			
		}
		else return -1;
		$html .= "</div>";
		$html .= "</div>";	
		return $html;
	}

	private function getAlbumStartPicture($albumID)
	{
		if ( $stmt = $this->db->prepare("SELECT filename FROM uploadmanager_file WHERE folder_id=?") )
		{
			$stmt->bind_param("i", $albumID);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($fileName);

			if ($stmt->num_rows > 0) 
			{
				$stmt->fetch();
			}
			else $fileName = "";
		}
		else return -1;
		return $fileName;
	}
}
?>