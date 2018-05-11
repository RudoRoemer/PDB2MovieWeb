#!/bin/bash
source ../../config.conf
purename=$1
mysql -u $sqlUser -p"$sqlPassword" -D $sqlDB -e"UPDATE Requests SET time_start = CURRENT_TIMESTAMP() WHERE filename='$purename';"
