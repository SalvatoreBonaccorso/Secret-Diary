<?php
    // impostiamo l'inizio della sessione
    session_start();

    // mi creo una variabile vuota a cui associerò un eventuale errore
    $error ="";

    // verifichiamo se c'è la variabile logout per capire se il
    // il nostro utente si è disconnesso
    if (array_key_exists("logout", $_GET)) {

        session_destroy();
        // questa funzione distrugge la sessione
        unset($_SESSION);
        // distruggiamo anche il cookie impostando un tempo negativo
        setcookie("id", "", time() - 60*60);
        // svuotiamo la variabile
        $_COOKIE["id"] = "";      
    } 
    // mentre se è connesso deve essere reindirizzato nella pagina di logged in 
    // qui verifico se è presente la chiave ed il suo valore sia per la sessione che per i cookie
    else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {  
        header("Location: loggedinpage.php");  
    }

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

            // se l'utente si sta registrando
            if ($_POST['signUp'] == '1') {
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
                        $passwordDecripted = md5(md5($id).$password);

                        // aggiorno la password inserita nel database con il valore decriptato
                        $query = "UPDATE utente SET `password` = '$passwordDecripted' WHERE id = $id LIMIT 1";
                        
                        mysqli_query($link, $query);

                        // inseriamo l'id dell'utente in sessione
                        $_SESSION['id'] = $id;

                        // verifichiamo se l'utente ha richiesto di essere ricordato
                        // spuntando la checkbox nel form
                        if ($_POST['stayLoggedIn'] == 1) {
                            setcookie("id", $id, time() + 60*60*24*365);               
                        }
                        // quando l'utente si registra lo reindirizzeremo alla pagina di login
                        header("Location: loggedinpage.php");
                    } 
                    else {   
                        $error = "<p>C'è stato un problema nella registrazione, per favore riprova di nuovo.</p>";     
                    }
                }
            }
            // altrimenti (se l'utente si sta loggando praticamente)
            else{
                // impostiamo una query per verificare se ii valore dell'email è già registrato
                $query = "SELECT * FROM `utente` WHERE email = '".mysqli_real_escape_string($link, $email)."'";
                
                $result = mysqli_query($link, $query);
            
                $row = mysqli_fetch_array($result);

                // verifichiamo se è settata la variabile row
                if (isset($row)) {
                    
                    $hashedPassword = md5(md5($row['id']).$password);
                    
                    if ($hashedPassword == $row['password']) {
                        
                        $_SESSION['id'] = $row['id'];
                        
                        if ($_POST['stayLoggedIn'] == '1') {
                            // settiamo i cookie
                            setcookie("id", $row['id'], time() + 60*60*24*365);
                        } 
                        // reindirizziamo l'utente alla pagina di login
                        header("Location: loggedinpage.php");        
                    } 
                    else {                  
                        $error = "La combinazione email/password non è stata trovata.";                
                    }       
                } 
                else {    
                    $error = "La combinazione email/password non esiste";   
                }               
            }         
        }
    }   
?>

<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="style.css">

        <title>Secret diary</title>
    </head>
    <body>
        <div class="container" id="homePageContainer">
            <h1>Secret diary</h1>

            <div id="error"><?php echo $error; ?></div>

            <!-- Creazione di un Form per la registrazione-->
            <form method="POST">

                <fieldset class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="La tua email" >
                </fieldset>

                <fieldset class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="La tua password">
                </fieldset> 

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="stayLoggedIn" value=1> Rimani loggato
                    </label>
                </div>

                <!-- Quest'input hidden non sarà visibile,ma ci servirà
                    per differenziare un forma da un altro
                    Infatti in questo form attribuiamo il valore 1-->
                <fieldset class="form-group">
                    <input type="hidden" name="signUp" value="1">
                    <input type="submit" class="btn btn-success" value="Registrati">
                </fieldset>

            </form> 

            <!-- Creazione di un secondo form per loggare-->
            <form method="POST">

                <fieldset class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="La tua email" >
                </fieldset>

                <fieldset class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="La tua password">
                </fieldset>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="stayLoggedIn" value=1> Rimani loggato
                    </label>
                </div>

                <!-- attribuiamo il valore 0 a questo form -->
                <fieldset class="form-group">
                    <input type="hidden" name="signUp" value="0">
                    <input type="submit" class="btn btn-success" value="Log In">
                </fieldset>
            </form> 


            <!-- Optional JavaScript -->
            <!-- jQuery first, then Popper.js, then Bootstrap JS -->
            <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        </div>
    </body>
</html>