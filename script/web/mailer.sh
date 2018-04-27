#!/bin/bash
email=$1
msg=$2
doc=$3
extra=$4

shift 4 
args=()
for i in $@
do
  args=(${args[@]} $i)
done
echo $args
(
echo "subject: "$msg
mapfile -t toSend < email-texts/$doc
for i in "${toSend[@]}"
do
  echo $i
done
echo "-----"
echo "file: " $1
echo "python file used: " $2
echo "resolution: " $3
echo "combi: " $4
echo "multi: " $5
echo "waters: " $6
echo "threed: " $7
echo "confs: " $8
echo "freq: " $9
echo "step: " ${10}
echo "dstep: " ${11}
echo "Keep list: " ${12}
echo "Modes: " ${13}
echo "Cutoffs: " ${14}
echo "Date Submitted: " ${15}
echo "-----"
echo "Your User Reference Code: " ${16};
if [ $doc == "complete.txt" ]; then
  echo "Download Link: "$extra
fi
)| /usr/sbin/sendmail -i -- $email
echo $?
