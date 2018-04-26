#!/bin/bash

purename=$1
processingUser="phsbqz"

wholeEntry=$(qstat -a | grep $processingUser | grep ${purename::16} | sed 's/\.moo.*/.moo/')
qdel $wholeEntry
qdel $?
