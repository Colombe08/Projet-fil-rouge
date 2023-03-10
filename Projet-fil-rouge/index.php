<?php
$mysqli = @new mysqli(
    // Etablir la connexion à ma base de donnée //
    $servername ="localhost",
    $username= "root",
    $password= "root",
    $dbname ="formInscription"
);

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Vous avez bien été inscrit(e) !";
    }
    
    catch(PDOException $e){
        echo "la connexion a échoué" . $e->getMessage();
    }

// Definir les paramètres et vérifier si elles existent //
if(isset($_POST['envoyer']))
{

    if(isset($_POST['email']) && isset($_POST['password']))
    {
        // On stock les posts dans htmlspecialchars pour éviter les failles xss //
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        // On vérifie si l'utilisateur est bien inscrit dans notre base de données //
        $check = $bdd->prepare('SELECT email, password FROM users WHERE email = ?');
        $check->execute(array($email));
        $data = $check->fetch();
        $row = $check->rowCount();

        if($row == 0)
        {
            if(strlen($email) <= 254)
            {
                if(filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    // On va hacher le mot de passe //
                    $password = hash('sha256', $password);

                    // On va stocker l'adresse ip //
                    $ip = $_SERVER['REMOTE_ADDR'];
                    
                    $insert = $bdd->prepare('INSERT INTO `users`(`nom`, `prenom`, `email`, `motdepasse`, `adresse`, `complement`, `ville`, `pays`, `code`, `cgu`, `protection`, `ip`) VALUES(:nom, :prenom, :email, :motdepasse, :adresse, :complement, :ville, :pays, :code, :cgu, :protection, :ip)');
                // Mon tableau assiociative //
                    $insert->execute(array(
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'email' => $email,
                        'motdepasse' => $password,
                        'adresse' => $adresse,
                        'complement' => $complement,
                        'ville' => $ville,
                        'pays' => $pays,
                        'code' => $code,
                        'cgu' => $cgu,
                        'protection' => $protection,
                        'ip' => $ip
                    ));
                    header('Location: inscription.html?reg_err=success');
                }else header('Location: inscription.html?reg_err=email');
            }else header('Location: inscription.html?reg_err=email_length');
        }else header('Location: inscription.html?reg_err=already');
    }    
}

// fermer la connexion //
$mysqli->close();

?>

