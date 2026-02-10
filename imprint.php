<?php
	
	include("white_config.inc.php");
	include("white_actions.inc.php");

	$this_Title = "Impressum";
	
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
        <a href="<?= BASE_URL; ?>" class="breadcrumb-link"><?=$cur_Title;?></a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= $this_Title; ?></span>
      </nav>
    </div>
  </div>
</section>

  <section class="contact-section">
    <div class="container">
      <div class="row g-4">

        <div class="col-md-12">
          <div class="contact-card contact-info">
		  
            <!-- start content -->
			<h2><?=$this_Title;?></h2>
			      <p>Herausgeber:</p>
      <p><?=$cur_Firma;?> <br><?=$cur_Strasse;?> <br><?=$cur_PLZ;?> <?=$cur_Ort;?> </p>
      <p>E-Mail: <?=$cur_Mail;?></p>
      <p>Registergericht <?=$cur_Gericht;?> <?=$cur_Nummer;?> </p>
      <p>Vertretungsberechtigte Geschäftsführer: <br><?=$cur_BD_Sys_Name;?> <br><br>USt-Identifikationsnummer gemäß § 27a Umsatzsteuergesetz: <br><?=$cur_Steuernummer;?> </p>
      <p>Inhaltlich Verantwortlich gemäß §§ 5 TMG, 18&nbsp;MStV&nbsp;i.V.m. § 25 TTDSG;&nbsp;</p>
      <p>Plattform der EU-Kommission zur Online-Streitbeilegung:&nbsp;&nbsp; <a rel="noopener noreferrer" target="_blank" href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home.chooseLanguage">https://ec.europa.eu/consumers/odr</a>
      </p>
      <p>Hinweis gemäß Verbraucherstreitbeilegungsgesetz (VSBG) <br>Wir sind nicht bereit und verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen. </p>
      <p>
      </p>
      <h2>Haftungsausschluss</h2>
      <h3>1. Inhalt des Onlineangebotes</h3>
      <p>Die <?=$cur_Firma;?> übernimmt keinerlei Gewähr für die Aktualität, Korrektheit, Vollständigkeit oder Qualität der bereitgestellten Informationen. Haftungsansprüche gegen die <?=$cur_Firma;?>, welche sich auf Schäden materieller oder ideeller Art beziehen, die durch die Nutzung oder Nichtnutzung der dargebotenen Informationen bzw. durch die Nutzung fehlerhafter und unvollständiger Informationen verursacht wurden, sind grundsätzlich ausgeschlossen, sofern seitens der <?=$cur_Firma;?> kein nachweislich vorsätzliches oder grob fahrlässiges Verschulden vorliegt. Alle Angebote sind freibleibend und unverbindlich. Die <?=$cur_Firma;?> behält es sich ausdrücklich vor, Teile der Seiten oder das gesamte Angebot ohne gesonderte Ankündigung zu verändern, zu ergänzen, zu löschen oder die Veröffentlichung zeitweise oder endgültig einzustellen.</p>
      <h3>2. Verweise und Links</h3>
      <p>Bei direkten oder indirekten Verweisen auf fremde Internetseiten („Links“), die außerhalb des Verantwortungsbereiches der <?=$cur_Firma;?> liegen, würde eine Haftungsverpflichtung ausschließlich in dem Fall in Kraft treten, in dem die <?=$cur_Firma;?> von den Inhalten Kenntnis hat und es ihm technisch möglich und zumutbar wäre, die Nutzung im Falle rechtswidriger Inhalte zu verhindern. Die <?=$cur_Firma;?> erklärt hiermit ausdrücklich, dass zum Zeitpunkt der Linksetzung keine illegalen Inhalte auf den zu verlinkenden Seiten erkennbar waren. Auf die aktuelle und zukünftige Gestaltung, die Inhalte oder die Urheberschaft der gelinkten/verknüpften Seiten hat die <?=$cur_Firma;?> keinerlei Einfluss. Deshalb distanziert er sich hiermit ausdrücklich von allen Inhalten aller gelinkten /verknüpften Seiten, die nach der Linksetzung verändert wurden. Diese Feststellung gilt für alle innerhalb des eigenen Internetangebotes gesetzten Links und Verweise sowie für Fremdeinträge in von der <?=$cur_Firma;?> eingerichteten Gästebüchern, Diskussionsforen und Mailinglisten. Für illegale, fehlerhafte oder unvollständige Inhalte und insbesondere für Schäden, die aus der Nutzung oder Nichtnutzung solcherart dargebotener Informationen entstehen, haftet allein der Anbieter der Seite, auf welche verwiesen wurde, nicht derjenige, der über Links auf die jeweilige Veröffentlichung lediglich verweist.</p>
      <h3>3. Urheber- und Kennzeichenrecht</h3>
      <p>Die <?=$cur_Firma;?> ist bestrebt, in allen Publikationen die Urheberrechte der verwendeten Grafiken, Tondokumente, Videosequenzen und Texte zu beachten, von ihr selbst erstellte Grafiken, Tondokumente, Videosequenzen und Texte zu nutzen oder auf lizenzfreie Grafiken, Tondokumente, Videosequenzen und Texte zurückzugreifen. Alle innerhalb des Internetangebotes genannten und ggf. durch Dritte geschützten Marken- und Warenzeichen unterliegen uneingeschränkt den Bestimmungen des jeweils gültigen Kennzeichenrechts und den Besitzrechten der jeweiligen eingetragenen Eigentümer. Allein aufgrund der bloßen Nennung ist nicht der Schluß zu ziehen, dass Markenzeichen nicht durch Rechte Dritter geschützt sind! Das Copyright für veröffentlichte, von der <?=$cur_Firma;?> selbst erstellte Objekte bleibt allein bei der <?=$cur_Firma;?>. Eine Vervielfältigung oder Verwendung solcher Grafiken, Tondokumente, Videosequenzen und Texte in anderen elektronischen oder gedruckten Publikationen ist ohne ausdrückliche Zustimmung der <?=$cur_Firma;?> nicht gestattet.</p>
      <h3>4. Rechtswirksamkeit dieses Haftungsausschlusses</h3>
      <p>Dieser Haftungsausschluss ist als Teil des Internetangebotes von der <?=$cur_Firma;?> zu betrachten. Sofern Teile oder einzelne Formulierungen dieses Textes der geltenden Rechtslage nicht, nicht mehr oder nicht vollständig entsprechen sollten, bleiben die übrigen Teile des Dokumentes in ihrem Inhalt und ihrer Gültigkeit davon unberührt. <br>Wenn in dieser Homepage von Kunden, Vorgesetzten, Kollegen, Mitarbeitern o.ä. gesprochen wird, so gelten die Aussagen gleichermaßen für weibliche und männliche Personen. </p>
			<!-- end content -->
			
			
        </div>
	
      </div>
    </div>
  </section>

<?php

	include("white_footer.tpl.php");
	
?>