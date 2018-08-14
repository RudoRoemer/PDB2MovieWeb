#!/bin/bash
log=/storage/disqs/$USER/TEST.txt

for file in ./pdb_des/* ; do
	if [[ $(find "$file" -mtime +7 -print) ]]; then
		echo $(find "$file" -mtime +0 -print)>>$log
		rm $file
	fi
done

echo "This ran $(date)">>$log
