<?php
require_once('../config/site.php');

session_start();
if (!isset($_SESSION['script_user'])) {
    header('Location:index.php');
    exit();
}

require_once('../Connections/conn.php');

$_SESSION['csrf_d'] = md5(uniqid());

$query_Recordset_List = <<<EOF
SELECT
t1.*,
t2.name AS team1_name,
t3.name AS team2_name,
t4.name AS commentator_name
FROM matches t1
LEFT JOIN teams t2 ON t1.team1_id = t2.id
LEFT JOIN teams t3 ON t1.team2_id = t3.id
LEFT JOIN commentators t4 ON t1.commentator_id = t4.id
WHERE
t1.is_deleted = 0
ORDER BY t1.id DESC
EOF;

$Recordset_List = mysqli_query($conn, $query_Recordset_List) or die(mysqli_error($conn));
$row_Recordset_List = mysqli_fetch_assoc($Recordset_List);
$totalRows_Recordset_List = mysqli_num_rows($Recordset_List);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $label_array[83]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include('menu_section.php'); ?>
<div class="container">
    <div class="row" style="margin-top: 10px;">
        <a href="new_match.php"><?php echo $label_array[84]; ?></a><br/>
        <?php
        if ($totalRows_Recordset_List != 0) {
            ?>
            <p style="margin-top: 10px;"><strong><?php echo $label_array[83]; ?></strong>
                &nbsp;
                <a href="../index.php" target="_blank"><?php echo $label_array[124]; ?></a>
            </p>
            <table class="table table-bordered table-striped" style="width: 1000px;">
                <tr class="info">
                    <th><?php echo $label_array[6]; ?></th>
                    <th><?php echo $label_array[85]; ?></th>
                    <th><?php echo $label_array[86]; ?></th>
                    <th><?php echo $label_array[123]; ?></th>
                    <th><?php echo $label_array[79]; ?></th>
                    <th><?php echo $label_array[87]; ?></th>
                    <th><?php echo $label_array[42]; ?></th>
                </tr>
                <?php
                do {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row_Recordset_List['title']) .
                                ($row_Recordset_List['match_date'] != '' ?
                                    ' (' . htmlspecialchars($row_Recordset_List['match_date']) . ')' : ''); ?></td>
                        <td><?php echo htmlspecialchars($row_Recordset_List['team1_name']); ?></td>
                        <td><?php echo htmlspecialchars($row_Recordset_List['team2_name']); ?></td>
                        <td><?php echo ($row_Recordset_List['commentator_name'] != '' ? htmlspecialchars
                            ($row_Recordset_List['commentator_name']) : '-'); ?></td>
                        <td><a href="match_player_selection.php?id=<?php echo $row_Recordset_List['id'];
                            ?>"><?php echo $label_array[88]; ?></a> | <a
                                href="match_commentation.php?id=<?php echo $row_Recordset_List['id']; ?>"><?php echo
                                $label_array[89];
                                ?></a>
                        </td>
                        <td><a href="edit_match.php?id=<?php echo $row_Recordset_List['id']; ?>"><?php echo
                                $label_array[87];
                                ?></a></td>
                        <td>
                            <form id="d_m_<?php echo $row_Recordset_List['id']; ?>"
                                  action="<?php echo 'delete_match.php?id=' . $row_Recordset_List['id']; ?>"
                                  method="post" role="form">
                                <input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
                            </form>
                            <a href="#"
                               onclick="if (confirm('<?php echo $label_array[44]; ?>')) { document.getElementById
                                   ('<?php echo 'd_m_' .
                                   $row_Recordset_List['id']; ?>').submit(); } return false;"><?php echo
                                $label_array[42]; ?></a>
                        </td>
                    </tr>
                <?php
                } while ($row_Recordset_List = mysqli_fetch_assoc($Recordset_List));
                ?>
            </table>
        <?php } ?>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
    </div>
</div>
</body>
</html>