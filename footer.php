  <!-- Optional JavaScript -->
            <!-- jQuery first, then Popper.js, then Bootstrap JS -->
            <script src="https://code.jquery.com/jquery-3.4.1.min.js" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        
            <!-- applico il JQuery per far switchare tra la parte per loggare e quella per registrare
                attraverso il bottone il link "login" -->
            <script type="text/javascript">

                // seleziono l'ID del form di login
                $(".toggleForms").click(function(){
                    $("#signUpForm").toggle();
                    $("#logInForm").toggle();
                })

                // script che si attiva quando iniziamo a scrivere nella textArea
                // e aggiorna il contenuto del campo "Diario" nel database
                $('#diary').bind('input propertychange', function() {

                    $.ajax({
                        method: "POST",
                        url: "updatedatabase.php",
                        data: { content: $("#diary").val() }
                    });

                });

            </script>
        </div>
    </body>
</html>

