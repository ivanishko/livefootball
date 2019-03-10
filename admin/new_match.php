<?php
require_once('../config/site.php');

session_start();
if (!isset($_SESSION['script_user'])) {
    header('Location:index.php');
    exit();
}

require_once('../Connections/conn.php');

$query_Recordset_Teams = "SELECT id, name FROM teams WHERE is_deleted = 0";
$Recordset_Teams = mysqli_query($conn, $query_Recordset_Teams) or die(mysqli_error($conn));
$row_Recordset_Teams = mysqli_fetch_assoc($Recordset_Teams);
$totalRows_Recordset_Teams = mysqli_num_rows($Recordset_Teams);
if ($totalRows_Recordset_Teams < 2) {
    echo "Please define at least 2 teams first";
    exit();
}

//commentator array - begin
$commentators = array();
$query_Recordset_Commentators = "SELECT id, name FROM commentators WHERE is_deleted = 0 ORDER BY name ASC";
$Recordset_Commentators = mysqli_query($conn, $query_Recordset_Commentators) or die(mysqli_error($conn));

while ($row_Recordset_Commentators = mysqli_fetch_assoc($Recordset_Commentators)) {
    $commentators[$row_Recordset_Commentators['id']] = $row_Recordset_Commentators;
}
//commentator array - end

if (isset($_POST['team1_id'])
    && isset($_POST['team2_id'])
    && isset($_POST['title'])
    && trim($_POST['title']) != ''
    && isset($_POST['match_date'])
    && trim($_POST['match_date']) != ''
    && isset($_POST['commentator_id'])
    && isset($_POST['csrf_i'])
    && isset($_SESSION['csrf_i'])
    && $_POST['csrf_i'] == $_SESSION['csrf_i']
) {
    $title       = prepare_for_db($_POST['title']);
    $description = prepare_for_db($_POST['description']);
    $match_date  = prepare_for_db($_POST['match_date']);
    $team1_id    = intval($_POST['team1_id']);
    $team2_id    = intval($_POST['team2_id']);
    $commentator_id = (array_key_exists(intval($_POST['commentator_id']),
        $commentators) ? intval($_POST['commentator_id']) :
        0);

    $stadium            = prepare_for_db($_POST['stadium']);
    $referee_head       = prepare_for_db($_POST['referee_head']);
    $referee_assistant  = prepare_for_db($_POST['referee_assistant']);
    $referee_assistant2 = prepare_for_db($_POST['referee_assistant2']);
    $referee_fourth     = prepare_for_db($_POST['referee_fourth']);

    if ($team1_id != $team2_id) {
        $sql = <<<EOF
INSERT INTO matches(
title,
description,
match_date,
team1_id,
team2_id,
stadium,
commentator_id,
referee_head,
referee_assistant,
referee_assistant2,
referee_fourth
) VALUES (
'{$title}',
'{$description}',
'{$match_date}',
'{$team1_id}',
'{$team2_id}',
'{$stadium}',
'{$commentator_id}',
'{$referee_head}',
'{$referee_assistant}',
'{$referee_assistant2}',
'{$referee_fourth}'
)
EOF;

        //save match
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        $match_id = mysqli_insert_id($conn);

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
'{$match_id}',
team_id,
id,
name,
squad_number,
display_order,
default_status
FROM team_players
WHERE
team_id = '{$team1_id}'
AND is_deleted = 0
EOF;
        //save team1 players of match
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
'{$match_id}',
team_id,
id,
name,
squad_number,
display_order,
default_status
FROM team_players
WHERE
team_id = '{$team2_id}'
AND is_deleted = 0
EOF;
        //save team2 players of match
        mysqli_query($conn, $sql) or die(mysqli_error($conn));

        header("Location: matches.php");
        exit();
    }
}

$_SESSION['csrf_i'] = md5(uniqid());

$query_Recordset_Teams = "SELECT id, name FROM teams WHERE is_deleted = 0";
$Recordset_Teams = mysqli_query($conn, $query_Recordset_Teams) or die(mysqli_error($conn));
$row_Recordset_Teams = mysqli_fetch_assoc($Recordset_Teams);
$totalRows_Recordset_Teams = mysqli_num_rows($Recordset_Teams);
if ($totalRows_Recordset_Teams < 2) {
    echo $label_array[92]; //Please define at least 2 teams first.
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $label_array[84]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include('menu_section.php'); ?>
<div class="container">
    <div class="row" style="margin-top: 10px;">
        <p><strong><?php echo $label_array[84]; ?></strong></p>

        <form action="" method="post" enctype="multipart/form-data" role="form">
            <table class="table table-bordered" style="width: 500px;">
                <tr>
                    <td><?php echo $label_array[6]; ?>:</td>
                    <td><input type="text" name="title" style="width:250px;"/></td>
                </tr>
                <tr>
                    <td><?php echo $label_array[7]; ?>:</td>
                    <td><input type="text" name="match_date" style="width:250px;"/></td>
                </tr>
                <tr>
                    <td><?php echo $label_array[93]; ?>:</td>
                    <td>
                        <select name="team1_id">
                            <?php do {
                                echo <<<EOF
<option value="{$row_Recordset_Teams['id']}">{$row_Recordset_Teams['name']}</option>
EOF;
                            } while ($row_Recordset_Teams = mysqli_fetch_assoc($Recordset_Teams)); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $label_array[94]; ?>:</td>
                    <td>
                        <select name="team2_id">
                            <?php
                            mysqli_data_seek($Recordset_Teams, 0);
                            while ($row_Recordset_Teams = mysqli_fetch_assoc($Recordset_Teams)) {
                                echo <<<EOF
<option value="{$row_Recordset_Teams['id']}">{$row_Recordset_Teams['name']}</option>
EOF;
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $label_array[8]; ?>:</td>
                    <td><textarea name="description" rows="3" cols="1" style="width: 250px;"></textarea></td>
                </tr>
                <tr>
                    <td><?php echo $label_array[9]; ?>:</td>
                    <td><input type="text" name="stadium" style="width:250px;"/></td>
                </tr>
                <tr>
                    <td><?php echo $label_array[123]; ?>:</td>
                    <td>
                        <select name="commentator_id">
                            <option value="0"><?php echo $label_array[122]; ?></option>
                            <?php
                            foreach ($commentators as $commentator) {
                                echo <<<EOF
<option value="{$commentator['id']}">{$commentator['name']}</option>
EOF;
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr class="info">
                    <td colspan="2"><?php echo $label_array[10]; ?></td>
                </tr>
                <tr>
                    <td><?php echo $label_array[11]; ?>:</td>
                    <td><input type="text" name="referee_head" style="width:250px;"/></td>
                </tr>
                <tr>
                    <td><?php echo $label_array[12]; ?>:</td>
                    <td><input type="text" name="referee_assistant" style="width:250px;"/></td>
                </tr>
                <tr>
                    <td><?php echo $label_array[13]; ?>:</td>
                    <td><input type="text" name="referee_assistant2" style="width:250px;"/></td>
                </tr>
                <tr>
                    <td><?php echo $label_array[14]; ?>:</td>
                    <td><input type="text" name="referee_fourth" style="width:250px;"/></td>
                </tr>
                <tr>
                    <td><input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/></td>
                    <td>
                        <label><input type="submit" name="Submit" value="<?php echo $label_array[95]; ?>"/></label>
                    </td>
                </tr>
            </table>
        </form>
        <br/>
    </div>
</div>
</body>
</html>