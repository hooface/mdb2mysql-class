mdb2mysql-class
=======================
This class is an convertor, which exports Microsoft Access Databases to MySQL.

Examples
--------
```php
<?php
include_once('mdb2mysql.class.php');
$m2m = new mdb2mysql();

//mysql connection data
$m2m->mysql = array("host" => "localhost", "user" => 'root', "password" => 'examplepw', "database" => 'exampledatabase');

//mdb source file
$m2m->dbq = '/full/path/to/source.mdb';

//start the sequence
$m2m->start();
?>
```

License
-------

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public License
as published by the Free Software Foundation; either version 2.1
of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
