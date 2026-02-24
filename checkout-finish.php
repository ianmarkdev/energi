<?php
include("white_config.inc.php");
include("white_actions.inc.php");

// Clear any remaining session data
if(isset($_SESSION["cart"])) {
    unset($_SESSION["cart"]);
    unset($_SESSION['checkout']);
}
?>
<!doctype html>
<html data-n-head-ssr lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <meta name="robots" content="noindex,nofollow">
  <title>Bestellung abgeschlossen - HeizOel24</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/checkout.css">
</head>
<body class="bg-white">
  <div id="__nuxt">
    <div id="__layout">
      <div id="order-process" class="overflow-hidden">
        <div class="nav-container focused-navbar">
          <nav class="navbar bg-white shadow-sm px-0 px-md-3 navbar-light">
            <div class="container">
              <div class="row w-100 no-gutters justify-content-center">
                <div class="d-flex align-items-center order-3 justify-content-end justify-content-md-start order-md-1 col mr-auto">
                  <button type="button" class="btn d-flex align-items-center burger-button btn-transparent">
                    <div class="burger-icon"><span></span><span></span><span></span><span></span></div>
                    <div class="ml-3 d-none d-md-block text-muted">Menü</div>
                  </button>
                </div>
                <a href="/" class="navbar-brand order-2 mr-0 col text-center"><img alt="<?= $cur_Title; ?> Logo" src="<?= BASE_URL . $cur_LogoDark; ?>" class="brand-link" style="max-height:40px;"></a>
                <div class="order-1 order-md-3 ml-auto col d-flex justify-content-md-end align-items-center"></div>
              </div>
            </div>
          </nav>
        </div>
        <main role="main">
          <div id="login-registration">
            <div class="container-md">
              <div class="row d-block mx-lg-0 row-max-width">
                <div class="row stretch-max-height row-gutter-bigger-lg justify-content-center row-max-width">
                  <div class="col-12 col-gutter-bigger-lg py-lg-5 py-2 col-md-8 col-lg-6 order-1 order-md-0">
                    <div class="b-overlay-wrap position-relative login-view modal-lg-padding p-3 p-md-5 border-0 my-3 card shadow-md-blur text-center">

                      <div class="mb-4">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <circle cx="12" cy="12" r="10" fill="#16a34a" opacity="0.15"/>
                          <path d="M9 12l2 2 4-4" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                          <circle cx="12" cy="12" r="10" stroke="#16a34a" stroke-width="1.5"/>
                        </svg>
                      </div>

                      <div class="text-larger font-weight-bold mb-3">Vielen Dank für Ihre Bestellung!</div>

                      <div class="text-smaller text-gray-700 mb-4">
                        Ihre Bestellung wurde erfolgreich aufgenommen. Sie erhalten in Kürze eine Bestätigungs-E-Mail.
                      </div>

                      <div class="border rounded p-3 mb-4 text-left bg-light">
                        <div class="font-weight-bold text-smaller mb-2">Die nächsten Schritte:</div>
                        <div class="text-smaller-2 text-gray-700">
                          <div class="mb-2"><strong>1.</strong> Wir rufen Sie innerhalb der nächsten 24 Stunden an, um den Liefertermin zu vereinbaren.</div>
                          <div class="mb-2"><strong>2.</strong> Nach Terminvereinbarung erhalten Sie Ihre Rechnung per E-Mail.</div>
                          <div class="mb-2"><strong>3.</strong> Nach Zahlungseingang wird der Liefertermin verbindlich bestätigt.</div>
                          <div><strong>4.</strong> Das Heizöl wird geliefert und bei Ihnen getankt.</div>
                        </div>
                      </div>

                      <div class="bg-green-100 p-3 rounded text-smaller-2 text-left mb-4">
                        <strong>Hinweis:</strong> Bitte prüfen Sie auch Ihren Spam-Ordner, falls Sie keine E-Mail erhalten. Stellen Sie sicher, dass Sie telefonisch erreichbar sind.
                      </div>

                      <a href="index.php" class="btn btn-flat-green w-100">Zurück zur Startseite</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>
</body>
</html>
