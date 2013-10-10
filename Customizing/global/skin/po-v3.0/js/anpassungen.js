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
 **/

if(!window.po) window.po = {}; // PhysikOnline Namespace
po.anpassungen = {}; // Anpassungs-Namensraum

po.anpassungen.setup = function() {
	// Enable "JS-only" content
	$(".js-only").show(); // vgl. po3-classes.less unten.

	po.anpassungen.header();
	po.anpassungen.footer();
};

po.anpassungen.header = function(){
	// Im Wesentlichen von Philip geschrieben:

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
};

po.anpassungen.footer = function() {
	// Sven, POTT #689
	po.anpassungen.werkzeugbox = $(".il_Footer .werkzeuge ul");

	// 1. Kurzlink
	$("<li/>").append($(".ilPermaLink")).appendTo(po.anpassungen.werkzeugbox);
	$(".ilPermaLink a").text("Kurzlink");

	// 2. Mobile-Styleswitch
	$("<li/>").append($("a.iosStyleSwitch").text("Mobile-Version")).appendTo(po.anpassungen.werkzeugbox);
	$("div.iosStyleSwitch").remove();

	// 3. Feedback
	// ausgelagert in feedback.js zwecks einfacher Deaktivierung
}

