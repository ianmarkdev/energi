<?php
	
	include("white_config.inc.php");
	include("white_actions.inc.php");

	$this_Title = "Vielen Dank!";
	$this_Finish_Active = TRUE;
	
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
		unset($_SESSION['checkout']);
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

  <section class="contact-section">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-12 justify-content-center">
          <div class="">
            <h2>Vielen Dank für Ihre Bestellung!</h2>
			<p>Bitte prüfen Sie auch Ihren Spam- oder Junk-Ordner, falls Sie die E-Mail nicht in Ihrem Posteingang finden.
			<br>
			<h3>Was passiert als Nächstes?</h3>
			  Ihre Bestellung wird derzeit von unserem Team überprüft.<br>
			  Einer unserer Mitarbeiter wird Sie innerhalb der nächsten 1-3 Werktage telefonisch kontaktieren, um den Liefertermin zu bestätigen. <br><br>
			  			<h3>Tipps für Ihre Bestellung:</h3> 
			  	- Bitte stellen Sie sicher, dass Sie in den nächsten 1-3 Werktagen telefonisch erreichbar sind. <br>
				- Planen Sie den Liefertermin so, dass jemand vor Ort sein kann, um die Lieferung entgegenzunehmen. <br><br>
Sie können diese Seite nun schließen!









        </div>
      </div>
    </div>
  </section>

<?php

	include("white_footer.tpl.php");
	
?>