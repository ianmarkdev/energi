<?php
	
	include("white_config.inc.php");
	include("white_actions.inc.php");

	$this_Title = "Datenschutz";
	
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
			  <p>Vielen Dank für den Besuch auf unseren Webseiten. Wir nehmen Datenschutz sehr ernst und sind bestrebt im Rahmen unseres Webseitenangebotes Ihre personenbezogenen Daten zu schützen.</p>
  <p>Unter personenbezogenen Daten verstehen wir alle Daten über die persönlichen und sachlichen Verhältnisse einer natürlichen Person. Personenbezogene Daten, die auf unserer Webseite erhoben werden, werden ausschließlich für die eigenen Zwecke von <?=$cur_Title;?> verwendet. 
</p>
  <h2>Verantwortliche Stelle und Datenschutzkontakt</h2>
  <h5>Verantwortlich für die Datenverarbeitung im Sinne des Art. 4 Nr. 7 DS-GVO ist</h5>
  <h5><?=$cur_Firma;?></h5>
  <p><?=$cur_Strasse;?> <br><?=$cur_PLZ;?> <?=$cur_Ort;?> <br>E-Mail: <a rel="noopener noreferrer" href="mailto:<?=$cur_Mail;?>"><?=$cur_Mail;?></a>
  </p>
  <h5>Gesetzliche Vertreterin</h5>
  <p><?=$cur_BD_Sys_Name;?></p>
  <h5>Unseren Datenschutzbeauftragten erreichen Sie unter:</h5>
  <p><?=$cur_BD_Sys_Name;?> <br><?=$cur_Strasse;?> <br><?=$cur_PLZ;?> <?=$cur_Ort;?> <br>E-Mail: <a rel="noopener noreferrer" href="mailto:<?=$cur_Mail;?>"><?=$cur_Mail;?></a>
  </p>
  <h2>Rechtsgrundlagen der Datenverarbeitung</h2>
  <p>Einwilligung: Soweit wir für Verarbeitungsvorgänge personenbezogener Daten Ihre Einwilligung eingeholt wurde, ist Artikel 6 Absatz 1 lit. a EU-Datenschutzgrundverordnung (DS-GVO) die Rechtsgrundlage für die Datenverarbeitung. <br>Vertrag:&nbsp;Bei der Verarbeitung von personenbezogenen Daten, die zur Erfüllung eines Vertrages, dessen Vertragspartei Sie sind, ist Artikel 6 Absatz 1 lit. b DSGVO die Rechtsgrundlage. Dies gilt auch für Verarbeitungsvorgänge, die zur Durchführung vorvertraglicher Maßnahmen erforderlich sind. <br>Gesetzliche Pflicht:&nbsp;Soweit eine Verarbeitung personenbezogener Daten zur Erfüllung einer rechtlichen Verpflichtung erforderlich ist, denen unser Unternehmen unterliegt, dient Artikel 6 Absatz 1 lit. c DSGVO als Rechtsgrundlage. <br>Für den Fall, dass lebenswichtige Interessen von Ihnen oder einer anderen natürlichen Person eine Verarbeitung personenbezogener Daten erforderlich machen, ist Artikel 6 Absatz 1 lit. d DSGVO die Rechtsgrundlage. <br>Berechtigtes Interesse:&nbsp;Ist die Verarbeitung zur Wahrung eines berechtigten Interesses unseres Unternehmens oder eines Dritten erforderlich und überwiegen die Interessen, Grundrechte und Grundfreiheiten des Betroffenen das erstgenannte Interesse nicht, so dient Artikel 6 Absatz 1 lit. f DSGVO als Rechtsgrundlage für die Verarbeitung. Das berechtige Interesse unseres Unternehmens liegt in der Durchführung unserer Geschäftstätigkeit. </p>
  <h2>Betroffenenrechte</h2>
  <p>Im Rahmen unserer Datenverarbeitung werden personenbezogene Daten von Ihnen verarbeitet. Es stehen Ihnen die Rechte aus dem Dritten Kapitel der DS-GVO gegenüber unserem Unternehmen zu. <br>Wir beachten die Rechte auf Auskunft, Berichtigung, Einschränkung der Verarbeitung, Löschung oder Übertragbarkeit ihrer personenbezogenen Daten. Sie können diese Rechte wie folgt geltend machen: <br>Recht auf Auskunft <br>Sie haben das Recht von uns eine Bestätigung darüber zu verlangen, ob wir sie betreffende personenbezogene Daten verarbeiten. Ist dies der Fall, so haben Sie ein Recht auf Auskunft über diese personenbezogenen Daten und auf folgende Informationen: </p>
  <ul>
    <li>
      <p>a) die Verarbeitungszwecke;</p>
    </li>
    <li>
      <p>b) die Kategorien personenbezogener Daten, die verarbeitet werden;</p>
    </li>
    <li>
      <p>c) die Empfänger oder Kategorien von Empfängern, gegenüber denen die personenbezogenen Daten offengelegt worden sind oder noch offengelegt werden, insbesondere bei Empfängern in Drittländern oder bei internationalen Organisationen;</p>
    </li>
    <li>
      <p>d) falls möglich die geplante Dauer, für die die personenbezogenen Daten gespeichert werden, oder, falls dies nicht möglich ist, die Kriterien für die Festlegung dieser Dauer;</p>
    </li>
    <li>
      <p>e) das Bestehen eines Rechts auf Berichtigung oder Löschung der sie betreffenden personenbezogenen Daten oder auf Einschränkung der Verarbeitung durch den Verantwortlichen oder eines Widerspruchsrechts gegen diese Verarbeitung;</p>
    </li>
    <li>
      <p>f) das Bestehen eines Beschwerderechts bei einer Aufsichtsbehörde;</p>
    </li>
    <li>
      <p>g) wenn die personenbezogenen Daten nicht bei der betroffenen Person erhoben werden, alle verfügbaren Informationen über die Herkunft der Daten;</p>
    </li>
    <li>
      <p>h) das Bestehen einer automatisierten Entscheidungsfindung einschließlich Profiling gemäß Artikel 22 Absätze 1 und 4 und — zumindest in diesen Fällen — aussagekräftige Informationen über die involvierte Logik sowie die Tragweite und die angestrebten Auswirkungen einer derartigen Verarbeitung für die betroffene Person.</p>
    </li>
  </ul>
  <p>Werden personenbezogene Daten an ein Drittland oder an eine internationale Organisation übermittelt, so haben Sie das Recht, über die geeigneten Garantien gemäß Artikel 46 DS-GVO im Zusammenhang mit der Übermittlung unterrichtet zu werden. <br>Wir stellen Ihnen eine Kopie der personenbezogenen Daten, die Gegenstand der Verarbeitung sind, zur Verfügung. Für alle weiteren Kopien, die Sie beantragen, können wir ein angemessenes Entgelt auf der Grundlage der Verwaltungskosten verlangen. Stellen Sie den Antrag auf Auskunft elektronisch, so müssen wir die Informationen in einem gängigen elektronischen Format zur Verfügung zu stellen, sofern Sie nichts anderes angeben. <br>Das Recht auf Erhalt einer Kopie darf die Rechte und Freiheiten anderer Personen nicht beeinträchtigen. <br>Recht auf Berichtigung <br>Sie haben außerdem das Recht, unverzüglich die Berichtigung Sie betreffender unrichtiger personenbezogener Daten zu verlangen. Unter Berücksichtigung der Zwecke der Verarbeitung haben Sie das Recht, die Vervollständigung unvollständiger personenbezogener Daten — auch mittels einer ergänzenden Erklärung — zu verlangen. <br>Recht auf Löschung („Recht auf Vergessen werden“) <br>Sie haben auch das Recht, von uns zu verlangen, dass Sie betreffende personenbezogene Daten unverzüglich gelöscht werden, und wir sind verpflichtet, personenbezogene Daten unverzüglich zu löschen, sofern einer der folgenden Gründe zutrifft: </p>
  <ul>
    <li>
      <p>a) Die personenbezogenen Daten sind für die Zwecke, für die sie erhoben oder auf sonstige Weise verarbeitet wurden, nicht mehr notwendig.</p>
    </li>
    <li>
      <p>b) Sie widerrufen Ihre Einwilligung, auf die sich die Verarbeitung gemäß Artikel 6 I lit. a DS-GVO oder Artikel 9 Absatz 2 Buchstabe a stützte, und es fehlt an einer anderweitigen Rechtsgrundlage für die Verarbeitung.</p>
    </li>
    <li>
      <p>c) Sie legen gemäß Artikel 21 Absatz 1 Widerspruch gegen die Verarbeitung ein und es liegen keine vorrangigen berechtigten Gründe für die Verarbeitung vor, oder Sie legen gemäß Artikel 21 Absatz 2 Widerspruch gegen die Verarbeitung ein.</p>
    </li>
    <li>
      <p>d) Die personenbezogenen Daten wurden unrechtmäßig verarbeitet.</p>
    </li>
    <li>
      <p>e) Die Löschung der personenbezogenen Daten ist zur Erfüllung einer rechtlichen Verpflichtung nach dem Unionsrecht oder dem Recht der Mitgliedstaaten erforderlich, dem wir unterliegen.</p>
    </li>
    <li>
      <p>f) Die personenbezogenen Daten wurden in Bezug auf angebotene Dienste der Informationsgesellschaft gemäß Artikel 8 Absatz 1 erhoben.</p>
    </li>
  </ul>
  <p>(2)Haben wir die personenbezogenen Daten öffentlich gemacht und sind wir gemäß Absatz 1 zu deren Löschung verpflichtet, so treffen wir unter Berücksichtigung der verfügbaren Technologie und der Implementierungskosten angemessene Maßnahmen, auch technischer Art, um für die Datenverarbeitung Verantwortliche, die die personenbezogenen Daten verarbeiten, darüber zu informieren, dass Sie die Löschung aller Links zu diesen personenbezogenen Daten oder von Kopien oder Replikationen dieser personenbezogenen Daten verlangt haben. Dies gilt nicht, soweit die Verarbeitung erforderlich ist a) zur Ausübung des Rechts auf freie Meinungsäußerung und Information; b) zur Erfüllung einer rechtlichen Verpflichtung, die die Verarbeitung nach dem Recht der Union oder der Mitgliedstaaten, dem wir unterliegen erfordert, oder zur Wahrnehmung einer Aufgabe, die im öffentlichen Interesse liegt oder in Ausübung öffentlicher Gewalt erfolgt, die uns übertragen wurde; c) aus Gründen des öffentlichen Interesses im Bereich der öffentlichen Gesundheit gemäß Artikel 9 Absatz 2 Buchstaben h und i sowie Artikel 9 Absatz 3; d) für im öffentlichen Interesse liegende Archivzwecke, wissenschaftliche oder historische Forschungszwecke oder für statistische Zwecke gemäß Artikel 89 Absatz 1, soweit das in Absatz 1 genannte Recht voraussichtlich die Verwirklichung der Ziele dieser Verarbeitung unmöglich macht oder ernsthaft beeinträchtigt, oder e) zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen. <br>Recht auf Einschränkung der Verarbeitung <br>(1)Die betroffene Person hat das Recht, von dem Verantwortlichen die Einschränkung der Verarbeitung zu verlangen, wenn eine der folgenden Voraussetzungen gegeben ist: </p>
  <ul>
    <li>
      <p>a) die Richtigkeit der personenbezogenen Daten von der betroffenen Person bestritten wird, und zwar für eine Dauer, die es dem Verantwortlichen ermöglicht, die Richtigkeit der personenbezogenen Daten zu überprüfen</p>
    </li>
    <li>
      <p>b) die Verarbeitung unrechtmäßig ist und die betroffene Person die Löschung der personenbezogenen Daten ablehnt und stattdessen die Einschränkung der Nutzung der personenbezogenen Daten verlangt;</p>
    </li>
    <li>
      <p>c) der Verantwortliche die personenbezogenen Daten für die Zwecke der Verarbeitung nicht länger benötigt, die betroffene Person sie jedoch zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen benötigt, oder</p>
    </li>
    <li>
      <p>d) die betroffene Person Widerspruch gegen die Verarbeitung gemäß Artikel 21 Absatz 1 eingelegt hat, solange noch nicht feststeht, ob die berechtigten Gründe des Verantwortlichen gegenüber denen der betroffenen Person überwiegen.</p>
    </li>
  </ul>
  <p>(2) Wurde die Verarbeitung gemäß Absatz 1 eingeschränkt, so dürfen diese personenbezogenen Daten — von ihrer Speicherung abgesehen — nur mit Einwilligung der betroffenen Person oder zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen oder zum Schutz der Rechte einer anderen natürlichen oder juristischen Person oder aus Gründen eines wichtigen öffentlichen Interesses der Union oder eines Mitgliedstaats verarbeitet werden. 4.5.2016 L 119/44 Amtsblatt der Europäischen Union DE <br>(3) Eine betroffene Person, die eine Einschränkung der Verarbeitung gemäß Absatz 1 erwirkt hat, wird von dem Verantwortlichen unterrichtet, bevor die Einschränkung aufgehoben wird. <br>Artikel 19 Mitteilungspflicht im Zusammenhang mit der Berichtigung oder Löschung personenbezogener Daten oder der Einschränkung der Verarbeitung <br>Der Verantwortliche teilt allen Empfängern, denen personenbezogenen Daten offengelegt wurden, jede Berichtigung oder Löschung der personenbezogenen Daten oder eine Einschränkung der Verarbeitung nach Artikel 16, Artikel 17 Absatz 1 und Artikel 18 mit, es sei denn, dies erweist sich als unmöglich oder ist mit einem unverhältnismäßigen Aufwand verbunden. Der Verantwortliche unterrichtet die betroffene Person über diese Empfänger, wenn die betroffene Person dies verlangt. <br>Artikel 20 Recht auf Datenübertragbarkeit <br>(1)Die betroffene Person hat das Recht, die sie betreffenden personenbezogenen Daten, die sie einem Verantwortlichen bereitgestellt hat, in einem strukturierten, gängigen und maschinenlesbaren Format zu erhalten, und sie hat das Recht, diese Daten einem anderen Verantwortlichen ohne Behinderung durch den Verantwortlichen, dem die personenbezogenen Daten bereitgestellt wurden, zu übermitteln, sofern </p>
  <ul>
    <li>
      <p>a) die Verarbeitung auf einer Einwilligung gemäß Artikel 6 Absatz 1 Buchstabe a oder Artikel 9 Absatz 2 Buchstabe a oder auf einem Vertrag gemäß Artikel 6 Absatz 1 Buchstabe b beruht und</p>
    </li>
    <li>
      <p>b) die Verarbeitung mithilfe automatisierter Verfahren erfolgt. (2)Bei der Ausübung ihres Rechts auf Datenübertragbarkeit gemäß Absatz 1 hat die betroffene Person das Recht, zu erwirken, dass die personenbezogenen Daten direkt von einem Verantwortlichen einem anderen Verantwortlichen übermittelt werden, soweit dies technisch machbar ist.</p>
    </li>
  </ul>
  <p>(3) Die Ausübung des Rechts nach Absatz 1 des vorliegenden Artikels lässt Artikel 17 unberührt. Dieses Recht gilt nicht für eine Verarbeitung, die für die Wahrnehmung einer Aufgabe erforderlich ist, die im öffentlichen Interesse liegt oder in Ausübung öffentlicher Gewalt erfolgt, die dem Verantwortlichen übertragen wurde. <br>(4) Das Recht gemäß Absatz 2 darf die Rechte und Freiheiten anderer Personen nicht beeinträchtigen. <br>Sie haben außerdem das Recht unseren Datenschutzbeauftragten bzgl. der vorgenannten Rechte sowie zu allen mit der Verarbeitung ihrer personenbezogenen Daten im Zusammenhang stehenden Fragen, zu Rate zu ziehen. <br>Außerdem können unsere Kunden gegenüber den zuständigen Aufsichtsbehörden ihr Recht auf Beschwerde geltend machen. <br>Recht auf Widerspruch <br>Sie haben das Recht, aus Gründen, die sich aus ihrer besonderen Situation ergeben, jederzeit gegen die Verarbeitung sie betreffender personenbezogenen Daten, die aufgrund von Art. 6 Abs. 1 lit. e oder f erfolgt Widerspruch einzulegen; dies gilt auch für ein auf diese Bestimmung gestütztes Profiling. Der Verantwortliche verarbeitet die personenbezogenen Daten dann nicht mehr, es sei denn, er kann zwingende schutzwürdige Gründe für die Verarbeitung nachweisen, die die Interessen, Rechte und Freiheiten der ihrer Person überwiegen, oder die Verarbeitung dient der Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen. <br>2) Werden ihre personenbezogenen Daten verarbeitet, um Direktwerbung zu betreiben, so haben Sie das Recht, jederzeit Widerspruch gegen die Verarbeitung sie betreffender personenbezogener Daten zum Zwecke derartiger Werbung einzulegen; dies gilt auch für das Profiling, soweit es mit solcher Direktwerbung in Verbindung steht <br>3) Widersprechen Sie der Verarbeitung für Zwecke der Direktwerbung, so werden Ihre personenbezogenen Daten nicht mehr für die Zwecke verarbeitet. <br>4) im Zusammenhang mit der Nutzung von Diensten der Informationsgesellschaft können Sie ungeachtet der Richtlinie 2002/58/EG ihr Widerspruchsrecht mittels automatisierter Verfahren ausüben, bei denen technische Spezifikationen verwendet werden. <br>5) Sie haben das Recht, aus Gründen, die sich aus Ihrer besonderen Situation ergeben, gegen die sie betreffende Verarbeitung sie betreffender personenbezogener Daten, die zu wissenschaftlichen oder historischen Forschungszwecken oder zu statistischen Zwecken gem. Art. 89 Abs. 1 erfolgt, Widerspruch einzulegen, es sei denn, die Verarbeitung ist zur Erfüllung einer im öffentlichen Interesse liegenden Aufgabe erforderlich <br>Unbeschadet eines anderweitigen verwaltungsrechtlichen oder gerichtlichen Rechtsbehelfs steht Ihnen das Recht auf Beschwerde bei einer Aufsichtsbehörde wenn Sie der Ansicht sind, dass die Verarbeitung der Sie betreffenden personenbezogenen Daten gegen die DS-GVO verstößt. </p>
  <h2>Webserverlogs</h2>
  <p>Im Rahmen der Nutzung unseres Internetangebotes werden die Verbindungsinformationen in den Server-Log-Dateien gespeichert. <br>Zu diesen Informationen zählen: </p>
  <ul>
    <li>
      <p>IP-Adresse des aufrufenden Systems</p>
    </li>
    <li>
      <p>Browser Informationen wie verwendetes Betriebssystem und Bildschirmauflösung</p>
    </li>
    <li>
      <p>aufgerufene Webseite</p>
    </li>
    <li>
      <p>Ursprungswebseite</p>
    </li>
    <li>
      <p>Zeitpunkt des Aufrufs</p>
    </li>
  </ul>
  <p>Die Webserverprotokolle werden ausschließlich zu Sicherheitszwecken verarbeitet. <br>Wir verwenden die Protokolldaten nur für statistische Auswertungen zum Zweck des Betriebs, der Sicherheit und der Optimierung des Angebotes. Wir behalten uns jedoch vor, die Protokolldaten nachträglich zu überprüfen, wenn aufgrund konkreter Anhaltspunkte der berechtigte Verdacht einer rechtswidrigen Nutzung besteht. </p>
  <h2>Kontaktformular</h2>
  <p>Im Rahmen des Kontaktformulars haben Sie die Möglichkeit, beliebige Daten an uns zu senden. Die Daten werden von unserem Webserver per Mail an das E-Mail Postfach eines unserer Unternehmen weitergeleitet. Bitte beachten Sie, dass die Kommunikation über das Kontaktformular nicht verschlüsselt erfolgt. Bitte nutzten Sie für vertrauliche Kommunikation aus eigenem Interesse einen sicheren Kommunikationskanal.</p>
  <h2>Kommentarfunktion</h2>
  <p>Bei Nutzung der Kommentarfunktion wird Ihre IP-Adresse zum Zweck der Missbrauchskontrolle gespeichert.</p>
  <h2>Cookies</h2>
  <p>Unsere Webseiten verwendet Cookies. Cookies sind Textdateien, die auf Ihrem Endgerät gespeichert werden. Cookies können beim Aufruf der Webseite von der Webseite ausgelesen, übertragen und geändert werden. Wir nutzen Cookies nur mit zufälligen, pseudonymen Identifikationsnummern. Diese Identifikationsnummern werden genutzt um Ihr Nutzungsverhalten auf unseren Webseiten auszuwerten. Zu keinem Zeitpunkt wird das Nutzungsprofil dem Namen einer natürlichen Person zugeordnet. Wenn Sie spezielle Funktionen (wie z.B. den Warenkorb oder „angemeldet bleiben“) unserer Webseiten nutzen, werden Cookies zusätzlich für diese Funktionen verwendet. <br>Es ist jederzeit möglich der Setzung von Cookies durch entsprechende Änderung der Einstellung im Internetbrowser zu wiedersprechen. Gesetze Cookies können gelöscht werden. Es wird darauf hingewiesen, dass bei Deaktivierung von Cookies möglicherweise nicht alle Funktionen unserer Internetseite vollumfänglich genutzt werden können. <br>Die genauen Funktionen der Cookies können sie den detaillierteren Informationen dieser Datenschutzerklärung entnehmen. </p>
  <h2>Einsatz Flash / Evercookie Cookie zur Messung</h2>
  <p>Unsere Websiten nutzen Flash-Cookies. Diese werden nicht durch Ihren Browser erfasst, sondern durch Ihr Flash-Plug-in. Diese speichern die notwendigen Daten unabhängig von Ihrem verwendeten Browser und haben kein automatisches Ablaufdatum. Wenn Sie keine Verarbeitung der Flash-Cookies wünschen, müssen Sie ein entsprechendes Add-On installieren, z. B. „Better Privacy“ für Mozilla Firefox ( <a href="https://addons.mozilla.org/de/firefox/addon/betterprivacy/">https://addons.mozilla.org/de/firefox/addon/betterprivacy/</a>) oder Adobe-Flash-Killer-Cookie für Google Chrome.] </p>
  <h2>Google Analytics</h2>
  <p>Diese Website benutzt Google Analytics, einen Webanalysedienst der Google Inc. („Google“). Google Analytics verwendet sog. „Cookies“, Textdateien, die auf Ihrem Computer gespeichert werden und die eine Analyse der Benutzung der Website durch Sie ermöglichen. Die durch den Cookie erzeugten Informationen über Ihre Benutzung dieser Website werden in der Regel an einen Server von Google in den USA übertragen und dort gespeichert. Im Falle der Aktivierung der IP-Anonymisierung auf dieser Webseite wird Ihre IP-Adresse von Google jedoch innerhalb von Mitgliedstaaten der Europäischen Union oder in anderen Vertragsstaaten des Abkommens über den Europäischen Wirtschaftsraum zuvor gekürzt. Nur in Ausnahmefällen wird die volle IP-Adresse an einen Server von Google in den USA übertragen und dort gekürzt. Im Auftrag des Betreibers dieser Website wird Google diese Informationen benutzen, um Ihre Nutzung der Website auszuwerten, um Reports über die Websiteaktivitäten zusammenzustellen und um weitere mit der Websitenutzung und der Internetnutzung verbundene Dienstleistungen gegenüber dem Websitebetreiber zu erbringen. Die im Rahmen von Google Analytics von Ihrem Browser übermittelte IP-Adresse wird nicht mit anderen Daten von Google zusammengeführt. Sie können die Speicherung der Cookies durch eine entsprechende Einstellung Ihrer Browser-Software verhindern; wir weisen Sie jedoch darauf hin, dass Sie in diesem Fall gegebenenfalls nicht sämtliche Funktionen dieser Website vollumfänglich werden nutzen können. Sie können darüber hinaus die Erfassung der durch das Cookie erzeugten und auf Ihre Nutzung der Websiten bezogenen Daten (inkl. Ihrer IP-Adresse) an Google sowie die Verarbeitung dieser Daten durch Google verhindern, indem sie das unter dem folgenden Link verfügbare Browser-Plugin herunterladen und installieren&nbsp;tools.google.com/dlpage/gaoptout <br>Die Rechtsgrundlage für unsere Datenerhebung mit der Software Google Analytics ist Ihre Einwilligung gemäß Art. 6 Abs. 1 a) DS-GVO, die Sie jederzeit mit Wirkung für die Zukunft mit dem oben beschriebenen Verfahren widerrufen können. Ihre Daten werden nach 14 Monaten automatisch gelöscht. Die Löschung von Daten, deren Aufbewahrungsdauer erreicht ist, erfolgt automatisch einmal im Monat. Weitere Informationen zu Google Analytics und zum Datenschutz erhalten Sie auf der Webseite :&nbsp;policies.google.com/privacy </p>
  <h2>Google Adwords</h2>
  <p>Unsere Webseiten nutzen das Google AdWords Conversion Tracking. Dies ist ein Analysedienst der Google Inc., Amphitheater Parkway, Mountainview; California 94043, USA. Der Dienst setzt einen Cookie auf Ihrem Rechner, sofern Sie über eine Google Werbeanzeige zu unserer Seite gekommen sind. Wir nutzten das Cookie nicht, um Sie persönlich zu identifizieren. Es dient lediglich dazu, zu erkennen, ob ein Nutzer über eine von uns gekaufte Werbung auf unsere Webseiten gelangt ist. Es ist damit nachvollziehbar über welche Werbung Sie unsere Webseiten besucht haben und ob Sie unserer Webseiten danach noch einmal angesurft haben. Wir nutzen die Erkenntnisse aus dieser Analyse um unserer Werbung gezielter anpassen zu können. <br>Eine vollständige Übersicht der Datenverarbeitung können Sie der Datenschutzerklärung von Google entnehmen. Diese Informationen finden Sie unter: <a target="_blank" href="http://www.google.de/policies/privacy/">www.google.de/policies/privacy/</a>
  </p>
  <h2>Social Plug Ins</h2>
  <p>Wir setzen Social-Media-Plug-ins ein. Wir setzen dabei die sog. 2-Klick-Lösung ein. Das heißt, wenn Sie unsere Seite besuchen, werden zunächst grundsätzlich keine personenbezogenen Daten an die Anbieter dieser Plug-ins weitergegeben. Den Anbieter des Plug-ins erkennen Sie über die Markierung auf dem ausgegrauten Kasten anhand des Anfangsbuchstabens. Nur wenn Sie auf einen der Plug-ins klicken, werden personenbeziehbare Daten übermittelt: Durch die Aktivierung des Plug-ins werden Daten automatisiert an den jeweiligen Social Media Dienst übermittelt und dort (bei ausländischen Anbietern im jeweiligen Land) gespeichert. Wir haben weder Einfluss auf die erhobenen Daten und Datenverarbeitungsvorgänge, noch sind uns der volle Umfang der Datenerhebung, die Zwecke sowie die Speicherfristen bekannt. Da der Social Media Dienst die Datenerhebung insbesondere über Cookies vornimmt, empfehlen wir Ihnen, vor dem Klick auf den ausgegrauten Kasten über die Sicherheitseinstellungen Ihres Browsers alle Cookies zu löschen. <br>Wenn Sie ein Plug-in aktivieren, erhält der Socidal Media Dienst die Information, dass Sie die entsprechende Unterseite unserer Online-Angebote aufgerufen haben. Zudem werden unter anderem die IP Adresse, das Anbieter-Cookie, die Browsereinstellung und weitere Daten übermittelt. Dies erfolgt unabhängig davon, ob Sie ein Konto bei diesem Social Media Dienst besitzen und dort eingeloggt sind. Wenn Sie ein Konto bei dem Social Media Dienst besitzen bzw. wenn Sie bei dem Dienst eingeloggt sind, werden diese Daten direkt Ihrem Konto zugeordnet. Wenn Sie den aktivierten Button betätigen und z. B. die Seite verlinken, speichert der Social Media Dienst auch diese Information in Ihrem Nutzerkonto und teilt dies Ihren Kontakten öffentlich mit. Wenn Sie die Zuordnung mit Ihrem Profil bei dem Social Media Dienst nicht wünschen, müssen Sie sich vor Aktivierung des Buttons ausloggen. <br>Der Social Media Dienst speichert diese Daten als Nutzungsprofile und nutzt diese für Zwecke der Werbung, Marktforschung und/oder bedarfsgerechten Gestaltung seiner Website. Eine solche Auswertung erfolgt insbesondere (auch für nicht eingeloggte Nutzer) zur Darstellung von bedarfsgerechter Werbung und um andere Nutzer des sozialen Netzwerks über Ihre Aktivitäten auf unserer Website zu informieren. Ihnen steht ein Widerspruchsrecht zu gegen die Bildung dieser Nutzerprofile, wobei Sie sich zur Ausübung dessen an den jeweiligen Social Media Dienst wenden müssen. <br>Weitere Informationen zu Zweck und Umfang der Datenerhebung und ihrer Verarbeitung durch den Social Media Dienst erhalten Sie in den im Folgenden mitgeteilten Datenschutzerklärungen dieser Anbieter. Dort erhalten Sie auch weitere Informationen zu Ihren diesbezüglichen Rechten und Einstellungsmöglichkeiten zum Schutze Ihrer Privatsphäre. <br>Adressen der jeweiligen Anbieter und URL mit deren Datenschutzhinweisen: <br>Facebook Inc., 1601 S California Ave, Palo Alto, California 94304, USA; www.facebook.com/policy.php; weitere Informationen zur Datenerhebung: www.facebook.com/help/186325668085084, www.facebook.com/about/privacy/your-info-on-other applications sowie www.facebook.com/about/privacy/your-info everyoneinfo. <br>Google Inc., 1600 Amphitheater Parkway, Mountainview, California 94043, USA; www.google.com/policies/privacy/partners/. <br>Twitter, Inc., 1355 Market St, Suite 900, San Francisco, California 94103, USA; twitter.com/privacy. <br>Xing AG,&nbsp;Gänsemarkt 43, 20354 Hamburg, DE; www.xing.com/privacy. <br>LinkedIn Corporation, 2029 Stierlin Court, Mountain View, California 94043, USA; www.linkedin.com/legal/privacy-policy. </p>
  <h2>Google Maps</h2>
  <p>Wir binden auf den Unterseiten zur besseren Veranschaulichung eine Karte ein, die von Servern der Google Inc. bereitgestellt wird. Wenn Sie sich die Karte anzeigen lassen, wird eine Verbindung zum Google Server hergestellt, bei der unter anderem die IP-Adresse an Google übertragen wird. Weiterhin besteht für Google die Möglichkeit, Cookies zu schreiben und zu lesen. Bei diesen Cookies kann es sich um Google Nutzer Cookies handeln, die direkt mit Ihrer Person verbunden sind. Weitere Informationen zu Google Maps können Sie den Datenschutzbedingungen und den Nutzungsbedingungen von Google unter&nbsp;policies.google.com/privacy&nbsp;entnehmen.</p>
  <h2>Login Bereich</h2>
  <p>Soweit Daten im Rahmen eines Nutzerlogins erfasst werden, werden diese nur zur Bereitstellung des jeweiligen Services genutzt. Eine Auswertung findet nur zur Sicherstellung eines komfortablen und sicheren Betriebs des Systems statt.</p>
  <h2>Shop</h2>
  <p>Im Rahmen unserer Webseite haben Sie die Möglichkeit, Einkäufe in einem Warenkorb zwischen zu speichern. Für den Zeitraum der Nutzungssitzung wird Ihre Produktauswahl in einem sogenannten Session Cookie gespeichert. Nach dem Schließen der Webseite in Ihrem Browser wird dieser Cookie automatisch gelöscht.</p>
  <h2>Newsletter</h2>
  <p>Soweit Sie Ihre Emailadresse zur Zusendung eines Newsletters eingegeben haben, nutzen wir die Daten nur zur Zusendung von Informationen entsprechend der Newsletteranmeldung. <br>Mit der Anmeldung zum Newsletter speichern wir Ihre IP-Adresse und das Datum der Anmeldung. Diese Speicherung dient alleine dem Nachweis im Fall, dass ein Dritter eine Emailadresse missbraucht und sich ohne Wissen des Berechtigten für den Newsletterempfang anmeldet. <br>Sie können den Newsletter jederzeit über <?=$cur_Mail;?> abbestellen. </p>
  <h2>Online Schrift / Icon Bibliotheken</h2>
  <p>Auf dieser Webseite kommen Adobe Typekit Webfonts zum Einsatz. Typekit ist ein Dienst, der von der Firma Adobe angeboten wird. Dieser Dienst stellt Schriftarten zur Verfügung, die im Webbrowser des Nutzers nach einem Serveraufruf bei Adobe (in den USA) dargestellt werden. Hierbei wird zumindest die IP-Adresse des Browsers des Endgerätes des Nutzers dieser Webseite von Adobe gespeichert. Nähere Informationen finden Sie in den Datenschutzhinweisen von Typekit, die Sie unter&nbsp;www.adobe.com/privacy/policies/typekit.html&nbsp;abrufen können.</p>

  <h2>Trusted Shops</h2>
  <p>Zur Anzeige unseres Trusted Shops Gütesiegels und der gegebenenfalls gesammelten Bewertungen sowie zum Angebot der Trusted Shops Produkte für Käufer nach einer Bestellung ist auf dieser Webseite das Trusted Shops Trustbadge eingebunden. <br>Dies dient der Wahrung unserer im Rahmen einer Interessensabwägung überwiegenden berechtigten Interessen an einer optimalen Vermarktung unseres Angebots gemäß Art. 6 Abs. 1 S. 1 lit. f DSGVO. Das Trustbadge und die damit beworbenen Dienste sind ein Angebot der Trusted Shops GmbH, Subbelrather Str. 15C, 50823 Köln. <br>Bei dem Aufruf des Trustbadge speichert der Webserver automatisch ein sogenanntes Server-Logfile, das z.B. Ihre IP-Adresse, Datum und Uhrzeit des Abrufs, übertragene Datenmenge und den anfragenden Provider (Zugriffsdaten) enthält und den Abruf dokumentiert. Diese Zugriffsdaten werden nicht ausgewertet und spätestens sieben Tagen nach Ende Ihres Seitenbesuchs automatisch überschrieben. <br>Weitere personenbezogene Daten werden lediglich an Trusted Shops übertragen, soweit Sie hierzu eingewilligt haben, sich nach Abschluss einer Bestellung für die Nutzung von Trusted Shops Produkten entscheiden oder sich bereits für die Nutzung registriert haben. In diesem Fall gilt die zwischen Ihnen und Trusted Shops getroffene vertragliche Vereinbarung. </p>
  
  <!-- end content -->
			
			
        </div>
	
      </div>
    </div>
  </section>

<?php

	include("white_footer.tpl.php");
	
?>