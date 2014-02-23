/**
 * Trac Tickets and Wiki pages linking
 * 2014 Sven Koeppel, GPL
 * Requires jquery and d3.js
 **/

var width = 1200,
    height = 800;

var color = d3.scale.category20();

var force = d3.layout.force()
    .charge(-30) // was 120
    .linkDistance(10) // was 30
 //   .alpha(-0.5)  // was 0
    .size([width, height]);

var svg = d3.select("#svg-container").append("svg")
    .attr("width", width)
    .attr("height", height);

// browser size change...
function svgSize() {
	svg.attr('width', window.innerWidth - 10);
	svg.attr('height', window.innerHeight - 10);
	//d3.select("g").attr("transform", "scale(" + $("#svg-container").width()/900 + ")");
	//$("svg").height($("#svg-container").width()*0.618);
}
$(svgSize);
$(window).on('resize',svgSize);

var node, link;

function enable_node(key) { // key == string
	 node.filter(function(d){ return d.key == key; }).each(function(d){
		node.classed('details-active', false); // vmtl etwas ineffizient
		d3.select(this).classed('details-active', true);
		// highlight all links where this node participates
		link.classed('details-active', function(o) {
			return d == o.source || d == o.target;
		});

		$("#details").removeClass().addClass(d.type);
		$("#details > h3").html('<a href="{2}" target="_blank" title="{0} in neuem Fenster öffnen">{0} <i>{1}</i></a>'.format(d.type, d.key, d.link));
		$("#details > div.info").text(''); //.html("<a href='{0}'>Visit</a>".format(d.link));
		$("#trac-content").empty()
			.html('<img src="loading.gif">')
			.load(d.link + " .trac-content", function(data, status, xhr){
				if(status == 'error') {
					msg = "Bitte <a href='/pott/login' target='_blank'>im POTT einloggen</a> und dann hier neuladen zum Anschauen. Fehler: ";
					$( "#trac-content" ).html( msg + xhr.status + " " + xhr.statusText );
				} else {
					$("#trac-content a").filter('.ticket, .wiki').each(function(){
						types = [/ticket\/(\d+)/, /wiki\/([^?#]+)$/];
						for(i=0;i<types.length;i++) {
							m = this.href.match(types[i]);
							if(m && m.length > 1) {
								//console.log("Found ",m[1],this.href);
								var key = m[1];
								$(this).click(function(){
									// achtung: auf sowas wie m[1] kann er hier nicht mehr zugreifen
									enable_node(key);
									return false;
								}).addClass("graph-link");
							}
						}
					});
				}
	  	});
	});
}


d3.json("get_graph.php", function(error, graph) {
  //console.log(graph);
  force
      .nodes(graph.nodes)
      .links(graph.links)
      .start();

  link = svg.selectAll(".link")
      .data(graph.links)
      .enter().append("line")
      .attr("class", "link")
      .style("stroke-width", function(d) { return Math.sqrt(d.value); });

  tooltip = $("<div/>").attr({'id':'tooltip','class':'hidden'}).appendTo("body");

  node = svg.selectAll(".node")
      .data(graph.nodes)
      .enter().append("circle")
      .attr("class", function(d){ return "node "+d.type; })
      .attr("r", 5)
      .on("click", function(d,i) {
		enable_node(d.key);
      })
      .on("mouseover", function(d) {
	tooltip.removeClass('hidden').addClass('visible')
		.html("<b>{0}</b> {1}".format(d.type,d.key))
		.css({'left': (d3.event.pageX + 7) + "px",
		      'top': (d3.event.pageY - 35) + "px"});
	d3.select(this).classed('hover', true);
      })
      .on("mouseout", function(d) {
	tooltip.removeClass('visible').addClass('hidden');
	d3.select(this).classed('hover', false);
      })
      //.style("fill", function(d) { return color(d.group); })
      .call(force.drag);

  node.append("title")
      .text(function(d) { return d.name; });

  force.on("tick", function() {
    link.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

    node.attr("cx", function(d) { return d.x; })
        .attr("cy", function(d) { return d.y; });
  });
});


// lib helping: "string{0}and{1}".format('abc','def');
String.prototype.format = String.prototype.format = function() {
    var s = this,
        i = arguments.length;

    while (i--) {
        s = s.replace(new RegExp('\\{' + i + '\\}', 'gm'), arguments[i]);
    }
    return s;
};

