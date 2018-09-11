#!/bin/bash

purename=$1
processingUser="$USER"

#wholeEntry=$(qstat -a | grep $processingUser | grep ${purename::16} | sed 's/\.moo.*/.moo/')
#qdel $wholeEntry

wholeEntry=$(squeue | grep $processingUser | grep ${purename::8} | sed 's/\.moo.*/.moo/' | cut -b 1-22)
scancel $wholeEntry
echo $?

# remove calculation directory
rm pdb_tmp/$purename*
rm -r pdb_tmp/$purename/

rm pdb_des/$purename*

cd ..
