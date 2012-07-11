#!/bin/sh

if [ -f ../Local/db.sh ]; then
	source ../Local/db.sh;
fi

if [ -z "$mysqlhost" ]; then
	echo "MySQL Host (default: 127.0.0.1)";
	read mysqlhost;
	if [ -z "$mysqlhost" ]; then
		mysqlhost=127.0.0.1
	fi;
fi;

if [ -z "$mysqlusername" ]; then
	echo "MySQL Username (default: root)";
	read mysqlusername;
	if [ -z "$mysqlusername" ]; then
		mysqlusername=root
	fi;
fi;

if [ -z "${mysqlpassword+set}" ]; then
	echo "MySQL Password";
	read mysqlpassword;
fi;

if [ -z "$mysqldb" ]; then
	echo "MySQL Database";
	read mysqldb;
fi;

if [ -z "${mysqlpath+set}" ]; then
	echo "MySQL Path (default: /usr/local/mysql/bin/ )";
	read mysqlpath;
	if [ -z "$mysqlpath" ]; then
		mysqlpath=/usr/local/mysql/bin/
	fi;
fi;

PATH="$mysqlpath":$PATH;

mysql --host="$mysqlhost" --user="$mysqlusername" --password="$mysqlpassword" "$mysqldb" -e "source canonization.sql"
mysqldump --opt --compact --host="$mysqlhost" --user="$mysqlusername" --password="$mysqlpassword" "$mysqldb" > ../corpus.sql

echo Completed Database Dump;
