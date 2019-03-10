<?php
/*
 * This file is used by the jQuery Ajax function to check if there is an update.
 */
require_once('config/site.php');
require_once('Connections/conn.php');

$match_revision_nr   = 0;
$comment_revision_nr = 0;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = <<<EOF
SELECT
t1.match_revision_nr,
t1.comment_revision_nr
FROM matches t1
WHERE
t1.id='{$id}'
AND t1.is_deleted = 0
EOF;

    $Recordset_MatchDetails = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $row_match_details = mysqli_fetch_assoc($Recordset_MatchDetails);

    if (mysqli_num_rows($Recordset_MatchDetails) == 0) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

$match_revision_nr   = $row_match_details['match_revision_nr'];
$comment_revision_nr = $row_match_details['comment_revision_nr'];

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header('Content-Type: application/json');

echo json_encode(array('m_nr' => $match_revision_nr, 'c_nr' => $comment_revision_nr));