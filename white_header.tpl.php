<?php

	require_once 'Mobile_Detect.php';
	$detectMobile = new Mobile_Detect;

	if(empty($_SESSION["kunde"]) OR $_SESSION["kunde"] == "Privatkunde") {
		$this_Privatkunde_Active = TRUE;
		$this_Geschäftskunde_Active = FALSE;
		$this_Unternehmen_Active = FALSE;
	} else if($_SESSION["kunde"] == "Geschäftskunde") {
		$this_Privatkunde_Active = FALSE;
		$this_Geschäftskunde_Active = TRUE;
		$this_Unternehmen_Active = FALSE;
	} else if($_SESSION["kunde"] == "Unternehmen") {
		$this_Privatkunde_Active = FALSE;
		$this_Geschäftskunde_Active = FALSE;
		$this_Unternehmen_Active = TRUE;
	}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?=$cur_Title;?> | <?=$this_Title;?></title>
	<base href="<?=$this_BaseURL;?>" />
	
	<!-- MyClickShield Tracking Code -->
<script>
(function(w,d,s,k){
  w.MCS=w.MCS||{};w.MCS.k=k;
  var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s);
  j.async=true;
  j.src='https://t.myclickshield.com/t.js?k='+k;
  f.parentNode.insertBefore(j,f);
})(window,document,'script','nMjAaI6MrNGatFTZr1uhZMNvjvmvxaKm');
</script>
<!-- End MyClickShield Tracking Code -->
	
	<!-- Preconnect to external resources for faster loading -->
	<link rel="preconnect" href="https://code.jquery.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="dns-prefetch" href="https://code.jquery.com">
	<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
	<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

	<meta name="description" content="Jetzt Heizöl online kaufen zum besten Preis – mit Lieferung, Rechnung & persönlichem Service. Einfach Postleitzahl & Menge eingeben – sofort Preis berechnen.">
	<meta name="robots" content="index, follow">
	<meta name="theme-color" content="#14313c">

	<!-- Open Graph -->
	<meta property="og:title" content="Heizöl günstig bestellen – <?=$cur_Title;?>">
	<meta property="og:description" content="Heizöl online kalkulieren und bestellen – fair & zuverlässig. Jetzt Preis sichern.">
	<meta property="og:image" content="<?=$cur_Domain;?><?=$cur_LogoDark;?>">
	<meta property="og:url" content="<?=$cur_Domain;?>">
	<meta property="og:type" content="website">
	<meta property="og:locale" content="de_DE">
	<meta property="og:site_name" content="<?=$cur_Title;?>">

	<!-- Twitter Card -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?=$cur_Title;?> – Heizöl online">
	<meta name="twitter:description" content="Schnell & günstig Heizöl bestellen mit <?=$cur_Title;?>.">
	<meta name="twitter:image" content="<?=$cur_Domain;?><?=$cur_LogoDark;?>">

	<link rel="canonical" href="<?=$cur_Domain . $_SERVER['REQUEST_URI']?>">


	<link rel="icon" type="image/png" href="<?= BASE_URL; ?>assets/images/favicon/favicon-96x96.png" sizes="96x96" />
	<!--
	<link rel="icon" type="image/svg+xml" href="<?= BASE_URL; ?>assets/images/favicon/favicon.svg" />
	-->
	<link rel="shortcut icon" href="<?= BASE_URL; ?>assets/images/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL; ?>assets/images/favicon/apple-touch-icon.png" />
	<link rel="manifest" href="<?= BASE_URL; ?>assets/images/favicon/site.webmanifest" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "Product",
		  "name": "Premium Heizöl",
		  "image": "<?=$cur_Domain;?>/<?=$cur_LogoDark;?>",
		  "description": "Hochwertiges Premium Heizöl mit sauberer Verbrennung und schneller Lieferung.",
		  "sku": "PH-3500",
		  "brand": {
		    "@type": "Brand",
		    "name": "HeizölDirekt"
		  },
		  "aggregateRating": {
		    "@type": "AggregateRating",
		    "ratingValue": "5.0",
		    "reviewCount": "6"
		  },
		  "review": [
		    {
		      "@type": "Review",
			  "author": { "@type": "Person", "name": "Familie Müller" },
		      "datePublished": "2025-06-10",
		      "reviewBody": "Bestellung am Montag aufgegeben, Mittwoch war das Heizöl da. Der Fahrer war pünktlich und sehr freundlich.",
		      "name": "Schnell, zuverlässig und super Service!",
		      "reviewRating": {
		        "@type": "Rating",
		        "ratingValue": "5",
		        "bestRating": "5"
		      }
		    },
		    {
		      "@type": "Review",
		      "author": { "@type": "Person", "name": "Thomas K." },
		      "datePublished": "2025-06-14",
		      "reviewBody": "Wurde telefonisch sehr gut beraten. Das Premium-Heizöl brennt deutlich sauberer als das vom vorherigen Anbieter.",
		      "name": "Hervorragende Beratung und Top-Qualität",
		      "reviewRating": {
		        "@type": "Rating",
		        "ratingValue": "5",
		        "bestRating": "5"
		      }
		    },
		    {
		      "@type": "Review",
		      "author": { "@type": "Person", "name": "Renate S." },
		      "datePublished": "2025-06-03",
		      "reviewBody": "Endlich ein Anbieter ohne Überraschungen! Preis online berechnet, bestellt, geliefert.",
		      "name": "Faire Preise, keine versteckten Kosten",
		      "reviewRating": {
		        "@type": "Rating",
		        "ratingValue": "4.5",
		        "bestRating": "4.5"
		      }
		    },
		    {
		      "@type": "Review",
		      "author": { "@type": "Person", "name": "Marcus W." },
		      "datePublished": "2025-05-23",
		      "reviewBody": "Brauchte dringend Heizöl und bekam einen Notfall-Termin. Der Service ist wirklich außergewöhnlich gut.",
		      "name": "Professionell und kundenorientiert",
		      "reviewRating": {
		        "@type": "Rating",
		        "ratingValue": "5",
		        "bestRating": "5"
		      }
		    },
		    {
		      "@type": "Review",
		      "author": { "@type": "Person", "name": "Familie Schneider" },
		      "datePublished": "2025-06-19",
		      "reviewBody": "Alles lief online problemlos ab. Am Liefertag war der Fahrer pünktlich auf die Minute.",
		      "name": "Einfacher Bestellprozess, schnelle Lieferung",
		      "reviewRating": {
		        "@type": "Rating",
		        "ratingValue": "4.5",
		        "bestRating": "4.5"
		      }
		    },
		    {
		      "@type": "Review",
		      "author": { "@type": "Person", "name": "Andreas F." },
		      "datePublished": "2025-06-18",
		      "reviewBody": "Habe spontan bestellt und trotzdem eine schnelle Lieferung bekommen. Fahrer war hilfsbereit.",
		      "name": "Top Preis-Leistung und sehr freundlich",
		      "reviewRating": {
		        "@type": "Rating",
		        "ratingValue": "5",
		        "bestRating": "5"
		      }
		    }
		  ]
		}
		</script>

	<!-- Organization Schema for Local SEO -->
	<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "Organization",
		"name": "<?=$cur_Title;?>",
		"url": "<?=$cur_Domain;?>",
		"logo": "<?=$cur_Domain;?><?=$cur_LogoDark;?>",
		"contactPoint": {
			"@type": "ContactPoint",
			"telephone": "<?=$cur_Telefon;?>",
			"contactType": "customer service",
			"availableLanguage": "German"
		},
		"address": {
			"@type": "PostalAddress",
			"streetAddress": "<?=$cur_Strasse;?>",
			"postalCode": "<?=$cur_PLZ;?>",
			"addressLocality": "<?=$cur_Ort;?>",
			"addressCountry": "DE"
		}
	}
	</script>

	<script id="usercentrics-cmp" src="https://web.cmp.usercentrics.eu/ui/loader.js" data-settings-id="ShUBcwBfUDZaVQ" async></script>

	<!--
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	-->

	<!-- Critical CSS inline for faster First Contentful Paint -->
	<style>
	/* Critical above-the-fold styles */
	*,::after,::before{box-sizing:border-box}body{margin:0;font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;font-size:1rem;line-height:1.5;background:#f6f8fa}
	.container{width:100%;padding-right:12px;padding-left:12px;margin-right:auto;margin-left:auto}
	@media(min-width:576px){.container{max-width:540px}}@media(min-width:768px){.container{max-width:720px}}@media(min-width:992px){.container{max-width:960px}}@media(min-width:1200px){.container{max-width:1140px}}@media(min-width:1400px){.container{max-width:1320px}}
	.navbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;padding:.5rem 1rem;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.03);min-height:64px}
	.navbar-brand{font-weight:700;font-size:2rem;text-decoration:none}
	.d-none{display:none!important}.d-flex{display:flex!important}.d-xl-none{display:none!important}.d-xl-flex{display:flex!important}
	@media(max-width:1199.98px){.d-xl-none{display:block!important}.d-xl-flex{display:none!important}}
	.top-bar{background:rgb(243 244 245/1);border-bottom:1px solid #d8d8d8;font-weight:500}
	.top-bar-mobile{background:rgb(243 244 245/1);border-bottom:1px solid #d8d8d8}
	.py-2{padding-top:.5rem!important;padding-bottom:.5rem!important}.py-3{padding-top:1rem!important;padding-bottom:1rem!important}
	.align-items-center{align-items:center!important}.justify-content-center{justify-content:center!important}
	img{max-width:100%;height:auto}
	</style>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/addition-v1.css.php">
	<link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/special-v1.css.php">
	<link href="assets/css/bootstrap-icons.css" rel="stylesheet">
	<link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/trusted-shops.css?v=1.0.3">
	<?php if($detectMobile->isMobile()) { ?>
	<!-- Mobile: preload font for faster loading -->
	<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
	<?php } ?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<?php

		if($is_File_Name == "index.php") {

	?>
	<!-- Preload LCP image - only hero background (one high priority) -->
	<?php if($detectMobile->isMobile()) { ?>
	<link rel="preload" as="image" href="<?=BASE_URL;?>assets/images/bg-1.webp" fetchpriority="high">
	<?php } ?>

	<style>
	.hero-section {
		background: url('<?=BASE_URL;?>assets/images/bg-1.webp') center/cover no-repeat;
	}
	</style>
	<?php

		} else {

	?>
	<link rel="preload" as="image" href="<?=BASE_URL;?>assets/images/bg-1.webp">

	<style>
	.breadcrumb-section {
		background: url('/assets/images/bg-1.webp') center center / cover no-repeat;
	}
	</style>
	<?php

		}

	?>

	<style>
	.btn-green {
		color: #000;
		font-weight: 500 !important;
		font-size: 17px;
	}

	.calc-product-btn[disabled] {
		background: #e3e3e3;
		color: #333;
		opacity: .76;
		font-weight: 400;
		border: 1px solid #333;
	}
	</style>

	<?php

		if($detectMobile->isMobile()) {

	?>
	<style>
	.calc-section {
		padding: 16px 0 40px 0;
	}
	</style>
	<?php

		}

	?>

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

	<style>
	.calc-product-btn.disabled {
    background-color: #e7eaec;
    color: #9eaab2;
    border: none;
    cursor: not-allowed;
}
.calc-product-btn.disabled i {
    color: #9eaab2;
}

.calc-product-btn.active {
    background-color: #0c2a3e;
    color: #ffffff;
    font-weight: 550;
    border: none;
	font-size:15px;
}
.calc-product-btn.active i {
    color: #18e127;
}

[type="button"]:not(:disabled), [type="reset"]:not(:disabled), [type="submit"]:not(:disabled), button:not(:disabled) {
  cursor: pointer;
  background: #eee;
  border: 1px solid #e7eaec;
  color: #333;
}
</style>

<!--
<link rel="preload" href="<?=BASE_URL;?>assets/js/jquery-3.7.1.min.js" as="script">
<link rel="preload" href="<?=BASE_URL;?>assets/js/bootstrap.bundle.min.js" as="script">
-->
<!--
<script src="<?=BASE_URL;?>assets/js/jquery-3.7.1.min.js"></script>
<script src="<?=BASE_URL;?>assets/js/bootstrap.bundle.min.js"></script>
-->

</head>
<body>

<!-- Skip link for keyboard navigation -->
<a href="#main-content" class="skip-link" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;z-index:9999;padding:1rem;background:#14313c;color:#fff;text-decoration:none;font-weight:600;">Zum Hauptinhalt springen</a>
<style>.skip-link:focus{position:fixed!important;left:50%!important;top:10px!important;transform:translateX(-50%);width:auto!important;height:auto!important;overflow:visible!important;}</style>

<!-- Mobile Top Bar with Trustpilot Widget -->
<div class="top-bar-mobile d-xl-none py-3" style="background: rgb(243 244 245/1); border-bottom: 1px solid #d8d8d8; min-height: 50px; display: flex; align-items: center; justify-content: center;">
	<div id="trustpilot-widget-mobile" style="width: 240px;"></div>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		var containerMobile = document.getElementById('trustpilot-widget-mobile');
		if (containerMobile) {
			var iframeMobile = document.createElement('iframe');
			iframeMobile.src = '<?=BASE_URL;?>trustpilot-proxy.php';
			iframeMobile.width = '250';
			iframeMobile.height = '34';
			iframeMobile.frameBorder = '0';
			iframeMobile.scrolling = 'no';
			iframeMobile.style.border = 'none';
			iframeMobile.style.display = 'block';
			containerMobile.appendChild(iframeMobile);
		}
	});
	</script>
</div>

<div class="top-bar d-none d-xl-flex py-2 align-items-center">
	<div class="d-flex align-items-center" style="gap:1rem;">
		<div class="container-fluid d-flex align-items-center" style="gap:1rem;">
			<a href="<?=BASE_URL;?>privatkunde" class="topbar-link <?php echo ($this_Privatkunde_Active === TRUE) ? 'active' : ''; ?>">Privatkunde</a>
			<a href="<?=BASE_URL;?>geschäftskunde" class="topbar-link <?php echo ($this_Geschäftskunde_Active === TRUE) ? 'active' : ''; ?>">Geschäftskunde</a>
			<a href="<?=BASE_URL;?>unternehmen" class="topbar-link <?php echo ($this_Unternehmen_Active === TRUE) ? 'active' : ''; ?>">Unternehmen</a>
		</div>
	</div>

	<div class="ms-auto d-flex align-items-center" style="height: 100%;">
		<!-- Trust Pilot Widget Test 2 -->
		<div id="trustpilot-widget" style="display:flex; align-items:center; justify-content:center;"></div>
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			var container = document.getElementById('trustpilot-widget');
			if (container) {
				var iframe = document.createElement('iframe');
				iframe.src = '<?=BASE_URL;?>trustpilot-proxy.php';
				iframe.width = '260';
				iframe.height = '34';
				iframe.frameBorder = '0';
				iframe.scrolling = 'no';
				iframe.style.border = 'none';
				iframe.style.display = 'block';
				container.appendChild(iframe);
			}
		});
		</script>
	</div>
</div>


<nav class="navbar navbar-expand-xl px-2">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="<?=BASE_URL;?>index.php">
            <img src="<?=BASE_URL;?><?=$cur_LogoDark;?>" alt="<?=$cur_Title;?>" style="width:250px;" fetchpriority="high" decoding="async" width="250" height="60">
        </a>
        <button class="navbar-toggler ms-auto border-0" type="button" id="mobileNavBtn" title="Mobiles Menü">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse d-none d-xl-flex align-items-center" id="mainNav" style="width:100%;">
            <div class="mx-auto d-flex align-items-center gap-3" style="gap:2rem;">
                <a class="main-nav-link <?php if(!empty($this_Home_Active)) { echo 'active'; } ?>" href="<?= BASE_URL; ?>index.php">Startseite</a>
                <a class="main-nav-link <?php if(!empty($this_Calc_Active)) { echo 'active'; } ?>" href="<?= BASE_URL; ?>Heizöl-Rechner">Heizöl-Rechner</a>
                <a class="main-nav-link <?php if(!empty($this_About_Active)) { echo 'active'; } ?>" href="<?= BASE_URL; ?>Über-uns">Über uns</a>
                <a class="main-nav-link <?php if(!empty($this_FAQ_Active)) { echo 'active'; } ?>" href="<?= BASE_URL; ?>FAQ">FAQ</a>
				<a class="main-nav-link <?php if(!empty($this_Reviews_Active)) { echo 'active'; } ?>" href="<?= BASE_URL; ?>Bewertungen">Bewertungen</a>
                <a class="main-nav-link <?php if(!empty($this_Contact_Active)) { echo 'active'; } ?>" href="<?= BASE_URL; ?>Kontakt">Kontakt</a>
            </div>
            <div id="rightContainer" class="d-flex align-items-center ms-4">
                <a href="mailto:<?=$cur_Mail;?>" id="cleanLink"><?=$cur_Mail;?></a>
                <span style="margin-left:4rem;font-size:1.12rem;"><img src="<?=BASE_URL;?>assets/images/de.svg" alt="Deutschland" width="24" height="16" style=""> Deutschland</span>
            </div>
        </div>
    </div>
</nav>



<!-- MOBILE NAV OVERLAY -->
<div class="mobile-nav-backdrop"></div>
<div class="mobile-nav-overlay">
    <button class="mobile-nav-close" id="mobileNavClose" aria-label="Schließen" title="Schließen">
        <svg width="22" height="22" stroke="#14313c" stroke-width="2" fill="none"><line x1="6" y1="6" x2="16" y2="16"/><line x1="16" y1="6" x2="6" y2="16"/></svg>
    </button>
    <nav>
        <a href="<?= BASE_URL; ?>index.php" class="main-nav-link active">Startseite</a>
        <a href="<?= BASE_URL; ?>Heizöl-Rechner" class="main-nav-link">Heizöl-Rechner</a>
        <a href="<?= BASE_URL; ?>Über-uns" class="main-nav-link">Über uns</a>
        <a href="<?= BASE_URL; ?>Kontakt" class="main-nav-link">Kontakt</a>
		<hr />
        <a href="mailto:<?=$cur_Mail;?>" id="cleanLink"><?=$cur_Mail;?></a>
                <span style="margin-left:0rem;font-size:1.12rem;"><img src="<?=BASE_URL;?>assets/images/de.svg" alt="Deutschland" width="24" height="16" style=""> Deutschland</span>
    </nav>
</div>
