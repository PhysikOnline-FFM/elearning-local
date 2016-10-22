#!/bin/bash

# lessc is not installed on itp-servers -> using local install
# also cmd 'node' does not work is called 'nodejs' on this system
# -> change 'node' in first line in 'node_modules/less/bin/lessc'
# to 'nodejs' - LG 02.10.16

if [ ! -f ./node_modules/less/bin/lessc ]; then
  echo "ERROR: Seems like there is no lessc file. Have you installed less locally \
using 'npm install less'? Also please have a look at the comments \
in this file."
  exit 1
fi

node_modules/less/bin/lessc ./less.d/po-v5.1.less ./po-v5.1.css

exit 0