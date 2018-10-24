<?php  
	$hashStr = htmlspecialchars(uniqid('',true));

	$subject = "Elfelejtett jelszó";
	$message = '<h1>Tisztelt '.$row["name"].'!</h1>';

	$message .= '<p>Ezt a levelet azért kapta, mert Ön vagy valaki a nevében a '.$this->BUSS_NAME.' honlapján jelszóemlékeztetőt kért.</p>';

	$message .= '<p>A jelszó megváltoztatásához kérem kattintson az alábbi linkre</p>';
	$message .= '<p><a href="http://'.$this->BASE_URL.'/#ForgotPasswordForm&traget=main&lang='.$lang.'&username='.$row["username"].'&hash='.$hashStr.'">Jelszó megváltoztatása</a></p>';

	$message .= '<p>Üdvözlettel:<br>'.$this->BUSS_NAME.' csapata</p>';	
?>
