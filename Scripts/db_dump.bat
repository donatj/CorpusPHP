@ECHO OFF

SET mysqlpath=
SET mysqlhost=
SET mysqlusername=
SET mysqlpassword=
SET mysqldb=

IF EXIST ../Local/db.bat CALL ../local/db.bat
IF NOT DEFINED mysqlpath SET /P mysqlpath=MySQL Path:
IF NOT DEFINED mysqlhost SET /P mysqlhost=MySQL Host:
IF NOT DEFINED mysqlusername SET /P mysqlusername=MySQL Username:
IF NOT DEFINED mysqlpassword SET /P mysqlpassword=MySQL Password:
IF NOT DEFINED mysqldb SET /P mysqldb=MySQL Database:

%mysqlpath%\mysqldump --opt --compact --host=%mysqlhost% --user=%mysqlusername% --password=%mysqlpassword% %mysqldb% > ../corpus.sql

ECHO Completed Database Dump
PAUSE