/**
 * Hacks fuer die ILIAS-Hauptseite
 *
 * Dieses Script ermöglicht den Login von der Startseite heraus.
 * Wuerde auch gut zu den anpassungen.js passen, bezieht sich halt 
 * eben nur auf eine einzige Seite. Waere auch am besten aufgehoben,
 * wenn das inline oder so wäre, direkt in der Hauptseite eingebunden.
 *
 **/

if(!window.po) window.po = {}; 	// PhysikOnline Namespace
po.hauptseite = {}; 			// Hauptseite-Namensraum

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
		// returned null:
		// console.log("PHPSESSID: "+readCookie("PHPSESSID"));
		eraseCookie("PHPSESSID");
		
		// console.log("alle Cookies nacher: "+document.cookie);


		return true; // Formular absenden!
	});
	
	// Seiteninhalte verschieben, Startseitenlayout umschalten auf Kacheln etc.
	po.hauptseite.contentManipulation();
};

po.hauptseite.contentManipulation = function() {
	// More-Button in Willkommens-Box
	$('div#more-btn a').click(function(){
		$('div#more-text').slideToggle(300, function(){
			if ($(this).is(':visible'))
				$('div#more-btn a').text('weniger');
			else
				$('div#more-btn a').text('mehr');
		});
	});
	
	// Entferne Willkommen+Login-Box,
	// um sie ggf. später außerhalb des regulären Content-Bereichs wieder einzufügen.
	/*var block = $("#po3-hauptseite-top").detach();
	
	if ($("#ilTopBarNav a[href^=login]").length !== 0){
		// Wenn nicht angemeldet
		$("#po-hauptseite").prepend(block);
	}*/
	
	// Füge PodcastWiki Zeile ein
	//$("#po-hauptseite table.ilContainerBlock").append("<tr id='item_row_podcastwiki' class='ilCLIRow2'><td class='ilCLI'><div id='po3-podcastwiki' class='ilContainerListItemOuter'><div><a href='http://podcast-wiki.physik.uni-frankfurt.de/'><img width='22' src='/local/logos/podcast-goethe-klein.png' title='Symbol Kategorie' alt='' style='position: absolute;  padding: 0px;'></img></a></div><div style='margin: 0px 0px 0px 25px; padding: 0px;'><div class='il_ContainerListItem'><div class='po3-itemHeader'><div style='float:left;padding-bottom: 5px'><h4 class='il_ContainerItemTitle'><a class='il_ContainerItemTitle' href='http://podcast-wiki.physik.uni-frankfurt.de/'>  Podcast Wiki Physik</a></h4></div></div><div class='il_Description' style='clear:both; zoom:1;'>PodcastWiki ist ein studentisches Videoprojekt, das physikalische Experimente vorführt, Vorlesungsinhalte erklärt oder Arbeitsgruppen des Fachbereichs Physik der Goethe-Universität vorstellt.</div><div style='clear:both;'></div></div></div></div></td></tr>");
	
	// Metro, Kachel, Tile - Startseite im frischen Design
	if ($("#po3-hauptseite-tile").length){
        // Gewöhnliche Kategorieliste ausblenden
		$("#po3-hauptseite-list").hide();
		
		// Schalter aktivieren
		$("#po3-tile-schalter").click(function(){
			$("#po3-hauptseite-tile").hide(700);
			$("#po3-hauptseite-list").show(700);
		});
	}
};
