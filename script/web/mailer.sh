#!/bin/bash

email=$1
subj=$2
msg=$3

mail -r no-reply@warwick.ac.uk -s $subj $email <<EOF
$msg
EOF
