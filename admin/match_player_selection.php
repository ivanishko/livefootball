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
t1.id,
t1.team1_id,
t1.team2_id,
t2.name AS home_team,
t3.name AS away_team
FROM matches t1
LEFT JOIN teams t2 ON t1.team1_id = t2.id
LEFT JOIN teams t3 ON t1.team2_id = t3.id
WHERE
t1.id='{$id}'
AND t1.is_deleted = 0
EOF;

    $Recordset_MatchDetails = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $row_match_details = mysqli_fetch_assoc($Recordset_MatchDetails);

    if (mysqli_num_rows($Recordset_MatchDetails) == 0) {
        header("Location: matches.php");
        exit();
    }
} else {
    header("Location: matches.php");
    exit();
}

if (isset($_POST['Submit'])
    && isset($_POST['csrf_e'])
    && isset($_SESSION['csrf_e'])
    && $_POST['csrf_e'] == $_SESSION['csrf_e']
) {
    foreach ($_POST as $key => $value) {
        if (preg_match('/name([0-9]*)/', $key, $result)) {
            $name = prepare_for_db($value);

            $sql = <<<EOF
UPDATE match_players
SET
name = '{$name}'
WHERE
id='{$result[1]}'
EOF;
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }

        if (preg_match('/squad_number([0-9]*)/', $key, $result)) {
            $squad_number = prepare_for_db($value);

            $sql = <<<EOF
UPDATE match_players
SET
squad_number = '{$squad_number}'
WHERE
id='{$result[1]}'
EOF;
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }

        if (preg_match('/display_order([0-9]*)/', $key, $result)) {
            $display_order = (intval($value) <= 255 ? intval($value) : 0);

            $sql = <<<EOF
UPDATE match_players
SET
display_order = '{$display_order}'
WHERE
id='{$result[1]}'
EOF;
            mysqli_query($conn, $sql) or die(mysqli_error($conn));
        }

        if (preg_match('/status([0-9]*)/', $key, $result)) {
            if (in_array($value, array('not_available', 'substitute', 'first_eleven'))) {
                $sql = <<<EOF
UPDATE match_players
SET
status = '{$value}'
WHERE
id='{$result[1]}'
EOF;

                mysqli_query($conn, $sql) or die(mysqli_error($conn));
            }
        }

        $sql = <<<EOF
UPDATE matches
SET
match_revision_nr = match_revision_nr + 1
WHERE
id = $id
EOF;

        mysqli_query($conn, $sql) or die(mysqli_error($conn));
    }

    header("Location: match_player_selection.php?id=" . $id);
    exit();
}

$_SESSION['csrf_e'] = md5(uniqid());

$team1_id = $row_match_details['team1_id'];
$team2_id = $row_match_details['team2_id'];

//team 1 players
$sql = <<<EOF
SELECT
t1.id,
t1.name,
t1.squad_number,
t1.status,
t1.display_order
FROM match_players t1
WHERE
t1.match_id='{$id}'
AND t1.team_id = '{$team1_id}'
ORDER BY t1.status ASC,  t1.display_order ASC, t1.name ASC
EOF;

$Recordset_Players = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$players_team1 = array();

while ($row_player = mysqli_fetch_assoc($Recordset_Players)) {
    $players_team1[] = $row_player;
}

//team 2 players
$sql = <<<EOF
SELECT
t1.id,
t1.name,
t1.squad_number,
t1.status,
t1.display_order
FROM match_players t1
WHERE
t1.match_id='{$id}'
AND t1.team_id = '{$team2_id}'
ORDER BY t1.status ASC,  t1.display_order ASC, t1.name ASC
EOF;

$Recordset_Players = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$players_team2 = array();

while ($row_player = mysqli_fetch_assoc($Recordset_Players)) {
    $players_team2[] = $row_player;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $label_array[81]; ?></title>
    <!-- Bootstrap -->
    <script
            src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous">
    </script>
    <style>
        .label-list {
            font-size: 30px;
        }

    </style>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include('menu_section.php'); ?>
<div class="container">
    <div class="row" style="margin-top: 10px;">
        <p><strong><?php echo $label_array[81]; ?></strong></p>
        <form action="" method="post">
            <div class="col-lg-6 border"><h3><?php echo htmlspecialchars($row_match_details['home_team']); ?></h3>
                <div class="input-group mb-3">
                    <label style="width: 320px;"></label>
                    <label class="owner-start-label label-list" style="width: 60px; text-align: center">?</label>
                    <label class="owner-reserve-label label-list" style="width: 60px;text-align: center">?</label>
                    <label class="owner-notav-label label-list" style="width: 60px;text-align: center">?</label>
                    <label style="width: 200px;">Name</label>
                    <label style="width: 60px;text-align: center">Number</label>
                    <label style="width: 60px;text-align: center">Order</label>
                    <label style="width: 60px;text-align: center">Start</label>
                    <label style="width: 60px;text-align: center">Reserve</label>
                    <label style="width: 60px;text-align: center">not</label>
                </div>
                <?php foreach ($players_team1 as $player) {?>
                    <div class="input-group mb-3">
                        <input class="form-control form-control-sm" type="text"   name="name<?php echo $player['id']; ?>"
                               value="<?php echo htmlspecialchars($player['name']); ?>"
                               style="width:200px;" aria-describedby="basic-addon1"/>


                        <input class="form-control form-control-sm" type="text" name="squad_number<?php echo $player['id']; ?>"
                               value="<?php echo htmlspecialchars
                               ($player['squad_number']); ?>"
                               style="width:60px;"/>
                        <input class="form-control form-control-sm" type="text" name="display_order<?php echo $player['id']; ?>"
                               value="<?php echo htmlspecialchars
                               ($player['display_order']); ?>"
                               style="width:60px;"/>

                        <input onchange="countCheck();"
                               class="owner-start" type="radio"
                               name="status<?php echo $player['id']; ?>"
                               value="first_eleven"
                            <?php if ($player['status'] == 'first_eleven') {
                                echo ' checked';
                            }
                            ?> style="width:60px;">

                        <input onchange="countCheck();"
                               class="owner-reserve"  type="radio"
                               name="status<?php echo $player['id']; ?>"
                               value="substitute"
                            <?php if ($player['status'] == 'substitute') {
                                echo ' checked';
                            }
                            ?> style="width:60px;">
                        <input onchange="countCheck();"
                               class="owner-not-available"  type="radio"
                               name="status<?php echo $player['id']; ?>"
                               value="not_available"
                            <?php if ($player['status'] == 'not_available') {
                                echo ' checked';
                            }
                            ?> style="width:60px;" >

                    </div>
                <?php } ?>



            </div>



            <div class="col-lg-6 border"><h3><?php echo htmlspecialchars($row_match_details['away_team']); ?></h3>
                <div class="start">
                    <div class="input-group mb-3">
                        <label for="" style="width: 320px;"></label>
                        <label class="guest-start-label label-list" style="width: 60px; text-align: center">?</label>
                        <label class="guest-reserve-label label-list"  style="width: 60px;text-align: center"">?</label>
                        <label class="guest-notav-label label-list"  style="width: 60px;text-align: center"">?</label>
                        <label style="width: 200px;">Name</label>
                        <label style="width: 60px;text-align: center">Number</label>
                        <label style="width: 60px;text-align: center">Order</label>
                        <label style="width: 60px;text-align: center">Start</label>
                        <label style="width: 60px;text-align: center">Reserve</label>
                        <label style="width: 60px;text-align: center">not</label>
                    </div>
                    <?php foreach ($players_team2 as $player) {?>
                        <div class="input-group mb-3">
                            <input class="form-control form-control-sm" type="text"   name="name<?php echo $player['id']; ?>"
                                   value="<?php echo htmlspecialchars($player['name']); ?>"
                                   style="width:200px;" aria-describedby="basic-addon1"/>
                            <input class="form-control form-control-sm" type="text" name="squad_number<?php echo $player['id']; ?>"
                                   value="<?php echo htmlspecialchars
                                   ($player['squad_number']); ?>"
                                   style="width:60px;"/>
                            <input class="form-control form-control-sm" type="text" name="display_order<?php echo $player['id']; ?>"
                                   value="<?php echo htmlspecialchars
                                   ($player['display_order']); ?>"
                                   style="width:60px;"/>


                            <input onchange="countCheck();"
                                   class="guest-start" type="radio"
                                   name="status<?php echo $player['id']; ?>"
                                   value="first_eleven"
                                <?php if ($player['status'] == 'first_eleven') {
                                    echo ' checked';
                                }
                                ?> style="width:60px;">

                            <input onchange="countCheck();"
                                   class="guest-reserve" type="radio"
                                   name="status<?php echo $player['id']; ?>"
                                   value="substitute"
                                <?php if ($player['status'] == 'substitute') {
                                    echo ' checked';
                                }
                                ?> style="width:60px;">

                            <input onchange="countCheck();"
                                   class="guest-not-available" type="radio"
                                   name="status<?php echo $player['id']; ?>"
                                   value="not_available"
                                <?php if ($player['status'] == 'not_available') {
                                    echo ' checked';
                                }
                                ?> style="width:60px;" >

                        </div>
                    <?php } ?>
                </div>

            </div>
            <div class="col-lg-12">
                <input type="hidden" name="csrf_e" value="<?php echo $_SESSION['csrf_e']; ?>"/>
                <input name="Submit" class="btn btn-primary" value="<?php echo $label_array[2]; ?>" type="submit"/>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(countCheck());

    function countCheck()
    {
        let CountOwnerStart = $('input[class=owner-start]:checked').length;
        let CountOwnerReserve = $('input[class=owner-reserve]:checked').length;
        let CountOwnerNotAv = $('input[class=owner-not-available]:checked').length;

        let CountGuestStart = $('input[class=guest-start]:checked').length;
        let CountGuestReserve = $('input[class=guest-reserve]:checked').length;
        let CountGuestNotAv = $('input[class=guest-not-available]:checked').length;

        console.log(CountOwnerStart); $('.owner-start-label').html(CountOwnerStart);
        console.log(CountOwnerReserve); $('.owner-reserve-label').html(CountOwnerReserve);
        console.log(CountOwnerNotAv); $('.owner-notav-label').html(CountOwnerNotAv);

        console.log(CountGuestStart); $('.guest-start-label').html(CountGuestStart);
        console.log(CountGuestReserve); $('.guest-reserve-label').html(CountGuestReserve);
        console.log(CountGuestNotAv); $('.guest-notav-label').html(CountGuestNotAv);

    }

</script>



</body>
</html>