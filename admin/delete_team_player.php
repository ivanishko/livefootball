<?php
require_once('../config/site.php');

session_start();
if (!isset($_SESSION['script_user'])) {
    header('Location:index.php');
    exit();
}

require_once('../Connections/conn.php');

if (isset($_GET['id'])
    && isset($_POST['csrf_d'])
    && isset($_SESSION['csrf_d'])
    && $_POST['csrf_d'] == $_SESSION['csrf_d']
) {
    $id = intval($_GET['id']);

    mysqli_query($conn, "UPDATE team_players SET is_deleted = 1 WHERE id='{$id}'");

    unset($_SESSION['csrf_d']);

    if (isset($_GET['d'])) {
        header("Location: team_players.php?id=" . intval($_GET['d']));
    } else {
        header("Location: teams.php");
    }

    exit();
} else {
    header("Location: teams.php");
    exit();
}