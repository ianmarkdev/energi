<?php
session_start();
include("white_config.inc.php");
include("white_actions.inc.php");
require_once 'Mobile_Detect.php';
$detectMobile = new Mobile_Detect;

if(empty($_SESSION['checkout']['personal_data']) || empty($_SESSION['checkout']['payment_method'])) {
    header("Location: checkout");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle telephone coordination option
    $phoneOption = isset($_POST['telefonischeAbstimmung']);
    $_SESSION['checkout']['delivery_date'] = $phoneOption ? 'Telefonische Abstimmung' : ($_POST['delivery_date'] ?? null);
    $_SESSION['checkout']['delivery_add']  = null;
    // Update shipping method if changed
    $_SESSION['checkout']['shipping_method'] = $_POST['shipping'] ?? $_SESSION['checkout']['shipping_method'] ?? 'Standardlieferung';
    $_SESSION['checkout']['shipping_price'] = $_POST['shipping'] === 'Expresslieferung' ? 45.99 : 0;

    header("Location: checkout-schritt-3");
    exit;
}

$formatter = new \IntlDateFormatter(
    'de_DE',
    \IntlDateFormatter::FULL,
    \IntlDateFormatter::NONE,
    'Europe/Berlin',
    \IntlDateFormatter::GREGORIAN,
    'EEEE, d. MMMM'
);

// Generate slots for both shipping methods
function generateSlots($formatter, $daysOffset) {
    $slots = [];
    $dt = new DateTime();
    $dt->modify("+{$daysOffset} days");
    $count = 0;
    while($count < 7) {
        if($dt->format('w') != 0) { // Skip Sundays
            $label = $formatter->format($dt->getTimestamp());
            $date = $dt->format('Y-m-d');
            $slots[] = ['value'=>"{$date} 08:00-12:00", 'label'=>"{$label} (08:00 - 12:00)"];
            $slots[] = ['value'=>"{$date} 15:00-18:00", 'label'=>"{$label} (15:00 - 18:00)"];
            $count++;
        }
        $dt->modify('+1 day');
    }
    return $slots;
}

$slotsStandard = generateSlots($formatter, 14); // Standard: 14 days in advance
$slotsExpress = generateSlots($formatter, 7);   // Express: 7 days in advance

// Get current shipping method from session
$currentShipping = $_SESSION['checkout']['shipping_method'] ?? 'Standardlieferung';
$slots = ($currentShipping === 'Expresslieferung') ? $slotsExpress : $slotsStandard;

$ggvs = $global_GGVS;
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Checkout – Schritt 2/3 | <?=$cur_Title;?></title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/checkout-v1.css.php">
  
  <link rel="icon" type="image/png" href="<?= BASE_URL; ?>assets/images/favicon/favicon-96x96.png" sizes="96x96" />
  <!--<link rel="icon" type="image/svg+xml" href="<?= BASE_URL; ?>assets/images/favicon/favicon.svg" />-->
  <link rel="shortcut icon" href="<?= BASE_URL; ?>assets/images/favicon/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL; ?>assets/images/favicon/apple-touch-icon.png" />
  <link rel="manifest" href="<?= BASE_URL; ?>assets/images/favicon/site.webmanifest" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/trusted-shops.css?v=1.0.3">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    .checkout-card { margin-bottom: 1.5rem; }
    .sidebar-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1rem; }
    .sidebar-row { display: flex; justify-content: space-between; margin-bottom: .6rem; font-size: .95rem; }
    .sidebar-divider { border-top: 1px solid #e5e7eb; margin: 1rem 0; }
    .price-box { background: #f6f8fa; padding: 1rem; border-radius: 8px; }
    @media (min-width: 992px) { #sidebar { position: sticky; top: 100px; } }
    @media (max-width: 991px) { .sticky-footer { position: fixed; bottom:0; left:0; right:0; background:#fff; padding:1rem; box-shadow:0 -2px 6px rgba(0,0,0,0.1); z-index:999; } }
    select:disabled, .form-check-input:disabled + label { opacity: 0.5; pointer-events: none; }
  </style>
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
<header class="checkout-header d-flex align-items-center px-3">
  <div class="checkout-logo"><a href="index.php"><img src="<?= BASE_URL . $cur_LogoDark ?>" alt="<?= $cur_Title ?>" style="width:280px;margin-top:20px;"></a></div>
</header>

<ul class="nav stepper-nav d-flex align-items-center">
<!--
  <li class="nav-item"><a class="nav-link" href="#">Bestellangaben</a></li>
  <li class="nav-item"><a class="nav-link active" href="#">Liefertermin</a></li>
  <li class="nav-item"><a class="nav-link" href="#">Bestätigung</a></li>
  -->
  
    <li class="nav-item"><a class="nav-link" href="#"><?= $detectMobile->isMobile() ? 'Angaben' : 'Bestellangaben'; ?></a></li>
  <li class="nav-item"><a class="nav-link active" href="#"><?= $detectMobile->isMobile() ? 'Termin' : 'Liefertermin'; ?></a></li>
  <li class="nav-item"><a class="nav-link" href="#"><?= $detectMobile->isMobile() ? 'Bestätigung' : 'Bestätigung'; ?></a></li>
</ul>

<section class="checkout-section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-7">
        <form method="post">
          <input type="hidden" name="shipping" value="<?= htmlspecialchars($currentShipping) ?>">
          <div class="checkout-card">
            <h4 class="fw-bold mb-4">Liefertermin</h4>
            <p class="mb-2"><strong>Versandart:</strong> <?= $currentShipping ?><?= $currentShipping === 'Expresslieferung' ? ' (+45,99 €)' : ' (Kostenlos)' ?></p>
            <p id="delivery_date_info" class="text-muted"><?= $currentShipping === 'Expresslieferung' ? 'Verfügbare Termine ab 7 Werktagen.' : 'Verfügbare Termine ab 14 Werktagen.' ?></p>
            <div class="mb-3">
              <label for="delivery_date" class="form-label" id="delivery_date_label"><?= $currentShipping === 'Expresslieferung' ? 'Expresstermin' : 'Liefertermin' ?> <span class="text-danger">*</span></label>
              <select id="delivery_date" name="delivery_date" class="form-select required">
                <option value="">Bitte auswählen</option>
<?php foreach ($slots as $slot): ?>
                <option value="<?= htmlspecialchars($slot['value']); ?>"><?= htmlspecialchars($slot['label']); ?></option>
<?php endforeach; ?>
              </select>
            </div>
            <strong class="fw-bold mb-4">Oder</strong>
            <div class="form-check mt-3 mb-3">
              <input class="form-check-input" type="checkbox" value="Telefonische Abstimmung" id="telefonischeAbstimmung" name="telefonischeAbstimmung">
              <label class="form-check-label" for="telefonischeAbstimmung">
                Auf Wunsch stimmen wir den Liefertermin nach Ihrer Bestellung persönlich mit Ihnen ab.
              </label>
            </div>
          </div>

          <div class="alert alert-info d-flex align-items-start" role="alert" style="background-color: #e7f3ff; border: 1px solid #b6d4fe; border-radius: 10px; padding: 1rem;">
            <i class="fas fa-info-circle me-2" style="margin-top: 5px;"></i>
            <div>
              <strong>Hinweis:</strong> Nach der Bestellung kontaktieren wir Sie telefonisch zur finalen Terminbestätigung.
            </div>
          </div>

          <div class="sticky-footer">
            <div class="container d-flex justify-content-between align-items-center">
              <!--<button type="button" class="btn btn-outline-secondary" onclick="location.href='checkout.php'">&larr; Zurück</button>-->
              <button type="submit" id="nextBtn" class="btn btn-success px-4 py-2" disabled>Weiter zu: Bestätigung &rarr;</button>
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
            <div id="sidebar-mwst" class="sidebar-row"></div>
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
<script>
$(function(){
  const cart = <?= json_encode($_SESSION['cart'], JSON_NUMERIC_CHECK); ?>;
  const ggvs = <?= $ggvs ?>;

  // Delivery date slots (based on fixed shipping method from session)
  const slots = <?= json_encode($slots); ?>;

  function initDeliveryDateOptions() {
    updateSubmitState();
  }

  function updateSubmitState() {
    const phoneOption = $('#telefonischeAbstimmung').is(':checked');
    const dateSet = $('#delivery_date').val() !== "";
    $('#nextBtn').prop('disabled', !(dateSet || phoneOption));
  }

  function calcSidebar() {
    let netto = 0, mainHTML = '';
    cart.forEach(item => {
      if (item.type === 'main') {
        const liter = item.quantity;
        const price100 = (item.price / liter) * 100;
        const p100final = item.lieferstellen === 1 ? price100 : price100 / item.lieferstellen;
        const suffix = item.lieferstellen === 1 ? "Lieferstelle" : "Lieferstellen";
        const subtotal = item.price + ggvs;
        mainHTML += `<div class="sidebar-row"><strong>${item.name}</strong></div>
                     <div class="sidebar-row"><span>Menge:</span><span>${liter} L</span></div>
                     <div class="sidebar-row"><span>Lieferstellen:</span><span>${item.lieferstellen} ${suffix}</span></div>
                     <div class="sidebar-row"><span>Preis/100L:</span><span>${p100final.toFixed(2).replace('.',',')} €</span></div>
                     <div class="sidebar-row"><span>GGVS-Umlage:</span><span>${ggvs.toFixed(2).replace('.',',')} €</span></div>`;
        netto += subtotal / 1.19;
      }
    });
    $('#sidebar-main').html(mainHTML);

    // Get shipping from session (fixed, not selectable)
    const shippingMethod = '<?= $currentShipping ?>';
    const shippingPrice = <?= $currentShipping === 'Expresslieferung' ? '45.99' : '0' ?>;
    const shippingDisplay = shippingPrice > 0 ? shippingPrice.toFixed(2).replace('.',',') + ' €' : 'Kostenlos';
    let deliveryHTML = `<div class="sidebar-row"><strong>Versand:</strong> ${shippingMethod}</div>`;
    deliveryHTML += `<div class="sidebar-row"><span>Versandkosten:</span><span>${shippingDisplay}</span></div>`;
    const phoneOption = $('#telefonischeAbstimmung').is(':checked');
    const val = $('#delivery_date').val();
    if (phoneOption) {
      deliveryHTML += `<div class="sidebar-row"><strong>Termin:</strong> Telefonische Abstimmung</div>`;
    } else if (val) {
      deliveryHTML += `<div class="sidebar-row"><strong>Termin:</strong> ${$('#delivery_date option:selected').text()}</div>`;
    }
    $('#sidebar-delivery').html(deliveryHTML);
    netto += shippingPrice / 1.19;

    let addHTML = '';
    cart.filter(i => i.type==='additional').forEach(i=>{
      addHTML += `<div class="sidebar-row">${i.name}: ${i.price.toFixed(2).replace('.',',')} €</div>`;
    });
    $('#sidebar-additional').html(addHTML);

    $('#sidebar-payment').html(`<div class="sidebar-row"><strong>Zahlung:</strong> <?= $_SESSION['checkout']['payment_method'] ?></div>`);

    const mwst = netto * 0.19;
    const brutto = netto + mwst;
    $('#sidebar-netto').html(`<span>Netto:</span><span>${netto.toFixed(2).replace('.',',')} €</span>`);
    $('#sidebar-mwst').html(`<span>MWSt:</span><span>${mwst.toFixed(2).replace('.',',')} €</span>`);
    $('#sidebar-brutto').html(`<span>Brutto:</span><span>${brutto.toFixed(2).replace('.',',')} €</span>`);
  }

  $('#delivery_date').on('change', function(){
    updateSubmitState();
    calcSidebar();
  });

  $('#telefonischeAbstimmung').on('change', function(){
    const isChecked = $(this).is(':checked');
    $('#delivery_date').prop('disabled', isChecked);
    if (isChecked) {
      $('#delivery_date').val('');
    }
    updateSubmitState();
    calcSidebar();
  });

  // Auto-select first delivery date on page load
  initDeliveryDateOptions();
  calcSidebar();
});
</script>
<?php
    include("white_trusted_widget.php");
?>
</body>
</html>
