<?php
	
	include("white_config.inc.php");
	include("white_actions.inc.php");

	$this_Title = "Heizöl-Rechner";
	$this_Calc_Active = TRUE;
	
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


<section class="calc-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="calc-container">
                    <h2 class="fw-medbold mb-2" style="<?php if($detectMobile->isMobile()) { echo 'font-size:1.4rem;'; } else { echo 'font-size:2rem;'; } ?>">Welches Heizöl möchtest du bestellen?</h2>
                    <form class="mb-2 mt-3" autocomplete="off" id="calc">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="calc-label mb-1" for="plz">Postleitzahl</label>
                                <input type="text" class="form-control" id="plz" placeholder="z.B. 53115" maxlength="5">
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
                        <div class="calc-info mt-3">
                            <svg width="20" height="20" style="margin-bottom:2px;<?php if($detectMobile->isMobile()) { echo 'display:none;'; } ?>" fill="none" stroke="#123045" stroke-width="2"><circle cx="10" cy="10" r="8"/><line x1="10" y1="6" x2="10" y2="11"/><circle cx="10" cy="15" r="1.2" fill="#123045"/></svg>
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
                        <!-- <div class="col-md-4 mb-3">
                            <div class="calc-product-card disabled">
                                <div class="calc-product-title mb-2">Heizöl DIN schwefelarm</div>
                                <div class="small mb-2">Der Klassiker unter den Heizölen</div>
                                <ul class="calc-product-details ps-2 mb-3">
                                    <li><i class="bi bi-check2 text-success me-2"></i> Raffinerie-Standard-Qualität mit Preisgarantie</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Schwefelarm 50 ppm</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Zuverlässiger Heizbetrieb bei hoher Qualität</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Erfüllt DIN 51603-1</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Frei von Zusätzen</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Schnelle Lieferung</li>
                                </ul>
                                <div class="calc-product-price fw-medbold mb-3 text-success fs-5" id="price1"></div>
                                <button class="calc-product-btn w-100 disabled" disabled>
                                    Weiter mit diesem Produkt <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </button>
                                <button type="button" class="btn btn-outline-green w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modalHeizöl1">
                                    Produktdetails anzeigen <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="calc-product-card disabled">
                                <div class="calc-product-title mb-2">Sparheizöl schwefelarm</div>
                                <div class="small mb-2">Unser Effektivstes</div>
                                <ul class="calc-product-details ps-2 mb-3">
                                    <li><i class="bi bi-check2 text-success me-2"></i> 6 - 8 % Ersparnis gegenüber Heizöl DIN</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> nahezu schadstofffreie Verbrennung</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Sehr hohe Umweltverträglichkeit (überdurchschnittlich)</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Höchstmögliche Energieeffizienz</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Stark reduzierte Rußbildung</li>
                                </ul>
                                <div class="calc-product-price fw-medbold mb-3 text-success fs-5" id="price2"></div>
                                <button class="calc-product-btn w-100 disabled" disabled>
                                    Weiter mit diesem Produkt <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </button>
                                <button type="button" class="btn btn-outline-green w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modalHeizöl2">
                                    Produktdetails anzeigen <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="calc-product-card disabled">
                                <div class="calc-product-title mb-2">Sparheizöl schwefelarm CO₂</div>
                                <div class="small mb-2">Mit Beitrag fürs Klima</div>
                                <ul class="calc-product-details ps-2 mb-3">
                                    <li><i class="bi bi-check2 text-success me-2"></i> 6 - 8 % Ersparnis gegenüber Heizöl DIN</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> nahezu schadstofffreie Verbrennung</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> TÜV geprüfte CO₂-Kompensation</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Höchste Energieeffizienz</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Stark reduzierte Rußbildung</li>
                                </ul>
                                <div class="calc-product-price fw-medbold mb-3 text-success fs-5" id="price3"></div>
                                <button class="calc-product-btn w-100 disabled" disabled>
                                    Weiter mit diesem Produkt <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </button>
                                <button type="button" class="btn btn-outline-green w-100 mt-2" data-bs-toggle="modal" data-bs-target="#modalHeizöl3">
                                    Produktdetails anzeigen <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div> -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- start modals -->
<div class="modal fade" id="modalHeizöl1" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title">Produktmerkmale</h5>
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

<div class="modal fade" id="modalHeizöl2" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title">Produktmerkmale</h5>
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

<div class="modal fade" id="modalHeizöl3" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title">Produktmerkmale</h5>
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

<?php

	include("white_footer.tpl.php");
	
?>