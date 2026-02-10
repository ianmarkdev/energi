<?php

session_start();

if(isset($_GET['logout'])) {
	
    if(isset($_SESSION['user_id'])) {
	    
        $_SESSION = array();
        session_destroy();
    }

    header('location: Anmeldung');
    exit();
    
}

if(isset($_GET["clearcart"])) {
	unset($_SESSION['cart']);
}

 	if(isset($_SESSION["user_id"])) {
	 	$cur_SessionID = $_SESSION["user_id"];
	 	
	 	$get_UserData = $SQL -> prepare('SELECT permission, admin, full_name, email_address, language FROM kunden WHERE id = ?');
	 	$get_UserData -> bind_param('i', $_SESSION["user_id"]);
	 	$get_UserData -> execute();
	 	$get_UserData -> store_result();
	 	$get_UserData -> bind_result($get_User_Permission, $get_User_Admin, $get_User_FullName, $get_User_EmailAddress, $get_User_Language);
	 	$get_UserData -> fetch();
		
		$is_Logged_In = TRUE;
		
		/*
		$get_UserDaten = $SQL -> prepare('SELECT name, anschrift, stadt, plz, staat, familienstand, mobilnummer, geburtsdatum FROM kunden_daten WHERE user_id = ? LIMIT 1');
	 	$get_UserDaten -> bind_param('i', $_SESSION['user_id']);
	 	$get_UserDaten -> execute();
	 	$get_UserDaten -> store_result();
	 	$get_UserDaten -> bind_result($get_User_Name, $get_User_Anschrift, $get_User_Stadt, $get_User_PLZ, $get_User_Staat, $get_User_Familienstand, $get_User_Mobilnummer, $get_User_Geburtsdatum);
		$get_UserDaten -> fetch();
						
		if($get_User_Anschrift == "none") {
			$has_Full_Details = FALSE;
		} else {
			$has_Full_Details = TRUE;
			$current_User_Geburtsdatum = new DateTime($get_User_Geburtsdatum);
			$new_User_Geburtsdatum = $current_User_Geburtsdatum->format('Y-m-d');
		}
		
	 					
	 	//ZUNAME RAUSFISCHEN
	 	$split_Name_Parts = explode(" ", $get_User_Name);
	 	$get_User_Zuname = array_pop($split_Name_Parts);
		*/
		
		$get_User_Anschrift = $SQL->prepare("SELECT anrede, vorname, nachname, rufnummer, email_adresse, postleitzahl, ort, strasse FROM kunden_anschrift WHERE user_id = ?");
		$get_User_Anschrift->bind_param("i", $_SESSION["kunden_id"]);
		$get_User_Anschrift->execute();
		$get_User_Anschrift->store_result();
		$get_User_Anschrift->bind_result($this_Data_Anrede, $this_Data_Vorname, $this_Data_Nachname, $this_Data_Rufnummer, $this_Data_Email, $this_Data_Postleitzahl, $this_Data_Ort, $this_Data_Strasse);
		$get_User_Anschrift->fetch();
		
	} else if(!isset($_SESSION["user_id"])) {
		//CUSTOMER IS NOT LOGGED IN
		$is_Logged_In = FALSE;
	} else {
		$is_Logged_In = FALSE;
	}

?>