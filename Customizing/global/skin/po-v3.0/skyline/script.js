/**
 * Zerlegung von Frankfurts Skyline in Fouriermoden
 * Eine Numpy + HTML-Canvas-Demonstration von Sven Köppel, 2013
 * 
 * Das vorliegende Javascript ist der verarbeitende HTML5-Part
 * an dem Gesamtprogramm. Vorliegend sind eine Serie von Einzelbildern
 * im JSON-Format. Dieses Script kuemmert sich nur um den Zeichen-
 * vorgang. Input der Daten und UI-Handling passieren an anderer
 * Stelle.
 * 
 * Als Namensraum gilt "skyline".
 * Benötigt jQuery und Canvas-faehigen Browser.
 *
 **/

skyline = {
	skyline_color: '#0163aa',
	background_color: '#f1f1f1',
	width: 0, // Breite wird durch Datenbreite gesetzt!
	height: 65, // Hoehe wird hier vorgegeben

	// in ms, die Geschwindigkeitsstellschraube
	total_duration: 4000,
	// fps (Frames per Second)
	fps: 40,
	
	data: [], // filled externally

	debug: false, // Debug-Ausgaben (braucht sprintf.js)

	// Statemachine, use helper functions
	StatusEnum: { "stopped": 0, "running": 1, "paused": 2 },
	status: 0,

	// Use helper function zum steuern
	DirectionEnum: { "forward": "forward", "backward": "backward" },
	direction: "forward",

	finished_callback: null, // Setzen: Callback wenn fertig
};

/**
 * Debugging-Funktion, um den Fortschritt festzuhalten.
 **/
skyline.progress = function() {
	if(!skyline.prog_inited) {
		// init
		skyline.prog = $("progress")[0];
		skyline.prog.max = skyline.max_frames;
		$("#total").text(skyline.max_frames + ", skyline datalen: " + skyline.data.length);
		skyline.prog_inited = 1;
	}

	$("#cur").text(skyline.cur_frame);
	skyline.prog.value = skyline.cur_frame;
}

skyline.setup_plot = function() {
	// setup linestyle etc. for plotting
	// only needs to be done once, saves time in each loop
	if (skyline.c === undefined){
		log('skyline.c is undefined. Please call skyline.setup() first');
		return;
	}
	ctx = skyline.c;
	ctx.strokeStyle = "darkblue";
	ctx.fillStyle = skyline.skyline_color;
	ctx.lineWidth= 2;
}

skyline.bg = function() {	
	// clear by painting the background
	if (skyline.c === undefined){
		log('skyline.c is undefined. Please call skyline.setup() first');
		return;
	}
	ctx = skyline.c;
	ctx.save();
	ctx.fillStyle = skyline.background_color;
	ctx.fillRect(0,0, skyline.width, skyline.height);
	ctx.restore();
}

skyline.simple_loop = function() {
	// Einfacher setinterval-Worker, zieht keine frame_times zurate.
	// lässt sich umkehren, bei frame_times ist das bedeutend schwieriger.

	var s = skyline;
	// check if we already on next skyline picture
	if(s.direction == "forward" && s.cur_skyline < s.data.length &&
	   s.cur_frame > (s.cur_skyline+1) * s.total_duration / skyline.fps)
		s.cur_skyline++;
	if(s.direction == "backward" && s.cur_skyline >= 0 &&
	   s.cur_frame < (s.cur_skyline-1) * s.total_duration / skyline.fps)
		s.cur_skyline--;

	// calculate skyline_index by frame_index as a floating point number
	// Derzeit: Einfach mitteln zwischen beiden
	var skyline_index = s.cur_skyline,
	    skyline_float = s.cur_frame  / s.max_frames * s.data.length,
	    skyline_prev = Math.floor(skyline_float), // id of last skyline id
	    skyline_next = Math.ceil(skyline_float),  // id of next skyline id
	    transition = skyline_float - skyline_prev; // value between [0,1]
	if(skyline_prev >= s.data.length)
		skyline_prev--;
	if(skyline_next >= skyline.data.length) // to plot last graph
		skyline_next = skyline_prev;
	if(skyline.debug) {
		//log(s.status, s.direction, skyline_float, skyline_prev, skyline_next, transition, skyline.cur_frame);
		//$("pre").text(sprintf("float = %.3f\nprev  = %f\nnext  = %f\ntrans = %.3f\nframe = %d",
		//	skyline_float, skyline_prev, skyline_next, transition, skyline.cur_frame));
		//skyline.progress();
	}

	skyline.plot_frame(skyline_prev, skyline_next, transition);

	if(s.direction == "forward" && skyline.cur_frame++ >= skyline.max_frames ||
	   s.direction == "backward" && skyline.cur_frame-- == 0)
		skyline.stop();
}

skyline.loop = function() {
	// this function is called by setinterval, Tut
	// Gewichten mit (skyline.times[frame_index] / skyline.times_sum),
	// welches darstellt wie lange ein skyline-bild dargestellt werden soll.
	
	var s = skyline;

	// check if we already passed beyond the next frame_timestep
	if(s.cur_skyline < s.data.length &&
	   s.cur_frame > s.frame_times[s.cur_skyline+1])
		s.cur_skyline++;

	// frame_time: The time (in s.cur_frame units) of the last picture in s.data
	prev_frame_time = s.frame_times[s.cur_skyline];
	next_frame_time = s.frame_times[s.cur_skyline+1];
	// transition: The percentage to the next picture in s.data 
	transition =  (s.cur_frame - prev_frame_time) / ( next_frame_time - prev_frame_time);
	// the previous and next skyline index
	skyline_prev = s.cur_skyline;
	skyline_next = s.cur_skyline+1;
	if(skyline_next >= s.data.length)
		skyline_next = skyline_prev;

	// debugging output
	if(skyline.debug) {
		$("pre").text(sprintf("cur_skyline = %d\ncur_frame = %d\nprev_frame_time = %.3f\ntransition = %.3f\nprev = %d\nnext = %d\n",
			s.cur_skyline, skyline.cur_frame, prev_frame_time, transition, skyline_prev, skyline_next));
		skyline.progress();
	}

	skyline.plot_frame(skyline_prev, skyline_next, transition);
	if(skyline.cur_frame++ >= skyline.max_frames)
		skyline.stop();
}

/**
 * Plot the current skyline graph
 *
 * @param prev skyline id (id in data array) for previous skyline element
 * @param next same for next skyline element
 * @param due  Percentage value [0,1] about the transition progress
 **/
skyline.plot_frame = function(prev, next, due) {
	skyline.bg();
	
	if (skyline.c === undefined){
		log('skyline.c is undefined. Please call skyline.setup() first');
		return;
	}
	ctx = skyline.c;
	stepsize = 1; // pixelaufloesung

	//skyline.plot(skyline_last);

	ctx.beginPath();
	ctx.moveTo(0,skyline.height);
	for(x=0; x < skyline.width; x+=stepsize) {
		p = skyline.data[prev][x];
		n = skyline.data[next][x];
		y = (p < n) ?  (n-p)*due + p
                            :  p - (p-n)*due;
                    
		ctx.lineTo(x, skyline.height - y);
	}
	ctx.lineTo(skyline.width, skyline.height);

	ctx.closePath();
	ctx.fill();
	//ctx.stroke();
}

skyline.plot = function(skyline_index) {
	// plot a single skyline data as stroke.
	// it is currently only used for debugging
//	skyline.bg();

	ydata = skyline.data[skyline_index];
	
	if (skyline.c === undefined){
		log('skyline.c is undefined. Please call skyline.setup() first');
		return;
	}
	ctx = skyline.c;
	stepsize = 2; // schrittgroesse in elementen des datenarrays = pixeln

	ctx.save();
	ctx.strokeStyle = "red";
	ctx.beginPath();
	ctx.moveTo(0,skyline.height);
	for(x=0; x < skyline.width; x+=stepsize) {
		ctx.lineTo(x, skyline.height - ydata[x]);
	}
	ctx.lineTo(skyline.width, skyline.height);

//	ctx.closePath();
	//ctx.fill();
	ctx.stroke();
	ctx.restore();

//	$("#cur").text(skyline_index);
//	skyline.prog.value = skyline_index;
}

skyline.setup_times = function() {
	// ein bisschen Zeiten-Akrobatik

	// anzahl animationseinzelbilder (Frames)
	skyline.max_frames = skyline.total_duration / 1000 * skyline.fps;
	skyline.cur_frame = 0;
	skyline.cur_skyline = 0;

	/*
	   Es gibt folgende Zeitachsen, zwischen denen umgerechnet wird:

           frame index: |---------------*---------------------| skyline.max_frames
           Index der die zu rendernden Frames durchgeht. skyline.cur_frame = aktuell.

           In den JSON-Daten stehen dann eine Menge "Graphen", welche Momentaufnahmen
           zu gewissen Zeiten sind. Der Index auf skyline.data geht diese Graphen
           durch:          

           skyline idx: 0-----1------2------3-----4------5----| skyline.data.length

           Im einfachsten "Jeder Graph gleichlang"-Modell konnte die Umrechnung
           von frameindex zu skyline-index leicht durchgeführt werden:

              skyline_float = frame_index / skyline.max_frames * skyline.data.length;
              skyline_prev = Math.floor(skyline_float); // id of last skyline id
              skyline_next = Math.ceil(skyline_float);  // id of next skyline id
              transition = skyline_float - skyline_last; // value between [0,1]

           Dann muss man nur noch den passenden "Zwischengraph" ausrechnen und zeichnen,
           was plot_frame() macht.
           Will man aber nicht alle Bilder gleichlang haben, dann muss ein Zeitcode
           dazu. Ich hab mich fuer das Array skyline.times entschieden, welches mit
           dem gleichen Index wie skyline idx durchgegangen werden kann und jedem Bild
           zuordnet, wie lange es zu sehen sein soll.
        */

	// Summe der zeiten
	skyline.times_sum = 0;
	// poor man's fold: skyline.times.reduce(function(x,y){return x+y;});
	$.each(skyline.times, function(i,x){ skyline.times_sum += x; });

	// ordne jeder "times" einen "Absolutwert" in frame-index-Einheiten zu
	var cur_sum = 0;
	// (NB: map funktioniert nur, wenn es nicht-parallel implementiert ist!)
	skyline.frame_times = $.map(skyline.times, function(t){
		var cur_fidx = t  * skyline.max_frames / skyline.times_sum;
		var old_sum = cur_sum;
		cur_sum += cur_fidx;
		return old_sum;
	});
	// noch einen dazuhaengen
	skyline.frame_times.push(skyline.max_frames);
}


/**
 * Setup the skyline data. Das bietet sich beim ersten Laden der JSON-Daten
 * an.
 * @param skyline_data Die JSON-Daten (als Datenstruktur, nicht String)
 * @param $canvas  JQuery-Objekt auf Canvas
 *
 **/
skyline.setup = function($canvas, skyline_data, callback) {
	// json-daten extrahieren/kopieren
	skyline.times = skyline_data['times'];
	// skyline_data['yval'] ist eine 1d-Liste mit y-Achsen.
	skyline.data = skyline_data['yval'];

	// Datendimension setzt Canvasbreite
	skyline.width = skyline.data[0].length;

	// setupt canvas
	$canvas.attr("width", skyline.width);
	$canvas.attr("height", skyline.height);
	skyline.c = $canvas[0].getContext('2d');

	skyline.setup_plot();
	skyline.setup_times();
	skyline.finished_callback = callback;
};

// Kann nur aequiabstaende zwischen den Bildern, aber auch rueckwaerts
skyline._start_simple = function() {
	if(skyline.status == skyline.StatusEnum.stopped) {
		// Counter zurueckstellen
		skyline.cur_frame = skyline.direction == "forward" ? 0 : skyline.max_frames;
		skyline.cur_skyline = skyline.direction == "forward" ? 0 : skyline.data.length;
	}

	skyline.interval = setInterval(skyline.simple_loop, 1000 / skyline.fps  );
	skyline.status = skyline.StatusEnum.running;
}

// Kann Times-Array verwenden, aber nicht rueckwaerts laufen
skyline._start_with_times = function() {
	skyline.interval = setInterval(skyline.loop, 1000 / skyline.fps  );
	skyline.status = skyline.StatusEnum.running;
};

// Der Startknopf.
skyline.start = skyline._start_simple;

// Animation pausieren, mit start() wieder weitermachen.
skyline.pause = function() {
	if(skyline.status) {
		clearInterval(skyline.interval);
		skyline.status = skyline.StatusEnum.paused;
	} else return false;
}

// Animation stoppen, auf Wunsch keinen Callback ausführen
skyline.stop = function(do_not_run_callback) {
	clearInterval(skyline.interval);
	// Counter zuruecksetzen: (fuer _start_simple ueberfluessig)
	skyline.cur_frame = 0;
	skyline.cur_skyline = 0;
	skyline.status = skyline.StatusEnum.stopped;
	if(!do_not_run_callback && typeof skyline.finished_callback === 'function')
		skyline.finished_callback();
	if(skyline.debug)
		console.log("Stopped!");
}

// Public-Kontrollfunktionen
skyline.is_running = function() { return skyline.status; }
skyline.set_backward = function() { skyline.direction = "backward"; }
skyline.set_forward = function() { skyline.direction = "forward"; }
skyline.is_forward = function() { return skyline.direction == "forward"; }
skyline.toggle_direction = function() { skyline.direction = skyline.is_forward() ? "backward" : "forward"; }

// Change the speed of the animation
skyline.set_duration = function(time_in_ms) {
	skyline.total_duration = time_in_ms;
	skyline.setup_times();
}


//$(skyline.setup);
