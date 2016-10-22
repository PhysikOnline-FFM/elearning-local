#!/usr/bin/python
from pylab import *
from scipy import ndimage

# sage portablilitaet
import pylab as pl
DATA = ""

# diese version entstanden nach pokal
ion()

skyline = imread("skyline.png")

# farben rausschmeissen, BW-Bild machen
tresh = 0.5
skyline = array([[ 1 if pixel[0] > tresh else 0 for pixel in line ] for line in skyline])

xmax,ymax = skyline.shape

# Elemente des Graphen = Laenge Y-Achse
skyline_1d = ndarray(ymax)

# find first 1 in each row
for i_col,col in enumerate(rot90(skyline)):
	skyline_1d[i_col] = xmax
	for i_row,row in enumerate(col == 1):
		if row:
			skyline_1d[i_col] = i_row
			break
# figur kippen
skyline_1d = xmax - skyline_1d[::-1]

# rest ani 

# dafuer sorgen, dass skyline_1d[0] = skyline_1d[N] = 0, also
# kein absoluter Shift in einer Fourierentwicklung auftritt.
# dazu links und rechts mit ein paar Nullen padden
#skyline_1d = pad(skyline_1d, (10,10), 'constant', constant_values=(0,0))

# funktioniert, aber *nicht* machen, da sonst PNG und Canvas nicht mehr uebereinstimmen
# das Originalbild hat schon ausreichend nullen links und rechts
#padding = [0] * 15
#skyline_1d = array(padding + list(skyline_1d) + padding)


# nun: Entwicklung durch Fouriermoden
# dazu ein paar Skalen wg Interplation:
skyline_len = skyline_1d.size # size == shape[0] in 1d
# X-Achse von skyline_1d
X_skyline = linspace(0, skyline_len, skyline_len)

# Fourier-Komponenten (komplexwertig) (Reale FFT)
sft = rfft(skyline_1d)

# Teile der Fourier-Entwicklung mitnehmen
def skyent(tresh):
	l = copy(sft)
	l[abs(l) < tresh] = 0 # zero out low values
	r = irfft(l) # # inverse berechnen, shift empirisch
	r[r < 0 ] = 0 # negative terme abschneiden
	# es gibt ohnehin fast keine negativen terme
	return r

# 
# human file size printing
def sizeof_fmt(num):
    for x in ['bytes','KB','MB','GB','TB']:
        if num < 1024.0:
            return "%3.1f %s" % (num, x)
        num /= 1024.0
def print_stat(label, data, anzahl_daten):
    print " * %s:\t%s  \t(%.2f Byte/Zahl)" % (label, sizeof_fmt(len(data)), len(data) / float(anzahl_daten))

# Anzahl gewuenschter Bilder (Zeitdiskretisierung)
#N = len(sft)
N = 100

# mitnamestrategie histogram
#count,hist = pl.histogram(abs(sft.real), bins=N)
#dyn_t = hist[::-1]

# mitnahmestrategie exp
#dyn_t = sort(abs(sft.real))[::-1][::int(len(sft)/N)]
#dyn_t = sort(abs(sft.real))[::-1] 

# Neue Untersuchungsmoeglichkeiten

def uebergang(maxabs_liste):
	ax = figure()
	imshow( np.array( [ skyent(x) for x in maxabs_liste ]),
		extent=[0, len(skyent(0)), maxabs_liste[-1], maxabs_liste[0]],
		aspect = 'auto' )
	#ax.set_aspect(1)

def spline(abweichung, length):
	from scipy import interpolate
	x = linspace(0, 1, 5)
	y = array([0, 0.5 - abweichung, 0.5, 0.5 + abweichung, 1 ])
	#s = interpolate.PchipInterpolator(x,y)
	s = interpolate.InterpolatedUnivariateSpline(x,y)
	xnew = linspace(0, 1, length)
	ynew = s(xnew)
	figure(); plot(x, y, 'x', xnew, ynew)
	return ynew

# rausfinden wo die Party steigt:
maxabsis = linspace(6,5200,2000)
d2 = np.array( [ skyent(x) for x in maxabsis ]) # imshowable
aenderungen2d = diff(d2, axis=0) # imshow(,aspect="auto")
aenderungen = abs(sum(aenderungen2d, axis=1))
figure()
# hier sieht man die wichtgsten Aenderungen bei welchen Absolutwerten sie passieren
semilogx(maxabsis[:-1], aenderungen, "o-")

# Liste daraus machen:
meine_beitraege = list( sort(maxabsis[ argsort(aenderungen)[::-1] ][:15])[::-1] )

meine_beitraege = [1300,960] + meine_beitraege + [10, 1]



if 1:
	# wichtigste beitraege, auf 10er gerundet
	wichtigster_beitrag = floor(sort(abs(sft.real))[::-1][0:20]/10) * 10

	print wichtigster_beitrag

	#meine_beitraege    = [8000, 5320, 1500, 1180, 960, 900, 700, 580, 540, 430, 300, 160, 55, 10, 1]

	# herausgefunden durch flaechen, bei denen sich nicht viel tut.
	# Plot dazu: uebergang(linspace(1,945,600))
	#meine_beitraege     = [ 920, 904, 834, 700, 540, 510, 440, 400, 360, 270, 188, 140, 30, 1]



	#times              = [    1,   1,    2,    1,   1,   1,    1,  2,   1,   1,   2,   1,  1,  1, 1]
	times = [1] * len(meine_beitraege)
	#meine_beitraege = [1400, 1180, 600, 300, 1]

	dyn_t = meine_beitraege


	# Raley
	#dyn_list2 = sort(abs(sft.real))[::-1]
	#from scipy.stats import *
	#rv = rayleigh(scale=30., loc=100)
	#dyn_t = rv.sf( linspace(0, N, N) ) * dyn_list2

	# daten generieren und runden
	movie = [list(around(skyent(t), decimals=2)) for t in dyn_t]

	# von exakt zu ungenau laufen lassen
	movie = movie[::-1]

	# dafuer sorgen, dass in den letzten Bild links und rechts auf null runtergeht
	movie[-1] = list(array(movie[-1]) * sin(linspace(0, pi, len(movie[-1]))))
	# ein Nullbild dranhaengen (alles auf 0)
	movie.append([0.]*len(movie[0]))

	count_data = len(dyn_t)*len(skyent(1))
	print "Mit N=%i Schritten, je d=%i Daten pro Zeit:" % (N, len(skyent(1)))

	# build json structure
	json_struct = { "times": times, "yval": movie }

	# json encode
	import json
	json_list = json.dumps(json_struct)

	# mit nem regex mist lange zahlen rausfiltern
	# vgl. http://stackoverflow.com/questions/1447287/format-floats-with-standard-json-module
	import re
	json_list = re.sub(r'(\d+)\.(\d{2})\d+', "\\1.\\2", json_list)
	json_list = json_list.replace("0.0", "0.")
	json_list = json_list.replace(" ", "") # remove whitespace

	# daten prefixen
	from datetime import datetime as dt
	json_data = "// PhysikOnline Frankfurt Skyline Fouriermoden-Graphdaten \n" + \
		"// JSON generiert am %s durch pokal.uni-frankfurt.de\n" % dt.now().ctime() + \
		"skyline_data="+json_list+";"

	# laenge angeben
	print_stat("Laenge der puren JSON-Daten", json_data, count_data)

	# gzip/whatever compression (like to be done by HTTP deflate
	json_compressed = json_data.encode("zlib")
	print_stat("Laenge des gezipped JSON", json_compressed, count_data)

	# theorie: Bestes Binary Json-Ergebnis
	sizeof_float = 2 # in byte. 16 bit float, 32 bit double.
	print_stat("Laenge von binary JSON", sizeof_float*list(array(movie).flatten()), count_data)

	# in Datei schreiben...
	filename = "skyline_json.js"
	with open(DATA+filename, "w") as text_file:
	    text_file.write(json_data)
