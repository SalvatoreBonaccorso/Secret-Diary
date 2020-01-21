<?php 

    // inizializziamo la sessione
    session_start();

    // verifichiamo se c'è il valore del cookie
    if (array_key_exists("id", $_COOKIE)) {
        
        // associamo il valore del cookie alla sessione
        $_SESSION['id'] = $_COOKIE['id'];
        
    }

    // verifichiamo se esiste la variabile in sessione
    if(array_key_exists("id", $_SESSION)) {
        echo "<p>Utente loggato. <a href='index.php?logout=1'>Log out</a></p>";
    }
    else{
        header("Location: index.php");
    }
//         include 'connection.php';
//         $diary='';
//         //echo 'test';
//         $sql ="select diary from users where id=".(int)$_SESSION['id'];

//         $query= mysqli_query($link,$sql);
//         $row = mysqli_fetch_array($query);
//         $diary = $row['diary'];
//     } else {
        
//         header("Location: index.php");
        
//     }

// include 'header.php';



?>