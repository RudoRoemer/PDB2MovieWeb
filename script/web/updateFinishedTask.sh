#!/bin/bash
source ../../config.conf
purename=$1
mysql -u $sqlUser -p"$sqlPassword" -D $sqlDB -e"UPDATE Requests SET time_comp = CURRENT_TIMESTAMP() WHERE filename='$purename' AND complete = 0;"
mysql -u $sqlUser -p"$sqlPassword" -D $sqlDB -e"UPDATE Requests SET complete = 1 WHERE filename='$purename';"
