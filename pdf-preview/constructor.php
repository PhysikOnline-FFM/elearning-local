<html>
<head>
	<title>PDF Preview-Constructor</title>
	<meta charset="utf-8">
	<!-- Sven Koeppel, 03. Maerz 2015 -->

	<!-- Styles/Wireframe by pott-talks/jquery-talks.htm -->
	<!-- jQuery per CDN -->
	<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<!-- Bootstrap 3 per CDN -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

	<!-- some styling -->
	<style type="text/css">
		body { background-color: #eef9ff; }
		.page-header {
			background-color: white;
			box-shadow: 0 1px 2px rgba(0, 0, 0, 0.075);
			padding: 1em;			
		}
		img.logo {
			width: 120px;
			float: right;
		}

		div.pdf-pic img {
			max-width: 100%;
		}

		div.pdf-pic.displays-thumb img {
			box-shadow: 0 1px 2px rgba(0,0,0,.7);
			border-radius: 3px;
		}
	</style>
	
<script type="text/javascript">
var po_url = "https://elearning.physik.uni-frankfurt.de";
var path = '/pdf-preview/';
var metadata_flag = "&metadata=yes";
  
function get_pdf_preview_url(pdf_url, page=null, size=null) {
	// $.param has problems with UTF-8 characters, encode by ourselve
	return url = path+"?url="+encodeURI(pdf_url)+(size?"&size="+size:"")+(page?"&page="+(page-1):"");
	// page counting bug in PDF preview, workaround here.
}

function display_pdf() {
	pdf_url = $("#url").val();
	curpage = $("#page_detail input").val();
	sizeX = $("#sizeX").val();
	sizeY = $("#sizeY").val();
	size = sizeX+"x"+sizeY;
	if(size == "x") size = null;

	imgurl = get_pdf_preview_url(pdf_url, curpage, size);

	$("div.pdf-pic img").attr('src', imgurl);
	$("div.pdf-pic a").attr("href", imgurl);
	$("div.pdf-pic").addClass("displays-thumb");
	$("#out-url").val(po_url + imgurl);

	// also check if the URL does not give back an error!
	$.ajax({
		url: imgurl,
		jsonp: "jsonp",
		dataType: "jsonp",
		error: function(data) {
			if(data.status == 200) {
				// all is fine with the generated picture. Of course parsing
				// it as jsonp fails.
			} else if(data.responseJSON) {
				$("#details1 .alert").removeClass("alert-success").addClass("alert-danger")
					.html("<b>Problem with current parameter set</b>. See error message below");
				show_json_details(data.responseJSON);
			} else {
				$("#details1 .alert").removeClass("alert-success").addClass("alert-danger")
					.html("<b>Something weird happened</b>");
				if(console) console.log(data);
			}
		},
		success: function(data) {
			// all is fine with the generated picture.
		}
	});
		
}

function show_json_details(data) {
	$("#info").removeClass('hidden').find(".content").append(
		$('<dl class="dl-horizontal"/>').append($.map(data, function(v,k) {
			return $('<dt>'+k+'</dt><dd><pre>'+v+'</pre></dd>');
		}))
	);
}

$(function(){
	$("button.generate").click(function(){
		pdf_url = $("#url").val();
		metaurl = get_pdf_preview_url(pdf_url)+metadata_flag;
		$.ajax({
			url: metaurl,
			jsonp: "jsonp", // name of the GET parameter
			dataType: "jsonp",
			error: function(data) {
				$("#details1").removeClass('hidden').find(".alert").addClass("alert-danger")
					.html('<b>Could not load PDF!</b> Please see the error attached. Most likely, the <a href="'+pdf_url+'">given URL</a> does not give a PDF file. Maybe it is also protected by password.');
				show_json_details(data.responseJSON);
			},
			success: function(data) {
				$("#details1").removeClass('hidden').find('.alert').addClass('alert-success')
					.html('<b>PDF found</b> You may now change additional settings. <button class="button show_json_details">Show details</button>');
				$("button.show_json_details").click(function(){ show_json_details(data); $("button.show_json_details").remove(); });

				show_pdf_details(data);
			}
		});
	});
});

function show_pdf_details(data) {
	if(!data.output) {
		alert("This was not really a success!"); // seltsam
		show_pdf_details(data);
	}

	// able to find out page numbers?
	if((p=data.output.match(/Pages:\s*(\d+)/)) && p[1]) {
		pageNum = parseInt(p[1]);
		if(pageNum == 1) {
			$("#page_detail input").append("Das PDF hat nur eine Seite.");
		}
		$slider = $("#page_detail").removeClass('hidden').find("input").attr("max",pageNum);
		$("#page_detail").find(".max").text(pageNum);
		sliderchange = function(){
			$("#page_detail").find(".cur").text($slider.val());
		};
		$slider.change(sliderchange); // onchange
		$slider.change(display_pdf); // preview
		sliderchange(); // current value
	}

	// size details
	$("#size_detail input").change(display_pdf);

	$("#post_url, #size_detail").removeClass("hidden");
	$("#generate").addClass("hidden");
	display_pdf(); // display pdf preview picture
}
</script>
</head>
<body>
<div class="container-fluid">

<div class="page-header">
	<img class="logo" src="https://elearning.physik.uni-frankfurt.de/local/logos/PhysikOnline-Logo3D.png">
	<h1>PDF-Previewer für Websites
		<small>PhysikOnline-Service</small>
	</h1>

	<p>Dies ist ein (inoffizieller) Dienst für PhysikOnline-Websites, um
	Vorschaubilder von PDFs als Bilder in Webseiten einzubinden. Die PDFs müssen
	im Internet unter einer <tt>uni-frankfurt.de</tt>-Adresse gespeichert sein und werden
	anhand ihrer URL identifiziert. Siehe auch <a href="https://elearning.physik.uni-frankfurt.de/projekt/ticket/867">POTT 867</a>.</p>

	<noscript>
	<p><b>Dieser Previewer braucht JavaScript zum Zusammensetzen der Preview-URL!</b></p>
	</noscript>
</div>

<div class="row">
	<div class="col-md-4">
		<div class="pdf-pic">
			<a href="#" title="Direkt zum Bild"><img src="http://upload.wikimedia.org/wikipedia/commons/9/9b/Adobe_PDF_icon.png"></a>
		</div>
	</div>

	<div class="col-md-8">
		<div class="panel panel-default">
		<div class="panel-body">
			<form class="form-horizontal">
				<div class="form-group form-group-lg">
					<label for="url" class="col-sm-2 control-label">URL des PDFs</label>
					<div class="col-sm-10">
						<input class="form-control" type="url" id="url" placeholder="http://www.example.com/adresse-vom-dokument.pdf">
					</div>
				</div>
				<div class="form-group hidden" id="details1">
					<div class="col-sm-offset-2 col-sm-10">
						<div class="alert" role="alert">
							<!-- content goes here -->
						</div>
					</div>
				</div>
				<div class="form-group hidden" id="info">
					<div class="col-sm-offset-2 col-sm-10">
						<div class="alert alert-info" role="alert">
							<strong>Raw PDF conversion output:</strong>
							<div class="content"><!-- rendered jsonify goes here --></div>
						</div>
					</div>
				</div>
				<div class="form-group hidden" id="page_detail">
					<label for="page" class="col-sm-2 control-label">Anzuzeigende Seite: <br>Seite <span class="cur">X</span>/<span class="max">Y</span></label>
					<div class="col-sm-10">
						<input type="range" name="page" id="page" min="1" max="1" value="1" step="1">	
					</div>
				</div>
				<div class="form-group hidden" id="size_detail">
					<label for="sizeX" class="col-sm-2 control-label">Vorschaugröße</label>
					<div class="col-sm-10">
						<input type="number" name="sizeX" id="sizeX" min="1" max="100000" value="800"> x
						<input type="number" name="sizeY" id="sizeY" min="1" max="100000"> x
						<b>Pixel</b>
						<br><small><em>Angaben von nur einer Zahl reichen: <tt>x900</tt> oder <tt>400x</tt> berechnet die zweite Größe maßstabsgetreu.</em></small>
					</div>
				</div>
				<div class="form-group hidden" id="post_url">
					<label for="out-url" class="col-sm-2 control-label">Generiertes Bild</label>
					<div class="col-sm-10">
						<b>Vorschaubild links angezeigt</b>. Adresse zum Kopieren:
						<input class="form-control" type="url" id="out-url">
					</div>
				</div>
				<div class="form-group" id="generate">
					<div class="col-sm-offfset-2 col-sm-10">
						<button type="button" class="btn btn-primary generate">Vorschaubild generieren</button>
					</div>
				</div>
			</form>
		</div>
		</div>
	</div>
</div>
