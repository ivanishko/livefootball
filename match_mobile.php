<?php
/*
 * This is the match detail page. (simple version)
 * This page can also be shown using iframe.
 * Ex:
 * <iframe src="http://yoursite.com/match_mobile.php?id=1" width="600" height="800" scrolling="yes" frameborder="0"></iframe>
 */
require_once('config/site.php');
require_once('Connections/conn.php');

$id = 0;
if (isset($_GET['id'])){ $id = intval($_GET['id']); }

if ($script_match_type == 'soccer') {
    $sql = <<<EOF
SELECT
t1.id,
t1.status,
t1.last_start_time,
t1.team1_id,
t1.team2_id,
t1.id,
t1.title,
t1.description,
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
        die('match not exists');
    }
}
elseif ($script_match_type == 'rugby') {
    $sql = <<<EOF
SELECT
t1.id,
t1.status,
t1.last_start_time,
t1.team1_id,
t1.team2_id,
t1.id,
t1.title,
t1.description,
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
    IFNULL(SUM(t6.point), 0)
    FROM rugby_match_scores t4
    INNER JOIN rugby_score_types t6 ON t4.rugby_score_type_id = t6.id
    INNER JOIN match_players t5 ON t4.match_player_id = t5.id
    WHERE
    t5.team_id = t2.id
    AND t5.match_id = t1.id
    AND t4.is_deleted = 0
) AS home_goal_sum,
(
    SELECT
    IFNULL(SUM(t6.point), 0)
    FROM rugby_match_scores t4
    INNER JOIN rugby_score_types t6 ON t4.rugby_score_type_id = t6.id
    INNER JOIN match_players t5 ON t4.match_player_id = t5.id
    WHERE
    t5.team_id = t3.id
    AND t5.match_id = t1.id
    AND t4.is_deleted = 0
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
        die('match not exists');
    }
}


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
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($row_match_details['title'] . ' (' . $row_match_details['match_date'] . ')');
        ?></title>

<link href="css/bootstrap.min.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
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

<table class="table table-bordered">
	<tr>
    	<td align="center">
    	<h3><?php echo htmlspecialchars($row_match_details['home_team']); ?></h3>
        <br/>
        <img src="<?php echo $row_match_details['home_team_logo']; ?>" height="60" width="60"/>
        <br/>
        <?php echo htmlspecialchars($row_match_details['home_team_manager']); ?>
        </td>
        <td align="center">
        <h2><?php echo nl2br(htmlspecialchars($row_match_details['title'])); ?></h2>
        <h4><?php echo htmlspecialchars($row_match_details['stadium']); ?></h4>
    	<table class="table table-bordered">
            <tr>
                <td align="center"
                    style="font-size:42px;"><?php echo '<b>' . htmlspecialchars(
                            $row_match_details['home_goal_sum']
                        ) . '</b>'; ?>
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
        <?php echo nl2br(htmlspecialchars($row_match_details['description'])); ?>
        </td>
        <td align="center">
    	<h3><?php echo htmlspecialchars($row_match_details['away_team']); ?></h3>
        <br/>
        <img src="<?php echo $row_match_details['away_team_logo']; ?>" height="60" width="60"/>
        <br/>
        <?php echo htmlspecialchars($row_match_details['away_team_manager']); ?>
        </td>
    </tr>
</table>

<span id="commentary" style="overflow-x:hidden;">
<?php require("commentary.php"); ?>
</span>

<script type="text/javascript" language="javascript1.2">
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

</body>
</html>