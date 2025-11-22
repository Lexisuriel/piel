<?php
session_start();
class Database{
	
<<<<<<< HEAD
	private $host  = 'localhost';
    private $user  = 'root';
    private $password   = "";
    private $database  = "piel"; 
=======
	private $host  = 'mysql-lexisuriel.alwaysdata.net';
    private $user  = '439233';
    private $password   = "2929*210*18*22Lu";
    private $database  = "lexisuriel_piel20"; 
>>>>>>> fb84d44eb42953d6901f16e1b620003264f9bcb9
    
    public function getConnection(){		
		$conn = new mysqli($this->host, $this->user, $this->password, $this->database);
		if($conn->connect_error){
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			return $conn;
		}
    }
}
?>