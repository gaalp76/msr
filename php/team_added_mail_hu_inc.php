<?php  
	switch ($linkedTo) {
		case 'msr':
			$competitionName = 'Military Survival Run';
			break;
		case 'vulcanrun':
			$competitionName = 'Vulcan Run';
			break;

	}
	$subject = 'Csapat létrehozás a '.$competitionName.' versenyre';
	$message = '<h1>Tisztelt '.$row["name"].'!</h1>';

	$message .= '<p>Ezt a levelet azért kapta, mert valaki nevezett a '.$this->BUSS_NAME.' honlapján a '.$competitionName.' versenyre. A versenyen létrehozota a "'.$teamName.'" csapatot. </p>';	

	$message .= '<p>Amennyiben Ön is szeretne tagja lenni a csapatnak, kérem kattintson az alábbi linkre:</p>';

	$message .= '<p><a href="http://'.$this->BASE_URL.'/#'.$linkedTo.'Entry&action=confirmAddTeamate&lang='.$lang.'&comp_reg_id='.$competitionRegID.'&competitionID='.$competitionID.'&teamID='.$teamID.'">Belépek a "'.$teamName.'" csapatba.</a></p>';

	$message .= '<p><b>A verseny időpontja: '.$row['start_date'].'</b></p>';

	$message .= '<p>Részletes információk a <a href="http://www.survivalrun.hu" target="_blank">honlapon</a> megtekinthetők.</p>';

	$message .= '<p>Amennyiben nem Ön regisztrált, kérjük tekintse ezt a levelünket tárgytalannak.</p>';

	$message .= '<p>Üdvözlettel:</br>'.$this->BUSS_NAME.'</br> csapata</p>';	
?>
