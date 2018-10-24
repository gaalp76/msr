<?php  
	switch ($rowComp["linked_to"]) {
		case 'msr':
			$competitionName = 'Military Survival Run';
			break;
		case 'vulcanrun':
			$competitionName = 'Vulcan Run';
			break;

	}
	$subject = 'Nevezés a '.$competitionName.' versenyre';
	$message = '<h1>Tisztelt '.$rowCompReg["name"].'!</h1>';

	$message .= '<p>Ezt a levelet azért kapta, mert Ön vagy valaki a nevében nevezett a '.$this->BUSS_NAME.' honlapján a '.$competitionName.' versenyre.</p>';

	$str_length = 5;
	$message .= '<p><b>Az Ön regisztrációs kódja:'.$str = substr(str_repeat(0, $str_length) . $competitionRegID, -$str_length).'</b></p>';
	$message .= '<p><b>A regisztrációs kódot kérjük őrizze meg, ezzel a kóddal hivatkozhat nevezésére.</b></p>';

	$message .= '<p>A nevezés megerősítéséhez kérem kattintson az alábbi linkre:</p>';

	$message .= '<p><a href="http://'.$this->BASE_URL.'/#ConfirmCompetitionRegistration&lang='.$lang.'&comp_reg_id='.$competitionRegID.'&competitionID='.$competitionID.'">Nevezés megerősítése.</a></p>';

	$message .= '<p>A verseny időpontja: '.$rowComp['start_date'].'</p>';

	$message .= '<p>Részletes információk a <a href="http://www.survivalrun.hu" target="_blank">honlapon</a> megtekinthetők.</p>';
	$message .= '<p><b>Fizetés átutalással, forintban a szervező Honvéd „Ezüst Nyíl” SE bankszámlaszámára<br> Nyugat Takarék Szövetkezet: 59800156-11021412<br>
SWIFT kód: TAKBHUHBXXX </b></p>';

	$message .= '<p>Amennyiben nem Ön regisztrált, kérjük tekintse ezt a levelünket tárgytalannak.</p>';

	$message .= '<p>Üdvözlettel:<br>'.$this->BUSS_NAME.'<br> csapata</p>';	
?>
