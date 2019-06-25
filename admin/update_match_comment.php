<?php
require_once('../config/site.php');
require_once('../Connections/conn.php');

$minute = trim($_POST['minute']);
$text = trim($_POST['text']);
$sql =  "UPDATE commentation SET comment_minute = '".$minute."', comment = '".$text."' WHERE id = ".(int)$_POST['id'];
$Recordset_MatchDetails = mysqli_query($conn, $sql) or die(mysqli_error($conn));
echo $Recordset_MatchDetails;
