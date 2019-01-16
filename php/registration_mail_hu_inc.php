<?php  
	
	$subject = 'Regisztráció a '.$this->BUSS_NAME.' honlapján';
	$message = '<h1>Tisztelt '.$row["name"].'!</h1>';

	$message .= '<p><b>Ez egy automatikus e-mail, kérjük ne válaszoljon.</b></p>';

	$message .= '<p>Ezt a levelet azért kapta, mert Ön vagy valaki a nevében regisztrált a '.$this->BUSS_NAME.' honlapján.</p>';

	$message .= '<p>A regisztráció megerősítéséhez kérem kattintson az alábbi linkre:</p>';

	$message .= '<p><a href="http://'.$this->BASE_URL.'/#ConfirmRegistration&lang='.$lang.'&username='.$row["username"].'&hash='.$hashStr.'">Regisztráció megerősítése.</a></p>';

	$message .= '<p>Amennyiben nem Ön regisztrált kérjük tekintse ezt a levelünket tárgytalannak.</p>';

	$message .= '<p>Üdvözlettel:</br>'.$this->BUSS_NAME.'</br> csapata</p>';	
?>
