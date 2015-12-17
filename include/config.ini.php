<?php
	include('include/function.php');
	//Protect the $_GET and $_POST variables
	//session management
	include('include/session.php');
	//lang management
	include('include/lang.php');
	//Database connect
	try {
    /**************************************
    * Create databases and                *
    * open connections                    *
    **************************************/

    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:database.sqlite3');
    // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE,
                            PDO::ERRMODE_EXCEPTION);
  }
  catch(PDOException $e) {
    // Print PDOException message
    echo "failed : ".$e->getMessage();
  }


?>
