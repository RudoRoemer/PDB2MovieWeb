#!/bin/bash
email=$1
msg=$2
doc=$3
args=$4
extra=$5
(
echo "subject: "$msg
mapfile -t toSend < email-texts/$doc
for i in "${toSend[@]}"
do
  echo $i
done
echo "file: " $args[0]
echo "python file used: " $args[1]
echo "resolution: " $args[2]
echo "combi: " $args[3]
echo "multi: " $args[4]
echo "waters: " $args[5]
echo "threed: " $args[6]
echo "confs: " $args[7]
echo "freq: " $args[8]
echo "step: " $args[9]
echo "dstep: " $args[10]
echo "Keep list: " $args[11]
echo "Modes: " $args[12]
echo "Cutoffs: " $args[13]
echo "Date Submitted: " $args[14]
echo "---------"
echo "Your User Reference Code: " $args[15];
if [ $doc -eq "complete.txt" ]; then
  echo "Download Link: "$extra
fi
)| sendmail -i -- $email
