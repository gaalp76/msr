<?php  
	
	switch ($rowComp["linked_to"]) {
		case 'msr':
			$competitionName = 'Military Survival Run';
			break;
		case 'vulcanrun':
			$competitionName = 'Vulcan Run';
			break;

	}
	$subject = 'Nevezés megerősítése a '.$competitionName.' versenyre';
	$message = '<h1>Tisztelt '.$rowCompReg["name"].'!</h1>';

	$message .= '<p>Regisztrációját megerősítettük. A továbbiakban kérjük utalja a megfelelő összeget forintban a szervező Honvéd „Ezüst Nyíl” SE bankszámlaszámára:</p>';

	$message .= '<p><b>Nyugat Takarék Szövetkezet: 59800156-11021412<br>SWIFT kód: TAKBHUHBXXX</b></p>';

	$message .= '<p>Nevezési díj:</p>';

	$message .= '<ul>
					<li>5 km - 3.500 Ft</li>
					<li>10 km - 4.000 Ft</li>
					<li>15 km - 4.500 Ft</li>
				</ul>';

	$message .= '<p>Amennyiben technikai pólót is szeretne rendelni, kérjük utaljon <b> további 2200 Ft-ot<b>.</p>';

	$message .= '<p>A nevezés elfogadásáról, jóváírásáról utalása után e-mailben értesítjük.</p>';

	$message .= '<p>Üdvözlettel:<br>'.$this->BUSS_NAME.'<br> csapata</p>';
?>
