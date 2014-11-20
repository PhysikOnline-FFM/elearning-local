$(function(){
/*	$(window).on('resize', function() {
		$( $(window).width() < 420) )
	}); */

	// short-URL-Service, vgl POTT #471

	if(shortlink = find_shortlink()) {
		var u = $("#altlinks ul");
		u.find("li.last").removeClass("last");
		u.append('<li class="last"><a href="#" id="shortlink"></a></li>');
		//append("<div id='shortlinks'><h3>Kurzlink</h3> <a href='#' id='shortlink'></a></div>");
		$("#shortlink").attr('href', shortlink).text("Kurzlink").click(function(){
			var c = $("#shortlink_copy");
			if(c.length)
				c.toggle();
			else
			    $('<input type="text" id="shortlink_copy">').val(shortlink).click(function(){
				$(this).select();
			    }).insertAfter('#shortlink');
			return false;
		});
	}

	// PWP-Feedeinbettung fuer den POTT,  vgl POTT #982
	pwpFeedSelector = ".pwp-feed";
	if($(pwpFeedSelector).length) {
		load_pwp_feed(pwpFeedSelector);
	}
});

function find_shortlink() {
	url = location.href;
	replacements = {
		// new part => regex finding old part

		// 1. generelle Kuerzung, ersetze langen uni-frankfurt.de-Domainnamen
		"http://physikelearning.de/projekt": /^http(.*)projekt/,

		// 2. Kuerzel fuer Trac-komponenten
		"w": /projekt\/wiki/,
		"t": /projekt\/ticket/,
		"r": /projekt\/report/,
		
		// 3. Superkurze Ticketurls: ...de/t/123 => ...de/123
		"/": /\/t\//		
	}

	$.each(replacements, function(repl, regex) {
		url = url.replace(regex, repl);
	});
	return (url != location.href) ? url : null;
}

function load_pwp_feed(targetDivSel) {
	// Siehe POTT #982 fuer PodcastWiki-Feed-Einbettung!
	// ?embed=true ist, damit preview-jpgs als data-URI im XML eingebettet werden und nicht ueber HTTP
	// geholt werden muessen (HTTP-HTTPs-Issue, siehe #982)
	$.get('/pwp-feed-embedding/?embed=true', function(doc){
		// jQuery Buggy mit Namespaces, workaround:
		no_ns_doc = doc.replace(/<(\/?[a-z0-9]+):/gi, "<$1_")

		// this is the simplest way to get an DOM from the XML, but
		// it is parsed as HTML. Problem: <link> tag is not supposed to have
		// content (<link>http://....</link> gets <link/>http://...).
		//$feed = $(no_ns_doc);

		// this is probably better.
		$feed = $($.parseXML(no_ns_doc));

		html='<ul>';
		// take first three podcast feed items
		$feed.find('item').slice(0,4).each(function(){
			$this = $(this); // for these closures:
			t = function(tag){ return $this.find(tag).text() };
			preview = function(attr){ return $this.find("pwpfeed_image").attr(attr); };

			html += '<li class="video">';
			html += strformat('<a href="{0}" title="Video über {1} sehen">', t("link"), t("itunes_subtitle"));
			html += strformat('<img src="{0}" width="{1}" height="{2}" alt="Preview"/>',
				preview('href'), preview('width'), preview('height')
			);
			html += strformat('<span class="title">{0}</span><span class="subtitle">{1}</span>',
				t('title'), t("itunes_subtitle")
			);
			html += '</a></li>';
		});
		html += '</ul>';
		$(targetDivSel).html(html);
	}, /* dataType: */ 'text').fail(function() {
		$(targetDivSel).html("Failed to load videos");
	});
}

// JS sprintf:
// strformat("{0} is dead, but {1} is alive! {0} {2}", "ASP", "ASP.NET")
// siehe #982
function strformat() {
    var args = arguments;
    return args[0].replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[parseFloat(number)+1] != 'undefined'
        ? args[parseFloat(number)+1]
        : match
      ;
    });
}
