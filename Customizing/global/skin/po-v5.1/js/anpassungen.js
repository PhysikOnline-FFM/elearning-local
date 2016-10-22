/**
 * ILIAS-Modifikationen in PO3
 *
 * Dieses Script beinhaltet alle "Quick and Dirty"-Anpassungen,
 * die am ILIAS vorgenommen wurden.
 *
 * Konventionell "geteilte" Variablen:
 *   po.anpassungen.werkzeugbox: eine $("ul")
 *       der man eigene Menuepunkte hinzufuegen kann (gemacht von feedback.js).
 *
 * Anpassungen an ILIAS 5.1 - LG 28.09.16
 **/

if(!window.po) window.po = {}; // PhysikOnline Namespace
po.anpassungen = {}; // Anpassungs-Namensraum

po.anpassungen.setup = function() {
	// Enable "JS-only" content
	$(".js-only").show(); // vgl. po3-classes.less unten.
	$(".hidden-js").hide(); // nur fuer JS-faehige Browser verstecken
  	
	po.anpassungen.menu(); 
	po.anpassungen.footer();
};

// versteckt den Menu-Button, wenn man nicht angemeldet ist, nicht optimal, da der Button kurz sichtbar ist
po.anpassungen.menu = function() {
	var menuIcon = $(".navbar-toggle");
	var menuContent = $("#ilMainMenuEntries");
	
	console.log("menu-function");

	if ($.trim(menuContent.html()) == "") {
		menuIcon.hide();
		console.log("icon versteckt: "+menuContent.html());
	}
}

po.anpassungen.footer = function() {
	// Sven, POTT #689
	//po.anpassungen.werkzeugbox = $(".ilFooter .werkzeuge ul");
	po.anpassungen.werkzeugbox = $(".ilFooter #js-only-feedback");

	// 1. Kurzlink - auskommentiert 22.09.16
	
	
	//$("<li/>").append($(".permalink_label").detach()).append($("#current_perma_link").detach()).appendTo(po.anpassungen.werkzeugbox);
	$("a.permalink_label").text("Kurzlink");
   	$("label[for=current_perma_link]").remove(); 
	$("#current_perma_link").remove();
	
	// 2. Styleswitch
	//$("<li/>").append($("a.iosStyleSwitch").text("Skin wechseln")).appendTo(po.anpassungen.werkzeugbox);
	$("<p/>").append($("a.iosStyleSwitch").text("Skin wechseln")).appendTo($("#po-beschreibung"));
	$("div.iosStyleSwitch").remove();

	// 3. Feedback
	// ausgelagert in feedback.js zwecks einfacher Deaktivierung
}

