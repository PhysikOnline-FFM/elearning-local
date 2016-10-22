/**
 * PhysikOnline Universe
 *
 * Kuemmert sich um die Einblendung des Universe-Krams aus ILIAS
 * heraus und steuert die Skyline.
 *
 *
 **/

if(!window.po) window.po = {}; // PhysikOnline Namespace
po.universe = {}; // Universe-Namensraum

po.universe.config = {
	// Adresse. PHP wegen verdammtem Caching
	box_url: '/local/Customizing/global/skin/po-v5.1/universe/content.php',
	// jquery-selektor, der auf der eingebundenen Seite ein Objekt raussucht
	box_container: '#po-universe',
};

//////////////////


po.skyline = {};
po.skyline.config = {
	// Skyline-JSON
	json_url: '/local/Customizing/global/skin/po-v5.1/skyline/skyline_json.js',
};

po.skyline.finished = function() {
	if(po.skyline.user_moved_away) {
		// Hab mit der Animation aufgehört, weil Benutzer Maus wegbewegt hat.
		// Stelle richtige Spielrichtung wieder her.
		skyline.set_forward();
		po.skyline.wrapper.removeClass("running");
		po.skyline.user_moved_away = false;
	} else {
		if(po.skyline.fired_universe) {
			// Animation ist voll durchgefahren, und Benutzer war schonmal
			// im Universum. *nicht* nochmal das Universum laden auf Mouseover,
			// um User nicht zu nerven.
			// Also nix tun. Wir könnten zur Feier des Tages die Animation
			// rueckwaerts abspielen lassen. Das macht dem User sicher spass.
			skyline.toggle_direction();
			skyline.start();
		} else {
			// Animation ist voll durchgefahren und Benutzer ist auf Animation
			// geblieben. Der Hovereffekt hat noch nie das Universum angestossen.
			po.universe.load();
			po.skyline.fired_universe = true;
		}
	}
};

po.skyline.setup = function() {
	// Skyline-Spass einrichten
	po.skyline.wrapper = $("#po3-skyline"); // früher $("#po3-skyline a");
	po.skyline.wrapper.hover(function() {
		if(!po.skyline.canvas) {
			po.skyline.canvas = $("<canvas/>").appendTo(po.skyline.wrapper);
			$.getJSON(po.skyline.config.json_url, function(data) {
				if (skyline.debug) log("Skyline JSON loaded");
				po.skyline.loaded = true;
				if(po.skyline.ignore_this_time) {
					// Maus schon wieder weggenommen
					po.skyline.ignore_this_time = false;
					return;
				}
				// window.skyline is now present
				if (skyline.debug) log("Setup:", po.skyline.canvas);
				skyline.setup(po.skyline.canvas, data, po.skyline.finished);
				// canvas anzeigen
				po.skyline.wrapper.addClass("running");
				// Skyline starten
				skyline.start();
			}).fail(function(jqxhr, textStatus, error ) {
				log("Failed to load Skyline JSON data!");
				log("Reason: " +  textStatus + ", " + error);
			});
		} else {
			// Skyline-JSON schon geladen, Animation schonmal (an)gespielt.
			// nochmal starten, nach vorne.
			if(skyline.is_running()) {
				// nichts machen, laeuft ja schon.
				return;
			}
			skyline.set_forward();
			skyline.start();
			po.skyline.wrapper.addClass("running");
		}
	}, function(){
		// mouseout handler
		if(!po.skyline.loaded) {
			// Maus so schnell weggenommen, dass JSON nich mal laden konnte
			po.skyline.ignore_this_time = true;
		} else {
			if(po.skyline.clicked) {
				// Skyline angeklickt, also herrscht Interesse. Weiteranimieren.
				po.skyline.clicked = true; // nicht wegmachen.
				return;
			} else {
				// Skyline animiert sich, aber Benutzer hat Angst bekommen.
				// Wieder rückwärts machen.
				po.skyline.user_moved_away = true;
				skyline.set_backward();
			}
		}
	}).click(function(){
		po.skyline.clicked = true;
	});


};


//////////////////

po.universe.setup = function() {
	$("a.po-universe.footer").click(function() {
		$('html, body').animate({scrollTop:0}, "fast").promise().done(po.universe.load);
	});
	$("a.po-universe.header").click(po.universe.load);
	po.skyline.setup();
}

po.universe.close = function() {
	// close the universe display. This is the reverse of showup().
	po.universe.container.slideUp();
	$(".ilMainMenuHeadSection").slideDown(function() { // when complete...
		if(skyline) {
			// rebuild the skyline if possible
			skyline.stop(); // falls sie laufen sollte (warum auch immer)
			skyline.set_backward();
			skyline.start();
		}
	});
}

po.universe.showup = function() {
	// callback after content loaded via ajax
	// or after clicked on a seccond time
	if(!po.universe.ajax_content_prepared) {
		var u = $("#po-universe");
		u.find("button.close").click(po.universe.close);
		// is als Link geloest
		//u.find("button.fourier").click(po.universe.show_fourier);

		po.universe.ajax_content_prepared = true;
	}

	po.universe.container.slideDown();
	$(".ilMainMenuHeadSection").slideUp();
	// wenn noch was von der skyline uebrig ist... stoppen!
	if(skyline) skyline.stop();
}

po.universe.load = function() {
	// Universe loader.
	if(po.universe.container) {
		// when already loaded, just show
		po.universe.showup();
		return;
	}

	po.universe.container = $('<div id="po-universeContainer"/>').insertAfter(".ilMainMenuHeadSection");
	po.universe.container.hide().load(
		po.universe.config.box_url + " " + po.universe.config.box_container,
		po.universe.showup
	);
}
