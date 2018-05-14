#!/bin/bash

purename=$1
processingUser="phsbqz"

wholeEntry=$(qstat -a | grep $processingUser | grep ${purename::16} | sed 's/\.moo.*/.moo/')
qdel $wholeEntry
echo $?

cd pdb_tmp/
rm $purename*
rm -r $purename/

cd ../pdb_des/
rm $purename*

cd ..
