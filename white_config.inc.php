<?php

	//WHITERUNNER - HEIZÖL SHOP
	//CONFIGURATION FILE
	//08.08.2025

	/*
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	error_reporting(0);
	ini_set('display_errors', 0);
	*/
	
	error_reporting(E_ALL);
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
	ini_set('error_log', __DIR__ . '/error.log');

	$SQL_host = "localhost";
	$SQL_username = "energiepreise24";
	$SQL_password = "5etVu323?";
	$SQL_database = "energiepreise24";
	// $SQL_username = "root";
	// $SQL_password = "";
	// $SQL_database = "heizoel_energie";

	$appSecurityToken = "bfFgPxtjwSBgEaGvzvHiLJYGMqLIHJvPLJVJZmhZjrGKgksYZvCxGUTbfFgPxtjwSBgEaGvzvHiLJYGMqLIHJvPLJVJZmhZjrYZvCxGUT";
	
	date_default_timezone_set("Europe/Berlin");
	$appTimestamp = time();
	$appCurrentDate = date("d.m.Y", $appTimestamp);
	$appCurrentTime = date("H:i", $appTimestamp);
	$createdAt = $appCurrentDate." - ".$appCurrentTime." Uhr";
	
	$SQL = new MySQLi($SQL_host, $SQL_username, $SQL_password, $SQL_database);

	if(mysqli_connect_errno() != 0 || !$SQL->set_charset('utf8')) {
		die('SQL-Verbindung nicht möglich! Daten überprüfen!');
	}

	$cur_Date = date('Y-m-d');
	$cur_Time = date('H:i:s');
	$cur_Timestamp = $cur_Date." ".$cur_Time;
	
	
	$get_HauptDaten = $SQL -> prepare('SELECT titel, seite_slogan, domain, ceo, firma, strasse, plz, ort, land, telefon, email, gericht, nummer, steuernummer, mails_active, mails_to, logo_dark, logo_light, favicon, theme_color, smtp_active, smtp_host, smtp_user, smtp_pass, smtp_type, smtp_port, bd_name, bd_iban, bd_bic, first_price, second_price, third_price, pdf_logo, main_color, secondary_color, base_url, hero_bg, google_tag, conversion_tag, additional_tag, enable_sepa, enable_invoice FROM einstellungen LIMIT 1');
	$get_HauptDaten -> execute();
	$get_HauptDaten -> store_result();
	$get_HauptDaten -> bind_result($cur_Title, $cur_SeiteSlogan, $cur_Domain, $cur_CEO, $cur_Firma, $cur_Strasse, $cur_PLZ, $cur_Ort, $cur_Land, $cur_Telefon, $cur_Mail, $cur_Gericht, $cur_Nummer, $cur_Steuernummer, $cur_MailsActive, $cur_MailsTo, $cur_LogoDark, $cur_LogoLight, $cur_Favicon, $cur_ThemeColor, $cur_SMTPActive, $cur_SMTPHost, $cur_SMTPUser, $cur_SMTPPass, $cur_SMTPType, $cur_SMTPPort, $cur_BDName, $cur_BDIBAN, $cur_BDBIC, $cur_FirstPrice, $cur_SecondPrice, $cur_ThirdPrice, $cur_PDFLogo, $cur_MainColor, $cur_SecondaryColor, $cur_BaseURL, $cur_HeroBG, $cur_GoogleTag, $cur_ConversionTag, $cur_AdditionalTag, $cur_EnableSEPA, $cur_EnableInvoice);
	$get_HauptDaten -> fetch();
	
	//FETCH CURRENT ACTIVE BD-NAME
	$fetch_BD_CEOName = $SQL->prepare("SELECT bd_agent, bd_name, bd_iban, bd_bic FROM bd_auswahl WHERE status = 1 LIMIT 1");
	$fetch_BD_CEOName->execute();
	$fetch_BD_CEOName->store_result();
	$fetch_BD_CEOName->bind_result($cur_BD_Sys_Agent, $cur_BD_Sys_Name, $cur_BD_Sys_IBAN, $cur_BD_Sys_BIC);
	$fetch_BD_CEOName->fetch();

	// $cur_BaseURL = 'http://localhost/energi/';
  $cur_Domain = $cur_Domain ?? 'https://heizoel-energie.de/';
  $parsedUrl = parse_url($cur_Domain);
  $site_name = $parsedUrl['host'];
	
	//GLOBAL CONFIG
	$this_BaseURL = $cur_BaseURL;
	define('BASE_URL', $cur_BaseURL);
	$this_MainColor = $cur_MainColor;
	$this_SecColor = $cur_SecondaryColor;
	$this_HeroBG = BASE_URL.$cur_HeroBG;
	//$this_PageBG = BASE_URL."assets/images/page-bg-3.jpg";
	$this_PageBG = BASE_URL.$cur_HeroBG;
	$global_GGVS = 42.59;
	
	//SMTP ADDITION
	$new_SMTPHost = $cur_SMTPHost;
	$new_SMTPUser = $cur_SMTPUser;
	$new_SMTPPass = $cur_SMTPPass;
	$cur_SMTPType = $cur_SMTPType;
	$cur_SMTPPort = $cur_SMTPPort;
	
	$cur_PageTitle = $cur_Title;
	$cur_FileName = basename($_SERVER['PHP_SELF']);
	 	
 	$cur_Datum = date("d.m.Y");
		
	$curDatum = date('Y-m-d');
	$curDatum_Format = date("d.m.Y", strtotime($curDatum));
	$cur_TimeMinimal = date('H:i');
	$energy_CurDate = $curDatum_Format.", ".$cur_TimeMinimal." Uhr";
		
	//INDEX BREAK FUNCTION
	function addBrAfterFirstWord($input) {
		if (preg_match('/^(\w+(?:-\w+)*)(.*)$/', $input, $matches)) {
			return $matches[1] . '<br>' . $matches[2];
		}
		return $input;
	}
	
	function hasThirtyOrMoreChars($input) {
		if ($input >= 30) {
			return true;
		} else {
			return false;
		}
	}
	
	function removeHtmlAndSummarize($input, $summaryLength = 50) {
		$cleanInput = strip_tags($input);

		$words = explode(' ', $cleanInput);

		if (count($words) <= $summaryLength) {
			return $cleanInput;
		}

		$summary = implode(' ', array_slice($words, 0, $summaryLength));

		return $summary . '...';
	}
	
	function formatNumber($value) {
		$formattedValue = number_format($value, 2, ',', '.');
		return $formattedValue;
	}
	
	if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
    	$getIPAddress = $_SERVER['HTTP_CLIENT_IP'];
	} else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    	$getIPAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
    	$getIPAddress = $_SERVER['REMOTE_ADDR'];
	}
	
	$is_File_Name = basename($_SERVER['PHP_SELF']);
	
	function generateRandomPassword($length = 20) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomPassword = '';
    
		for ($i = 0; $i < $length; $i++) {
			$randomPassword .= $characters[rand(0, $charactersLength - 1)];
		}
    
		return $randomPassword;
	}
	
	function cleanDomain($url) {
		$cleanedUrl = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $url);

		$domain = strtok($cleanedUrl, '/');

		return $domain;
	}
	
	function transformDecimalPoint($number) {
		$transformedNumber = str_replace('.', ',', $number);
		return $transformedNumber;
	}
	
	
	function convertToList($multilineString) {
		$lines = preg_split('/\r\n|\r|\n/', $multilineString);

		$output = "<ul>\n";

		foreach($lines as $line) {
			$line = trim($line);
			if(!empty($line)) {
				$output .= "<li>" . nl2br(htmlspecialchars($line)) . "</li>\n";
			}
		}

		$output .= "</ul>\n";

		return $output;
	}
	
	function convertCartToList(array $cart): string
	{
    if(empty($cart)) {
        return '<p>Ihr Warenkorb ist leer.</p>';
    }

    $html  = '<ul>';
    foreach($cart as $item) {
        $line = htmlspecialchars($item['name'], ENT_QUOTES);

        if($item['type'] === 'main') {
            $liter = number_format($item['quantity'], 0, ',', '.') . 'L';
            $stellen = $item['lieferstellen'] . ' Lieferstellen';
            $preis = number_format($item['price'], 2, ',', '.') . ' €';

            $line .= " – {$liter} – {$stellen} – {$preis}";
        } else {
            $preis = number_format($item['price'], 2, ',', '.') . ' €';
            $line .= " – – {$preis}";
        }

        $html .= '<li>' . $line . '</li>';
    }
    $html .= '</ul>';

    return $html;
}

function getFilenameFromPath($filePath) {
    return basename($filePath);
}

function replaceUmlauts($text) {
    $umlauts = array(
        'ä' => '&auml;',
        'Ä' => '&Auml;',
        'ö' => '&ouml;',
        'Ö' => '&Ouml;',
        'ü' => '&uuml;',
        'Ü' => '&Uuml;',
        'ß' => '&szlig;'
    );

    return strtr($text, $umlauts);
}

function convertToCents($amount) {
    return (int)round($amount * 100);
}

function formatGermanDateTime($input) {
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $input);

    if (!$date) {
        return "Ungültiges Datum";
    }

    return $date->format('d.m.Y - H:i:s') . ' Uhr';
}

function formatDeliverySlot(string $input): string
{
    [$datePart, $timePart] = explode(' ', $input);
    [$startTime, $endTime] = explode('-', $timePart);

    $dt = DateTime::createFromFormat('Y-m-d', $datePart);
    if(!$dt) return $input;

    $formattedDate = $dt->format('d.m.Y');

    return sprintf('%s, %s - %s Uhr', $formattedDate, $startTime, $endTime);
}

function containsDate(string $input): bool
{
    $patterns = [
        '/\b\d{4}-\d{2}-\d{2}\b/',
        '/\b\d{1,2}\.\d{1,2}\.\d{4}\b/',
        '/\b\d{1,2}\/\d{1,2}\/\d{4}\b/',
        '/\b\d{1,2}\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s+\d{4}\b/i',
        '/\b(January|February|March|April|May|June|July|August|September|October|November|December)\s+\d{1,2},\s+\d{4}\b/i',
        '/\b(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday),?\s+(January|February|March|April|May|June|July|August|September|October|November|December)\s+\d{1,2},\s+\d{4}\b/i',
    ];

    foreach($patterns as $pattern) {
        if(preg_match($pattern, $input)) {
            return true;
        }
    }

    return false;
}



function transformDate($input) {
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $input);

    if(!$date) {
        return "Ungültiges Datum";
    }

    return $date->format('d.m.Y');
}

function cleanHtmlForEmail($html) {
    $replacements = [
        'ä' => '&auml;',
        'ü' => '&uuml;',
        'ö' => '&ouml;',
        'Ä' => '&Auml;',
        'Ü' => '&Uuml;',
        'Ö' => '&Ouml;',
        'ß' => '&szlig;',
        '€' => '&euro;',
        '§' => '&sect;',
        '©' => '&copy;',
        '®' => '&reg;',
        '™' => '&trade;',
        '…' => '&hellip;',
        '„' => '&bdquo;',
        '“' => '&ldquo;',
        '”' => '&rdquo;',
        '‚' => '&sbquo;',
        '‘' => '&lsquo;',
        '’' => '&rsquo;',
        '–' => '&ndash;',
        '—' => '&mdash;',
        '«' => '&laquo;',
        '»' => '&raquo;',
        '•' => '&bull;',
        '†' => '&dagger;',
        '‡' => '&Dagger;',
        '′' => '&prime;',
        '″' => '&Prime;',
        '‰' => '&permil;',
        '¢' => '&cent;',
        '£' => '&pound;',
        '¥' => '&yen;',
        '¤' => '&curren;',
    ];

    return str_replace(array_keys($replacements), array_values($replacements), $html);
}
	
?>