# Warning: This is the unsupported "CorpusPHP" framework. 

For the more modern "Corpus Components" set of libraries, see https://github.com/CorpusPHP

---

CorpusPHP: The PHP Framework for the Rest of Us
===============================================

More information available at the [CorpusPHP](http://donatstudios.com/CorpusPHP) homepage.

Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

Overview
--------

Most PHP scripts today have major portability issues, caused largely by a dependency on global variables.  
The purpose of CoprusPHP is to resolve this by encapsulting todays procedural scripts into modules outside the global namespace.

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

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/donatj/corpusphp/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

