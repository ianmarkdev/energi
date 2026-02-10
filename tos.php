<?php
	
	include("white_config.inc.php");
	include("white_actions.inc.php");

	$this_Title = "AGB";
	
	session_start();
	
	if($_GET["set"] == "privat") {
		$_SESSION["kunde"] = "Privatkunde";
		$this_Privatkunde_Active = TRUE;
		$this_Geschäftskunde_Active = FALSE;
		$this_Unternehmen_Active = FALSE;
	}
	
	if($_GET["set"] == "geschaeft") {
		$_SESSION["kunde"] = "Geschäftskunde";
		$this_Privatkunde_Active = FALSE;
		$this_Geschäftskunde_Active = TRUE;
		$this_Unternehmen_Active = FALSE;
	}
	
	if($_GET["set"] == "unternehmen") {
		$_SESSION["kunde"] = "Unternehmen";
		$this_Privatkunde_Active = FALSE;
		$this_Geschäftskunde_Active = FALSE;
		$this_Unternehmen_Active = TRUE;
	}
	/* == END: TOP-BAR ROUTING == */
	
	if(isset($_SESSION["cart"])) {
		unset($_SESSION["cart"]);
	}
	
	include("white_header.tpl.php");
	
?>

<section class="breadcrumb-section position-relative">
  <div class="breadcrumb-overlay"></div>
  <div class="container breadcrumb-content d-flex align-items-center h-100">
    <div class="breadcrumb-inner">
      <h1 class="breadcrumb-title"><?=$this_Title;?></h1>
      <nav class="breadcrumb-nav mt-1">
        <a href="<?= BASE_URL; ?>" class="breadcrumb-link"><?=$cur_Title;?></a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= $this_Title; ?></span>
      </nav>
    </div>
  </div>
</section>

<section class="agb-section container py-5">

<div class="col-md-12">
          <div class="contact-card contact-info">

  <h1>Allgemeine Geschäftsbedingungen (AGB)</h1>
  <p><?= $cur_Firma ?><br>
  <?= $cur_Strasse ?><br>
  <?= $cur_PLZ ?> <?= $cur_Ort ?><br>
  E-Mail: <a href="mailto:<?= $cur_Mail ?>"><?= $cur_Mail ?></a><br>
  Amtsgericht: <?= $cur_Gericht ?> | Handelsregisternummer: <?= $cur_Nummer ?><br>
  USt-IdNr.: <?= $cur_Steuernummer ?></p>

  <h2>1. Geltungsbereich</h2>
  <p>Diese Allgemeinen Geschäftsbedingungen (AGB) gelten für alle Verträge, die zwischen dem Anbieter und dem Kunden über den Online-Shop abgeschlossen werden. Abweichende Bedingungen des Kunden werden nicht anerkannt, es sei denn, der Anbieter stimmt ausdrücklich schriftlich zu.</p>

  <h2>2. Vertragspartner</h2>
  <p>Der Kaufvertrag kommt zustande mit der Firma <?= $cur_Firma ?>, vertreten durch die Geschäftsführung.</p>

  <h2>3. Vertragsgegenstand</h2>
  <p>Vertragsgegenstand ist die Lieferung von Heizöl an die vom Kunden angegebene Lieferanschrift. Die konkreten Produkte, Preise und Liefermengen ergeben sich aus der jeweiligen Bestellübersicht.</p>

  <h2>4. Vertragsschluss</h2>
  <p>Die Darstellung der Produkte im Online-Shop stellt kein rechtlich bindendes Angebot dar, sondern eine Aufforderung zur Abgabe einer Bestellung. Durch Anklicken des Buttons „Kostenpflichtig bestellen“ gibt der Kunde ein verbindliches Angebot zum Abschluss eines Kaufvertrags ab. Der Anbieter bestätigt den Eingang der Bestellung per E-Mail. Der Vertrag kommt mit dieser Auftragsbestätigung oder mit Lieferung der Ware zustande.</p>

  <h2>5. Preise & Zahlung</h2>
  <p>Alle angegebenen Preise verstehen sich in Euro und beinhalten die gesetzliche Mehrwertsteuer sowie etwaige Zuschläge wie z. B. die GGVS-Umlage. Folgende Zahlungsmethoden stehen zur Verfügung:</p>
  <ul>
    <li><strong>SEPA-Überweisung:</strong> Nach Erhalt der Rechnung Überweisen Sie den Rechnungsbetrag auf das Empfängerkonto. Die Lieferung erfolgt am vereinbarten Termin nur nach dem Geldeingang.</li>
  </ul>

  <h2>6. Lieferung</h2>
  <p>Die Lieferung erfolgt an die vom Kunden im Bestellprozess angegebene Lieferanschrift. Lieferfristen sind abhängig von der gewählten Lieferoption und der jeweiligen Region. Der Anbieter ist zu Teillieferungen berechtigt, sofern dies dem Kunden zumutbar ist.</p>

  <h2>7. Widerrufsrecht</h2>
  <p>Gemäß § 312g Abs. 2 Nr. 8 BGB besteht kein Widerrufsrecht für die Lieferung von Heizöl, da es sich um eine Ware handelt, die schnell verderben kann bzw. deren Preis von Schwankungen am Markt abhängt, auf die der Unternehmer keinen Einfluss hat.</p>

  <h2>8. Eigentumsvorbehalt</h2>
  <p>Die Ware bleibt bis zur vollständigen Bezahlung Eigentum des Anbieters.</p>

  <h2>9. Gewährleistung</h2>
  <p>Es gelten die gesetzlichen Gewährleistungsrechte. Bei offensichtlichen Mängeln hat der Kunde diese innerhalb von 7 Tagen nach Lieferung schriftlich anzuzeigen.</p>

  <h2>10. Haftung</h2>
  <p>Der Anbieter haftet unbeschränkt bei Vorsatz oder grober Fahrlässigkeit sowie bei Verletzung des Lebens, des Körpers oder der Gesundheit. Bei leicht fahrlässiger Verletzung einer wesentlichen Vertragspflicht ist die Haftung auf den vertragstypischen, vorhersehbaren Schaden begrenzt.</p>

  <h2>11. Datenschutz</h2>
  <p>Die Verarbeitung personenbezogener Daten erfolgt im Einklang mit der DSGVO. Nähere Informationen finden Sie in unserer <a href="Datenschutz">Datenschutzerklärung</a>.</p>

  <h2>12. Schlussbestimmungen</h2>
  <p>Es gilt deutsches Recht unter Ausschluss des UN-Kaufrechts. Gerichtsstand für alle Streitigkeiten ist, soweit gesetzlich zulässig, <?= $cur_Gericht ?>.</p>

</div>
</div>
</section>

<?php

	include("white_footer.tpl.php");
	
?>