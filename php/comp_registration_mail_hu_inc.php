<?php  
	switch ($rowComp["linked_to"]) {
		case 'sr':
			$competitionName = 'Survival Run';
			$specifiedMessage = '';
			$contact = "sr@survivalrun.hu";
			break;
		case 'msr':
			$competitionName = 'Military Survival Run';
			$specifiedMessage = '';
			$contact = "msr@survivalrun.hu";
			break;
		case 'vulcanrun':
			$competitionName = 'Vulcan Run';
			$specifiedMessage = '';
			$contact = "vulcanrun@survivalrun.hu";
			break;
		case 'halfmarathon':
			$competitionName = 'III. Festék Bázis Jánosházai Félmaraton';
			$specifiedMessage = '';
			$contact = "halfmarathon@survivalrun.hu";
			break ;
		case 'vulcanobstacle':
			$competitionName = 'Vulcan Run - akadályfutás';
			$specifiedMessage = '';
			$contact = "vulcanobstacle@survivalrun.hu";
			break ;

	}
	
	$subject = 'Nevezés a '.$competitionName.' versenyre';
	$message = '<h1>Tisztelt '.$rowCompReg["name"].'!</h1>';

	$message .= '<p><b>Ez egy automatikus e-mail, kérjük ne válaszoljon rá. Amennyiben kérdése van a versennyel kapcsolatban, kérjük a következő e-mail címen érdeklődjön:'. $contact.'</b></p>';
	$message .= '<p>Ezt a levelet azért kapta, mert Ön vagy valaki a nevében nevezett a '.$this->BUSS_NAME.' honlapján a '.$competitionName.' versenyre.</p>';

	$str_length = 5;
	$message .= '<p><b>Az Ön regisztrációs kódja:'.$str = substr(str_repeat(0, $str_length) . $competitionRegID, -$str_length).'</b></p>';
	$message .= '<p><b>A regisztrációs kódot kérjük őrizze meg, ezzel a kóddal hivatkozhat nevezésére.</b></p>';

	$message .= '<p>A nevezés megerősítéséhez kérem kattintson az alábbi linkre:</p>';

	$message .= '<p><a href="http://'.$this->BASE_URL.'/#ConfirmCompetitionRegistration&lang='.$lang.'&comp_reg_id='.$competitionRegID.'&competitionID='.$competitionID.'">Nevezés megerősítése.</a></p>';

	
	$message .= $specifiedMessage;
	
	$message .= '<p>Amennyiben nem Ön regisztrált kérjük, tekintse ezt a levelünket tárgytalannak.</p>';
	$message .= '<p>Amennyiben az e-mail kiküldese után 5 nappal sem erősíti meg nevezési szándékát, regisztrációját töröljük, amiről e-mailben értesítjük.</p>';

	$message .= '<p>Kapcsolat: </p>'.$contact;

	$message .= '<p>Üdvözlettel:<br>'.$this->BUSS_NAME.'<br> csapata</p>';	
?>
