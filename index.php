<?php
        // verifichiamo se abbiamo qualche variabile inviata via POST
        // "array_key_exists" verifica se esiste la chiave nell'array POST ( chiave,array ) 
    if (array_key_exists('email', $_POST) OR array_key_exists('password', $_POST)) {

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

        // mi creo una variabile vuota a cui associerò un eventuale errore
        $error ="";

        // recupero i valori dei campi e li assegno alle variabili omonime
        $email = $_POST['email'];
        $password = $_POST['password'];

        // verifico se i campi sono stati inviati dal form
        if (!$email) {
            $error.= "Il campo email è richiesto..<br>";   
        }
        if (!$password) {   
            $error.= "Il campo password è richiesto..<br>";    
        }
        if($error != "") {   
            $error = "Si è verificato un errore nel tuo form<br>".$error;    
        }
        // adesso controlliamo se l'email non è stata già occupata da qualcuno
        // ovvero se l'utente esiste già nel nostro database
        else {
            // 'mysqli_real_escape_string' permette di inserire l'escape (\) nel caso fosse presente
            // una stringa contenente un apostrofo in modo tale da prendere i dati in maniera corretta
            $query = "SELECT `id` FROM `utente` WHERE email = '".mysqli_real_escape_string($link, $email)."'LIMIT 1";
            
            //effettuo la query al database con relativo controllo
            $result = mysqli_query($link, $query);

            // 'mysqli_num_rows($result)' ci ritorna il numero di record della tabella
            if (mysqli_num_rows($result) > 0) { 
                $error = "<p>L'indirizzo email inserito è già registrato.</p>";    
            } 
            else {
                // inserimento dei valori ordinati sul database per registrare l'utente
                $query = "INSERT INTO `utente` (`email`, `password`)
                            VALUES ('".mysqli_real_escape_string($link,$email)."',
                                    '".mysqli_real_escape_string($link,$password)."')";
                
                //effettuo la query al database con relativo controllo
                $result = mysqli_query($link, $query);
                if ($result) {

                    // "mysqli_insert_id()" ci ritorna l'id dell'utente appena inserito 
                    $id = mysqli_insert_id($link);

                    // effettuo il salto della password sull'id dell'utente per codificare la password
                    $passwordDecripted = md5(md5($id)."password");

                    // aggiorno la password inserita nel database con il valore decriptato
                    $query = "UPDATE utente SET `password` = '$passwordDecripted' WHERE id = $id LIMIT 1";
                    
                    mysqli_query($link, $query);

                    // quando l'utente si registra
                    $error = "Registrazione effettuata con successo";
                } 
                else {   
                    $error = "<p>C'è stato un problema nella registrazione, per favore riprova di nuovo.</p>";     
                }
            }
        }
    }   
?>

<h2>HTML Forms</h2>

<div id="error"><?php echo $error; ?></div>

<!-- Creazione di un Form per la registrazione-->
<form method="POST">
    <input type="email" name="email" placeholder="La tua email" >
    <input type="password" name="password" placeholder="La tua password">
    <input type="checkbox" name="stayLoggedIn" value=1>
    <input type="submit" value="Registrati">
</form> 

<!-- Creazione di un secondo form per loggare-->
<form method="POST">
    <input type="text" name="email" placeholder="La tua email" >
    <input type="text" name="password" placeholder="La tua password">
    <input type="submit" value="Log In">
</form> 
