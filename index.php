<?php

	include("white_config.inc.php");
	include("white_actions.inc.php");

	$this_Title = "Heizöl günstig kaufen | Online Preisrechner";
	$this_Home_Active = TRUE;

	if(!session_id()) { session_start(); }

	if(isset($_GET["set"]) && $_GET["set"] == "privat") {
    $_SESSION["kunde"] = "Privatkunde";
		$this_Privatkunde_Active = TRUE;
		$this_Geschäftskunde_Active = FALSE;
		$this_Unternehmen_Active = FALSE;
	}

	if(isset($_GET["set"]) && $_GET["set"] == "geschaeft") {
    $_SESSION["kunde"] = "Geschäftskunde";
		$this_Privatkunde_Active = FALSE;
		$this_Geschäftskunde_Active = TRUE;
		$this_Unternehmen_Active = FALSE;
	}

	if(isset($_GET["set"]) && $_GET["set"] == "unternehmen") {
    $_SESSION["kunde"] = "Unternehmen";
		$this_Privatkunde_Active = FALSE;
		$this_Geschäftskunde_Active = FALSE;
		$this_Unternehmen_Active = TRUE;
	}
	/* == END: TOP-BAR ROUTING == */

	if(isset($_SESSION["cart"])) {
    unset($_SESSION["cart"]);
		unset($_SESSION['checkout']);
	}

	include("white_header.tpl.php");

  ?>

<section class="hero-section position-relative" style="<?php if($detectMobile->isMobile()) { echo 'display:none!important;'; } ?>">
  <div class="hero-overlay"></div>
  <div class="container hero-content py-5">
    <div class="col-md-7 col-lg-6">
      <h1 class="hero-headline mb-3">
        Heizöl zum Bestpreis – heute sparen
            </h1>
            <hr style="border-top:2px solid #fff; opacity:.7;">
            <div class="hero-subline mb-4" style="color:#fff;">
               Tagesaktuelle Preise, schnelle Lieferung & persönliche Beratung.
Jetzt Preis berechnen – unverbindlich & kostenlos.
            </div>
            <a href="<?=BASE_URL;?>#angebot-anfordern" class="btn btn-green px-4 py-2">Jetzt Heizöl-Preis kostenlos kalkulieren &rarr;</a>
        </div>
    </div>
</section>

<main id="main-content">
<section class="calc-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
				<div id="angebot-anfordern" style="height:1px;"><!--scroll--></div>
                <div class="calc-container">
                    <h2 class="fw-medbold mb-2" style="<?php if($detectMobile->isMobile()) { echo 'font-size:1.4rem;'; } else { echo 'font-size:2rem;'; } ?>">Welches Heizöl möchtest du bestellen?</h2>
                    <form class="mb-2 mt-3" autocomplete="off" id="calc">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="calc-label mb-1" for="plz">Postleitzahl</label>
                                <input type="text" class="form-control" id="plz" placeholder="z.B. 53115" maxlength="5" aria-describedby="calc-help" autocomplete="postal-code">
                            </div>
                            <div class="col-md-4">
                                <label class="calc-label mb-1" for="menge">Liefermenge in Liter</label>
                                <input type="number" class="form-control" id="menge" placeholder="2000">
                            </div>
                            <div class="col-md-4">
                                <label class="calc-label mb-1" for="stellen">Anzahl der Lieferstellen</label>
                                <select class="form-select" id="stellen">
                                    <option selected="">1</option>
                                    <option>2</option>
                                    <option>3</option>
                                </select>
                            </div>
                        </div>
                        <div class="calc-info mt-3" id="calc-help">
                            <svg width="20" height="20" style="margin-bottom:2px;<?php if($detectMobile->isMobile()) { echo 'display:none;'; } ?>" fill="none" stroke="#123045" stroke-width="2" aria-hidden="true" focusable="false"><circle cx="10" cy="10" r="8"/><line x1="10" y1="6" x2="10" y2="11"/><circle cx="10" cy="15" r="1.2" fill="#123045"/></svg>
                            Gib deine Postleitzahl ein, um die für deinen Standort verfügbaren Preise anzuzeigen.
                        </div>
                    </form>

                    <div class="calc-products row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="calc-product-card disabled">
                                <div class="calc-product-title mb-2">Heizöl Standard</div>
                                <div class="small mb-2">Der Klassiker unter den Heizölen</div>
                                <ul class="calc-product-details ps-2 mb-3">
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Raffinerie-Standard-Qualität mit Preisgarantie</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Schwefelarm 50 ppm</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Zuverlässiger Heizbetrieb bei hoher Qualität</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Erfüllt DIN 51603-1</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Frei von Zusätzen</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Schnelle Lieferung</li>
                                </ul>
                                <div class="calc-product-price fw-medbold mb-3 text-success fs-5" id="price1"></div>
                                <button class="calc-product-btn w-100 disabled" disabled>
                                    Weiter mit diesem Produkt <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-outline-green w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modalHeizöl1">
                                    Produktdetails anzeigen <i class="bi bi-plus-lg" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="calc-product-card disabled">
                                <div class="calc-product-title mb-2">Heizöl Premium</div>
                                <div class="small mb-2">Unser Effektivstes</div>
                                <ul class="calc-product-details ps-2 mb-3">
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> 6 - 8 % Ersparnis gegenüber Heizöl DIN</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> nahezu schadstofffreie Verbrennung</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Sehr hohe Umweltverträglichkeit (überdurchschnittlich)</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Höchstmögliche Energieeffizienz</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Stark reduzierte Rußbildung</li>
									<li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Schnelle Lieferung</li>
                                </ul>
                                <div class="calc-product-price fw-medbold mb-3 text-success fs-5" id="price2"></div>
                                <button class="calc-product-btn w-100 disabled" disabled>
                                    Weiter mit diesem Produkt <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-outline-green w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modalHeizöl2">
                                    Produktdetails anzeigen <i class="bi bi-plus-lg" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <!--<div class="col-md-4 mb-3">
                            <div class="calc-product-card disabled">
                                <div class="calc-product-title mb-2">Sparheizöl schwefelarm CO₂</div>
                                <div class="small mb-2">Mit Beitrag fürs Klima</div>
                                <ul class="calc-product-details ps-2 mb-3">
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> 6 - 8 % Ersparnis gegenüber Heizöl DIN</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> nahezu schadstofffreie Verbrennung</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> TÜV geprüfte CO₂-Kompensation</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Höchste Energieeffizienz</li>
                                    <li><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i> Stark reduzierte Rußbildung</li>
                                </ul>
                                <div class="calc-product-price fw-medbold mb-3 text-success fs-5" id="price3"></div>
                                <button class="calc-product-btn w-100 disabled" disabled>
                                    Weiter mit diesem Produkt <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-outline-green w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modalHeizöl3">
                                    Produktdetails anzeigen <i class="bi bi-plus-lg" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>-->

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- start modals -->
<div class="modal fade" id="modalHeizöl1" tabindex="-1" aria-labelledby="modalTitle1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="modalTitle1">Produktmerkmale - Heizöl Standard</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless mb-0">
          <tbody>
            <tr>
              <th>Zubuchbare Leistungen</th>
              <td>Standardlieferung</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Produktqualität</th>
              <td>DIN Norm</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>mögliche Zahlungsarten</th>
              <td>Rechnung, SEPA</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Neutralisierter Geruch</th>
              <td>Nein</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Anlieferung CO₂-kompensiert</th>
              <td>optional zubuchbar</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Schwefelarm</th>
              <td>Ja</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalHeizöl2" tabindex="-1" aria-labelledby="modalTitle2" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="modalTitle2">Produktmerkmale - Heizöl Premium</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless mb-0">
          <tbody>
            <tr>
              <th>Anlieferung CO₂-kompensiert</th>
              <td>optional zubuchbar</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Neutralisierter Geruch</th>
              <td>Ja</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Reduzierter Verbrauch</th>
              <td>Ja</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Schwefelarm</th>
              <td>Ja</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalHeizöl3" tabindex="-1" aria-labelledby="modalTitle3" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="modalTitle3">Produktmerkmale</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless mb-0">
          <tbody>
            <tr>
              <th>Zubuchbare Leistungen</th>
              <td>Auf Wunsch: CO₂-Urkunde über Kompensation</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Anlieferung CO₂-kompensiert</th>
              <td>Ja</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Neutralisierter Geruch</th>
              <td>Ja</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Reduzierter Verbrauch</th>
              <td>Ja</td>
            </tr>
            <tr><td colspan="2" class="border-top"></td></tr>
            <tr>
              <th>Schwefelarm</th>
              <td>Ja</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- end modals -->

<section class="features-section">
    <div class="container">
        <h2 class="fw-lowbold mb-5" style="font-size:2.2rem;text-align:center;color:<?=$this_SecColor;?>"><?=$cur_Title;?> – ausgewählte und zertifizierte Produktqualität</h2>
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div>

					<center><img src="<?=BASE_URL;?>assets/images/1-web.webp" loading="lazy" decoding="async" width="150" height="150" alt="Beste Heizölqualität" style="width:150px;height:auto;"></center>
                    <h5 class="fw-medbold mt-2 mb-2">Beste Heizölqualität</h5>
                    <div class="text-secondary small">Mit Heizöl von <?=$cur_Title;?> bekommst du geprüfte und zertifizierte Produkte für deinen Kessel.</div>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div>
                    <center><img src="<?=BASE_URL;?>assets/images/2-web.webp" loading="lazy" decoding="async" width="150" height="150" alt="CO₂-kompensierte Anlieferung" style="width:150px;height:auto;"></center>
                    <h5 class="fw-medbold mt-2 mb-2">CO₂-kompensierte Anlieferung</h5>
                    <div class="text-secondary small">Wir kompensieren die CO₂-Emissionen, die bei der Anfahrt mit unseren Dieselfahrzeugen entstehen.</div>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div>
                    <center><img src="<?=BASE_URL;?>assets/images/3-web.webp" loading="lazy" decoding="async" width="150" height="150" alt="Kauf auf Rechnung möglich" style="width:150px;height:auto;"></center>
                    <h5 class="fw-medbold mt-2 mb-2">Kauf auf Rechnung möglich</h5>
                    <div class="text-secondary small">SEPA-Überweisung oder auf Rechnung – Wir akzeptieren eine Reihe unterschiedlicher Zahlarten.</div>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div>
                    <center><img src="<?=BASE_URL;?>assets/images/4-web.webp" loading="lazy" decoding="async" width="150" height="150" alt="Persönlicher Service" style="width:150px;height:auto;"></center>
                    <h5 class="fw-medbold mt-2 mb-2">Persönlicher Service</h5>
                    <div class="text-secondary small">Du hast Fragen zum Vertrag oder unseren Produkten? Unser Kundenservice ist persönlich für dich da.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Schema for Rich Results -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Ist eure Anlieferung klimaneutral?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Unsere Anlieferung ist nicht direkt klimaneutral, allerdings kompensieren wir den anfallenden CO₂-Ausstoß unserer mit Diesel angetriebenen Transporter, sodass dein Heizöl dennoch CO₂-kompensiert bei dir ankommt."
      }
    },
    {
      "@type": "Question",
      "name": "Wie setzt sich der Heizölpreis zusammen?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Rohöl ist ein börsennotiertes Produkt und wird global gehandelt. Es gibt verschiedene Einflussfaktoren: Globale Faktoren (Börsenkurs, Euro-Dollar-Kurs, Nachrichtenlage, Raffineriekapazitäten, Bestandsdaten) und lokale Faktoren (Verfügbarkeit, Logistikkosten, Rheinpegel, Steuern und Abgaben)."
      }
    },
    {
      "@type": "Question",
      "name": "Wie häufig ändert sich der Heizölpreis?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Der Heizölpreis ändert sich durch die Börsenentwicklung sehr regelmäßig und schnell. Wir aktualisieren werktags die Preise mehrfach täglich."
      }
    },
    {
      "@type": "Question",
      "name": "Wann wird mein bestelltes Heizöl geliefert?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Die Lieferzeiten variieren je nach Auftragslage und Logistiksituation. Es gibt auch die Möglichkeit der Expresslieferung innerhalb von 5 Werktagen und der 48-Std-Express Lieferung gegen Aufpreis."
      }
    }
  ]
}
</script>

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
Zudem gibt es die Möglichkeit der Expresslieferung innerhalb von 5 Werktagen und der 48-Std-Express Lieferung gegen Aufpreis.
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
          Du kannst deine Rechnung auf verschiedene Wege begleichen: Rechnung, bar bei Lieferung, Vorkasse, EC oder Kreditkarte oder mit deinem individuellen Zahlplan von <?=$cur_Title;?>.
<br><br>
Bitte beachte: Die angebotenen Zahlarten können eingeschränkt und abhängig sein von Faktoren wie Bonität, Rechnungswert oder der gewählten Lieferart.
        </div>
      </div>

	  <div class="faq-item">
        <div class="faq-question">
          Was ist der Unterschied zwischen Standard und <?=$cur_Title;?> Sparheizöl?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Der Hauptunterschied zwischen <?=$cur_Title;?> Standard Heizöl und Sparheizöl liegt in der Effizienz und Umweltverträglichkeit. Beide Varianten sind schwefelarm und erfüllen die DIN-Norm 51603-1. Während das Standard Heizöl eine bewährte Heizleistung bietet, ermöglicht das Sparheizöl eine Einsparung von 6–8 %, da es nahezu schadstofffrei verbrennt und eine höhere Energieeffizienz aufweist. Zudem reduziert es die Rußbildung erheblich, wodurch die Heizungsanlage geschont wird. Ein weiterer Vorteil des Sparheizöls ist die Geruchsneutralisierung, die für mehr Komfort sorgt. Beide Heizöl-Varianten sind optional CO₂-kompensiert erhältlich.
        </div>
      </div>

	  <div class="faq-item">
        <div class="faq-question">
          Kann ich Standardheizöl mit <?=$cur_Title;?> Sparheizöl in einem Tank vermischen?
          <span class="faq-icon">⌄</span>
        </div>
        <div class="faq-answer">
          Das Mischen unserer Heizöle ist problemlos möglich, alle Sorten sind untereinander mischbar. Basis beider Heizölsorten ist die DIN 51603-1.
        </div>
      </div>
    </div>
  </div>
</section>



<section class="ral-section py-5">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-md-4 text-center text-md-start">

        <img src="<?= BASE_URL ?>assets/images/teaser2-web.webp"
             alt="RAL Gütezeichen für zertifiziertes Heizöl"
             class="img-fluid ral-img" loading="lazy" decoding="async" width="200" height="200">




      </div>
      <div class="col-md-8">
        <h2 class="ral-title">Zertifizierte Qualität – mit dem RAL-Gütezeichen</h2>
        <p class="ral-text">
          Das RAL-Gütezeichen gilt als Qualitätsprädikat für den sicheren Energie-Einkauf.
          Es weist auf eine hochwertige Produktgüte, zuverlässige Liefermengen,
          qualifiziertes Fachpersonal und regelmäßige Sicherheitschecks hin. Wir
          tragen das RAL-Gütezeichen seit vielen Jahren und sind als zuverlässiger
          Heizöl-Lieferant in der Region bekannt. Auf unseren Kundenservice sind wir
          besonders stolz.
        </p>
      </div>
    </div>
  </div>
</section>

<section class="reviews-section py-5">
  <div class="container">
    <h2 class="reviews-title text-center mb-4">Was unsere Kunden sagen</h2>
    <div class="row g-4">
      <div class="col-12 col-md-6">
        <div class="review-card h-100">
          <div class="review-header">
            <div>
              <strong>Familie Müller</strong> <span class="verified"><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i></span><br>
              <small><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Hamburg &nbsp;•&nbsp; Vor 2 Wochen</small>
            </div>
            <div class="stars" role="img" aria-label="5 von 5 Sternen"><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i></div>
          </div>
          <h5 class="review-title">Schnell, zuverlässig und super Service!</h5>
          <p class="review-text">
            Bestellung am Montag aufgegeben, Mittwoch war das Heizöl da. Der Fahrer war pünktlich und sehr freundlich. Preis war deutlich günstiger als beim lokalen Händler.
          </p>
          <div class="review-order">Bestellung: 3.500 L Premium Heizöl</div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="review-card h-100">
          <div class="review-header">
            <div>
              <strong>Thomas K.</strong> <span class="verified"><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i></span><br>
              <small><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> München &nbsp;•&nbsp; Vor 1 Woche</small>
            </div>
            <div class="stars" role="img" aria-label="5 von 5 Sternen"><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i></div>
          </div>
          <h5 class="review-title">Hervorragende Beratung und Top-Qualität</h5>
          <p class="review-text">
            Wurde telefonisch sehr gut beraten. Das Premium-Heizöl brennt deutlich sauberer als das vom vorherigen Anbieter. Gerne wieder!
          </p>
          <div class="review-order">Bestellung: 5.000 L Premium Heizöl</div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="review-card h-100">
          <div class="review-header">
            <div>
              <strong>Renate S.</strong> <span class="verified"><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i></span><br>
              <small><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Köln &nbsp;•&nbsp; Vor 3 Wochen</small>
            </div>
            <div class="stars" role="img" aria-label="4,5 von 5 Sternen"><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-half" aria-hidden="true"></i></div>
          </div>
          <h5 class="review-title">Faire Preise, keine versteckten Kosten</h5>
          <p class="review-text">
            Endlich ein Anbieter ohne Überraschungen! Preis online berechnet, bestellt, geliefert. Genau wie versprochen. Sehr zu empfehlen.
          </p>
          <div class="review-order">Bestellung: 2.800 L Standard Heizöl</div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="review-card h-100">
          <div class="review-header">
            <div>
              <strong>Marcus W.</strong> <span class="verified"><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i></span><br>
              <small><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Berlin &nbsp;•&nbsp; Vor 1 Monat</small>
            </div>
            <div class="stars" role="img" aria-label="5 von 5 Sternen"><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i></div>
          </div>
          <h5 class="review-title">Professionell und kundenorientiert</h5>
          <p class="review-text">
            Brauchte dringend Heizöl und bekam einen Notfall-Termin. Alles hat super geklappt. Der Service ist wirklich außergewöhnlich gut.
          </p>
          <div class="review-order">Bestellung: 4.200 L Premium Heizöl</div>
        </div>
      </div>
	        <div class="col-12 col-md-6">
        <div class="review-card h-100">
          <div class="review-header">
            <div>
              <strong>Familie Schneider</strong> <span class="verified"><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i></span><br>
              <small><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Stuttgart &nbsp;•&nbsp; Vor 4 Tagen</small>
            </div>
            <div class="stars" role="img" aria-label="4,5 von 5 Sternen"><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-half" aria-hidden="true"></i></div>
          </div>
          <h5 class="review-title">Einfacher Bestellprozess, schnelle Lieferung</h5>
          <p class="review-text">
            Alles lief online problemlos ab. Am Liefertag war der Fahrer pünktlich auf die Minute. Heizölqualität ist top, wir bestellen wieder.
          </p>
          <div class="review-order">Bestellung: 3.000 L Standard Heizöl</div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="review-card h-100">
          <div class="review-header">
            <div>
              <strong>Andreas F.</strong> <span class="verified"><i class="bi bi-check2 text-success me-2" aria-hidden="true"></i></span><br>
              <small><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Bremen &nbsp;•&nbsp; Vor 5 Tagen</small>
            </div>
            <div class="stars" role="img" aria-label="5 von 5 Sternen"><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i><i class="bi bi-star-fill" aria-hidden="true"></i></div>
          </div>
          <h5 class="review-title">Top Preis-Leistung und sehr freundlich</h5>
          <p class="review-text">
            Habe spontan bestellt und trotzdem eine schnelle Lieferung bekommen. Fahrer war hilfsbereit und kompetent. Absolut empfehlenswert!
          </p>
          <div class="review-order">Bestellung: 3.700 L Premium Heizöl</div>
        </div>
      </div>
    </div>
  </div>
</section>



</main>

<?php

	include("white_footer.tpl.php");

?>
