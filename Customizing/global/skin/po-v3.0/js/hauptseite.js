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
}
