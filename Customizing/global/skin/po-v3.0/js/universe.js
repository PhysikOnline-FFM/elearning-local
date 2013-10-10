/**
 * PhysikOnline Universe
 *
 * Kuemmert sich um die Einblendung des Universe-Krams aus ILIAS
 * heraus.
 *
 *
 **/

if(!window.po) window.po = {}; // PhysikOnline Namespace
po.universe = {}; // Universe-Namensraum

po.universe.config = {
	// Adresse 
	box_url: '/local/Customizing/global/skin/po-v3.0/universe/content.htm',
	// jquery-selektor, der auf der eingebundenen Seite ein Objekt raussucht
	box_container: '#po-universe'
};

po.universe.setup = function() {
	$("a.po-universe.footer").click(function() {
		$('html, body').animate({scrollTop:0}, "fast", po.universe.load);
	});
	$("a.po-universe.header").click(po.universe.load);
}

po.universe.load = function() {
	// Universe loader.
	if(po.universe.container) {
		// when already loaded, just show
		po.universe.container.slideDown();
		return;
	}

	po.universe.container = $('<div id="po-universeContainer"/>').insertAfter(".ilMainMenuHeadSection");
	po.universe.container.hide().load(
		po.universe.config.box_url + " " + po.universe.config.box_container, function() {
		po.universe.container.slideDown();
	});
}
