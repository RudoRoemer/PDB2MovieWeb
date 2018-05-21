#!/bin/bash
source "../../../configs.conf"
find $localScripts/download/* -mtime +7 -exec rm {} \;
mysql -u $sqlUser -p"$sqlPass" -D $sqlDB -e"UPDATE Users SET current_requests = 0;"
