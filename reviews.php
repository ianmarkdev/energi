<?php
	
	include("white_config.inc.php");
	include("white_actions.inc.php");

	$this_Title = "Bewertungen und Erfahrungen unserer Kund*innen";
	$this_Title_HTML = "Bewertungen";
	$this_Reviews_Active = TRUE;
	
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
      <h1 class="breadcrumb-title"><?=$this_Title_HTML;?></h1>
      <nav class="breadcrumb-nav mt-1">
        <a href="<?= BASE_URL; ?>index.php" class="breadcrumb-link"><?=$cur_Title;?></a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= $this_Title_HTML; ?></span>
      </nav>
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
              <strong>Familie Müller</strong> <span class="verified"><i class="bi bi-check2 text-success me-2"></i></span><br>
              <small><i class="bi bi-geo-alt-fill"></i> Hamburg &nbsp;•&nbsp; Vor 2 Wochen</small>
            </div>
            <div class="stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
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
              <strong>Thomas K.</strong> <span class="verified"><i class="bi bi-check2 text-success me-2"></i></span><br>
              <small><i class="bi bi-geo-alt-fill"></i> München &nbsp;•&nbsp; Vor 1 Woche</small>
            </div>
            <div class="stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
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
              <strong>Renate S.</strong> <span class="verified"><i class="bi bi-check2 text-success me-2"></i></span><br>
              <small><i class="bi bi-geo-alt-fill"></i> Köln &nbsp;•&nbsp; Vor 3 Wochen</small>
            </div>
            <div class="stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i></div>
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
              <strong>Marcus W.</strong> <span class="verified"><i class="bi bi-check2 text-success me-2"></i></span><br>
              <small><i class="bi bi-geo-alt-fill"></i> Berlin &nbsp;•&nbsp; Vor 1 Monat</small>
            </div>
            <div class="stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
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
              <strong>Familie Schneider</strong> <span class="verified"><i class="bi bi-check2 text-success me-2"></i></span><br>
              <small><i class="bi bi-geo-alt-fill"></i> Stuttgart &nbsp;•&nbsp; Vor 4 Tagen</small>
            </div>
            <div class="stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i></div>
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
              <strong>Andreas F.</strong> <span class="verified"><i class="bi bi-check2 text-success me-2"></i></span><br>
              <small><i class="bi bi-geo-alt-fill"></i> Bremen &nbsp;•&nbsp; Vor 5 Tagen</small>
            </div>
            <div class="stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
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

<?php

	include("white_footer.tpl.php");
	
?>