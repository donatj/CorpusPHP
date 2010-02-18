CorpusPHP: The PHP Framework for the Rest of Us
=============================================

* * *

Overview
--------

Many PHP scripts today have major issue, a dependency on global variables.  
The purpose of CoprusPHP is to resolve this by encapsulting scripts into modules.

Installation
------------

File Permissions

#### Set writeable:	 
`cache/`

#### Database
1. Create a database  
	The Database should be `utf8` with a collation of `utf8_general_ci`
2. Execute `Corpus.sql` in your newly created database.

Configuration
-------------

Initially, set your database connection information and file system constants in:

`includes/configure.php`

Other configuration options are availble in the `config` table.