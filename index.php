<html>
<head>
<title>Connecting MySQL Server</title>
</head>
<body>
<?php
	$conn = connect_conn();

	$db_name = 'eyoungdb';
//	$tbl_name = 'ZL_tbl_1';

//	create_db($conn, $db_name);

	select_db($conn, $db_name);
	show_tbl($conn,'tbl_user');

//	create_tbl($conn, $tbl_name);

	//$retval = delete_db($conn, $db_name);

	close_conn($conn);

//=================================================================================================================
	function connect_conn(){
		//$dbhost = 'localhost:3306';
		//$dbuser = 'root';
		//$dbpass = 'root';
		$dbhost = '121.41.104.220';
		$dbuser = 'root';
		$dbpass = 'Cccc1111';
		$conn = mysql_connect($dbhost, $dbuser, $dbpass);
		if(! $conn ){
			die('Could not connect: ' . mysql_error());
		}
		else{
			echo 'Connected successfully<br>';
		}
		return $conn;
	}
	
	function create_db( $conn, $db_name ){
		$sql = 'CREATE DATABASE '.$db_name;
		$retval = mysql_query($sql, $conn);
		if(!$retval){
			if( mysql_errno() != 1007 ){
				die('Could not create database:' .mysql_error().mysql_errno().'<br>');
			}
			else{
				echo "Database ".$db_name." already exist<br>";
			}
		}
		else{
			echo "Database ".$db_name." created successfully<br>";
		}
		return $retval;
	}

	function select_db( $conn, $db_name ){
		$retval = mysql_select_db( $db_name );
		if(!$retval){
			die('Could not select database:' .mysql_error().mysql_errno().'<br>');
		}
		else{
			echo "Database ".$db_name." selected successfully<br>";
		}
	}

	function create_tbl( $conn, $tbl_name){
		$sql = "CREATE TABLE ".$tbl_name." ( ".
				"ZL_id INT NOT NULL AUTO_INCREMENT, ".
				"ZL_title VARCHAR(100) NOT NULL, ".
				"ZL_author VARCHAR(40) NOT NULL, ".
				"submission_data DATE, ".
				"PRIMARY KEY( ZL_id )".
				");";
		$retval = mysql_query($sql, $conn);
		if(!$retval){
			if( mysql_errno() != 1050 ){
				die('Could not create table: '.mysql_error().mysql_errno());
			}
			else{
				echo "Table ".$tbl_name." already exist<br>";
			}
		}
		else{
			echo 'Table '.$tbl_name.' created successfully<br>';
		}
		return $retval;
	}

	function show_tbl( $conn, $tbl_name){
		$sql = "SHOW TABLES FROM eyoungdb ";
		//$sql  = "SHOW FULL COLUMNS FROM tbl_user ";
		//$sql = "SHOW COLUMNS FROM tbl_user";
		$retval = mysql_query($sql, $conn);
		if(!$retval){
			die('Could not select table: '.mysql_error().mysql_errno());
		}
		else{
			echo '<table>';
			while($row = mysql_fetch_row($retval)){
				echo "<tr><td>$row[0]</td></tr>";
				//print_r($row);
			}
			echo '</table>';
		}
		return $retval;
	}

	function select_tbl( $conn, $tbl_name){
		$sql = "SELECT * FROM ".$tbl_name;
		$retval = mysql_query($sql, $conn);
		if(!$retval){
			die('Could not select table: '.mysql_error().mysql_errno());
		}
		else{
			echo '<table>';
			echo '<tr><td>userid</td><td>username</td><td>password</td></tr>';
			while($row=mysql_fetch_row($retval))
				echo '<tr><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td></tr>';
			echo '</table>';
		}
		return $retval;
	}

	function delete_db( $conn, $db_name){
		$sql = 'DROP DATABASE ZL';
		$retval = mysql_query($sql, $conn);
		if(!$retval){
			die('Could not delete database:' .mysql_error());
		}
		echo "Database ".$db_name." deleted successfully<br>";
		return $retval;
	}

	function close_conn($conn){
		$retval = mysql_close($conn);
		if(! $retval ){
			die('Could not close connection ' . mysql_error());
		}
		echo 'Connect closed successfully<br>';
	}
?>
</body>
</html>