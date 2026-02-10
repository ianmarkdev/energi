<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/admin_error.txt');

require '../qrcode/vendor/autoload.php';

require '../snappy_new/vendor/autoload.php';

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

use Knp\Snappy\Pdf;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("../white_config.inc.php");

session_start();

if ($_SESSION["security_token"] != $appSecurityToken) {

  if (!isset($_GET["redirect"])) {

    echo '<form action="" method="POST"><input type="password" name="security_token"><input type="submit" name="check_token" value="login &raquo;"></form>';
  }

  if ($_POST) {

    $sentSecurityToken = $_POST["security_token"];

    if ($sentSecurityToken == $appSecurityToken) {

      $_SESSION["security_token"] = $appSecurityToken;

      header("Location: index.php?redirect");
      exit();
    } else {

      header("Location: index.php");
      exit();
    }
  }
} else if ($_SESSION["security_token"] == $appSecurityToken) {

  if ($_GET["a"] == "sesskill") {

    unset($_SESSION['security_token']);
    session_destroy();
    header('Location: index.php');
    exit();
  }


  if ($_GET["do"] == "del" && isset($_GET["id"])) {
    $delid = $_GET["id"];
    $deloid = $_GET["oid"];
    $deluid = $_GET["uid"];

    $del_User = $SQL->prepare("DELETE FROM kunden WHERE kunden_id = ? LIMIT 1");
    $del_User->bind_param("i", $deluid);
    $del_User->execute();
    $del_User->close();

    $del_Order = $SQL->prepare("DELETE FROM kunden_bestellungen WHERE order_id = ? AND user_id = ? LIMIT 1");
    $del_Order->bind_param("ii", $deloid, $deluid);
    $del_Order->execute();
    $del_Order->close();

    $del_Data = $SQL->prepare("DELETE FROM kunden_anschrift WHERE user_id = ? LIMIT 1");
    $del_Data->bind_param("i", $deluid);
    $del_Data->execute();
    $del_Data->close();

    $del_Invoice = $SQL->prepare("DELETE FROM kunden_rechnungen WHERE user_id = ? AND order_id = ? LIMIT 1");
    $del_Invoice->bind_param("ii", $deluid, $deloid);
    $del_Invoice->execute();
    $del_Invoice->close();

    header("Location: index.php?success=10");
  }


  if ($_GET["do"] == "check" && isset($_GET["id"]) && isset($_GET["oid"]) && isset($_GET["uid"])) {
    //MARK ORDER AS IN WORK
    $did = $_GET["id"];
    $doid = $_GET["oid"];
    $duid = $_GET["uid"];

    //GET INVOICE
    $get_Invoice = $SQL->prepare("SELECT pdf_file, used_bd FROM kunden_rechnungen WHERE order_id = ?");
    $get_Invoice->bind_param("i", $doid);
    $get_Invoice->execute();
    $get_Invoice->store_result();
    $get_Invoice->bind_result($dpdf_file, $dused_bd);
    $get_Invoice->fetch();

    //30.04.25 UPDATE BD DATA
    $get_Active_BD = $SQL->prepare("SELECT bd_name, bd_iban, bd_bic FROM bd_auswahl WHERE bd_id = ? LIMIT 1");
    $get_Active_BD->bind_param("i", $dused_bd);
    $get_Active_BD->execute();
    $get_Active_BD->store_result();
    $get_Active_BD->bind_result($active_BD_Name, $active_BD_IBAN, $active_BD_BIC);
    $get_Active_BD->fetch();

    $get_ProductsData = $SQL->prepare("SELECT gesamtsumme, anschrift, lieferadresse, payment_method, products_array, delivery_date, delivery_add, liefer_array FROM kunden_bestellungen WHERE order_id = ?");
    $get_ProductsData->bind_param("i", $doid);
    $get_ProductsData->execute();
    $get_ProductsData->store_result();
    $get_ProductsData->bind_result($dgesamt_summe, $danschrift, $lieferadresse, $d_payment_method, $dproducts_array, $d_delivery_date, $d_delivery_add, $dliefer_array);
    $get_ProductsData->fetch();

    $lieferanschriften = json_decode($dliefer_array, true);
    $products_php_array = json_decode($dproducts_array, true);

    function formatLieferadressenMailBlock($lieferanschriften)
    {
      $out = '';
      if (!$lieferanschriften || !is_array($lieferanschriften)) return '';
      $counter = 1;
      foreach ($lieferanschriften as $adr) {
        $out .= "<b>Lieferstelle #$counter</b>\n";
        $out .= trim("{$adr['anrede']} {$adr['vorname']} {$adr['nachname']}") . "\n";
        $out .= trim($adr['strasse']) . "\n";
        $out .= trim("{$adr['plz']} {$adr['ort']}") . "\n";
        $out .= trim($adr['land']) . "\n\n";
        $counter++;
      }
      return trim($out);
    }

    $format_d_gesamt = formatNumber($dgesamt_summe);

    $remind_date_add = date('Y-m-d H:i:s');

    $update_Order = $SQL->prepare("UPDATE kunden_bestellungen SET status = 3, remind_date = ? WHERE id = ?");
    $update_Order->bind_param("si", $remind_date_add, $did);
    $update_Order->execute();
    $update_Order->close();

    $fetch_User = $SQL->prepare("SELECT anrede, vorname, nachname, rufnummer, email_adresse, postleitzahl, ort, strasse FROM kunden_anschrift WHERE user_id = ?");
    $fetch_User->bind_param("i", $duid);
    $fetch_User->execute();
    $fetch_User->store_result();
    $fetch_User->bind_result($d_anrede, $d_vorname, $d_nachname, $d_rufnummer, $d_email_adresse, $d_postleitzahl, $d_ort, $d_strasse);
    $fetch_User->fetch();

    $d_fullname = $d_vorname . " " . $d_nachname;

    $attach_pdf_file = "../invoice_files/" . $dpdf_file;



    $vorname_clean = replaceUmlauts($d_vorname);
    $nachname_clean = replaceUmlauts($d_nachname);
    $ceo_clean = replaceUmlauts($cur_CEO);
    $firma_clean = replaceUmlauts($cur_Firma);

    if ($d_anrede == "Herr") {
      $craft_anrede = "Sehr geehrter Herr " . $d_vorname . " " . $d_nachname . ",";
    } else if ($d_anrede == "Frau") {
      $craft_anrede = "Sehr geehrte Frau " . $d_vorname . " " . $d_nachname . ",";
    }

    //FETCH PRODUTS
    $totalSum = 0;
    $countItem = 0;

    $sumGross = 0;
    foreach ($products_php_array as $i) {
      $sumGross += $i['price'];
    }

    //GENERATE PRODUCT ROWS
    foreach ($products_php_array as $item) {
      if ($item["type"] == "main") {
        //MAIN PRODUCT			
        /* START CONSTRUCT MAIL LINE */
        //CALC 100L PRICE
        $singlePrice = $item['price'] / $item['quantity'] / $item['lieferstellen'] * 100;
        $singlePrice_format = number_format($singlePrice, 2, ',', '.');

        $productTotalx = $item['price'];
        $singleSumx = $productTotalx;
        $singleSumx_format = number_format($singleSumx, 2, ',', '.');

        $product_line_single_full = $singlePrice * $item['quantity'];
        $product_line_single_full_liefer = $product_line_single_full * $item['lieferstellen'];
        $product_line_single_full_format = number_format($product_line_single_full_liefer, 2, ',', '.');

        if ($item['lieferstellen'] == 1) {
          $product_line_lieferstellen_suffix = "Lieferstelle";
        } else {
          $product_line_lieferstellen_suffix = "Lieferstellen";
        }

        $product_line_html_new = '<tr>
				<td>1</td>
				<td>' . $item['name'] . '</td>
				<td>' . $item['quantity'] . ' Liter bei ' . $item['lieferstellen'] . ' ' . $product_line_lieferstellen_suffix . '</td>
				<td>' . $singlePrice_format . ' € / 100l</td>
				<td>' . $singleSumx_format . ' €</td>
				</tr>';


        $listFinalSum = $sumGross + $global_GGVS;
        $listFinalSum_format = number_format($listFinalSum, 2, ',', '.');

        $list_lieferstellen = $item['lieferstellen'];
        /* END CONSTRUCT MAIL LINE */
      }
    }

    //GET ADDY FOR MAIL
    $lieferadresseDB_Mail = formatLieferadressenMailBlock($lieferanschriften);
    $lieferadresseDB_Mail_Line = nl2br($lieferadresseDB_Mail);

    //CREATE LIEFERDATUM
    if ($d_delivery_date != "none") {
      $parts = explode(' ', $d_delivery_date, 2);
      $datePart = $parts[0];
      $timePart = $parts[1] ?? '';
      $dt = DateTime::createFromFormat('Y-m-d', $datePart);
      $formatter = new \IntlDateFormatter('de_DE', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Europe/Berlin', \IntlDateFormatter::GREGORIAN, 'EEEE, d. MMMM');
      $deliveryDateLabel = $formatter->format($dt->getTimestamp()) . ' ' . $timePart;

      $deliveryDateSQL = $d_delivery_date;
      $deliveryDateMail = $deliveryDateLabel;
    } else {
      $deliveryDateLabel = "none";
      $deliveryDateSQL = "none";
      $deliveryDateMail = "Telefonische Abstimmung";
    }








    $mail = new PHPMailer(true);


    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->Host = $cur_SMTPHost;
    $mail->SMTPAuth = true;
    $mail->Username = $cur_SMTPUser;
    $mail->Password = $cur_SMTPPass;
    $mail->SMTPSecure = $cur_SMTPType;
    $mail->Port = $cur_SMTPPort;

    $mail->setFrom($cur_SMTPUser, $cur_Title);
    $mail->XMailer = '';
    $mail->addAddress($d_email_adresse, $d_fullname);


    if (file_exists($attach_pdf_file)) {
      $mail->addAttachment($attach_pdf_file);
    } else {
      echo 'Attachment error';
    }

    $mail->isHTML(true);

    $mail->Subject = 'Ihre Rechnung von ' . $cur_Title;
    $mail->Body    = $cleanedHtml;
    $mail->send();

    //END: SENDING EMAIL INFO TO CUSTOMER


    header("Location: index.php?success=1");
    //header("Location: index.php?custom=1&file=$attach_pdf_file");
    exit();
  }

  if ($_GET["success"] == 1) {
    $show_Success_Msg = "Sie haben den Bestellstatus für diese Bestellung auf 'Rechnung versendet' markiert und die Rechnung versendet.";
  }

  if ($_GET["do"] == "finish" && isset($_GET["id"]) && isset($_GET["oid"]) && isset($_GET["uid"])) {
    //MARK ORDER AS FINISHED
    $did = $_GET["id"];
    $doid = $_GET["oid"];
    $duid = $_GET["uid"];

    $update_Order = $SQL->prepare("UPDATE kunden_bestellungen SET status = 4 WHERE id = ?");
    $update_Order->bind_param("i", $did);
    $update_Order->execute();
    $update_Order->close();

    $fetch_User = $SQL->prepare("SELECT anrede, vorname, nachname, rufnummer, email_adresse, postleitzahl, ort, strasse FROM kunden_anschrift WHERE user_id = ?");
    $fetch_User->bind_param("i", $duid);
    $fetch_User->execute();
    $fetch_User->store_result();
    $fetch_User->bind_result($d_anrede, $d_vorname, $d_nachname, $d_rufnummer, $d_email_adresse, $d_postleitzahl, $d_ort, $d_strasse);
    $fetch_User->fetch();

    $d_fullname = $d_vorname . " " . $d_nachname;

    if ($d_anrede == "Herr") {
      $this_anrede = "Sehr geehrter Herr $d_vorname $d_nachname";
    } else if ($d_anrede == "Frau") {

      $this_anrede = "Sehr geehrte Frau $d_vorname $d_nachname";
    } else {
      $this_anrede = "Hallo $d_vorname $d_nachname";
    }

    header("Location: index.php?success=2");
    exit();
  }

  if ($_GET["success"] == 2) {
    $show_Success_Msg = "Sie haben den Bestellstatus als abgeschlossen markiert und eine E-Mail versendet.";
  }

  if ($_GET["success"] == 10) {
    $show_Success_Msg = "Sie haben diese Bestellung, den Kunden, die Anschrift und ggf. Rechnung gelöscht.";
  }

  if (isset($_POST["change_bd_data"])) {
    //UPDATE BD-DATA
    $bd_name = $_POST["name"];
    $bd_iban = $_POST["iban"];
    $bd_bic = $_POST["bic"];

    $update_MySQL = $SQL->prepare("UPDATE einstellungen SET bd_name = ?, bd_iban = ?, bd_bic = ? WHERE id = 1");
    $update_MySQL->bind_param("sss", $bd_name, $bd_iban, $bd_bic);
    $update_MySQL->execute();
    $update_MySQL->close();

    $show_Success_Msg = "Sie haben die BD-Daten soeben erfolgreich aktualisiert.";
  }

  //

  if (isset($_POST["change_price_data"])) {
    //UPDATE PRICE-DATA
    $change_first = $_POST["first_price"];
    $change_second = $_POST["second_price"];
    $change_third = $_POST["third_price"];

    $update_MySQL = $SQL->prepare("UPDATE einstellungen SET first_price = ?, second_price = ?, third_price = ? WHERE id = 1");
    $update_MySQL->bind_param("sss", $change_first, $change_second, $change_third);
    $update_MySQL->execute();
    $update_MySQL->close();

    $show_Success_Msg = "Sie haben die Preise für Heizöl soeben erfolgreich aktualisiert.";
  }

  //CHANGE SITE DATA
  if (isset($_POST["change_page_data"])) {
    $up_titel = $_POST["titel"];
    $up_domain = $_POST["domain"];

    $up_firma = $_POST["firma"];
    $up_ceo = $_POST["ceo"];
    $up_adresse = $_POST["adresse"];
    $up_plz = $_POST["plz"];
    $up_stadt = $_POST["stadt"];
    $up_telefon = $_POST["telefon"];
    $up_email = $_POST["email"];
    $up_steuer_id = $_POST["steuer_id"];

    $up_gericht = $_POST["gericht"];
    $up_nummer = $_POST["nummer"];

    /* START CHECK FILE-UPLOADS */
    $uploadDir = '../assets/uploads/';
    $new_LogoLight = $cur_LogoLight;
    $new_LogoDark = $cur_LogoDark;

    function generateRandomFilename($extension)
    {
      return uniqid('logo_', true) . '.' . $extension;
    }


    if (isset($_FILES['logo_light']) && $_FILES['logo_light']['error'] === UPLOAD_ERR_OK) {
      $tmpName = $_FILES['logo_light']['tmp_name'];
      $extension = pathinfo($_FILES['logo_light']['name'], PATHINFO_EXTENSION);
      $newName = generateRandomFilename($extension);
      if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
        $new_LogoLight = "assets/uploads/" . $newName;
      }
    }

    if (isset($_FILES['logo_dark']) && $_FILES['logo_dark']['error'] === UPLOAD_ERR_OK) {
      $tmpName = $_FILES['logo_dark']['tmp_name'];
      $extension = pathinfo($_FILES['logo_dark']['name'], PATHINFO_EXTENSION);
      $newName = generateRandomFilename($extension);
      if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
        $new_LogoDark = "assets/uploads/" . $newName;
      }
    }

    /* == START: GENERATE NEW FAVICON == */
    if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
      $new_Favicon_TargetDir = "../assets/images/favicon/";
      $new_Favicon_WebDir = "assets/images/favicon/";
      $new_Favicon_Sizes = [16, 32, 48, 96, 180];

      //START: DELETE OLD FAVICON-FILES
      $scan_Files = glob($new_Favicon_TargetDir . "*");
      foreach ($scan_Files as $scanned_Files) {
        if (is_file($scanned_Files) && basename($scanned_Files) !== '.gitignore') {
          unlink($scanned_Files);
        }
      }
      //END: DELETE OLD FAVICON FILES

      //aaa
      if (!extension_loaded('imagick')) {
        die("Imagick-Erweiterung nicht installiert!");
      }

      $tmpFile = $_FILES["favicon"]["tmp_name"];
      $image = new Imagick($tmpFile);

      if ($image->getImageMimeType() !== 'image/png') {
        die("FEHLER: Bitte ein PNG hochladen!");
      }

      $w = $image->getImageWidth();
      $h = $image->getImageHeight();
      if ($w !== $h) {
        die("FEHLR: Das Bild muss quadratisch sein!");
      }

      foreach ($new_Favicon_Sizes as $size) {
        $icon = clone $image;
        $icon->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
        $icon->setImageFormat('png');
        $icon->writeImage($new_Favicon_TargetDir . "favicon-{$size}x{$size}.png");
        if ($size == 180) {
          $icon->writeImage($new_Favicon_TargetDir . "apple-touch-icon.png");
        }
        $icon->destroy();
      }

      $ico = new Imagick();
      foreach ([16, 32, 48] as $size) {
        $icon = clone $image;
        $icon->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
        $icon->setImageFormat('png');
        $ico->addImage($icon);
        $icon->destroy();
      }
      $ico->setFormat('ico');
      $ico->writeImage($new_Favicon_TargetDir . "favicon.ico");
      $ico->destroy();

      $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="96" height="96" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg">
  <image href="favicon-96x96.png" width="96" height="96"/>
</svg>';
      file_put_contents($new_Favicon_TargetDir . "favicon.svg", $svg);

      $manifest = [
        "name" => "Website",
        "icons" => [
          [
            "src" => "favicon-96x96.png",
            "sizes" => "96x96",
            "type" => "image/png"
          ],
          [
            "src" => "apple-touch-icon.png",
            "sizes" => "180x180",
            "type" => "image/png"
          ]
        ]
      ];
      file_put_contents($new_Favicon_TargetDir . "site.webmanifest", json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

      $image->destroy();
      //xxx
    }
    /* == END: GENERATE NEW FAVICON == */

    $google_tag = isset($_POST['google_tag']) ? trim($_POST['google_tag']) : '';
    $conversion_tag = isset($_POST['conversion_tag']) ? trim($_POST['conversion_tag']) : '';
    $additional_tag = isset($_POST['additional_tag']) ? trim($_POST['additional_tag']) : '';

    $google_tag_sql = addslashes($google_tag);
    $conversion_tag_sql = addslashes($conversion_tag);
    $additional_tag_sql = addslashes($additional_tag);
    /* END CHECK FILE-UPLOADS */

    $new_main_color = isset($_POST['main_color']) ? $_POST['main_color'] : $cur_MainColor;
    $new_secondary_color = isset($_POST['secondary_color']) ? $_POST['secondary_color'] : $cur_SecondaryColor;

    $new_smtp_host = $_POST["smtp_host"];
    $new_smtp_user = $_POST["smtp_user"];
    $new_smtp_pass = $_POST["smtp_pass"];
    $new_smtp_type = $_POST["smtp_type"];
    $new_smtp_port = $_POST["smtp_port"];

    $new_PDFLogo = $cur_PDFLogo;

    if (isset($_FILES['pdf_logo']) && $_FILES['pdf_logo']['error'] === UPLOAD_ERR_OK) {
      $tmpName = $_FILES['pdf_logo']['tmp_name'];
      $originalName = $_FILES['pdf_logo']['name'];
      $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

      if ($extension === 'jpg' || $extension === 'jpeg') {
        $newName = generateRandomFilename($extension);
        if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
          $new_PDFLogo = "assets/uploads/" . $newName;
        }
      } else {
        echo "Nur JPG-Dateien sind für das PDF-Logo erlaubt!";
      }
    }

    $new_enable_sepa = $_POST["enable_sepa"];
    $new_enable_invoice = $_POST["enable_invoice"];

    $update_Page_Data = $SQL->prepare("UPDATE einstellungen SET titel = ?, seite_titel = ?, domain = ?, ceo = ?, firma = ?, strasse = ?, plz = ?, ort = ?, telefon = ?, email = ?, gericht = ?, nummer = ?, steuernummer = ?, logo_dark = ?, logo_light = ?, smtp_host = ?, smtp_user = ?, smtp_pass = ?, smtp_type = ?, smtp_port = ?, pdf_logo = ?, main_color = ?, secondary_color = ?, google_tag = ?, conversion_tag = ?, additional_tag = ?, enable_sepa = ?, enable_invoice = ? WHERE id = 1");
    $update_Page_Data->bind_param("ssssssssssssssssssssssssssii", $up_titel, $up_titel, $up_domain, $up_ceo, $up_firma, $up_adresse, $up_plz, $up_stadt, $up_telefon, $up_email, $up_gericht, $up_nummer, $up_steuer_id, $new_LogoDark, $new_LogoLight, $new_smtp_host, $new_smtp_user, $new_smtp_pass, $new_smtp_type, $new_smtp_port, $new_PDFLogo, $new_main_color, $new_secondary_color, $google_tag, $conversion_tag, $additional_tag, $new_enable_sepa, $new_enable_invoice);
    $update_Page_Data->execute();
    $update_Page_Data->close();

    $show_Success_Msg = "Sie haben die allgemeinen Angaben soeben aktualisiert.";
  }

  //UPDATE 29.08.2024 - CREATE INVOICE FROM ADMIN-CP
  if (isset($_GET["generate_invoice"]) && isset($_GET["id"]) && isset($_GET["oid"]) && isset($_GET["uid"])) {
    //CREATE NEW INVOICE
    $this_Use_ID = $_GET["id"];
    $this_Use_OID = $_GET["oid"];
    $this_Use_UID = $_GET["uid"];

    $did = $_GET["id"];
    $doid = $_GET["oid"];
    $duid = $_GET["uid"];

    //CHECK IF CUSTOMER ALREADY HAS INVOICE
    $check_For_Double_Inv = $SQL->prepare("SELECT COUNT(id) FROM kunden_rechnungen WHERE user_id = ? AND order_id = ?");
    $check_For_Double_Inv->bind_param("ii", $this_Use_UID, $this_Use_OID);
    $check_For_Double_Inv->execute();
    $check_For_Double_Inv->store_result();
    $check_For_Double_Inv->bind_result($this_DoubleInv_Check);
    $check_For_Double_Inv->fetch();

    if ($this_DoubleInv_Check > 0) {
      die("Fehler: Kunde hat bereits eine Rechnung. Failsafe.");
    }

    $zusatzinfos = "none";

    //FETCH USER DATA FOR PDF-CREATION
    $fetch_Invoice_User_Records = $SQL->prepare("SELECT produkte, gesamtsumme, anschrift, lieferadresse, payment_method, products_array, delivery_date, creation_date, status FROM kunden_bestellungen WHERE id = ? AND order_id = ? AND user_id = ?");
    $fetch_Invoice_User_Records->bind_param("iii", $_GET["id"], $_GET["oid"], $_GET["uid"]);
    $fetch_Invoice_User_Records->execute();
    $fetch_Invoice_User_Records->store_result();
    $fetch_Invoice_User_Records->bind_result($invoice_produkte, $invoice_gesamtsumme, $invoice_anschrift, $invoice_lieferadresse, $invoice_payment_method, $invoice_products_array, $invoice_delivery_date, $invoice_creation_date, $invoice_status);
    $fetch_Invoice_User_Records->fetch();

    if (empty($invoice_lieferadresse) or $invoice_lieferadresse == "none") {
      //NO ADDITIONAL LIEFERADDRESS
      $user_Has_Lieferadresse = FALSE;
    } else {
      $user_Has_Lieferadresse = TRUE;
    }

    $fetch_Invoice_Full_Details = $SQL->prepare("SELECT anrede, vorname, nachname, rufnummer, email_adresse, postleitzahl, ort, strasse FROM kunden_anschrift WHERE user_id = ?");
    $fetch_Invoice_Full_Details->bind_param("i", $_GET["uid"]);
    $fetch_Invoice_Full_Details->execute();
    $fetch_Invoice_Full_Details->store_result();
    $fetch_Invoice_Full_Details->bind_result($invoice_anrede, $invoice_vorname, $invoice_nachname, $invoice_rufnummer, $invoice_email_adresse, $invoice_postleitzahl, $invoice_ort, $invoice_strasse);
    $fetch_Invoice_Full_Details->fetch();

    //CONVERT SERIALIZED PRODUCTS BACK TO AN ARRAY
    //$user_products = unserialize($invoice_products_array);
    $user_products = json_decode($invoice_products_array, true);

    //PREPARE LAST DATA FOR INVOICE-CREATION
    $companyName = $cur_Firma;
    $companyAddress = $cur_Strasse . ", " . $cur_PLZ . " " . $cur_Ort;
    $companyPhone = $cur_Telefon;
    $companyEmail = $cur_Mail;

    $customerName = $full_name;
    $customerAddress = $strasse . ", " . $postleitzahl . " " . $ort;
    $invoiceDate = $appCurrentDate;
    $invoiceNumber = rand(10000, 99999);

    $this_Invoice_VWZ = "RNG-" . $nachname . "-" . $invoiceNumber;

    $get_BD = $SQL->prepare("
						SELECT bd_id, bd_name, bd_iban, bd_bic, daily_used, daily_limit, total_used, total_limit
    					FROM bd_auswahl
    					WHERE status = 1
    					AND daily_used < daily_limit
    					AND total_used < total_limit
						ORDER BY id ASC
    					LIMIT 1
					");
    $get_BD->execute();
    $get_BD->store_result();

    if ($get_BD->num_rows === 0) {
      $show_Error_Msg = "Kein aktiver Bankdrop mit verfügbarem Limit gefunden.";
    } else {
      $get_BD->bind_result($active_BD_ID, $active_BD_Name, $active_BD_IBAN, $active_BD_BIC, $used_today, $limit_daily, $used_total, $limit_total);
      $get_BD->fetch();

      $update_BD = $SQL->prepare("
					        UPDATE bd_auswahl
					        SET daily_used = daily_used + 1,
					            total_used = total_used + 1
 					       WHERE bd_id = ?
 					   ");
      $update_BD->bind_param("i", $active_BD_ID);
      $update_BD->execute();


      $conf_mwst = "19";

      $logoPath = "../" . $cur_PDFLogo;
      if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoSrc = 'data:image/png;base64,' . $logoData;
      } else {
        $logoSrc = '';
      }

      $invoice_fullname = $invoice_vorname . " " . $invoice_nachname;

      //CHECK FOR PAYMENT METHOD
      if ($invoice_payment_method == "SEPA-Überweisung") {

        $this_Addition_Info .= $zusatzinfos;
      } else if ($invoice_payment_method == "Rechnung") {

        $this_Addition_Info .= $zusatzinfos;
      }



      /* ===== START: NEW SNAPPY PDF-GEN 17.11.24 ===== */
      function extractDate($inputString)
      {
        $pattern = '/\b\d{2}\.\d{2}\.\d{4}\b/';

        if (preg_match($pattern, $inputString, $matches)) {
          return $matches[0];
        }

        return null;
      }

      if ($user_Has_Lieferadresse == TRUE) {
        //$transform_Lieferadresse = nl2br(htmlspecialchars($invoice_lieferadresse, ENT_QUOTES, 'UTF-8', false));
        $transform_Lieferadresse = $invoice_lieferadresse;
      }

      $extract_invoice_date = transformDate($invoice_creation_date);

      /* === START 31.10.25 QRCODE UPDATE === */
      $qrcode_format_gesamt = formatNumber($invoice_gesamtsumme);

      $qrcode_text = <<<EOT
Empfänger: $active_BD_Name
IBAN: $active_BD_IBAN
BIC/SWIFT: $active_BD_BIC
Betrag: $qrcode_format_gesamt €
Verwendungszweck: $this_Use_OID
EOT;


      $randomName = uniqid('qr_', true) . '.png';
      $savePath = '../qrcode/uploads/' . $randomName;
      $qrDBPath = 'qrcode/uploads/' . $randomName;
      $qrPDFPath = $cur_Domain . "/" . $qrDBPath;



      $qrCode = new QrCode(
        data: $qrcode_text,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High,
        size: 300,
        margin: 10,
        roundBlockSizeMode: RoundBlockSizeMode::Margin,
        foregroundColor: new Color(0, 0, 0),
        backgroundColor: new Color(255, 255, 255)
      );

      // PNG schreiben
      $writer = new PngWriter();
      $result = $writer->write($qrCode);
      $result->saveToFile($savePath);
      /* === END 31.10.25 QRCODE UPDATE === */

      /* === START 31.10.25 DELIVERYDATE INVOICE === */
      if ($invoice_delivery_date != "none") {
        $inv_parts = explode(' ', $invoice_delivery_date, 2);
        $inv_datePart = $inv_parts[0];
        $inv_timePart = $inv_parts[1] ?? '';
        $inv_dt = DateTime::createFromFormat('Y-m-d', $inv_datePart);
        $inv_formatter = new \IntlDateFormatter('de_DE', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Europe/Berlin', \IntlDateFormatter::GREGORIAN, 'EEEE, d. MMMM');
        $inv_deliveryDateLabel = $inv_formatter->format($inv_dt->getTimestamp()) . ' ' . $inv_timePart;

        $inv_deliveryDateSQL = $invoice_delivery_date;
        $inv_deliveryDateInvoice = $inv_deliveryDateLabel . " Uhr";
      } else {
        $inv_deliveryDateLabel = "none";
        $inv_deliveryDateSQL = "none";
        $inv_deliveryDateInvoice = "Telefonische Abstimmung";
      }
      /* === END 31.10.25 DELIVERYDATE INVOICE === */

      $snappy = new Pdf('/usr/local/bin/wkhtmltopdf');

      $html_snappy = '
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>RECHNUNG</title>
    <link rel="stylesheet" href="' . $cur_Domain . '/assets/css/bootstrap2.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap");

        body, p, span, h1, h2, h3, h4, h5, h6 {
            font-family: "Roboto", sans-serif;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table th, .table td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
        }

        .table th {
            background-color: #f4f4f4;
            text-align: left;
        }

        .panel-body {
            font-family: "Roboto", sans-serif;
        }

        .panel-body strong, .panel-body span[style*="font-weight"] {
            font-weight: 700;
        }
		
		        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .invoice-table thead th {
            text-align: left;
            font-weight: 700;
            border-bottom: 2px solid #ddd;
            padding: 10px 5px;
        }

        .invoice-table tbody td {
            padding: 8px 5px;
            border-bottom: 1px solid #ddd;
        }

        .invoice-table tfoot td {
            font-weight: 700;
            padding: 8px 5px;
            text-align: right;
        }

        .invoice-summary {
            margin-top: 20px;
            float: right;
            text-align: right;
        }

        .invoice-summary td {
            padding: 5px 0;
        }

        .invoice-summary .total {
            font-size: 16px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="container" style="padding:0;margin:0;max-width:100%;">

        <div class="row">
            <div class="col-xs-6">
              
                <a href="' . $cur_Domain . '">
                    <img src="' . $cur_Domain . '/' . $cur_PDFLogo . '" style="width:450px;height:auto;">
                </a><br />
                    
                    <span style="font-size:15.5px;color:grey;"><br>' . $cur_Strasse . ', ' . $cur_PLZ . ' ' . $cur_Ort . ', Deutschland</span>
                    <br /><br />';

      if ($user_Has_Lieferadresse == FALSE) {
        //NO LIEFERADDRESSE
        $html_snappy .= '<span style="color:#000;font-size:15.5px;display:block;line-height:25px;">
                    ' . $invoice_anrede . ' ' . $invoice_vorname . ' ' . $invoice_nachname . '<br>
                    ' . $invoice_strasse . '<br>
                    ' . $invoice_postleitzahl . ' ' . $invoice_ort . '<br>
					Deutschland
                    </span>';
      } else if ($user_Has_Lieferadresse == TRUE) {
        //HAS LIEFERADRESSE
        $html_snappy .= '
					<span style="color:#000;font-size:15.5px;display:block;line-height:25px;">
					<span style="color:#000;font-size:18.5px;font-weight:750;"><b>Lieferstelle #1</b></span><br>
                    ' . $invoice_vorname . ' ' . $invoice_nachname . '<br>
                    ' . $invoice_strasse . '<br>
                    ' . $invoice_postleitzahl . ' ' . $invoice_ort . '<br>
					Deutschland
                    </span><br><br>
					<span style="color:#000;font-size:15.5px;display:block;line-height:25px;">
					<!--<span style="color:#000;font-size:18.5px;font-weight:750;"><b>Lieferadresse</b></span><br>-->
                    ' . $transform_Lieferadresse . '
                    </span>';
      }

      $html_snappy .= '</div>
            <div class="col-xs-2">
                <img src="' . $cur_Domain . '/assets/images/waiting_payment.jpg" style="height:75px;width:auto;">
            </div>
			
			<div class="col-xs-4">
                <div class="panel panel-default" style="border-radius:0px!important;">
                      <div class="panel-heading" style="background:#000;color:#fff;font-size:28px;font-weight:700;">
                        <center><h4>RECHNUNG</h4></center>
                      </div>
                      <div class="panel-body" style="text-align:left;min-height:100px;background:#eee;padding:25px 25px 25px 25px;">
                        <span style="font-weight:700!important;">Bestell-Nr.</span> ' . $this_Use_OID . '<br />
                        <span style="font-weight:700!important;">Rechnungs-Nr.:</span> ' . $this_Use_OID . '<br /><br />
                        
                        Datum: ' . $cur_Datum . '<br />
                        Bearbeiter: ' . $active_BD_Name . '<br />
                        Bestelldatum: ' . $extract_invoice_date . '
                      </div>
                </div>
            </div>
			
        </div>
        
        <div class="row" style="min-width:100%;min-width:100%;">
        
            <div class="col-xs-12" style="min-width:100%;min-width:100%;">
            
                <!-- START PRODUCTS TABLE -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Pos.</th>
                <th>Produkt</th>
                <th>Anzahl</th>
                <th>Einzelpreis<br><span style="font-weight:400;">Inkl. USt</span></th>
                <th>Gesamt<br><span style="font-weight:400;">Inkl. USt</span></th>
            </tr>
        </thead>
        <tbody>';

      /* == START: INJECT PRODUCTS FROM CART == */

      $productRows = '';
      $totalPrice = 0;

      //CONSTRUCT TAXES VALUE
      $new_TaxRate = '0.' . $conf_mwst;

      $taxRate = $new_TaxRate;


      //FETCH PRODUCTS FROM ANEW ARRAY
      $totalSum = 0;
      $countItem = 0;
      foreach ($user_products as $item) {
        $countItem++;
        if ($item['type'] == 'main') {
          //MAIN PRODUCT
          //$productTotal = $item['quantity'] * $item['price'] * $item['lieferstellen'];
          $productTotal = $item['price'];
          //$totalSum += $productTotal;

          //CALC SINGLE PRICE
          //$singleSum = $productTotal / $item['quantity'] / $item['lieferstellen'];
          $singleSum = $productTotal;
          //$newTotal = $singleSum / $item['quantity'] / $item['lieferstellen'];
          //$newTotal = $singleSum / $item['quantity'] / $item['lieferstellen'];
          $newTotal = $singleSum / $item['quantity'] / $item['lieferstellen'] * 100;
          $totalSum += $singleSum;

          if ($item['lieferstellen'] == 1) {
            $lieferstelle_text = "Lieferstelle";
            $new_einzelpreis = $newTotal;
          } else {
            $lieferstelle_text = "Lieferstellen";
            $new_einzelpreis = $newTotal / $item["lieferstellen"];
          }

          $html_snappy .= '
					            <tr>
									<td>' . $countItem . ' </td>
					                <td>' . $item['name'] . '</td>
									<td>' . $item['quantity'] . 'l bei ' . $item['lieferstellen'] . ' ' . $lieferstelle_text . '</td>
									
					                <td>' . number_format($new_einzelpreis, 2, ',', '.') . ' € p. 100l</td>
									<td>' . number_format($singleSum, 2, ',', '.') . ' €</td>
					            </tr>';
        } else if ($item['type'] == 'additional') {
          //OPTIONAL PRODUCT
          $productTotal = 0;
          $totalSum += $productTotal;

          //CALC SINGLE PRICE
          $singleSum = $productTotal;

          $html_snappy .= '
					            <tr>
									<td>' . $countItem . ' </td>
					                <td>' . $item['name'] . '</td>
									<td>1x</td>
									<td>' . number_format($singleSum, 2, ',', '.') . ' €</td>
					                <td>' . number_format($productTotal, 2, ',', '.') . ' €</td>
					            </tr>';
        }
      }

      $deliveryCount = $countItem + 1;
      $html_snappy .= '
					            <tr>
									<td>' . $deliveryCount . '</td>
					                <td>Lieferung</td>
									<td>kostenfrei</td>
									<td>0,00 €</td>
					                <td>0,00 €</td>
					            </tr>';

      $calc_tax_value = $totalSum * 0.19;
      $calc_net_value = $totalSum - $calc_tax_value;
      $ggvs_value = $global_GGVS;

      $new_gross_total = $totalSum + $ggvs_value;

      //END FETCH PRODUCTS FROM ANEW ARRAY


      $this_Net_Amount = number_format($calc_net_value, 2, ',', '.');
      $this_Tax_Amount = number_format($calc_tax_value, 2, ',', '.');
      $this_GGVS_Amount = number_format($ggvs_value, 2, ',', '.');
      $this_Gross_Amount = number_format($new_gross_total, 2, ',', '.');


      /* == END: INJECT PRODUCTS FROM CART == */


      $html_snappy .= '
        </tbody>
    </table>

    <table class="invoice-summary">
        <tr>
            <td>Gesamtsumme (Netto):</td>
            <td>' . $this_Net_Amount . ' €</td>
        </tr>
        <tr>
            <td>19% MwSt.:</td>
            <td>' . $this_Tax_Amount . ' €</td>
        </tr>
		<tr>
            <td>GGVS-Umlage</td>
            <td>' . $this_GGVS_Amount . ' €</td>
        </tr>
        <tr class="total">
            <td>Gesamtsumme Brutto:</td>
            <td>&nbsp;&nbsp;&nbsp;' . $this_Gross_Amount . ' €</td>
        </tr>
    </table>
                <!-- END PRODUCTS TABLE -->
            
            </div>
        
        </div>
		
		
		
		<div class="row">
			<div class="col-xs-3">
				<!-- START BOX -->
				<div id="contentBox" style="background:#eee;padding:10px 10px 10px 10px;text-align:left;line-height:35px;font-size:15.5px;">
					<span style="font-weight:700">Gewählte Zahlungsart</span><br>
					<span style="font-weight:400">' . $invoice_payment_method . '</span>
				</div>
				<!-- END BOX -->
			</div>
			
			<div class="col-xs-3">
				<!-- START BOX -->
				<div id="contentBox" style="background:#eee;padding:10px 10px 10px 10px;text-align:left;line-height:35px;font-size:15.5px;">
					<span style="font-weight:700">Gewählte Versandart</span><br>
					<span style="font-weight:400">Standardlieferung</span>
				</div>
				<!-- END BOX -->
			</div>
			
			<div class="col-xs-3">
				<!-- START BOX -->
				<div id="contentBox" style="background:#eee;padding:10px 10px 10px 10px;text-align:left;line-height:35px;font-size:15.5px;">
					<span style="font-weight:700">Lieferzeitraum</span><br>
					<span style="font-weight:400;">' . $inv_deliveryDateInvoice . '</span>
				</div>
				<!-- END BOX -->
			</div>
			
			<div class="col-xs-3">
				<!-- NONE -->
			</div>
		</div>
		
		
		<div class="row">
			<div id="lineHeight" style="min-height:75px;"></div>
		</div>
		
		<!-- start new row -->
		<div class="row">
		
			<div class="col-xs-9">
				<!-- START TEXT -->
				<span style="color:#b8b8b8;font-weight:700;font-size:13.5px;">Vielen Dank für Ihren Auftrag</span><br>
				<span style="color:#000;font-weight:700;font-size:20px;">Bitte überweisen Sie den Betrag auf das folgende Konto</span><br>
				<span style="color:#000;font-weight:400;font-size:14.5px;">
				Zahlungsempfänger: ' . $active_BD_Name . ' • IBAN: ' . $active_BD_IBAN . ' • BIC: ' . $active_BD_BIC . '<br><br>
Mit dem nebenstehenden Girocode können Sie die Rechnungsdaten schnell, bequem und sicher über Ihr
Smartphone in Ihre Mobile-Banking-App übernehmen. Bei Fragen wenden Sie sich bitte an Ihre Bank.
				</span>
				<!-- END TEXT -->
			</div>
			
			<div class="col-xs-3">
				<div id="qrCodeBox" style="background:#eee;padding:10px 10px 10px 10px;max-width:175px;">
					<img src="' . $qrPDFPath . '" style="background:#fff;max-height:155px;">
				</div>
			</div>
			
			<!--<div class="col-xs-1"></div>-->
		
		</div>
		<!-- end new row -->
		
		
		
		

		
		
</div>
    
	
	
</body>
</html>


';

      $gen_FileID = rand(100000, 999999);
      $gen_FileName = "Rechnung-Nr_" . $gen_FileID . ".pdf";
      $pdfFilePath = '../invoice_files/' . $gen_FileName;

      $add_DBFileName = "invoice_files/" . $gen_FileName;

      $footerTemplate = file_get_contents('../snappy_new/footer.html');
      $footerContent = str_replace('{{bd_name}}', $active_BD_Name, $footerTemplate);
      $footerContent2 = str_replace('{{bd_iban}}', $active_BD_IBAN, $footerContent);
      $footerContent3 = str_replace('{{bd_bic}}', $active_BD_BIC, $footerContent2);
      $footerContent4 = str_replace('{{footer_phone}}', $cur_Telefon, $footerContent3);

      $footerContent5 = str_replace('{{footer_company}}', $cur_Firma, $footerContent4);
      $footerContent6 = str_replace('{{footer_address}}', $cur_Strasse, $footerContent5);
      $footerContent7 = str_replace('{{footer_zip}}', $cur_PLZ, $footerContent6);
      $footerContent8 = str_replace('{{footer_city}}', $cur_Ort, $footerContent7);
      $footerContent9 = str_replace('{{footer_ust}}', $cur_Steuernummer, $footerContent8);

      $footerContent10 = str_replace('{{gericht}}', $cur_Gericht, $footerContent9);
      $footerContent11 = str_replace('{{nummer}}', $cur_Nummer, $footerContent10);

      $tempFooterPath = '../snappy_new/temp/footer_temp.html';
      file_put_contents($tempFooterPath, $footerContent11);

      $snappy->setOption('margin-bottom', '25mm');
      $snappy->setOption('enable-local-file-access', true);
      $snappy->setOption('footer-html', $tempFooterPath);

      $snappy->generateFromHtml($html_snappy, $pdfFilePath);

      unlink($tempFooterPath);
      /* ===== END: NEW SNAPPY PDF-GEN 17.11.24 ===== */

      //ADD INVOICE TO DATABASE
      $add_New_Invoice = $SQL->prepare("INSERT INTO kunden_rechnungen (user_id, order_id, pdf_file, vwz, zusatzinfos, used_bd, status) VALUES (?, ?, ?, ?, ?, ?, '1')");
      $add_New_Invoice->bind_param("iisssi", $this_Use_UID, $this_Use_OID, $gen_FileName, $this_Invoice_VWZ, $zusatzinfos, $active_BD_ID);
      $add_New_Invoice->execute();
      $add_New_Invoice->close();
      /* END: TEMPLATES FOR INVOICE */

      //UPDATE USER ORDER TO: CREATED INVOICE
      $this_Update_Order = $SQL->prepare("UPDATE kunden_bestellungen SET status = 2 WHERE order_id = ?");
      $this_Update_Order->bind_param("i", $_GET["oid"]);
      $this_Update_Order->execute();
      $this_Update_Order->close();



      /* ======= start sending invoice ======= */
      $get_Invoice = $SQL->prepare("SELECT pdf_file, used_bd FROM kunden_rechnungen WHERE order_id = ?");
      $get_Invoice->bind_param("i", $doid);
      $get_Invoice->execute();
      $get_Invoice->store_result();
      $get_Invoice->bind_result($dpdf_file, $dused_bd);
      $get_Invoice->fetch();

      //30.04.25 UPDATE BD DATA
      $get_Active_BD = $SQL->prepare("SELECT bd_name, bd_iban, bd_bic FROM bd_auswahl WHERE bd_id = ? LIMIT 1");
      $get_Active_BD->bind_param("i", $dused_bd);
      $get_Active_BD->execute();
      $get_Active_BD->store_result();
      $get_Active_BD->bind_result($active_BD_Name, $active_BD_IBAN, $active_BD_BIC);
      $get_Active_BD->fetch();

      $get_ProductsData = $SQL->prepare("SELECT gesamtsumme, anschrift, lieferadresse, payment_method, products_array, delivery_date, delivery_add, liefer_array FROM kunden_bestellungen WHERE order_id = ?");
      $get_ProductsData->bind_param("i", $doid);
      $get_ProductsData->execute();
      $get_ProductsData->store_result();
      $get_ProductsData->bind_result($dgesamt_summe, $danschrift, $lieferadresse, $d_payment_method, $dproducts_array, $d_delivery_date, $d_delivery_add, $dliefer_array);
      $get_ProductsData->fetch();

      $lieferanschriften = json_decode($dliefer_array, true);
      $products_php_array = json_decode($dproducts_array, true);

      function formatLieferadressenMailBlock($lieferanschriften)
      {
        $out = '';
        if (!$lieferanschriften || !is_array($lieferanschriften)) return '';
        $counter = 1;
        foreach ($lieferanschriften as $adr) {
          $out .= "<b>Lieferstelle #$counter</b>\n";
          $out .= trim("{$adr['anrede']} {$adr['vorname']} {$adr['nachname']}") . "\n";
          $out .= trim($adr['strasse']) . "\n";
          $out .= trim("{$adr['plz']} {$adr['ort']}") . "\n";
          $out .= trim($adr['land']) . "\n\n";
          $counter++;
        }
        return trim($out);
      }

      $format_d_gesamt = formatNumber($dgesamt_summe);

      $remind_date_add = date('Y-m-d H:i:s');

      $update_Order = $SQL->prepare("UPDATE kunden_bestellungen SET status = 3, remind_date = ? WHERE id = ?");
      $update_Order->bind_param("si", $remind_date_add, $did);
      $update_Order->execute();
      $update_Order->close();

      $fetch_User = $SQL->prepare("SELECT anrede, vorname, nachname, rufnummer, email_adresse, postleitzahl, ort, strasse FROM kunden_anschrift WHERE user_id = ?");
      $fetch_User->bind_param("i", $duid);
      $fetch_User->execute();
      $fetch_User->store_result();
      $fetch_User->bind_result($d_anrede, $d_vorname, $d_nachname, $d_rufnummer, $d_email_adresse, $d_postleitzahl, $d_ort, $d_strasse);
      $fetch_User->fetch();

      $d_fullname = $d_vorname . " " . $d_nachname;

      $attach_pdf_file = "../invoice_files/" . $dpdf_file;



      $vorname_clean = replaceUmlauts($d_vorname);
      $nachname_clean = replaceUmlauts($d_nachname);
      $ceo_clean = replaceUmlauts($cur_CEO);
      $firma_clean = replaceUmlauts($cur_Firma);

      if ($d_anrede == "Herr") {
        $craft_anrede = "Sehr geehrter Herr " . $d_vorname . " " . $d_nachname . ",";
      } else if ($d_anrede == "Frau") {
        $craft_anrede = "Sehr geehrte Frau " . $d_vorname . " " . $d_nachname . ",";
      }

      //FETCH PRODUTS
      $totalSum = 0;
      $countItem = 0;

      $sumGross = 0;
      foreach ($products_php_array as $i) {
        $sumGross += $i['price'];
      }

      //GENERATE PRODUCT ROWS
      foreach ($products_php_array as $item) {
        if ($item["type"] == "main") {
          //MAIN PRODUCT			
          /* START CONSTRUCT MAIL LINE */
          //CALC 100L PRICE
          $singlePrice = $item['price'] / $item['quantity'] / $item['lieferstellen'] * 100;
          $singlePrice_format = number_format($singlePrice, 2, ',', '.');

          $productTotalx = $item['price'];
          $singleSumx = $productTotalx;
          $singleSumx_format = number_format($singleSumx, 2, ',', '.');

          $product_line_single_full = $singlePrice * $item['quantity'];
          $product_line_single_full_liefer = $product_line_single_full * $item['lieferstellen'];
          $product_line_single_full_format = number_format($product_line_single_full_liefer, 2, ',', '.');

          if ($item['lieferstellen'] == 1) {
            $product_line_lieferstellen_suffix = "Lieferstelle";
          } else {
            $product_line_lieferstellen_suffix = "Lieferstellen";
          }

          $product_line_html_new = '<tr>
							<td  style="padding: 12px 8px; font-size: 14px; border-bottom: 1px solid #e5e7eb;">1</td>
							<td  style="padding: 12px 8px; font-size: 14px; border-bottom: 1px solid #e5e7eb;">' . $item['name'] . '</td>
							<td  style="padding: 12px 8px; font-size: 14px; border-bottom: 1px solid #e5e7eb;">' . $item['quantity'] . ' Liter bei ' . $item['lieferstellen'] . ' ' . $product_line_lieferstellen_suffix . '</td>
							<td  style="padding: 12px 8px; font-size: 14px; border-bottom: 1px solid #e5e7eb;">' . $singlePrice_format . ' € / 100l</td>
							<td  style="padding: 12px 8px; text-align: right; font-size: 14px; border-bottom: 1px solid #e5e7eb;">' . $singleSumx_format . ' €</td>
							</tr>';


          $listFinalSum = $sumGross + $global_GGVS;
          $listFinalSum_format = number_format($listFinalSum, 2, ',', '.');

          $list_lieferstellen = $item['lieferstellen'];
          /* END CONSTRUCT MAIL LINE */
        }
      }

      //GET ADDY FOR MAIL
      $lieferadresseDB_Mail = formatLieferadressenMailBlock($lieferanschriften);
      $lieferadresseDB_Mail_Line = nl2br($lieferadresseDB_Mail);

      //CREATE LIEFERDATUM
      if ($d_delivery_date != "none") {
        $parts = explode(' ', $d_delivery_date, 2);
        $datePart = $parts[0];
        $timePart = $parts[1] ?? '';
        $dt = DateTime::createFromFormat('Y-m-d', $datePart);
        $formatter = new \IntlDateFormatter('de_DE', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Europe/Berlin', \IntlDateFormatter::GREGORIAN, 'EEEE, d. MMMM');
        $deliveryDateLabel = $formatter->format($dt->getTimestamp()) . ' ' . $timePart;

        $deliveryDateSQL = $d_delivery_date;
        $deliveryDateMail = $deliveryDateLabel;
      } else {
        $deliveryDateLabel = "none";
        $deliveryDateSQL = "none";
        $deliveryDateMail = "Telefonische Abstimmung";
      }

      /* === 23.1.26 - START NEW INV MAIL-TPL === */
      $mail_table_color = "#197d00";
      // $new_mail_logo_dark  = "https://heizoel-energie.de/assets/images/email-logo.png";
      // $new_mail_logo_light = "https://heizoel-energie.de/assets/uploads/logo_69610a51a45fd9.06840555.webp";
      $new_mail_logo_dark = $cur_Domain . "/" . $cur_LogoDark;
      $new_mail_logo_light = $cur_Domain . "/" . $cur_LogoLight;
      $new_mail_icon = "https://images2.imgbox.com/49/38/znzOd5iH_o.png";
      $new_mail_inv_ivon = "https://images2.imgbox.com/ed/0e/trQijd82_o.png";

      // Calculate Netto and MwSt values for summary
      $sumNetto = $sumGross / 1.19;
      $sumMwst = $sumGross - $sumNetto;
      $sumNetto_format = number_format($sumNetto, 2, ',', '.');
      $sumMwst_format = number_format($sumMwst, 2, ',', '.');

      $html_newest = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ihre Rechnung ist bereit</title>
  <!--[if mso]>
  <style type="text/css">
    table {border-collapse: collapse;}
    .fallback-font {font-family: Arial, sans-serif;}
  </style>
  <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f6f7fb; font-family: Arial, Helvetica, sans-serif; line-height: 1.55; color: #111827;">
  <!-- Wrapper Table -->
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f6f7fb;">
    <tr>
      <td align="center" style="padding: 24px 14px;">
        <!-- Main Card -->
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 760px; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px;">

          <!-- Header -->
          <tr>
            <td style="padding: 22px; background: linear-gradient(180deg, rgba(14,165,233,0.10), rgba(255,255,255,0)); border-bottom: 1px solid #e5e7eb;">

              <!-- Header Row -->
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 16px;">
                <tr>
                  <td valign="top">
                    <table role="presentation" cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="width: 44px; height: 44px; border-radius: 14px;">
                          <img src="' . $new_mail_logo_dark . '" alt="Heizoel-Energie" style="width:150px;height:auto;">
                        </td>
                        <td style="padding-left: 12px;">
                          <p style="margin: 0; font-size: 16px; font-weight: bold; color: #111827;">' . $cur_Firma . '</p>
                          <p style="margin: 2px 0 0; font-size: 13px; color: #6b7280;">' . $cur_Strasse . ' · ' . $cur_PLZ . ' ' . $cur_Ort . ' <br> Tel. ' . $companyPhone . '</p>
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td valign="top" align="right" style="font-size: 13px; color: #6b7280;">
                    <p style="margin: 0;"><strong style="color: #111827;">Rechnung</strong></p>
                    <p style="margin: 4px 0 0;">Dok.-Nr.: <strong style="color: #111827;">' . $doid . '</strong></p>
                    <p style="margin: 4px 0 0;">Datum: <strong style="color: #111827;">' . date('d.m.Y') . '</strong></p>
                  </td>
                </tr>
              </table>

              <h2 style="margin: 0; font-size: 22px; color: #111827;">Ihre Rechnung ist bereit!</h2>
              <table role="presentation" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
                <tr>
                  <td style="padding: 8px 12px; background-color: rgba(245,158,11,0.10); border: 1px solid rgba(245,158,11,0.20); border-radius: 999px;">
                    <table role="presentation" cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="display:inline-table; width: 8px; height: 8px; background-color: #f59e0b; border-radius: 999px;"></td>
                        <td style="padding-left: 8px; font-size: 13px; font-weight: 600; color: #92400e;">Bitte Zahlung innerhalb von 48 Stunden vornehmen</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- end header -->

          <!-- Content -->
          <tr>
            <td style="padding: 16px 20px 10px;">
              <p style="margin: 0 0 12px; font-size: 14px; color: #111827;">
                Überweisen Sie den Rechnungsbetrag innerhalb von <strong>48 Stunden</strong>, um den festgelegten Liefertermin zu bestätigen.
                Bitte geben Sie bei der Überweisung den <strong>vorgegebenen Verwendungszweck</strong> an, damit wir Ihre Zahlung im System zuordnen können.
              </p>

              <!-- Zahlungsdaten Box -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #e5e7eb; border-radius: 14px; margin: 14px 0;">
                <tr>
                  <td style="padding: 14px;">
                    <p style="margin: 0 0 10px; font-size: 13px; color: #6b7280; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em;">Zahlungsdaten</p>

                    <!-- Two Column Layout: Payment Data + QR Code -->
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <!-- Left Column: Payment Data -->
                        <td valign="top" style="padding-right: 10px;">
                          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 14px;">
                            <tr>
                              <td width="170" style="padding: 4px 0; color: #6b7280;">Empfänger</td>
                              <td style="padding: 4px 0; color: #111827; font-weight: 700;">' . $active_BD_Name . '</td>
                            </tr>
                            <tr>
                              <td width="170" style="padding: 4px 0; color: #6b7280;">IBAN</td>
                              <td style="padding: 4px 0; color: #111827; font-weight: 700;">' . $active_BD_IBAN . '</td>
                            </tr>
                            <tr>
                              <td width="170" style="padding: 4px 0; color: #6b7280;">BIC</td>
                              <td style="padding: 4px 0; color: #111827; font-weight: 700;">' . $active_BD_BIC . '</td>
                            </tr>
                            <tr>
                              <td width="170" style="padding: 4px 0; color: #6b7280;">Betrag</td>
                              <td style="padding: 4px 0; color: #111827; font-weight: 700;"><strong>' . $listFinalSum_format . ' &euro;</strong></td>
                            </tr>
                            <tr>
                              <td width="170" style="padding: 4px 0; color: #6b7280;">Verwendungszweck</td>
                              <td style="padding: 4px 0; color: #111827; font-weight: 700;"><strong>' . $doid . '</strong></td>
                            </tr>
                          </table>
                        </td>

                        <!-- Right Column: QR Code -->
                        <td valign="top" align="right" width="150" style="padding-left: 10px;">
                          <img src="' . $qrPDFPath . '" alt="QR Code für Zahlungsdaten" width="140" height="140" style="display: block; border: 1px solid #e5e7eb; border-radius: 8px;" />
                        </td>
                      </tr>
                    </table>

                    <!-- Bottom Text -->
                    <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid #e5e7eb;">
                      <p style="margin: 0; font-size: 13px; color: #6b7280; line-height: 1.6;">
                        <strong style="color: #111827;">Mit dem nebenstehenden Girocode können Sie die Rechnungsdaten schnell, bequem und sicher über Ihr Smartphone in Ihre Mobile-Banking-App übernehmen. Bei Fragen wenden Sie sich bitte an Ihre Bank.
                      </p>
                    </div>
                  </td>
                </tr>
              </table>

               <!-- Trusted Shops Käuferschutz -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                style="margin: 18px 0;">
                <tr>
                  <td style="padding: 14px; border: 1px solid #e5e7eb; border-radius: 14px; background-color: #f9fafb;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td width="64" valign="top" style="padding-right: 14px;">
                          <a href="' . $cur_Domain . '/trustedshops" target="_blank">
                            <img src="' . $cur_Domain . '/assets/images/trusted-shops-seal.png" alt="Trusted Shops Käuferschutz"
                              width="60" height="60"
                              style="display:block; border:0; outline:none; text-decoration:none;" />
                          </a>
                        </td>
                        <td valign="top" style="font-size: 14px; color: #111827; line-height: 1.6;">
                          <strong>Trusted Shops Käuferschutz</strong><br />
                          Dieses Unternehmen bietet den Trusted Shops Käuferschutz für Einkäufe bis
                          <strong>20.000&nbsp;€</strong> an.<br /><br />
                          <a href="https://www.trustedshops.de/kaeuferschutz/" target="_blank" style="color:#6b7280;">
                            So ist dein Einkauf abgesichert.
                          </a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Lieferzeitraum Box -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #e5e7eb; border-radius: 14px; margin: 14px 0;">
                <tr>
                  <td style="padding: 14px;">
                    <p style="margin: 0 0 10px; font-size: 13px; color: #6b7280; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em;">Lieferzeitraum</p>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                      ' . $deliveryDateMail . ' / Der Liefertermin wird erst nach Zahlungseingang auf unser Bankkonto bestätigt.
                      Bitte überweisen Sie innerhalb von <strong style="color: #111827;">48 Stunden</strong> den Rechnungsbetrag.
                    </p>
                    <div style="height: 1px; background-color: #e5e7eb; margin: 14px 0;"></div>
                    <p style="margin: 0; font-size: 14px; color: #111827;">
                      Eine Bestätigungsemail folgt nach dem Zahlungseingang.
                    </p>
                  </td>
                </tr>
              </table>

              <!-- Hinweis bei Spätzahlung -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px dashed rgba(2,132,199,0.35); background-color: rgba(14,165,233,0.08); border-radius: 14px; margin: 14px 0;">
                <tr>
                  <td style="padding: 14px; font-size: 14px; color: #0b4a6f;">
                    <strong style="color: #083344;">Hinweis bei Spätzahlung:</strong><br />
                    Bei Spätzahlung rufen wir Sie zu einer erneuten Terminvereinbarung an.
                  </td>
                </tr>
              </table>

              <!-- Rechnungspositionen Box -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #e5e7eb; border-radius: 14px; margin: 14px 0;">
                <tr>
                  <td style="padding: 14px;">
                    <p style="margin: 0 0 10px; font-size: 13px; color: #6b7280; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em;">Rechnungspositionen</p>

                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <th style="padding: 12px 8px; font-size: 14px; color: #6b7280; font-weight: 400; text-align: left; border-bottom: 1px solid #e5e7eb; width: 50px;">Pos.</th>
                        <th style="padding: 12px 8px; font-size: 14px; color: #6b7280; font-weight: 400; text-align: left; border-bottom: 1px solid #e5e7eb; width: 180px;">Produkt</th>
                        <th style="padding: 12px 8px; font-size: 14px; color: #6b7280; font-weight: 400; text-align: left; border-bottom: 1px solid #e5e7eb;">Anzahl</th>
                        <th style="padding: 12px 8px; font-size: 14px; color: #6b7280; font-weight: 400; text-align: left; border-bottom: 1px solid #e5e7eb;">Einzelpreis<br><span style="font-size: 12px;">Inkl. USt</span></th>
                        <th style="padding: 12px 8px; font-size: 14px; color: #6b7280; font-weight: 400; text-align: left; border-bottom: 1px solid #e5e7eb;">Gesamt<br><span style="font-size: 12px;">Inkl. USt</span></th>
                      </tr>
                      <!-- PRODUCTS START -->
                      ' . $product_line_html_new . '
                      <!-- PRODUCTS END -->
                      <tr>
                        <td style="padding: 12px 8px; font-size: 14px; color: #111827; border-bottom: 1px solid #e5e7eb;">2</td>
                        <td style="padding: 12px 8px; font-size: 14px; color: #111827; border-bottom: 1px solid #e5e7eb;">Lieferung</td>
                        <td style="padding: 12px 8px; font-size: 14px; color: #111827; border-bottom: 1px solid #e5e7eb;">kostenfrei</td>
                        <td style="padding: 12px 8px; font-size: 14px; color: #111827; border-bottom: 1px solid #e5e7eb;">0,00 &euro;</td>
                        <td style="padding: 12px 8px; font-size: 14px; color: #111827; border-bottom: 1px solid #e5e7eb; text-align: right;">0,00 &euro;</td>
                      </tr>
                    </table>

                    <!-- Summary Section -->
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 24px;">
                      <tr>
                        <td style="padding: 6px 8px; font-size: 14px; color: #6b7280; text-align: right;">Gesamtsumme (Netto):</td>
                        <td style="padding: 6px 8px; font-size: 14px; color: #111827; text-align: right; width: 120px;">' . $this_Net_Amount . ' &euro;</td>
                      </tr>
                      <tr>
                        <td style="padding: 6px 8px; font-size: 14px; color: #6b7280; text-align: right;">19% MwSt.:</td>
                        <td style="padding: 6px 8px; font-size: 14px; color: #111827; text-align: right;">' . $this_Tax_Amount . ' &euro;</td>
                      </tr>
                      <tr>
                        <td style="padding: 6px 8px; font-size: 14px; color: #6b7280; text-align: right;">GGVS-Umlage</td>
                        <td style="padding: 6px 8px; font-size: 14px; color: #111827; text-align: right;">42,59 &euro;</td>
                      </tr>
                      <tr>
                        <td style="padding: 12px 8px 6px; font-size: 14px; color: #111827; font-weight: 700; text-align: right;"><strong>Gesamtsumme Brutto:</strong></td>
                        <td style="padding: 12px 8px 6px; font-size: 14px; color: #111827; font-weight: 700; text-align: right;"><strong>' . $listFinalSum_format . ' &euro;</strong></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Lieferanschrift Box -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #e5e7eb; border-radius: 14px; margin: 14px 0;">
                <tr>
                  <td style="padding: 14px;">
                    <p style="margin: 0 0 10px; font-size: 13px; color: #6b7280; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em;">Lieferanschrift</p>
                    <p style="margin: 0; font-size: 14px; color: #111827; line-height: 1.6;">
                      ' . $lieferadresseDB_Mail_Line . '
                    </p>
                  </td>
                </tr>
              </table>

              <!-- Geschätzter Lieferzeitraum Box -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #e5e7eb; border-radius: 14px; margin: 14px 0;">
                <tr>
                  <td style="padding: 14px;">
                    <p style="margin: 0 0 10px; font-size: 13px; color: #6b7280; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em;">Geschätzter Lieferzeitraum</p>
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 14px;">
                      <tr>
                        <td width="170" style="padding: 4px 0; color: #6b7280;">Status</td>
                        <td style="padding: 4px 0; color: #111827; font-weight: 700;"><strong>' . $deliveryDateMail . '</strong></td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Wichtig zu wissen Box -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #e5e7eb; border-radius: 14px; margin: 14px 0;">
                <tr>
                  <td style="padding: 14px;">
                    <p style="margin: 0 0 10px; font-size: 13px; color: #6b7280; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em;">Wichtig zu wissen</p>
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 14px; line-height: 1.7;">
                      <tr>
                        <td style="padding: 0 0 8px 0;">
                          <strong>Liefergarantie</strong><br />
                          Wir garantieren die Lieferung am vereinbarten Termin, sofern die Rechnung innerhalb von
                          <strong>48 Stunden nach Erhalt</strong> beglichen wurde.
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 8px 0;">
                          <strong>Abnahmemenge-Garantie</strong><br />
                          Sollten Sie statt der bestellten Menge (z. B. 1.500 Liter) weniger abnehmen können (z. B. 1.400 Liter),
                          erstatten wir Ihnen den Differenzbetrag selbstverständlich zurück.
                        </td>
                      </tr>
                      <tr>
                        <td style="padding: 8px 0 0 0;">
                          <strong>Zahlung</strong><br />
                          Aus Sicherheitsgründen bieten wir keine Barzahlung beim Fahrer mehr an.
                          Die Bezahlung erfolgt ausschließlich per Überweisung laut der – im Anhang befindlichen – Rechnung.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Die nächsten Schritte Box -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid #e5e7eb; border-radius: 14px; margin: 14px 0;">
                <tr>
                  <td style="padding: 14px;">
                    <p style="margin: 0 0 10px; font-size: 13px; color: #6b7280; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em;">Die nächsten Schritte</p>
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 14px; line-height: 1.75;">
                      <tr>
                        <td width="24" valign="top" style="padding: 0 8px 8px 0; font-weight: bold;">1.</td>
                        <td style="padding: 0 0 8px 0;">
                          <strong>Rechnung &amp; Zahlung</strong><br />
                          Bitte begleichen Sie den Rechnungsbetrag innerhalb von <strong>48 Stunden</strong>, damit wir den Liefertermin
                          verbindlich bestätigen können.
                        </td>
                      </tr>
                      <tr>
                        <td width="24" valign="top" style="padding: 8px 8px 8px 0; font-weight: bold;">2.</td>
                        <td style="padding: 8px 0;">
                          <strong>Zahlungseingang</strong><br />
                          Nach Zahlungseingang auf unserem Bankkonto erhalten Sie die Terminbestätigung per E-Mail.
                        </td>
                      </tr>
                      <tr>
                        <td width="24" valign="top" style="padding: 8px 8px 0 0; font-weight: bold;">3.</td>
                        <td style="padding: 8px 0 0 0;">
                          <strong>Lieferung</strong><br />
                          Das Heizöl wird geliefert und bei Ihnen getankt. Bitte seien Sie an diesem Termin erreichbar.
                          Sollten Sie den Termin verschieben wollen, rufen Sie uns einfach unter unserer Telefonnummer an:
                          <strong>' . $companyPhone . '</strong>.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Wichtiger Hinweis -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px dashed rgba(2,132,199,0.35); background-color: rgba(14,165,233,0.08); border-radius: 14px; margin: 14px 0;">
                <tr>
                  <td style="padding: 14px; font-size: 14px; color: #0b4a6f;">
                    <strong style="color: #083344;">Wichtiger Hinweis:</strong><br />
                    Sollten Sie den Termin verpassen oder nicht wahrnehmen, werden weitere <strong>3 Zustellversuche</strong>
                    an einem Folgetag versucht.
                  </td>
                </tr>
              </table>

              <!-- Zusammenfassung Callout -->
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 1px solid rgba(22,163,74,0.22); background-color: rgba(22,163,74,0.08); border-radius: 14px; margin: 14px 0;">
                <tr>
                  <td style="padding: 14px; font-size: 14px; color: #14532d;">
                    <strong style="color: #052e16;">Zusammenfassung:</strong><br />
                    Bitte überweisen Sie <strong>' . $listFinalSum_format . ' &euro;</strong> innerhalb von <strong>48 Stunden</strong> mit dem Verwendungszweck
                    <strong>' . $doid . '</strong>. Danach erhalten Sie die Terminbestätigung per E-Mail.
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding: 16px 22px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280;">
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td valign="top" style="width: 120px; padding-right: 16px;">
                    <img src="' . $new_mail_logo_dark . '" alt="Logo" style="width: 100px; height: auto;">
                  </td>
                  <td valign="top">
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                      <tr>
                        <td>
                          <strong style="color: #111827;">' . $cur_Firma . '</strong> · Sitz: ' . $cur_Ort . ' · ' . $cur_Nummer . ' · USt-IdNr. ' . $cur_Steuernummer . '
                        </td>
                      </tr>
                      <tr>
                        <td style="padding-top: 8px;">
                          Kontakt: <a href="mailto:' . $cur_SMTPUser . '" style="color: #0284c7; text-decoration: none;">' . $cur_SMTPUser . '</a> · <a href="tel:' . $companyPhone . '" style="color: #0284c7; text-decoration: none;">' . $companyPhone . '</a>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding-top: 4px; color: #6b7280;">
                          Dieses Dokument wurde elektronisch erstellt und ist ohne Unterschrift gültig.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
';
      /* === 21.10.25 - END NEW INV MAIL-TPL === */


      $cleanedHtml = cleanHtmlForEmail($html_newest);
      /* ==== END: NEW INVOICE-MAIL TEMPLATE ===== */







      $mail = new PHPMailer(true);


      $mail->isSMTP();
      $mail->CharSet = 'UTF-8';
      $mail->Encoding = 'base64';
      $mail->Host = $cur_SMTPHost;
      $mail->SMTPAuth = true;
      $mail->Username = $cur_SMTPUser;
      $mail->Password = $cur_SMTPPass;
      $mail->SMTPSecure = $cur_SMTPType;
      $mail->Port = $cur_SMTPPort;

      $mail->setFrom($cur_SMTPUser, $cur_Title);
      $mail->XMailer = '';
      $mail->addAddress($d_email_adresse, $d_fullname);


      if (file_exists($attach_pdf_file)) {
        $mail->addAttachment($attach_pdf_file);
      } else {
        echo 'Attachment error';
      }

      $mail->isHTML(true);

      $mail->Subject = 'Ihre Rechnung von ' . $cur_Title;
      $mail->Body    = $cleanedHtml;
      $mail->send();
      /* ======= end sending invoice ======= */

      $show_Success_Msg = "Du hast für diesen Kunden soeben eine PDF-Rechnung erstellt + versendet mit dem BD von $active_BD_Name ($active_BD_IBAN).";
      header("Refresh: 2; URL=index.php");
      //exit();
    }
  }

  if (isset($_POST["add_new_bd"])) {
    $add_agent      = $_POST["agent"];
    $add_name       = $_POST["name"];
    $add_iban       = $_POST["iban"];
    $add_bic        = $_POST["bic"];
    $add_status     = 0;
    $gen_id         = rand(100000, 999999);

    $add_daily_limit    = isset($_POST["daily_limit"]) ? intval($_POST["daily_limit"]) : 0;
    $add_total_limit    = isset($_POST["total_limit"]) ? intval($_POST["total_limit"]) : 0;

    $execute_SQL = $SQL->prepare("
    				    INSERT INTO bd_auswahl 
    				    (bd_id, bd_agent, bd_name, bd_iban, bd_bic, status, daily_limit, total_limit, daily_used, total_used, last_reset)
    				    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NULL)
    				");
    $execute_SQL->bind_param("issssiii", $gen_id, $add_agent, $add_name, $add_iban, $add_bic, $add_status, $add_daily_limit, $add_total_limit);
    $execute_SQL->execute();
    $execute_SQL->close();

    $show_Success_Msg = "Du hast soeben einen neuen Bankdrop inkl. RG-Limits hinzugefügt.";
  }

  if (isset($_POST["edit_bd_now"])) {
    $execute_ID  = $_POST["execute_id"];
    $edit_agent  = $_POST["agent"];
    $edit_name   = $_POST["name"];
    $edit_iban   = $_POST["iban"];
    $edit_bic    = $_POST["bic"];

    $edit_daily_limit = isset($_POST["daily_limit"]) ? intval($_POST["daily_limit"]) : 0;
    $edit_total_limit = isset($_POST["total_limit"]) ? intval($_POST["total_limit"]) : 0;

    $execute_SQL = $SQL->prepare("
    				    UPDATE bd_auswahl 
    				    SET bd_agent = ?, bd_name = ?, bd_iban = ?, bd_bic = ?, daily_limit = ?, total_limit = ?
    				    WHERE bd_id = ?
    				");
    $execute_SQL->bind_param("ssssiii", $edit_agent, $edit_name, $edit_iban, $edit_bic, $edit_daily_limit, $edit_total_limit, $execute_ID);
    $execute_SQL->execute();
    $execute_SQL->close();

    $show_Success_Msg = "Du hast soeben diesen Bankdrop inkl. RG-Limits aktualisiert.";
  }


  if ($_GET["settings"] == "bd" && $_GET["do"] == "bd-disable" && isset($_GET["bid"])) {
    //DISABLE BANKDROP
    $execute_SQL = $SQL->prepare("UPDATE bd_auswahl SET status = 0 WHERE bd_id = ?");
    $execute_SQL->bind_param("i", $_GET["bid"]);
    $execute_SQL->execute();
    $execute_SQL->close();

    $show_Success_Msg = "Du hast diesen Bankdrop soeben deaktiviert.";
  }

  if ($_GET["settings"] == "bd" && $_GET["do"] == "bd-enable" && isset($_GET["bid"])) {
    //ENABLE BANKDROP
    $execute_SQL = $SQL->prepare("UPDATE bd_auswahl SET status = 0");
    $execute_SQL->execute();
    $execute_SQL->close();

    $execute_SQL_Second = $SQL->prepare("UPDATE bd_auswahl SET status = 1 WHERE bd_id = ?");
    $execute_SQL_Second->bind_param("i", $_GET["bid"]);
    $execute_SQL_Second->execute();
    $execute_SQL_Second->close();

    $show_Success_Msg = "Du hast diesen Bankdrop soeben aktiviert.";
  }

  if ($_GET["settings"] == "bd" && $_GET["do"] == "bd-delete" && isset($_GET["bid"])) {
    //DELETE BANKDROP - CHECK IF BD HAS INVOICES
    $check_For_Active_BDInv = $SQL->prepare("SELECT COUNT(id) FROM kunden_rechnungen WHERE used_bd = ?");
    $check_For_Active_BDInv->bind_param("i", $_GET["bid"]);
    $check_For_Active_BDInv->execute();
    $check_For_Active_BDInv->store_result();
    $check_For_Active_BDInv->bind_result($this_SafeCheck_BDInv);
    $check_For_Active_BDInv->fetch();

    if ($this_SafeCheck_BDInv == 0) {
      //DELETE BD NOW
      $execute_SQL = $SQL->prepare("DELETE FROM bd_auswahl WHERE bd_id = ?");
      $execute_SQL->bind_param("i", $_GET["bid"]);
      $execute_SQL->execute();
      $execute_SQL->close();

      $show_Success_Msg = "Du hast diesen Bankdrop soeben aus dem System gelöscht.";
    } else if ($this_SafeCheck_BDInv > 0) {
      //BD HAS INVOICES
      $show_Error_Msg = "Dieser Bankdrop verfügt über aktive Rechnungen. Bitte nicht löschen, um Systemfehler zu vermeiden.";
    }
  }
  /* END: NEW BD ACTIONS */


?>

  <!DOCTYPE html>
  <html lang="de">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= $cur_Title; ?> - adminCP</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
      .badge-primary {
        background-color: #0096d6;
        color: #fff !important;
      }

      .badge-success {
        background-color: #c7f5d9;
        color: #0b4121 !important;
      }

      .badge-warning {
        background-color: #ffebc2;
        color: #453008 !important;
      }

      .badge-secondary {
        background-color: #ebcdfe;
        color: #6e02b1 !important;
      }

      .badge-dark {
        color: #fff;
        background-color: #343a40;
      }

      .badge {
        border-radius: .27rem;
      }

      .badge {
        display: inline-block;
        padding: .45em .65em;
        font-size: .85em;
        font-weight: 400;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
      }
    </style>
    <style>
      [data-tooltip] {
        position: relative;
        cursor: pointer;
      }

      [data-tooltip]::after {
        content: attr(data-tooltip);
        visibility: hidden;
        background-color: #333;
        color: #fff;
        text-align: center;
        padding: 5px;
        border-radius: 5px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        white-space: nowrap;
      }

      /* Tooltip arrow */
      [data-tooltip]::before {
        content: '';
        visibility: hidden;
        position: absolute;
        z-index: 1;
        bottom: 115%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
        opacity: 0;
        transition: opacity 0.3s;
      }

      [data-tooltip]:hover::after,
      [data-tooltip]:hover::before {
        visibility: visible;
        opacity: 1;
      }
    </style>

    <style>
      .btn-yolo {
        background-color: #d76953;
        color: #fff;
      }

      .btn-yolo:hover {
        background-color: #e8725b;
        color: #fff;
      }

      .btn-lol {
        background-color: #b87333;
        color: #fff;
      }

      .btn-lol:hover {
        background-color: #e3832a;
        color: #fff;
      }

      .btn-euro {
        background-color: #6ec40c;
        color: #fff;
      }

      .btn-euro:hover {
        background-color: #66b20f;
        color: #fff;
      }

      .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1.2rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #e8e5dd;
        background-clip: padding-box;
        border: 1px dashed #ced4da;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border-radius: 0.45rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
      }

      .form-control:focus {
        font-size: 1.2rem;
        background-color: #e8e5dd;
        border: 1px dashed #ced4da;
        border-radius: 0.45rem;
      }

      label {
        font-size: 1.2rem;
        letter-spacing: 0.5px;
        border-bottom: 1px dotted #eee;
      }
    </style>

    <!-- new btns start -->
    <style>
      .btn-one {
        background-color: #f4cb35;
        color: #333;
      }

      .btn-one:hover {
        background-color: #f8de7e;
        color: #333;
      }

      /* NEXT */

      .btn-two {
        background-color: #780606;
        color: #fff;
      }

      .btn-two:hover {
        background-color: #a90808;
        color: #fff;
      }

      /* NEXT */

      .btn-three {
        background-color: #702963;
        color: #fff;
      }

      .btn-three:hover {
        background-color: #a83e95;
        color: #333;
      }

      /* NEXT */

      .btn-four {
        background-color: #a0db8e;
        color: #333;
      }

      .btn-four:hover {
        background-color: #7fcf67;
        color: #333;
      }

      /* NEXT */

      .btn-five {
        background-color: #ca1f7b;
        color: #fff;
      }

      .btn-five:hover {
        background-color: #de2588;
        color: #fff;
      }
    </style>
    <!-- new btns end -->
    <link href="../assets/css/bootstrap-icons.css" rel="stylesheet">
  </head>

  <body>
    <div class="d-flex" id="wrapper">
      <div class="border-end bg-white" id="sidebar-wrapper" style="display:none;">
        <div class="sidebar-heading border-bottom bg-light"><b><?= $cur_Title; ?></b>
          <small>adminCP v1.8 <small>(01.11.25)</small></small>
        </div>
        <!--<div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="index.php"><b>Bestellungen (aktuell)</b></a>
					<a class="list-group-item list-group-item-action list-group-item-light p-3" href="bd_data.php">Bankdrop-Daten</a>
					<a class="list-group-item list-group-item-action list-group-item-light p-3" href="settings.php">Einstellungen</a>
                </div>-->
      </div>



      <!-- START PAGE -->
      <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">

          <div class="container-fluid" style="">
            <h1><?= $cur_Title; ?> - adminCP v1.8 <small>(01.11.25)</small></h1>

            <a href="index.php" class="btn btn-primary">Bestellungen aufrufen</a>&nbsp;<a href="index.php?settings=bd" class="btn btn-success">Bankdrop-Verwaltung</a>&nbsp;<a href="index.php?settings=main" class="btn btn-warning">Preise anpassen</a>&nbsp;<a href="index.php?settings=data" class="btn btn-secondary">Angaben ändern</a>&nbsp;<a href="?a=sesskill" class="btn btn-danger" style="float:right!important;">Session killen</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>

          </div>
        </nav>

        <div class="container-fluid">
          <!-- start adm -->








          <div id="contentContainerMain" style="margin-top:50px;margin-bottom:50px;">

            <?php if (isset($success)) { ?>
              <div class="alert alert-success"><?= $success; ?></div>
            <?php } ?>

            <?php

            if ($_GET["settings"] == "bd") {
              //BD SETTINGS

            ?>

              <div id="adminFormContainer" style="background-color:#fafafa;border:1px solid #d3d3d3;padding:55px 55px 55px 55px;border-radius:7px;margin-top:150px;margin-left:150px;margin-right:150px;">

                <h2 style="letter-spacing:0.5px;">BD-Verwaltung</h2>
                <p style="font-size:18px;letter-spacing:0.3px;">Hier kannst du die BD-Daten ändern, welche anschließend automatisiert auf den PDF-Rechnungen platziert werden.</p>
                <hr />

                <?php

                if ($_GET["bdaction"] == "add") {
                  //ADD NEW BD

                ?>

                  <h4>Neuen Bankdrop hinzufügen</h4>
                  <form action="index.php?settings=bd" method="POST">

                    <div class="row" style="max-width: 600px;">
                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label>Agenten-Name / Markierung</label>
                        <input type="text" name="agent" required class="form-control">
                      </div>

                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label>Empfänger-Name</label>
                        <input type="text" name="name" required class="form-control">
                      </div>

                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label>IBAN</label>
                        <input type="text" name="iban" required class="form-control">
                      </div>

                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label>BIC/SWIFT</label>
                        <input type="text" name="bic" required class="form-control">
                      </div>

                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label for="daily_limit">Tägliches Limit</label>
                        <div class="d-flex align-items-center">
                          <input type="number" name="daily_limit" id="daily_limit_input" max="70" class="form-control mr-2" min="0" value="0">
                          <input type="range" id="daily_limit_slider" min="0" max="70" value="0" class="form-control-range">
                        </div>
                      </div>

                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label for="total_limit">Gesamt-Limit</label>
                        <div class="d-flex align-items-center">
                          <input type="number" name="total_limit" id="total_limit_input" max="70" class="form-control mr-2" min="0" value="0">
                          <input type="range" id="total_limit_slider" min="0" max="70" value="0" class="form-control-range">
                        </div>
                      </div>

                      <!--
	<div class="col-md-6" style="margin-bottom:20px;">
		<label>Tägliches RG-Limit: <span id="dailyValue">50</span></label>
		<input type="range" name="daily_limit" class="form-range" min="0" max="500" value="50" id="dailySlider" oninput="document.getElementById('dailyValue').innerText = this.value">
	</div>

	<div class="col-md-6" style="margin-bottom:20px;">
		<label>Gesamtes RG-Limit: <span id="totalValue">500</span></label>
		<input type="range" name="total_limit" class="form-range" min="0" max="5000" value="500" id="totalSlider" oninput="document.getElementById('totalValue').innerText = this.value">
	</div>
	-->
                      <div class="col-md-12" style="margin-top:10px;">
                        <button type="submit" name="add_new_bd" class="btn btn-yolo btn-lg" style="width:100%;text-align:center;letter-spacing:0.2px;">
                          Bankdrop jetzt hinzufügen 👍
                        </button>
                      </div>
                    </div>

                  </form>


                <?php

                } else if ($_GET["bdaction"] == "edit" && isset($_GET["bid"])) {
                  //EDIT BD
                  $edit_bid = $_GET["bid"];

                  $fetch_bd_data = $SQL->prepare("SELECT bd_agent, bd_name, bd_iban, bd_bic, daily_limit, total_limit FROM bd_auswahl WHERE bd_id = ?");
                  $fetch_bd_data->bind_param("i", $edit_bid);
                  $fetch_bd_data->execute();
                  $fetch_bd_data->store_result();
                  $fetch_bd_data->bind_result($xyz_bd_agent, $xyz_bd_name, $xyz_bd_iban, $xyz_bd_bic, $xyz_daily_limit, $xyz_total_limit);
                  $fetch_bd_data->fetch();

                ?>

                  <h4>Bankdrop <b>"<?= $xyz_bd_name; ?>"</b> bearbeiten</h4>
                  <form action="index.php?settings=bd" method="POST">

                    <div class="row" style="max-width:600px;">
                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label>Agenten-Name / Markierung</label>
                        <input type="text" name="agent" required value="<?= htmlspecialchars($xyz_bd_agent); ?>" class="form-control">
                      </div>
                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label>Empfänger-Name</label>
                        <input type="text" name="name" required value="<?= htmlspecialchars($xyz_bd_name); ?>" class="form-control">
                      </div>
                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label>IBAN</label>
                        <input type="text" name="iban" required value="<?= htmlspecialchars($xyz_bd_iban); ?>" class="form-control">
                      </div>
                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label>BIC/SWIFT</label>
                        <input type="text" name="bic" required value="<?= htmlspecialchars($xyz_bd_bic); ?>" class="form-control">
                      </div>

                      <!--<div class="col-md-6" style="margin-bottom:20px;">
		<label>Tägliches RG-Limit: <span id="dailyValue"><?= intval($xyz_daily_limit); ?></span></label>
		<input type="range" name="daily_limit" class="form-range" min="0" max="500" value="<?= intval($xyz_daily_limit); ?>" oninput="document.getElementById('dailyValue').innerText = this.value">
	</div>

	<div class="col-md-6" style="margin-bottom:20px;">
		<label>Gesamtes RG-Limit: <span id="totalValue"><?= intval($xyz_total_limit); ?></span></label>
		<input type="range" name="total_limit" class="form-range" min="0" max="5000" value="<?= intval($xyz_total_limit); ?>" oninput="document.getElementById('totalValue').innerText = this.value">
	</div>-->

                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label for="daily_limit">Tägliches Limit</label>
                        <div class="d-flex align-items-center">
                          <input type="number" name="daily_limit" id="daily_limit_input" class="form-control mr-2" min="0" max="70" value="<?= $xyz_daily_limit; ?>">
                          <input type="range" id="daily_limit_slider" min="0" max="70" value="<?= $xyz_daily_limit; ?>" class="form-control-range">
                        </div>
                      </div>

                      <div class="col-md-6" style="margin-bottom:20px;">
                        <label for="total_limit">Gesamt-Limit</label>
                        <div class="d-flex align-items-center">
                          <input type="number" name="total_limit" id="total_limit_input" class="form-control mr-2" min="0" max="70" value="<?= $xyz_total_limit; ?>">
                          <input type="range" id="total_limit_slider" min="0" max="70" value="<?= $xyz_total_limit; ?>" class="form-control-range">
                        </div>
                      </div>

                      <div class="col-md-12" style="margin-top:20px;">
                        <input type="hidden" name="execute_id" value="<?= $edit_bid; ?>">
                        <button type="submit" name="edit_bd_now" class="btn btn-yolo btn-lg" style="width:100%;text-align:center;letter-spacing:0.2px;">Bankdrop jetzt aktualisieren 👍</button>
                      </div>
                    </div>

                  </form>

                <?php

                } else if ($_GET["bdaction"] == "show" && isset($_GET["bid"])) {
                  //SHOW BD INVOICES
                  $show_bid = $_GET["bid"];

                  $fetch_bd_data = $SQL->prepare("SELECT bd_agent, bd_name, bd_iban, bd_bic FROM bd_auswahl WHERE bd_id = ?");
                  $fetch_bd_data->bind_param("i", $show_bid);
                  $fetch_bd_data->execute();
                  $fetch_bd_data->store_result();
                  $fetch_bd_data->bind_result($xyz_bd_agent, $xyz_bd_name, $xyz_bd_iban, $xyz_bd_bic);
                  $fetch_bd_data->fetch();

                  //GET SUM FROM THIS INVOICES BD
                  $get_BD_Orders = $SQL->prepare("SELECT order_id FROM kunden_rechnungen WHERE used_bd = ?");
                  $get_BD_Orders->bind_param("i", $show_bid);
                  $get_BD_Orders->execute();
                  $get_BD_Orders->store_result();
                  $get_BD_Orders->bind_result($this_search_bd_oid);

                  $this_BD_Overview_Sum = 0;

                  while ($get_BD_Orders->fetch()) {
                    //FETCH SUM
                    $get_BD_Order_Sum = $SQL->prepare("SELECT gesamtsumme FROM kunden_bestellungen WHERE order_id = ? ORDER BY id ASC");
                    $get_BD_Order_Sum->bind_param("i", $this_search_bd_oid);
                    $get_BD_Order_Sum->execute();
                    $get_BD_Order_Sum->store_result();
                    $get_BD_Order_Sum->bind_result($this_search_bd_gesamtsumme);
                    $get_BD_Order_Sum->fetch();

                    //ADD IT TO SUM
                    $this_BD_Overview_Sum += $this_search_bd_gesamtsumme;
                  }

                ?>

                  <!-- START INVOICE BD-TABLE -->
                  <a href="index.php?settings=bd" class="btn btn-primary btn-md">Bankdropübersicht anzeigen &raquo;</a>
                  <hr />
                  <h4>Alle Rechnungen des Bankdrops "<b><?= $xyz_bd_agent; ?></b>" (<?= $xyz_bd_name; ?> - <?= $xyz_bd_iban; ?>/<?= $xyz_bd_bic; ?>)</h4>
                  <h5><b>Aktueller Betrag:</b> <?php echo formatNumber($this_BD_Overview_Sum); ?> &euro;</h5>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead class="thead-light">
                        <tr>
                          <th></th>
                          <th>Order-ID</th>
                          <th>Kunde</th>
                          <th>Bankdrop</th>
                          <th>PDF-File</th>
                          <th>Betrag</th>
                          <th>Bestelldatum</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php

                        $get_All_BDs_Invcs = $SQL->prepare("SELECT user_id, order_id, pdf_file, status FROM kunden_rechnungen WHERE used_bd = ?");
                        $get_All_BDs_Invcs->bind_param("i", $show_bid);
                        $get_All_BDs_Invcs->execute();
                        $get_All_BDs_Invcs->store_result();
                        $get_All_BDs_Invcs->bind_result($bd_inv_uid, $bd_inv_oid, $bd_inv_pdf, $bd_inv_status);

                        $thisBDInvID = 0;

                        while ($get_All_BDs_Invcs->fetch()) {

                          $thisBDInvID++;

                          //CALC VALUE OF ORDER
                          $calc_All_BDs = $SQL->prepare("SELECT gesamtsumme, creation_date FROM kunden_bestellungen WHERE order_id = ? AND user_id = ?");
                          $calc_All_BDs->bind_param("ii", $bd_inv_oid, $bd_inv_uid);
                          $calc_All_BDs->execute();
                          $calc_All_BDs->store_result();
                          $calc_All_BDs->bind_result($bd_inv_sum, $bd_inv_date);
                          $calc_All_BDs->fetch();

                          //GET USER DATA
                          $userInv_All_BDs = $SQL->prepare("SELECT vorname, nachname FROM kunden_anschrift WHERE user_id = ?");
                          $userInv_All_BDs->bind_param("i", $bd_inv_uid);
                          $userInv_All_BDs->execute();
                          $userInv_All_BDs->store_result();
                          $userInv_All_BDs->bind_result($bd_inv_vorname, $bd_inv_nachname);
                          $userInv_All_BDs->fetch();

                          //CLEAN PDF
                          $clean_pdf_file = getFilenameFromPath($bd_inv_pdf);

                        ?>

                          <tr>

                            <td>#<?= $thisBDInvID; ?></td>
                            <td><?= $bd_inv_oid; ?></td>
                            <td><?= $bd_inv_vorname; ?> <?= $bd_inv_nachname; ?></td>
                            <td><?= $xyz_bd_name; ?> (<?= $xyz_bd_iban; ?>/<?= $xyz_bd_bic; ?>)</td>
                            <td><a href="../invoice_files/<?= $bd_inv_pdf; ?>" target="_blank"><?= $clean_pdf_file; ?></a></td>
                            <td><?php echo formatNumber($bd_inv_sum); ?> &euro;</td>
                            <td>
                              <?= $bd_inv_date; ?>
                            </td>
                          </tr>


                        <?php

                        }

                        ?>
                      </tbody>
                    </table>
                  </div>
                  <!-- END INVOICE BD-TABLE -->

                <?php

                } else {

                ?>

                  <!-- START BD-TABLE -->
                  <a href="?settings=bd&bdaction=add" class="btn btn-info btn-md">Neuen Bankdrop hinzufügen &raquo;</a>
                  <hr />
                  <h4>Aktuelle Bankdrops im System</h4>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead class="thead-light">
                        <tr>
                          <!--<th>ID</th>-->
                          <th>Agent</th>
                          <th>BD-Name</th>
                          <th>IBAN</th>
                          <th>BIC</th>
                          <th>Täglich</th>
                          <th>Gesamt</th>
                          <th>Rechnungen</th>
                          <th>Betrag</th>
                          <th>Status</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php

                        $get_All_BDs = $SQL->prepare("SELECT id, bd_id, bd_agent, bd_name, bd_iban, bd_bic, status, daily_used, total_used, daily_limit, total_limit FROM bd_auswahl ORDER BY id ASC");
                        $get_All_BDs->execute();
                        $get_All_BDs->store_result();
                        $get_All_BDs->bind_result($bd_uniqueid, $bd_id, $bd_agent, $bd_name, $bd_iban, $bd_bic, $bd_status, $bd_daily_used, $bd_total_used, $bd_daily_limit, $bd_total_limit);

                        $thisBDID = 0;

                        while ($get_All_BDs->fetch()) {

                          $thisBDID++;

                          if ($bd_status == 1) {
                            $this_bd_status = '<span class="badge badge-success">BD ist aktiv</span>';
                          } else if ($bd_status == 0) {
                            $this_bd_status = '<span class="badge badge-dark">BD ist nicht aktiv</span>';
                          }

                          //CALC INVOICES
                          $calc_All_BDs = $SQL->prepare("SELECT COUNT(id) FROM kunden_rechnungen WHERE used_bd = ?");
                          $calc_All_BDs->bind_param("i", $bd_id);
                          $calc_All_BDs->execute();
                          $calc_All_BDs->store_result();
                          $calc_All_BDs->bind_result($bd_inv_count);
                          $calc_All_BDs->fetch();

                          //GET SUM FROM THIS INVOICES BD
                          $get_BD_Orders = $SQL->prepare("SELECT order_id FROM kunden_rechnungen WHERE used_bd = ?");
                          $get_BD_Orders->bind_param("i", $bd_id);
                          $get_BD_Orders->execute();
                          $get_BD_Orders->store_result();
                          $get_BD_Orders->bind_result($this_search_bd_oid);

                          $this_BD_Overview_Sum = 0;

                          while ($get_BD_Orders->fetch()) {
                            //FETCH SUM
                            $get_BD_Order_Sum = $SQL->prepare("SELECT gesamtsumme FROM kunden_bestellungen WHERE order_id = ?");
                            $get_BD_Order_Sum->bind_param("i", $this_search_bd_oid);
                            $get_BD_Order_Sum->execute();
                            $get_BD_Order_Sum->store_result();
                            $get_BD_Order_Sum->bind_result($this_search_bd_gesamtsumme);
                            $get_BD_Order_Sum->fetch();

                            //ADD IT TO SUM
                            $this_BD_Overview_Sum += $this_search_bd_gesamtsumme;
                          }

                          // DAILY PROGRESS
                          $daily_percent = $bd_daily_limit > 0 ? round(($bd_daily_used / $bd_daily_limit) * 100) : 0;
                          if ($daily_percent >= 100) {
                            $daily_color = 'bg-danger';
                          } elseif ($daily_percent >= 75) {
                            $daily_color = 'bg-warning';
                          } elseif ($daily_percent >= 50) {
                            $daily_color = 'bg-info';
                          } else {
                            $daily_color = 'bg-success';
                          }

                          // TOTAL PROGRESS
                          $total_percent = $bd_total_limit > 0 ? round(($bd_total_used / $bd_total_limit) * 100) : 0;
                          if ($total_percent >= 100) {
                            $total_color = 'bg-danger';
                          } elseif ($total_percent >= 75) {
                            $total_color = 'bg-warning';
                          } elseif ($total_percent >= 50) {
                            $total_color = 'bg-info';
                          } else {
                            $total_color = 'bg-success';
                          }

                        ?>

                          <tr>

                            <!--<td>#<?= $bd_id; ?></td>-->
                            <td><?= $bd_agent; ?></td>
                            <td><?= $bd_name; ?></td>
                            <td><?= $bd_iban; ?></td>
                            <td><?= $bd_bic; ?></td>
                            <td style="min-width:150px;">
                              <div class="progress" style="height: 18px;">
                                <div class="progress-bar <?= $daily_color; ?>" role="progressbar" style="width: <?= $daily_percent; ?>%;" aria-valuenow="<?= $daily_percent; ?>" aria-valuemin="0" aria-valuemax="100">
                                  <?= $daily_percent; ?>%
                                </div>
                              </div>
                              <small><?= $bd_daily_used; ?> / <?= $bd_daily_limit; ?></small>
                            </td>

                            <td style="min-width:150px;">
                              <div class="progress" style="height: 18px;">
                                <div class="progress-bar <?= $total_color; ?>" role="progressbar" style="width: <?= $total_percent; ?>%;" aria-valuenow="<?= $total_percent; ?>" aria-valuemin="0" aria-valuemax="100">
                                  <?= $total_percent; ?>%
                                </div>
                              </div>
                              <small><?= $bd_total_used; ?> / <?= $bd_total_limit; ?></small>
                            </td>

                            <td><a href="index.php?settings=bd&bdaction=show&bid=<?= $bd_id; ?>"><?= $bd_inv_count; ?> Rechnung(en)</a></td>
                            <td><?php echo formatNumber($this_BD_Overview_Sum); ?> &euro;</td>
                            <td><?= $this_bd_status; ?></td>
                            <td>
                              <?php

                              if ($bd_status == 1) {

                              ?>
                                <a href="?settings=bd&do=bd-disable&bid=<?= $bd_id; ?>" class="btn btn-three btn-sm" data-tooltip="Als inaktiv markieren" onclick="if (confirm('Diesen Bankdrop als inaktiv markieren?')){return true;}else{event.stopPropagation(); event.preventDefault();};">❌</a>
                              <?php

                              } else if ($bd_status == 0) {

                              ?>
                                <a href="?settings=bd&do=bd-enable&bid=<?= $bd_id; ?>" class="btn btn-info btn-sm" data-tooltip="Als aktiv markieren" onclick="if (confirm('Diesen Bankdrop als aktiv markieren?')){return true;}else{event.stopPropagation(); event.preventDefault();};">✅</a>&nbsp;
                              <?php

                              }

                              ?>
                              <a href="?settings=bd&bdaction=edit&bid=<?= $bd_id; ?>" class="btn btn-warning btn-sm" data-tooltip="Bankdrop editieren" onclick="if (confirm('Diesen Bankdrop jetzt editieren?')){return true;}else{event.stopPropagation(); event.preventDefault();};" data-tooltip="Zahlung eingegangen">💻</a>&nbsp;

                              <a href="?settings=bd&do=bd-delete&bid=<?= $bd_id; ?>" class="btn btn-danger btn-sm" data-tooltip="Bankdrop löschen" onclick="if (confirm('Diesen Bankdrop jetzt löschen?')){return true;}else{event.stopPropagation(); event.preventDefault();};">💀</a>

                            </td>
                          </tr>


                        <?php

                        }

                        ?>
                      </tbody>
                    </table>
                  </div>
                  <!-- END BD-TABLE -->

                <?php

                }

                ?>

              </div>

            <?php

            } else if ($_GET["settings"] == "main") {
              //MAIN SETTINGS

            ?>

              <div id="adminFormContainer" style="background-color:#fafafa;border:1px solid #d3d3d3;padding:55px 55px 55px 55px;border-radius:7px;margin-top:150px;margin-left:250px;margin-right:250px;">

                <h2 style="letter-spacing:0.5px;">Preise ändern</h2>
                <p style="font-size:18px;letter-spacing:0.3px;">Hier kannst du die Preise ändern, welche anschließend in den Shop übertragen werden.</p>
                <hr />

                <form action="index.php" method="POST">

                  <div class="row" style="max-width:450px;">
                    <div class="col-md-12">
                      <label for="iban">Heizöl DIN schwefelarm (p. 100 Liter)</label>
                      <div class="input-group">
                        <input type="text" name="first_price" id="first_price" value="<?= $cur_FirstPrice; ?>" required="" class="form-control" oninput="validateCurrencyInput(this)">
                        <span class="input-group-text">€</span>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <label for="bic">Sparheizöl schwefelarm (p. 100 Liter)</label>
                      <div class="input-group">
                        <input type="text" name="second_price" id="second_price" value="<?= $cur_SecondPrice; ?>" required="" class="form-control" oninput="validateCurrencyInput(this)">
                        <span class="input-group-text">€</span>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <label for="bic">Sparheizöl schwefelarm CO₂ (p. 100 Liter)</label>
                      <div class="input-group">
                        <input type="text" name="third_price" id="third_price" value="<?= $cur_ThirdPrice; ?>" required="" class="form-control" oninput="validateCurrencyInput(this)">
                        <span class="input-group-text">€</span>
                      </div>
                    </div>
                    <div class="col-md-12" style="margin-top:20px;">
                      <button type="submit" name="change_price_data" class="btn btn-yolo btn-lg" style="width:100%;text-align:center;letter-spacing:0.2px;">Heizöl-Preise aktualisieren 👍</button>
                    </div>
                  </div>

                </form>

              </div>

            <?php

            } else if ($_GET["settings"] == "data") {

            ?>

              <div id="adminFormContainer" style="background-color:#fafafa;border:1px solid #d3d3d3;padding:55px 55px 55px 55px;border-radius:7px;margin-top:150px;margin-left:250px;margin-right:250px;">

                <h2 style="letter-spacing:0.5px;">Shop-Angaben ändern</h2>
                <p style="font-size:18px;letter-spacing:0.3px;">Hier kannst du die Angaben ändern, welche anschließend dynamisch im Shop verwendet werden.</p>
                <hr />

                <form action="index.php" method="POST" enctype="multipart/form-data">

                  <div class="row" style="max-width:550px;">
                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Shop-Bezeichnung</label>
                      <input type="text" name="titel" value="<?= $cur_Title; ?>" required="" class="form-control">
                    </div>
                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Shop-Domain</label>
                      <input type="text" name="domain" value="<?= $cur_Domain; ?>" required="" class="form-control">
                    </div>
                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Firmen-Name</label>
                      <input type="text" name="firma" value="<?= $cur_Firma; ?>" required="" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label for="">Geschäftsführer</label>
                      <input type="text" name="ceo" value="<?= $cur_CEO; ?>" required="" class="form-control">
                    </div>
                    <div class="col-md-12" style="margin-bottom:20px;">
                      <label for="">Adresse</label>
                      <input type="text" name="adresse" value="<?= $cur_Strasse; ?>" required="" class="form-control">
                    </div>
                    <div class="col-md-4" style="margin-bottom:20px;">
                      <label for="">Postleitzahl</label>
                      <input type="text" name="plz" value="<?= $cur_PLZ; ?>" required="" class="form-control">
                    </div>
                    <div class="col-md-8">
                      <label for="">Stadt</label>
                      <input type="text" name="stadt" value="<?= $cur_Ort; ?>" required="" class="form-control">
                    </div>
                    <div class="col-md-4" style="margin-bottom:20px;">
                      <label for="">Telefon</label>
                      <input type="text" name="telefon" value="<?= $cur_Telefon; ?>" required="" class="form-control">
                    </div>
                    <div class="col-md-4" style="margin-bottom:20px;">
                      <label for="">E-Mail</label>
                      <input type="text" name="email" value="<?= $cur_Mail; ?>" required="" class="form-control">
                    </div>
                    <div class="col-md-4" style="margin-bottom:20px;">
                      <label for="">Steuer-Nr.</label>
                      <input type="text" name="steuer_id" value="<?= $cur_Steuernummer; ?>" required="" class="form-control">
                    </div>

                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Registergericht</label>
                      <input type="text" name="gericht" value="<?= $cur_Gericht; ?>" required="" class="form-control">
                    </div>
                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Register-Nr.</label>
                      <input type="text" name="nummer" value="<?= $cur_Nummer; ?>" required="" class="form-control">
                    </div>

                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Logo (dark)</label>
                      <img src="../<?= $cur_LogoDark; ?>" style="max-width:170px;">
                      <input type="file" name="logo_dark" class="form-control">
                    </div>

                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Logo (light)</label>
                      <img src="../<?= $cur_LogoLight; ?>" style="max-width:170px;background:#eee;">
                      <input type="file" name="logo_light" class="form-control">
                    </div>

                    <div class="col-md-12" style="margin-bottom:20px;">
                      <label for="">PDF-Logo (*.jpg)</label>
                      <img src="../<?= $cur_PDFLogo; ?>" style="max-width:170px;">
                      <input type="file" class="form-control" name="pdf_logo" id="pdf_logo" accept=".jpg,image/jpeg">
                    </div>

                    <div class="col-md-12" style="margin-bottom:20px;">
                      <label for="">Neues Favicon generieren</label>
                      <img src="<?= BASE_URL; ?>assets/images/favicon/favicon.ico" style="max-width:50px;">
                      <input type="file" class="form-control" name="favicon" id="favicon" accept="image/png">
                    </div>

                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Google-Tag (global)</label>

                      <textarea name="google_tag" id="google_tag" class="form-control" rows="5" cols="50" placeholder="&lt;script&gt;...&lt;/script&gt;">
                        <?= !empty($cur_GoogleTag) ? htmlspecialchars($cur_GoogleTag, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '' ?>
                      </textarea>
                    </div>

                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Conversion-Tag (isoliert)</label>

                      <textarea name="conversion_tag" id="conversion_tag" class="form-control" rows="5" cols="50" placeholder="&lt;script&gt;...&lt;/script&gt;">
                        <?= !empty($cur_ConversionTag) ? htmlspecialchars($cur_ConversionTag, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '' ?>
                      </textarea>
                    </div>

                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Hauptfarbe:</label>
                      <input type="color" class="form-control form-control-color" name="main_color" id="main_color" value="<?php echo htmlspecialchars($cur_MainColor); ?>">
                    </div>

                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Sekundärfarbe:</label>
                      <input type="color" class="form-control form-control-color" name="secondary_color" id="secondary_color" value="<?php echo htmlspecialchars($cur_SecondaryColor); ?>">
                    </div>

                    <!-- start smtp inputs -->
                    <div class="col-md-4" style="margin-bottom:20px;">
                      <label for="">SMTP-Host:</label>
                      <input type="text" name="smtp_host" value="<?= $cur_SMTPHost; ?>" class="form-control" required="">
                    </div>

                    <div class="col-md-4" style="margin-bottom:20px;">
                      <label for="">SMTP-User:</label>
                      <input type="text" name="smtp_user" value="<?= $cur_SMTPUser; ?>" class="form-control" required="">
                    </div>

                    <div class="col-md-4" style="margin-bottom:20px;">
                      <label for="">SMTP-Pass:</label>
                      <input type="text" name="smtp_pass" value="<?= $cur_SMTPPass; ?>" class="form-control" required="">
                    </div>

                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">SMTP-Typ:</label>
                      <input type="text" name="smtp_type" value="<?= $cur_SMTPType; ?>" class="form-control" required="">
                    </div>

                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">SMTP-Port:</label>
                      <input type="text" name="smtp_port" value="<?= $cur_SMTPPort; ?>" class="form-control" required="">
                    </div>
                    <!-- end smtp inputs -->

                    <div class="col-md-12" style="margin-bottom:20px;">
                      <label for="">Zusätzliche HTML-Tags (global)</label>

                      <textarea name="additional_tag" id="additional_tag" class="form-control" rows="5" cols="50" placeholder="&lt;script&gt;...&lt;/script&gt;">
			<?= !empty($cur_AdditionalTag) ? htmlspecialchars($cur_AdditionalTag, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '' ?>
		</textarea>
                    </div>

                    <!-- start pay -->
                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">SEPA aktivieren:</label>
                      <select name="enable_sepa" class="form-control" required="">
                        <?php

                        if ($cur_EnableSEPA == 0) {
                          echo '<option value="0" selected="">deaktiviert (zurzeit gewählt)</option>
				<option value="1">aktiviert</option>';
                        } else if ($cur_EnableSEPA == 1) {
                          echo '<option value="0">deaktiviert</option>
				<option value="1" selected="">aktiviert (zurzeit gewählt)</option>';
                        }

                        ?>
                      </select>
                    </div>

                    <div class="col-md-6" style="margin-bottom:20px;">
                      <label for="">Rechnung aktivieren:</label>
                      <select name="enable_invoice" class="form-control" required="">
                        <?php

                        if ($cur_EnableInvoice == 0) {
                          echo '<option value="0" selected="">deaktiviert (zurzeit gewählt)</option>
				<option value="1">aktiviert</option>';
                        } else if ($cur_EnableInvoice == 1) {
                          echo '<option value="0">deaktiviert</option>
				<option value="1" selected="">aktiviert (zurzeit gewählt)</option>';
                        }

                        ?>
                      </select>
                    </div>
                    <!-- end pay -->

                    <div class="col-md-12" style="margin-top:20px;">
                      <button type="submit" name="change_page_data" class="btn btn-yolo btn-lg" style="width:100%;text-align:center;letter-spacing:0.2px;">Angaben aktualisieren 👍</button>
                    </div>
                  </div>

                </form>

              </div>

            <?php

            } else if ($_GET["do"] == "createcontract" && isset($_GET["id"]) && isset($_GET["oid"]) && isset($_GET["uid"])) {
              //CREATE NEW CONTRACT
              $fetch_This_User_Records = $SQL->prepare("SELECT produkte, gesamtsumme, anschrift, payment_method, creation_date, status FROM kunden_bestellungen WHERE id = ? AND order_id = ? AND user_id = ?");
              $fetch_This_User_Records->bind_param("iii", $_GET["id"], $_GET["oid"], $_GET["uid"]);
              $fetch_This_User_Records->execute();
              $fetch_This_User_Records->store_result();
              $fetch_This_User_Records->bind_result($detail_produkte, $detail_gesamtsumme, $detail_anschrift, $detail_payment_method, $detail_creation_date, $detail_status);
              $fetch_This_User_Records->fetch();

              $fetch_User_Full_Details = $SQL->prepare("SELECT anrede, vorname, nachname, rufnummer, email_adresse, postleitzahl, ort, strasse FROM kunden_anschrift WHERE user_id = ?");
              $fetch_User_Full_Details->bind_param("i", $_GET["uid"]);
              $fetch_User_Full_Details->execute();
              $fetch_User_Full_Details->store_result();
              $fetch_User_Full_Details->bind_result($detail_anrede, $detail_vorname, $detail_nachname, $detail_rufnummer, $detail_email_adresse, $detail_postleitzahl, $detail_ort, $detail_strasse);
              $fetch_User_Full_Details->fetch();

              if ($detail_payment_method == "sepa") {
                $user_Has_CC = FALSE;
              } else if ($detail_payment_method == "cc") {
                $user_Has_CC = TRUE;
              }
            ?>

              <div id="adminFormContainer" style="background-color:#fafafa;border:1px solid #d3d3d3;padding:55px 55px 55px 55px;border-radius:7px;margin-top:150px;margin-left:250px;margin-right:250px;">

                <h2 style="letter-spacing:0.5px;">PDF-Rechnung für <b><?= $detail_vorname; ?> <?= $detail_nachname; ?></b> erstellen</h2>
                <p style="font-size:18px;letter-spacing:0.3px;">Hier kannst du nun eine PDF-Rechnung für die Bestellung dieses Kunden erstellen und einen Zusatztext hinzufügen.</p>
                <hr />

                <?php if ($detail_payment_method == "SEPA") { ?>
                  <div class="alert alert-info">Dieser Kunde hat <b>SEPA-Überweisung</b> als Zahlungsmethode gewählt.</div>
                <?php } else if ($detail_payment_method == "Rechnung") { ?>
                  <div class="alert alert-info">Dieser Kunde hat <b>Rechnung</b> als Zahlungsmethode gewählt.</div>
                <?php } ?>
                <hr />

                <form action="index.php" method="POST">

                  <div class="row" style="max-width:650px;">
                    <?php

                    if ($user_Has_CC == FALSE) {

                    ?>

                    <?php

                    } else if ($user_Has_CC == TRUE) {

                    ?>

                      <div class="col-md-12" style="margin-bottom:20px;">
                        <label for="">Zusatzinformationen</label>
                        <textarea name="rechnung_details" rows="5" cols="10" class="form-control" required="" placeholder="Hier deine Zusatzinfos eintragen..."></textarea>
                      </div>
                    <?php

                    }

                    ?>
                    <div class="col-md-12" style="margin-top:20px;">
                      <input type="hidden" name="id" value="<?= $_GET["id"]; ?>">
                      <input type="hidden" name="oid" value="<?= $_GET["oid"]; ?>">
                      <input type="hidden" name="uid" value="<?= $_GET["uid"]; ?>">
                      <button type="submit" name="generate_invoice" class="btn btn-yolo btn-lg" style="width:100%;text-align:center;letter-spacing:0.2px;">Rechnung erstellen 👍</button>
                    </div>
                  </div>

                </form>

              </div>

            <?php

            } else {

            ?>

              <h2>Aktuelle Bestellungen (neu - alt)</h2>
              <hr />
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <!--<th>ID</th>-->
                      <th>Kunde</th>
                      <th>E-Mail</th>
                      <th>Telefon</th>
                      <th>Anschrift</th>
                      <th>Lieferadr.</th>
                      <th>Produkte</th>
                      <th>Gesamtbetrag</th>
                      <th>Rechnung</th>
                      <th>Methode</th>
                      <th>Lieferung</th>
                      <!--<th>Zahlung</th>
					<th>Remind</th>-->
                      <th>Datum</th>
                      <th>Status</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php

                    $fetch_All_Records = $SQL->prepare("SELECT id, order_id, user_id, produkte, gesamtsumme, anschrift, lieferadresse, payment_method, delivery_date, delivery_add, creation_date, status, remind_date, remind_sent, remind_sent_date FROM kunden_bestellungen ORDER BY id DESC");
                    $fetch_All_Records->execute();
                    $fetch_All_Records->store_result();
                    $fetch_All_Records->bind_result($id, $order_id, $user_id, $produkte, $gesamtsumme, $anschrift, $lieferadresse, $payment_method, $delivery_date, $delivery_add, $creation_date, $status, $remind_date, $remind_sent, $remind_sent_date);

                    while ($fetch_All_Records->fetch()) {
                      //GET ORDER-DATA
                      if ($anschrift != "none") {

                        $fetch_User_Details = $SQL->prepare("SELECT anrede, vorname, nachname, rufnummer, email_adresse, postleitzahl, ort, strasse FROM kunden_anschrift WHERE user_id = ?");
                        $fetch_User_Details->bind_param("i", $user_id);
                        $fetch_User_Details->execute();
                        $fetch_User_Details->store_result();
                        $fetch_User_Details->bind_result($anrede, $vorname, $nachname, $rufnummer, $email_adresse, $postleitzahl, $ort, $strasse);
                        $fetch_User_Details->fetch();

                        //GET INVOICE-LINK
                        $confirmOrder_Get_InvoiceData = $SQL->prepare("SELECT pdf_file FROM kunden_rechnungen WHERE user_id = ? AND order_id = ?");
                        $confirmOrder_Get_InvoiceData->bind_param("ii", $user_id, $order_id);
                        $confirmOrder_Get_InvoiceData->execute();
                        $confirmOrder_Get_InvoiceData->store_result();
                        $confirmOrder_Get_InvoiceData->bind_result($this_dl_invoice_link);
                        $confirmOrder_Get_InvoiceData->fetch();

                        if ($confirmOrder_Get_InvoiceData->num_rows == 0) {
                          $user_Has_Invoice = FALSE;
                        } else {
                          $user_Has_Invoice = TRUE;

                          if (!empty($this_dl_invoice_link)) {
                            $pdf_file = getFilenameFromPath($this_dl_invoice_link);
                          }
                        }

                        if ($status == 1) {
                          $status_html = '<span class="badge badge-primary">Bestellung eingegangen<span>';
                        } else if ($status == 2) {
                          $status_html = '<span class="badge badge-warning">Rechnung wurde erstellt<span>';
                        } else if ($status == 3) {
                          $status_html = '<span class="badge badge-secondary">Rechnung wurde versendet<span>';
                        } else if ($status == 4) {
                          $status_html = '<span class="badge badge-success">Bestellung ist abgeschlossen<span>';
                        }

                        if ($payment_method == "SEPA") {
                          $this_payment = '<span class="btn btn-five btn-sm">💶 SEPA-Üw.</span>';
                        } else if ($payment_method = "Rechnung") {
                          $this_payment = '<span class="btn btn-two btn-sm">📄 Rechnung</span>';
                        }

                        //CALC FINAL SUM
                        $mainlist_finalsum = $gesamtsumme + $global_GGVS;

                        $transform_creation_date = formatGermanDateTime($creation_date);

                        //TRANSFORM DELIVERY DATE
                        if ($delivery_date == "none") {
                          $delivery_date_html = "Telefonische Abstimmung";
                        } else {
                          if (containsDate($delivery_date) == true) {
                            $delivery_date_html = formatDeliverySlot($delivery_date);
                          } else {
                            $delivery_date_html = $delivery_date;
                          }
                        }


                    ?>

                        <tr>
                          <!--<td><?= $id; ?></td>-->
                          <td><?= $vorname; ?> <?= $nachname; ?></td>
                          <td><a class="" href="mailto:<?= $email_adresse; ?>"><?= $email_adresse; ?></a></td>
                          <td><?= $rufnummer; ?></td>
                          <td><?= $anrede; ?> <?= $vorname; ?> <?= $nachname; ?><br>
                            <?= $strasse; ?><br>
                            <?= $postleitzahl; ?> <?= $ort; ?></td>

                          <td>
                            <?php

                            if (isset($lieferadresse)) {
                              echo $lieferadresse;
                            } else if (empty($lieferadresse)) {
                              echo '<i class="bi bi-x-circle text-danger"></i>';
                            }

                            ?>
                          </td>

                          <td><?php echo convertToList($produkte); ?></td>
                          <td><a href="#" class="btn btn-euro btn-sm">💶 <?= formatNumber($mainlist_finalsum); ?> &euro;</a></td>
                          <td>
                            <?php if ($user_Has_Invoice == TRUE) { ?>
                              <?php if (empty($this_dl_invoice_link)) { ?>
                                <a href="#" class="btn btn-dark btn-sm" data-tooltip="PDF-Rechnung nicht vorhanden">⚠️ n.V.</a>
                              <?php } else { ?>
                                <a href="../invoice_files/<?= $this_dl_invoice_link; ?>" class="btn btn-yolo btn-sm" target="_blank" data-tooltip="PDF-Rechnung öffnen">🗎 <?= $pdf_file; ?></a>
                              <?php } ?>
                            <?php } else if ($user_Has_Invoice == FALSE) { ?>
                              <a href="#" class="btn btn-one btn-sm" data-tooltip="PDF-Rechnung nicht erstellt">⚠️ noch nicht erstellt</a>
                            <?php } ?>
                          </td>
                          <td><?= $this_payment; ?></td>

                          <td><?= $delivery_date_html; ?></td>



                          <td><?= $transform_creation_date; ?></td>
                          <td><?= $status_html; ?></td>
                          <td>
                            <?php

                            if ($status == 1) {

                            ?>
                              <!--
					<a href="?do=createcontract&id=<?= $id; ?>&oid=<?= $order_id; ?>&uid=<?= $user_id; ?>" class="btn btn-three btn-sm" data-tooltip="Rechnung erstellen" onclick="if (confirm('Bist du dir sicher?')){return true;}else{event.stopPropagation(); event.preventDefault();};">&#128200;</a>
					--->
                              <a href="?generate_invoice=true&id=<?= $id; ?>&oid=<?= $order_id; ?>&uid=<?= $user_id; ?>" class="btn btn-three btn-sm" data-tooltip="Rechnung erstellen + versenden" onclick="if (confirm('Bist du dir sicher, dass du für <?= $vorname; ?> <?= $nachname; ?> eine Rechnung erstellen + versenden willst?')){return true;}else{event.stopPropagation(); event.preventDefault();};">&#128200;</a>
                            <?php

                            } else if ($status == 2) {

                            ?>
                              <a href="?do=check&id=<?= $id; ?>&oid=<?= $order_id; ?>&uid=<?= $user_id; ?>" class="btn btn-info btn-sm" data-tooltip="Rechnung senden" onclick="if (confirm('Bist du dir sicher?')){return true;}else{event.stopPropagation(); event.preventDefault();};">👍</a>&nbsp;
                            <?php

                            } else if ($status == 3) {

                            ?>
                              <a href="?do=finish&id=<?= $id; ?>&oid=<?= $order_id; ?>&uid=<?= $user_id; ?>" class="btn btn-warning btn-sm" onclick="if (confirm('Bist du dir sicher?')){return true;}else{event.stopPropagation(); event.preventDefault();};" data-tooltip="Zahlung eingegangen">✔️</a>&nbsp;

                            <?php

                            }

                            ?>
                            <a href="?do=del&id=<?= $id; ?>&oid=<?= $order_id; ?>&uid=<?= $user_id; ?>" class="btn btn-danger btn-sm" data-tooltip="Löschen" onclick="if (confirm('Bist du dir sicher?')){return true;}else{event.stopPropagation(); event.preventDefault();};">💀</a>

                          </td>

                        </tr>


                    <?php

                      }
                    }

                    ?>
                  </tbody>
                </table>
              </div>
          </div>





          <!-- end adm -->
        </div>
      </div>
      <!-- END PAGE -->
    <?php } ?>


    </div>

    <!--
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
		-->

    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>


    <script>
      $(document).ready(function() {
        $('[data-tooltip]').each(function() {
          var $elem = $(this);
          var tooltipText = $elem.attr('data-tooltip');

        });
      });
    </script>

    <!-- start sweet -->
    <!--
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
-->
    <script src="../assets/js/sweetalert2@11.js"></script>
    <script>
      $(document).ready(function() {
        let successMessage = "<?php echo addslashes($show_Success_Msg); ?>";
        if (successMessage) {
          Swal.fire({
            title: "Erfolg!",
            text: successMessage,
            icon: "success",
            confirmButtonText: "OK"
          });
        }
      });
    </script>


    <script>
      $(document).ready(function() {
        let errorMessage = "<?php echo addslashes($show_Error_Msg); ?>";
        if (errorMessage) {
          Swal.fire({
            title: "Achtung!",
            text: errorMessage,
            icon: "error",
            confirmButtonText: "OK"
          });
        }
      });
    </script>
    <!-- end sweet -->
    <script>
      function validateCurrencyInput(input) {
        input.value = input.value.replace(/[^0-9.]/g, '');

        const parts = input.value.split('.');
        if (parts.length > 2) {
          input.value = parts[0] + '.' + parts.slice(1).join('');
        }
      }
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const dailyInput = document.getElementById('daily_limit_input');
        const dailySlider = document.getElementById('daily_limit_slider');
        const totalInput = document.getElementById('total_limit_input');
        const totalSlider = document.getElementById('total_limit_slider');

        dailyInput.addEventListener('input', () => dailySlider.value = dailyInput.value);
        dailySlider.addEventListener('input', () => dailyInput.value = dailySlider.value);

        totalInput.addEventListener('input', () => totalSlider.value = totalInput.value);
        totalSlider.addEventListener('input', () => totalInput.value = totalSlider.value);
      });
    </script>

  </body>

  </html>

<?php

}

?>