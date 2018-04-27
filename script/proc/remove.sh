#!/bin/bash

purename=$1
processingUser="phsbqz"

wholeEntry=$(qstat -a | grep $processingUser | grep ${purename::16} | sed 's/\.moo.*/.moo/')
qdel $wholeEntry
<<<<<<< HEAD
echo $?
=======
qdel $?
>>>>>>> a80eada8f23558bb06094a64fc058e69c17f20d3
