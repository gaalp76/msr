<?php 
	$html  = '<h1>Elfelejtett jelszó</h1>';
	$html .= '<form name="forgot_pw_form" id="forgot_pw_form">';
	$html .= '	<label>Azonosító:</label>';
	$html .= '	<input type="text" name="forgot_pw_username" id="forgot_pw_username" value="'.$username.'" readonly=readonly>';
	$html .= '	<label>Érvényesítő kód:</label>';
	$html .= '  <input type="text" name="hash" id="hash" value="'.$hash.'">';
	$html .= '	<label>Új jelszó</label>';
	$html .= '	<input type="password" name="new_password" id="new_password"></input>';
	$html .= '	<label>Új jelszó megerősítése</label>';
	$html .= '	<input type="password" name="confirm_new_password">';
	$html .= '	<button id="change_password_btn" class="blue1">Elküld</button>';
	$html .= '</form>';
?>
