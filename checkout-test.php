<?php
session_start();
include("white_config.inc.php");
include("white_actions.inc.php");
require_once 'Mobile_Detect.php';
$detectMobile = new Mobile_Detect;

if(empty($_SESSION["cart"])) {
    header("Location: index.php");
    exit;
}
$price1 = $cur_FirstPrice;
$price2 = $cur_SecondPrice;
$price3 = $cur_ThirdPrice;
$ggvs   = $global_GGVS;

// Anzahl Lieferstellen aus Main-Produkt
$mainProduct = null;
foreach($_SESSION['cart'] as $item) {
    if($item['type'] === 'main') {
        $mainProduct = $item;
        break;
    }
}
$lieferstellenMax = $mainProduct && isset($mainProduct['lieferstellen']) ? (int)$mainProduct['lieferstellen'] : 1;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST['langweg'])) {
        $_SESSION['cart'][] = ['type' => 'additional', 'name' => 'Langer Weg zum Tank', 'price' => 0.00];
    }
    if(!empty($_POST['kleintank'])) {
        $_SESSION['cart'][] = ['type' => 'additional', 'name' => 'Kleiner Tankwagen', 'price' => 0.00];
    }

    // Lieferanschriften aus allen dynamischen Cards holen (die aktuell sichtbar waren beim Absenden)
    $_SESSION['checkout']['lieferanschriften'] = [];
    $numLieferstellen = intval($_POST['lieferanschriften_count'] ?? 1);

    for($i = 1; $i <= $numLieferstellen; $i++) {
        $_SESSION['checkout']['lieferanschriften'][$i] = [
            'anrede'    => $_POST["anrede_$i"] ?? '',
            'vorname'   => $_POST["vorname_$i"] ?? '',
            'nachname'  => $_POST["nachname_$i"] ?? '',
            'email'     => $i == 1 ? ($_POST["email_1"] ?? '') : '', // nur 1. Lieferanschrift
            'telefon'   => $i == 1 ? ($_POST["telefon_1"] ?? '') : '',
            'strasse'   => $_POST["strasse_$i"] ?? '',
            'plz'       => $_POST["plz_$i"] ?? '',
            'ort'       => $_POST["ort_$i"] ?? '',
            'land'      => $_POST["land_$i"] ?? '',
        ];
    }
    $_SESSION['checkout']['personal_data'] = $_SESSION['checkout']['lieferanschriften'][1];
    $_SESSION['checkout']['lieferanschriften_count'] = $numLieferstellen;
    $_SESSION['checkout']['lieferanschriften_max'] = $lieferstellenMax;

    $_SESSION['checkout']['payment_method'] = $_POST['payment'] ?? null;
    header("Location: checkout-schritt-2-test");
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Checkout – Schritt 1/3 | <?=$cur_Title;?></title>
  
  <link rel="icon" type="image/png" href="<?= BASE_URL; ?>assets/images/favicon/favicon-96x96.png" sizes="96x96" />
  <!--<link rel="icon" type="image/svg+xml" href="<?= BASE_URL; ?>assets/images/favicon/favicon.svg" />-->
  <link rel="shortcut icon" href="<?= BASE_URL; ?>assets/images/favicon/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL; ?>assets/images/favicon/apple-touch-icon.png" />
  <link rel="manifest" href="<?= BASE_URL; ?>assets/images/favicon/site.webmanifest" />
  
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/checkout-v1.css.php">
  <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/trusted-shops.css?v=1.0.4">
  <style>
    .lieferanschrift-card { border:1px solid #e2e8f0; border-radius:10px; padding:1rem; margin-bottom:1.2rem; position:relative; background:#fff; }
    .lieferanschrift-card .remove-delivery { position:absolute; top:12px; right:15px; color:#c00; cursor:pointer; font-size:1.6rem; z-index:2; }
    .lieferanschrift-card legend { font-size:1.1rem; font-weight:bold; }
    .add-lieferstelle-card { border:1.5px dashed #bbbbbb; border-radius:10px; background:#f7fafc; padding:1rem; text-align:center; color:#333; margin-bottom:1.5rem; }
    .add-lieferstelle-btn { display:inline-block; margin-top:7px; margin-bottom:2px; padding:.4rem 1.2rem; border:none; border-radius:4px; background:#18c341; color:#fff; font-weight:500; cursor:pointer; }
    .add-lieferstelle-btn:disabled { background:#c2c2c2; color:#eee; cursor:not-allowed; }
    .option-box { border:1px solid #e2e8f0; border-radius:10px; padding:1rem; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; cursor:pointer; transition:border-color 0.2s, background 0.2s; }
    .option-box input { pointer-events:none; }
    .option-box.active { border-color:#21e019; background-color:#e8f8e5; }
    .option-box label { margin:0; cursor:pointer; }
    .sidebar-row { display:flex; justify-content:space-between; margin-bottom:0.6rem; font-size:0.95rem; }
    .sidebar-divider { border-top:1px solid #e5e7eb; margin:1rem 0; }
    .price-box { background:#f6f8fa; padding:1rem; border-radius:8px; margin-top:1rem; }
  </style>
  <style>
.checkout-logo {
  margin-bottom: 2px;
}

.checkout-header {
  height: 100px;
  margin-bottom: 0px;
}
/*
.option-box.active {
  width: fit-content;
}*/
</style>
</head>
<body>
<header class="checkout-header d-flex align-items-center px-3">
  <div class="checkout-logo"><a href="index.php"><img src="<?= BASE_URL . $cur_LogoDark ?>" alt="<?= $cur_Title ?>" style="width:280px;margin-top:20px;"></a></div>
</header>
<ul class="nav stepper-nav d-flex align-items-center">
  <li class="nav-item"><a class="nav-link active" href="#"><?= $detectMobile->isMobile() ? 'Angaben' : 'Bestellangaben'; ?></a></li>
  <li class="nav-item"><a class="nav-link" href="#"><?= $detectMobile->isMobile() ? 'Termin' : 'Liefertermin'; ?></a></li>
  <li class="nav-item"><a class="nav-link" href="#"><?= $detectMobile->isMobile() ? 'Bestätigung' : 'Bestätigung'; ?></a></li>
</ul>
<section class="checkout-section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-7">
        <form id="step1form" method="post" action="">
          <div class="checkout-card">
            <h4 class="fw-bold mb-4">Versandoption</h4>
            <div class="option-box active" data-type="shipping" data-name="Standardlieferung">
              <div class="d-flex align-items-center">
                <input type="radio" name="shipping" id="shipping1" checked>
                <label for="shipping1" class="ms-2">Standardlieferung</label>
              </div>
              <span>Kostenlos</span>
            </div>
          </div>
		  
		  
		  <!--
          <div class="checkout-card">
            <h4 class="fw-bold mb-4">Zusätzliche Optionen</h4>
            <div class="option-box" data-type="additional" data-name="Langer Weg zum Tank">
              <div class="d-flex align-items-center">
                <input type="checkbox" name="langweg" id="langweg">
                <label for="langweg" class="ms-2">Langer Weg zum Tank</label>
              </div>
              <span>0,00 €</span>
            </div>
            <div class="option-box" data-type="additional" data-name="Kleiner Tankwagen">
              <div class="d-flex align-items-center">
                <input type="checkbox" name="kleintank" id="kleintank">
                <label for="kleintank" class="ms-2">Kleiner Tankwagen</label>
              </div>
              <span>0,00 €</span>
            </div>
          </div>
		  -->

          <div id="lieferanschrift-container"></div>
          <div id="add-lieferstelle-box"></div>
          <input type="hidden" name="lieferanschriften_count" id="lieferanschriften_count" value="1">

          <div class="checkout-card">
            <h4 class="fw-bold mb-4">Zahlungsmethode</h4>
            <div class="option-box" data-type="payment" data-name="SEPA-Überweisung" style="width:fit-content;<?php if($cur_EnableSEPA == 0) { echo 'display:none!important;'; } ?>">
              <div class="d-flex align-items-center">
                <input type="radio" name="payment" id="sepa" value="SEPA">
				<img src="<?=BASE_URL;?>assets/images/check-sepa-1.webp" style="<?= $detectMobile->isMobile() ? 'width:150px;margin-left:30px;' : 'width:150px;margin-left:15px;'; ?>">
              </div>
            </div>
            <div class="option-box" data-type="payment" data-name="Rechnung" style="<?php if($cur_EnableInvoice == 0) { echo 'display:none!important;'; } ?>width:fit-content;">
              <div class="d-flex align-items-center">
                <input type="radio" name="payment" id="rechnung" value="Rechnung">
				<img src="<?=BASE_URL;?>assets/images/check-rg-2.webp" style="<?= $detectMobile->isMobile() ? 'width:150px;margin-left:30px;' : 'width:150px;margin-left:15px;'; ?>">
              </div>
            </div>
          </div>
          <div class="sticky-footer">
            <div class="container d-flex justify-content-between align-items-center">
              <button type="submit" id="nextBtn" class="btn btn-success px-4 py-2" disabled>
                <?= $detectMobile->isMobile() ? "Weiter" : "Weiter zu: Liefertermin"; ?> &rarr;
              </button>
            </div>
          </div>
        </form>
      </div>
      <div class="col-lg-5">
        <div class="sidebar-card" id="sidebar">
          <h5 class="fw-bold mb-3">Übersicht</h5>
          <div id="sidebar-main"></div>
          <div id="sidebar-delivery"></div>
          <div id="sidebar-additional"></div>
          <div id="sidebar-payment"></div>
          <div class="sidebar-divider"></div>
          <div class="price-box">
            <div id="sidebar-netto" class="sidebar-row"></div>
            <div id="sidebar-mwst"  class="sidebar-row"></div>
            <div class="sidebar-divider"></div>
            <div id="sidebar-brutto" class="sidebar-row fw-bold fs-5"></div>
          </div>
        </div>
        <div class="sidebar-card mt-3">
          <h5 class="fw-bold mb-3">Vorteile</h5>
          <ul class="benefit-lists">
            <li>
              <img decoding="async" class="size-full alignnone trusted-shop-icon" src="<?= BASE_URL; ?>assets/images/trusted-shops-seal.png" alt="Siegel Vertrauenswürdige Händler" >
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


<!--
<script>
$(function(){
  const cart = <?= json_encode($_SESSION['cart'], JSON_NUMERIC_CHECK) ?>;
  const ggvs = <?= $ggvs ?>;
  // Ermitteln, wie viele Lieferstellen maximal erlaubt sind
  let lieferstellenMax = 1;
  for (const item of cart) {
    if (item.type === 'main' && item.lieferstellen) { lieferstellenMax = parseInt(item.lieferstellen); break; }
  }

  // Initial: alle Lieferstellen anzeigen
  let lieferanschriftenArr = [];
  for(let i=1; i<=lieferstellenMax; i++) lieferanschriftenArr.push(i);

  function buildLieferanschriftCards() {
    let html = '';
    for (let idx = 0; idx < lieferanschriftenArr.length; idx++) {
      let i = lieferanschriftenArr[idx];
      html += `
        <div class="lieferanschrift-card" data-lieferstelle="${i}">
          ${(lieferanschriftenArr.length > 1 && idx > 0) ? `<span class="remove-delivery" title="Entfernen">&times;</span>` : ''}
          <legend>${lieferanschriftenArr.length === 1 ? 'Lieferanschrift für alle Lieferstellen' : 'Lieferanschrift für Lieferstelle #'+(idx+1)}</legend>
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label>Anrede</label>
              <select class="form-select required-delivery" name="anrede_${i}" id="anrede_${i}">
                <option value="">Bitte auswählen</option>
                <option>Herr</option>
                <option>Frau</option>
                <option>Divers</option>
              </select>
            </div>
            <div class="col-md-4">
              <label>Vorname</label>
              <input type="text" class="form-control required-delivery" name="vorname_${i}" id="vorname_${i}">
            </div>
            <div class="col-md-4">
              <label>Nachname</label>
              <input type="text" class="form-control required-delivery" name="nachname_${i}" id="nachname_${i}">
            </div>
          </div>
          ${(idx === 0) ? `
            <div class="row g-3 mb-3">
              <div class="col-6 col-md-6">
                <label>E-Mail</label>
                <input type="email" class="form-control required-delivery" name="email_1" id="email_1">
              </div>
              <div class="col-6 col-md-6">
                <label>Telefon</label>
                <input type="text" class="form-control required-delivery" name="telefon_1" id="telefon_1">
              </div>
            </div>
          ` : ''}
          <div class="row g-3 mb-3">
            <div class="col-12 col-md-12">
              <label>Straße & Hausnummer</label>
              <input type="text" class="form-control required-delivery" name="strasse_${i}" id="strasse_${i}">
            </div>
          </div>
          <div class="row g-3">
            <div class="col-4 col-md-4">
              <label>Postleitzahl</label>
              <input type="text" class="form-control required-delivery" name="plz_${i}" id="plz_${i}">
            </div>
            <div class="col-8 col-md-8">
              <label>Ort</label>
              <input type="text" class="form-control required-delivery" name="ort_${i}" id="ort_${i}">
            </div>
            <div class="col-12 col-md-12">
              <label>Land</label>
              <select class="form-select required-delivery" name="land_${i}" id="land_${i}">
                <option>Deutschland</option>
              </select>
            </div>
          </div>
        </div>
      `;
    }
    $('#lieferanschrift-container').html(html);
    $('#lieferanschriften_count').val(lieferanschriftenArr.length);

    // Add-Button einfügen, wenn Cards < Maximum
    if(lieferanschriftenArr.length < lieferstellenMax) {
      $('#add-lieferstelle-box').html(`
        <div class="add-lieferstelle-card">
          <div>
            <span style="font-size:1.1rem; font-weight:400;">Weitere Lieferanschrift für zusätzliche Lieferstelle eintragen?</span><br>
            <button type="button" id="add-lieferstelle-btn" class="add-lieferstelle-btn">+ Weitere Lieferanschrift hinzufügen</button>
          </div>
        </div>
      `);
    } else {
      $('#add-lieferstelle-box').empty();
    }

    validateForm();
    updateTitles();
  }

  // Löschen-Handler
  $(document).on('click', '.remove-delivery', function(){
    const idx = $(this).closest('.lieferanschrift-card').index();
    if(lieferanschriftenArr.length > 1 && idx > 0) {
      lieferanschriftenArr.splice(idx, 1);
      buildLieferanschriftCards();
    }
  });

  // Hinzufügen-Handler
  $(document).on('click', '#add-lieferstelle-btn', function(){
    // Finde niedrigste freie Nummer
    for(let i=1; i<=lieferstellenMax; i++) {
      if(lieferanschriftenArr.indexOf(i) === -1) {
        lieferanschriftenArr.push(i);
        break;
      }
    }
    buildLieferanschriftCards();
  });

  function updateTitles() {
    if ($('.lieferanschrift-card').length === 1) {
      $('.lieferanschrift-card legend').text('Lieferanschrift für alle Lieferstellen');
    } else {
      $('.lieferanschrift-card').each(function(idx){
        $(this).find('legend').text('Lieferanschrift für Lieferstelle #' + (idx + 1));
      });
    }
  }
  function calcSidebar() {
    let mainHTML = '', netto = 0;
    cart.forEach(item => {
      if (item.type === 'main') {
        const liter = item.quantity;
        const p100 = (item.price / liter) * 100;
        let p100final;
        let lieferstellen_suffix;
        if (item.lieferstellen == 1) {
          p100final = p100;
          lieferstellen_suffix = "Lieferstelle";
        } else if(item.lieferstellen > 1) {
          const anteil = p100 / item.lieferstellen;
          p100final = anteil;
          lieferstellen_suffix = "Lieferstellen";
        }
        const subtotal = item.price + ggvs;
        mainHTML += `
          <div class="sidebar-row"><strong>${item.name}</strong></div>
          <div class="sidebar-row"><span>Menge:</span><span>${liter} L</span></div>
          <div class="sidebar-row"><span>Lieferstellen:</span><span>${item.lieferstellen} ${lieferstellen_suffix}</span></div>
          <div class="sidebar-row"><span>Preis/100L:</span><span>${p100final.toFixed(2).replace('.',',')} €</span></div>
          <div class="sidebar-row"><span>GGVS-Umlage:</span><span>${ggvs.toFixed(2).replace('.',',')} €</span></div>
        `;
        netto += subtotal / 1.19;
      }
    });
    $('#sidebar-main').html(mainHTML);

    let shipping = $('[data-type="shipping"]').data('name');
    $('#sidebar-delivery').html(`<div class="sidebar-row"><strong>Versand:</strong> ${shipping}</div>`);

    let addHTML = '';
    $('[data-type="additional"]').each(function() {
      const cb = $(this).find('input');
      if (cb.is(':checked')) {
        const name = $(this).data('name');
        addHTML += `<div class="sidebar-row">${name}: 0,00 €</div>`;
      }
    });
    $('#sidebar-additional').html(addHTML);

    let payment = $('[data-type="payment"] input:checked').closest('[data-type="payment"]').data('name');
    $('#sidebar-payment').html(payment ? `<div class="sidebar-row"><strong>Zahlung:</strong> ${payment}</div>` : '');

    const mwst = netto * 0.19;
    const brutto = netto + mwst;
    $('#sidebar-netto').html(`<span>Netto:</span><span>${netto.toFixed(2).replace('.',',')} €</span>`);
    $('#sidebar-mwst').html(`<span>MWSt:</span><span>${mwst.toFixed(2).replace('.',',')} €</span>`);
    $('#sidebar-brutto').html(`<span>Brutto:</span><span>${brutto.toFixed(2).replace('.',',')} €</span>`);
  }

  function validateForm() {
    let allValid = true;
    $('.lieferanschrift-card').each(function(){
      $(this).find('.required-delivery').each(function(){
        if ($(this).val().trim() === '') allValid = false;
      });
    });
    const paymentSelected = $('input[name="payment"]:checked').length > 0;
    $('#nextBtn').prop('disabled', !(allValid && paymentSelected));
  }

  // Nur eine Zusatzoption auswählbar
  $('[data-type="additional"]').on('click', function() {
    const input = $(this).find('input');
    const alreadyChecked = input.is(':checked');
    $('[data-type="additional"]').removeClass('active').find('input').prop('checked', false);
    if (!alreadyChecked) {
      $(this).addClass('active');
      input.prop('checked', true);
    }
    calcSidebar();
    validateForm();
  });

  // Versand- & Zahlungsoptionen
  $('.option-box').on('click', function() {
    const input = $(this).find('input');
    const type = $(this).data('type');
    if (input.attr('type') === 'radio') {
      $(`[data-type="${type}"]`).removeClass('active');
      $(this).addClass('active');
      input.prop('checked', true);
    }
    calcSidebar();
    validateForm();
  });

  // Live-Validierung
  $(document).on('input change', '.required-delivery, input[name="payment"]', function() {
    validateForm();
  });

  buildLieferanschriftCards();
  calcSidebar();
  validateForm();
});
</script>
-->

<script>
$(function(){
  const cart = <?= json_encode($_SESSION['cart'], JSON_NUMERIC_CHECK) ?>;
  const ggvs = <?= $ggvs ?>;
  // Ermitteln, wie viele Lieferstellen maximal erlaubt sind
  let lieferstellenMax = 1;
  for (const item of cart) {
    if (item.type === 'main' && item.lieferstellen) { lieferstellenMax = parseInt(item.lieferstellen); break; }
  }

  // Initial: alle Lieferstellen anzeigen
  let lieferanschriftenArr = [];
  for(let i=1; i<=lieferstellenMax; i++) lieferanschriftenArr.push(i);

  // Hilfsfunktion: aktuelle Werte der Lieferanschriften sammeln
  function getLieferanschriftValues() {
    let vals = {};
    $('.lieferanschrift-card').each(function(idx){
      const lieferstelle = $(this).data('lieferstelle');
      vals[lieferstelle] = {};
      $(this).find('input, select').each(function(){
        vals[lieferstelle][this.name] = $(this).val();
      });
    });
    return vals;
  }

  // Hilfsfunktion: Werte in die Inputs reinschreiben
  function setLieferanschriftValues(vals) {
    $('.lieferanschrift-card').each(function(idx){
      const lieferstelle = $(this).data('lieferstelle');
      if (vals[lieferstelle]) {
        $(this).find('input, select').each(function(){
          if(vals[lieferstelle][this.name] !== undefined) {
            $(this).val(vals[lieferstelle][this.name]);
          }
        });
      }
    });
  }

  function buildLieferanschriftCards() {
    // **Werte vor dem Umbau sichern**
    const alteWerte = getLieferanschriftValues();

    let html = '';
    for (let idx = 0; idx < lieferanschriftenArr.length; idx++) {
      let i = lieferanschriftenArr[idx];
      html += `
        <div class="lieferanschrift-card" data-lieferstelle="${i}">
          ${(lieferanschriftenArr.length > 1 && idx > 0) ? `<span class="remove-delivery" title="Entfernen">&times;</span>` : ''}
          <legend>${lieferanschriftenArr.length === 1 ? 'Lieferanschrift für alle Lieferstellen' : 'Lieferanschrift für Lieferstelle #'+(idx+1)}</legend>
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label>Anrede</label>
              <select class="form-select required-delivery" name="anrede_${i}" id="anrede_${i}">
                <option value="">Bitte auswählen</option>
                <option>Herr</option>
                <option>Frau</option>
                <option>Divers</option>
              </select>
            </div>
            <div class="col-md-4">
              <label>Vorname</label>
              <input type="text" class="form-control required-delivery" name="vorname_${i}" id="vorname_${i}">
            </div>
            <div class="col-md-4">
              <label>Nachname</label>
              <input type="text" class="form-control required-delivery" name="nachname_${i}" id="nachname_${i}">
            </div>
          </div>
          ${(idx === 0) ? `
            <div class="row g-3 mb-3">
              <div class="col-6 col-md-6">
                <label>E-Mail</label>
                <input type="email" class="form-control required-delivery" name="email_1" id="email_1">
              </div>
              <div class="col-6 col-md-6">
                <label>Telefon</label>
                <input type="text" class="form-control required-delivery" name="telefon_1" id="telefon_1">
              </div>
            </div>
          ` : ''}
          <div class="row g-3 mb-3">
            <div class="col-12 col-md-12">
              <label>Straße & Hausnummer</label>
              <input type="text" class="form-control required-delivery" name="strasse_${i}" id="strasse_${i}">
            </div>
          </div>
          <div class="row g-3">
            <div class="col-4 col-md-4">
              <label>Postleitzahl</label>
              <input type="text" class="form-control required-delivery" name="plz_${i}" id="plz_${i}">
            </div>
            <div class="col-8 col-md-8">
              <label>Ort</label>
              <input type="text" class="form-control required-delivery" name="ort_${i}" id="ort_${i}">
            </div>
            <div class="col-12 col-md-12">
              <label>Land</label>
              <select class="form-select required-delivery" name="land_${i}" id="land_${i}">
                <option>Deutschland</option>
              </select>
            </div>
          </div>
        </div>
      `;
    }
    $('#lieferanschrift-container').html(html);
    $('#lieferanschriften_count').val(lieferanschriftenArr.length);

    // Add-Button einfügen, wenn Cards < Maximum
    if(lieferanschriftenArr.length < lieferstellenMax) {
      $('#add-lieferstelle-box').html(`
        <div class="add-lieferstelle-card">
          <div>
            <span style="font-size:1.1rem; font-weight:400;">Weitere Lieferanschrift für zusätzliche Lieferstelle eintragen?</span><br>
            <button type="button" id="add-lieferstelle-btn" class="add-lieferstelle-btn">+ Weitere Lieferanschrift hinzufügen</button>
          </div>
        </div>
      `);
    } else {
      $('#add-lieferstelle-box').empty();
    }

    // **Jetzt die Werte wieder einsetzen**
    setLieferanschriftValues(alteWerte);

    validateForm();
    updateTitles();
  }

  // Löschen-Handler
  $(document).on('click', '.remove-delivery', function(){
    const idx = $(this).closest('.lieferanschrift-card').index();
    if(lieferanschriftenArr.length > 1 && idx > 0) {
      lieferanschriftenArr.splice(idx, 1);
      buildLieferanschriftCards();
    }
  });

  // Hinzufügen-Handler
  $(document).on('click', '#add-lieferstelle-btn', function(){
    // Finde niedrigste freie Nummer
    for(let i=1; i<=lieferstellenMax; i++) {
      if(lieferanschriftenArr.indexOf(i) === -1) {
        lieferanschriftenArr.push(i);
        break;
      }
    }
    buildLieferanschriftCards();
  });

  function updateTitles() {
    if ($('.lieferanschrift-card').length === 1) {
      $('.lieferanschrift-card legend').text('Lieferanschrift für alle Lieferstellen');
    } else {
      $('.lieferanschrift-card').each(function(idx){
        $(this).find('legend').text('Lieferanschrift für Lieferstelle #' + (idx + 1));
      });
    }
  }
  function calcSidebar() {
    let mainHTML = '', netto = 0;
    cart.forEach(item => {
      if (item.type === 'main') {
        const liter = item.quantity;
        const p100 = (item.price / liter) * 100;
        let p100final;
        let lieferstellen_suffix;
        if (item.lieferstellen == 1) {
          p100final = p100;
          lieferstellen_suffix = "Lieferstelle";
        } else if(item.lieferstellen > 1) {
          const anteil = p100 / item.lieferstellen;
          p100final = anteil;
          lieferstellen_suffix = "Lieferstellen";
        }
        const subtotal = item.price + ggvs;
        mainHTML += `
          <div class="sidebar-row"><strong>${item.name}</strong></div>
          <div class="sidebar-row"><span>Menge:</span><span>${liter} L</span></div>
          <div class="sidebar-row"><span>Lieferstellen:</span><span>${item.lieferstellen} ${lieferstellen_suffix}</span></div>
          <div class="sidebar-row"><span>Preis/100L:</span><span>${p100final.toFixed(2).replace('.',',')} €</span></div>
          <div class="sidebar-row"><span>GGVS-Umlage:</span><span>${ggvs.toFixed(2).replace('.',',')} €</span></div>
        `;
        netto += subtotal / 1.19;
      }
    });
    $('#sidebar-main').html(mainHTML);

    let shipping = $('[data-type="shipping"]').data('name');
    $('#sidebar-delivery').html(`<div class="sidebar-row"><strong>Versand:</strong> ${shipping}</div>`);

    let addHTML = '';
    $('[data-type="additional"]').each(function() {
      const cb = $(this).find('input');
      if (cb.is(':checked')) {
        const name = $(this).data('name');
        addHTML += `<div class="sidebar-row">${name}: 0,00 €</div>`;
      }
    });
    $('#sidebar-additional').html(addHTML);

    let payment = $('[data-type="payment"] input:checked').closest('[data-type="payment"]').data('name');
    $('#sidebar-payment').html(payment ? `<div class="sidebar-row"><strong>Zahlung:</strong> ${payment}</div>` : '');

    const mwst = netto * 0.19;
    const brutto = netto + mwst;
    $('#sidebar-netto').html(`<span>Netto:</span><span>${netto.toFixed(2).replace('.',',')} €</span>`);
    $('#sidebar-mwst').html(`<span>MWSt:</span><span>${mwst.toFixed(2).replace('.',',')} €</span>`);
    $('#sidebar-brutto').html(`<span>Brutto:</span><span>${brutto.toFixed(2).replace('.',',')} €</span>`);
  }

  function validateForm() {
    let allValid = true;
    $('.lieferanschrift-card').each(function(){
      $(this).find('.required-delivery').each(function(){
        if ($(this).val().trim() === '') allValid = false;
      });
    });
    const paymentSelected = $('input[name="payment"]:checked').length > 0;
    $('#nextBtn').prop('disabled', !(allValid && paymentSelected));
  }

  // Nur eine Zusatzoption auswählbar
  $('[data-type="additional"]').on('click', function() {
    const input = $(this).find('input');
    const alreadyChecked = input.is(':checked');
    $('[data-type="additional"]').removeClass('active').find('input').prop('checked', false);
    if (!alreadyChecked) {
      $(this).addClass('active');
      input.prop('checked', true);
    }
    calcSidebar();
    validateForm();
  });

  // Versand- & Zahlungsoptionen
  $('.option-box').on('click', function() {
    const input = $(this).find('input');
    const type = $(this).data('type');
    if (input.attr('type') === 'radio') {
      $(`[data-type="${type}"]`).removeClass('active');
      $(this).addClass('active');
      input.prop('checked', true);
    }
    calcSidebar();
    validateForm();
  });

  // Live-Validierung
  $(document).on('input change', '.required-delivery, input[name="payment"]', function() {
    validateForm();
  });

  buildLieferanschriftCards();
  calcSidebar();
  validateForm();
});
</script>

<?php
    include("white_trusted_widget.php");
?>

</body>
</html>
