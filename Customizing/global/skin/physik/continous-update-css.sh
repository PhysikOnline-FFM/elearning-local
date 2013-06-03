#!/bin/bash

while inotifywait physik.css.d; do
	echo CSS Update um $(date)
	./physik.css-update.sh
done;
