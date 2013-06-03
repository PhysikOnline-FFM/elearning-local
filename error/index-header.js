		$(function(){
			$("h2").first().html("Verzeichnislisting von <tt>" + location.pathname + "</tt>");
			// move readme above table
			$(".readme").insertBefore("table");
		});
