<?php
/*
 * This is the match detail page.
 * Users will come to page from the match listing page. (index.php)
 */
require_once('config/site.php');
require_once('Connections/conn.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($script_match_type == 'soccer') {
        $sql = <<<EOF
SELECT
t1.id,
t1.status,
t1.last_start_time,
t1.team1_id,
t1.team2_id,
t1.title,
t1.match_date,
t1.stadium,
t1.referee_head,
t1.referee_assistant,
t1.referee_assistant2,
t1.referee_fourth,
t1.match_revision_nr,
t1.comment_revision_nr,
t2.name AS home_team,
t3.name AS away_team,
t2.logo AS home_team_logo,
t3.logo AS away_team_logo,
t2.manager AS home_team_manager,
t3.manager AS away_team_manager,
(
    SELECT
    COUNT(1)
    FROM match_goals t4
    INNER JOIN match_players t5 ON t4.match_player_id = t5.id
    WHERE
    (
        (
            t5.team_id = t2.id
            AND t4.is_own_goal = 0
            AND t5.match_id = t1.id
        )
        OR
        (
            t5.team_id = t3.id
            AND t4.is_own_goal = 1
            AND t5.match_id = t1.id
        )
    )
    AND t4.is_deleted = 0
) AS home_goal_sum,
(
    SELECT
    COUNT(1)
    FROM match_goals t6
    INNER JOIN match_players t7 ON t6.match_player_id = t7.id
    WHERE
    (
        (
            t7.team_id = t3.id
            AND t6.is_own_goal = 0
            AND t7.match_id = t1.id
        )
        OR
        (
            t7.team_id = t2.id
            AND t6.is_own_goal = 1
            AND t7.match_id = t1.id
        )
    )
    AND t6.is_deleted = 0
) AS away_goal_sum
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
            header("Location: index.php");
            exit();
        }

    }

} else {
    header("Location: index.php");
    exit();
}

$team1_id = $row_match_details['team1_id'];
$team2_id = $row_match_details['team2_id'];

//match comments - begin
$sql = <<<EOF
SELECT
t1.id,
t1.comment,
t1.comment_type,
t1.comment_minute
FROM commentation t1
WHERE
t1.match_id='{$id}'
AND t1.is_deleted = 0
ORDER BY t1.insert_time DESC
EOF;

$Recordset_Comments = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$match_comments = array();

while ($row_comment = mysqli_fetch_assoc($Recordset_Comments)) {
    $match_comments[] = $row_comment;
}
//match comments - end

//match goals - begin
if ($script_match_type == 'soccer'){
    $sql = <<<EOF
SELECT
t1.id,
t1.goal_minute,
t1.is_penalty_goal,
t1.is_own_goal,
t1.insert_time,
t2.name,
t2.squad_number,
t2.team_id,
t2.id AS player_id
FROM match_goals t1
INNER JOIN match_players t2 ON t1.match_player_id = t2.id
WHERE
t2.match_id = '{$id}'
AND t1.is_deleted = 0
ORDER BY t1.insert_time ASC
EOF;

    $Recordset_Goals = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $goals_team1 = array();
    $goals_team2 = array();

    while ($row_goal = mysqli_fetch_assoc($Recordset_Goals)) {
        if ($row_goal['team_id'] == $team1_id) {
            $goals_team1[] = $row_goal;
        } else {
            $goals_team2[] = $row_goal;
        }
    }
}
//match goals - end



//match cards - begin
$sql = <<<EOF
SELECT
t1.id,
t1.card_minute,
t1.card_type,
t1.insert_time,
t2.name,
t2.squad_number,
t2.team_id,
t2.id AS player_id
FROM match_cards t1
INNER JOIN match_players t2 ON t1.match_player_id = t2.id
WHERE
t2.match_id = '{$id}'
AND t1.is_deleted = 0
ORDER BY t1.insert_time ASC
EOF;

$Recordset_Cards = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$cards_team1 = array();
$cards_team2 = array();

while ($row_card = mysqli_fetch_assoc($Recordset_Cards)) {
    if ($row_card['team_id'] == $team1_id) {
        $cards_team1[] = $row_card;
    } else {
        $cards_team2[] = $row_card;
    }
}
//match cards - end

//match substitutions - begin
$sql = <<<EOF
SELECT
t1.id,
t1.substitution_minute,
t1.insert_time,
t2.name AS player_name_in,
t2.squad_number AS squad_number_in,
t2.team_id AS team_id_in,
t2.id AS player_id_in,
t3.name AS player_name_out,
t3.squad_number AS squad_number_out,
t3.team_id AS team_id_out,
t3.id AS player_id_out
FROM match_substitutions t1
INNER JOIN match_players t2 ON t1.match_player_id_in = t2.id
INNER JOIN match_players t3 ON t1.match_player_id_out = t3.id
WHERE
t2.match_id = '{$id}'
AND t1.is_deleted = 0
ORDER BY t1.insert_time ASC
EOF;

$Recordset_Substitutions = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$substitutions_team1 = array();
$substitutions_team2 = array();

while ($row_substitution = mysqli_fetch_assoc($Recordset_Substitutions)) {
    if ($row_substitution['team_id_in'] == $team1_id) {
        $substitutions_team1[] = $row_substitution;
    } else {
        $substitutions_team2[] = $row_substitution;
    }
}
//match substitutions - end

//soccer team players - begin
if ($script_match_type == 'soccer'){
    //team 1 players
    $sql = <<<EOF
    SELECT
    t1.id,
    t1.name,
    t1.squad_number,
    t1.status,
    (
        SELECT
        COUNT(1)
        FROM match_goals t2
        WHERE
        t2.match_player_id = t1.id
        AND t2.is_deleted = 0
    ) AS goal_sum
    FROM match_players t1
    WHERE
    t1.match_id='{$id}'
    AND t1.team_id = '{$team1_id}'
    AND NOT t1.status = 'not_available'
    ORDER BY t1.status ASC,  t1.display_order ASC, t1.name ASC
EOF;

    $Recordset_Players = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $players_team1 = array();
    $players_team1_first11 = array();
    $players_team1_substitute = array();

    while ($row_player = mysqli_fetch_assoc($Recordset_Players)) {
        $players_team1[] = $row_player;

        if ($row_player['status'] == 'first_eleven') {
            $players_team1_first11[] = $row_player;
        } elseif  ($row_player['status'] == 'substitute'){
            $players_team1_substitute[] = $row_player;
        }
        else {
            $players_team1_coach[] = $row_player;
        }
    }

    //team 2 players
    $sql = <<<EOF
    SELECT
    t1.id,
    t1.name,
    t1.squad_number,
    t1.status,
    (
        SELECT
        COUNT(1)
        FROM match_goals t2
        WHERE
        t2.match_player_id = t1.id
        AND t2.is_deleted = 0
    ) AS goal_sum
    FROM match_players t1
    WHERE
    t1.match_id='{$id}'
    AND t1.team_id = '{$team2_id}'
    AND NOT t1.status = 'not_available'
    ORDER BY t1.status ASC,  t1.display_order ASC, t1.name ASC
EOF;

    $Recordset_Players = mysqli_query($conn, $sql) or die(mysqli_error($conn));

    $players_team2 = array();
    $players_team2_first11 = array();
    $players_team2_substitute = array();

    while ($row_player = mysqli_fetch_assoc($Recordset_Players)) {
        $players_team2[] = $row_player;

        if ($row_player['status'] == 'first_eleven') {
            $players_team2_first11[] = $row_player;
        } else {
            $players_team2_substitute[] = $row_player;
        }
    }
}
//soccer team players - end




$match_status_text = '';

switch ($row_match_details['status']) {
    case 'first_half':
        $match_status_text = $label_array[108]; //First Half
        break;
    case 'half_time':
        $match_status_text = $label_array[109]; //Half-Time
        break;
    case 'second_half':
        $match_status_text = $label_array[110]; //Second Half
        break;
    case 'finished':
        $match_status_text = $label_array[111]; //End of Match
        break;
    case 'first_dop_time':
        $match_status_text = $label_array[144]; //End of Match
        break;
    case 'second_dop_time':
        $match_status_text = $label_array[145]; //End of Match
        break;
}

$start_time = 1;

switch ($row_match_details['status']) {
    case "first_half":
        $start_time = time() - $row_match_details['last_start_time'];
        break;
    case 'second_half':
        $start_time = $script_match_first_half_length * 60 + (time() - $row_match_details['last_start_time']);
        break;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($row_match_details['title'] . ' (' . $row_match_details['match_date'] . ')');
        ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script language="javascript1.2" type="text/javascript">
        var i_s = <?php echo $start_time; ?>;
        var match_status = '<?php echo $row_match_details['status']; ?>';
        //////////////////////////////////////////////////////////////////////////
        var minutes = 0;
        var seconds = 0;

        function show_elapsed_time() {
            switch (match_status) {
                case 'temp':
                case 'not_started':
                    document.getElementById('time_elapsed').innerHTML = "00 : 00";
                    return true;
                    break;
                case 'half_time':
                    document.getElementById('time_elapsed').innerHTML = "<?php echo $script_match_first_half_length .
                    ' : 00'; ?>";
                    return true;
                    break;
                case 'finished':
                    document.getElementById('time_elapsed').innerHTML = "<?php echo $script_match_full_length .
                    ' : 00'; ?>";
                    return true;
                    break;
            }
            minutes = Math.floor(i_s / 60);
            seconds = i_s - (minutes * 60);
            if (minutes < 10) {
                minutes = "0" + minutes.toString();
            }
            if (seconds < 10) {
                seconds = "0" + seconds.toString();
            }
            document.getElementById('time_elapsed').innerHTML = minutes + " : " + seconds;
            i_s++;
            if (match_status == 'first_half' || match_status == 'second_half') {
                setTimeout('show_elapsed_time()', 1000);
            }
        }

        function refresh_page() {
            location.reload(true);
        }

        show_elapsed_time();
    </script>

</head>

<body>

<div class="container">
<div class="row" style="margin-top: 10px;">

<p><a href="index.php"><?php echo $label_array[114]; ?></a></p>

<div class="panel panel-default">
<div class="panel-heading">
    <h3><?php echo htmlspecialchars($row_match_details['title'] . ' (' . $row_match_details['match_date'] . ')');
        ?></h3></div>
<div class="panel-body">

<table class="table table-bordered" width="800">
<tr>
<td align="center" valign="top" class="col-md-3">
    <h3><?php echo htmlspecialchars($row_match_details['home_team']); ?></h3>
    <br/>
    <img src="<?php echo htmlspecialchars($row_match_details['home_team_logo']); ?>" height="130" width="130"/>
    <br/>
    <!-- здесь был главный тренер домашников -->



    <br/><br/>
    <table class="table table-bordered" style="font-size: 12px;">
        <tr class="info">
<?php if ($script_match_type == 'soccer'){ ?>
            <td><?php echo $label_array[58]; ?></td>
<?php } elseif ($script_match_type == 'rugby'){ ?>
    <td><?php echo $label_array[131]; ?></td>
<?php } ?>
        </tr>
        <?php foreach ($players_team1_first11 as $player) {
            ?>
            <tr>
                <td align="left">
                    <span
                        style="width: 18px; display:inline-block; color:#FF8040;"><?php echo htmlspecialchars(
                            $player['squad_number']
                        );
                        ?></span>
                    <span><?php echo htmlspecialchars($player['name']); ?></span>
                    <?php
                    $player_timeline = array();

                    //match goals - begin
                    if ($script_match_type == 'soccer'){
                        foreach ($goals_team1 as $goal) {
                            if ($goal['player_id'] == $player['id']) {
                                ob_start();
                                ?>
                                <img src="img/soccer_ball.png" id="p_g_<?php echo $goal['id']; ?>"
                                     data-toggle="tooltip"
                                     title="<?php echo str_replace(array('%1'), array($goal['goal_minute']),
                                             $label_array[116]) . htmlspecialchars(
                                             ($goal['is_own_goal'] ? '
                                      (' . $label_array[113] . ')' : '')
                                             . ($goal['is_penalty_goal'] ? ' (' . $label_array[112] . ')' : '')
                                         ); ?>"/>
                                <?php
                                $player_timeline[$goal['insert_time']] = trim(ob_get_clean());
                            }
                        }
                    }

                    //match goals - end

                    //cards
                    foreach ($cards_team1 as $card) {
                        if ($card['player_id'] == $player['id']) {
                            ob_start();
                            ?>
                            <img
                               src="<?php echo 'img/' . (($card['card_type'] == 'yellow') ? 'yellow_card.png' : (($card['card_type'] == 'second_yellow') ? 'second_yellow.png' :
                                        'red_card.png')); ?>"
                               

                               

                                id="p_c_<?php echo $card['id']; ?>" data-toggle="tooltip"
                                title="<?php echo str_replace(array('%1'),
                                    array(htmlspecialchars($card['card_minute'])), $label_array[116]); ?>"/>
                            <?php
                            $player_timeline[$card['insert_time']] = trim(ob_get_clean());
                        }
                    }
                    //substitutions
                    foreach ($substitutions_team1 as $substitution) {
                        if ($substitution['player_id_in'] == $player['id']) {
                            ob_start();
                            ?>
                            <img src="img/in.png"
                                 id="p_s_<?php echo $substitution['id'] . '_' . $substitution['player_id_in']; ?>"
                                 data-toggle="tooltip"
                                 title="<?php echo str_replace(array('%1', '%2'), array(
                                         htmlspecialchars($substitution['substitution_minute']),
                                         htmlspecialchars($substitution['player_name_out'])), $label_array[115]); ?>"/>
                            <?php
                            $player_timeline[$substitution['insert_time']] = trim(ob_get_clean());
                        } elseif ($substitution['player_id_out'] == $player['id']) {
                            ob_start();
                            ?>
                            <img src="img/out.png"
                                 id="p_s_<?php echo $substitution['id'] . '_' . $substitution['player_id_out']; ?>"
                                 data-toggle="tooltip"
                                 title="<?php echo str_replace(array('%1', '%2'), array(
                                         htmlspecialchars($substitution['substitution_minute']),
                                         htmlspecialchars($substitution['player_name_in'])), $label_array[115]); ?>"/>
                            <?php
                            $player_timeline[$substitution['insert_time']] = trim(ob_get_clean());
                        }
                    }

                    ksort($player_timeline);

                    if (count($player_timeline) > 0) {
                        echo '&nbsp;' . implode('&nbsp;', $player_timeline);
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
        <tr class="info">
            <td><?php echo $label_array[59]; ?></td>
        </tr>
        <?php foreach ($players_team1_substitute as $player) {
            ?>
            <tr>
                <td align="left">
                    <span
                        style="width: 18px; display:inline-block; color:#FF8040;"><?php echo htmlspecialchars(
                            $player['squad_number']
                        );
                        ?></span>
                    <span><?php echo htmlspecialchars($player['name']); ?></span>
                    <?php
                    $player_timeline = array();

                    //match goals - begin
                    if ($script_match_type == 'soccer'){
                        foreach ($goals_team1 as $goal) {
                            if ($goal['player_id'] == $player['id']) {
                                ob_start();
                                ?>
                                <img src="img/soccer_ball.png" id="p_g_<?php echo $goal['id']; ?>"
                                     data-toggle="tooltip"
                                     title="<?php echo str_replace(array('%1'), array($goal['goal_minute']),
                                             $label_array[116]) . htmlspecialchars(
                                             ($goal['is_own_goal'] ? '
                                  (' . $label_array[113] . ')' : '')
                                             . ($goal['is_penalty_goal'] ? ' (' . $label_array[112] . ')' : '')
                                         ); ?>"/>
                                <?php
                                $player_timeline[$goal['insert_time']] = trim(ob_get_clean());
                            }
                        }
                    }

                    //match goals - end

                    //cards
                    foreach ($cards_team1 as $card) {
                        if ($card['player_id'] == $player['id']) {
                            ob_start();
                            ?>
                            <img
                                src="<?php echo 'img/' . (($card['card_type'] == 'yellow') ? 'yellow_card.png' : (($card['card_type'] == 'second_yellow') ? 'second_yellow.png' :
                                        'red_card.png')); ?>"

                                id="p_c_<?php echo $card['id']; ?>" data-toggle="tooltip"
                                title="<?php echo str_replace(array('%1'),
                                    array(htmlspecialchars($card['card_minute'])), $label_array[116]); ?>"/>
                            <?php
                            $player_timeline[$card['insert_time']] = trim(ob_get_clean());
                        }
                    }
                    //substitutions
                    foreach ($substitutions_team1 as $substitution) {
                        if ($substitution['player_id_in'] == $player['id']) {
                            ob_start();
                            ?>
                            <img src="img/in.png"
                                 id="p_s_<?php echo $substitution['id'] . '_' . $substitution['player_id_in']; ?>"
                                 data-toggle="tooltip"
                                 title="<?php echo str_replace(array('%1', '%2'), array(
                                         htmlspecialchars($substitution['substitution_minute']),
                                         htmlspecialchars($substitution['player_name_out'])), $label_array[115]); ?>"/>
                            <?php
                            $player_timeline[$substitution['insert_time']] = trim(ob_get_clean());
                        } elseif ($substitution['player_id_out'] == $player['id']) {
                            ob_start();
                            ?>
                            <img src="img/out.png"
                                 id="p_s_<?php echo $substitution['id'] . '_' . $substitution['player_id_out']; ?>"
                                 data-toggle="tooltip"
                                 title="<?php echo str_replace(array('%1', '%2'), array(
                                         htmlspecialchars($substitution['substitution_minute']),
                                         htmlspecialchars($substitution['player_name_in'])), $label_array[115]); ?>"/>
                            <?php
                            $player_timeline[$substitution['insert_time']] = trim(ob_get_clean());
                        }
                    }

                    ksort($player_timeline);

                    if (count($player_timeline) > 0) {
                        echo '&nbsp;' . implode('&nbsp;', $player_timeline);
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>

        <tr class="info">
            <td></td>


        </tr>
        <?php foreach ($players_team1_coach as $coach) { ?>
        <tr>
            <td align="left">
                <span style="width: 90px; display:inline-block; color:#FF8040;">
                <?php echo $coach['squad_number']; ?>
                </span>
                <?php echo $coach['name']; ?>
            </td>
        </tr>
        <?php } ?>

    </table>

</td>
<td valign="top" align="center">
    <?php if ($row_match_details['stadium'] != '') {
        ?>
        <h4><?php echo htmlspecialchars($row_match_details['stadium']); ?></h4>
    <?php } ?>
    <?php if ($row_match_details['referee_head'] != '') {
        ?>
        <strong><?php echo $label_array[11]; ?>:</strong> <?php echo htmlspecialchars
        ($row_match_details['referee_head']); ?><br/>
    <?php } ?>
    <?php if ($row_match_details['referee_assistant'] != '') {
        ?>
        <strong><?php echo $label_array[12]; ?>:</strong> <?php echo htmlspecialchars
        ($row_match_details['referee_assistant']); ?><br/>
    <?php } ?>
    <?php if ($row_match_details['referee_assistant2'] != '') {
        ?>
        <strong><?php echo $label_array[13]; ?>:</strong> <?php echo htmlspecialchars
        ($row_match_details['referee_assistant2']); ?><br/>
    <?php } ?>
    <?php if ($row_match_details['referee_fourth'] != '') {
        ?>
        <strong><?php echo $label_array[14]; ?>:</strong> <?php echo htmlspecialchars
        ($row_match_details['referee_fourth']); ?><br/>
    <?php } ?>
    <br/>
    <table class="table table-bordered">
        <tr>
            <td align="center"
                style="font-size:42px;">
                <?php if ($row_match_details['status'] == 'not_started') {echo ' ' ;} else  {echo '<b>' . htmlspecialchars(
                        $row_match_details['home_goal_sum'] 
                    ) . '</b>';}  ?>
            </td>
            <td align="center">
                <span id="time_elapsed" style="font-size:22px; font-weight:bold;"></span>
                <br/><?php echo $match_status_text; ?>
            </td>
            <td align="center"
                style="font-size:42px;"><?php echo '<b>' . htmlspecialchars(
                        $row_match_details['away_goal_sum']
                    ) . '</b>'; ?>
            </td>
        </tr>
    </table>

    <?php
    //score table - begin
    if ($script_match_type == 'soccer'){
        if (count($goals_team1) > 0 || count($goals_team2) > 0) {
            $goal_timeline1 = array();
            $goal_timeline2 = array();

            foreach ($goals_team1 as $goal) {
                if ($goal['is_own_goal']){
                    $goal_timeline2[$goal['insert_time']] = $goal['goal_minute'] . '\' ' . $goal['name'] . htmlspecialchars(
                            ($goal['is_own_goal'] ? '
              (' . $label_array[113] . ')' : '')
                            . ($goal['is_penalty_goal'] ? ' (' . $label_array[112] . ')' : '')
                        );
                }
                else{
                    $goal_timeline1[$goal['insert_time']] = $goal['goal_minute'] . '\' ' . $goal['name'] . htmlspecialchars(
                            ($goal['is_own_goal'] ? '
              (' . $label_array[113] . ')' : '')
                            . ($goal['is_penalty_goal'] ? ' (' . $label_array[112] . ')' : '')
                        );
                }
            }

            foreach ($goals_team2 as $goal) {
                if ($goal['is_own_goal']){
                    $goal_timeline1[$goal['insert_time']] = $goal['goal_minute'] . '\' ' . $goal['name'] . htmlspecialchars(
                            ($goal['is_own_goal'] ? '
              (' . $label_array[113] . ')' : '')
                            . ($goal['is_penalty_goal'] ? ' (' . $label_array[112] . ')' : '')
                        );
                }
                else{
                    $goal_timeline2[$goal['insert_time']] = $goal['goal_minute'] . '\' ' . $goal['name'] . htmlspecialchars(
                            ($goal['is_own_goal'] ? '
              (' . $label_array[113] . ')' : '')
                            . ($goal['is_penalty_goal'] ? ' (' . $label_array[112] . ')' : '')
                        );
                }
            }
            ?>
            <table class="table table-bordered">
                <tr>
                    <td align="left" class="col-sm-6">
                        <?php
                        echo implode('<br>', $goal_timeline1);
                        ?>
                    </td>
                    <td align="right" class="col-sm-6">
                        <?php
                        echo implode('<br>', $goal_timeline2);
                        ?>
                    </td>
                </tr>
            </table>
        <?php
        }
    }

    ?>

<span id="commentary" style="height: 780px; display: block; overflow:scroll; overflow-x:hidden;">
<?php require("commentary.php"); ?>
</span>

</td>
<td align="center" valign="top" class="col-md-3">
    <h3><?php echo htmlspecialchars($row_match_details['away_team']); ?></h3>
    <br/>
    <img src="<?php echo $row_match_details['away_team_logo']; ?>" height="130" width="130"/>
    <br/>
<!-- здесь был главный тренер гостей -->
    <br/><br/>
    <table class="table table-bordered" style="font-size: 12px;">
        <tr class="info">
            <?php if ($script_match_type == 'soccer'){ ?>
                <td><?php echo $label_array[58]; ?></td>
            <?php } elseif ($script_match_type == 'rugby'){ ?>
                <td><?php echo $label_array[131]; ?></td>
            <?php } ?>
        </tr>
        <?php foreach ($players_team2_first11 as $player) {
            ?>
            <tr>
                <td align="left">
                    <span
                        style="width: 18px; display:inline-block; color:#FF8040;"><?php echo htmlspecialchars(
                            $player['squad_number']
                        );
                        ?></span>
                    <span><?php echo htmlspecialchars($player['name']); ?></span>
                    <?php
                    $player_timeline = array();

                    //match goals - begin
                    if ($script_match_type == 'soccer'){
                        foreach ($goals_team2 as $goal) {
                            if ($goal['player_id'] == $player['id']) {
                                ob_start();
                                ?>
                                <img src="img/soccer_ball.png" id="p_g_<?php echo $goal['id']; ?>"
                                     data-toggle="tooltip"
                                     title="<?php echo str_replace(array('%1'), array($goal['goal_minute']),
                                             $label_array[116]) . htmlspecialchars(
                                             ($goal['is_own_goal'] ? '
                                  (' . $label_array[113] . ')' : '')
                                             . ($goal['is_penalty_goal'] ? ' (' . $label_array[112] . ')' : '')
                                         ); ?>"/>
                                <?php
                                $player_timeline[$goal['insert_time']] = trim(ob_get_clean());
                            }
                        }
                    }

                    //match goals - end

                    //cards
                    foreach ($cards_team2 as $card) {
                        if ($card['player_id'] == $player['id']) {
                            ob_start();
                            ?>
                            <img
                                src="<?php echo 'img/' . (($card['card_type'] == 'yellow') ? 'yellow_card.png' : (($card['card_type'] == 'second_yellow') ? 'second_yellow.png' :
                                        'red_card.png')); ?>"
                                id="p_c_<?php echo $card['id']; ?>" data-toggle="tooltip"
                                title="<?php echo str_replace(array('%1'),
                                    array(htmlspecialchars($card['card_minute'])), $label_array[116]); ?>"/>
                            <?php
                            $player_timeline[$card['insert_time']] = trim(ob_get_clean());
                        }
                    }
                    //substitutions
                    foreach ($substitutions_team2 as $substitution) {
                        if ($substitution['player_id_in'] == $player['id']) {
                            ob_start();
                            ?>
                            <img src="img/in.png"
                                 id="p_s_<?php echo $substitution['id'] . '_' . $substitution['player_id_in']; ?>"
                                 data-toggle="tooltip"
                                 title="<?php echo str_replace(array('%1', '%2'), array(
                                         htmlspecialchars($substitution['substitution_minute']),
                                         htmlspecialchars($substitution['player_name_out'])), $label_array[115]); ?>"/>
                            <?php
                            $player_timeline[$substitution['insert_time']] = trim(ob_get_clean());
                        } elseif ($substitution['player_id_out'] == $player['id']) {
                            ob_start();
                            ?>
                            <img src="img/out.png"
                                 id="p_s_<?php echo $substitution['id'] . '_' . $substitution['player_id_out']; ?>"
                                 data-toggle="tooltip"
                                 title="<?php echo str_replace(array('%1', '%2'), array(
                                         htmlspecialchars($substitution['substitution_minute']),
                                         htmlspecialchars($substitution['player_name_in'])), $label_array[115]); ?>"/>
                            <?php
                            $player_timeline[$substitution['insert_time']] = trim(ob_get_clean());
                        }
                    }

                    ksort($player_timeline);

                    if (count($player_timeline) > 0) {
                        echo '&nbsp;' . implode('&nbsp;', $player_timeline);
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
        <tr class="info">
            <td><?php echo $label_array[59]; ?></td>
        </tr>
        <?php foreach ($players_team2_substitute as $player) {
            ?>
            <tr>
                <td align="left">
                    <span
                        style="width: 18px; display:inline-block; color:#FF8040;"><?php echo htmlspecialchars(
                            $player['squad_number']
                        );
                        ?></span>
                    <span><?php echo htmlspecialchars($player['name']); ?></span>
                    <?php
                    $player_timeline = array();

                    //match goals - begin
                    if ($script_match_type == 'soccer'){
                        foreach ($goals_team2 as $goal) {
                            if ($goal['player_id'] == $player['id']) {
                                ob_start();
                                ?>
                                <img src="img/soccer_ball.png" id="p_g_<?php echo $goal['id']; ?>" data-toggle="tooltip"
                                     title="<?php echo str_replace(array('%1'), array($goal['goal_minute']),
                                             $label_array[116]) . htmlspecialchars(
                                             ($goal['is_own_goal'] ? '
                                  (' . $label_array[113] . ')' : '')
                                             . ($goal['is_penalty_goal'] ? ' (' . $label_array[112] . ')' : '')
                                         ); ?>"/>
                                <?php
                                $player_timeline[$goal['insert_time']] = trim(ob_get_clean());
                            }
                        }
                    }

                    //match goals - end
                    
                    //cards
                    foreach ($cards_team2 as $card) {
                        if ($card['player_id'] == $player['id']) {
                            ob_start();
                            ?>
                            <img
                                src="<?php echo 'img/' . (($card['card_type'] == 'yellow') ? 'yellow_card.png' : (($card['card_type'] == 'second_yellow') ? 'second_yellow.png' :
                                        'red_card.png')); ?>"
                                id="p_c_<?php echo $card['id']; ?>" data-toggle="tooltip"
                                title="<?php echo str_replace(array('%1'),
                                    array(htmlspecialchars($card['card_minute'])), $label_array[116]); ?>"/>
                            <?php
                            $player_timeline[$card['insert_time']] = trim(ob_get_clean());
                        }
                    }
                    //substitutions
                    foreach ($substitutions_team2 as $substitution) {
                        if ($substitution['player_id_in'] == $player['id']) {
                            ob_start();
                            ?>
                            <img src="img/in.png"
                                 id="p_s_<?php echo $substitution['id'] . '_' . $substitution['player_id_in']; ?>"
                                 data-toggle="tooltip"
                                 title="<?php echo str_replace(array('%1', '%2'), array(
                                         htmlspecialchars($substitution['substitution_minute']),
                                         htmlspecialchars($substitution['player_name_out'])), $label_array[115]); ?>"/>
                            <?php
                            $player_timeline[$substitution['insert_time']] = trim(ob_get_clean());
                        } elseif ($substitution['player_id_out'] == $player['id']) {
                            ob_start();
                            ?>
                            <img src="img/out.png"
                                 id="p_s_<?php echo $substitution['id'] . '_' . $substitution['player_id_out']; ?>"
                                 data-toggle="tooltip"
                                 title="<?php echo str_replace(array('%1', '%2'), array(
                                     htmlspecialchars($substitution['substitution_minute']),
                                     htmlspecialchars($substitution['player_name_in'])), $label_array[115]); ?>"/>
                            <?php
                            $player_timeline[$substitution['insert_time']] = trim(ob_get_clean());
                        }
                    }

                    ksort($player_timeline);

                    if (count($player_timeline) > 0) {
                        echo '&nbsp;' . implode('&nbsp;', $player_timeline);
                    }
                    ?>
                </td>
            </tr>

        <?php } ?>
        <tr><td>    <?php echo 'Гл.тренер ' . htmlspecialchars($row_match_details['away_team_manager']); ?>
</td></tr>
    </table>
</td>
</tr>

</table>

</div>
</div>


<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>

<script type="text/javascript" language="javascript1.2">
    <?php
    if ($script_match_type == 'soccer'){
        foreach (array_merge($goals_team1, $goals_team2) as $goal){
        ?>
            $('#p_g_<?php echo $goal['id']; ?>').tooltip();
        <?php
        }
    }

    elseif ($script_match_type == 'rugby'){
        foreach (array_merge($rugby_scores_team1, $rugby_scores_team2) as $score){
        ?>
            $('#p_g_<?php echo $score['id']; ?>').tooltip();
    <?php
        }
    }

foreach (array_merge($cards_team1, $cards_team2) as $card){
?>
    $('#p_c_<?php echo $card['id']; ?>').tooltip();
    <?php
    }

    foreach (array_merge($substitutions_team1, $substitutions_team2) as $substitution){
    ?>
    $('#p_s_<?php echo $substitution['id'] . '_' . $substitution['player_id_in']; ?>').tooltip();
    $('#p_s_<?php echo $substitution['id'] . '_' . $substitution['player_id_out']; ?>').tooltip();
    <?php
    }
    ?>

    var match_revision_nr = <?php echo $row_match_details['match_revision_nr']; ?>;
    var comment_revision_nr = <?php echo $row_match_details['comment_revision_nr']; ?>;

    function checkForUpdates() {

        jQuery.get('match_revision_nr.php?id=<?php echo $id; ?>', function (data) {
            if (data.m_nr != match_revision_nr) {
                refresh_page();
            }
            else if (data.c_nr != comment_revision_nr) {
                comment_revision_nr = data.c_nr;
                jQuery('#commentary').load('commentary.php?id=<?php echo $id; ?>');
            }
        });

    }

    show_elapsed_time();
    setInterval('checkForUpdates()', 5000);
</script>

</div>
</div>
</body>
</html>