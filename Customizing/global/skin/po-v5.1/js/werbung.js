/**
 * Hacks zur Einlblendung von Werbung auf ILIAS
 *
 * Ist ausgelagert, um es einfacher deaktivieren zu können.
 * Content sollte daher auch dynamisch erstellt werden und nicht in Templates eingebunden sein.
 *
 **/

if(!window.po) window.po = {}; // PhysikOnline Namespace
po.werbung = {}; // Werbung-Namensraum

po.werbung.setup = function() {
	if ($("#po-hauptseite").length !== 0){
		// Auf Startseite
		po.werbung.okt2013('div.il_HeaderInner');
	}
	else if ($("#block_pditems_0_blhead").length !== 0) {
		// 'Auf Persönlicher Schreibtisch' & 'Meine Kurse'
		po.werbung.okt2013('div.il_HeaderInner');
        }
}

po.werbung.okt2013 = function(target){
	box = $('<div />')
		.addClass('coder-werbung')
		.append($('<div />')
			.addClass('text more')
			.html('Du bist Student? <br />Hast Programmierkenntnisse? Oder drehst gerne Videos?<br />Mehr Informationen unter:')
		)
		.append($('<a />​')
			.attr('href', 'http://physikelearning.de/job')
			.text('http://physikelearning.de/job')
			.addClass('text-right link')
		)
	;
	werbung = $('<div />').attr('id','coder-werbung13').append(box);
	werbung.appendTo(target);
	
	// Ganze Box zur Linkfläche machen
	werbung.click(function(event){
		if(!$( event.target ).is("a")){
			// check that user didnt click the link
			var a = $(this).find("a").first();
			if(a.length){
				a[0].click();
			}
		}
	});
}