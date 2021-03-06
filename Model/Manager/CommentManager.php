<?php


use App\Model\Entity\Comment;

class CommentManager
{

    public static function addComment(Comment $comment, int $characterFk)
    {
        $insert = Connect::getPDO()->prepare("INSERT INTO aiu12_comment (user_fk, content, date, character_image_fk) 
                                                    VALUES (:user_fk, :content, :date, :character_image_fk)");

        date_default_timezone_set('Europe/Brussels');
        $date = date('y-m-d H:i:s');

        $insert->bindValue(':user_fk', $comment->getUserFk());
        $insert->bindValue(':content', $comment->getContent());
        $insert->bindValue(':date', $date);
        $insert->bindValue(':character_image_fk', $comment->getCharacterImageFk());

        if ($insert->execute()) {
            $alert = [];
            $alert[] = '<div class="alert-succes">Commentaire ajouté !</div>';
            if (count($alert) > 0) {
                $_SESSION['alert'] = $alert;
                header('LOCATION: ?c=character&id=' . $characterFk);
            }
        } else {
            $alert = [];
            $alert[] = '<div class="alert-error">Une erreur c\est produite lors de l\'envoi du commentaire !</div>';
            if (count($alert) > 0) {
                $_SESSION['alert'] = $alert;
                header('LOCATION: ?c=character&id=' . $characterFk);
            }
        }
    }

    public static function getLastComment(int $id, $limit)
    {
        $select = Connect::getPDO()->prepare("SELECT * FROM aiu12_comment where character_image_fk = :id ORDER BY date desc LIMIT $limit");

        $select->bindValue(":id", $id);


        if ($select->execute()) {
            $datas = $select->fetchAll();
            ?>
            <div class="allDataComment">
                <?php
                foreach ($datas as $data) {
                    $select2 = Connect::getPDO()->prepare("SELECT * FROM aiu12_user where id = :id");
                    $select2->bindValue(':id', $data['user_fk']);
                    $select2->execute();
                    $datas2 = $select2->fetchAll();
                    foreach ($datas2 as $data2) {
                        ?>
                        <div class="commentContent">
                            <div class="CommentAuthor" style="display: inline"><a
                                        href="?c=profil&id=<?= $data2['id'] ?>"
                                        style="width: 100%"><b><?= $data2['username'] ?>
                                    </b></a></b> le <?= date('d-m-Y à H:i:', strtotime($data['date'])) ?></div>
                            <?php
                            if (isset($_SESSION['user'])) {
                                if ($_SESSION['user']['id'] === $data['user_fk'] or $_SESSION['role'] === 'admin') {
                                    ?>
                                    <form method="post" action="?c=delete" style="display: inline; width: 100%">
                                        <input type="text" name="idComment" value="<?= $data['id'] ?>"
                                               style="display: none">
                                        <p class="Ask" style="display: none">Voulez vous vraiment supprimer ce
                                            commentaire ?</p>
                                        <input type="submit" name="deleteComment" value="❌" title="Supprimer"
                                               style="cursor: pointer">
                                    </form>
                                    <?php
                                }
                            }
                            ?>
                            <br>
                            <div class="comment"><?= $data['content'] ?></div>
                            <br>
                        </div>

                        <?php
                    }
                    $id = $data['character_image_fk'];
                }

                echo '<a href="?c=picture&id=' . $id . '">Voir plus ⬇</a><br><br>';


                ?>
            </div>
            <br>

            <?php


        }
    }

    public static function deleteComment($id)
    {
        $select = Connect::getPDO()->prepare("SELECT * FROM aiu12_comment WHERE id = :id");
        $select->bindValue(':id', $id);
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        if ($select->execute()) {
            $datas = $select->fetchAll();
            foreach ($datas as $data) {
                if (isset($_SESSION['user'])) {
                    if ($_SESSION['user']['id'] === $data['user_fk'] or $_SESSION['role'] === 'admin') {
                        $delete = Connect::getPDO()->prepare("Delete  From aiu12_comment WHERE id = :id");
                        $delete->bindValue(':id', $id);

                        if ($delete->execute()) {
                            $alert = [];
                            $alert[] = '<div class="alert-succes">Le commentaire a été supprimé !</div>';
                            if (count($alert) > 0) {
                                $_SESSION['alert'] = $alert;
                                header('Location: ' . $referer);
                            }
                        }
                    } else {
                        $alert[] = '<div class="alert-error">Vous ne pouvez pas effectuer cette action !</div>';
                        if (count($alert) > 0) {
                            $_SESSION['alert'] = $alert;
                            header('Location: ' . $referer);
                        }
                    }
                } else {
                    $alert[] = '<div class="alert-error">Vous ne pouvez pas effectuer cette action !</div>';
                    if (count($alert) > 0) {
                        $_SESSION['alert'] = $alert;
                        header('Location: ' . $referer);
                    }
                }
            }
        }
    }
}