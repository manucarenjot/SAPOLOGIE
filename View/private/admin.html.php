<?php
if (isset($_SESSION['admin'])) {
    header('Location: ?c=home');
}
