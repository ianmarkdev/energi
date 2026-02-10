<div id="trustedPopup" style="display: none; position: fixed; inset: 0px; background: rgba(0, 0, 0, 0.6); z-index: 10000; align-items: center; justify-content: center;">
  <!-- Mobile Close Button -->
  <button onclick="closeTrustedPopup()" class="mobile-close-btn" style="display: none; position: absolute; top: 12px; right: 12px; width: 40px; height: 40px; border-radius: 50%; background: #dc2626; border: 2px solid #fff; color: white; font-size: 24px; font-weight: bold; cursor: pointer; z-index: 10001; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">×</button>
  <div id="browserWidget" style="background: #ffffff; border-radius: 12px; max-width: 420px; width: 95%; max-height: 90vh; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); overflow: hidden; display: flex; flex-direction: column; font-family: -apple-system, BlinkMacSystemFont, &#39;Segoe UI&#39;, Roboto, sans-serif;" data-browser="chrome">

    <!-- Safari Header -->
    <div id="safariHeader" class="browser-header" style="background: linear-gradient(rgb(232, 232, 232), rgb(212, 212, 212)); padding: 10px 12px; border-bottom: 1px solid rgb(184, 184, 184); display: none; align-items: center; gap: 8px;">
      <div class="browser-controls" style="display: flex; gap: 8px;">
        <button onclick="closeTrustedPopup()" class="browser-close" style="width: 12px; height: 12px; border-radius: 50%; background: #ff5f57; border: none; cursor: pointer;" title="Schließen"></button>
        <div class="browser-minimize" style="width: 12px; height: 12px; border-radius: 50%; background: #febc2e;"></div>
        <div class="browser-maximize" style="width: 12px; height: 12px; border-radius: 50%; background: #28c840;"></div>
      </div>
      <div class="browser-url-bar" style="flex: 1; background: white; border-radius: 8px; padding: 6px 12px; display: flex; align-items: center; gap: 8px; border: 1px solid #c0c0c0; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
        <i class="fas fa-lock" style="color: #22c55e; font-size: 11px;"></i>
        <span style="font-size: 12px; color: #374151; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">www.trustedshops.de/bewertung/info_X8A2B3C4D5E6F7G8H.html</span>
      </div>
      <i class="fas fa-redo" style="color: #6b7280; font-size: 12px; cursor: pointer;" onclick="reloadBrowserContent()"></i>
    </div>

    <!-- Chrome Header -->
    <div id="chromeHeader" class="browser-header" style="display: flex; background: linear-gradient(rgb(222, 225, 230), rgb(194, 197, 202)); padding: 6px 8px 0px; flex-direction: column;">
      <div class="browser-tab-bar" style="display: flex; align-items: flex-end;">
        <div class="browser-tab" style="background: #f1f3f4; padding: 8px 12px; border-radius: 8px 8px 0 0; font-size: 12px; color: #5f6368; display: flex; align-items: center; gap: 8px;">
          <img src="./Online Heizöl zum Sparpreis - Ihr Partner für Wärme._files/trust.png" alt="" style="width: 14px; height: 14px;">
          <span style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Trusted Shops</span>
          <span onclick="closeTrustedPopup()" style="width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; color: #5f6368;">×</span>
        </div>
      </div>
      <div class="browser-url-section" style="background: #f1f3f4; padding: 6px 0; margin: 0 -8px; padding-left: 12px; padding-right: 12px;">
        <div class="browser-url-bar" style="background: white; border-radius: 20px; padding: 6px 12px; display: flex; align-items: center; gap: 8px; border: 1px solid #dfe1e5;">
          <i class="fas fa-lock" style="color: #22c55e; font-size: 11px;"></i>
          <span style="font-size: 12px; color: #374151; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">www.trustedshops.de/bewertung/info_X8A2B3C4D5E6F7G8H.html</span>
          <i class="fas fa-redo" style="color: #6b7280; font-size: 12px; cursor: pointer;" onclick="reloadBrowserContent()"></i>
        </div>
      </div>
    </div>

    <div id="browserLoadingBar" style="height: 3px; background: rgb(229, 231, 235); position: relative; display: none;">
      <div class="browser-loading-bar" style="height: 100%; background: linear-gradient(to right, rgb(34, 197, 94), rgb(22, 163, 74)); position: absolute; left: 0px; top: 0px; animation: 0.8s ease-out 0s 1 normal forwards running browserLoad;"></div>
    </div>

    <div id="browserContent" class="browser-content-fade" style="overflow-y: auto; flex: 1 1 0%; opacity: 0;">
      <div style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb; background: linear-gradient(to bottom, #fafafa, #ffffff);">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 52px; height: 52px; border: 1px solid #e5e7eb; border-radius: 6px; padding: 3px; display: flex; align-items: center; justify-content: center; background: white; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <img src="<?=BASE_URL;?><?=$cur_LogoDark;?>" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
          </div>
          <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
              <span style="font-size: 17px; font-weight: 600; color: #1f2937;">Gütesiegel</span>
              <span style="background: #22c55e; color: white; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.3px;">gültig</span>
            </div>
            <div style="font-size: 12px; color: #6b7280; margin-top: 3px;">mit Käuferschutz</div>
            <a href="<?= $cur_Domain ?>" target="_blank" style="font-size: 12px; color: #2563eb; margin-top: 4px; font-weight: 500; text-decoration: none; display: flex; align-items: center; gap: 4px;">
              <i class="fas fa-external-link-alt" style="font-size: 9px;"></i>
              <?= $site_name ?>
            </a>
          </div>
        </div>
      </div>

      <div style="padding: 16px 24px; border-bottom: 1px solid #f3f4f6;">
        <div style="display: flex; align-items: center; gap: 8px; color: #6b7280; font-size: 13px;">
          <i class="fas fa-calendar-alt"></i>
          <span>Zertifiziert seit: 08.10.2010</span>
        </div>
      </div>

      <div style="padding: 20px 24px; border-bottom: 1px solid #f3f4f6;">
        <p style="font-size: 14px; color: #374151; line-height: 1.6; margin: 0;">
          Das Trusted Shops Gütesiegel zeichnet vertrauenswürdige Onlinehändler aus.
        </p>
        <p style="font-size: 14px; color: #374151; line-height: 1.6; margin: 16px 0 0 0;">
          <?= $site_name ?> erfüllt unter anderem folgende <span style="color: #2563eb;">Trusted Shops Qualitätskriterien</span>:
        </p>
      </div>

      <div style="padding: 20px 24px; border-bottom: 1px solid #f3f4f6;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
          <div style="width: 24px; height: 24px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-check" style="color: white; font-size: 12px;"></i>
          </div>
          <span style="font-size: 14px; color: #374151;">Geprüfte Identität</span>
        </div>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
          <div style="width: 24px; height: 24px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-check" style="color: white; font-size: 12px;"></i>
          </div>
          <span style="font-size: 14px; color: #374151;">Verschlüsselte Datenübertragung</span>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 24px; height: 24px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-check" style="color: white; font-size: 12px;"></i>
          </div>
          <span style="font-size: 14px; color: #374151;">Verständlicher Bestellvorgang</span>
        </div>
      </div>

      <div style="padding: 20px 24px; border-bottom: 1px solid #f3f4f6;">
        <div style="display: flex; align-items: flex-start; gap: 12px;">
          <div style="width: 24px; height: 24px; background: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
            <i class="fas fa-check" style="color: white; font-size: 12px;"></i>
          </div>
          <div>
            <div style="font-size: 15px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">Trusted Shops Käuferschutz</div>
            <p style="font-size: 14px; color: #374151; line-height: 1.5; margin: 0;">
              Dieses Unternehmen bietet den Trusted Shops Käuferschutz für Einkäufe bis 20.000 € an.
            </p>
            <a href="https://www.trustedshops.de/kaeuferschutz/" target="_blank" style="font-size: 14px; color: #2563eb; cursor: pointer; display: inline-block; margin-top: 8px; text-decoration: none;">So ist dein Einkauf abgesichert. <i class="fas fa-external-link-alt" style="font-size: 10px;"></i></a>
          </div>
        </div>
      </div>

      <div style="padding: 20px 24px; background: #f9fafb;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
          <div style="display: flex; align-items: center; gap: 4px;">
            <i class="fas fa-star" style="color: #f59e0b; font-size: 16px;"></i>
            <i class="fas fa-star" style="color: #f59e0b; font-size: 16px;"></i>
            <i class="fas fa-star" style="color: #f59e0b; font-size: 16px;"></i>
            <i class="fas fa-star" style="color: #f59e0b; font-size: 16px;"></i>
            <i class="fas fa-star-half-alt" style="color: #f59e0b; font-size: 16px;"></i>
          </div>
          <div>
            <span data-ts-rating="true" style="font-size: 18px; font-weight: 700; color: #1f2937;">4,75</span>
            <span data-ts-rating-text="true" style="font-size: 14px; color: #22c55e; font-weight: 500; margin-left: 4px;">Sehr gut</span>
          </div>
        </div>
        <div style="font-size: 13px; color: #6b7280;"><span data-ts-reviews-12m="true">89</span> Bewertungen in den letzten 12 Monaten</div>
        <div style="font-size: 13px; color: #6b7280;"><span data-ts-reviews-total="true">1.124</span> Bewertungen insgesamt</div>

        <div style="margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 16px;">
          <div style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 12px;">Aktuelle Bewertungen</div>

          <div id="reviewsContainer">
            <div class="review-item" style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb;">
              <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 6px;">
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <span style="font-size: 11px; color: #9ca3af; margin-left: 6px;">Verifiziert</span>
              </div>
              <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.4;">Lieferung schneller als erwartet. Kontakt freundlich und kompetent. Top Service!</p>
              <div style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Frank K. - 28.09.2025</div>
            </div>

            <div class="review-item" style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb;">
              <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 6px;">
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <span style="font-size: 11px; color: #9ca3af; margin-left: 6px;">Verifiziert</span>
              </div>
              <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.4;">Sehr zufrieden mit Abwicklung und Lieferung. Immer wieder gerne!</p>
              <div style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Thomas W. - 17.09.2025</div>
            </div>

            <div class="review-item" style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb;">
              <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 6px;">
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <span style="font-size: 11px; color: #9ca3af; margin-left: 6px;">Verifiziert</span>
              </div>
              <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.4;">Ölpreisinformation per Mail sehr praktisch. Bestellung verlief reibungslos.</p>
              <div style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Leonhard K. - 29.07.2025</div>
            </div>

            <div class="review-item review-fade-in" style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb;">
              <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 6px;">
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <span style="font-size: 11px; color: #9ca3af; margin-left: 6px;">Verifiziert</span>
              </div>
              <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.4;">Alles bestens gelaufen. Fahrzeug war pünktlich da, Fahrer sehr freundlich.</p>
              <div style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Michael K. - 17.12.2025</div>
            </div>

            <div class="review-item review-fade-in" style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb;">
              <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 6px;">
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <span style="font-size: 11px; color: #9ca3af; margin-left: 6px;">Verifiziert</span>
              </div>
              <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.4;">Optimale Bestellung und Abwicklung. Laufende Preisaktualisierung sehr nützlich.</p>
              <div style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Sabine H. - 11.11.2025</div>
            </div>

            <div class="review-item review-fade-in" style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb;">
              <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 6px;">
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <span style="font-size: 11px; color: #9ca3af; margin-left: 6px;">Verifiziert</span>
              </div>
              <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.4;">Jederzeit wieder! Freundlich, kompetent und ehrlich. Klare Empfehlung!</p>
              <div style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Peter S. - 21.12.2025</div>
            </div>

            <div class="review-item review-fade-in" style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb;">
              <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 6px;">
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <span style="font-size: 11px; color: #9ca3af; margin-left: 6px;">Verifiziert</span>
              </div>
              <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.4;">Öl wurde frühzeitig geliefert. Mitarbeiterin am Telefon war sehr hilfsbereit.</p>
              <div style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Ilse D. - 02.12.2025</div>
            </div>

            <div class="review-item review-fade-in" style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb;">
              <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 6px;">
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <span style="font-size: 11px; color: #9ca3af; margin-left: 6px;">Verifiziert</span>
              </div>
              <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.4;">Heizölbestellung schnell und zuverlässig. Verlief absolut reibungslos.</p>
              <div style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Willi O. - 21.12.2025</div>
            </div>

            <div class="review-item review-fade-in" style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb;">
              <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 6px;">
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                <span style="font-size: 11px; color: #9ca3af; margin-left: 6px;">Verifiziert</span>
              </div>
              <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.4;">Ratenzahlung ist total klasse. Hilft ungemein bei der Finanzplanung.</p>
              <div style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Manuela K. - 09.11.2025</div>
            </div>
          </div>

          <div id="loadMoreSpinner" style="display: none; text-align: center; padding: 16px;">
            <div class="load-more-spinner"></div>
            <div style="font-size: 12px; color: #6b7280; margin-top: 8px;">Weitere Bewertungen werden geladen...</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Widget Trigger (fixed corner button) with close button -->
<div id="tsWidgetTrigger" class="ts-widget-container" style="cursor: pointer; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1); touch-action: manipulation; position: fixed;">
  <button id="tsCloseBtn" onclick="event.stopPropagation(); closeTrustedWidget();" style="position: absolute; top: -8px; right: -8px; width: 22px; height: 22px; background: #374151; border: 2px solid white; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; color: white; font-weight: bold; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">×</button>
  <div class="ts-widget" onclick="openTrustedPopup();" style="pointer-events: auto;">
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; pointer-events: none;">
      <img src="assets/images/trusted-shops-seal.png" alt="Trusted Shops" class="ts-seal-img" style="object-fit: contain; pointer-events: none;">
      <div class="ts-label" style="font-weight: 600; color: #1f2937; text-align: center; pointer-events: none;">Käuferschutz</div>
      <div class="ts-stars" style="display: flex; gap: 2px; pointer-events: none;">
        <i class="fas fa-star" style="color: #f59e0b;"></i>
        <i class="fas fa-star" style="color: #f59e0b;"></i>
        <i class="fas fa-star" style="color: #f59e0b;"></i>
        <i class="fas fa-star" style="color: #f59e0b;"></i>
        <i class="fas fa-star" style="color: #f59e0b;"></i>
      </div>
      <div style="width: 80%; height: 1px; background-color: #d1d5db; margin: 4px 0; pointer-events: none;"></div>
      <div class="ts-rating" data-ts-widget-rating="true" style="font-weight: 700; color: #1f2937; pointer-events: none;">4,75</div>
      <div class="ts-status" data-ts-widget-rating-text="true" style="color: #1f2937; font-weight: 600; pointer-events: none;">Sehr gut</div>
      <div class="ts-reviews-count" style="font-size: 10px; color: #6b7280; margin-top: 2px; pointer-events: none;"><span data-ts-reviews-total="true">1.124</span> Bewertungen</div>
    </div>
  </div>
</div>
<script>
  function closeTrustedWidget() {
    document.getElementById('tsWidgetTrigger').style.display = 'none';
    sessionStorage.setItem('ts_widget_closed', 'true');
  }
  if (sessionStorage.getItem('ts_widget_closed') === 'true') {
    document.addEventListener('DOMContentLoaded', function() {
      var widget = document.getElementById('tsWidgetTrigger');
      if (widget) widget.style.display = 'none';
    });
  }
  // Infinite Scroll - Additional Reviews Data
  const additionalReviews = [{
      name: 'Michael K.',
      date: '17.12.2025',
      text: 'Alles bestens gelaufen. Fahrzeug war pünktlich da, Fahrer sehr freundlich.'
    },
    {
      name: 'Sabine H.',
      date: '11.11.2025',
      text: 'Optimale Bestellung und Abwicklung. Laufende Preisaktualisierung sehr nützlich.'
    },
    {
      name: 'Peter S.',
      date: '21.12.2025',
      text: 'Jederzeit wieder! Freundlich, kompetent und ehrlich. Klare Empfehlung!'
    },
    {
      name: 'Ilse D.',
      date: '02.12.2025',
      text: 'Öl wurde frühzeitig geliefert. Mitarbeiterin am Telefon war sehr hilfsbereit.'
    },
    {
      name: 'Willi O.',
      date: '21.12.2025',
      text: 'Heizölbestellung schnell und zuverlässig. Verlief absolut reibungslos.'
    },
    {
      name: 'Manuela K.',
      date: '09.11.2025',
      text: 'Ratenzahlung ist total klasse. Hilft ungemein bei der Finanzplanung.'
    },
    {
      name: 'Klaus N.',
      date: '14.12.2025',
      text: 'Öl wurde schneller als gedacht geliefert. Alles war perfekt!'
    },
    {
      name: 'Helga B.',
      date: '05.10.2025',
      text: 'Schnelle Lieferung, fairer Preis. Besser geht es nicht.'
    },
    {
      name: 'Rainer M.',
      date: '28.08.2025',
      text: 'Seit Jahren Stammkunde. Service ist immer top!'
    },
    {
      name: 'Gisela F.',
      date: '15.07.2025',
      text: 'Unkomplizierte Bestellung, schnelle Lieferung. Sehr empfehlenswert.'
    }
  ];
  let reviewIndex = 0;
  let isLoadingReviews = false;

  function createReviewHTML(review) {
    return `
            <div class="review-item review-fade-in" style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 8px; border: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center; gap: 2px; margin-bottom: 6px;">
                    <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                    <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                    <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                    <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                    <i class="fas fa-star" style="color: #f59e0b; font-size: 10px;"></i>
                    <span style="font-size: 11px; color: #9ca3af; margin-left: 6px;">Verifiziert</span>
                </div>
                <p style="font-size: 13px; color: #374151; margin: 0; line-height: 1.4;">${review.text}</p>
                <div style="font-size: 11px; color: #9ca3af; margin-top: 6px;">${review.name} - ${review.date}</div>
            </div>
        `;
  }

  function loadMoreReviews() {
    if (isLoadingReviews || reviewIndex >= additionalReviews.length) return;

    isLoadingReviews = true;
    const spinner = document.getElementById('loadMoreSpinner');
    const container = document.getElementById('reviewsContainer');

    spinner.style.display = 'block';

    setTimeout(function() {
      const reviewsToAdd = additionalReviews.slice(reviewIndex, reviewIndex + 3);
      reviewsToAdd.forEach(function(review) {
        container.insertAdjacentHTML('beforeend', createReviewHTML(review));
      });
      reviewIndex += 3;
      spinner.style.display = 'none';
      isLoadingReviews = false;
    }, 800);
  }

  function setupInfiniteScroll() {
    const content = document.getElementById('browserContent');
    content.addEventListener('scroll', function() {
      const scrollTop = content.scrollTop;
      const scrollHeight = content.scrollHeight;
      const clientHeight = content.clientHeight;

      if (scrollTop + clientHeight >= scrollHeight - 100) {
        loadMoreReviews();
      }
    });
  }

  function openTrustedPopup() {
    const popup = document.getElementById('trustedPopup');
    const loadingBar = document.getElementById('browserLoadingBar');
    const content = document.getElementById('browserContent');

    applyBrowserTheme();
    popup.style.display = 'flex';
    loadingBar.style.display = 'block';
    content.style.opacity = '0';
    content.classList.remove('browser-content-fade');

    const barInner = loadingBar.querySelector('.browser-loading-bar');
    barInner.style.animation = 'none';
    barInner.offsetHeight;
    barInner.style.animation = 'browserLoad 0.8s ease-out forwards';

    setTimeout(function() {
      loadingBar.style.display = 'none';
      content.classList.add('browser-content-fade');
    }, 800);
  }

  function closeTrustedPopup() {
    document.getElementById('trustedPopup').style.display = 'none';
  }

  function reloadBrowserContent() {
    const loadingBar = document.getElementById('browserLoadingBar');
    const content = document.getElementById('browserContent');

    loadingBar.style.display = 'block';
    content.style.opacity = '0';
    content.classList.remove('browser-content-fade');

    const barInner = loadingBar.querySelector('.browser-loading-bar');
    barInner.style.animation = 'none';
    barInner.offsetHeight;
    barInner.style.animation = 'browserLoad 0.8s ease-out forwards';

    setTimeout(function() {
      loadingBar.style.display = 'none';
      content.classList.add('browser-content-fade');
    }, 800);
  }

  function applyBrowserTheme() {
    const browser = detectBrowser();
    const widget = document.getElementById('browserWidget');
    const safariHeader = document.getElementById('safariHeader');
    const chromeHeader = document.getElementById('chromeHeader');

    widget.setAttribute('data-browser', browser);

    if (browser === 'safari') {
      safariHeader.style.display = 'flex';
      chromeHeader.style.display = 'none';
    } else {
      safariHeader.style.display = 'none';
      chromeHeader.style.display = 'flex';
    }
  }

  function detectBrowser() {
    const ua = navigator.userAgent;
    const isSafari = /Safari/.test(ua) && !/Chrome/.test(ua);
    const isIOS = /iPhone|iPad|iPod/.test(ua);
    const isMac = /Macintosh/.test(ua);

    if (isSafari || isIOS || isMac) {
      return 'safari';
    }
    return 'chrome';
  }

  // Initialize infinite scroll and popup click handler
  document.addEventListener('DOMContentLoaded', function() {
    setupInfiniteScroll();

    // Close popup when clicking outside
    var popup = document.getElementById('trustedPopup');
    if (popup) {
      popup.addEventListener('click', function(e) {
        if (e.target === this) {
          closeTrustedPopup();
        }
      });

      // Add touch support for mobile
      popup.addEventListener('touchend', function(e) {
        if (e.target === this) {
          e.preventDefault();
          closeTrustedPopup();
        }
      });
    }

    // Add touch support for widget
    var widgetClickArea = document.getElementById('tsWidgetClickArea');
    if (widgetClickArea) {
      widgetClickArea.addEventListener('touchend', function(e) {
        e.preventDefault();
        openTrustedPopup();
      });
    }
  });
</script>