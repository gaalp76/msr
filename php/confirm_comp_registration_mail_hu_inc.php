<?php  
	$str_length = 5;

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
			$specifiedMessage = '<p><b>Összeg (Nevezési díj):</b></p>
								<ul>
									<li> 5 km - 3.500 Ft</li>
									<li>10 km - 4.000 Ft</li>
									<li>15 km - 4.500 Ft</li>
								</ul>';
			$contact = "vulcanrun@survivalrun.hu";
			break;
		case 'halfmarathon':
			$competitionName = 'III. Festék Bázis Jánosházai Félmaraton';
			$specifiedMessage = '';

			$specifiedMessage .= '<table border="1">';
			$specifiedMessage .= '	<tbody>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td><b>Kedvezm&eacute;nyezett neve:</td></b>';
			$specifiedMessage .= '			<td>Honv&eacute;d Ez&uuml;st Ny&iacute;l SE</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td><b>Banksz&aacute;mlasz&aacute;m:</b></td>';
			$specifiedMessage .= '			<td>59800156-11021412-00000000</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td valign="top"><b>SWIFT k&oacute;d:</td></b>';
			$specifiedMessage .= '			<td>TAKBHUHBXXX<br>(Amennyiben külföldi számláról utal)</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td><b>K&ouml;zlem&eacute;ny:</td></b>';
			$specifiedMessage .= '			<td>'.$str = $rowComp["linked_to"].substr(str_repeat(0, $str_length).$rowCompReg["id"], -$str_length).'  '.$comp_dist.' (valamint a "valto" szó, amennyiben váltóban szeretne indulni, továbbá a váltó tagok regisztrációs kódja)</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '	</tbody>';
			$specifiedMessage .= '</table>';

			$specifiedMessage .= '<p>Nevezési díjak</p>';
                        $specifiedMessage .= '<table border="1">';
			$specifiedMessage .= '	<tbody>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<th>Táv</th>';
			$specifiedMessage .= '			<th>Előnevezés 02.25-ig</th>';
			$specifiedMessage .= '			<th>Helyszíni nevezés</th>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td>5 km</td>';
			$specifiedMessage .= '			<td>3.000 Ft</td>';
			$specifiedMessage .= '			<td>4.000 Ft</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td>10 km</td>';
			$specifiedMessage .= '			<td>5.000 Ft</td>';
			$specifiedMessage .= '			<td>6.000 Ft</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td>21,1 km</td>';
			$specifiedMessage .= '			<td>5.000 Ft</td>';
			$specifiedMessage .= '			<td>6.000 Ft</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td>Váltó</td>';
			$specifiedMessage .= '			<td>2 x 4.000 Ft</td>';
			$specifiedMessage .= '			<td>2 x 5.000 Ft</td>';
			$specifiedMessage .= '		</tr>';
			
			$specifiedMessage .= '	</tbody>';
			$specifiedMessage .= '</table>';

			$contact = "halfmarathon@survivalrun.hu";
			break ;
		case 'vulcanobstacle':
			$competitionName = 'Vulcan Run - akadályfutás';
			$specifiedMessage .= '<table border="0">';
			$specifiedMessage .= '	<tbody>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td><b>Kedvezm&eacute;nyezett neve:</td></b>';
			$specifiedMessage .= '			<td>Honv&eacute;d Ez&uuml;st Ny&iacute;l SE</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td><b>Banksz&aacute;mlasz&aacute;m:</b></td>';
			$specifiedMessage .= '			<td>59800156-11021412-00000000</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td valign="top"><b>SWIFT k&oacute;d:</td></b>';
			$specifiedMessage .= '			<td>TAKBHUHBXXX<br>(Amennyiben külföldi számláról utal)</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td><b>K&ouml;zlem&eacute;ny:</td></b>';
			$specifiedMessage .= '			<td>'.$str = $rowComp["linked_to"].substr(str_repeat(0, $str_length).$rowCompReg["id"], -$str_length).' (Az Ön regisztrációs kódja)</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '	</tbody>';
			$specifiedMessage .= '</table>';

			$specifiedMessage .= '<table border="0">';
			$specifiedMessage .= '	<tbody>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<th>Táv</th>';
			$specifiedMessage .= '			<th>Február 05-ig</th>';
			$specifiedMessage .= '			<th>Márciusv 05-ig</th>';
			$specifiedMessage .= '			<th>Április 1-ig</th>';
			$specifiedMessage .= '			<th>Helyszíni nevezés</th>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td>8+ km</td>';
			$specifiedMessage .= '			<td>9.000 Ft/fő</td>';
			$specifiedMessage .= '			<td>10.000 Ft/fő</td>';
			$specifiedMessage .= '			<td>11.000 Ft/fő</td>';
			$specifiedMessage .= '			<td>13.000 Ft/fő</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td>14+ km</td>';
			$specifiedMessage .= '			<td>11.000 Ft/fő</td>';
			$specifiedMessage .= '			<td>12.000 Ft/fő</td>';
			$specifiedMessage .= '			<td>13.000 Ft/fő</td>';
			$specifiedMessage .= '			<td>15.000 Ft/fő</td>';
			$specifiedMessage .= '		</tr>';
			$specifiedMessage .= '		<tr>';
			$specifiedMessage .= '			<td>Póló</td>';
			$specifiedMessage .= '			<td>2.000 Ft/fő</td>';
			$specifiedMessage .= '			<td>2.000 Ft/fő</td>';
			$specifiedMessage .= '			<td>2.000 Ft/fő</td>';
			$specifiedMessage .= '			<td>nincs lehetőség</td>';
			$specifiedMessage .= '		</tr>';
			
			$specifiedMessage .= '	</tbody>';
			$specifiedMessage .= '</table>';

			$contact = "vulcanobstacle@survivalrun.hu";
			break ;

	}
	$subject = 'Nevezés megerősítése a '.$competitionName.' versenyre';
	$message = '<h1>Tisztelt '.$rowCompReg["name"].'!</h1>';

	$message .= '<p><b>Ez egy automatikus e-mail, kérjük ne válaszoljon rá. Amennyiben kérdése van a versennyel kapcsolatban, kérjük a következő e-mail címen érdeklődjön:'. $contact.'</b></p>';

	$message .= '<p>Regisztrációját megerősítettük. A továbbiakban kérjük utalja a megfelelő összeget az alábbiak szerint:</p>';

	$message .= $specifiedMessage;

	$message .= '<p>Amennyiben az e-mail kiküldese után 5 nappal nem érkezik be utalása, regisztrációját töröljük, amiről e-mailben értesítjük.</p>';

	$message .= '<p>A nevezés elfogadásáról, jóváírásáról utalása után e-mailben értesítjük.</p>';

	$message .= '<p>Kapcsolat: </p>'.$contact;

	$message .= '<p>Üdvözlettel:<br>'.$this->BUSS_NAME.'<br> csapata</p>';
?>
