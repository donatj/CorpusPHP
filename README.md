CorpusPHP: The PHP Framework for the Rest of Us
===============================================

More information available at the [CorpusPHP](http://donatstudios.com/CorpusPHP) homepage.

Overview
--------

Many PHP scripts today have a major portability issue, a dependency on global variables.  
The purpose of CoprusPHP is to resolve this by encapsulting scripts into modules.

CorpusPHP can be used as a framework, or as an Application to build off.

Requirements
------------

- PHP 5.2.0+, 5.3.0+ Supported
- MySQL >= 5.1

Installation
------------

#### File System

- Move or copy contents of `Source/` to your desired location.
- Set `cache/` writeable:	 


#### Database
1. Create a database  
	- The Database should be `utf8` encoded with a collation of `utf8_general_ci`
2. Execute `corpus.sql` in your newly created database.

Configuration
-------------

Initially, set your database connection information and file system constants in:

`includes/configure.php`

Other configuration options are availble in the `config` table.