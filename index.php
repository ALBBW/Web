<?php
/*
*	Programmname: Adminpage
*	Autor.......: Phillip-Morton Paape <phillip_morton@yahoo.com>
*	Datum.......: 2018/11/12
*
*	Beschreibung: Webseite zum Visuellen Darstellen von Arbeitszeiten der Auszubildenden
*					die Automatisch alle Fehlzeiten im Monat Addiert
*
*/
	require("scripts/php/marmalo.php");
	$dt = new date_time();
	session_start();													//Session wird Gestartet
	if($_SESSION["login"] == "")										//Falls Session Variable login leer
	{
		header('location: login.php');										//Gehe zum Loginscreen
	}

	require("Scripts/php/controller.php");								//Binde controller.php als require ein
	$contro = new controller();											//Instanziierung des controllers
	$conndata = $contro->GetServerData();								//Holen der Datenbank Verbindungsinformationen

	if(($_SESSION["monat"] == "") || ($_SESSION["jahr"] == ""))			//Falls kein Monat und Jahr gewählt
	{
		$_SESSION["monat"] = date("m", time());							//Setzte Monat auf aktuellen Monat
		$_SESSION["jahr"] = date("Y", time());							//Setzte Jahr auf Aktuelles Jahr
	}
	$monthbadtime = 0;													//Variable für die gesamte Fehlzeit im Monat
	$monat = $_SESSION["monat"];										//Übergabe des Session Wertes für Monat an die Monat Variable
	$year = $_SESSION["jahr"];											//Übergabe des Session wertes für Jahr an die year Variable
	$selected_user = $_SESSION["selusr"];								//Übergabe des Session Wertes für Ausgewählten TN an die Variable
	if(isset($_POST["change_Calendar"]))								//Wenn Kalendar aktuallisiert
	{
		$monat = $contro->GetMonthNum($_POST["monat"]);					//Hole Nummer des Monats (Jannuar = 1)
		$_SESSION["monat"] = $monat;									//Speichere Monat nummer in Session Variable
		$_SESSION["jahr"] = $_POST["yearselect"];						//Speichere Gewähltes Jahr in Session Variable
		$year = $_SESSION["jahr"];										//Übergebe Session Jahr an year Variable
	}
	
	if(isset($_POST["lout"]))											//Wenn Logout gedrückt
	{
		session_destroy();												//Zerstöre Session
		header('location: login.php');									//und Gehe zum Login
	}
	
	if(isset($_POST["tnclick"]))																					//Wenn Teilnehmer angeklickt
	{
		$selected_user = $_POST["tn"];																				//Ausgewählter Teilnehmer wird in Variable Gespeichert
		$_SESSION["selusr"] = $selected_user;																		//Ausgewählter Teilnehmer wird in Session Variable Gespeichert
		$conn = mysqli_connect($conndata["server"], $conndata["admin"], $conndata["pass"], $conndata["db"]);		//Baue Datenbankverbindung auf
		$sql = "SELECT * FROM teilnehmer WHERE rfid='" . $selected_user . "'";										//Select String
		$query = mysqli_query($conn, $sql);																			//Führe sql String aus
		$usrdata = mysqli_fetch_assoc($query);																		//Hole Daten aus der Datenbank
		$_SESSION["tnname"] = $usrdata["vorname"] . " " . $usrdata["nachname"];										//Übergebe vor- und nachname an Session Variable
		mysqli_close($conn);																						//Beende Datenbankverbindung
	}
	
	if(isset($_POST["add_exception"]))																				//Wenn Ausnahme hinzufügen geklickt
	{
		$tusr = $_POST["user"];																						//Ausgewählter Teilnehmer wird an Variable Übergeben
		$date = $_POST["date"];																						//Ausgewähltes Datum wird an Variable übergeben
		$conn = mysqli_connect($conndata["server"], $conndata["admin"], $conndata["pass"], $conndata["db"]);		//Baue Datenbankverbindung auf
		$sql = "UPDATE anwesenheit SET status='exception' WHERE id_tn='" . $tusr . "' AND datum='" . $date . "'";	//sql Update String
		$query = mysqli_query($conn, $sql);																			//Mache änderungen wirksam
		mysqli_close($conn);																						//Beende Datenbankverbindung
	}
	
	if(isset($_POST["del_exception"]))																				//Wenn Ausnahme löschen gedrückt
	{
		$tusr = $_POST["user"];																						//Ausgewählter Teilnehmer wird an Variable übergeben
		$date = $_POST["date"];																						//Ausgewähltes Datum wird an Variable übergeben
		$conn = mysqli_connect($conndata["server"], $conndata["admin"], $conndata["pass"], $conndata["db"]);		//Baue Datenbankverbindung auf
		$sql = "UPDATE anwesenheit SET status='bad' WHERE id_tn='" . $tusr . "' AND datum='" . $date . "'";			//sql Update String
		$query = mysqli_query($conn, $sql);																			//Mache änderungen wirksam
		mysqli_close($conn);																						//Beende Datenbankverbindung
	}
	
	if(isset($_POST["create_new_tn"]))											//Wenn erstelle neuen Teilnehmer gedrückt
	{
		$vorname = $_POST["tnvor"];												//Vorname wird an Variable übergeben						
		$nachname = $_POST["tnnach"];											//Nachname wird an Variable übergeben
		$gruppe = $_POST["ausgr"];												//Ausbildungsgruppe wird an Variable übergeben
		$newtnrfidcode = $_POST["rfidnewtn"];									//RFID Code wird an Variable übergeben
		$contro->CreateNewTN($vorname, $nachname, $gruppe, $newtnrfidcode);		//Rufe CreateNewTN Methode auf und übergebe alle variablen
	}
	
	if(isset($_POST["addTermin"]))
	{
		$header = $_POST["termheader"];
		$content = $_POST["content"];
		$hour = $_POST["termhour"];
		$minute = $_POST["termminute"];
		$user = $_POST["user"];
		$date = $_POST["date"];
		$termtime = $hour . ":" . $minute;
		$conn = mysqli_connect($conndata["server"], $conndata["admin"], $conndata["pass"], $conndata["db"]);
		$checkSQL = "SELECT * FROM termine WHERE tn_id='" . $user . "' AND betreff='" . $header . "' AND nachricht='" . $content . "' AND datum='" . $date . "' AND zeit='" . $termtime . "'";
		echo($checkSQL);
		$checkQuery = mysqli_query($conn, $checkSQL);
		echo(var_dump($checkQuery));
		$ifexistent = mysqli_num_rows($checkQuery);
		if($ifexistent == 0)
		{
			$sql = "INSERT INTO termine (tn_id, betreff, nachricht, datum, zeit) VALUES ('" . $user . "', '" . $header . "','" . $content . "','0" . $date ."', '" . $termtime . "')";
			$query = mysqli_query($conn, $sql);
		}
	}
	
	
	
	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Zeitüberwachung</title>
	<link rel="stylesheet" href="Styles/layout.css">
</head>
<body>
	<div id="back">
	</div>
	<div id="header">
		<h1 id="logo">TimeControl</h1>
	</div>
	<div id="sidebar">
		<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
			<input type="submit" name="showtn" value="Teilnehmer Anzeigen" class="btn_side">
			<input type="submit" name="newtn" value="Neuen Teilnehmer Hinzufügen" class="btn_side">
			<input type="submit" name="lout" value="Logout" class="btn_side">
		</form>
	</div>
	<?php
	if(isset($_SESSION["tnname"]))																					//Wenn Teilnehmer ausgewählt dann Lade Kalendar Feld
	{
	?>
	<div id="mainfield">
		<?php
		echo("<h3>" . $_SESSION["tnname"] . " - " . $contro->GetMonth($monat) . " " . $year . " Aktivität</h3>");
		?>
		<hr>
		<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
		<?php
			echo("<select name='monat' id='monthselect'>");
			for($i = 1; $i < 13; $i++)
			{
				if($i == $dt->get_month())
				{
					echo("<option selected='selected'>" . $contro->getMonth($i) . "</option>");
				}
				else
				{
					echo("<option>" . $contro->getMonth($i) . "</option>");
				}
			}
			echo("</select>");
			echo("<select name='yearselect' id='yearselect'>");
			for($i = 2018; $i < 2038; $i++)							
			{
				if($i == $dt->get_year())
				{
					echo("<option selected='Selected'>" . $i . "</option>");
				}
				else
				{
					echo("<option>" . $i . "</option>");
				}
			}
			echo("</select>");
			echo("<input type='submit' name='change_Calendar' id='ccbtn' value='Aktualisieren'>");
			echo("<div id='calendar'>");

				$j = 1;
				?>
					<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
				<?php
				$monthDays = $contro->GetMonthDays($_SESSION["monat"]);
				$isSchalt = $contro->IfSchalt($year);
				if($isSchalt == 1)
				{
					if($monat == 2)
					{
						$monthDays++;
					}
				}
				$conn = mysqli_connect($conndata["server"], $conndata["admin"], $conndata["pass"], $conndata["db"]);
				$sql = "SELECT * FROM teilnehmer WHERE rfid='" . $selected_user . "'";
				$query = mysqli_query($conn, $sql);
				$result = mysqli_fetch_assoc($query);
				$tnid = $result["id"];
				for($i = 0; $i < $monthDays; $i++)
				{
					$sql = "SELECT * FROM anwesenheit WHERE id_tn='" . $tnid . "' AND Datum='" . $j . "." . $monat . "." . $year . "'";
					$query = mysqli_query($conn, $sql);
					$result = mysqli_fetch_assoc($query);
					$dayname =  $contro->Translator(date("l", mktime(0,0,0,$monat,$j,$year)));
					echo("<div id='test'>");
					if(($dayname == "Samstag") || ($dayname == "Sonntag"))
					{
						echo("<input type='submit' name='daybtn" . $j . "' value='" . $j ."' class='dcalendarnone' title='" . $dayname . ": Wochenende'>");
					}
					else if($result["status"] == "bad")
					{
						$gestime = $contro->TimeResolution($result["gekommen"], $result["gegangen"]);
						$monthbadtime += $gestime;
						echo("<input type='submit' name='daybtn" . $j . "' value='" . $j ."' class='dcalendarbad' title='"  . $dayname . ": " .  $gestime . " Minuten zu Spät'>");
					}
					else if($result["status"] == "normal")
					{
						echo("<input type='submit' name='daybtn" . $j . "' value='" . $j ."' class='dcalendargood' title='" . $dayname . ": Keine Fehlzeit'>");
					}
					else if($result["status"] == "exception")
					{
						echo("<input type='submit' name='daybtn" . $j . "' value='" . $j ."' class='dcalendarexcep' title='" . $dayname . ": Entschuldigt'>");
					}
					else
					{
						echo("<input type='submit' name='daybtn" . $j . "' value='" . $j . "' class='dcalendarnormal' title='" . $dayname . "'>");
					}
					echo("<input type='submit' name='dayaddcal" . $j . "' value='+' class='addTerminbtn' title='Termin hinzufügen'></div>");
					$j++;
				}
				mysqli_close($conn);
			?>
			</form>
		</div>
		<table id="tnstats">
			<tr>
				<td>
					<p>Gesamte Fehlzeit:</p>
				</td>
				<td>
					<?php
					echo("<p>" . $monthbadtime . " Minuten zu Spät</p>");
					?>
				</td>
			</tr>
			<tr>
				<td>
					<p>ID:</p>
				</td>
				<td>
					<p><?php echo($selected_user) ?></p>
				</td>
			</tr>
		</table>
	</div>
	<div id="rembar">
		<h2 style="text-align: center;">Heutige Erinnerungen</h2>
		<hr>
		<div id="remline">
		<?php
			$datenow = $dt->get_day() . "." . $dt->get_month() . "." . $dt->get_year();
			$conn = mysqli_connect($conndata["server"], $conndata["admin"], $conndata["pass"], $conndata["db"]);
			$sql = "SELECT * FROM termine WHERE datum='" . $datenow . "'";
			$query = mysqli_query($conn, $sql);
			while($rows = mysqli_fetch_assoc($query))
			{	
				echo("<p style='cursor: help;' title='" . $rows["nachricht"] . "'><u>" . $contro->GetTN($rows["tn_id"]) . " - " . $rows["betreff"] . " (" . $rows["zeit"] . ") Uhr</u></p>");
			}
		?>
		</div>
	</div>
	<?php
	echo("</div>");
	echo("</div>");
	}
		if(isset($_POST["newtn"]))
		{
			echo("<div id='blackback'></div>");
			echo("<div id='popwindow2'>");
			echo("<h2>Teilnehmer hinzufügen</h2><hr>");
			?>
			<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
			<?php
			echo("<input type='text' name='tnvor' id='tnvor_txt' placeholder='Vorname'>");
			echo("<input type='text' name='tnnach' id='tnnach_txt' placeholder='Nachname'>");
			echo("<select name='ausgr' id='ausgr'");
			$conn = mysqli_connect($conndata["server"], $conndata["admin"], $conndata["pass"], $conndata["db"]);
			$sql = "SELECT * FROM ausbildungsgruppen WHERE 1";
			$query = mysqli_query($conn, $sql);
			while($result = mysqli_fetch_assoc($query))
			{
				echo("<option>" . $result["krz"] . "</option>");
			}
			mysqli_close($conn);
			echo("</select>");
			echo("<input type='text' name='rfidnewtn' id='ntnrfid' placeholder='RFID-CODE'>");
			echo("<input type='submit' name='back'  id='tnnewback' value='Zurück'>");
			echo("<input type='submit' name='create_new_tn'  id='tnnewaccp' value='Hinzufügen'>");
		}
		if(isset($_POST["showtn"]))
		{
			echo("<div id='blackback'></div>");
			echo("<div id='popwindow'>");
			echo("<h2>Teilnehmer</h2><hr>");
			echo("<div id='scrolldiv'>");
			$conndata = $contro->GetServerData();
			$conn = mysqli_connect($conndata["server"], $conndata["admin"], $conndata["pass"], $conndata["db"]);
			$sql = "SELECT * FROM teilnehmer WHERE 1";
			$query = mysqli_query($conn, $sql);
			while($result = mysqli_fetch_assoc($query))
			{
				?>
				<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
				<?php
				echo("<input type='submit' name='tnclick' class='tnbtn' value='" . $result["vorname"] . " " . $result["nachname"] . "'>");
				echo("<input type='text' name='tn' value='" . $result["rfid"] . "' style='display: none;'>");
				echo("</form>");
			}
			mysqli_close($conn);
			echo("</div>");
			?>
				<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post">
			<?php
			echo("<input type='submit' name='back' class='btn' value='Zurück' style='position: absolute; padding: 8px 32px; left: 40%; bottom: 1%;'></form>");
		}
		/*
		*	Hier ist der Code für die Einzelnen Tage
		*/
		switch(true)											//Analyse per Switch welcher Tag angeklickt wurde
		{
			case isset($_POST["daybtn1"]): 
				$contro->GetDay($tnid, "1", $monat, $year);		//Methodenaufruf und übergabe der Parameter für den gewählten Tag
				break;
			case isset($_POST["daybtn2"]):
				$contro->GetDay($tnid, "2", $monat, $year);
				break;
			case isset($_POST["daybtn3"]):
				$contro->GetDay($tnid, "3", $monat, $year);
				break;
			case isset($_POST["daybtn4"]):
				$contro->GetDay($tnid, "4", $monat, $year);
				break;
			case isset($_POST["daybtn5"]):
				$contro->GetDay($tnid, "5", $monat, $year);
				break;
			case isset($_POST["daybtn6"]):
				$contro->GetDay($tnid, "6", $monat, $year);
				break;
			case isset($_POST["daybtn7"]):
				$contro->GetDay($tnid, "7", $monat, $year);
				break;
			case isset($_POST["daybtn8"]):
				$contro->GetDay($tnid, "8", $monat, $year);
				break;
			case isset($_POST["daybtn9"]):
				$contro->GetDay($tnid, "9", $monat, $year);
				break;
			case isset($_POST["daybtn10"]):
				$contro->GetDay($tnid, "10", $monat, $year);
				break;
			case isset($_POST["daybtn11"]):
				$contro->GetDay($tnid, "11", $monat, $year);
				break;
			case isset($_POST["daybtn12"]):
				$contro->GetDay($tnid, "12", $monat, $year);
				break;
			case isset($_POST["daybtn13"]):
				$contro->GetDay($tnid, "13", $monat, $year);
				break;
			case isset($_POST["daybtn14"]):
				$contro->GetDay($tnid, "14", $monat, $year);
				break;
			case isset($_POST["daybtn15"]):
				$contro->GetDay($tnid, "15", $monat, $year);
				break;
			case isset($_POST["daybtn16"]):
				$contro->GetDay($tnid, "16", $monat, $year);
				break;
			case isset($_POST["daybtn17"]):
				$contro->GetDay($tnid, "17", $monat, $year);
				break;
			case isset($_POST["daybtn18"]):
				$contro->GetDay($tnid, "18", $monat, $year);
				break;
			case isset($_POST["daybtn19"]):
				$contro->GetDay($tnid, "19", $monat, $year);
				break;
			case isset($_POST["daybtn20"]):
				$contro->GetDay($tnid, "20", $monat, $year);
				break;
			case isset($_POST["daybtn21"]):
				$contro->GetDay($tnid, "21", $monat, $year);
				break;
			case isset($_POST["daybtn22"]):
				$contro->GetDay($tnid, "22", $monat, $year);
				break;
			case isset($_POST["daybtn23"]):
				$contro->GetDay($tnid, "23", $monat, $year);
				break;
			case isset($_POST["daybtn24"]):
				$contro->GetDay($tnid, "24", $monat, $year);
				break;
			case isset($_POST["daybtn25"]):
				$contro->GetDay($tnid, "25", $monat, $year);
				break;
			case isset($_POST["daybtn26"]):
				$contro->GetDay($tnid, "26", $monat, $year);
				break;
			case isset($_POST["daybtn27"]):
				$contro->GetDay($tnid, "27", $monat, $year);
				break;
			case isset($_POST["daybtn28"]):
				$contro->GetDay($tnid, "28", $monat, $year);
				break;
			case isset($_POST["daybtn29"]):
				$contro->GetDay($tnid, "29", $monat, $year);
				break;
			case isset($_POST["daybtn30"]):
				$contro->GetDay($tnid, "30", $monat, $year);
				break;
			case isset($_POST["daybtn31"]):
				$contro->GetDay($tnid, "31", $monat, $year);
				break;
		}
		
		switch(true)											//Analyse per Switch welcher Tag angeklickt wurde
		{
			case isset($_POST["dayaddcal1"]): 
				$contro->addTermin($tnid, "1", $monat, $year);		//Methodenaufruf und übergabe der Parameter für den gewählten Tag
				break;
			case isset($_POST["dayaddcal2"]):
				$contro->addTermin($tnid, "2", $monat, $year);
				break;
			case isset($_POST["dayaddcal3"]):
				$contro->addTermin($tnid, "3", $monat, $year);
				break;
			case isset($_POST["dayaddcal4"]):
				$contro->addTermin($tnid, "4", $monat, $year);
				break;
			case isset($_POST["dayaddcal5"]):
				$contro->addTermin($tnid, "5", $monat, $year);
				break;
			case isset($_POST["dayaddcal6"]):
				$contro->addTermin($tnid, "6", $monat, $year);
				break;
			case isset($_POST["dayaddcal7"]):
				$contro->addTermin($tnid, "7", $monat, $year);
				break;
			case isset($_POST["dayaddcal8"]):
				$contro->addTermin($tnid, "8", $monat, $year);
				break;
			case isset($_POST["dayaddcal9"]):
				$contro->addTermin($tnid, "9", $monat, $year);
				break;
			case isset($_POST["dayaddcal10"]):
				$contro->addTermin($tnid, "10", $monat, $year);
				break;
			case isset($_POST["dayaddcal11"]):
				$contro->addTermin($tnid, "11", $monat, $year);
				break;
			case isset($_POST["dayaddcal12"]):
				$contro->addTermin($tnid, "12", $monat, $year);
				break;
			case isset($_POST["dayaddcal13"]):
				$contro->addTermin($tnid, "13", $monat, $year);
				break;
			case isset($_POST["dayaddcal14"]):
				$contro->addTermin($tnid, "14", $monat, $year);
				break;
			case isset($_POST["dayaddcal15"]):
				$contro->addTermin($tnid, "15", $monat, $year);
				break;
			case isset($_POST["dayaddcal16"]):
				$contro->addTermin($tnid, "16", $monat, $year);
				break;
			case isset($_POST["dayaddcal17"]):
				$contro->addTermin($tnid, "17", $monat, $year);
				break;
			case isset($_POST["dayaddcal18"]):
				$contro->addTermin($tnid, "18", $monat, $year);
				break;
			case isset($_POST["dayaddcal19"]):
				$contro->addTermin($tnid, "19", $monat, $year);
				break;
			case isset($_POST["dayaddcal20"]):
				$contro->addTermin($tnid, "20", $monat, $year);
				break;
			case isset($_POST["dayaddcal21"]):
				$contro->addTermin($tnid, "21", $monat, $year);
				break;
			case isset($_POST["dayaddcal22"]):
				$contro->addTermin($tnid, "22", $monat, $year);
				break;
			case isset($_POST["dayaddcal23"]):
				$contro->addTermin($tnid, "23", $monat, $year);
				break;
			case isset($_POST["dayaddcal24"]):
				$contro->addTermin($tnid, "24", $monat, $year);
				break;
			case isset($_POST["dayaddcal25"]):
				$contro->addTermin($tnid, "25", $monat, $year);
				break;
			case isset($_POST["dayaddcal26"]):
				$contro->addTermin($tnid, "26", $monat, $year);
				break;
			case isset($_POST["dayaddcal27"]):
				$contro->addTermin($tnid, "27", $monat, $year);
				break;
			case isset($_POST["dayaddcal28"]):
				$contro->addTermin($tnid, "28", $monat, $year);
				break;
			case isset($_POST["dayaddcal29"]):
				$contro->addTermin($tnid, "29", $monat, $year);
				break;
			case isset($_POST["dayaddcal30"]):
				$contro->addTermin($tnid, "30", $monat, $year);
				break;
			case isset($_POST["dayaddcal31"]):
				$contro->addTermin($tnid, "31", $monat, $year);
				break;
		}
	?>
});
</body>
</html>