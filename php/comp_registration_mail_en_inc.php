<?php  

	/*LE KELL FORDÍTANI ANGOLRA !! */
	switch ($rowComp["linked_to"]) {
		case 'msr':
			$competitionName = 'Military Survival Run';
			break;
		case 'vulcanrun':
			$competitionName = 'Vulcan Run';
			break;

	}
	$subject = 'Registration to '.$competitionName.' competition';
	$message = '<h1>Tisztelt '.$rowCompReg["name"].'!</h1>';

	$message .= '<p>Ezt a levelet azért kapta, mert Ön vagy valaki a nevében nevezett a '.$this->BUSS_NAME.' honlapján a '.$competitionName.' versenyre.</p>';

	$message .= '<p>A nevezés megerősítéséhez kérem kattintson az alábbi linkre:</p>';

	$message .= '<p><a href="http://'.$this->SERVERNAME.'/'.$this->BASE_URL.'/#ConfirmCompetitionRegistration&target=main&lang='.$lang.'&comp_reg_id='.$competitionRegID.'">Nevezés megerősítése.</a></p>';

	$message .= '<p>A verseny időpontja: '.$rowComp['start_date'].'</p>';

	$message .= '<p>Részletes információk a <a href="http://www.survivalrun.hu" target="_blank">honlapon</a> megtekinthetők.</p>';

	$message .= '<p>Amennyiben nem Ön regisztrált kérjük tekintse ezt a levelünket tárgytalannak.</p>';

	$message .= '<p>Üdvözlettel:</br>'.$this->BUSS_NAME.'</br> csapata</p>';	
?>
