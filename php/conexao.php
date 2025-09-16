<?php
    $dbhost= "localhost";
    $dbuser= "root";
    $dbpass= "";
    $dbname= "db_conecta";

    $conn = new mysqli ($dbhost, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Conexão falhou: $conn->connect_error");
    }
?>