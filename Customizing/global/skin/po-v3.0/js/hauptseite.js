/**
 * Hacks fuer die ILIAS-Hauptseite
 *
 * Dieses Script ermöglicht den Login von der Startseite heraus.
 * Wuerde auch gut zu den anpassungen.js passen, bezieht sich halt 
 * eben nur auf eine einzige Seite. Waere auch am besten aufgehoben,
 * wenn das inline oder so wäre, direkt in der Hauptseite eingebunden.
 *
 **/

if(!window.po) window.po = {}; // PhysikOnline Namespace
po.hauptseite = {}; // aHauptseite-Namensraum

po.hauptseite.setup = function() {
	// Loginbox auf Startseite funktionsfaehig machen, POTT #643
	$("#po3-hauptseite-login form").submit(function(){
		// Q  & Dirty
		function createCookie(name,value,days) {
		    if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		    }
		    else var expires = "";
		    document.cookie = name+"="+value+expires+"; path=/";
		}

		function readCookie(name) {
		    var nameEQ = name + "=";
		    var ca = document.cookie.split(';');
		    for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		    }
		    return null;
		}

		function eraseCookie(name) {
		    createCookie(name,"",-1);
		}
		// Cookie loeschen!
		eraseCookie("PHPSESSID");

		return true; // Formular absenden!
	});
	
	// Seiteninhalte verschieben, Startseitenlayout umschalten auf Kacheln etc.
	po.hauptseite.contentManipulation();
};

po.hauptseite.contentManipulation = function() {
	// More-Button in Willkommens-Box
	$('div#more a.more').click(function(){
		$(this).remove();
		$('div#po3-hauptseite-willkommen div.more').slideToggle();
	});
	
	// Entferne Willkommen+Login-Box,
	// um sie ggf. später außerhalb des regulären Content-Bereichs wieder einzufügen.
	var block = $("#po3-hauptseite-top").detach();
	
	// Füge PodcastWiki Zeile ein
	$("#po-hauptseite table.ilContainerBlock").append("<tr id='item_row_podcastwiki' class='ilCLIRow2'><td class='ilCLI'><div id='po3-podcastwiki' class='ilContainerListItemOuter'><div><a href='http://podcast-wiki.physik.uni-frankfurt.de/'><img width='22' src='/local/logos/podcast-goethe-klein.png' title='Symbol Kategorie' alt='' style='position: absolute;  padding: 0px;'></img></a></div><div style='margin: 0px 0px 0px 25px; padding: 0px;'><div class='il_ContainerListItem'><div class='po3-itemHeader'><div style='float:left;padding-bottom: 5px'><h4 class='il_ContainerItemTitle'><a class='il_ContainerItemTitle' href='http://podcast-wiki.physik.uni-frankfurt.de/'>  Podcast Wiki Physik</a></h4></div></div><div class='il_Description' style='clear:both; zoom:1;'>PodcastWiki ist ein studentisches Videoprojekt, das physikalische Experimente vorführt, Vorlesungsinhalte erklärt oder Arbeitsgruppen des Fachbereichs Physik der Goethe-Universität vorstellt.</div><div style='clear:both;'></div></div></div></div></td></tr>");
	
	if ($("#po3-anonymous-user").length !== 0){
		// Wenn nicht angemeldet
		$("#mainspacekeeper div.il_HeaderInner").append(block);
		
		/* Deaktiviert mit neuer Startseite 05.11.2013 - Philip
		// Anpassung der Kategorie-Liste auf der Startseite
		var rows = "#item_row_1361, #item_row_90, #item_row_1393, #item_row_75, #item_row_76";
		// Blende obige Listenpunkte aus
		$(rows).hide();
		// Füge "Alle anzeigen" hinzu
		$("<tr>")
			.addClass("ilCLIRow2")
			.attr("id", "po3-showAllRows")
			.append(
				$("<td>")
					.css({"text-align": "center"})
					.append(
						$("<a>")
							.css({"display": "block"})
							.text("Alle anzeigen")
							.click(function(){
								$(rows).show();
								$("#po3-showAllRows").hide();
							})
					)
				)
			.appendTo("#po-hauptseite table.ilContainerBlock");
		*/
	}
	
	// Metro, Kachel, Tile - Startseite im frischen Design
	if ($("#po3-hauptseite-tile").length){
		// Block mit Kacheln aufgreifen um ihn später an anderer Stelle einzublenden
		var tile = $("#po3-hauptseite-tile").detach();
		// Gewöhnliche Kategorieliste ausblenden
		$("#po3-hauptseite-list").closest('.ilTable').hide();
		
		$("#mainspacekeeper div.il_HeaderInner").append(tile.show());
		
		// Schalter aktivieren
		$("#po3-tile-schalter").click(function(){
			$("#po3-hauptseite-tile").hide(500);
			$("#po3-hauptseite-list").closest('.ilTable').show(500);
		});
	}
};