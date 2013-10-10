/*	PhysikOnline 3.0
 *	Sieht einige Änderungen im ILIAS vor, die mit JavaScript am einfachsten umgesetzt werden können.
 *	Z.B. werden einige Menüpunkte verschoben.
 */

$(document).ready(function() {
	// Verschieben von Menüpunkten aus "persönlicher Schreibtisch" in Benutzer-Dropdown
	$("#mm_desk_ov div.ilGroupedListSep").remove(); //Entferne Seperatoren
	mm_mail = $("#mm_pd_mail").detach();
	mm_profil = $("#mm_pd_profile").detach();
	mm_settings = $("#mm_pd_sett").detach();
	sep = $("<div>").addClass("ilGroupedListSep");
	
	$("#po3-mm_user_ov")
		.prepend(sep.clone())
		.prepend(mm_mail)
		.prepend(sep.clone())
		.prepend(mm_settings)
		.prepend(mm_profil);

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
});


// Feedback service

$(function() {
	// CSS ist eingebunden im Ordner less.d
	
	feedback_path = '/local/feedback';
	
	$.getScript(feedback_path+"/feedback.js", function() {
		options = {
			h2cPath: feedback_path + '/html2canvas.js',
			url: feedback_path + '/send.php',
			label: 'Feedback',
			header: "Probleme berichten",
			nextLabel: "Weiter",
			reviewLabel: "Vorschau",
			sendLabel: "Abschicken",
			closeLabel: "Schließen",
			messageSuccess: "Ihr Feedback wurde abgeschickt. Wir werden uns schnellstmöglich um die Bearbeitung kümmern.",
			messageError: "Es gab einen Fehler bei der Verarbeitung ihres Feedbacks. Bitte schreiben Sie stattdessen eine E-Mail an team@elearning.physik.uni-frankfurt.de.",
		};
		options["pages"] = [
				new window.Feedback.Form([
					{
						type: "textarea",
						name: "Issue",
						label: "Sie können an dieser Stelle beschreiben, was auf der neuen Lernplattform nicht richtig funktioniert. Auch ein Screenshot wird dabei übermittelt. Bitte vergessen Sie nicht ihren Namen und E-Mail-Adresse, falls Sie eine Antwort erhalten möchten.",
						required: false,
					}
				]),
				new window.Feedback.Screenshot(options),
				new window.Feedback.Review()
			];
		
		Feedback(options);
	});
});
