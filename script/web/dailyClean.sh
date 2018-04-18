#!/bin/bash
remoteUser=phsbqz
remoteServer=godzilla.csc.warwick.ac.uk
log="/var/www/html/TEST.txt"

echo "This ran $(date), files delete:">>$log

for file in ./download/* ; do
        if [[ $(find "$file" -mtime +7 -print) ]]; then
                echo '    '$file>>$log
                rm $file
        fi
done

ssh $remoteUser@$remoteServer 'cd /storage/disqs/phsbqz && ./garbageCollection.sh'

mysql -u admin -p'penicillin_loves_beta_lactamase' -D db_pdb2movie -e"INSERT INTO RequestsHistory SELECT * FROM Requests;DELETE FROM Requests WHERE filename='$purename';UPDATE Users SET current_requests = 0"
