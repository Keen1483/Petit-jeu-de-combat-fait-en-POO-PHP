<?php
require('index.php');
spl_autoload_register(function($class) {
    require_once($class.'.php');
});

try {
    $db = new PDO('mysql:host=localhost;dbname=cours;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : '.$e->getMessage());
}

session_start();

if(isset($_SESSION['utiliser']) && isset($_SESSION['nom'])) {
    $_POST['utiliser'] = $_SESSION['utiliser'];
    $_POST['nom'] = $_SESSION['nom'];
}

$message = '';
$nbrPerso = null;
$manager = new PersonnageManager($db);

if(isset($_POST['creer'])) {

    if (isset($_POST['nom']) && strlen($_POST['nom']) <= 50) {

        if (ltrim($_POST['nom']) == '') {
    
            $message = 'Entrer le nom du personnage';
        } else {
    
            if(!$manager->exists($_POST['nom'])) {
                $manager->add(new Personnage([
                    'nom' => htmlspecialchars($_POST['nom']),
                ]));
            } else {
                $message = 'Le personnage que vous voulez crée existe déjà, choisissez un autre nom';
            }
        }
    }
} elseif(isset($_POST['utiliser'])) {

    if (isset($_POST['nom']) && strlen($_POST['nom']) <= 50) {

        if (ltrim($_POST['nom']) == '') {
    
            $message = 'Entrer le nom du personnage';

            session_destroy();
        } else {
    
            if($manager->exists($_POST['nom'])) {

                $_SESSION['perso'] = $manager->get($_POST['nom']);
                $_SESSION['persos'] = $manager->getList($_POST['nom']);

                $_SESSION['utiliser'] = 'utiliser';
                $_SESSION['nom'] = $_POST['nom'];

                if(isset($_GET['idAt']) && isset($_GET['idDef'])) {

                    $_GET['idAt'] = (int)$_GET['idAt'];
                    $_GET['idDef'] = (int)$_GET['idDef'];
                    if($manager->exists($_GET['idAt']) && $manager->exists($_GET['idDef'])) {
                        $attact = $manager->get($_GET['idAt']);
                        $defend = $manager->get($_GET['idDef']);

                        if($attact->degats() >= 100) {
                            $message = 'Ce personnage est mort et ne peut être utiliser';
                        } else {
                            $resp = $attact->frapper($defend);
                
                            $manager->update($defend);
                    
                            if($resp == 1) {
                                $message = 'Le personnage a voulu se frapper lui-même';
                            } elseif ($resp == 2) {
                                $message = 'Le personnage a été tué';
                            } elseif ($resp) {
                                $message = 'Le personnage a bien été frappé';
                            }
                        }
                    }

                    $_SESSION['perso'] = $manager->get($_POST['nom']);
                    $_SESSION['persos'] = $manager->getList($_POST['nom']);
                }
   
            } else {
                $message = 'Le personnage que vous voulez utiliser n\'existe pas';

                session_destroy();
            }

            $nbrPerso = $manager->count();
        }
    }
}

header('Location: index.php?message='.$message.'&count='.$nbrPerso);
exit();
