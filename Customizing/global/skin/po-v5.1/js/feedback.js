/**
 * Feedback aus dem ILIAS direkt in den POTT
 *
 * In diesem Script sind alle Sachen verpackt, die die Feedback-Funktion in
 * ILIAS einbauen.
 *
 * Nutzt po.anpassungen.werkzeugbox.
 *
 * Anpassungen an ILIAS 5.1 - LG 28.09.16
 **/

if(!window.po) window.po = {}; // PhysikOnline Namespace
po.feedback = {}; // Feedback-Namensraum

po.feedback.settings = {
	// Pfad zu Feedback-Funktionen (PHP-Mailer, JS-Libs)
	path: '/local/feedback',
};

po.feedback.setup = function() {
	if(po.anpassungen) {
		//po.anpassungen.werkzeugbox.append('<li><a href="#load_feedback" id="po-loadfeedback" title="Schickt einen Screenshot mit Problembeschreibung an das Physikelearning-Team">Problem berichten</a></li>');
		po.anpassungen.werkzeugbox.append('<a href="#load_feedback" id="po-loadfeedback" title="Schickt einen Screenshot mit Problembeschreibung an das Physikelearning-Team">Problem berichten</a>');
		$("#po-loadfeedback").click(po.feedback.run);
	} else {
		log("po feedback system: Cannot setup because require po.anpassungen!");
	}
}

po.feedback.run = function() {
	// Feedback service
	// CSS ist eingebunden im Ordner less.d
	
	feedback_path = po.feedback.settings.path;
	
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

		// QUICK AND DIRTY Feedback Button Move: System anfeuern
		$("button.feedback-btn").click();
	});
}
