#!/bin/bash

purename=$1
log=/var/www/html/query.txt

mysql -u admin -p'penicillin_loves_beta_lactamase' -D db_pdb2movie -e"UPDATE Requests SET complete = 1 WHERE filename='$purename';"

echo $query>>$log
eval $query
