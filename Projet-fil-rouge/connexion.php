<?php

// Ouvrir la session // 
session_start();
if(isset($_SESSION['user']))
    header('Location:connexion.html');

$mysqli = @new mysqli(
// Etablir la connexion à ma base de données //
$servername ="localhost",
$username= "root",
$password= "root",
$dbname ="formConnexion"
);

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Bienvenu !";
}

catch(PDOException $e){
    echo "la connexion a échoué" . $e->getMessage();
}

if(isset($_POST['envoyer']))
{

    // On vérifie si le champ "recaptcha-response" contient une valeur //
    if(empty($_POST['recaptcha-response'])){
        header('Location: connexion.html');
    }else{
        // On prépare l'URL //
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=6LePI3EfAAAAAPpyhaOGT-lIiBda_8jU7fm7c5aK&response={$_POST['recaptcha-response']}";
    
        // On vérifie si curl est installé
        if(function_exists('curl_version')){
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
        }else{
            // On utilise file_get_contents //
            $response = file_get_contents($url);
        }

        // On vérifie qu'on a une réponse //
        $data - json_decode($response);
        echo "<pre>";
        var_dump($data->success);
        echo "</pre>";

    if(isset($_POST['email']) && isset($_POST['password']))
    {
        if(empty($_POST['email']) AND isset($_POST['password']))
        {

        
            // On stock les posts dans htmlspecialchars pour éviter les failles xss contenu malveillant //
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);

            // On vérifie si l'utilisateur est bien inscrit dans notre base de données //
            $check = $bdd->prepare('SELECT pseudo, email, password FROM users WHERE email = ?');
            $check->execute(array($email));
            $data = $check->fetch();
            $row = $check->rowCount();

            // Si l'utilisateur existe == 1 sinon il n'existe pas //
            if($row == 1)
            {
                // On verifie que l'adresse email est valide // 
                if(filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    // On va hacher le mot de passe //
                    $password = hash('sha256', $password);         
                    if($data['password'] === $password)
                    {
                        $_SESSION['user'] = $data['pseudo'];
                    }else header('Location:connexion.html?login_err=password');
                }else header('Location:connexion.html?login_err=email');
            }else header('Location:connexion.html?login_err=already');
        }else header('Location:connexion.html');
    } 
  }   
}

// fermer la connexion //
$mysqli->close();

?>


    
