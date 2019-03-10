<?php
require_once('../config/site.php');

session_start();
if (!isset($_SESSION['commentator_user'])) {
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

    mysqli_query($conn, "UPDATE rugby_match_scores SET is_deleted = 1 WHERE id='{$id}'");

    unset($_SESSION['csrf_d']);

    $sql = <<<EOF
UPDATE matches
SET
match_revision_nr = match_revision_nr + 1
WHERE
id = (
    SELECT
    t2.match_id
    FROM match_goals t1
    INNER JOIN match_players t2 ON t1.match_player_id = t2.id
    WHERE
    t1.id = '{$id}'
)
EOF;

    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    if (isset($_GET['d'])) {
        header("Location: match_commentation.php?id=" . intval($_GET['d']));
    } else {
        header("Location: matches.php");
    }

    exit();
} else {
    header("Location: matches.php");
    exit();
}