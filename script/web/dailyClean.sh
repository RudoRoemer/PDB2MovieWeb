#!/bin/bash
source ../../config.conf
find $localScripts/../../html/download/* -mtime +7 -exec rm {} \;
mysql -u $sqlUser -p"$sqlPassword" -D $sqlDB -e"UPDATE Users SET current_requests = 0;"
