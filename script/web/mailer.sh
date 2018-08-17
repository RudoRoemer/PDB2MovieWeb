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
echo "from: no-reply@warwick.ac.uk"
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
echo "keep list: " ${12}
echo "modes: " ${13}
echo "cutoffs: " ${14}
echo "date submitted: " ${15}
echo "-----"
echo "Your User Reference Code: " ${16};
echo "Your Comments: " ${17}
echo "-----"
echo "submission command: " ${18}
if [ $doc == "complete.txt" ]; then
  echo "Download Link: https://"$extra
fi
)| /usr/sbin/sendmail -i -f "no-reply@warwick.ac.uk" -- $email
echo $?
