<?php
if (isset($_SESSION['user'])) {
    if ($_GET['id'] === $_SESSION['user']['id']) {
        ?>
        <a href="?c=profil&a=update-profil&id=<?= $_SESSION['user']['id'] ?>">Modifier le profil</a>
        <a href="?c=profil&a=add-character&id=<?= $_SESSION['user']['id'] ?>">Ajouter un personnage</a>
        <?php
    }
}