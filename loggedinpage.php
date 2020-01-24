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

        include 'connection.php';
        $diary='';
        //echo 'test';
        $sql ="SELECT diario FROM utente WHERE id=".(int)$_SESSION['id'];

        $query= mysqli_query($link,$sql);
        $row = mysqli_fetch_array($query);
        $diary = $row['diario'];
    } else {
        
        header("Location: index.php");
        
    }

include 'header.php';

?>

<nav class="navbar  navbar-light bg-light">
    <a class="navbar-brand" href="#">Il mio diario segreto</a>
    <div class="pull-xs-right">
        <a class="btn btn-success-outline" href="index.php?logout=1">Esci</a>
    </div>
</nav>

<div class="container-fluid" id="containerLoggedInPage">
    <form>  
        <textarea id="diary" class="form-control"><?=$diary?></textarea>
    </form>

</div> 

<?php
include 'footer.php';
?>