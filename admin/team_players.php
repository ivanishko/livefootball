<?php
require_once('../config/site.php');

session_start();
if (!isset($_SESSION['script_user'])) {
    header('Location:index.php');
    exit();
}

require_once('../Connections/conn.php');

$_SESSION['csrf_d'] = md5(uniqid());

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (mysqli_num_rows(mysqli_query($conn, "SELECT id FROM teams WHERE id='{$id}' AND is_deleted = 0")) == 0) {
        header("Location: teams.php");
        exit();
    }
} else {
    header("Location: teams.php");
    exit();
}

if (isset($_POST['name'])
    && trim($_POST['name']) != ''
    && isset($_POST['squad_number'])
    && trim($_POST['squad_number']) != ''
    && isset($_POST['csrf_i'])
    && isset($_SESSION['csrf_i'])
    && $_POST['csrf_i'] == $_SESSION['csrf_i']
) {
    $name           = prepare_for_db($_POST['name']);
    $squad_number   = prepare_for_db($_POST['squad_number']);
    $display_order  = (intval($_POST['display_order']) <= 255 ? intval($_POST['display_order']) : 0);
    $default_status = prepare_for_db('not_available');
    if (isset($_POST['default_status'])
        && in_array($_POST['default_status'], array('not_available', 'substitute', 'first_eleven'))
    ) {
        $default_status = prepare_for_db($_POST['default_status']);
    }

    $sql = <<<EOF
INSERT INTO team_players(name, team_id, squad_number, display_order, default_status)
VALUES
(
'{$name}',
'{$id}',
'{$squad_number}',
'{$display_order}',
'{$default_status}'
);
EOF;

    //SQL
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $team_player_id = mysqli_insert_id($conn);

    $sql = <<<EOF
INSERT INTO match_players(
match_id,
team_id,
team_player_id,
name,
squad_number,
display_order,
status
)
SELECT
id,
team1_id,
'{$team_player_id}',
'{$name}',
'{$squad_number}',
'{$display_order}',
'{$default_status}'
FROM matches
WHERE
team1_id = '{$id}'
AND is_deleted = 0
EOF;

    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $sql = <<<EOF
INSERT INTO match_players(
match_id,
team_id,
team_player_id,
name,
squad_number,
display_order,
status
)
SELECT
id,
team2_id,
'{$team_player_id}',
'{$name}',
'{$squad_number}',
'{$display_order}',
'{$default_status}'
FROM matches
WHERE
team2_id = '{$id}'
AND is_deleted = 0
EOF;
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    header("Location: team_players.php?id=" . $id);
    exit();
}

$_SESSION['csrf_i'] = md5(uniqid());

$query_Recordset_List = <<<EOF
SELECT
id,
name,
squad_number,
default_status,
display_order
FROM team_players
WHERE
team_id = '{$id}'
AND is_deleted = 0
ORDER BY default_status, display_order ASC, name ASC
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
    <title><?php echo $label_array[96]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="margin:0;padding:0;">
<?php include('menu_section.php'); ?>
<div class="container">
    <div class="row" style="margin-top: 10px;">
        <p><strong><?php echo $label_array[97]; ?></strong></p>

        <form action="" method="post" enctype="multipart/form-data" role="form">
            <table class="table table-bordered" style="width: 600px;">
                <tr>
                    <td>
                        <?php echo $label_array[19]; ?>
                    </td>
                    <td>
                        <input type="text" name="name" style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[20]; ?>
                    </td>
                    <td>
                        <input type="text" name="squad_number" style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[21]; ?>
                    </td>
                    <td>
                        <select name="default_status">
                            <option value="not_available" selected="selected"><?php echo $label_array[22]; ?></option>
                            <option value="first_eleven">
                                <?php
                                if ($script_match_type == 'soccer'){
                                    echo $label_array[23];
                                }
                                elseif ($script_match_type == 'rugby'){
                                    echo $label_array[132];
                                }
                                ?>
                            </option>
                            <option value="substitute"><?php echo $label_array[24]; ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[25]; ?>
                    </td>
                    <td>
                        <input type="text" name="display_order" value="0" style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td><input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/></td>
                    <td>
                        <input type="submit" name="Submit" value="<?php echo $label_array[95]; ?>"/>
                    </td>
                </tr>
            </table>
        </form>
        <?php
        if ($totalRows_Recordset_List != 0) {
            ?>
            <p><strong><?php echo $label_array[96]; ?></strong></p>
            <table class="table table-bordered table-striped" style="width: 600px;">
                <tr class="info">
                    <th><?php echo $label_array[19]; ?></th>
                    <th><?php echo $label_array[20]; ?></th>
                    <th><?php echo $label_array[25]; ?></th>
                    <th><?php echo $label_array[21]; ?></th>
                    <th><?php echo $label_array[87]; ?></th>
                    <th><?php echo $label_array[42]; ?></th>
                </tr>
                <?php
                do {
                    $default_status = $label_array[22];

                    switch ($row_Recordset_List['default_status']) {
                        case 'substitute':
                            $default_status = $label_array[24];
                            break;
                        case 'first_eleven':
                            $default_status = $label_array[23];
                            if ($script_match_type == 'rugby'){
                                $default_status = $label_array[132];
                            }
                            break;
                    }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row_Recordset_List['name']); ?></td>
                        <td><?php echo htmlspecialchars($row_Recordset_List['squad_number']); ?></td>
                        <td><?php echo htmlspecialchars($row_Recordset_List['display_order']); ?></td>
                        <td><?php echo $default_status; ?></td>
                        <td>
                            <a href="edit_team_player.php?id=<?php echo $row_Recordset_List['id']; ?>&amp;d=<?php echo $id; ?>">Edit</a>
                        </td>
                        <td>
                            <form id="d_tp_<?php echo $row_Recordset_List['id']; ?>" action="<?php echo
                                'delete_team_player.php?id=' . $row_Recordset_List['id'] . '&amp;d=' . $id; ?>"
                                  method="post" role="form">
                                <input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
                            </form>
                            <a href="#"
                               onclick="if (confirm('<?php echo $label_array[44]; ?>')) { document.getElementById
                                   ('<?php echo 'd_tp_' .
                                   $row_Recordset_List['id']; ?>').submit(); } return false;"><?php echo
                                $label_array[42]; ?></a>
                        </td>
                    </tr>
                <?php
                } while ($row_Recordset_List = mysqli_fetch_assoc($Recordset_List));
                ?>
            </table>
        <?php } ?>
    </div>
</div>
</body>
</html>
