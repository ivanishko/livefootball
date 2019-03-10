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

    // permission check - begin
    $sql =<<<EOF
SELECT
1
FROM match_cards t1
INNER JOIN match_players t2 ON t1.match_player_id = t2.id
INNER JOIN matches t3 ON t2.match_id = t3.id
WHERE
t1.id = '{$id}'
AND t3.commentator_id = '{$_SESSION['commentator_user']['id']}'
EOF;

    $Recordset_Control = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    if (mysqli_num_rows($Recordset_Control) == 0) {
        header("Location: matches.php");
        exit();
    }
    // permission check - end

    mysqli_query($conn, "UPDATE match_cards SET is_deleted = 1 WHERE id='{$id}'");

    unset($_SESSION['csrf_d']);

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