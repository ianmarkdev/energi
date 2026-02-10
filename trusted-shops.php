<?php

include("white_config.inc.php");
include("white_actions.inc.php");

$this_Title = "Trusted Shops";
$this_FAQ_Active = TRUE;

session_start();

if ($_GET["set"] == "privat") {
  $_SESSION["kunde"] = "Privatkunde";
  $this_Privatkunde_Active = TRUE;
  $this_Geschäftskunde_Active = FALSE;
  $this_Unternehmen_Active = FALSE;
}

if ($_GET["set"] == "geschaeft") {
  $_SESSION["kunde"] = "Geschäftskunde";
  $this_Privatkunde_Active = FALSE;
  $this_Geschäftskunde_Active = TRUE;
  $this_Unternehmen_Active = FALSE;
}

if ($_GET["set"] == "unternehmen") {
  $_SESSION["kunde"] = "Unternehmen";
  $this_Privatkunde_Active = FALSE;
  $this_Geschäftskunde_Active = FALSE;
  $this_Unternehmen_Active = TRUE;
}
/* == END: TOP-BAR ROUTING == */

if (isset($_SESSION["cart"])) {
  unset($_SESSION["cart"]);
}

include("white_trusted_header.php");

?>
<header class="ts-header">
  <div class="ts-header-top">
      <div class="ts-header-top-inner">
          <div class="ts-header-bar">
          <a href="https://www.trustedshops.de" target="_blank" class="ts-logo">
              <img src="assets/images/e-trustedshops_black.svg" alt="Trusted Shops" style="height: 32px;">
          </a>
          <form class="ts-search" action="https://www.trustedshops.de/bewertung/" method="get" target="_blank">
              <i class="fas fa-search"></i>
              <input type="text" name="q" placeholder="Suche nach anderen Unternehmen">
          </form>
          <a href="https://www.trustedshops.de/login/" target="_blank" class="ts-header-icon"><i class="fas fa-user"></i></a>
          <div class="ts-header-seal-btn">e</div>
          </div>
      </div>
  </div>
  <div class="ts-header-company">
    <div class="ts-header-company-content">
      <div class="ts-header-company-name">
        <span data-ts-company-domain="true"><?= $site_name; ?></span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <path d="M12 2L14.4 4.4L17.6 3.2L18.8 6.4L22 7.6L20.8 10.8L23.2 13.2L20.8 15.6L22 18.8L18.8 20L17.6 23.2L14.4 22L12 24.4L9.6 22L6.4 23.2L5.2 20L2 18.8L3.2 15.6L0.8 13.2L3.2 10.8L2 7.6L5.2 6.4L6.4 3.2L9.6 4.4L12 2Z" fill="#f59e0b" />
          <path d="M10 14L8 12L7 13L10 16L17 9L16 8L10 14Z" fill="white" />
        </svg>
      </div>
      <div class="ts-header-company-stats">
        Erfahrungen: <strong><span data-ts-reviews-12m="true">89</span> Bewertungen in den letzten 12 Monaten</strong> | <strong><span data-ts-reviews-total="true">1.124</span> Bewertungen insgesamt</strong>
      </div>
      <div class="ts-header-company-rating">
        <div class="ts-header-stars">
          <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
        </div>
        <span class="ts-header-rating-value" data-ts-rating="true">4,75</span>
        <span class="ts-header-rating-text" data-ts-rating-text="true">Sehr gut</span>
        <i class="fas fa-chevron-down" style="font-size: 10px; color: #9ca3af;"></i>
      </div>
    </div>
  </div>
</header>

<main class="ts-main">
  <div class="ts-content">
    <!-- Bewertungsübersicht -->
    <div class="ts-overview">
      <div class="ts-overview-title">
        <i class="fas fa-star"></i>
        Bewertungsübersicht
      </div>
      <p class="ts-overview-text">
        Kunden loben besonders die einfache Möglichkeit, Heizölpreise zu vergleichen und dadurch günstige Angebote zu finden. Die Lieferung wird überwiegend als pünktlich, zuverlässig und unkompliziert wahrgenommen. Die Freundlichkeit und Hilfsbereitschaft der Fahrer sowie des Kundenservice werden häufig positiv hervorgehoben. Auch der Bestellvorgang wird als einfach und kundenfreundlich bewertet.
      </p>
      <div class="ts-overview-more">Mehr anzeigen <i class="fas fa-chevron-down"></i></div>
      <p class="ts-overview-note">Dieser Text wurde automatisch durch ein KI-System aus Nutzerbewertungen erstellt.</p>

      <div class="ts-tags">
        <span class="ts-tag"><i class="fas fa-plus"></i> Preisvergleich</span>
        <span class="ts-tag"><i class="fas fa-plus"></i> Lieferung</span>
        <span class="ts-tag"><i class="fas fa-plus"></i> Freundlichkeit</span>
        <span class="ts-tag"><i class="fas fa-plus"></i> Bestellprozess</span>
        <span class="ts-tag"><i class="fas fa-plus"></i> Aktualität/Information</span>
        <span class="ts-tag"><i class="fas fa-plus"></i> Zahlungsoptionen</span>
      </div>

      <div class="ts-helpful">
        <span>War diese Information hilfreich für Dich?</span>
        <div class="ts-helpful-icons">
          <i class="far fa-thumbs-up"></i>
          <i class="far fa-thumbs-down"></i>
        </div>
      </div>
    </div>

    <!-- Digital Trust Banner -->
    <div class="ts-trust-banner">
      <i class="fas fa-info-circle"></i>
      <span>Digital Trust ist unsere Mission. <a href="#legal-info">So funktionieren Bewertungen</a> bei Trusted Shops.</span>
    </div>

    <!-- Reviews Section -->
    <h2 class="ts-reviews-header">Bewertungen für <?= $site_name; ?></h2>

    <div class="ts-reviews-controls">
      <div class="ts-search-reviews">
        <i class="fas fa-search" style="color: #999;"></i>
        <input type="text" placeholder="Suchbegriff eingeben">
      </div>
      <button class="ts-filter-btn">
        Filter <i class="fas fa-chevron-down"></i>
      </button>
      <div class="ts-sort-btn">
        <i class="fas fa-sort"></i> Sortierung
      </div>
      <button class="ts-relevanz-btn">
        Relevanz <i class="fas fa-chevron-down"></i>
      </button>
    </div>

    <!-- Reviews -->
    <div id="reviews-container">
      <div class="ts-review">
        <div class="ts-review-header">
          <div class="ts-review-avatar initials red">HM</div>
          <div class="ts-review-meta">
            <div class="ts-review-author"><span class="ts-review-author-name">Heinrich M.</span></div>
            <div class="ts-review-count">1 Bewertungen</div>
            <div class="ts-review-date-verified">
              <div class="ts-review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="ts-review-date">15.12.2025</span>
              <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
            </div>
          </div>
        </div>
        <h3 class="ts-review-title">Zeitgemäß, aktuell notwendig</h3>
        <p class="ts-review-text"><?= $site_name; ?> ist eine gute Möglichkeit, um in seiner eigenen Umgebung die Lieferanten davon überzeugen kann, dass es noch günstigere Preise gibt als die einem angeboten werden. Als mein ehemaliger Lieferant erfuhr, dass ich einen wesentlich günstigeren Preis bei Wärme Schmidt bekommen habe, hat man mir sofort den gleichen Preis genannt.</p>
        <div class="ts-review-actions">
          <span>Hilfreich</span>
          <span>Melden</span>
        </div>
      </div>

      <div class="ts-review">
        <div class="ts-review-header">
          <div class="ts-review-avatar initials indigo">PS</div>
          <div class="ts-review-meta">
            <div class="ts-review-author"><span class="ts-review-author-name">Peter S.</span> | München</div>
            <div class="ts-review-count">2 Bewertungen</div>
            <div class="ts-review-date-verified">
              <div class="ts-review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="ts-review-date">17.12.2025</span>
              <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
            </div>
          </div>
        </div>
        <h3 class="ts-review-title">Alles bestens gelaufen</h3>
        <p class="ts-review-text">Fahrzeug 15 Min vor Termin da, Fahrer souverän, Fahrzeug macht einschließlich aller Amaturen optisch einen guten Eindruck. Obwohl weniger bestellt wurde der Tank ganz voll gemacht und es erfolgte keine Nachkalkulation obwohl der Händler hierzu berechtigt wäre.</p>
        <div class="ts-review-actions">
          <span>Hilfreich</span>
          <span>Melden</span>
        </div>
      </div>

      <div class="ts-review">
        <div class="ts-review-header">
          <div class="ts-review-avatar initials blue">FK</div>
          <div class="ts-review-meta">
            <div class="ts-review-author"><span class="ts-review-author-name">Frank K.</span></div>
            <div class="ts-review-count">4 Bewertungen</div>
            <div class="ts-review-date-verified">
              <div class="ts-review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="ts-review-date">28.09.2025</span>
              <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
            </div>
          </div>
        </div>
        <h3 class="ts-review-title">Geht nicht besser</h3>
        <p class="ts-review-text">Lieferung von <?= $cur_Firma; ?> schneller als erwartet. Zum Zeitpunkt der Bestellung der attraktivste Preis für meine Region. Kontakt mit Zentrale freundlich, kompetent und informativ. Ebenso der Fahrer, der mit seinen Arbeitsgeräten umzugehen weiß. Zusammenfassend: Top Service in allen Belangen.</p>
        <div class="ts-review-actions">
          <span>Hilfreich</span>
          <span>Melden</span>
        </div>
      </div>

      <div class="ts-review">
        <div class="ts-review-header">
          <div class="ts-review-avatar initials green">ID</div>
          <div class="ts-review-meta">
            <div class="ts-review-author"><span class="ts-review-author-name">Ilse D.</span></div>
            <div class="ts-review-count">2 Bewertungen</div>
            <div class="ts-review-date-verified">
              <div class="ts-review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="ts-review-date">30.11.2025</span>
              <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
            </div>
          </div>
        </div>
        <p class="ts-review-text">Diese Plattform bietet eine umfassende Übersicht über die tagesaktuellen Heizölpreise der verschiedenen Anbieter/Lieferanten. Man erhält ebenso Informationen über mögliche Entwicklungstendenzen der Preise. Der Vergleich der einzelnen Lieferanten in der jeweiligen Region ist sehr hilfreich bei der Kaufentscheidung.</p>
        <div class="ts-review-actions">
          <span>Hilfreich</span>
          <span>Melden</span>
        </div>
      </div>

      <div class="ts-review">
        <div class="ts-review-header">
          <div class="ts-review-avatar initials pink">SB</div>
          <div class="ts-review-meta">
            <div class="ts-review-author"><span class="ts-review-author-name">Sabine B.</span> | Hamburg</div>
            <div class="ts-review-count">3 Bewertungen</div>
            <div class="ts-review-date-verified">
              <div class="ts-review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="ts-review-date">17.09.2025</span>
              <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
            </div>
          </div>
        </div>
        <h3 class="ts-review-title">Zuverlässig und schnell</h3>
        <p class="ts-review-text">Wir kaufen unser Heizöl schon eine Weile über <?= $site_name; ?>. Die <?= $cur_Firma; ?> liefert zuverlässig und pünktlich. Wir waren immer sehr zufrieden mit der Abwicklung und den Lieferungen, die oftmals schneller bei uns eintrafen als wir dachten.</p>
        <div class="ts-review-actions">
          <span>Hilfreich</span>
          <span>Melden</span>
        </div>
      </div>

      <div class="ts-review">
        <div class="ts-review-header">
          <div class="ts-review-avatar initials purple">MK</div>
          <div class="ts-review-meta">
            <div class="ts-review-author"><span class="ts-review-author-name">Manuela K.</span> | Bremen</div>
            <div class="ts-review-count">3 Bewertungen</div>
            <div class="ts-review-date-verified">
              <div class="ts-review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="ts-review-date">21.12.2025</span>
              <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
            </div>
          </div>
        </div>
        <p class="ts-review-text">Jederzeit wieder freundlich, kompetent und ehrlich. Ich wurde angerufen und es wurde gefragt ob eine Lieferung am nächsten Tag geht. Das zeitnah am Bestelltermin. Leider war ich nicht Zuhause. So wurde ein anderer Termin gefunden. Jederzeit wieder.</p>
        <div class="ts-review-actions">
          <span>Hilfreich</span>
          <span>Melden</span>
        </div>
      </div>

      <div class="ts-review">
        <div class="ts-review-header">
          <div class="ts-review-avatar initials orange">WO</div>
          <div class="ts-review-meta">
            <div class="ts-review-author"><span class="ts-review-author-name">Willi O.</span></div>
            <div class="ts-review-count">5 Bewertungen</div>
            <div class="ts-review-date-verified">
              <div class="ts-review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="ts-review-date">21.12.2025</span>
              <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
            </div>
          </div>
        </div>
        <p class="ts-review-text">Die Heizölbestellung bei <?= $site_name; ?> ist schnell, zuverlässig und absolut problemlos. Zudem wurde ich von der <?= $cur_Firma; ?> noch nie enttäuscht. Jede Bestellung verlief reibungslos.</p>
        <div class="ts-review-actions">
          <span>Hilfreich</span>
          <span>Melden</span>
        </div>
      </div>

      <div class="ts-review">
        <div class="ts-review-header">
          <div class="ts-review-avatar initials teal">KN</div>
          <div class="ts-review-meta">
            <div class="ts-review-author"><span class="ts-review-author-name">Klaus N.</span></div>
            <div class="ts-review-count">6 Bewertungen</div>
            <div class="ts-review-date-verified">
              <div class="ts-review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="ts-review-date">14.12.2025</span>
              <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
            </div>
          </div>
        </div>
        <p class="ts-review-text">Das Öl wurde von Wärme Schmidt schneller als gedacht geliefert. Die Firma kenne ich schon lange über <?= $site_name; ?>. Alles war perfekt. Wenn der Preis stimmt, immer wieder gerne. Allen Mitarbeitern schöne Weihnachtstage und Glück fürs Jahr 2026!</p>
        <div class="ts-review-actions">
          <span>Hilfreich</span>
          <span>Melden</span>
        </div>
      </div>

      <div class="ts-review">
        <div class="ts-review-header">
          <div class="ts-review-avatar initials pink">LK</div>
          <div class="ts-review-meta">
            <div class="ts-review-author"><span class="ts-review-author-name">Leonhard K.</span></div>
            <div class="ts-review-count">8 Bewertungen</div>
            <div class="ts-review-date-verified">
              <div class="ts-review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="ts-review-date">16.09.2025</span>
              <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
            </div>
          </div>
        </div>
        <p class="ts-review-text">Habe hier zum ersten Mal bestellt und es hat alles sehr gut funktioniert. Leider war nur Barzahlung als Option möglich, aber der Lieferant hat mir dann vorher am Telefon die Bezahlung per EC Karte ermöglicht.</p>
        <div class="ts-review-actions">
          <span>Hilfreich <span style="color: #0ea5e9;">1</span></span>
          <span>Melden</span>
        </div>
      </div>

      <div class="ts-review">
        <div class="ts-review-header">
          <div class="ts-review-avatar initials green">TW</div>
          <div class="ts-review-meta">
            <div class="ts-review-author"><span class="ts-review-author-name">Thomas W.</span> | Berlin</div>
            <div class="ts-review-count">3 Bewertungen</div>
            <div class="ts-review-date-verified">
              <div class="ts-review-stars">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
              </div>
              <span class="ts-review-date">09.11.2025</span>
              <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
            </div>
          </div>
        </div>
        <p class="ts-review-text">Ich habe schon das zweite Mal bestellt. Kommunikation und vor allem die Bestellung mit Ratenzahlung ist total klasse. Hilft ungemein bei der Finanzplanung. Werde das auf jeden Fall beibehalten. Habe aufgrund des günstigen Preises die doppelte Menge bestellt.</p>
        <div class="ts-review-actions">
          <span>Hilfreich</span>
          <span>Melden</span>
        </div>
      </div>
    </div>

    <!-- Loading indicator -->
    <div id="loading-indicator" style="text-align: center; padding: 20px; display: none;">
      <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #0ea5e9;"></i>
    </div>
  </div>

  <!-- Sidebar -->
  <aside class="ts-sidebar">
    <div class="ts-sidebar-card collapsed" id="sidebar-info">
      <div class="ts-collapse-header" onclick="toggleSidebarCard('sidebar-info')">
        <div class="ts-seal-header">
          <img src="assets/images/trusted-shops-seal.png" alt="Trusted Shops" class="ts-seal-img">
          <div>
            <div class="ts-seal-text">Gütesiegel <span class="ts-seal-badge">gültig</span></div>
            <div class="ts-seal-sub">mit Käuferschutz</div>
          </div>
        </div>
        <i class="fas fa-chevron-up ts-collapse-icon"></i>
      </div>
      <div class="ts-collapse-content">
        <h3 class="ts-about-title" data-ts-about-title="true">Über <?= $site_name; ?></h3>
        <p class="ts-about-text" data-ts-description="true"><?= $cur_SeiteSlogan; ?></p>
        <a href="<?= $cur_Domain ?>" target="_blank" class="ts-website-btn">
          Zur Webseite <i class="fas fa-external-link-alt"></i>
        </a>

        <div class="ts-contact-title">Kontakt</div>
        <div class="ts-contact-item">
          <i class="fas fa-phone"></i>
          <a data-ts-phone="true" href="tel:<?= $cur_Telefon; ?>"><?= $cur_Telefon; ?></a>
        </div>
        <div class="ts-contact-item">
          <i class="fas fa-globe"></i>
          <a data-ts-website="true" href="<?= $cur_Domain; ?>" target="_blank"><?= $cur_Domain; ?></a>
        </div>
        <div class="ts-contact-item">
          <i class="fas fa-envelope"></i>
          <a data-ts-email="true" href="mailto:<?= $cur_Mail; ?>"><span class="__cf_email__" data-cfemail=""><?= $cur_Mail; ?></span></a>
        </div>
        <div class="ts-contact-item">
          <i class="fas fa-map-marker-alt"></i>
          <div class="ts-address-block">
            <div data-company-replace="true" style="font-weight: 600;"><?= $cur_Firma; ?></div>
            <div data-address-street-replace="true"><?= $cur_Strasse; ?><br><?= $cur_PLZ; ?> <?= $cur_Ort; ?></div>
          </div>
        </div>

        <div class="ts-register-section">
          <div class="ts-register-title">Handelsregister</div>
          <div class="ts-register-text">
            <span data-register-court-replace="true"><?= $cur_Gericht; ?></span><br>
            <span data-register-number-replace="true"><?= $cur_Nummer; ?></span>
          </div>
        </div>

        <div class="ts-ceo-section">
          <div class="ts-ceo-title">Vertreten durch</div>
          <div class="ts-ceo-name" data-ts-ceo="true"><?= $cur_CEO; ?></div>
        </div>

        <div class="ts-profiles-box">
          <p>Dieses Unternehmen hat weitere Trusted Shops Profile.</p>
          <a href="https://www.trustedshops.de" target="_blank" class="ts-profiles-btn">
            Alle Profile anzeigen <i class="fas fa-external-link-alt"></i>
          </a>
        </div>

        <div class="ts-categories-section">
          <div class="ts-categories-title">Kategorien</div>
          <a href="#" class="ts-category-link" data-ts-category="true">Brennstoffe & Heizöl</a>
        </div>
      </div>
    </div>
  </aside>
</main>
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
<script>
  function toggleSidebarCard(id) {
    if (window.innerWidth > 768) return;
    const card = document.getElementById(id);
    if (card) {
      card.classList.toggle('collapsed');
    }
  }

  function toggleVerifiedPopup() {
    const popup = document.getElementById('verified-popup');
    if (popup.style.display === 'none') {
      popup.style.display = 'block';
    } else {
      popup.style.display = 'none';
    }
  }

  document.addEventListener('click', function(e) {
    const popup = document.getElementById('verified-popup');
    const badge = document.querySelector('.ts-verified-badge');
    if (popup && badge && !badge.contains(e.target)) {
      popup.style.display = 'none';
    }
  });

  const additionalReviews = [{
      name: 'Rainer M.',
      initials: 'RM',
      color: 'blue',
      count: 4,
      date: '28.08.2025',
      title: 'Seit Jahren Stammkunde',
      text: 'Seit Jahren Stammkunde. Service ist immer top! Kann ich nur weiterempfehlen an Familie und Freunde.'
    },
    {
      name: 'Gisela F.',
      initials: 'GF',
      color: 'purple',
      count: 2,
      date: '15.07.2025',
      title: 'Unkomplizierte Bestellung',
      text: 'Unkomplizierte Bestellung, schnelle Lieferung. Sehr empfehlenswert. Der Preis war auch sehr fair.'
    },
    {
      name: 'Helmut R.',
      initials: 'HR',
      color: 'teal',
      count: 5,
      date: '02.07.2025',
      title: 'Öl wurde frühzeitig geliefert',
      text: 'Öl wurde frühzeitig geliefert. Mitarbeiterin am Telefon war sehr hilfsbereit und freundlich.'
    },
    {
      name: 'Wolfgang S.',
      initials: 'WS',
      color: 'orange',
      count: 3,
      date: '21.06.2025',
      title: 'Schnell und zuverlässig',
      text: 'Heizölbestellung schnell und zuverlässig. Verlief absolut reibungslos. Gerne wieder!'
    },
    {
      name: 'Andrea L.',
      initials: 'AL',
      color: 'pink',
      count: 1,
      date: '14.06.2025',
      title: 'Toller Service',
      text: 'Toller Service, faire Preise, pünktliche Lieferung. Was will man mehr? Absolute Empfehlung!'
    },
    {
      name: 'Dieter H.',
      initials: 'DH',
      color: 'indigo',
      count: 6,
      date: '08.06.2025',
      title: 'Immer zufrieden',
      text: 'Schon mehrfach bestellt und immer zufrieden gewesen. Sehr zuverlässiger Anbieter.'
    },
    {
      name: 'Monika S.',
      initials: 'MS',
      color: 'green',
      count: 2,
      date: '01.06.2025',
      title: 'Einfache Online-Bestellung',
      text: 'Einfache Online-Bestellung, schnelle Bestätigung, pünktliche Lieferung. Perfekt!'
    },
    {
      name: 'Werner K.',
      initials: 'WK',
      color: 'red',
      count: 4,
      date: '25.05.2025',
      title: 'Preis-Leistung stimmt',
      text: 'Preis-Leistung stimmt hier auf jeden Fall. Lieferung kam sogar einen Tag früher.'
    },
    {
      name: 'Ingrid M.',
      initials: 'IM',
      color: 'blue',
      count: 3,
      date: '18.05.2025',
      title: 'Freundliche Beratung',
      text: 'Freundliche Beratung am Telefon. Liefertermin wurde genau eingehalten. Super!'
    },
    {
      name: 'Bernd T.',
      initials: 'BT',
      color: 'purple',
      count: 7,
      date: '11.05.2025',
      title: 'Alles wie versprochen',
      text: 'Alles wie versprochen. Qualität des Heizöls ist einwandfrei. Gerne wieder!'
    }
  ];

  let currentIndex = 0;
  let isLoading = false;

  function createReviewHTML(review) {
    return `
                <div class="ts-review" style="animation: fadeIn 0.3s ease-out;">
                    <div class="ts-review-header">
                        <div class="ts-review-avatar initials ${review.color}">${review.initials}</div>
                        <div class="ts-review-meta">
                            <div class="ts-review-author"><span class="ts-review-author-name">${review.name}</span></div>
                            <div class="ts-review-count">${review.count} Bewertungen</div>
                            <div class="ts-review-date-verified">
                                <div class="ts-review-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="ts-review-date">${review.date}</span>
                                <span class="ts-review-verified"><i class="fas fa-check-circle"></i> Verifiziert</span>
                            </div>
                        </div>
                    </div>
                    <h3 class="ts-review-title">${review.title}</h3>
                    <p class="ts-review-text">${review.text}</p>
                    <div class="ts-review-actions">
                        <span>Hilfreich</span>
                        <span>Melden</span>
                    </div>
                </div>
            `;
  }

  function loadMoreReviews() {
    if (isLoading || currentIndex >= additionalReviews.length) return;

    isLoading = true;
    document.getElementById('loading-indicator').style.display = 'block';

    setTimeout(() => {
      const container = document.getElementById('reviews-container');
      const reviewsToLoad = additionalReviews.slice(currentIndex, currentIndex + 3);

      reviewsToLoad.forEach(review => {
        container.insertAdjacentHTML('beforeend', createReviewHTML(review));
      });

      currentIndex += 3;
      isLoading = false;
      document.getElementById('loading-indicator').style.display = 'none';
    }, 500);
  }

  window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
      loadMoreReviews();
    }
  });
</script>
<script>
  function checkLogoSize() {
    var logoImg = document.querySelector(".logo img");
    var headerContent = document.querySelector(".header-content");
    if (logoImg && headerContent) {
      var logoHeight = logoImg.offsetHeight;
      if (logoHeight <= 35) {
        headerContent.classList.add("logo-small");
      } else {
        headerContent.classList.remove("logo-small");
      }
    }
  }
  window.addEventListener("load", checkLogoSize);
  window.addEventListener("resize", checkLogoSize);
</script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"d6b7e08e1e9d4f048ebd0d99b47dedab","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>

<?php
/* ===============================
   BASIC SITE DATA
   =============================== */

$site_name = $site_name ?? 'Heating oil energy';
$site_url  = $cur_Domain; // 

/* ===============================
   REVIEWS (MUST MATCH HTML)
   =============================== */

$reviews = [
  [
    'author' => 'Heinrich M.',
    'date'   => '2025-12-15',
    'text'   => $site_name . ' ist eine gute Möglichkeit, um in seiner eigenen Umgebung die Lieferanten davon überzeugen kann, dass es noch günstigere Preise gibt als die einem angeboten werden.',
    'rating' => 5
  ],
  [
    'author' => 'Peter S.',
    'date'   => '2025-12-17',
    'text'   => 'Fahrzeug 15 Min vor Termin da, Fahrer souverän, Fahrzeug macht einschließlich aller Amaturen optisch einen guten Eindruck.',
    'rating' => 5
  ],
  [
    'author' => 'Frank K.',
    'date'   => '2025-09-28',
    'text'   => 'Lieferung von ' . ($cur_Firma ?? 'dem Anbieter') . ' schneller als erwartet. Zum Zeitpunkt der Bestellung der attraktivste Preis für meine Region.',
    'rating' => 5
  ],
  [
    'author' => 'Ilse D.',
    'date'   => '2025-11-30',
    'text'   => 'Diese Plattform bietet eine umfassende Übersicht über die tagesaktuellen Heizölpreise der verschiedenen Anbieter.',
    'rating' => 5
  ],
  [
    'author' => 'Sabine B.',
    'date'   => '2025-09-17',
    'text'   => 'Wir kaufen unser Heizöl schon eine Weile über ' . $site_name . '. Die Lieferung erfolgt zuverlässig und pünktlich.',
    'rating' => 5
  ]
];

/* ===============================
   AGGREGATE VALUES
   =============================== */

$review_count = count($reviews);
$rating_value = 5.0;
?>

<!-- ===============================
     ORGANIZATION + AGGREGATE RATING
     =============================== -->
<script type="application/ld+json">
  <?= json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    '@id'      => $site_url . '/#organization',
    'name'     => $site_name,
    'url'      => $site_url,
    'aggregateRating' => [
      '@type'        => 'AggregateRating',
      'ratingValue' => $rating_value,
      'reviewCount' => $review_count,
      'bestRating'  => 5,
      'worstRating' => 1
    ]
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- ===============================
     REVIEWS SCHEMA
     =============================== -->
<script type="application/ld+json">
  <?= json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => $site_name,
    'review'   => array_map(function ($r) {
      return [
        '@type' => 'Review',
        'author' => [
          '@type' => 'Person',
          'name'  => $r['author']
        ],
        'datePublished' => $r['date'],
        'reviewBody'    => $r['text'],
        'reviewRating'  => [
          '@type'       => 'Rating',
          'ratingValue' => $r['rating'],
          'bestRating'  => 5,
          'worstRating' => 1
        ]
      ];
    }, $reviews)
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>
