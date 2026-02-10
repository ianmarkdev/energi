<?php
	
	include("white_config.inc.php");
	include("white_actions.inc.php");

	$this_Title = "Kontakt";
	$this_Contact_Active = TRUE;
	
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

  <section class="contact-section">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-5">
          <div class="contact-card contact-info">
            <h5>Anschrift</h5>
            <p><?=$cur_Firma;?><br><?=$cur_Strasse;?><br><?=$cur_PLZ;?> <?=$cur_Ort;?></p>
            <h5>E-Mail</h5>
            <p><a href="mailto:<?=$cur_Mail;?>"><?=$cur_Mail;?></a></p>
            <h5>Öffnungszeiten</h5>
            <p>Mo–Fr: 08:00–18:00<br>Sa: 09:00–13:00</p>
          </div>
        </div>
        <div class="col-md-7">
          <div class="contact-card contact-form">
            <form id="contactForm" method="post" action="">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="cvVorname" class="form-label">Vorname</label>
                  <input type="text" id="cvVorname" name="vorname" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label for="cvNachname" class="form-label">Nachname</label>
                  <input type="text" id="cvNachname" name="nachname" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label for="cvEmail" class="form-label">E-Mail Adresse</label>
                  <input type="email" id="cvEmail" name="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label for="cvTelefon" class="form-label">Telefon</label>
                  <input type="tel" id="cvTelefon" name="telefon" class="form-control">
                </div>
                <div class="col-12">
                  <label for="cvBetreff" class="form-label">Betreff</label>
                  <input type="text" id="cvBetreff" name="betreff" class="form-control" required>
                </div>
                <div class="col-12">
                  <label for="cvNachricht" class="form-label">Nachricht</label>
                  <textarea id="cvNachricht" name="nachricht" rows="5" class="form-control" required></textarea>
                </div>
              </div>
              <div class="mt-4 text-end">
                <button type="submit" class="btn">Anfrage absenden</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php

	include("white_footer.tpl.php");
	
?>