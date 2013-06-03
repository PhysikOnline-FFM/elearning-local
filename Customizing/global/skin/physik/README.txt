Hinweise zur Verzeichnisstruktur, Woche 12.10.2011:

* physik.css.d: Kleines System um CSS aus vielen Dateien im Unterordner zusammenzusetzen.

* images: Softlink zum default template, NICHT BEARBEITEN

* src: Stattdessen ist das hier ein lokales images-Verzeichnis.

Gute Kommandos um Sachen zu finden:

cd /var/www/elearning
find {Services,Modules}/*/templates/default/*.html | xargs grep ilMainHeader

oder suche im default template-Verzeichnis.
