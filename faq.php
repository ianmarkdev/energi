<?php
	
	include("white_config.inc.php");
	include("white_actions.inc.php");

	$this_Title = "FAQ";
	$this_FAQ_Active = TRUE;
	
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
        <a href="<?= BASE_URL; ?>index.php" class="breadcrumb-link"><?=$cur_Title;?></a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= $this_Title; ?></span>
      </nav>
    </div>
  </div>
</section>

<section class="faq-section py-5">
  <div class="container">
    <h2 class="faq-title text-center mb-4">FAQs zu Heizöl</h2>
    <div class="faq-list">
      <div class="faq-item">
        <div class="faq-question">
          Ist eure Anlieferung klimaneutral?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Unsere Anlieferung ist nicht direkt klimaneutral, allerdings kompensieren wir den anfallenden CO₂-Ausstoß unserer mit Diesel angetriebenen Transporter, sodass dein Heizöl dennoch CO₂-kompensiert bei dir ankommt.
        </div>
      </div>
      <div class="faq-item">
        <div class="faq-question">
          Wie setzt sich der Heizölpreis zusammen?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Rohöl ist ein börsennotiertes Produkt und wird global gehandelt. Es gibt verschiedene Einflussfaktoren: Globale Faktoren (Börsenkurs, Euro-Dollar-Kurs, Nachrichtenlage, Raffineriekapazitäten, Bestandsdaten) und lokale Faktoren (Verfügbarkeit, Logistikkosten, Rheinpegel, Steuern und Abgaben).
        </div>
      </div>
      <div class="faq-item">
        <div class="faq-question">
          Wie häufig ändert sich der Heizölpreis?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Der Heizölpreis ändert sich durch die Börsenentwicklung sehr regelmäßig und schnell.
<br><br>
Wir aktualisieren werktags die Preise mehrfach täglich – abhängig von den tatsächlichen Marktgegebenheiten – u.a. der Börseneröffnung in den USA um 15 Uhr deutscher Zeit.
<br><br>
Erste Preise werktags ab ca. 9 Uhr, um 12 Uhr ermitteln wir den Preis für unseren Heizölchart (darauf verlinken!)
<br><br>
In der Regel haben wir den aktuellen Onlinepreis rund um die Uhr abgebildet zu dem bestellt werden kann.
        </div>
      </div>
	  
	  <div class="faq-item">
        <div class="faq-question">
          Welcher Heizölpreis gilt bei meiner Lieferung?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Der Preis zum Bestellzeitpunkt ist verbindlich. Der vereinbarte Preis gilt bis zur Lieferung – egal wie sich der Preis in der Zwischenzeit entwickelt. Wir berechnen weder mehr bei steigenden, noch weniger bei sinkenden Heizölpreisen.
<br><br>
Bitte beachte: Beim Heizölkauf besteht das gesetzliche Widerrufsrecht für Verbraucherkunden nicht, weil auf Verträge über die Lieferung von Heizöl der Ausschlussgrund des § 312g Abs.2 Nr.8 BGB anwendbar ist.
<br><br>
Verbraucher können somit ihre auf Abschluss des Vertrages gerichtete Willenserklärung nicht widerrufen. Das gilt auch für den gewerblichen Kunden. Es spielt dabei grundsätzlich keine Rolle, ob der Bestellvorgang im Rahmen des Onlinehandels oder beim sogenannten Telefonverkauf erfolgt ist.
        </div>
      </div>
	  
	  <div class="faq-item">
        <div class="faq-question">
          Wann wird mein bestelltes Heizöl geliefert?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Die Lieferzeiten variieren je nach Auftragslage und Logistiksituation. Wir geben beim Bestellprozess immer eine Standardlieferzeit an und stellen transparent dar, in welchem Zeitrahmen wir liefern werden.
<br><br>
Übrigens: Ein Wunschliefertermin ist ohne Zusatzkosten möglich! Gib bei deiner Bestellung einfach neben dem Datum auch das gewünschte Zeitfenster an.
        </div>
      </div>
	  
	  <div class="faq-item">
        <div class="faq-question">
          Wie erfahre ich wann nun genau mein Heizöl geliefert wird?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Für den genauen Liefertermin rufen wir dich einige Tage vorher an und stimmen Tag und Uhrzeit nochmal mit dir ab. Bitte beachte, dass wir stets ein Zeitfenster von mehreren Stunden vereinbaren müssen, da genauere Vereinbarungen aufgrund der komplexen Logistik und Verkehrssituation nicht möglich sind.
<br><br>
Gut zu wissen: Bei deiner Bestellung kannst du bereits einen Wunschliefertermin inklusive Zeitfenster vereinbaren – natürlich kostenfrei.
        </div>
      </div>
	  
	  <div class="faq-item">
        <div class="faq-question">
          Was muss ich am Tag der Heizöllieferung beachten?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Unser Fahrer muss ungehindert Zugang zum Tankraum und Tank haben. Das Gesetz verlangt, dass unser Fahrer zudem eine Sichtkontrolle vornimmt. Dafür benötigt er Zugang zum Füllstuten.
<br><br>
Wichtig: Bitte schalte die Heizung vor Lieferung aus und erst 1-2 Stunden nach Lieferung wieder an!
        </div>
      </div>
	  
	  <div class="faq-item">
        <div class="faq-question">
          Wie kann ich meine Heizölrechnung bezahlen?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Wir bieten die Zahlung per SEPA-Überweisung an. Nach Erhalt der Rechnung muss vor Lieferung der Rechnungsbetrag bezahlt werden.
        </div>
      </div>
	  
	  <div class="faq-item">
        <div class="faq-question">
          Was ist der Unterschied zwischen Heizöl Standard
 und Heizöl Premium?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Der Hauptunterschied zwischen Heizöl Premium
 und Heizöl Standard liegt in der Effizienz und Umweltverträglichkeit. Beide Varianten sind schwefelarm und erfüllen die DIN-Norm 51603-1. Während das Heizöl Standard eine bewährte Heizleistung bietet, ermöglicht das Heizöl Premium eine Einsparung von 6–8 %, da es nahezu schadstofffrei verbrennt und eine höhere Energieeffizienz aufweist. Zudem reduziert es die Rußbildung erheblich, wodurch die Heizungsanlage geschont wird. Ein weiterer Vorteil des Sparheizöls ist die Geruchsneutralisierung, die für mehr Komfort sorgt. Beide Heizöl-Varianten sind optional CO₂-kompensiert erhältlich.
        </div>
      </div>
	  
	  <div class="faq-item">
        <div class="faq-question">
          Kann ich Heizöl Standard mit Heizöl Premium in einem Tank vermischen?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Das Mischen unserer Heizöle ist problemlos möglich, alle Sorten sind untereinander mischbar. Basis beider Heizölsorten ist die DIN 51603-1.
        </div>
      </div>
    </div>
  </div>
</section>

<?php

	include("white_footer.tpl.php");
	
?>