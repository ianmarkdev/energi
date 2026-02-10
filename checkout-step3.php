<?php

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
include("white_config.inc.php");
include("white_actions.inc.php");
require_once 'Mobile_Detect.php';
$detectMobile = new Mobile_Detect;

if (
  empty($_SESSION['checkout']['personal_data']) ||
  empty($_SESSION['checkout']['payment_method']) ||
  (
    empty($_SESSION['checkout']['delivery_date']) &&
    empty($_SESSION['checkout']['delivery_add'])
  )
) {
  header('Location: checkout');
  exit;
}


$personal = $_SESSION['checkout']['personal_data'];
$paymentMethod = $_SESSION['checkout']['payment_method'];
$deliveryDateRaw = $_SESSION['checkout']['delivery_date'];
$cart = $_SESSION['cart'];
$ggvs = $global_GGVS;
$deliveryAdd = $_SESSION['checkout']['delivery_add'] ?? null;

if (isset($deliveryDateRaw) && $deliveryDateRaw !== 'Telefonische Abstimmung') {
  $parts = explode(' ', $deliveryDateRaw, 2);
  $datePart = $parts[0];
  $timePart = $parts[1] ?? '';
  $dt = DateTime::createFromFormat('Y-m-d', $datePart);
  $formatter = new \IntlDateFormatter('de_DE', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Europe/Berlin', \IntlDateFormatter::GREGORIAN, 'EEEE, d. MMMM');
  $deliveryDateLabel = $formatter->format($dt->getTimestamp()) . ' ' . $timePart;

  $deliveryDateSQL = $deliveryDateRaw;
  $deliveryDateMail = $deliveryDateLabel;
} else {
  $deliveryDateLabel = "Telefonische Abstimmung";
  $deliveryDateSQL = "Telefonische Abstimmung";
  $deliveryDateMail = "Telefonische Abstimmung";
}

// NEU: Mehrere Lieferadressen unterstützen
$lieferanschriften = $_SESSION['checkout']['lieferanschriften'] ?? null;
$anzahlLieferstellen = intval($_SESSION['checkout']['lieferstellen_count'] ?? 1);

if ($lieferanschriften && is_array($lieferanschriften)) {
  $lieferanschriften_json = $SQL->real_escape_string(json_encode($lieferanschriften, JSON_UNESCAPED_UNICODE));
} else {
  $lieferanschriften_json = null;
}

function formatLieferadressenBlock($lieferanschriften)
{
  // Entferne die erste Adresse, das ist die Hauptadresse!
  if (!$lieferanschriften || !is_array($lieferanschriften)) return '';
  // Entferne die Adresse mit Key 1 oder den ersten Eintrag
  if (isset($lieferanschriften[1])) {
    unset($lieferanschriften[1]);
  } else {
    array_shift($lieferanschriften);
  }
  if (count($lieferanschriften) === 0) return '';
  $out = '';
  $counter = 2; // Da die Hauptadresse #1 war, Zusatzadressen ab #2
  foreach ($lieferanschriften as $adr) {
    $out .= '<span style="color:#000;font-size:18.5px;font-weight:750;"><b>';
    $out .= "Lieferstelle #{$counter}</b></span><br>\n";
    $out .= htmlspecialchars(trim("{$adr['anrede']} {$adr['vorname']} {$adr['nachname']}")) . "<br>\n";
    $out .= htmlspecialchars(trim($adr['strasse'])) . "<br>\n";
    $out .= htmlspecialchars(trim("{$adr['plz']} {$adr['ort']}")) . "<br>\n";
    $out .= htmlspecialchars(trim($adr['land'])) . "<br><br>\n";
    $counter++;
  }
  return trim($out);
}

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



if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['agree_terms']) && !empty($_POST['agree_withdraw'])) {
  /* start exec */
  $kunden_id = rand(100000, 999999);
  $fullName = $personal['vorname'] . ' ' . $personal['nachname'];
  $email = $personal['email'];
  $addressText = "{$personal['anrede']} {$fullName}\n{$personal['strasse']}\n{$personal['plz']} {$personal['ort']}\n{$personal['land']}";
  $productList = convertCartToList($cart);
  $productsJson = $SQL->real_escape_string(json_encode($cart, JSON_UNESCAPED_UNICODE));
  //$sumGross = 0; foreach($cart as $i) { $sumGross += $i['price'] + $ggvs; }
  $sumGross = 0;
  foreach ($cart as $i) {
    $sumGross += $i['price'];
  }
  // Add shipping price to total
  $shippingPrice = $_SESSION['checkout']['shipping_price'] ?? 0;
  $sumGross += $shippingPrice;
  $order_id = $kunden_id * 1000 + rand(1, 999);
  $now = date('Y-m-d H:i:s');
  $prepareCart_Json = json_encode($cart);

  //GENERATE TEXT-PRODUTS
  foreach ($_SESSION['cart'] as $item) {
    if ($item["type"] == "main") {
      //MAIN PRODUCT
      $produkte_text .= $item['name'] . ' - ' . $item['quantity'] . 'l - ' . $item['lieferstellen'] . ' Lieferstellen - ' . number_format($item['price'], 2, ',', '.') . " € \n";

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
				<td style="padding: 12px 8px; font-size: 14px;">1</td>
				<td style="padding: 12px 8px; font-size: 14px;">' . $item['name'] . '</td>
				<td style="padding: 12px 8px; font-size: 14px;">' . $item['quantity'] . ' Liter bei ' . $item['lieferstellen'] . ' ' . $product_line_lieferstellen_suffix . '</td>
				<td style="padding: 12px 8px; font-size: 14px;">' . $singlePrice_format . ' € / 100l</td>
				<td style="padding: 12px 8px; font-size: 14px;">' . $singleSumx_format . ' €</td>
				</tr>';

      $listFinalSum = $sumGross + $global_GGVS;
      $listFinalSum_format = number_format($listFinalSum, 2, ',', '.');

      $list_lieferstellen = $item['lieferstellen'];
      /* END CONSTRUCT MAIL LINE */
    } else {
      $produkte_text .= $item['name'] . ' - ' . $item['quantity'] . ' - ' . number_format($item['price'], 2, ',', '.') . " €\n";
    }
    $gesamtsumme += $item['price'];
  }

  // Calculate Netto and MwSt values for summary
  $sumMwst = $sumGross * 0.19;
  $sumNetto = $sumGross - $sumMwst;
  $sumNetto_format = number_format($sumNetto, 2, ',', '.');
  $sumMwst_format = number_format($sumMwst, 2, ',', '.');

  $SQL->begin_transaction();
  try {
    $SQL->query("INSERT INTO kunden (kunden_id,permission,admin,full_name,email_address,password,language) VALUES ('$kunden_id',1,0,'$fullName','$email','','DE')");
    $SQL->query("INSERT INTO kunden_anschrift (user_id,anrede,vorname,nachname,rufnummer,email_adresse,postleitzahl,ort,strasse,land,status) VALUES ('$kunden_id','{$personal['anrede']}','{$personal['vorname']}','{$personal['nachname']}','{$personal['telefon']}','{$personal['email']}','{$personal['plz']}','{$personal['ort']}','{$personal['strasse']}','{$personal['land']}',1)");
    if (!empty($lieferanschriften) && is_array($lieferanschriften)) {
      $lieferadresseDB = formatLieferadressenBlock($lieferanschriften);
      $lieferadresseZP = formatLieferadressenBlock($lieferanschriften);
      if ($lieferadresseDB === '') $lieferadresseDB = null;
    } else {
      $lieferadresseDB = null;
      $lieferadresseZP = "none";
    }


    $SQL->query("INSERT INTO kunden_bestellungen (
    order_id, user_id, produkte, gesamtsumme, anschrift, lieferadresse, payment_method, 
    products_array, delivery_method, delivery_date, delivery_add, creation_date, status, liefer_array
) VALUES (
    '$order_id', '$kunden_id', '$produkte_text', '$sumGross', '$addressText',
    '{$SQL->real_escape_string($lieferadresseDB)}', '$paymentMethod', '$productsJson', '{$_SESSION['checkout']['shipping_method']}',
    '$deliveryDateSQL', '$deliveryAdd', '$now', 1, '$lieferanschriften_json'
)");

    $SQL->commit();

    if ($personal["anrede"] == "Herr") {
      $craft_anrede = "Sehr geehrter Herr " . $personal["vorname"] . " " . $personal["nachname"] . ",";
    } else if ($personal["anrede"] == "Frau") {
      $craft_anrede = "Sehr geehrte Frau " . $personal["vorname"] . " " . $personal["nachname"] . ",";
    }

    //GET ADDY FOR MAIL
    $lieferadresseDB_Mail = formatLieferadressenMailBlock($lieferanschriften);
    $lieferadresseDB_Mail_Line = nl2br($lieferadresseDB_Mail);

    $mail_table_color = "#197d00";
    // $new_mail_logo_dark  = 'https://heizoel-energie.de/assets/images/email-logo.png';
    $new_mail_logo_dark = $cur_Domain . "/" . $cur_LogoDark;
    $new_mail_logo_light = $cur_Domain . "/" . $cur_LogoLight;
    $new_mail_icon = "https://images2.imgbox.com/49/38/znzOd5iH_o.png";

    $message = '
<!DOCTYPE html>
<html lang="de" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <title>Auftragsbestätigung – Heizöl</title>
  <!--[if mso]>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->
  <style>
    body,
    table,
    td,
    a {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    table,
    td {
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    img {
      -ms-interpolation-mode: bicubic;
      border: 0;
      height: auto;
      line-height: 100%;
      outline: none;
      text-decoration: none;
    }

    body {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
    }

    @media screen and (max-width: 600px) {
      .mobile-full {
        width: 100% !important;
      }

      .mobile-stack {
        display: block !important;
        width: 100% !important;
      }

      .mobile-padding {
        padding: 10px !important;
      }
    }
  </style>
</head>

<body
  style="margin: 0; padding: 0; background-color: #f6f7fb; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased;">

  <!-- Wrapper Table -->
  <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f6f7fb;">
    <tr>
      <td align="center" style="padding: 28px 16px;">

        <!-- Main Container -->
        <table role="presentation" cellpadding="0" cellspacing="0" width="600" class="mobile-full"
          style="max-width: 600px; width: 100%;">

          <!-- Main Card -->
          <tr>
            <td>
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden;">

                <!-- Card Header -->
                <tr>
                  <td
                    style="padding: 22px; background: linear-gradient(180deg, rgba(14,165,233,0.10), rgba(255,255,255,0)); border-bottom: 1px solid #e5e7eb;">

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
                                <p style="margin: 2px 0 0; font-size: 13px; color: #6b7280;">' . $cur_Strasse . ' · ' . $cur_PLZ . ' ' . $cur_Ort . ' <br> Tel. ' . $cur_Telefon . '</p>
                              </td>
                            </tr>
                          </table>
                        </td>
                        <td valign="top" align="right" style="font-size: 13px; color: #6b7280;">
                          <p style="margin: 0;"><strong style="color: #111827;">Auftragsbestätigung</strong></p>
                          <p style="margin: 4px 0 0;">Dok.-Nr.: <strong style="color: #111827;">' . $order_id . '</strong></p>
                          <p style="margin: 4px 0 0;">Datum: <strong style="color: #111827;">' . date('d.m.Y') . '</strong></p>
                        </td>
                      </tr>
                    </table>

                    <h2 style="margin: 0; font-size: 22px; color: #111827;">Auftragsbestätigung – Heizöl-Lieferung</h2>
                    <table role="presentation" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
                      <tr>
                        <td
                          style="padding: 8px 12px; background-color: rgba(22,163,74,0.10); border: 1px solid rgba(22,163,74,0.20); border-radius: 999px;">
                          <table role="presentation" cellpadding="0" cellspacing="0">
                            <tr>
                              <td style="display:inline-table; width: 8px; height: 8px; background-color: #16a34a; border-radius: 999px;">
                              </td>
                              <td style="padding-left: 8px; font-size: 13px; font-weight: 600; color: #16a34a;">Auftrag
                                bestätigt</td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>

                <!-- Card Content -->
                <tr>
                  <td style="padding: 18px 22px;">

                    <!-- Two Column Grid: Kunde & Lieferung - NOW STACKED -->
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                      <!-- Kunde -->
                      <tr>
                        <td style="padding-bottom: 14px;">
                          <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                            style="border: 1px solid #e5e7eb; border-radius: 14px;">
                            <tr>
                              <td style="padding: 14px;">
                                <h3
                                  style="margin: 0 0 8px; font-size: 13px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">
                                  Kunde</h3>
                                <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                  style="font-size: 14px;">
                                  <tr>
                                    <td style="color: #6b7280; padding: 3px 0; width: 140px;">Name</td>
                                    <td style="color: #111827; font-weight: 600; padding: 3px 0;">' . $fullName . '</td>
                                  </tr>
                                  <tr>
                                    <td style="color: #6b7280; padding: 3px 0;">Adresse</td>
                                    <td style="color: #111827; font-weight: 600; padding: 3px 0;">' . $personal['strasse'] . ', ' . $personal['plz'] . ' ' . $personal['ort'] . '</td>
                                  </tr>
                                  <tr>
                                    <td style="color: #6b7280; padding: 3px 0;">E-Mail</td>
                                    <td style="color: #111827; font-weight: 600; padding: 3px 0;">' . $personal['email'] . '</td>
                                  </tr>
                                  <tr>
                                    <td style="color: #6b7280; padding: 3px 0;">Telefon</td>
                                    <td style="color: #111827; font-weight: 600; padding: 3px 0;">' . $personal['telefon'] . '</td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <!-- Lieferung -->
                      <tr>
                        <td>
                          <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                            style="border: 1px solid #e5e7eb; border-radius: 14px;">
                            <tr>
                              <td style="padding: 14px;">
                                <h3
                                  style="margin: 0 0 8px; font-size: 13px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">
                                  Lieferung</h3>
                                <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                  style="font-size: 14px;">
                                  <tr>
                                    <td style="color: #6b7280; padding: 3px 0; width: 140px;">Lieferadresse</td>
                                    <td style="color: #111827; font-weight: 600; padding: 3px 0;">' . $lieferadresseDB_Mail_Line . '</td>
                                  </tr>
                                  <tr>
                                    <td style="color: #6b7280; padding: 3px 0;">Status</td>
                                    <td style="color: #111827; font-weight: 600; padding: 3px 0;">Termin wird telefonisch abgestimmt</td>
                                  </tr>
                                  <tr>
                                    <td style="color: #6b7280; padding: 3px 0;">Lieferzeit</td>
                                    <td style="color: #111827; font-weight: 600; padding: 3px 0;">' . $deliveryDateMail . '</td>
                                  </tr>
                                  <tr>
                                    <td style="color: #6b7280; padding: 3px 0;">Zahlungsart</td>
                                    <td style="color: #111827; font-weight: 600; padding: 3px 0;">' . $paymentMethod . '</td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>


                    <!-- Thank You Note -->
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 14px;">
                      <tr>
                        <td
                          style="padding: 14px; border: 1px dashed rgba(2,132,199,0.35); background-color: rgba(14,165,233,0.08); border-radius: 14px; color: #0b4a6f; font-size: 14px;">
                          <strong style="color: #083344;">Vielen Dank für Ihre Bestellung bei uns!</strong><br><br>
                          Wir werden Sie innerhalb der nächsten <strong>24 Stunden</strong> unter der von Ihnen
                          angegebenen Telefonnummer anrufen,
                          um gemeinsam einen Liefertermin festzulegen. Bitte bleiben Sie in dieser Zeit erreichbar.<br>
                          <strong>Unsere Telefonnummer: ' . $cur_Telefon . '</strong>
                        </td>
                      </tr>
                    </table>

                    <!-- Wichtig zu wissen -->
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                      style="margin-top: 14px; border: 1px solid #e5e7eb; border-radius: 14px;">
                      <tr>
                        <td style="padding: 14px;">
                          <h3
                            style="margin: 0 0 8px; font-size: 13px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">
                            Wichtig zu wissen</h3>
                          <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                            style="font-size: 14px; line-height: 1.6; color: #111827;">
                            <tr>
                              <td style="padding: 6px 0;">
                                <strong>Liefergarantie</strong><br>
                                Wir garantieren die Lieferung am vereinbarten Termin, sofern die Rechnung innerhalb von
                                <strong>48 Stunden nach Terminvereinbarung</strong> beglichen wurde.
                              </td>
                            </tr>
                            <tr>
                              <td style="padding: 6px 0;">
                                <strong>Abnahmemenge-Garantie</strong><br>
                                Sollten Sie statt der bestellten Menge (z. B. 1.500 Liter) weniger abnehmen können (z.
                                B. 1.400 Liter), erstatten wir Ihnen den Differenzbetrag selbstverständlich zurück.
                              </td>
                            </tr>
                            <tr>
                              <td style="padding: 6px 0;">
                                <strong>Zahlung</strong><br>
                                Aus Sicherheitsgründen bieten wir keine Barzahlung beim Fahrer mehr an. Die Bezahlung
                                erfolgt ausschließlich per Überweisung nach Erhalt der Rechnung.
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>

                    <!-- Die nächsten Schritte -->
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                      style="margin-top: 14px; border: 1px solid #e5e7eb; border-radius: 14px;">
                      <tr>
                        <td style="padding: 14px;">
                          <h3
                            style="margin: 0 0 8px; font-size: 13px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;">
                            Die nächsten Schritte</h3>
                          <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                            style="font-size: 14px; line-height: 1.7; color: #111827;">
                            <tr>
                              <td
                                style="padding: 6px 0; vertical-align: top; width: 24px; color: #0ea5e9; font-weight: bold;">
                                1.</td>
                              <td style="padding: 6px 0;">
                                <strong>Anruf zur Terminvereinbarung</strong><br>
                                Wir melden uns innerhalb der nächsten 24 Stunden telefonisch bei Ihnen (unter
                                <strong>' . $cur_Telefon . '</strong>), um einen passenden Liefertermin zu vereinbaren.
                              </td>
                            </tr>
                            <tr>
                              <td
                                style="padding: 6px 0; vertical-align: top; width: 24px; color: #0ea5e9; font-weight: bold;">
                                2.</td>
                              <td style="padding: 6px 0;">
                                <strong>Rechnung &amp; Zahlung</strong><br>
                                Sobald wir den Liefertermin gemeinsam festgelegt haben, erhalten Sie umgehend Ihre
                                Rechnung. Bitte begleichen Sie den Rechnungsbetrag innerhalb von <strong>48
                                  Stunden</strong>, damit wir den Liefertermin verbindlich bestätigen können.
                              </td>
                            </tr>
                            <tr>
                              <td
                                style="padding: 6px 0; vertical-align: top; width: 24px; color: #0ea5e9; font-weight: bold;">
                                3.</td>
                              <td style="padding: 6px 0;">
                                <strong>Zahlungseingang</strong><br>
                                Nach Zahlungseingang auf unserem Bankkonto erhalten Sie die Terminbestätigung per
                                E-Mail.
                              </td>
                            </tr>
                            <tr>
                              <td
                                style="padding: 6px 0; vertical-align: top; width: 24px; color: #0ea5e9; font-weight: bold;">
                                4.</td>
                              <td style="padding: 6px 0;">
                                <strong>Lieferung</strong><br>
                                Das Heizöl wird geliefert und bei Ihnen getankt. Bitte seien Sie an diesem Termin
                                erreichbar. Sollten Sie den Termin verschieben wollen, rufen Sie uns einfach unter
                                unserer Telefonnummer an.
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>

                    <!-- Hinweis Note -->
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 14px;">
                      <tr>
                        <td
                          style="padding: 14px; border: 1px dashed rgba(2,132,199,0.35); background-color: rgba(14,165,233,0.08); border-radius: 14px; color: #0b4a6f; font-size: 14px;">
                          <strong>Hinweis:</strong><br>
                          Sollten Sie den Termin verpassen oder nicht wahrnehmen, werden weitere <strong>3
                            Zustellversuche</strong> an einem Folgetag versucht.
                        </td>
                      </tr>
                    </table>

                    <!-- Order Items Table -->
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                      style="margin-top: 14px; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden;">
                      <tr>
                        <th style="padding: 12px 8px; font-size: 14px; color: #6b7280; font-weight: 400; text-align: left; border-bottom: 1px solid #e5e7eb; width: 50px;">Pos.</th>
                        <th style="padding: 12px 8px; font-size: 14px; color: #6b7280; font-weight: 400; text-align: left; border-bottom: 1px solid #e5e7eb; width: 180px;">Produkt</th>
                        <th style="padding: 12px 8px; font-size: 14px; color: #6b7280; font-weight: 400; text-align: left; border-bottom: 1px solid #e5e7eb;">Anzahl</th>
                        <th style="padding: 12px 8px; font-size: 14px; color: #6b7280; font-weight: 400; text-align: left; border-bottom: 1px solid #e5e7eb;">Einzelpreis<br><span style="font-size: 12px;">Inkl. USt</span></th>
                        <th style="padding: 12px 8px; font-size: 14px; color: #6b7280; font-weight: 400; text-align: left; border-bottom: 1px solid #e5e7eb;">Gesamt<br><span style="font-size: 12px;">Inkl. USt</span></th>
                      </tr>
                      ' . $product_line_html_new . '
                      <tr>
                        <td style="padding: 12px 8px; font-size: 14px; color: #111827; border-bottom: 1px solid #e5e7eb;">2</td>
                        <td style="padding: 12px 8px; font-size: 14px; color: #111827; border-bottom: 1px solid #e5e7eb;">' . ($_SESSION['checkout']['shipping_method'] ?? 'Standardlieferung') . '</td>
                        <td style="padding: 12px 8px; font-size: 14px; color: #111827; border-bottom: 1px solid #e5e7eb;">' . ($shippingPrice > 0 ? number_format($shippingPrice, 2, ',', '.') . ' €' : 'kostenfrei') . '</td>
                        <td style="padding: 12px 8px; font-size: 14px; color: #111827; border-bottom: 1px solid #e5e7eb;">' . number_format($shippingPrice, 2, ',', '.') . ' €</td>
                        <td style="padding: 12px 8px; font-size: 14px; color: #111827; border-bottom: 1px solid #e5e7eb;">' . number_format($shippingPrice, 2, ',', '.') . ' €</td>
                      </tr>
                    </table>

                    <!-- Summary Section -->
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 24px;">
                      <tr>
                        <td style="padding: 6px 8px; font-size: 14px; color: #6b7280; text-align: right;">Gesamtsumme (Netto):</td>
                        <td style="padding: 6px 8px; font-size: 14px; color: #111827; text-align: right; width: 120px;">' . $sumNetto_format . ' €</td>
                      </tr>
                      <tr>
                        <td style="padding: 6px 8px; font-size: 14px; color: #6b7280; text-align: right;">19% MwSt.:</td>
                        <td style="padding: 6px 8px; font-size: 14px; color: #111827; text-align: right;">' . $sumMwst_format . ' €</td>
                      </tr>
                      <tr>
                        <td style="padding: 6px 8px; font-size: 14px; color: #6b7280; text-align: right;">GGVS-Umlage</td>
                        <td style="padding: 6px 8px; font-size: 14px; color: #111827; text-align: right;">42,59 €</td>
                      </tr>
                      <tr>
                        <td style="padding: 12px 8px 6px; font-size: 14px; color: #111827; font-weight: 700; text-align: right;"><strong>Gesamtsumme Brutto:</strong></td>
                        <td style="padding: 12px 8px 6px; font-size: 14px; color: #111827; font-weight: 700; text-align: right;"><strong>' . $listFinalSum_format . ' €</strong></td>
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
                                <strong style="color: #111827;">' . $cur_Firma . '</strong> · Sitz: ' . $cur_Ort . ' · ' . $cur_Nummer . ' ·
                                USt-IdNr. ' . $cur_Steuernummer . '
                              </td>
                            </tr>
                            <tr>
                              <td style="padding-top: 8px;">
                                Kontakt: <a href="mailto:' . $cur_SMTPUser . '"
                                  style="color: #0284c7; text-decoration: none;">' . $cur_SMTPUser . '</a> · <a
                                  href="tel:' . $cur_Telefon . '" style="color: #0284c7; text-decoration: none;">' . $cur_Telefon . '</a>
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

      </td>
    </tr>
  </table>

</body>

</html>
';
    $cleanedHtml = cleanHtmlForEmail($message);
    $cleanedName = replaceUmlauts($fullName);

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->isSMTP();
    $mail->Host = $cur_SMTPHost;
    $mail->SMTPAuth = true;
    $mail->Username = $cur_SMTPUser;
    $mail->Password = $cur_SMTPPass;
    $mail->SMTPSecure = $cur_SMTPType;
    $mail->Port = $cur_SMTPPort;

    $mail->setFrom($cur_SMTPUser, $cur_Title);
    $mail->XMailer = '';
    $mail->addAddress($email, $fullName);
    $mail->isHTML(true);
    $mail->Subject = 'Ihre Bestellung bei ' . $cur_Title;
    $mail->Body    = $cleanedHtml;
    $mail->send();
    /* end send email notify */

    /* === start zapier connect === */
    $zapierWebhookUrl = 'https://hooks.zapier.com/hooks/catch/25171370/ui8itvm/';

    $data = array(
      'name' => $fullName,
      'email' => $personal['email'],
      'phone' => $personal['telefon'],
      'address' => $personal['strasse'],
      'zip' => $personal['plz'],
      'city' => $personal['ort'],
      'additional_delivery' => $lieferadresseZP,
      'products' => $produkte_text,
      'totalsum' => $sumGross,
      'payment' => $paymentMethod,
      'delivery' => $_SESSION['checkout']['shipping_method'] ?? 'Standardlieferung',
      'delivery_date' => $deliveryDateMail,
      'date' => $createdAt
    );

    $ch = curl_init($zapierWebhookUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    curl_close($ch);
    /* === start zapier connect === */

    unset($_SESSION['checkout'], $_SESSION['cart']);
    header('Location: bestellung-abgeschlossen');
    exit;
  } catch (Exception $e) {
    $SQL->rollback();
    die('Fehler: ' . $e->getMessage());
  }
  /* end exec */
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Checkout – Schritt 3/3 | <?= $cur_Title; ?></title>

  <link rel="icon" type="image/png" href="<?= BASE_URL; ?>assets/images/favicon/favicon-96x96.png" sizes="96x96" />
  <!--<link rel="icon" type="image/svg+xml" href="<?= BASE_URL; ?>assets/images/favicon/favicon.svg" />-->
  <link rel="shortcut icon" href="<?= BASE_URL; ?>assets/images/favicon/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL; ?>assets/images/favicon/apple-touch-icon.png" />
  <link rel="manifest" href="<?= BASE_URL; ?>assets/images/favicon/site.webmanifest" />

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/checkout-v1.css.php">
  <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/trusted-shops.css?v=1.0.3">
  <?php if ($detectMobile->isMobile()) { ?>


    <style>
      .checkout-card {
        margin-bottom: 1.5rem;
      }

      .sidebar-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem;
      }

      .sidebar-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: .6rem;
        font-size: .95rem;
      }

      .sidebar-divider {
        border-top: 1px solid #e5e7eb;
        margin: 1rem 0;
      }

      .price-box {
        background: #f6f8fa;
        padding: 1rem;
        border-radius: 8px;
      }

      @media(min-width:992px) {
        #sidebar {
          position: sticky;
          top: 100px;
        }
      }

      @media(max-width:991px) {
        .checkout-section {
          padding-bottom: 10rem !important;
        }

        .sticky-footer {
          position: fixed;
          bottom: 0;
          left: 0;
          right: 0;
          background: #fff;
          padding: 1rem;
          box-shadow: 0 -2px 6px rgba(0, 0, 0, .1);
          z-index: 999;
          display: flex;
          gap: 0.5rem;
        }

        .sticky-footer .btn {
          padding: 0.5rem 1rem;
          font-size: 0.875rem;
          flex: 1;
        }

        .sidebar-card {
          margin-bottom: 1rem;
        }
      }

      .sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #fff;
        padding: 1rem;
        box-shadow: 0 -2px 6px rgba(0, 0, 0, .1);
        z-index: 999;
      }

      .sticky-footer .btn {
        padding: .5rem 1rem;
        font-size: .875rem;
      }

      .mobile-trust-summary {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 10px;
      }

      .mobile-trust-summary .trust-item {
        font-size: 13px;
        color: #166534;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .mobile-trust-summary .trust-item:last-child {
        margin-bottom: 0;
      }

      .mobile-trust-summary .trust-item i {
        font-size: 14px;
      }

      .cta-row {
        width: 100%;
      }

      .cta-row .btn {
        padding: 12px 20px;
        font-size: 16px;
        font-weight: 600;
      }
    </style>
  <?php } else { ?>
    <style>
      .checkout-card {
        margin-bottom: 1.5rem;
      }

      .sidebar-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1rem;
      }

      .sidebar-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: .6rem;
        font-size: .95rem;
      }

      .sidebar-divider {
        border-top: 1px solid #e5e7eb;
        margin: 1rem 0;
      }

      .price-box {
        background: #f6f8fa;
        padding: 1rem;
        border-radius: 8px;
      }

      @media(min-width:992px) {
        #sidebar {
          position: sticky;
          top: 100px;
        }
      }

      @media(max-width:991px) {
        .sticky-footer {
          position: fixed;
          bottom: 0;
          left: 0;
          right: 0;
          background: #fff;
          padding: 1rem;
          box-shadow: 0 -2px 6px rgba(0, 0, 0, .1);
          z-index: 999;
        }
      }

      #mb-agree {
        margin-bottom: 25px;
      }

      .checkout-card#agree {
        padding: 14px 2px 2px 2px;
      }
    </style>
  <?php } ?>
  <style>
    .checkout-header {
      height: 90px;
    }

    .stepper-nav .nav-link {
      font-weight: 400;
    }
  </style>
  <style>
    .checkout-logo {
      margin-bottom: 2px;
    }

    .checkout-header {
      height: 100px;
      margin-bottom: 0px;
    }

    @media(max-width:767px) {
      .sticky-footer {
        display: flex;
        flex-direction: column;
      }
    }
  </style>
  <?php

		if(isset($cur_AdditionalTag)) {

			echo $cur_AdditionalTag;

		}

		if(isset($cur_GoogleTag)) {

			echo $cur_GoogleTag;

		}

		if($cur_FileName == "checkout-finish.php") {

			if(isset($cur_ConversionTag)) {

				echo $cur_ConversionTag;

			}

		}

	?>
</head>

<body>
  <header class="checkout-header d-flex align-items-center px-3 py-2 border-bottom">
    <div class="checkout-logo"><a href="index.php"><img src="<?= BASE_URL . $cur_LogoDark ?>" alt="<?= $cur_Title ?>" style="width:280px;margin-top:20px;"></a></div>
  </header>
  <ul class="nav stepper-nav d-flex align-items-center px-3 py-2 border-bottom bg-light">
    <li class="nav-item"><a class="nav-link" href="#"><?= $detectMobile->isMobile() ? 'Angaben' : 'Bestellangaben'; ?></a></li>
    <li class="nav-item"><a class="nav-link" href="#"><?= $detectMobile->isMobile() ? 'Termin' : 'Liefertermin'; ?></a></li>
    <li class="nav-item"><a class="nav-link active" href="#"><?= $detectMobile->isMobile() ? 'Bestätigung' : 'Bestätigung'; ?></a></li>
  </ul>
  <section class="checkout-section py-4">
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-7">

          <div class="checkout-card card">
            <div class="card-body">
              <h4 class="card-title fw-bold d-flex justify-content-between align-items-center">
                Ihre Angaben
                <a href="checkout" title="Angaben bearbeiten" style="color: #6c757d; font-size: 1rem;"><i class="fas fa-pencil-alt"></i></a>
              </h4>
              <p><strong>Versand:</strong> <?= $_SESSION['checkout']['shipping_method'] ?? 'Standardlieferung' ?> (<?= ($_SESSION['checkout']['shipping_price'] ?? 0) > 0 ? number_format($_SESSION['checkout']['shipping_price'], 2, ',', '.') . ' €' : 'Kostenlos' ?>)</p>
              <?php foreach ($_SESSION['checkout']['additional_options'] ?? [] as $opt): ?>
                <p><strong>Zusatz:</strong> <?= htmlspecialchars($opt['name']) ?> (<?= number_format($opt['price'], 2, ',', '.') ?> €)</p>
              <?php endforeach; ?>


              <?php
              if (!empty($lieferanschriften) && is_array($lieferanschriften)) {
                foreach ($lieferanschriften as $idx => $adr) { ?>
                  <p>
                    <strong><?= (count($lieferanschriften) > 1 ? "Lieferstelle #$idx:" : "Lieferanschrift:") ?></strong><br>
                    <?= htmlspecialchars("{$adr['anrede']} {$adr['vorname']} {$adr['nachname']}") ?><br>
                    <?= htmlspecialchars($adr['strasse']) ?><br>
                    <?= htmlspecialchars("{$adr['plz']} {$adr['ort']}") ?><br>
                    <?= htmlspecialchars($adr['land']) ?>
                  </p>
                <?php }
              } else {
                // Fallback falls nichts da, nimm $personal
                ?>
                <p><strong>Lieferanschrift:</strong><br>
                  <?= htmlspecialchars("{$personal['anrede']} {$personal['vorname']} {$personal['nachname']}") ?><br>
                  <?= htmlspecialchars($personal['strasse']) ?><br>
                  <?= htmlspecialchars("{$personal['plz']} {$personal['ort']}") ?><br>
                  <?= htmlspecialchars($personal['land']) ?>
                </p>
              <?php } ?>



              <?php

              //print_r($_SESSION['checkout']['lieferanschriften']);

              //echo $_SESSION['checkout']['additional_delivery'];

              ?>
              <p><strong>Zahlung:</strong> <?= htmlspecialchars($paymentMethod) ?></p>

              <?php if (!empty($deliveryDateRaw)) { ?>
                <p><strong>Liefertermin:</strong> <?= htmlspecialchars($deliveryDateLabel) ?></p>
              <?php } ?>
              <?php if (!empty($deliveryAdd)): ?>
                <p><strong>Zusatz:</strong> <?= htmlspecialchars($deliveryAdd) ?></p>
              <?php endif; ?>
            </div>
          </div>

          <div class="checkout-card card" id="agree">
            <div class="card-body">
              <form id="confirmForm" method="post">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms">
                  <label class="form-check-label" for="agree_terms">Ich habe alle Angaben überprüft und bestätigt.</label>
                </div>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" id="agree_withdraw" name="agree_withdraw">
                  <label class="form-check-label" for="agree_withdraw">Ich akzeptiere die <a href="AGB" target="_blank">Allgemeinen Geschäftsbedingungen</a> und die <a href="Datenschutz" target="_blank">Datenschutzerklärung</a>.</label>
                </div>
                <div class="sticky-footer">
                  <!-- Mobile Trust Summary -->
                  <div class="mobile-trust-summary d-lg-none">
                    <div class="trust-item"><i class="fas fa-check-circle text-success"></i> Liefertermin: <?= htmlspecialchars($deliveryDateLabel) ?></div>
                    <div class="trust-item"><i class="fas fa-check-circle text-success"></i> Sichere Zahlung garantiert</div>
                  </div>
                  <div class="cta-row container d-flex justify-content-between align-items-center mt-2">
                    <button type="submit" id="submitBtn" class="btn btn-success" disabled><?php if ($detectMobile->isMobile()) {
                                                                                            echo "Bestellung aufgeben";
                                                                                          } else {
                                                                                            echo "Bestellung abschicken";
                                                                                          } ?></button>
                  </div>
                </div>
              </form>
            </div>
          </div>

        </div>

        <div class="col-lg-5">
          <div class="sidebar-card" id="sidebar">
            <h5 class="fw-bold mb-3">Übersicht</h5>
            <div id="sidebar-main"></div>
            <div id="sidebar-delivery"></div>
            <div id="sidebar-additional"></div>
            <div id="sidebar-payment"></div>
            <div id="sidebar-date"></div>
            <div class="sidebar-divider"></div>
            <div class="price-box">
              <div id="sidebar-netto" class="sidebar-row"></div>
              <div id="sidebar-mwst" class="sidebar-row"></div>
              <div class="sidebar-divider"></div>
              <div id="sidebar-brutto" class="sidebar-row fw-bold fs-5"></div>
            </div>
          </div>

          <div class="sidebar-card mt-3">
            <h5 class="fw-bold mb-3">Vorteile</h5>
            <ul class="benefit-lists">
              <li>
                <img decoding="async" class="size-full alignnone trusted-shop-icon" src="<?= BASE_URL; ?>assets/images/trusted-shops-seal.png" alt="Siegel Vertrauenswürdige Händler">
                <p>Dieser Kauf ist durch den Trusted Shops Käuferschutz abgesichert.</p>
              </li>
              <li>
                <div class="icon-block">
                  <i class="fas fa-lock"></i>
                </div>
                <p>Sichere Datenübertragung dank SSL-Verschlüsselung.</p>
              </li>
              <li>
                <div class="icon-block">
                  <i class="fas fa-check"></i>
                </div>
                <p>Geprüfter Online-Shop mit verifizierten Kundenbewertungen.</p>
              </li>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </section>
  <script src="assets/js/jquery-3.7.1.min.js"></script>
  <script>
    $(function() {
      const cart = <?= json_encode($_SESSION['cart'], JSON_NUMERIC_CHECK); ?>;
      const ggvs = <?= $ggvs ?>;
      const paymentMethod = <?= json_encode($paymentMethod) ?>;
      const deliveryLabel = <?= json_encode($deliveryDateLabel) ?>;
      const deliveryAdd = <?= json_encode($_SESSION['checkout']['delivery_add'] ?? null) ?>;
      const shippingMethod = <?= json_encode($_SESSION['checkout']['shipping_method'] ?? 'Standardlieferung') ?>;
      const shippingPrice = <?= $_SESSION['checkout']['shipping_price'] ?? 0 ?>;

      function calcSidebar() {
        let htmlMain = '',
          net = 0;
        cart.forEach(item => {
          if (item.type === 'main') {
            const liter = item.quantity;
            const p100 = (item.price / liter) * 100;

            let p100final;
            let lieferstellen_suffix;

            if (item.lieferstellen == 1) {
              p100final = p100;
              lieferstellen_suffix = "Lieferstelle";

            } else if (item.lieferstellen > 1) {
              const anteil = p100 / item.lieferstellen;
              p100final = anteil;
              lieferstellen_suffix = "Lieferstellen";
            }

            const subtotal = item.price + ggvs;
            htmlMain += `<div class="sidebar-row"><strong>${item.name}</strong></div>`;
            htmlMain += `<div class="sidebar-row"><span>Menge:</span><span>${liter} L</span></div>
		<div class="sidebar-row"><span>Lieferstellen:</span><span>${item.lieferstellen} ${lieferstellen_suffix}</span></div>`;
            htmlMain += `<div class="sidebar-row"><span>Preis/100L:</span><span>${p100final.toFixed(2).replace('.',',')} €</span></div>`;
            htmlMain += `<div class="sidebar-row"><span>GGVS-Umlage:</span><span>${ggvs.toFixed(2).replace('.',',')} €</span></div>`;
            net += subtotal / 1.19;
          }
        });
        $('#sidebar-main').html(htmlMain);
        const shippingDisplay = shippingPrice > 0 ? shippingPrice.toFixed(2).replace('.', ',') + ' €' : 'Kostenlos';
        $('#sidebar-delivery').html(`<div class="sidebar-row"><strong>Versand:</strong> ${shippingMethod}</div><div class="sidebar-row"><span>Versandkosten:</span><span>${shippingDisplay}</span></div>`);
        net += shippingPrice / 1.19;
        let addHtml = '';
        cart.filter(i => i.type === 'additional').forEach(i => {
          addHtml += `<div class="sidebar-row">${i.name}: ${i.price.toFixed(2).replace('.',',')} €</div>`;
        });
        $('#sidebar-additional').html(addHtml);
        $('#sidebar-payment').html(`<div class="sidebar-row"><strong>Zahlung:</strong> ${paymentMethod}</div>`);

        if (deliveryLabel != 'none') {
          $('#sidebar-date').html(`<div class="sidebar-row"><strong>Termin:</strong> ${deliveryLabel}</div>`);
        }

        if (deliveryAdd) {
          $('#sidebar-date').append(`<div class="sidebar-row"><strong>Zusatz:</strong> ${deliveryAdd}</div>`);
        }

        const vat = net * 0.19,
          br = net + vat;
        $('#sidebar-netto').html(`<span>Netto:</span><span>${net.toFixed(2).replace('.',',')} €</span>`);
        $('#sidebar-mwst').html(`<span>MWSt:</span><span>${vat.toFixed(2).replace('.',',')} €</span>`);
        $('#sidebar-brutto').html(`<span>Brutto:</span><span>${br.toFixed(2).replace('.',',')} €</span>`);
      }
      // Enable submit
      $('#agree_terms,#agree_withdraw').on('change', function() {
        $('#submitBtn').prop('disabled', !($('#agree_terms').is(':checked') && $('#agree_withdraw').is(':checked')));
      });
      calcSidebar();
    });
  </script>
<?php
    include("white_trusted_widget.php");
?>
</body>

</html>