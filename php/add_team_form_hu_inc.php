<?php
	$html  =	'<div class="btn-container">';

	$competitionRegID = $competition_entry->getCompetitionRegID($_SESSION["userID"], $competitionID);
	if ( $teamID = $competition_entry->regUserIsMember($competitionRegID, $competitionID) ) // 0, -1, -2, *
	{
		$html .=	'<div style="width:100%; overflow:auto;"><button style="float:left" id="show-add-team-form-btn" type="button" class="disabled" disabled>Csapat létrehozás</button>';
		$html .=	'<button style="float:left; margin-left:10px" id="delete-team-btn" type="button" class="blue1">Csapat töröl</button></div>';
		
		$invitedTeamatesArray = $competition_entry->getIntoTeamInvitedUsers($teamID, $competitionID);

		if (is_array($invitedTeamatesArray))
		{
			foreach (array_keys($invitedTeamatesArray, $competitionRegID) as $key) 
			{
			    unset($invitedTeamatesArray[$key]);
			    $invitedTeamatesArray = array_values($invitedTeamatesArray);
			}
		}
	}
	else
	{
		$teamID = -1;
		$html .=	'<div style="width:100%; overflow:auto;"><button style="float:left" id="show-add-team-form-btn" type="button" class="blue1">Csapat/váltó létrehozás</button>';
		$html .=	'<button style="float:left; margin-left:10px" id="delete-team-btn" type="button" class="disabled" disabled>Csapat/váltó töröl</button></div>';
	}

	$html .=	'</div>';

	$html .=	'<div id="add-team-rules">';
	$html .= 	'<h2>A csapat/váltó alakítás lépései</h2>';
	$html .=  	'<ol>';
	$html .=    	'<li>Csapatot/váltót csak azután lehet létrehozni, miután egyéni nevezésüket jóváhagytuk.</li>';
	$html .=    	'<li>Csapatot/váltót csak a honlapon <b>regisztrált felhasználó</b> hozhat létre (jobb felső sarok)</li>';
	$html .=    	'<li>A csapatkapitány és a csapattagok/váltótagok a fenti lépések szerint neveznek a versenyre.</li>';
	$html .=    	'<li>A csapatkapitány elkéri a csapattagok/váltótagok regisztrációs kódját.</li>';
	$html .=    	'<li>A csapatkapitány a honlapon a nevezések menüpont alatt kitölti a csapat/váltó alakítás űrlapját.</li>';
	$html .=    	'<li>A csapattagok/váltótagok emailt kapnak a csapattagi/váltótagi felkérésről.</li>';
	$html .=    	'<li>A csapattagok/váltótagok az emailben a megadott linkre kattintva elfogadhatják a csapattagi/váltótagi felkérést.</li>';
	$html .=    	'<li>Amennyiben minden csapattag/váltótag megerősíttete részvételi szándékát a csapatban/váltóban, a csapat/váltó megalakult.</li>';
	$html .=  	'</ol>';
	$html .=	'</div>';

	$html .= '<form id="add-team-form" name="add-team-form" method="post" action="#">';
	$html .= 		'<h2>Csapat adatai</h2>';

	$html .= 		'<input type="hidden" name="competitionID" id="competitionID" value="'.$competitionID.'">';
	$html .= 		'<label for="team_name">*Csapat neve:</label>';
	$html .= 		'<input type="text" name="team_name" id="team_name" team_id="'.$teamID.'" value="'.
						$teamName = ($teamID > 0 ? $competition_entry->getTeamNameFromID($teamID, $competitionID) : "").'"
						'.
						$teamName = ($teamID > 0 ? 'disabled' : '').'>';


	$str_length = 5;			
	$html .= 	'<label for="teamleader">Csapatkapitány regisztrációs kódja:</label>';
	$html .= 	'<input type="text" name="teamates[]" readonly id="teamleader" value="'.substr(str_repeat(0, $str_length) . $competitionRegID, -$str_length).'" >';

	$teamateNum = $competition_entry->getTeamMembersCount($competitionID);
	$i = 0;

	while ( $i <= $teamateNum-2) 
	{		
		$invitedTeamate= $invitedTeamatesArray[$i];
		$i++;
		$isMember = $competition_entry->regUserIsMember($invitedTeamate, $competitionID);

		$html .= 	'<label for="teamate'.$i.'">Meghívott tag'.$i.' regisztrációs kódja:</label>';
		$html .= 	'<input type="text" name="teamates[]" id="teamate'.$i.'" value="'.
						$teamName = ($teamID > 0 ? $invitedTeamate : "").'"
						'.
						$teamName = ($teamID > 0 ? 'readonly' : '').'>';
		if ($teamID > 0)
			$html .= 	'<div '.$status = ($isMember ? 'class="ok-sign" title="Csatlakozás a csapathoz/váltóhoz elfogadva.' : 'class="question-mark" title="Visszaigazolásra vár.'). '"></div>';
		
	}

	$html .=		'<div class="btn-container">';
	$html .=			'<button id="add-team-btn" type="button" '.
						$teamName = ($teamID <= 0 ? 'class="blue1"' : ' class="disabled" disabled"').'>Elküld</a>';
	$html .= 		'</div>';
	$html .= '</form>';
?>