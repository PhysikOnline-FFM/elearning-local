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
	$(".hidden-js").hide(); // nur fuer JS-faehige Browser verstekcen

	po.anpassungen.header();
	po.anpassungen.footer();
	//po.anpassungen.tablerow_links(); Deaktivieren, POTT #712.
	po.anpassungen.treestructures();
};

po.anpassungen.tablerow_links = function() {
	// Tabellenzeilen anklickbar machen (größere Klickfläche)
	var t = $("table.ilContainerBlock td.ilCLI").css("cursor", "pointer");
	var f = function(event){
		// bubbling: Erst reagieren, wenn target in t ist.
		if( $.inArray(event.target, t) > -1) {
			console.log("Auf teil geklickt");
		} else {
			console.log(event.target);
		}
		return true;
		/*
		if( !$( event.target ).is( "a" ) &&  !$( event.target ).is( "input" ) ) {
			// check that user didnt click the link
			var a = $(this).find("a").first();
			if(a.length) {
				a[0].click();
			}
		}*/
	};
	t.click(f);
};

po.anpassungen.treestructures = function(){
	// Fix für Darstellung von Baumstrukturen und anderen "left contents" von ILIAS, die nicht mehr ins aktuelle Layout passen.
	// Die Container sollen losgelöst, verschiebbar und größenänderbar sein.
	// Siehe Tickets #654, #700, #713, #933
	var div = $('div#left_nav').resizable().draggable();
	
	// Resize
	var vp_width = $(window).width();
	var cont_width = $('div.po3-contentBlock').width();
	div.css('width', Math.max(240, Math.round((vp_width - cont_width - 50)/2)));
	// Positioniere Container anders, bei kleinen Bildschirmenen
	if (vp_width < 1400){
		div.css({'left': (vp_width - div.width() - 30)+'px'})
	}
}

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

