<?php  
	
	/*switch ($rowComp["linked_to"]) {
		case 'msr':
			$competitionName = 'Military Survival Run';
			break;
		case 'vulcanrun':
			$competitionName = 'Vulcan Run';
			break;

	}
	$subject = 'Nevezés megerősítése a '.$competitionName.' versenyre';
	$message = '<h1>Tisztelt '.$rowCompReg["name"].'!</h1>';
*/
	header('Content-Type: text/html; charset=utf-8');
	$str_length = 5;

	$message = '<p><b>Ez egy automatikus e-mail, kérjük ne válaszoljon!</b></p>';
	$message .= '<p>Regisztrációját megerősítettük. A továbbiakban kérjük utalja a megfelelő összeget az egyesület számlájára forintban az alábbiak szerint:</p>';

	$message .= '<table border="0">';
	$message .= '	<tbody>';
	$message .= '		<tr>';
	$message .= '			<td><b>Kedvezm&eacute;nyezett neve:</td></b>';
	$message .= '			<td>Honv&eacute;d Ez&uuml;st Ny&iacute;l SE</td>';
	$message .= '		</tr>';
	$message .= '		<tr>';
	$message .= '			<td><b>Banksz&aacute;mlasz&aacute;m:</td></b>';
	$message .= '			<td>59800156-11021412-00000000</td>';
	$message .= '		</tr>';
	$message .= '		<tr>';
	$message .= '			<td valign="top"><b>SWIFT k&oacute;d:</td></b>';
	$message .= '			<td>TAKBHUHBXXX<br>(Amennyiben külföldi számláról utal)</td>';
	$message .= '		</tr>';
	$message .= '		<tr>';
	$message .= '			<td><b>K&ouml;zlem&eacute;ny:</td></b>';
	$message .= '			<td>0001(Az Ön regisztrációs kódja)</td>';
	$message .= '		</tr>';
	$message .= '	</tbody>';
	$message .= '</table>';

	$message .= '<p><b>Összeg (Nevezési díj):</b></p>';

	$message .= '<ul>
					<li>5 km - 3.500 Ft</li>
					<li>10 km - 4.000 Ft</li>
					<li>15 km - 4.500 Ft</li>
				</ul>';

	$message .= '<p>Amennyiben technikai pólót is szeretne rendelni, kérjük utaljon <b> további 2200 Ft-ot</b>.</p>';

	$message .= '<p>A nevezés elfogadásáról, jóváírásáról utalása után e-mailben értesítjük.</p>';

	//$message .= '<p>Üdvözlettel:<br>'.$this->BUSS_NAME.'<br> csapata</p>';
	echo $message;
?>
