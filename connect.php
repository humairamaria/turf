<?php 

$servername = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'turf';

// Create connection
$conn = new mysqli($servername, $USERNAME, $PASSWORD, $DATABASE);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


//$servername= 'localhost';
//$USERNAME= 'root';
//$PASSWORD='';
//$DATABaSE='turf';


//$conn=new mysqli($servername,$USERNAME,$PASSWORD,$DATABaSE);
//if ($conn->connect_error){
    //echo "connection unsuccesful" .$conn->connect_error;

//}else{
    //echo 'connected';
//}
//$conn->close();

?>