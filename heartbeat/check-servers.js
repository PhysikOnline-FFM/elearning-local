// This is a service written by Sven once for svenk. On Server side there are checks
// that only valid hostnames can be checked, no IPs, as is stated in this documentation.

/* API to simple RPC like uptime service. Usage:

    is_online("127.0.0.1", function(json_data) {
      	console.log(json_data)
    });
*/
function is_online(indata, callme, context) {
	//data = {"host": hostname, "output": "plain"};
	$.ajax({
		url: 'online-status.php',
		context: [ callme, context ],
		data: indata, 
		success: function(outdata) {
			this[0].call(this[1], outdata);//data == "online");
		} // success func
	}); // ajax
}

function format_popover() {
	// this = <span class="state label..."/>
	d = $(this).data('status-data');
	s = '<dl class="dl-horizontal">';
	for(var k in d) {
		s += '<dt>{0}</dt><dd>{1}</dd>'.format(k, d[k]);
	}
	s += '</dl>';
	return s;
}
 
function check_server() {
	// schaut nach, welcher Server online ist, setzt CSS-Klasse und
	// packt online-Server unter Reihenfolgungserhaltung (reverse)
	// an den Anfang der Liste
	
	// test if we need lazy loading
	/*if($(".server.lazy").length && !window.inview_loaded) {
		$.getScript("/src/plain.design/jquery.inview.js", function(){
			window.inview_loaded = 1;
			check_server();
		});
		return;
	}*/
	var check = function(){
		$(this).removeClass("online offline").addClass("loading");
		if(!$(this).children(".state").length)
			$(this).prepend("<span class='state label label-default'/>");
		$(this).children(".state").html("Loading")
			.data('status-data',{'state':'Loading'})
			.popover({
				'html': true,
				'trigger': 'hover',
				'placement': 'auto top',
				'title': 'Details',
				'content': format_popover,
				
			});
		

		is_online($(this).data(), function(data) {
			$(this).children(".state").removeClass("label-default")
				.addClass("label-"+data['label_status']).html(data['text'])
				.data('status-data', data);
			/*
			if(data['online']) {
				//$(thisonlineRadius Login Service ).detach();
				//$("#serverlist").prepend( $(this) );
				$(this).addClass("online");
				$(".state", this).removeClass("label-default").addClass("label-success").html("online");
				// Switch macht netten Effekt, braucht jqueryUI
				// und sieht auch nicht so umwerfend aus
				//$(this).switchClass("offline", "online", "slow").slideDown();
			} else {
				//$(this).switchClass("online", "offline", "slow");
				$(this).addClass("offline");
				$(".state", this).removeClass("label-default").addClass("label-danger").html("offline");
			}
			*/
			$(this).removeClass("loading");
		}, this); // is_online()
	};
	//$(".server.lazy").one('inview', check);
	$(".server").not(".lazy").each(check);
	// netter trick, um Liste umzudrehen: $($(".server").get().reverse())
	// aber voellig unnoetig hier.
}

function more() {
	$("div.more").hide();
	$("button.more").click(function(){
		$("button.more").hide();
		$("div.more").slideDown();
	});
}

$(check_server);
$(more);

// lib helping: "string{0}and{1}".format('abc','def');
String.prototype.format = String.prototype.format = function() {
    var s = this,
        i = arguments.length;

    while (i--) {
        s = s.replace(new RegExp('\\{' + i + '\\}', 'gm'), arguments[i]);
    }
    return s;
};
