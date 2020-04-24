<?php
spl_autoload_register(function($class) {
    require_once($class.'.php');
});

session_start();
if(isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Min combat</title>
</head>
<body>
    <p><?php if(isset($_GET['count']) && !empty($_GET['count'])) echo 'Nombre de joueurs dans le jeux '. $_GET['count']; ?></p>
    <p><?php if(isset($_GET['message'])) echo $_GET['message']; ?></p>
    


    <?php
    if(!isset($_SESSION['perso']) && !isset($_SESSION['persos'])) {
    ?>
        <form action="traitment.php" method="POST">
            <p>
                <label for="nom">Nom</label> : 
                <input type="text" name="nom" id="nom" maxlength="50" placeholder="Nom du personnage">
                <input type="submit" value="Créer ce personnage" name="creer">
                <input type="submit" value="Utiliser ce personnage" name="utiliser">
            </p>
        </form>
    <?php
    } elseif(isset($_SESSION['perso']) && isset($_SESSION['persos'])) {
    ?>
        <p><button><a href="?deconnexion=1">DECONNEXION</a></button></p>
        <p>
            <fieldset>
                <legend>Mes informations</legend>
                <?= 'Nom : '.$_SESSION['perso']->nom() ?><br><?= 'Dégats : '.$_SESSION['perso']->degats() ?>
            </fieldset>
        </p>
        <p>
            <fieldset>
                <legend>Informations des joueurs à combatre</legend>
                <?php foreach($_SESSION['persos'] as $perso): ?>
                    <p><a href="traitment.php?idDef=<?= $perso->id() ?>&amp;idAt=<?= $_SESSION['perso']->id() ?>">
                        <?= 'Nom : '.$perso->nom() ?></a><br>
                    <?= 'Dégats : '.$perso->degats() ?></p>
                <?php endforeach; ?>
            </fieldset>
        </p>
    <?php
    }
    ?>
</body>
</html>