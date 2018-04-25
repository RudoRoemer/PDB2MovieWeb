#!/bin/bash

purename=$1
processingUser="phsbqz"

wholeEntry=$(qstat -x | xmllint --format - | xml_grep "Job" --text_only | grep $processingUser | grep $purename | sed 's/\(.\)/\1\n/g')

arr=($wholeEntry)
echo $arr

tmp1=""
tmp2=""
for i in "${arr[@]}"
do
	tmp1=$tmp2
	tmp2=$i
	curText=${curText}$i
	echo $curText
	if [ "$tmp1$tmp2" == ".c" ]; then
		qdel ${curText%.*}
		exit	
	fi
done
