<footer class="site-footer">
  <div class="container">
    <div class="row">
      <div class="col-lg-3 col-md-6 mb-4">
        <img src="<?= $cur_LogoLight; ?>" decoding="async" loading="lazy" style="width:280px;margin-bottom:2.4rem;" alt="<?= $cur_Title; ?>">
        <div style="margin-bottom:2.4rem;">
          <?= $cur_Title; ?> ist deine erste Anlaufstelle für hochqualitatives Heizöl zu attraktiven Konditionen.
        </div>

      </div>
      <div class="col-lg-2 col-md-6 mb-4">
        <h6>Navigation</h6>
        <a href="<?= BASE_URL; ?>index.php" class="footer-link">Startseite</a>
        <a href="<?= BASE_URL; ?>Heizöl-Rechner" class="footer-link">Heizöl-Rechner</a>
        <a href="<?= BASE_URL; ?>Über-uns" class="footer-link">Über uns</a>
        <a href="<?= BASE_URL; ?>FAQ" class="footer-link">FAQ</a>
        <a href="<?= BASE_URL; ?>Kontakt" class="footer-link">Kontakt</a>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <h6>Zahlungsmethoden</h6>
        <img src="<?= BASE_URL; ?>assets/images/sepa_3-web.webp" decoding="async" loading="lazy" alt="SEPA-Überweisung als Zahlungsmethode" style="max-width:200px;">
        <!--
				<img src="<?= BASE_URL; ?>assets/images/rechnung_2-web.webp" decoding="async" loading="lazy" alt="Rechnung als Zahlungsmethode" style="max-width:200px;">
				-->
      </div>
      <div class="col-lg-4 col-md-6 mb-4">
        <!-- start partner -->
        <h6>Auszeichnungen</h6>
        <div class="textwidget" style="display: flex;">
          <p>
            <img decoding="async" class="size-full wp-image-234 alignnone" src="<?= BASE_URL; ?>assets/images/trust1-web.webp" alt="Auszeichnung von Ekomi" width="162" height="237">&nbsp; &nbsp; &nbsp; &nbsp;
          </p>
          <p>
            <a href="<?= BASE_URL; ?>trustedshops">
              <img decoding="async" class="size-full wp-image-142 alignnone trusted-shop-logo" src="<?= BASE_URL; ?>assets/images/trusted-shops-seal.png" alt="Siegel Vertrauenswürdige Händler" width="134" height="134" style="margin-top:5px;">
            </a>
            <a href="<?= BASE_URL; ?>trustedshops">
              <img decoding="async" class="size-full wp-image-689 alignnone" src="<?= BASE_URL; ?>assets/images/partner-web2.png" alt="Logos von Trustami und Ekomi" width="142" height="142">
            </a>
          </p>
        </div>
        <!-- end partner -->
      </div>
    </div>
    <div class="footer-bottom row">
      <div class="col-6 d-flex flex-wrap justify-content-between">Copyright &copy; 2025 by <?= $cur_Title; ?></div>

      <div class="col-6 d-flex flex-wrap justify-content-between" style="justify-content:end!important;">
        <a href="<?= BASE_URL; ?>AGB" class="footer-bottom-link">AGB</a>
        <a href="<?= BASE_URL; ?>Impressum" class="footer-bottom-link">Impressum</a>
        <a href="<?= BASE_URL; ?>Datenschutz" class="footer-bottom-link">Datenschutz</a>
        <a href="#" class="footer-bottom-link"
          onclick="UC_UI.showSecondLayer(); return false;">
          Cookie Einstellungen
        </a>
      </div>
    </div>
  </div>
</footer>
<!--
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
-->

<!--
<script src="<?= BASE_URL; ?>assets/js/jquery-3.7.1.min.js" defer></script>
<script src="<?= BASE_URL; ?>assets/js/bootstrap.bundle.min.js" defer></script>
-->
<!--
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256..." crossorigin="anonymous"></script>

<script src=" https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js "></script>
-->

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous" defer></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>


<script>
  // Wait for jQuery to be available (deferred loading)
  function initMobileNav() {
    function closeMobileNav() {
      $('.mobile-nav-overlay').removeClass('show');
      $('.mobile-nav-backdrop').removeClass('show');
      $('body').removeClass('overflow-hidden');
    }
    $('#mobileNavBtn').on('click', function(e) {
      $('.mobile-nav-overlay').addClass('show');
      $('.mobile-nav-backdrop').addClass('show');
      $('body').addClass('overflow-hidden');
    });
    $('#mobileNavClose, .mobile-nav-backdrop').on('click', closeMobileNav);
    $('.mobile-nav-overlay nav a').on('click', closeMobileNav);
  }
  if (typeof jQuery !== 'undefined') { $(initMobileNav); }
  else { document.addEventListener('DOMContentLoaded', function() { $(initMobileNav); }); }
</script>


<?php if ($is_File_Name == "index-test.php" or $is_File_Name == "heiz_calc.php") { ?>

  <script>
    $(document).ready(function() {
      const firstPrice = <?= $cur_FirstPrice ?>;
      const secondPrice = <?= $cur_SecondPrice ?>;
      const thirdPrice = <?= $cur_ThirdPrice ?>;

      let isValidPLZ = false;
      let isValidMenge = false;
      let isValidStellen = false;

      function validateInputs() {
        return isValidPLZ && isValidMenge && isValidStellen;
      }

      function formatPrice(num) {
        return num.toLocaleString('de-DE', {
          style: 'currency',
          currency: 'EUR'
        });
      }

      function updatePrices() {
        if (!validateInputs()) return;

        const menge = parseInt($('#menge').val(), 10);
        const stellen = parseInt($('#stellen').val(), 10);
        const calc = price => (price / 100) * menge * stellen;

        $('#price1').html(`Gesamtpreis: ${formatPrice(calc(firstPrice))}<br><span class="text-secondary small">(Preis pro 100L: ${firstPrice.toFixed(2)} €)</span>`);
        $('#price2').html(`Gesamtpreis: ${formatPrice(calc(secondPrice))}<br><span class="text-secondary small">(Preis pro 100L: ${secondPrice.toFixed(2)} €)</span>`);
        $('#price3').html(`Gesamtpreis: ${formatPrice(calc(thirdPrice))}<br><span class="text-secondary small">(Preis pro 100L: ${thirdPrice.toFixed(2)} €)</span>`);

        $('.calc-product-card').removeClass('disabled');
        $('.calc-product-btn')
          .removeClass('disabled')
          .addClass('active')
          .prop('disabled', false);
      }

      $('#plz').on('input', function() {
        const val = $(this).val().trim();
        if (!$(this).data('touched')) $(this).data('touched', true);

        if (/^\d{5}$/.test(val)) {
          isValidPLZ = true;
          if ($(this).data('touched')) {
            $(this).removeClass('is-invalid').addClass('is-valid');
          }
        } else {
          isValidPLZ = false;
          if ($(this).data('touched')) {
            $(this).removeClass('is-valid').addClass('is-invalid');
          }
        }
        updatePrices();
      });

      $('#menge').on('input', function() {
        const val = parseInt($(this).val(), 10);
        if (!$(this).data('touched')) $(this).data('touched', true);

        if (!isNaN(val) && val >= 1500 && val <= 6000) {
          isValidMenge = true;
          if ($(this).data('touched')) {
            $(this).removeClass('is-invalid').addClass('is-valid');
          }
        } else {
          isValidMenge = false;
          if ($(this).data('touched')) {
            $(this).removeClass('is-valid').addClass('is-invalid');
          }
        }
        updatePrices();
      });

      $('#stellen').on('change', function() {
        const val = $(this).val();
        if (val && !isNaN(parseInt(val))) {
          isValidStellen = true;
          $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
          isValidStellen = false;
          $(this).removeClass('is-valid').addClass('is-invalid');
        }
        updatePrices();
      });

      $('.calc-product-btn').on('click', function() {
        if (!validateInputs()) return;

        const menge = parseInt($('#menge').val(), 10);
        const stellen = parseInt($('#stellen').val(), 10);
        const productName = $(this).closest('.calc-product-card').find('.calc-product-title').text().trim();
        let rawPrice = 0;

        if (productName.includes('Standard')) {
          rawPrice = (firstPrice / 100) * menge * stellen;
        } else if (productName.includes('Premium')) {
          rawPrice = (secondPrice / 100) * menge * stellen;
        } else {
          rawPrice = (secondPrice / 100) * menge * stellen;
        }

        $.post('<?= BASE_URL; ?>addToCart.ajax.php', {
          type: 'main',
          name: productName,
          quantity: menge,
          lieferstellen: stellen,
          price: rawPrice
        }, function() {
          window.location.href = '<?= BASE_URL; ?>checkout';
        });
      });

      if ($('#stellen').val()) {
        $('#stellen').addClass('is-valid');
        isValidStellen = true;
      }

      // Keine trigger() mehr, damit bei Page Load keine Validierung erscheint
    });
  </script>


<?php } ?>

<script>
  $(function() {
    $('.faq-question').on('click', function() {
      var $item = $(this).closest('.faq-item');
      $('.faq-item.open').not($item).removeClass('open').find('.faq-answer').slideUp(250);
      $item.toggleClass('open');
      $item.find('.faq-answer').slideToggle(250);
    });
  });
</script>

<script>
  $(function() {
    const $track = $('.reviews-track');
    const cardWidth = $('.review-card').outerWidth(true);
    let index = 0;
    const maxIndex = $('.review-card').length - 1;

    $('.next-btn').on('click', function() {
      index = Math.min(index + 1, maxIndex);
      $track.css('transform', `translateX(-${cardWidth * index}px)`);
    });
    $('.prev-btn').on('click', function() {
      index = Math.max(index - 1, 0);
      $track.css('transform', `translateX(-${cardWidth * index}px)`);
    });
  });
</script>

<!--
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
-->

<?php if ($is_File_Name == "index-test.php") { ?>
<?php } ?>

<!-- bla -->

<?php

  if ($is_File_Name != "trusted-shops.php"){
    include("white_trusted_widget.php");
  }
	
?>
</body>

</html>