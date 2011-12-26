<?php
/**
 * @author Gianluca Raberger
 * @version 1.0.0
 * @package mdb2mysql
 */

set_time_limit(0);

class mdb2mysql {

	public $dbq = null;
	protected $connectionType = "ADODB.Connection";
	
	public $mysql = array("host" => "localhost", "user" => 'user', "password" => NULL, "database" => NULL);
	
	protected $mysqlLink;
	protected $db_connection;


	public function start() {
		if($this->preCheck() === true) {
		$this->mysqlLink = mysql_connect($this->mysql['host'], $this->mysql['user'], $this->mysql['password']);
		mysql_select_db($this->mysql['database']);
		$this->db_connection = odbc_connect('DRIVER={Microsoft Access Driver (*.mdb)};Dbq='.$this->dbq ,$this->connectionType, '');

		$r = odbc_tables($this->db_connection);
		$tables = array();
		while($object = odbc_fetch_object($r)) {
		    if($object->TABLE_TYPE=='TABLE') {
			$tables[]=$object->TABLE_NAME;
		    }
		}
		foreach($tables as $k => $v) {
		    $this->exportTable($v);
		}
		exit('done');
		}
	}
	protected function exportTable($tableName) {
	    $time1 = microtime(true);
	    echo $tableName.' import started.<br/>';
	    $sql = "DROP TABLE `".addslashes($tableName)."`";
	    mysql_query($sql);

	    $sql = "SELECT * FROM ".$tableName;

	    $r = odbc_exec($this->db_connection,$sql);
	    $first = true;

	    while($object = odbc_fetch_object($r)) {
		if($first) {
		    $columns = $this->createMysqlTable($tableName,$object,$r);
		    $columnNames = array_keys($columns);
		    foreach($columnNames as $k => $v) {
			$columnNames[$k]=strtolower($v);
		    }
		}
		$first = false;
		foreach($columns as $k => $v) {
		    $columns[$k] = addslashes($object->$k);
		}
		$sql = "INSERT INTO `".$tableName."` (`".implode('`, `',$columnNames)."`) VALUES ('".implode("', '",$columns)."')";
		mysql_query($sql);

	    }
	    $total = number_format(microtime(true)-$time1,3);
	    echo $tableName.' exported: '.$total.' S<br/>';
	}
	
	protected function createMysqlTable($tableName,$object,$r) {
	    $parts = array();
	    $i = 1;
	    foreach($object as $k => $v) {
		switch(strtolower(odbc_field_type($r,$i))) {
		    case 'varchar':
		    case 'counter':
		    case 'double':
		    case 'integer':
		    case 'real':
		    $parts[odbc_field_name($r,$i)]=array('type'=>'varchar','length'=>'255');
		    $sColumns[(odbc_field_name($r,$i))]=true;
		    break;
		    case 'longchar':
		    $parts[odbc_field_name($r,$i)]=array('type'=>'longtext','length'=>'255');
		    $sColumns[(odbc_field_name($r,$i))]=true;
		    break;
		    case 'bit':
		    $parts[odbc_field_name($r,$i)]=array('type'=>'tinyint','length'=>'1');
		    $sColumns[(odbc_field_name($r,$i))]=true;
		    break;
		    case 'byte':
		    case 'smallint':
		    $parts[odbc_field_name($r,$i)]=array('type'=>'smallint','length'=>'255');
		    break;
		    default:
		    exit($tableName.'.'.odbc_field_name($r,$i).': '.odbc_field_type($r,$i).' unknown column type.');
		}
		$i++;
	    }
	    $columns = $parts;
	    $parts = array();
	    $sql = "CREATE TABLE `".$tableName."`(";
	    foreach($columns as $k => $v) {
		if($v['type']=='longchar') {
		    $parts[] = "`".strtolower($k)."` ".$v['type']." NOT NULL";
		}
		else {
		    $parts[] = "`".strtolower($k)."` ".$v['type']."(".$v['length'].") NOT NULL default ''";
		}
	    }
	    $sql .= implode(",\n",$parts);
	    $sql .= ") type = InnoDB";
	    mysql_query($sql);
	    return $sColumns;
	}
	
	protected function preCheck() {
		$errorTotal = 0;
		$errors = array();
		
		//checking PHP Version
		if(!(version_compare(PHP_VERSION, '5.3.0') >= 0)) {
			$errorTotal++;
			$errors[] = "PHP Version is older than 5.3.0";
		}
		if(!function_exists('mysql_connect')) {
			$errorTotal++;
			$errors[] = "MySQL is not supported";
		}
		elseif(!mysql_connect($this->mysql['host'], $this->mysql['user'], $this->mysql['password'])) {
			$errorTotal++;
			$errors[] = "Couldn't establish connection to MySQL Server";
		}
		elseif(!mysql_select_db($this->mysql['database'])) {
			$errorTotal++;
			$errors[] = "Couldn't select Database";
		}
		
		if($errorTotal == 0)
			return true;
		else {
			?> 
		<!DOCTYPE html>
		<html>
			<head>
			  <meta charset='utf-8'>
			  <title>mdb2mysql-class</title>
			  <style type="text/css">
				body {
					margin: 0;
					font-family: Helvetica, Arial, FreeSans, san-serif;
					color: #000000;
				}
				#header {
					width: 100%;
					margin-top: 5px;
					font-size: 0.8em;
					padding-bottom:5px;
					border-bottom:1px solid #000;
				}
				#container {
					margin: 0 auto;
					width: 700px;
					padding-top: 40px;
					font-size: 0.8em;
				}
			  </style>
			</head>
		<body>
			<div id="header">
				<strong>&nbsp;mdb2mysql-class</strong>
			</div>
			<div id="container">
				<h2>Error while testing</h2>
				<table>
				<?php
				foreach($errors as $values) {
				?>
				<tr><td><img src="data:image/gif;base64,R0lGODlhEAAQANUAAO8AAP3ExPdBMfkoI/5RUfEaE//x8e4SEv9GQvcyMv7q6v8AAP9pXPhKO/k6
		OvYcF/tWSP5hVPUJCf8TE/ouKfUfGf/39/dKQv/MzP8zM/xKSv1eUf5mWfwoKPYQEO8XF/okHvcA
		APw7N/wmIftXSfpSQ/9aV////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
		AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEHACcALAAAAAAQABAAAAaQwJNw
		SCwWJ4ukcmI8TToMjpQTGXmOUERmSxFtQFfhk2HCECgUDUYDAQsXHATGYNBoFAYMpQR4czIEeHR0
		Dg8NfScLERlpggYOHh6HbxEdHXeDkJKICxsdCI4GCgkSApwbGXMKDg54AQ+mQh4gJHKaCQEJAgUf
		Q7MlA5EeErC8RR4VDcoCzMZGBwDR0gdN1UVBADs=" /></td><td><?php echo $values; ?></td></tr>
				<?php } ?>
				</table>
			</div>
		</body>
		</html>
	<?php exit();
		}
	 }
}
?>