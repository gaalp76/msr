<?php  
	switch ($rowComp["linked_to"]) {
		case 'msr':
			$competitionName = 'Military Survival Run';
			break;
		case 'vulcanrun':
			$competitionName = 'Vulcan Run';
			break;
		case 'halfmarathon':
			$competitionName = 'III. Festék Bázis Jánosházai Félmaraton';
			break ;

	}
	$subject = 'Csapat - váltó létrehozás a '.$competitionName.' versenyre';
	$message = '<h1>Tisztelt '.$row["name"].'!</h1>';

	$message .= '<p>Ezt a levelet azért kapta, mert a '.$this->BUSS_NAME.' honlapján a '.$competitionName.' versenyre nevezett és elfogadta felvételét "'.$teamName.'" nevű váltó csapatába/váltójába. </p>';	

/*
	$message .= '<p><b>A verseny időpontja: '.$row['start_date'].'</b></p>';

	$message .= '<p>Részletes információk a <a href="http://survivalrun.hu" target="_blank">honlapon</a> megtekinthetők.</p>';

*/
	$message .= '<p>Amennyiben nem Ön regisztrált, kérjük tekintse ezt a levelünket tárgytalannak.</p>';

	$message .= '<p>Jó felkészülést kívánunk!</p>';

	$message .= '<p>Üdvözlettel:</br>'.$this->BUSS_NAME.'</br> csapata</p>';	
?>
