<?php
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'inventory'; 

    try {
        // Create a new PDO instance and establish a connection
        $conn = new PDO("mysql:host=$servername;dbname=inventory", $username, $password);
        
        // Set the PDO error mode to exception to handle errors gracefully
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully.";
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>

