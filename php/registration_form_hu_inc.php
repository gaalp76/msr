<?php 
$html  = '<div class="banner-image" style="background-image:url(img/registration-banner-1.jpg)"></div>';

if($isCompetitionRegister)
{
	$html .= '<div class="information" style="width:100%; padding:10px">';
	$html .= 	'<h2>A nevezés lépései</h2>';
	$html .=  	'<ol>';
	$html .= 	'<li>Töltse le és hozza magával a kitöltött <a href="../docs/felelossegi_nyilatkozat.docx">felelősségvállalási nyilatkozatot</a> a versenyre!</li>';
	$html .= 	'<li>Regisztrált felhasználó esetén a lenti űrlap üres mezőit kell kitölteni.<br> Adatváltoztatás lehetséges az email cím kivételével.</li>';
	$html .= 	'<li>Nem regisztrált felhasználó esetén az összes mezőt ki kell tölteni.</li>';
	$html .= 	'<li>Az adatok helyes kitöltése, valamint az adatvédelmi nyilatkozat elfogadása után a nevezés gombra kell kattintani.</li>';
	$html .= 	'<li>A megadott email címre kiküldünk egy levelet, melyben ellenőrizheti adatait, valamint a "Nevezés megerősítése" linkre kattintva véglegesítheti nevezési szándékát.</li>';
	$html .= 	'<li>A következő lépésben az alábbi bankszámlaszámra kell elutalni a nevezési összeget. <b>A regisztrációs emailben kapott kódot kérjük az utalás közleményében feltüntetni szíveskedjen!</b><p><b>Fizetés átutalással, forintban a szervező Honvéd „Ezüst Nyíl” SE bankszámlaszámára<br> Nyugat Takarék Szövetkezet: 59800156-11021412<br>
SWIFT kód: TAKBHUHBXXX </b></p>
</li>';
	$html .= 	'<li>A szervezők a nevezés elfogadásáról a megadott email címen értesítik.</li>';
	$html .= 	'<li>A nevezés elfogadásra került.</li>';
	$html .=  	'</ol>';
	$html .= 	'<h2>A csapat alakítás lépései</h2>';
	$html .=  	'<ol>';
	$html .= 	'<li>Csapatot csak <b>előre regisztrált felhasználó</b> hozhat létre</li>';
	$html .= 	'<li>A csapatkapitány és a csapattagok a fenti lépések szerint neveznek a versenyre.</li>';
	$html .= 	'<li>A csapatkapitány elkéri a csapattagok regisztrációs kódját.</li>';
	$html .= 	'<li>A csapatkapitány a honlapon a nevezések menüpont alatt kitölti a csapatalakítás űrlapját.</li>';
	$html .= 	'<li>A csapattagok emailt kapnak a csapattagi felkérésről.</li>';
	$html .= 	'<li>A csapattagok az emailben a megadott linkre kattintva elfogadhatják a csapattagi felkérést.</li>';
	$html .= 	'<li>Amennyiben minden csapattag megerősíttete részvételi szándékát a csapatban, a csapat megalakult.</li>';
	$html .= 	'<li>A csapattagok 3x5 km-es váltóban indulhatnak.</li>';
	$html .=  	'</ol>';
	$html .=	'</div>';
}

$html .= '<form id="registration-form" name="registration-form" method="post" action="#">';
$html .= 	'<h2>Regisztrációs adatok</h2></br>';
$html .= 	'<input type="hidden" name="competitionID" id="competitionID" value="'.$competitionID.'">';


$html .= 	'<div class="personal-data">';

if(!$isCompetitionRegister)
{
$html .= 		'<label for="reg_username">*Felhasználónév:</label>';
$html .= 		'<input type="text" name="reg_username" id="reg_username" value="'.$username= ($userDataArray!="" ?  $userDataArray["username"] : "").'" '.$disabled= ($userDataArray!="" ? 'readonly="readonly"':'').'>';
$html .= 		'<label for="reg_password">*Jelszó:</label>';
$html .= 		'<input type="password" name="reg_password" id="reg_password" value="">';
$html .= 		'<label for="confirm_reg_password">*Jelszó megerősítése:</label>';
$html .= 		'<input type="password" name="confirm_reg_password" id="confirm_reg_password" value="">';
}

$html .= 		'<label for="lastname">*Vezetéknév:</label>';
$html .= 		'<input type="text" name="lastname" id="lastname" value="'.$lastname= ($userDataArray!="" ?  $userDataArray["lastname"] : "").'">';
$html .= 		'<label for="firstname">*Keresztnév:</label>';
$html .= 		'<input type="text" name="firstname" id="firstname" value="'.$firstname= ($userDataArray!="" ?  $userDataArray["firstname"] : "").'">';

$html .= 		'<label for="mother_name">*Anyja neve:</label>';
$html .= 		'<input type="text" name="mother_name" id="mother_name" value="'.$mother_name= ($userDataArray!="" ?  $userDataArray["mother_name"] : "").'">';

$html .= 		'<label for="born_date">*Születési dátum:</label>';
$html .= 		'<input type="text" name="born_date" id="born_date" value="'.$born_date= ($userDataArray!="" ?  $userDataArray["born_date"] : "").'">';

$html .= 		'<label for="sex">*Neme:</label>';
$html .= 		'<select name="sex" id="sex">';

$html .= 			'<option value="1" '. ($userDataArray!="" && $userDataArray["sex"] == "1" ? "selected" : "").' >Férfi</option>';
$html .= 			'<option value="2" '. ($userDataArray!="" && $userDataArray["sex"] == "2" ? "selected" : "").'>Nő</option>';

$html .= 		'</select>';


if($isCompetitionRegister && $CompetitionType == "military")
{
$html .= 		'<label for="pid">*Személyi igazolvány/útlevél szám:</label>';
$html .= 		'<input type="text" name="pid" id="pid" value="'.$pid= ($userDataArray!="" ?  $userDataArray["id"] : "").'">';
}
if($isCompetitionRegister)
{
	$html .= 		'<label for="er_name">*Baleset esetén értesítendő neve:</label>';
	$html .= 		'<input type="text" name="er_name" id="er_name" >';
	$html .= 		'<label for="er_phone">*Baleset esetén értesítendő telefonszáma:</label>';
	$html .= 		'<input type="text" name="er_phone" id="er_phone" >';

	$html .= 		'<label for="t_shirt">*Technikai póló mérete:</label>';
	$html .= 		'<select name="t_shirt" id="t_shirt">';
	$html .= 			'<option value="0" >Nem kérek</option>';
	$html .= 			'<option value="1" >XS</option>';
	$html .= 			'<option value="2" >S</option>';
	$html .= 			'<option value="3" >M</option>';
	$html .= 			'<option value="4" >L</option>';
	$html .= 			'<option value="5" >XL</option>';
	$html .= 			'<option value="6" >XXL</option>';
	$html .= 			'<option value="7" >3XL</option>';
	$html .= 		'</select>';


	$html .= 	'<label for="comp_dist_1">Versenytáv:</label>';
	$html .= 	$this->getCompetitionDistanceComboBox($competitionID);
}
$html .=	'</div>';

$html .=	'<div class="mailing-data">';
$html .= 		'<label for="email">*Email:</label>';
$html .= 		'<input type="email" name="email" id="email" value="'.$email= ($userDataArray!="" ?  $userDataArray["email"] : "").'" '.$disabled= ($userDataArray!="" ? 'readonly="readonly"':'').'>';
$html .= 		'<label for="phone">*Mobil telefonszám:</label>';
$html .= 		'<input type="text" name="phone" id="phone" value="'.$phone= ($userDataArray!="" ?  $userDataArray["phone"] : "").'">';	
$html .= 		'<label for="country">*Ország:</label>';
$html .= 		'<select name="country" id="country" >';
$html .= 		'<option >Válasszon</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Magyarország"?"selected"   : "").'>Magyarország</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Ausztria"?"selected"   : "").'>Ausztria</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Cseh ország"?"selected"   : "").'>Cseh ország</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Horvátország"?"selected"   : "").'>Horvátország</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Lengyelország"?"selected"   : "").'>Lengyelország</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Románia"?"selected"   : "").'>Románia</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Szerbia"?"selected"   : "").'>Szerbia</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Szlovákia"?"selected"   : "").'>Szlovákia</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Szlovénia"?"selected"   : "").'>Szlovénia</option>';
$html .= 		'<option '.$select= ($userDataArray!="" &&  $userDataArray["country"] == "Ukrajna"?"selected"   : "").'>Ukrajna</option>';
$html .= 		'</select>';

$html .= 		'<label for="mailing-zip">Irányítószám:</label>';
$html .= 		'<input type="text" name="mailing-zip" id="mailing-zip" value="'.$email= ($userDataArray!="" ?  $userDataArray["zip"] : "").'">';
$html .= 		'<label for="mailing-city">Település:</label>';
$html .= 		'<input type="text" name="mailing-city" id="mailing-city" value="'.$email= ($userDataArray!="" ?  $userDataArray["city"] : "").'">';
$html .= 		'<label for="mailing-address">Cím (utca, hsz.):</label>';
$html .= 		'<input type="text" name="mailing-address" id="mailing-address" value="'.$email= ($userDataArray!="" ?  $userDataArray["address"] : "").'">';
$html .= 	'</div>';


if($isCompetitionRegister)
{

	$html .=	'<div class="btn-container">Elfogadom az <a href="../docs/adatvedelmi_szabalyzat.pdf" target="_blank"> adatvédelmi-</a> valamint a <a href="../docs/verseny_szabalyzat.docx">verseny
szabályzatot</a>.<input type="checkbox" name="accept_rules" id="accept_rules"></div>';
	$html .=	'<div class="btn-container">';
	$html .= 		'<button id="sign-up-btn" type="button" class="blue2">Nevezek</a>';
	$html .=	'</div>';
}
else
{
	$html .=	'<div class="btn-container">Elfogadom az <a href="../docs/adatvedelmi_szabalyzat.pdf"  target="_blank">adatvédelmi 
szabályzatot</a>. <input type="checkbox" name="accept_rules" id="accept_rules"></div>';
	$html .=	'<div class="btn-container"><button id="sign-up-btn" type="button" class="blue2">'.$btnName = ($userDataArray!="" ?  "Mentés" : "Elküld").'</a></div>';
}
$html .= '</form>';



return $html;
?>