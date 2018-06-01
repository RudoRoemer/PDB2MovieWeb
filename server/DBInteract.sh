#!/bin/bash

favourites=0
users=0
requests=0
blacklist=0

add=0
delete=0
select=0
edit=0
help=0

while test $# -gt 0; do
	case "$1" in
		-h|--help)
			echo "To interact with the database with this file, you must use one of the first flags:"
			echo ""
			echo "		-f | --favourites             	: Do something with the favourites table"
			echo "		-u | --users                  	: Do something with the users table"
			echo "		-r | --requests			: Do something with the requests table"
			echo ""
			echo "Then select one of these flags to state what you want to do to the sections:"
			echo ""
			echo "		-a                            	: Add to the database"
			echo "		-d                            	: Delete entry from the database"
			echo "		-s                            	: select from / view the contents of"
			echo "		-e                            	: edit an existing entry in the database" 
			echo ""
			echo "Combinations and their arguments:"
			echo ""
			echo "		-f -a <SUFFIX> [<MAX_REQUESTS>]					: default values for max requests is 5"
                        echo "		-f -d <SUFFIX>							:"
                        echo "		-f -s [<SUFFIX>]						:"
                        echo "		-f -e <SUFFIX> <COLUMN> <NEW_VALUE>				:"
                        echo ""
			echo "		-u -a <NAME> [<NO_REQUESTS>]					: default number of requests is 3."
                        echo "		-u -d <NAME>							:"
                        echo "		-u -s [<NAME>]							:"
                        echo "		-u -e <NAME> <COLUMN> <NEW_VALUE>				:"
                        echo ""
                        echo "		-r -d <NAME>							:"
                        echo "		-r -s [<NAME>]							:"
                        echo "		-r -e <NAME> <COLUMN> <NEW_VALUE>				: Edits the first instance of a requests when NAME is used."
                        echo ""
			echo "Blacklisting"
			echo "		There are two blacklist variables. one in Favourites, and one in Users; both of these are 1|0 values."
			echo "		The User blacklist bans a specific user."
			echo "		The Favourites blacklist bans a specific email suffix."
			echo "		Use -u -e flags with the user's email and a 1|0 to update their blacklist status."
			echo "		User -f -e with the email suffix in question and a 1|0 to update its blacklist status."
			echo ""
			help=1
			shift
			;;
		-f)
			favourites=1
			shift
			;;
		-u)
                        users=1
                        shift
                        ;;
		-r)
                        requests=1
                        shift
                        ;;
		-b)
                        blacklist=1
                        shift
                        ;;
		-a)
			add=1	
			shift
			;;	
		-d)
			delete=1
                        shift
                        ;;
		-s)
			select=1
                        shift
                        ;;
		-e)
			edit=1
                        shift
                        ;;
		*)
			break
			;;
	esac
done

sqlCred="mysql -u <USER> -p"<PASSWORD>" -D <DATABASE>"
operationCheck=$(($add + $delete + $select + $edit))
taskCheck=$(($favourites + $users + $requests + $blacklist))
if [ $taskCheck -ne 1 ] && [ $help -ne 1 ]; then
        echo "Error: Must have only one task flag"
        exit 113
fi
if [ $operationCheck -ne 1 ] && [ $help -ne 1 ]; then
	echo "Error: Must have only one operation flag"
	exit 113
fi

if [ $favourites -eq 1 ]; then
	if [ $add -eq 1  ]; then
		$sqlCred -e "INSERT INTO Favourites (email_suffix, max_requests) VALUES ('$1', ${2:-5})"	
		echo $?
	fi
        if [ $delete -eq 1 ]; then
		read -r -p "Deleting directly from the database can cause issues if you use ambiguous parameters. Are you sure? [y/N] " response
		case "$response" in
    			[yY][eE][sS]|[yY]) 
        			$sqlCred -e "DELETE FROM Favourites WHERE email_suffix='$1'"
				echo $?
				;;
    			*)
        			echo "No changes made"
        			;;
		esac
        fi
        if [ $select -eq 1 ]; then
		if [ -z $1 ]; then	
			$sqlCred -e "SELECT * FROM Favourites"
        		echo $?
		else
			$sqlCred -e "SELECT * FROM Favourites WHERE email_suffix='$1'"			
			echo $?
		fi
	fi
        if [ $edit -eq 1 ]; then
		$sqlCred -e "UPDATE Favourites SET $2=$3 WHERE email_suffix='$1'"	
        	echo $?
	fi		
fi

if [ $requests -eq 1 ]; then
	if [ $add -eq 1  ]; then
		echo "Adding to the database directly will not send a request to a processing server."
        fi
        if [ $delete -eq 1 ]; then
		read -r -p "Deleting directly from the database can cause issues if you use ambiguous parameters. Are you sure? [y/N] " response
		case "$response" in
			[yY[eE][sS]|[yY])
				$sqlCred -e "DELETE FROM Requests WHERE filename='$1'"
				echo $?
				;;
			*)
				echo "No changes made"
				;;
		esac
        fi
        if [ $select -eq 1 ]; then
		if [ -z $1 ]; then
			$sqlCred -e "SELECT * FROM Requests"
			echo $?	
		else
			$sqlCred -e "SELECT * FROM Requests WHERE filename='$1'"
			echo $?
		fi
        fi
        if [ $edit -eq 1 ]; then
		$sqlCred -e "UPDATE Requests SET $2=$3 WHERE filename='$1'"
		echo $?
        fi
fi

if [ $users -eq 1 ]; then
	if [ $add -eq 1 ]; then
		ran=$RANDOM$RANDOM
		sixDig=${ran::6}
		$sqlCred -e "INSERT INTO Users (email, max_requests, current_requests, blacklisted, secret_code) VALUES ('$1',${2:-5},0,0,$sixDig)" "INSERT INTO Favourites (email_suffix, max_requests) VALUES ('$1', ${2:-5})"
        	echo $?
	fi
        if [ $delete -eq 1 ]; then
		read -r -p "Deleting directly from the database can cause issues if you use ambiguous parameters. Are you sure? [y/N] " response
		case "$response" in
			[yY[eE][sS]|[yY])
				$sqlCred -e "DELETE FROM Users WHERE email='$1'"
				echo $?
				;;
			*)
				echo "No changes made"
				;;
		esac	
        fi
        if [ $select -eq 1 ]; then
		if [ -z $1 ]; then
			$sqlCred -e "SELECT * FROM Users"
			echo $?
		else
			$sqlCred -e "SELECT * FROM Users WHERE email='$1'"
			echo $?
		fi
        fi
        if [ $edit -eq 1 ]; then
		$sqlCred -e "UPDATE Users SET $2=$3 WHERE email='$1'"
		echo $?
        fi
fi

if [ $blacklist -eq 1 ]; then
	echo "Blacklist table has been deprecated. Blacklist properties have been added to Users and Favourites tables."
fi

