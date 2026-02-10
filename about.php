<?php
	
	include("white_config.inc.php");
	include("white_actions.inc.php");

	$this_Title = "Über uns";
	$this_About_Active = TRUE;
	
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


<section class="ral-section py-5">
  <div class="container">
    <div class="row align-items-center g-4 mb-5">
      <div class="col-md-4 text-center text-md-start">
        <img src="<?= BASE_URL ?>assets/images/about-8.jpg"
             alt="RAL Gütezeichen"
             class="img-fluid about-img"
			 style="border-radius:20px;">
      </div>
      <div class="col-md-8">
        <h2 class="ral-title">Mit nur wenigen Mausklicks zu Ihrem Heizöl</h2>
        <p class="ral-text">
          Wir bieten eine große Bandbreite an unterschiedlichen Heizölvarianten in verschiedensten Qualitätsstufen und Lieferoptionen an. Ganz gleich, ob Standard-, Premium- oder schwefelarmes Heizöl – bei uns finden Sie garantiert das passende Produkt für Ihre individuellen Bedürfnisse.
        </p>
        <p class="ral-text">
          Unsere Kundinnen und Kunden profitieren von einer transparenten Preisgestaltung, flexiblen Lieferzeiten und einer persönlichen Beratung, die weit über das übliche Maß hinausgeht. Ob Online-Bestellung oder telefonischer Kontakt – unser Service ist auf Effizienz und Kundenzufriedenheit ausgerichtet.
        </p>
        <p class="ral-text">
          Mit unserem digitalen Bestellprozess sparen Sie nicht nur Zeit, sondern erhalten auch aktuelle Marktpreise in Echtzeit. Unser Ziel ist es, Heizöl-Bestellungen so einfach und zuverlässig wie möglich zu gestalten – für Privatpersonen, Hausverwaltungen und Gewerbe.
        </p>
      </div>
    </div>

    <div class="row align-items-center g-4 flex-md-row-reverse">
      <div class="col-md-4 text-center text-md-end">
        <img src="<?= BASE_URL ?>assets/images/about-7.PNG"
             alt="Zufriedene Heizöl-Kunden"
             class="img-fluid about-img"
			 style="border-radius:20px;">
      </div>
      <div class="col-md-8">
        <h2 class="ral-title">Kundenzufriedenheit ist unser Brennstoff</h2>
        <p class="ral-text">
          Über 95 % unserer Kundinnen und Kunden bewerten unseren Service mit „sehr gut“. Vom ersten Klick bis zur pünktlichen Lieferung steht bei uns der Mensch im Mittelpunkt. Unser erfahrenes Team sorgt dafür, dass jede Lieferung reibungslos und zuverlässig abläuft – selbst bei kurzfristigen Bestellungen oder speziellen Anforderungen.
        </p>
        <p class="ral-text">
          Wir setzen auf regelmäßige Qualitätssicherung und arbeiten ausschließlich mit zertifizierten Partnern und regionalen Lieferanten zusammen. So garantieren wir nicht nur die Einhaltung höchster Standards, sondern stärken auch lokale Strukturen.
        </p>
        <p class="ral-text">
          Ihre Zufriedenheit ist für uns nicht nur ein Anspruch – sie ist der Maßstab, an dem wir unseren täglichen Service messen.
        </p>
      </div>
    </div>
  </div>
</section>


<?php

	include("white_footer.tpl.php");
	
?>