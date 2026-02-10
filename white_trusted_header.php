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
	
	<meta name="description" content="Jetzt Heizöl online kaufen zum besten Preis – mit Lieferung, Rechnung & persönlichem Service. Einfach Postleitzahl & Menge eingeben – sofort Preis berechnen.">
	
	<meta property="og:title" content="Heizöl günstig bestellen – <?=$cur_Title;?>">
	<meta property="og:description" content="Heizöl online kalkulieren und bestellen – fair & zuverlässig. Jetzt Preis sichern.">
	<meta property="og:image" content="<?=$cur_LogoDark;?>">
	<meta property="og:url" content="<?=$cur_Domain;?>">
	<meta property="og:type" content="website">

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?=$cur_Title;?> – Heizöl online">
	<meta name="twitter:description" content="Schnell & günstig Heizöl bestellen mit <?=$cur_Title;?>.">
	<meta name="twitter:image" content="<?=$cur_LogoDark;?>">

	<link rel="canonical" href="<?=$cur_Domain . $_SERVER['REQUEST_URI']?>">
	
	
	<link rel="icon" type="image/png" href="<?= BASE_URL; ?>assets/images/favicon/favicon-96x96.png" sizes="96x96" />
	<!--
	<link rel="icon" type="image/svg+xml" href="<?= BASE_URL; ?>assets/images/favicon/favicon.svg" />
	-->
	<link rel="shortcut icon" href="<?= BASE_URL; ?>assets/images/trusted-favicon.ico" />
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

	
	<script id="usercentrics-cmp" src="https://web.cmp.usercentrics.eu/ui/loader.js" data-settings-id="ShUBcwBfUDZaVQ" async></script>

	<!--
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	-->
	
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	
	
	<link rel="preload" href="<?= BASE_URL; ?>assets/css/addition-v1.css.php" as="style">
	<link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/addition-v1.css.php">


	<link rel="preload" href="<?= BASE_URL; ?>assets/css/special-v1.css.php" as="style">
	<link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/special-v1.css.php">

	<!--
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
	-->
	<link href="assets/css/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/trusted-shops.css?v=1.0.3">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<?php
	
		if($is_File_Name == "index.php") { 
		
	?>
	<link rel="preload" as="image" href="<?=BASE_URL;?>assets/images/bg-1.webp">
	
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