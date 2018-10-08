<?php
/**
*  NEWS CLASS
		   function __construct($db)
	public function addNews($title, $author, $start_date, $end_date, $visiblity, $main_visiblity, content, $linked_to)
	public function updateNews($newsID, $newsID, $title, $author, $start_date, $end_date, $visiblity, main_visiblity, 							   $content, $linked_to)
	public function deleteNews($newsID)
	public function getNewsMainID($newsID="")
	public function getNewsSearchResult($title, $author, $start_date, $end_date, $visiblity, $keyword, $linked_to)
	public function getNewsIDFromMainNews($newsMainID, $lang)
	public function getNews($newsID)
	public function getNewsSearchForm($linked_to)
	public function getNewsMetaDataForm($newsID, $linked_to)
*/
class News extends Config
{
	private $db;	

	function __construct($db)
	{
		$this->db = $db;
		parent::__construct();
	}

	public function addNews($title, 
							$author, 
							$start_date, 
							$end_date, 
							$visiblity, 
							$main_visiblity,
							$content, 
							$linked_to)
	{
		$newsID = array();
		if(($newsMainID = $this->getNewsMainID()) == -1) return "failed_database";

		foreach ($this->LANGUAGE as $lang => $value) 
		{
			$visiblity[$lang] = !empty($visiblity[$lang])?1:0;
			$main_visiblity[$lang] = !empty($main_visiblity[$lang])?1:0;
			
			if($stmt = $this->db->prepare("INSERT INTO news (
											 title,
											 author,
											 start_date,
											 end_date,
											 visiblity,
											 main_visiblity,
											 content,
											 linked_to
											) 
											VALUES (?,?,?,?,?,?,?,?)"
						 ))
			{
				$stmt->bind_param('ssssiiss', 	
									$title[$lang],
									$author[$lang],
									$start_date[$lang],
									$end_date[$lang],
									$visiblity[$lang],
									$main_visiblity[$lang],
									$content[$lang],
									$linked_to);
				$stmt->execute();
				$id = $stmt->insert_id;

				//if ($lang == $this->LANG_DEFAULT)
				{
					$newsID[$lang] = $id;
				}

				if($stmt = $this->db->prepare("INSERT INTO news_language (	
																			main_id, 
																			news_id, 
																			lang
																		)
																VALUES(?,?,?)"))
				{
					$stmt->bind_param('iis', 	
									$newsMainID,
									$id,
									$lang);
					$stmt->execute();
				}
				else return -1;		
			}
			else return -1;
		}
		return $newsID;
	}

	public function updateNews( $newsID, 
								$title, 
								$author, 
								$start_date, 
								$end_date, 
								$visiblity,
								$main_visiblity,  
								$content, 
								$linked_to)
	{
		foreach ($this->LANGUAGE as $lang => $value) 
		{
			$visiblity[$lang] = !empty($visiblity[$lang])?1:0;
			$main_visiblity[$lang] = !empty($main_visiblity[$lang])?1:0;

			if($stmt = $this->db->prepare("UPDATE news SET
															title = ?,
															author = ?,
															start_date = ?,
															end_date = ?,
															visiblity = ?,
															main_visiblity = ?,
															content = ?,
															linked_to = ?
											WHERE id = ?"))
			{
				$stmt->bind_param('ssssiissi', 	
									$title[$lang],
									$author[$lang],
									$start_date[$lang],
									$end_date[$lang],
									$visiblity[$lang],
									$main_visiblity[$lang],
									$content[$lang],
									$linked_to,
									$newsID[$lang]);
				$stmt->execute();
				
			}
			else return -1;
		}
		return 1;
	}

	public function deleteNews($newsID)
	{
		$newsMainID = $this->getNewsMainID($newsID);

		if ($newsMainID != -1 && $stmt = $this->db->prepare("SELECT news_id FROM news_language WHERE main_id = ?"))
		{
			$stmt->bind_param('i',$newsMainID);
			$stmt->execute();
			$result = $stmt->get_result();
			
			while($row = $result->fetch_assoc())
			{
				$newsID = $row["news_id"];
				if ($delStmt = $this->db->prepare("DELETE FROM news WHERE id = ?"))
				{
					$delStmt->bind_param('i',$newsID);
					$delStmt->execute();
				}
				else return -1;
			}

			if ($stmt = $this->db->prepare("DELETE FROM  news_language WHERE main_id = ?"))
			{
				$stmt->bind_param('i',$newsMainID);
				$stmt->execute();
				return "success_news_delete";
			}
			else return -1;

		}
		else return "failed_database";
	}

	public function getNewsMainID($newsID="")
	{
		if(!empty($newsID))
		{
			if ($stmt = $this->db->prepare("SELECT main_id from news_language WHERE news_id = ?"))
			{
				$stmt->bind_param('i',$newsID);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				return $row["main_id"];
			}
			else return -1;
		}
		else if ($stmt = $this->db->prepare("SELECT MAX(main_id) AS main_id from news_language"))
		{
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();

			return $row["main_id"]>0?++$row["main_id"]:1;	
		}
		else return -1;
	}

	public function getNewsSearchResult($title, $author, $start_date, $end_date, $visiblity, $keyword, $linked_to, $lang = "hu")
	{
		$html = "";
		$title_ = !empty($title)?"%".$title."%":"%";
		$author_ = !empty($author)?"%".$author."%":"%";
		$keyword_ = !empty($keyword)?"%".$keyword."%":"%";


		if($stmt = $this->db->prepare("SELECT id, start_date, end_date, author, title, IF(LENGTH(?), 
																SUBSTRING(	content,
																	LENGTH(SUBSTRING_INDEX(content,?,1)) - 
																	LOCATE('<p>', 
																			REVERSE(
																						SUBSTRING_INDEX(content,?,1)
																					)
																		  ) - 1,
																	LOCATE('</p>', 
																			content, 
																			POSITION(? IN content) 
																			) - 1
																),
																SUBSTRING(	content,
																			1,
																			LOCATE('</p>', 
																					content, 
																					POSITION(? IN content)
																					) - 1
																			)
															) AS short_content 
																FROM news 
																INNER JOIN news_language
																ON news_language.news_id = news.id 
																WHERE 
																	title LIKE ? AND 
																	author LIKE ? AND 
																	start_date >= ? AND 
																	end_date >= ? AND 
																	content LIKE ? AND 
																	linked_to = ? AND  
																	visiblity = ? AND
																	lang = ?
																ORDER BY start_date DESC 
																LIMIT 0,50"
									)
		)
		{
			$stmt->bind_param('sssssssssssis',
								$keyword,
								$keyword,
								$keyword,
								$keyword,
								$keyword,	
								$title_,
								$author_,
								$start_date,
								$end_date,
								$keyword_,
								$linked_to,
								$visiblity,
								$lang
							);

			$stmt->execute();
			$stmt->store_result();
			$result = $stmt->bind_result($id, $start_date, $end_date, $author, $title, $short_content);
			if($stmt->num_rows)
			{
				$html = "<div id='news-search-list' linked_to = '".$linked_to."' class='news-search-list'>";	
				while ($stmt->fetch())
				{
					$html .= "<div class='news-item' newsid='".$id."'>";
					$html .= "<p class='valid'>Érvényes: ".$start_date." - ". $end_date."</p>";
					$html .= "<h2 class='news-item-header' newsID='".$id."' linked_to='".$linked_to."'>".$title."</h2>";
					$html .= "<p class='content'>".str_ireplace($keyword, "<strong>".$keyword."</strong>",$short_content)."</p>";
					$html .= "<p class='author'>Írta: ".str_ireplace($author, "<strong>".$author."</strong>",$author)."</p>";
					$html .= "</div>";
				}
				$html .= "</div>";
			}
		}
		else
		{
			return -1;
		}
		return $html;	
	}

	public function getNewsIDFromMainNews($newsMainID, $lang)
	{
		if ($stmt = $this->db->prepare("SELECT news_id from news_language WHERE main_id = ? AND lang = ?"))
			{
				$stmt->bind_param("is", $newsMainID, $lang);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				return $row["news_id"];
			}
			else return -1;
	}

	public function getNews($newsID)
	{
		$return_array = array();
		$newsMainID = $this->getNewsMainID($newsID);
		foreach ($this->LANGUAGE as $lang => $value) 
		{
			if(!empty($newsMainID))
			{
				if (($newsID = $this->getNewsIDFromMainNews($newsMainID, $lang))!= -1 && 
					$stmt = $this->db->prepare("SELECT content from news WHERE id = ?"))
				{
					$stmt->bind_param("i", $newsID);
					$stmt->execute();
					$result = $stmt->get_result();
					$row = $result->fetch_assoc();
					$return_array[$lang] = $row["content"];
				}
				else return -1;
			}
		}
		return $return_array;
	}

	public function getNewsSearchIcon()
	{
		$html = "<div class='news-search-icon'></div>";
		return $html;
	}

	public function getNewsView($newsID)
	{
		$html = "";
		if($stmt = $this->db->prepare("SELECT title,start_date,content,author
												FROM news 
												WHERE 
													news.id = ?" 
									)
		)
		{
			$stmt->bind_param('i',
								$newsID
							);

			$stmt->execute();
			$stmt->store_result();
			$result = $stmt->bind_result($title,$start_date,$content,$author);
			$stmt->fetch();

			$html .= "<div class='news-container'>";
			$html .= "<div class='title'>".$title."</div>";
			$html .= "<div class='date'>".$start_date."</div>";
			$html .= "<div class='content'>".$content."</div>";
			$html .= "<div class='author'>".$author."</div>";
			
			$html .= "</div>";
		}
		return $html;
	}

	public function getNewsSearchForm($linked_to, $isCommon = 0)
	{
		$html = "<div class='icon-container'>";
		$html .= "<div class='news-search-icon'></div>";
		$html .= "</div>";
		$html .= "<div class='formContainer'>";
		$html .= "<form id='newsSearchForm' method='post'>";
		$html .= "<fieldset>";
		$html .= "<legend>Hírek keresése</legend>";
		

		if(!$isCommon)
		{
			$html .= "<div style='text-align:right'>";
			$html .= "<label for='visiblity'>Láthatóság</label>";
			$html .= "<input type='checkbox' name='visiblity' id='visiblity' checked>";
			$html .= "</div>";
		}

		$html .= "<label for='title'>Cím: </label>";
		$html .= "<input type='text' id='title' name='title' placeholder='Cím..'>";
		$html .= "<label for='author'>Író: </label>";
		$html .= "<input type='text' id='author' name='author' placeholder='Író..'>";
		$html .= "<label for='start_date'>Megjelenés kezdő dátuma: </label>";
		$html .= "<input type='text' id='start_date' name='start_date' placeholder='Kezdő dátum..'>";
		$html .= "<label for='end_date'>Megjelenés vég dátuma: </label>";
		$html .= "<input type='text' id='end_date' name='end_date' placeholder='Vég dátum..'>";
		$html .= "<label for='title'>Kulcsszó: </label>";
		$html .= "<input type='text' id='keyword' name='keyword' placeholder='Kulcsszó..'>";
		$html .= "<input class='news-search-btn' type='submit' value='Mehet'>";	
		$html .= "</fieldset>";
		$html .= "</div>";
		return $html;
	}

	public function getNewsMetaDataForm($newsID, $linked_to)
	{
		$newsMainID = !empty($newsID)?$this->getNewsMainID($newsID):"";
		$main_visiblity_checked = "checked";
		$visiblity_checked = "checked";
		$addNews = empty($newsID)?1:0;

		$html = "<div id='newsMetaTabs' class='formContainer'>";
		
		$html .= "<ul>";
		foreach ($this->LANGUAGE as $key => $value) {
			$html .= "<li><a href='#".$key."'>".$value."</a></li>";
		}
		$html .= "</ul>";
		
		$html .= "<form id='newsMetaForm'>";
		$html .= "<input type='hidden' name='addNews' value='".$addNews."'>";
		
		foreach ($this->LANGUAGE as $key => $value) 
		{
			if(!$addNews)
			{
				if (($newsID = $this->getNewsIDFromMainNews($newsMainID, $key)) && 
					$stmt = $this->db->prepare("SELECT id, start_date, end_date, author, title, visiblity, main_visiblity from news WHERE id = ?"))
				{
					$stmt->bind_param("i", $newsID);
					$stmt->execute();
					$result = $stmt->get_result();
					$row = $result->fetch_assoc();
					$main_visiblity_checked = $row["main_visiblity"]?"checked":"";
					$visiblity_checked = $row["visiblity"]?"checked":"";
				}
				else return -1;
			}
			
			$html .= "<div id='".$key."'>";
			$html .= "<fieldset>";
			$html .= "<legend>Hírek metaadatai</legend>";
			$html .= "<input type='hidden' name='newsid[".$key."]' id='news-item' value='".($val=$newsID?$newsID:"")."'>";

			if(!empty($linked_to))
			{
				$html .= "<div class='check-box'>";
				$html .= "<label for='main_visiblity-".$key."'>Főoldalon jelenjen meg</label>";
				$html .= "<input type='checkbox' name='main_visiblity[".$key."]' id='main_visiblity-".$key."' ".$main_visiblity_checked.">";
				$html .= "</div>";
			}
			else
			{
				$html .= "<input type='hidden' name='main_visiblity[".$key."]' id='main_visiblity-".$key."' value='1'>";
			}

			$html .= "<div class='check-box'>";
			$html .= "<label for='visiblity-".$key."'>Láthatóság</label>";
			$html .= "<input type='checkbox' name='visiblity[".$key."]' id='visiblity-".$key."' ".$visiblity_checked.">";
			$html .= "</div>";

			$html .= "<label for='title[".$key."]'>Cím: </label>";
			$html .= "<input type='text' class='title' name='title[".$key."]' value='".($val=$newsID?$row["title"]:"")."' placeholder='Cím..'>";
			$html .= "<label for='author[".$key."]'>Író: </label>";
			$html .= "<input type='text' id='author' name='author[".$key."]' value='".($val=$newsID?$row["author"]:"")."' placeholder='Író..'>";
			$html .= "<label for='start_date[".$key."]'>Megjelenés kezdő dátuma: </label>";
			$html .= "<input type='text' class='start_date' name='start_date[".$key."]' value='".($val=$newsID?$row["start_date"]:"")."' placeholder='Kezdő dátum..'>";
			$html .= "<label for='end_date[".$key."]'>Megjelenés vég dátuma: </label>";
			$html .= "<input type='text' class='end_date' name='end_date[".$key."]' value='".($val=$newsID?$row["end_date"]:"")."' placeholder='Vég dátum..'>";		
			$html .= "</fieldset>";
			$html .= "</div>";
		}
		
		$html .= "</form>";
		$html .= "</div>";
		return $html;
	}
}

?>