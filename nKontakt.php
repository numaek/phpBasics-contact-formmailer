<?php

	/* 
	 * phpBasics
	 * ---------
	 * 
	 * Script:        Kontaktformular
	 * 
	 * Version:       1.0
	 * Release:       01.10.2019
	 * 
	 * Author:        numaek   
	 * Copyright (c): 2004-2019 by www.numaek.de
	 * 
	 * *********************************************************************************************************************************************************************************************
	 */


	// Konfiguration
	// =============
	define('NKONTAKT_EMAIL', 'meineadresse@gibtesnicht.fake');


	// =================================================================================================================================================================================================


	echo "<!DOCTYPE html>
	<html lang=\"de\">
		<head>
			<title>Kontaktformular</title>
			<META charset=\"utf-8\">
			<META NAME=\"viewport\" content=\"width=device-width, initial-scale=1.0\">

		</head>
	<body>

	<h1>Kontaktformular</h1><br>\n";


	$preValue           = array();
	$getValue           = array();

	$getValue['anrede'] = "";
	$getValue['name']   = "";
	$getValue['email']  = "";
	$getValue['text']   = "";

	$zeigeFormular      = 1;


	if( isset($_POST['senden']) )
	{
		// Anrede kann nr die beiden festen Werte "Frau" oder "Herr" liefern
		// =================================================================
		$getValue['anrede'] = htmlspecialchars(trim($_POST['anrede']));


		// Name ist kein Pflichtfeld und erzeugt keinen Fehler
		// ===================================================
		$getValue['name'] = htmlspecialchars(trim($_POST['name']));


		// Email ist ein Pflichtfeld und muss im korrekten Format vorliegen, sonst wird einen Fehler erzeugt
		// =================================================================================================
		$getValue['email'] =  htmlspecialchars(trim($_POST['email']));
		if( $getValue['email'] == "" )
		{
			$colorEmail = "#FFD2CF";
			$isError[]  = "Geben Sie bitte eine Emailadresse ein.";
		} else
		  {
			if( function_exists('filter_var') )
			{
				// PHP-Version 7
				if( !filter_var($getValue['email'], FILTER_VALIDATE_EMAIL) )
				{
					$colorEmail = "#FFFF00";
					$isError[]  = "Das ist keine richtige Emailadresse.";
				}
			} else
			  {
				// PHP-Version 5
				if( !preg_match('/^[A-Z0-9._-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z.]{2,6}$/i', $getValue['email']) ) 
				{
					$colorEmail = "#FFFF00";
					$isError[]  = "Das ist keine richtige Emailadresse.";
				}
			  }
		  }


		// Text ist ein Pflichtfeld und darf daher nicht leer sein
		// =======================================================
		$getValue['text']  = htmlspecialchars(trim($_POST['text']));
		if( $getValue['text'] == "" )
		{
			$colorText = "#FFD2CF";
			$isError[] = "Geben Sie bitte einen Text ein.";
		}


		// Falls kein Fehler auftrat...
		// ============================
		if( sizeof($isError) == 0 )
		{
			// Default-Wert eintragen falls leer
			// =================================
			$getValue['name'] = ( $getValue['name'] != "" ) ? $getValue['name'] : "Kein Name angegeben";


			// Dem Mail-Header Info zum Absender und Antwort geben
			// ===================================================
			$mailHeader  = "FROM: Kontaktformular <".$getValue['email'].">\r\n";
			$mailHeader .= "Reply-To: <".$getValue['email'].">\r\n";
			$mailHeader .= "Mime-Version: 1.0\r\n";
			$mailHeader .= "Content-type: text/plain; charset=utf-8";


			// Den angezeigten Mailinhalt zusammensetzen
			// =========================================
			$mailMsg    = "";
			$mailMsg   .= "Geschrieben von ".$getValue['anrede']." ".$getValue['name'];
			$mailMsg   .= "\n\n";
			$mailMsg   .= "Emailadresse: ".$getValue['email'];
			$mailMsg   .= "\n\n";
			$mailMsg   .= "Nachricht:\n".$getValue['text'];


			// Die Mail senden und (Miss)erfolgs-Meldung ausgeben
			// ==================================================
			if( @mail(NKONTAKT_EMAIL, "Nachricht vom Kontaktformular", $mailMsg, $mailHeader) )
			{
				echo "Die Anfrage wurde gesendet.";
			} else
			  {
				echo "Die Anfrage konnte leider nicht gesendet werden.";
			  }

			echo "<br><br><a href=\"".$_SERVER['PHP_SELF']."\">Zur&uuml;ck zum Kontaktformular</a>";


			// Anzeige des Formulars blocken
			// =============================
			$zeigeFormular = 0;
		} else
		  {
			// Fehlermeldungen ausgeben
			// ========================
			$errorMsg = "<b><font color=\"red\">Folgende Fehler sind aufgetreten:</font></b><br>";
			for( $em = 0; $em < sizeof($isError); $em++ )
			{
				$errorMsg .= "- ".$isError[$em]."<br>";
			}
			$errorMsg .= "<br><br>\n";

			echo $errorMsg;
		  }
	}


	if( $zeigeFormular == 1 )
	{
		// Werte vorgeben falls keine Usereingaben gemacht wurden
		// ======================================================
		$preValue['anrede'] = ( $getValue['anrede'] != "" ) ? $getValue['anrede'] : "Herr";
		$preValue['name']   = ( $getValue['name']   != "" ) ? $getValue['name']   : "";
		$preValue['email']  = ( $getValue['email']  != "" ) ? $getValue['email']  : "";
		$preValue['text']   = ( $getValue['text']   != "" ) ? $getValue['text']   : "";

		$bgEmail = ( isset($colorEmail) ) ? "background-color: ".$colorEmail.";" : "";
		$bgText  = ( isset($colorText)  ) ? "background-color: ".$colorText.";"  : "";


		// Das Formular mit den Vorgabe-Werten anzeigen
		// ============================================
		echo "<form name=\"kontakt\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">

			Anrede:<br>
			<select name=\"anrede\" style=\"width: 100px;\">\n";
				if( $preValue['anrede'] == "Frau" )
				{
					echo "<option selected value=\"Frau\">Frau<option value=\"Herr\">Herr\n";
				} else
				  {
					echo "<option value=\"Frau\">Frau<option selected value=\"Herr\">Herr\n";
				  }
			echo "</option></select><br><br>

			Name:<br>
			<input type=\"text\" name=\"name\" value=\"".$preValue['name']."\" style=\"width: 250px;\"><br><br>

			Emailadresse:<br>
			<input type=\"text\" name=\"email\" value=\"".$preValue['email']."\" style=\"width: 250px; ".$bgEmail."\"><br><br>

			Nachricht:<br>
			<textarea name=\"text\" wrap=\"virtual\" style=\"width: 99%; height: 300px; ".$bgText."\">".$preValue['text']."</textarea><br><br>

			<input type=\"submit\" value=\"Abschicken\">
			<input type=\"reset\"  value=\"Reset\">
			<input type=\"hidden\" name=\"senden\" value=\"1\">

		</form>\n";
	}


	echo "</body></html>";

?>