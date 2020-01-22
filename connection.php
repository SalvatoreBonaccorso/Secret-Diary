<?php

// dati di connessione al mio database MySQL
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'diario_segreto';

// connessione al database
$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (mysqli_connect_error()) {      
    die ("C'è stato un errore nel collegamento al database");    
}
?>

