<?php
require_once('../config/site.php');

session_start();
if (!isset($_SESSION['script_user'])) {
    header('Location:index.php');
    exit();
}

require_once('../Connections/conn.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = <<<EOF
SELECT
t1.name,
t1.squad_number,
t1.default_status,
t1.display_order
FROM team_players t1
WHERE
t1.id='{$id}'
AND t1.is_deleted = 0
EOF;

    $Recordset_TeamPlayerDetails = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $row_team_player_details = mysqli_fetch_assoc($Recordset_TeamPlayerDetails);

    if (mysqli_num_rows($Recordset_TeamPlayerDetails) == 0) {
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
    && isset($_POST['csrf_e'])
    && isset($_SESSION['csrf_e'])
    && $_POST['csrf_e'] == $_SESSION['csrf_e']
) {
    $name           = prepare_for_db($_POST['name']);
    $squad_number   = prepare_for_db($_POST['squad_number']);
    $default_status = prepare_for_db(
        in_array(
            $_POST['default_status'],
            array('not_available', 'substitute', 'first_eleven')
        ) ? $_POST['default_status'] : 'not_available'
    );
    $display_order  = (intval($_POST['display_order']) <= 255 ? intval($_POST['display_order']) : 0);

    $sql = <<<EOF
UPDATE team_players
SET
name = '{$name}',
squad_number = '{$squad_number}',
default_status = '{$default_status}',
display_order = '{$display_order}'
WHERE
id = '{$id}'
EOF;

    //save team player
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    if (isset($_GET['d'])) {
        header("Location: team_players.php?id=" . intval($_GET['d']));
    } else {
        header("Location: teams.php");
    }

    exit();
}

$_SESSION['csrf_e'] = md5(uniqid());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $label_array[18]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include('menu_section.php'); ?>
<div class="container">
    <div class="row" style="margin-top: 10px;">
        <p><strong><?php echo $label_array[18]; ?></strong></p>

        <form action="" method="post" enctype="multipart/form-data" role="form">
            <table class="table table-bordered" style="width: 400px;">
                <tr>
                    <td>
                        <?php echo $label_array[19]; ?>
                    </td>
                    <td>
                        <input type="text" name="name"
                               value="<?php echo htmlspecialchars($row_team_player_details['name']); ?>"
                               style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[20]; ?>
                    </td>
                    <td>
                        <input type="text" name="squad_number"
                               value="<?php echo htmlspecialchars($row_team_player_details['squad_number']); ?>"
                               style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[21]; ?>
                    </td>
                    <td>
                        <select name="default_status">
                            <option
                                value="not_available" <?php if ($row_team_player_details['default_status'] == 'not_available') {
                                ?> selected="selected"<?php } ?>><?php echo $label_array[22]; ?>
                            </option>
                            <option
                                value="first_eleven" <?php if ($row_team_player_details['default_status'] == 'first_eleven') {
                                ?> selected="selected"<?php } ?>>
                                <?php
                                if ($script_match_type == 'soccer'){
                                    echo $label_array[23];
                                }
                                elseif ($script_match_type == 'rugby'){
                                    echo $label_array[132];
                                }
                                ?>
                            </option>
                            <option
                                value="substitute" <?php if ($row_team_player_details['default_status'] == 'substitute') {
                                ?> selected="selected"<?php } ?>><?php echo $label_array[24]; ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[25]; ?>
                    </td>
                    <td>
                        <input type="text" name="display_order"
                               value="<?php echo htmlspecialchars($row_team_player_details['display_order']); ?>"
                               style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td><input type="hidden" name="csrf_e" value="<?php echo $_SESSION['csrf_e']; ?>"/></td>
                    <td>
                        <input type="submit" name="Submit" value="<?php echo $label_array[2]; ?>"/>
                    </td>
                </tr>
            </table>
        </form>
        <br/>
    </div>
</div>
</body>
</html>