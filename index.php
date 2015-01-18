<html>
<head>
<title>Connecting MySQL Server</title>
</head>
<?php
	$conn = connect_conn();
	$db_name = 'ZL';

	$retval = create_db($conn, $db_name);
	if($retval){
		$retval = select_db($conn, $db_name);
		if($retval){
			$retval = delete_db($conn, $db_name);
		}
	}

	close_conn($conn);

	//connect to mysql
	function connect_conn()
	{
	    $dbhost = 'localhost:3306';
	    $dbuser = 'root';
	    $dbpass = 'root';
	    $conn = mysql_connect($dbhost, $dbuser, $dbpass);
	    if(! $conn){
	        die('Could not connect:' .mysql_error());
	    }
	    echo 'Connected successfully<br>';
	    return $conn;
	}

    //create a database
    function create_db($conn, $db_name)
    {
	    $sql = 'CREATE DATABASE '.$db_name;
	    $retval = mysql_query($sql, $conn);
	    if(! $retval){
	    	echo 'Could not create database: '.$db_name.' '.mysql_error().'<br>';
	    }
	    else{
		    echo 'Database '.$db_name.' created successfully<br>';
		}
		return $retval;
	}

    //select database
    function select_db($conn, $db_name)
    {
	    $retval = mysql_select_db($db_name, $conn);
	    if(!$retval){
	    	echo 'Database '.$db_name.' could not selected<br>';
	    }
	    else{
	    	echo 'Database '.$db_name.' selected<br>';
	    }
	    return $retval;
	}

    //delete a database
    function delete_db($conn, $db_name)
    {
	    $sql = 'DROP DATABASE '.$db_name;
	    $retval = mysql_query($sql, $conn);
	    if(!$retval){
	    	echo 'Could not delete database '.$db_name.' '.mysql_error().'<br>';
	    }
	    else{
		    echo 'Database '.$db_name.' deleted successfully<br>';
		}
	}

	function close_conn($conn)
	{
	    mysql_close($conn);
	}
?>
</html>