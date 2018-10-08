<?php 
$html  = '<h1>Registration form</h1>';
$html .= '<form id="registration-form" name="registration-form" method="post" action="#">';
$html .= 	'<input type="hidden" name="competitionID" id="competitionID" value="'.$competitionID.'">';
$html .= 	'<div class="personal-data">';
if(!$isCompetitionRegister)
{
$html .= 		'<label for="reg_username">*Username:</label>';
$html .= 		'<input type="text" name="reg_username" id="reg_username" value="'.$username= ($userDataArray!="" ?  $userDataArray["username"] : "").'" '.$disabled = ($userDataArray!="" ? 'readonly="readonly"':'').'>';
$html .= 		'<label for="reg_password">*Password:</label>';
$html .= 		'<input type="password" name="reg_password" id="reg_password" value="">';
$html .= 		'<label for="confirm_reg_password">*Confirm password:</label>';
$html .= 		'<input type="password" name="confirm_reg_password" id="confirm_reg_password" value="">';
}
$html .= 		'<label for="lastname">*Last name:</label>';
$html .= 		'<input type="text" name="lastname" id="lastname" value="'.$lastname= ($userDataArray!="" ?  $userDataArray["lastname"] : "").'">';
$html .= 		'<label for="firstname">*First name:</label>';
$html .= 		'<input type="text" name="firstname" id="firstname" value="'.$firstname= ($userDataArray!="" ?  $userDataArray["firstname"] : "").'">';
if($isCompetitionRegister && $CompetitionType == "military")
{
$html .= 		'<label for="pid">*Personal identify/passport:</label>';
$html .= 		'<input type="text" name="pid" id="pid" value="'.$pid= ($userDataArray!="" ?  $userDataArray["id"] : "").'">';
}
if($isCompetitionRegister)
{

	$html .= 	'<label for="comp_dist_1">Competition distance:</label>';
	$html .= 	$this->getCompetitionDistanceComboBox($competitionID);
}
$html .=	'</div>';

$html .=	'<div class="mailing-data">';
$html .= 		'<label for="email">*Email:</label>';
$html .= 		'<input type="email" name="email" id="email" value="'.$email= ($userDataArray!="" ?  $userDataArray["email"] : "").'" '.$disabled= ($userDataArray!="" ? 'readonly="readonly"':'').'>';
$html .= 		'<label for="phone">*Mobil:</label>';
$html .= 		'<input type="text" name="phone" id="phone" value="'.$phone= ($userDataArray!="" ?  $userDataArray["phone"] : "").'">';		
$html .= 		'<label for="country">*Country:</label>';
$html .= 		'<select name="country" id="country" >';
$html .= 		'<option >Válasszon</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Hungary"?"selected"   : "").'>Hungary</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Austria"?"selected"   : "").'>Austria</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Croatia"?"selected"   : "").'>Croatia</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Czech"?"selected"   : "").'>Czech</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Slovakia"?"selected"   : "").'>Slovakia</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Slovenia"?"selected"   : "").'>Slovenia</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Serbia"?"selected"   : "").'>Serbia</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Polland"?"selected"   : "").'>Polland</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Romania"?"selected"   : "").'>Romania</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Ukraine"?"selected"   : "").'>Ukraine</option>';
$html .= 		'</select>';
$html .= 		'<label for="mailing-city">City:</label>';
$html .= 		'<input type="text" name="mailing-city" id="mailing-city" value="'.$email= ($userDataArray!="" ?  $userDataArray["city"] : "").'">';
$html .= 		'<label for="mailing-address">Address (street, Nr.):</label>';
$html .= 		'<input type="text" name="mailing-address" id="mailing-address" value="'.$email= ($userDataArray!="" ?  $userDataArray["address"] : "").'">';
$html .= 		'<label for="mailing-zip">ZIP:</label>';
$html .= 		'<input type="text" name="mailing-zip" id="mailing-zip" value="'.$email= ($userDataArray!="" ?  $userDataArray["zip"] : "").'">';
$html .= 	'</div>';

if($isCompetitionRegister)
{
$html .=	'<div class="btn-container"><button id="sign-up-btn" type="button" class="blue1">Register</a></div>';
}
else
{
$html .=	'<div class="btn-container"><button id="sign-up-btn" type="button" class="blue1">'.$btnName = ($userDataArray!="" ?  "Save" : "Send").'</a></div>';
}
$html .= '</form>';
?>