<?php
class controller
{
	//Verbindungsvariablen
	private $hostname = "localhost";
	private $admin = "root";
	private $pw = "";
	private $db = "adminpage";
	
	public function Login($user, $password)																//Methode für Login
	{
		$passhash = hash("sha512", $password);															//Das Übergebene Passwort wird gehasht mit SHA512
		$conn = mysqli_connect($this->hostname, $this->admin, $this->pw, $this->db);					//Aufbauen einer verbindung zur Datenbank
		$sql = "SELECT * FROM user WHERE username='" . $user . "' AND pwhash='" . $passhash . "'";		//Select String
		$query = mysqli_query($conn, $sql);																//Ausführen der SQL abfrage
		$result = mysqli_num_rows($query);																//Ermitteln wie viele Treffer sich ergaben
		if($result > 0)																					//Wenn Ergebnis nicht 0 
		{
			session_start;																					//Dann Starte Session
			$_SESSION["login"] = $username;																	//Speichere Username in Session Arrasy
			return 0;																						//Gebe 0 Zurück
		}
		else																							//Wenn Ergebnis 0
		{
			return 1;																						//Gebe 1 Zurück
		}
		mysqli_close($conn);																			//Schließe die Datenbankverbindung
	}
	
	public function CreateNewTN($vorname, $nachname, $gruppe, $rfid)																								//Methode zum erstellen eines neuen Teilnehmers
	{
		$conn = mysqli_connect($this->hostname, $this->admin, $this->pw, $this->db);																				//Aufbau der Datenbankverbindung
		$sql = "INSERT INTO teilnehmer (vorname, nachname, ausbildung, rfid) VALUES ('" . $vorname . "','" . $nachname . "','" . $gruppe . "','" . $rfid . "')";	//Insert String
		$query = mysqli_query($conn, $sql);																															//SQL Ausführung (Erstellung des neuen Teilnehmers)
	}
	
	public function TimeResolution($gekommen, $gegangen)	//Methode zur berechnung der Fehlzeiten
	{
		for($i = 0; $i < strlen($gekommen); $i++)			//Schleife die so lange läuft wie $gekommen stellen hat
		{
			switch($gekommen[$i])							//Switch zur Prüfung der einzelnen zeichen
			{
				case ':':									//Wenn : dann ersetze mit .
					$gekommen[$i] = '.';					// . ersetzt :
					break;
				default: 
					break;
			}
		}
		for($i = 0; $i < strlen($gegangen); $i++)			//Schleife die so lange läuft wie $gegangen stellen hat
		{
			switch($gegangen[$i])							//Switch zur prüfung der einzelnen Stellen
			{
				case ':':									//Wenn : dann ersetze mit .
					$gegangen[$i] = '.';					// . ersetzt :
					break;
				default:
					break;
			}
		}
		$difftime = $gegangen - $gekommen;					//Differenzzeit Zwischen gekommen und gegangen wird berechnet
		$difftime -= 8.30;
		$difftime = $difftime - ($difftime * 2);			//Wert wird Negiert (von Negativ zu Positiv)
		$diffstring = (string)$difftime;					//Differenzwert wird als String übergeben an String variable
		$minuten = $diffstring[0] * 60;						//Berechnung der Zeit in Minuten
		$minuten += ($difftime - $diffstring[0]) * 100;
		return $minuten;									//Rückgabe der gesamten Fehlminuten
	}
	
	public function GetServerData()			//Methode zum Holen der Serverdaten
	{
		$connection = [						//Connection Array das die Serverdatenbeinhaltet
			"server" => $this->hostname,	//Hostname wird übergeben
			"admin" => $this->admin,		//adminname wird übergeben
			"pass" => $this->pw,			//Passwort wird Übergeben
			"db" => $this->db,				//Datenbankname wird übergeben
		];
		return $connection;					//Array wird zurückgegeben
	}
	
	public function SendTCP()																			//Methode zum Senden von Daten über TCP
	{
		$tcp = fsockopen("tcp://127.0.0.1", 42000, $errno, $errstr, 30);								//Verbindungsaufbau zu Ziel Computer
		if(!$tcp)																						//Wenn Verbindung Fehlgeschlagen
		{
			echo($errstr . "(" . $errno . ")<br>\n");														//Gebe Errormeldung aus
		}
		else																							//Wenn Verbindung erfolgreich
		{
			$out = "{\n \"Username\": \"Helga\" \n \"PasswordHash\": \"dx818eufj2u3urf783zf7u3\" \n}";		//Sende String
			fwrite($tcp, $out);																					//Sende daten ab
			fclose($tcp);																						//Schließe die Verbindung
		}
	}
	
	public function Translator($word)	//Methode zur Wortübersetzung
	{
		switch($word)
		{
			case "Monday":
				$word = "Montag";
				break;
			case "Tuesday":
				$word = "Dienstag";
				break;
			case "Wednesday":
				$word = "Mittwoch";
				break;
			case "Thursday":
				$word = "Donnerstag";
				break;
			case "Friday":
				$word = "Freitag";
				break;
			case "Saturday":
				$word = "Samstag";
				break;
			case "Sunday":
				$word = "Sonntag";
				break;
		}
		return $word;
	}
	
	public function GetMonth($zahl)		//Methode zur Monatsauflösung aus Monats Zahlen
	{
		switch($zahl)
		{
			case 1: 
				$zahl = "Jannuar";
				break;
			case 2: 
				$zahl = "Februar";
				break;
			case 3: 
				$zahl = "März";
				break;
			case 4: 
				$zahl = "April";
				break;
			case 5: 
				$zahl = "Mai";
				break;
			case 6: 
				$zahl = "Juni";
				break;
			case 7: 
				$zahl = "Juli";
				break;
			case 8: 
				$zahl = "August";
				break;
			case 9: 
				$zahl = "September";
				break;
			case 10: 
				$zahl = "Oktober";
				break;
			case 11: 
				$zahl = "November";
				break;
			case 12: 
				$zahl = "Dezember";
				break;
		}
		return $zahl;
	}
	
	public function GetTN($tnid)
	{
		$conn = mysqli_connect($this->hostname, $this->admin, $this->pw, $this->db);
		$sql = "SELECT * FROM teilnehmer WHERE id='" . $tnid . "'";
		$query = mysqli_query($conn, $sql);
		$result = mysqli_fetch_assoc($query);
		$tn = $result["vorname"] . " " . $result["nachname"];
		mysqli_close($conn);
		return $tn;
	}
	
	public function AddTermin($id, $day, $month, $year)
	{
		echo("<div id='blackback'></div>");
		echo("<div id='popwindow'>");
		echo("<h1>Neuen Termin hinzufügen</h1><hr>");
		echo("<form action=" . $_SERVER["PHP_SELF"] .  " method='post'>");
		echo("<input type='text' name='termheader' id='terminheader' placeholder='Betreff'>");
		echo("<textarea name='content' id='contentbx' placeholder='Informationen zum Termin'></textarea>");
		echo("<div id='termtimer'>");
		echo("<select name='termhour'>");
		echo("<option>Stunde</option>");
		for($i = 0; $i < 25; $i++)
		{
			echo("<option>" . $i . "</option>");
		}
		echo("</select>");
		echo("<select name='termminute'>");
		echo("<option>Minute</option>");
		for($i = 0; $i < 60; $i++)
		{
			if($i < 10)
			{
				echo("<option>0" . $i . "</option>");
			}
			else
			{
				echo("<option>" . $i . "</option>");
			}
		}
		echo("</select>");
		echo(" - " . $day . "." . $month . "." . $year);
		echo("</div>");
		echo("<input type='submit' name='back' id='termback' value='Zurück'>");
		echo("<input type='submit' name='addTermin' id='aterm' value='Eintragen'>");
		echo("<input type='text' name='date' class='hidden' value='" . $day . "." . $month . "." . $year . "'>");
		echo("<input type='text' name='user' class='hidden' value='" . $id . "'>");
		echo("</form>");
		echo("</div>");
	}
	
	public function GetDay($id, $day, $month, $year)																														//Methode zur ausgabe des gewählten Tages
	{
		$conndata = $this->GetServerData();																																	//Hole Server Daten
		$conn = mysqli_connect($conndata["server"], $conndata["admin"], $conndata["pass"], $conndata["db"]);																//Baue Verbindung zur Datenbank auf
		$sql = "SELECT * FROM anwesenheit WHERE id_tn='" . $id . "' AND datum='" . $day . "." . $month . "." . $year . "'";													//Select String
		$query = mysqli_query($conn, $sql);																																	//Ausführung des SQL Befehls
		$data = mysqli_fetch_assoc($query);																																	//Speichern der geholten Daten
		//$_SESSION["idfusr"] = $tnid;																																		//Speichern der Teilnehmer id in Session Variable
		$Teilnehmer = $_SESSION["tnname"];																																	//übergabe des Teilnehmernamens in Variable von Session Variable
		$gekommen = $data["gekommen"];																																		//übergabe der Anfangszeit in Variable von Session Variable 
		$gegangen = $data["gegangen"];																																		//übergabe der Endzeit in Variable von Session Variable
		if($data["status"] == "bad")																																		//Wenn Status bad = zu spät
		{
			$fehlzeit = $this->TimeResolution($data["gekommen"], $data["gegangen"]);																							//Berechne Fehlzeit für Tag
		}
		else																																								//Wenn Status nicht Bad
		{
			$fehlzeit = "0";																																					//Setze fehlzeit auf 0
		}
		echo("<div id='blackback'></div>");
		echo("<div id='popwindow'>");
		echo("<h2 id='dayname'>" . $this->Translator(date("l", mktime(0,0,0,$month,$day,$year))) . " " . $day . "." . $this->GetMonth($month) . " " . $year . "</h2><hr>");
		echo("<form action='" . $_SERVER["PHP_SELF"] . "' method='post'>");
		echo("<h2>" . $Teilnehmer . "</h2>");
		echo("<h3>Gekommen: " . $gekommen . " Uhr</h3>");
		echo("<h3>Gegangen: " . $gegangen . " Uhr</h3>");
		echo("<h3>Fehlzeit: " . $fehlzeit . " Minuten</h3><hr>");
		if($data["status"] == "bad")
		{
			echo("<h3>Ausnahme hinzufügen</h3>");
			echo("<select name='exception' id='excep_sel'>");
			echo("<option>Krank</option><option>Termin</option>");
			echo("</select>");
			echo("<input type='text' name='user' value='" . $id . "' class='hidden'>");
			echo("<input type='text' name='date' value='" . $day  . "." . $month . "." . $year . "' class='hidden'>");
			echo("<input type='submit' name='add_exception' id='btn_excep' value='Eintragen'><hr>");
		}
		else if($data["status"] == "exception")
		{
			echo("<h3>Ausnahme entfernen</h3>");
			echo("<input type='text' name='user' value='" . $id . "' class='hidden'>");
			echo("<input type='text' name='date' value='" . $day  . "." . $month . "." . $year . "' class='hidden'>");
			echo("<input type='submit' name='del_exception' id='btn_excep' value='Entfernen'><hr>");
		}
		echo("<input type='submit' name='back' class='btn' style='padding: 16px 32px;' value='Zurück'>");
		echo("</form>");
		echo("</div>");
	}
	
	public function GetMonthDays($month)	//Methode zur Analyse der gesamten Tage Einzelner Monate
	{
		switch($month)
		{
			case 1:				//Jannuar
				$month = 31;
				break;
			case 2:				//Februar
				$month = 28;
				break;
			case 3:				//März
				$month = 31;
				break;
			case 4:				//April
				$month = 30;
				break;
			case 5:				//Mai
				$month = 31;
				break;
			case 6:				//Juni
				$month = 30;
				break;
			case 7:				//Juli
				$month = 31;
				break;
			case 8:				//August
				$month = 31;
				break;
			case 9:				//September
				$month = 30;
				break;
			case 10:			//Oktober
				$month = 31;
				break;
			case 11:			//November
				$month = 30;
				break;
			case 12:			//Dezember
				$month = 31;
				break;
		}
		return $month;
	}
	
	public function IfSchalt($jahr)		//Methode zum ermitteln von Schaltjahren
	{
		$schalt = 0;					//
		$schritte = 0;					//
		$sjahr = 2016;					//Variable mit Start Jahr
		while($schritte < 30)			//While Schleife die 30 Schritte geht
		{
			if($jahr == $sjahr)			//Wenn Aktuelles Jahr gleich sJahr dann ist es ein Schaltjahr
			{
				$schalt = 1;			//Schalt Variable wird um 1 erhöht
			}
			$schritte++;				//Schritte wird um 1 erhöht
			$sjahr += 4;				//sJahr wird um 4 erhöht (Weil Schaltjahr alle 4 Jahre)
		}
		return $schalt;					//Rückgabe des Schalt wertes
	}
	
	public function GetMonthNum($month)
	{
		switch($month)
		{
			case "Jannuar":
				$month = 1;
				break;
			case "Februar":
				$month = 2;
				break;
			case "März":
				$month = 3;
				break;
			case "April":
				$month = 4;
				break;
			case "Mai":
				$month = 5;
				break;
			case "Juni":
				$month = 6;
				break;
			case "Juli":
				$month = 7;
				break;
			case "August":
				$month = 8;
				break;
			case "September":
				$month = 9;
				break;
			case "Oktober":
				$month = 10;
				break;
			case "November":
				$month = 11;
				break;
			case "Dezember":
				$month = 12;
				break;
		}
		return $month;
	}
	
	public function test()
	{
		echo("Funzt");
	}
}