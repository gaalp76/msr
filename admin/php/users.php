<?php
/**
*  USER CLASS
	   function __construct($db,$userLevel = 0)
public function initMenuArray()
public function check_authority($menu)
public function createFolders($linkedTo)
public function deleteUser()
public function updateUser($lastname,$firstname,$phone,$username,$password,$confirm_password)
public function addUser($lastname,$firstname,$phone,$username,$password,$confirm_password)
public function checkUsername($username,$isEdit=false)
private function userIsAdmin($userID)
private function getUsersComboBox($toID)
private function has_children($name) 
private function getUsersMenuAuthorityList( $parent = "Home")
private function deleteUserAuthority($menuID)
private function addUserAuthority($menuID)
public function saveUserAuthority($menu,$menuID,$authority)
public function getAuthorityUserForm()
public function getUserMenuForContainer($container)
public function getUserMenu()
public function userLogin($userID, $password)
public function logoutUser()
public function getLogoutButton($lang="hu")
public function getUserLoginForm()
public function getUserForm($title, $btnCaption, $dataEdit=false, $dataShow=false, $userComboBox = false)
*/
class User extends Config
{
	private $db;	
	private $menu;
	private $userLevel;
	public $menuArray = array();
	public $userID = 0;

	function __construct($db,$userLevel = 0)
	{
		$this->db = $db;
		$this->userLevel = $userLevel;
		$this->initMenuArray();
		parent::__construct();
	}

	public function initMenuArray()
	{
		if ($stmt = $this->db->prepare("SELECT id, name, container, menu_order, caption, image from menu WHERE user_level>=? AND name NOT LIKE '%users%'  ORDER BY menu_order"))
		{
			$stmt->bind_param("i", $this->userLevel);
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc()) {
				$this->menuArray[] = $row;
			}
		}
		return -1;
	}

	public function check_authority($menu)
	{
		if ($stmt = $this->db->prepare("SELECT * from menu INNER JOIN user_authority_for_menu ON menu.id = user_authority_for_menu.menu_id WHERE user_id = ? AND menu.name = ?"))
		{
			$stmt->bind_param("is", $this->userID, $menu);
			$stmt->execute();
			$stmt->store_result();
			//echo "Numros: ".$stmt->num_rows." menuid: ".$menu;
			return ($stmt->num_rows > 0);
		}
		return -1;
	}

	public function createFolders($linkedTo)
	{
		$linkedTo = !empty($linkedTo)?$linkedTo:$this->LINKED_TO_DEFAULT;
		if(!file_exists($this->ABSOLUTE_UPLOAD_FOLDER."/".$linkedTo))
		{
			if(!mkdir($this->ABSOLUTE_UPLOAD_FOLDER."/".$linkedTo, 0777, true)) return -1;
		}

		if ($stmt = $this->db->prepare("SELECT * FROM upload_folder_root"))
		{
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_assoc())
			{
				if(!file_exists($this->ABSOLUTE_UPLOAD_FOLDER."/".$linkedTo."/".$row["name"]))
				{
					if(!mkdir($this->ABSOLUTE_UPLOAD_FOLDER."/".$linkedTo."/".$row["name"], 0777, true)) return -1;
				}
			}
		} else return -1;
		return 1;
	}

	public function deleteUser()
	{
		if ($stmt = $this->db->prepare("DELETE from user WHERE id = ?"))
		{
			$stmt->bind_param("i", $this->userID);
			$stmt->execute();
			return "success_user_delete";
		}
		return "failed_database";
	}

	public function updateUser($lastname,$firstname,$phone,$username,$password,$confirm_password)
	{
		if($this->checkUsername($username,1) == "true" && $password == $confirm_password)
		{
			$password = crypt($password,$this->SALT);
			if($stmt = $this->db->prepare("	
											UPDATE user SET 
												username = ?,
												password = ?,
												firstname = ?,
												lastname = ?,
												phone = ? 
											WHERE id = ?"
					 ))
			{
				$stmt->bind_param('sssssi', 	
							$username,
							$password,
							$firstname,
							$lastname,
							$phone,
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

	public function addUser($lastname,$firstname,$phone,$username,$password,$confirm_password)
	{
		if($this->checkUsername($username) == "true" && $password == $confirm_password)
		{
			$password = crypt($password,$this->SALT);
			if($stmt = $this->db->prepare("INSERT INTO user (
									 username,
									 password,
									 firstname,
									 lastname,
									 phone
									) 
								VALUES (?,?,?,?,?)"
					 ))
			{
				$stmt->bind_param('sssss', 	
							$username,
							$password,
							$firstname,
							$lastname,
							$phone);
				$stmt->execute();
				return "success_user_add";
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

	public function checkUsername($username,$isEdit=false)
	{
		
		if($isEdit)
		{
			if ($stmt = $this->db->prepare("SELECT id from user WHERE id = ? AND username = ?"))
			{
				$stmt->bind_param("is", $this->userID, $username);
				$stmt->execute();
				$stmt->store_result();
				if($stmt->num_rows > 0) 
				{
					return "true";
				}
			}
		}

		if ($stmt = $this->db->prepare("SELECT username from user WHERE username = ?"))
		{
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$stmt->store_result();
			return ($stmt->num_rows > 0)?"false":"true";
		}
		return -1;
	}

	private function userIsAdmin($userID)
	{
		if ($stmt = $this->db->prepare("SELECT user.id from menu INNER JOIN user_authority_for_menu ON menu.id = user_authority_for_menu.menu_id INNER JOIN user ON user.id = user_authority_for_menu.user_id WHERE user.id = ? AND menu.container LIKE '%users%'"))
		{
			$stmt->bind_param("i", $userID);
			$stmt->execute();
			$result = $stmt->get_result();
			return ($result->num_rows > 0);
		}
		else return -1;
	}

	private function getUsersComboBox($toID)
	{
		$usersComboBox = "<select id='users-combobox-".$toID."' class='users-combobox'>";
		$usersComboBox .= "<option value='0'>Válasszon</option>";
		if ($stmt = $this->db->prepare("SELECT * from user"))
		{
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc()) {
				if (!$this->userIsAdmin($row["id"]))
				{
					$selected = ($this->userID == $row["id"])?"selected":"";
					$usersComboBox .= "<option user_id='".$row["id"]."' value='".$row["id"]."' $selected>".
												$row["lastname"]." ".
												$row["firstname"].
										"</option>";
				}
			}
		}
		$usersComboBox .= '</select>';
		return $usersComboBox;
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

	private function getUsersMenuAuthorityList( $parent = "Home")
	{
		$this->menu .= "<ul class='menu-authority'>";
	   
	    foreach($this->menuArray as $item)
	    {	
	        if ($item["container"] == $parent)
	        {
	        	$checked = $this->check_authority($item['name'])?"checked":"";
	            $this->menu .= "<li menu_id='".$item['id']."'>";
	            $this->menu .= "<input type='checkbox' class='users-authority'  menu_id='".$item['id']."' menu='".$item['name']."' name='authority' ".$checked.">";
	            $this->menu .= $item['caption'];
	            
	            if ($this->has_children($item['name']))
        			$this -> getUsersMenuAuthorityList($item['name']);
      			
      			$this->menu .= "</li>";
	        }
	    }
	    $this->menu .= "</ul>";
	    return $this->menu;
	}

	private function deleteUserAuthority($menuID)
	{
		if ($stmt = $this->db->prepare("DELETE FROM user_authority_for_menu WHERE user_id = ? AND menu_id = ?"))
		{
			$stmt->bind_param("ii", $this->userID, $menuID);
			$stmt->execute();
		}
		else return -1;
	}

	private function addUserAuthority($menuID)
	{
		if ($stmt = $this->db->prepare("SELECT menu_id FROM user_authority_for_menu WHERE user_id = ? AND menu_id = ?"))
		{
			$stmt->bind_param("ii", $this->userID, $menuID);
			$stmt->execute();
			$result = $stmt->get_result();
			$exists = ($result->num_rows > 0);
		}
		else return 0;

		if (!$exists && $stmt = $this->db->prepare("INSERT INTO user_authority_for_menu (user_id, menu_id) VALUES(?,?)"))
		{
			$stmt->bind_param("ii", $this->userID, $menuID);
			$stmt->execute();
		}
		else return -1;
	}

	public function saveUserAuthority($menu,$menuID,$authority)
	{
		
		if($authority == "false")
		{
			$this->deleteUserAuthority($menuID);
			do
			{
				if ($stmt = $this->db->prepare("SELECT id, name from menu WHERE container = ?"))
				{
					$stmt->bind_param("s", $menu);
					$stmt->execute();
					$result = $stmt->get_result();
					$has_parent = ($result->num_rows > 0);

					while($row = $result->fetch_assoc())
					{
						$menu = $row["name"];
						$menuID = $row["id"];
						$this->deleteUserAuthority($menuID);
					}
				}
				else return "failed_database";
				
			} while($has_parent);
			return "success_user_delete_authority";
		}

		if($authority == "true")
		{
			do
			{
				if ($stmt = $this->db->prepare("SELECT id, container from menu WHERE name = ?"))
				{
					$stmt->bind_param("s", $menu);
					$stmt->execute();
					$result = $stmt->get_result();
					$row = $result->fetch_assoc();
					$menu = $row["container"];
					$has_parent = ($result->num_rows > 0);
					$menuID = $row["id"];
					$this->addUserAuthority($menuID);
				}
				else return "failed_database";
			} while($has_parent);

		}
		return "success_user_authority";
	}

	public function getAuthorityUserForm()
	{
		$html =  "<div class='formContainer'>";
		$html .= "<form id='authorityUserForm'>";
		$html .= "<fieldset>";
		$html .= "<legend>Felhasználó jogosultságok</legend>";
		$html .= $this->getUsersComboBox("authority");
		$html .= $this->userID?$this->getUsersMenuAuthorityList():"";
		$html .= "</fieldset>";
		$html .= "</form>";
		$html .= "</div>";
		return $html;
	}

	public function getUserMenuForContainer($container)
	{
		if ($stmt = $this->db->prepare("SELECT menu.* from menu INNER JOIN user_authority_for_menu ON menu.id = user_authority_for_menu.menu_id INNER JOIN user ON user.id = user_authority_for_menu.user_id WHERE user.id = ? AND menu.container = ? ORDER BY menu_order"))
		{
			$html = "";
			$stmt->bind_param("is", $this->userID, $container);
			$stmt->execute();
			$result = $stmt->get_result();							
			$i = 0;
			while ($menu = $result->fetch_assoc())
			{
				if(++$i == 1 && $container != "Home")
				{
					$html .= "<div class='tile home' menu_id='0' menu='Home' style='background-image: url(\"../img/common/home.png\")'>";
					$html .= "<span class='caption'>Kezdőoldal</span>";
					$html .= "</div>";
					$html .= "<div class='tile level-up' menu_id='0' menu='up' style='background-image: url(\"../img/common/level-up.png\")'>";
					$html .= "<span class='caption'>Fel</span>";
					$html .= "</div>";
				}
				$html .= "<div class='tile' menu_id='".$menu["id"]."' upload_root_folder='".$menu["upload_folder_id"]."' menu='".$menu["name"]."' style='background-image: url(\"../img/common/".$menu["image"]."\")'>";
				$html .= "<span class='caption'>".$menu["caption"]."</span>";
				$html .= "</div>";
			}
			return $html;
		}
		else return -1;
	}

	public function getUserMenu()
	{
		if ($stmt = $this->db->prepare("SELECT menu.container from menu INNER JOIN user_authority_for_menu ON menu.id = user_authority_for_menu.menu_id INNER JOIN user ON user.id = user_authority_for_menu.user_id WHERE user.id = ? GROUP BY menu.container"))
		{
			$html = "";
			$stmt->bind_param("s", $this->userID);
			$stmt->execute();
			$result = $stmt->get_result();

			while ($row = $result->fetch_assoc())
			{
					$menu = $this->getUserMenuForContainer($row["container"]);

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

	public function userLogin($userID, $password)
	{
		if ($userID == "" || $password == "") return "username_required";

		$userIdType = !filter_var($userID, FILTER_VALIDATE_EMAIL) ? "username" : "email";
		
		$password = crypt($password,$this->SALT);
		$sql = "SELECT id FROM user WHERE ".$userIdType."=? AND password=?";
		
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
				return "success_login";
			}
			return "failed_login";
		}
		return "failed_database";
	}

	public function logoutUser()
	{
		//$_SESSION = [];
		//session_destroy();
		$_SESSION["logedin"] = 0;
		unset($_SESSION["userID"]);
		return "success_logout";
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
		switch ($lang) {
			case 'hu':
				$html = "<span user_id='".$this->userID."' class='logout'>Kijelentkezés: ".$row["username"]."</span>";
				break;
			case 'en':
				$html = "<span user_id='".$this->userID."' class='logout'>Logout: ".$row["username"]."</span>";
				break;
			case 'de':
				$html = "<span user_id='".$this->userID."' class='logout'>Ausmelden: ".$row["username"]."</span>";
				break;
		}
		
		return $html;
	}

	public function getUserLoginForm()
	{
		$html = "<div class='formContainer'>";
		$html .= "<form id='loginForm'>";
		$html .= "<fieldset>";
		$html .= "<legend>Bejelentkezés</legend>";
		$html .= "<label for='username'>Felhasználónév</label>";
		$html .= "<input type='text' id='username' name='username' placeholder='Felhasználónév..'>";
		$html .= "<label for='password'>Jelszó</label>";
		$html .= "<input type='password' id='password' name='password' placeholder='Jelszó..'>";
		$html .= "</fieldset>";
		$html .= "<button id='loginSubmit' class='button-login'>Bejelentkezés</button>";
		$html .= "</div>";
		return $html;
	}

	public function getUserForm($title, $btnCaption, $dataEdit=false, $dataShow=false, $userComboBox = false)
	{
		if($dataShow)
		{
			if ($stmt = $this->db->prepare("SELECT * from user WHERE id = ?"))
			{
				$stmt->bind_param("i", $this->userID);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
			}
			else return -1;
		}

		$disabled = $dataShow&&!$dataEdit?"disabled":""; 

		$html =  "<div class='formContainer'>";
		$html .= "<form id='userForm'>";
		$html .= "<fieldset>";
		$html .= "<legend>".$title."</legend>";
		$html .= $userComboBox?"<label for='users'>Felhasználók:</label>".$this->getUsersComboBox("data"):"";
		$html .= "<label for='last_name'>Vezetéknév</label>";
		$html .= "<input type='text' id='lastname' name='lastname' ".$disabled." value='".($val=$dataShow?$row["lastname"]:"")."' placeholder='Vezetéknév..'>";
		$html .= "<label for='first_name'>Keresztnév</label>";
		$html .= "<input type='text' id='firstname' name='firstname' ".$disabled." value='".($val=$dataShow?$row["firstname"]:"")."' placeholder='Keresztnév..'>";
		$html .= "<label for='phone'>Telefonszám</label>";
		$html .= "<input type='text' id='phone' name='phone' ".$disabled." value='".($val=$dataShow?$row["phone"]:"")."' placeholder='Telefonszám..'>";
		$html .= "<label for='username'>Felhasználónév</label>";
		$html .= "<input type='text' id='username' name='username' ".$disabled." value='".($val=$dataShow?$row["username"]:"")."' placeholder='Felhasználónév..'>";
		
		if(!$dataShow || $dataEdit)
		{
			$html .= "<label for='password'>Jelszó</label>";
			$html .= "<input type='password' id='password' name='password' placeholder='Jelszó..'>";
			$html .= "<label for='confirm_password'>Jelszó újra</label>";
			$html .= "<input type='password' id='confirm_password' name='confirm_password' placeholder='Jelszó újra..'>";
		}
		$html .= "<input type='submit' value='".$btnCaption."'>";
		$html .= "</fieldset>";
		$html .= "</form>";
		$html .= "</div>";

		return $html;
	}

}

	
?>