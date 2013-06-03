#!/bin/bash

TARGET="physik.css"
DIR="physik.css.d"

cat <<EOF > $TARGET
/* Physik eLearning NG (Okt 2011) Template
   DIESE DATEI NICHT DIREKT BEARBEITEN!!!!!!!

   This file was composed from the physik.css.d/ subdirectory files automatically by the
   physik.css.update program. Any local changes will be lost!
   +++ Generated on $(date) +++
*/
EOF

for file in $DIR/*.css;
	do echo "/*** $file START ***/" >> $TARGET
	cat $file >> $TARGET
	echo "/*** $file END ***/" >> $TARGET
done;

echo "/* EOF */" >> $TARGET
