/**
 * Modulares Starter-System fuer Po3-JavaScripte
 *
 *
 *
 **/

if(!window.po) window.po = {}; // PhysikOnline Namespace
po.startup = {}; // Startup-Namensraum

po.startup.modules = [
	// geordnete Liste von Modulen, nach Reihenfolge
	'anpassungen',
	'feedback', // feedback depends on anpassungen
	'hauptseite', // independent
	//'universe', // independent
	//'werbung', //independent
];

po.startup.setup = function() {
	$.each(po.startup.modules, function() {
		if(po[this] && po[this].setup)
			po[this].setup();
		else
			log("po5Startup: No setup found for "+this);
	});
};

// the one and only body onloader in PO5 scripting
$(po.startup.setup);
