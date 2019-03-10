<?php
require_once('../config/site.php');

session_start();
if (!isset($_SESSION['commentator_user'])) {
	header('Location:index.php');
	exit();
}

require_once('../Connections/conn.php');

if (isset($_POST['auto_minute'])){
	if (in_array($_POST['auto_minute'], array('on', 'off'))){
		$_SESSION['auto_minute'] = $_POST['auto_minute'];
	}
}

$auto_minute = (isset($_SESSION['auto_minute']) ? $_SESSION['auto_minute'] : 'on');

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
t2.name AS home_team,
t3.name AS away_team,
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
AND t1.commentator_id = '{$_SESSION['commentator_user']['id']}'
EOF;

		$Recordset_MatchDetails = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		$row_match_details = mysqli_fetch_assoc($Recordset_MatchDetails);

		if (mysqli_num_rows($Recordset_MatchDetails) == 0) {
			header("Location: matches.php");
			exit();
		}
	}
	elseif ($script_match_type == 'rugby'){
		$sql = <<<EOF
SELECT
t1.id,
t1.status,
t1.last_start_time,
t1.team1_id,
t1.team2_id,
t2.name AS home_team,
t3.name AS away_team,
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
AND t1.commentator_id = '{$_SESSION['commentator_user']['id']}'
EOF;

		$Recordset_MatchDetails = mysqli_query($conn, $sql) or die(mysqli_error($conn));
		$row_match_details = mysqli_fetch_assoc($Recordset_MatchDetails);

		if (mysqli_num_rows($Recordset_MatchDetails) == 0) {
			header("Location: matches.php");
			exit();
		}

		// create rugby score types array
		$sql = <<<EOF
SELECT
t1.id,
t1.type,
t1.point
FROM rugby_score_types t1
WHERE
t1.is_deleted = 0
ORDER BY display_order ASC
EOF;

		$Recordset_ScoreTypes = mysqli_query($conn, $sql) or die(mysqli_error($conn));

		$rugby_score_types = array();
		while ($row_score_type = mysqli_fetch_assoc($Recordset_ScoreTypes)) {
			$rugby_score_types[$row_score_type['id']] = $row_score_type;
		}

	}
} else {
	header("Location: matches.php");
	exit();
}

$team1_id = $row_match_details['team1_id'];
$team2_id = $row_match_details['team2_id'];

//insert comment - begin
if (isset($_POST['insert_comment'])
	&& isset($_POST['minute'])
	&& isset($_POST['comment'])
	&& trim($_POST['comment']) != ''
	&& isset($_POST['csrf_i'])
	&& isset($_SESSION['csrf_i'])
	&& $_POST['csrf_i'] == $_SESSION['csrf_i']
) {
	if ($auto_minute == true && $_POST['minute'] == ''){
		$current_minute = '';
		
		switch ($row_match_details['status']) {
			case "first_half":
				$current_minute = floor((time() - $row_match_details['last_start_time']) / 60);
				break;
			case 'second_half':
				$current_minute = $script_match_first_half_length + floor((time() - $row_match_details['last_start_time']) / 60);
				break;
		}
		
		$_POST['minute'] = $current_minute;
	}
	
	$minute      = prepare_for_db($_POST['minute']);
	$comment     = prepare_for_db($_POST['comment']);
	$insert_time = time();

	$comment_type = prepare_for_db('standard');

	if (isset($_POST['comment_type'])
		&& in_array($_POST['comment_type'], array('standard', 'goal', 'yellow', 'red', 'substitution'))
	) {
		$comment_type = prepare_for_db($_POST['comment_type']);
	}

	$sql = <<<EOF
INSERT INTO commentation(
match_id,
comment_minute,
comment,
comment_type,
insert_time
)
VALUES(
'{$id}',
'{$minute}',
'{$comment}',
'{$comment_type}',
'{$insert_time}'
)
EOF;

	mysqli_query($conn, $sql) or die(mysqli_error($conn));

	$sql = <<<EOF
UPDATE matches
SET
comment_revision_nr = comment_revision_nr + 1
WHERE
id = $id
EOF;

	mysqli_query($conn, $sql) or die(mysqli_error($conn));

	header("Location: match_commentation.php?id=" . $id);
	exit();
}
//insert comment - end

//save rugby score record - begin
if ($script_match_type == 'rugby'){

	if (isset($_POST['insert_score'])
		&& isset($_POST['minute'])
		&& isset($_POST['match_player_id'])
		&& isset($_POST['rugby_score_type_id'])
		&& trim($_POST['minute']) != ''
		&& intval($_POST['match_player_id']) > 0
		&& intval($_POST['rugby_score_type_id']) > 0
		&& isset($_POST['csrf_i'])
		&& isset($_SESSION['csrf_i'])
		&& $_POST['csrf_i'] == $_SESSION['csrf_i']
	) {
		$match_player_id = intval($_POST['match_player_id']);
		$rugby_score_type_id = intval($_POST['rugby_score_type_id']);
		$minute          = prepare_for_db($_POST['minute']);

		$insert_time = time();

		$sql = <<<EOF
INSERT INTO rugby_match_scores(
match_player_id,
score_minute,
rugby_score_type_id,
insert_time
)
VALUES(
'{$match_player_id}',
'{$minute}',
'{$rugby_score_type_id}',
'{$insert_time}'
)
EOF;

		mysqli_query($conn, $sql) or die(mysqli_error($conn));

		$sql = <<<EOF
UPDATE matches
SET
match_revision_nr = match_revision_nr + 1
WHERE
id = $id
EOF;

		mysqli_query($conn, $sql) or die(mysqli_error($conn));

		header("Location: match_commentation.php?id=" . $id);
		exit();
	}

}
//save rugby score record - end

//save goal record - begin
if ($script_match_type == 'soccer'){
	if (isset($_POST['insert_goal'])
		&& isset($_POST['minute'])
		&& isset($_POST['match_player_id'])
		&& trim($_POST['minute']) != ''
		&& intval($_POST['match_player_id']) > 0
		&& isset($_POST['csrf_i'])
		&& isset($_SESSION['csrf_i'])
		&& $_POST['csrf_i'] == $_SESSION['csrf_i']
	) {
		$match_player_id = intval($_POST['match_player_id']);
		$minute          = prepare_for_db($_POST['minute']);
		$is_penalty_goal = (isset($_POST['is_penalty_goal']) ? 1 : 0);
		$is_own_goal     = (isset($_POST['is_own_goal']) ? 1 : 0);

		if ($is_penalty_goal == $is_own_goal && $is_penalty_goal == 1) {
			$is_own_goal = 1;
		}

		$insert_time = time();

		$sql = <<<EOF
	INSERT INTO match_goals(
	match_player_id,
	goal_minute,
	is_penalty_goal,
	is_own_goal,
	insert_time
	)
	VALUES(
	'{$match_player_id}',
	'{$minute}',
	'{$is_penalty_goal}',
	'{$is_own_goal}',
	'{$insert_time}'
	)
EOF;

		mysqli_query($conn, $sql) or die(mysqli_error($conn));

		$sql = <<<EOF
	UPDATE matches
	SET
	match_revision_nr = match_revision_nr + 1
	WHERE
	id = $id
EOF;

		mysqli_query($conn, $sql) or die(mysqli_error($conn));

		header("Location: match_commentation.php?id=" . $id);
		exit();
	}
}
//save goal record - end

//save card record - begin
if (isset($_POST['insert_card'])
	&& isset($_POST['match_player_id'])
	&& isset($_POST['card_type'])
	&& isset($_POST['minute'])
	&& intval($_POST['match_player_id']) > 0
	&& isset($_POST['csrf_i'])
	&& isset($_SESSION['csrf_i'])
	&& $_POST['csrf_i'] == $_SESSION['csrf_i']
) {
	$match_player_id = intval($_POST['match_player_id']);
	$card_type       = (in_array($_POST['card_type'], array('red', 'yellow')) ? $_POST['card_type'] : 'yellow');
	$card_minute     = prepare_for_db($_POST['minute']);
	$insert_time     = time();

	$sql = <<<EOF
INSERT INTO match_cards(
match_player_id,
card_type,
card_minute,
insert_time
)
VALUES(
'{$match_player_id}',
'{$card_type}',
'{$card_minute}',
'{$insert_time}'
)
EOF;

	mysqli_query($conn, $sql) or die(mysqli_error($conn));

	$sql = <<<EOF
UPDATE matches
SET
match_revision_nr = match_revision_nr + 1
WHERE
id = $id
EOF;

	mysqli_query($conn, $sql) or die(mysqli_error($conn));

	header("Location: match_commentation.php?id=" . $id);
	exit();
}
//save card record - end

//save substitution record - begin
if (isset($_POST['insert_substitution'])
	&& isset($_POST['match_player_id_in'])
	&& isset($_POST['match_player_id_out'])
	&& isset($_POST['minute'])
	&& intval($_POST['match_player_id_in']) > 0
	&& intval($_POST['match_player_id_out']) > 0
	&& isset($_POST['csrf_i'])
	&& isset($_SESSION['csrf_i'])
	&& $_POST['csrf_i'] == $_SESSION['csrf_i']
) {
	$match_player_id_in  = intval($_POST['match_player_id_in']);
	$match_player_id_out = intval($_POST['match_player_id_out']);
	$substitution_minute = prepare_for_db($_POST['minute']);
	$insert_time         = time();

	$sql = <<<EOF
INSERT INTO match_substitutions(
match_player_id_in,
match_player_id_out,
substitution_minute,
insert_time
)
VALUES(
'{$match_player_id_in}',
'{$match_player_id_out}',
'{$substitution_minute}',
'{$insert_time}'
)
EOF;

	if ($match_player_id_in != $match_player_id_out) {
		mysqli_query($conn, $sql) or die(mysqli_error($conn));

		$sql = <<<EOF
UPDATE matches
SET
match_revision_nr = match_revision_nr + 1
WHERE
id = $id
EOF;

		mysqli_query($conn, $sql) or die(mysqli_error($conn));

		header("Location: match_commentation.php?id=" . $id);
		exit();
	}
}
//save substitution record - end

//update match status - begin
if (isset($_POST['update_status'])
	&& isset($_POST['status'])
	&& trim($_POST['status']) != ''
	&& isset($_POST['csrf_e'])
	&& isset($_SESSION['csrf_e'])
	&& $_POST['csrf_e'] == $_SESSION['csrf_e']
) {
	$status = 'temp';

	if (in_array(
		$_POST['status'],
		array(
			'temp',
			'not_started',
			'first_half',
			'second_half',
			'half_time',
			'finished'
		)
	)
	) {
		$status = $_POST['status'];
	}

	$last_start_time = 0;

	if (in_array($status, array('temp', 'first_half', 'second_half', 'half_time'))
		&& $row_match_details['status'] != $status
	) {
		$last_start_time = time();
	}

	$status = prepare_for_db($_POST['status']);

	$sql = <<<EOF
UPDATE matches
SET
status = '{$status}',
last_start_time = CASE WHEN $last_start_time = 0 THEN last_start_time ELSE $last_start_time END,
match_revision_nr = match_revision_nr + 1
WHERE
id = $id
EOF;

	mysqli_query($conn, $sql) or die(mysqli_error($conn));

	header("Location: match_commentation.php?id=" . $id);
	exit();
}
//update match status - end

//set match time - begin
if (isset($_POST['set_time'])
	&& isset($_POST['minute'])
	&& trim($_POST['minute']) != ''
	&& isset($_POST['csrf_e'])
	&& isset($_SESSION['csrf_e'])
	&& $_POST['csrf_e'] == $_SESSION['csrf_e']
) {
	$new_time = time();

	$minute = intval($_POST['minute']);

	switch ($row_match_details['status']) {
		case 'first_half':
			$new_time -= ($minute * 60);
			break;
		case 'second_half':
			if ($minute < $script_match_first_half_length) {
				$minute = $script_match_first_half_length;
			}
			$new_time -= ($minute * 60) - $script_match_first_half_length * 60;
			break;
	}

	$sql = <<<EOF
UPDATE matches
SET
last_start_time = $new_time,
match_revision_nr = match_revision_nr + 1
WHERE
id = $id
EOF;

	mysqli_query($conn, $sql) or die(mysqli_error($conn));

	header("Location: match_commentation.php?id=" . $id);
	exit();
}
//set match time - end

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
t2.name,
t2.squad_number,
t2.team_id
FROM match_goals t1
INNER JOIN match_players t2 ON t1.match_player_id = t2.id
WHERE
t2.match_id = '{$id}'
AND t1.is_deleted = 0
ORDER BY t1.insert_time DESC
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

//rugby match scores - begin
if ($script_match_type == 'rugby'){
	$sql = <<<EOF
SELECT
t1.id,
t1.score_minute,
t2.name,
t2.squad_number,
t2.team_id,
t3.type,
t3.point
FROM rugby_match_scores t1
INNER JOIN match_players t2 ON t1.match_player_id = t2.id
INNER JOIN rugby_score_types t3 ON t1.rugby_score_type_id = t3.id
WHERE
t2.match_id = '{$id}'
AND t1.is_deleted = 0
ORDER BY t1.insert_time DESC
EOF;

	$Recordset_Scores = mysqli_query($conn, $sql) or die(mysqli_error($conn));

	$rugby_scores_team1 = array();
	$rugby_scores_team2 = array();

	while ($row_score = mysqli_fetch_assoc($Recordset_Scores)) {
		if ($row_score['team_id'] == $team1_id) {
			$rugby_scores_team1[] = $row_score;
		} else {
			$rugby_scores_team2[] = $row_score;
		}
	}
}
//rugby match scores - end

//match cards - begin
$sql = <<<EOF
SELECT
t1.id,
t1.card_minute,
t1.card_type,
t2.name,
t2.squad_number,
t2.team_id
FROM match_cards t1
INNER JOIN match_players t2 ON t1.match_player_id = t2.id
WHERE
t2.match_id = '{$id}'
AND t1.is_deleted = 0
ORDER BY t1.insert_time DESC
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
t2.name AS name_in,
t2.squad_number AS squad_number_in,
t2.team_id AS team_id_in,
t3.name AS name_out,
t3.squad_number AS squad_number_out,
t3.team_id AS team_id_out
FROM match_substitutions t1
INNER JOIN match_players t2 ON t1.match_player_id_in = t2.id
INNER JOIN match_players t3 ON t1.match_player_id_out = t3.id
WHERE
t2.match_id = '{$id}'
AND t1.is_deleted = 0
ORDER BY t1.insert_time DESC
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
		} else {
			$players_team1_substitute[] = $row_player;
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

//rugby team players - begin
if ($script_match_type == 'rugby'){
	//team 1 players
	$sql = <<<EOF
SELECT
t1.id,
t1.name,
t1.squad_number,
t1.status,
(
	SELECT
	SUM(t3.point)
	FROM rugby_match_scores t2
	INNER JOIN rugby_score_types t3 ON t2.rugby_score_type_id = t3.id
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
		} else {
			$players_team1_substitute[] = $row_player;
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
	SUM(t3.point)
	FROM rugby_match_scores t2
	INNER JOIN rugby_score_types t3 ON t2.rugby_score_type_id = t3.id
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
//rugby team players - end




$_SESSION['csrf_d'] = md5(uniqid());
$_SESSION['csrf_i'] = md5(uniqid());
$_SESSION['csrf_e'] = md5(uniqid());
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $label_array[28]; ?></title>
	<!-- Bootstrap -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<script language="javascript1.2" type="text/javascript">
		function insertRugbyScore(_form) {
			if (_form.match_player_id.value == '') {
				alert('<?php echo $label_array[29]; ?>');
				_form.match_player_id.focus();
				return false;
			}

			if (_form.minute.value == '') {
				alert('<?php echo $label_array[30]; ?>');
				_form.minute.focus();
				return false;
			}

			return true;
		}

		function insertGoal(_form) {
			if (_form.match_player_id.value == '') {
				alert('<?php echo $label_array[29]; ?>');
				_form.match_player_id.focus();
				return false;
			}

			if (_form.minute.value == '') {
				alert('<?php echo $label_array[30]; ?>');
				_form.minute.focus();
				return false;
			}

			return true;
		}

		function insertCard(_form) {
			if (_form.match_player_id.value == '') {
				alert('<?php echo $label_array[29]; ?>');
				_form.match_player_id.focus();
				return false;
			}

			if (_form.card_type.value == '') {
				alert('<?php echo $label_array[31]; ?>');
				_form.card_type.focus();
				return false;
			}

			if (_form.minute.value == '') {
				alert('<?php echo $label_array[32]; ?>');
				_form.minute.focus();
				return false;
			}

			return true;
		}

		function insertSubstitution(_form) {
			if (_form.match_player_id_in.value == '') {
				alert('<?php echo $label_array[29]; ?>');
				_form.match_player_id_in.focus();
				return false;
			}

			if (_form.match_player_id_out.value == '') {
				alert('<?php echo $label_array[29]; ?>');
				_form.match_player_id_out.focus();
				return false;
			}

			if (_form.minute.value == '') {
				alert('<?php echo $label_array[33]; ?>');
				_form.minute.focus();
				return false;
			}

			return true;
		}

		function adjustMatchTime(_form) {
			if (_form.minute.value == '') {
				alert('<?php echo $label_array[34]; ?>');
				_form.minute.focus();
				return false;
			}

			return true;
		}
	</script>
</head>

<body>
<?php include('menu_section.php'); ?>
<div class="container">
<div class="row" style="margin-top: 10px;">
<p><strong><?php echo $label_array[28]; ?></strong></p>
<table class="table table-bordered">
<tr>
	<td align="center"><strong><?php echo htmlspecialchars($row_match_details['home_team']); ?>
			<br><?php echo htmlspecialchars($row_match_details['home_goal_sum']); ?></strong></td>
	<td align="center">
		<iframe src="elapsed_time_section.php?id=<?php echo $id; ?>" style="width:100%" frameborder="0"
				height="26"></iframe>
	</td>
	<td align="center"><strong><?php echo htmlspecialchars($row_match_details['away_team']); ?>
			<br><?php echo htmlspecialchars($row_match_details['away_goal_sum']); ?></strong></td>
</tr>
<tr>
<td style="background-color: black;">
<?php
// SOCCER MATCH GOAL INSERT FORM - BEGIN
if ($script_match_type == 'soccer') {
?>
<form action="" method="post" enctype="multipart/form-data" role="form" onsubmit="return insertGoal(this);">
	<table class="table" style="background-color: #E2F3E9;">
		<tr>
			<td><?php echo $label_array[35]; ?></td>
		</tr>
		<tr>
		<tr>
			<td>
				<div class="form-group">
					<label><?php echo $label_array[36]; ?></label>
					<select name="match_player_id" class="form-control">
						<option value="" disabled="disabled" selected="selected"><?php echo $label_array[37];
							?></option>
						<?php foreach ($players_team1 as $player) {
							?>
							<option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['name']);
								?></option>
						<?php } ?>
					</select>
				</div>
				<div class="checkbox">
					<label>
						<input name="is_penalty_goal" type="checkbox"><?php echo $label_array[38]; ?>
					</label>
				</div>
				<div class="checkbox">
					<label>
						<input name="is_own_goal" type="checkbox"><?php echo $label_array[39]; ?>
					</label>
				</div>
				<div class="form-group">
					<label><?php echo $label_array[40]; ?></label>
					<input type="text" name="minute" class="form-control" style="width:160px;"/>
				</div>
				<input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/>
				<input type="hidden" name="insert_goal" value="1">
				<input type="submit" name="Submit" value="<?php echo $label_array[41]; ?>"/>
			</td>
		</tr>
	</table>
</form>
<table class="table table-bordered">
	<tr class="info">
		<td colspan="3"><?php echo $label_array[35]; ?></td>
	</tr>
	<tr>
		<th><?php echo $label_array[36]; ?></th>
		<th><?php echo $label_array[40]; ?></th>
		<th><?php echo $label_array[42]; ?></th>
	</tr>
	<?php foreach ($goals_team1 as $goal) {
		?>
		<tr>
			<td align="left"><?php echo htmlspecialchars($goal['name']); ?></td>
			<td align="left">
				<?php
				echo htmlspecialchars(
					$goal['goal_minute'] . ($goal['is_penalty_goal'] == true ? ' (' . $label_array[43] . ') ' :
						($goal['is_own_goal'] ==
						true ? ' (' . $label_array[80] . ')' : ''))
				);
				?>
			</td>
			<td align="left">
				<form id="d_mg_<?php echo $goal['id']; ?>" action="<?php echo
					'delete_match_goal.php?id=' . $goal['id'] . '&amp;d=' . $id; ?>"
					  method="post" role="form">
					<input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
				</form>
				<a href="#"
				   onclick="if (confirm('<?php echo $label_array[44]; ?>')) {
					   document.getElementById('<?php echo 'd_mg_' . $goal['id']; ?>').submit(); } return false;
					   "><?php echo $label_array[42]; ?></a>
			</td>
		</tr>
	<?php } ?>
</table>
<?php
}
// SOCCER MATCH GOAL INSERT FORM - END

// RUGBY MATCH SCORE INSERT FORM - BEGIN
if ($script_match_type == 'rugby') {
	?>
	<form action="" method="post" enctype="multipart/form-data" role="form" onsubmit="return insertRugbyScore(this);">
		<table class="table" style="background-color: #E2F3E9;">
			<tr>
				<td><?php echo $label_array[126]; ?></td>
			</tr>
			<tr>
			<tr>
				<td>
					<div class="form-group">
						<label><?php echo $label_array[36]; ?></label>
						<select name="match_player_id" class="form-control">
							<option value="" disabled="disabled" selected="selected"><?php echo $label_array[37];
								?></option>
							<?php foreach ($players_team1 as $player) {
								?>
								<option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars
									($player['name']);
									?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label><?php echo $label_array[128]; ?></label>
						<select name="rugby_score_type_id" class="form-control">
							<option value="" disabled="disabled" selected="selected"><?php echo $label_array[129];
								?></option>
							<?php foreach ($rugby_score_types as $key => $score_type) {
								?>
								<option value="<?php echo $key; ?>"><?php echo htmlspecialchars(
									(isset($label_array[130][$score_type['type']]) ?
										$label_array[130][$score_type['type']] : $score_type['type']));
									?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label><?php echo $label_array[40]; ?></label>
						<input type="text" name="minute" class="form-control" style="width:160px;"/>
					</div>

					<input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/>
					<input type="hidden" name="insert_score" value="1">
					<input type="submit" name="Submit" value="<?php echo $label_array[127]; ?>"/>
				</td>
			</tr>
		</table>
	</form>
	<table class="table table-bordered">
		<tr class="info">
			<td colspan="3"><?php echo $label_array[126]; ?></td>
		</tr>
		<tr>
			<th><?php echo $label_array[36]; ?></th>
			<th><?php echo $label_array[40]; ?></th>
			<th><?php echo $label_array[42]; ?></th>
		</tr>
		<?php foreach ($rugby_scores_team1 as $score) {
			?>
			<tr>
				<td align="left"><?php echo htmlspecialchars($score['name']); ?></td>
				<td align="left">
					<?php
					echo htmlspecialchars(
						$score['score_minute'] . ' (' . (isset($label_array[130][$score['type']]) ?
								$label_array[130][$score['type']] : $score['type']) . ')'
					);
					?>
				</td>
				<td align="left">
					<form id="d_mg_<?php echo $score['id']; ?>" action="<?php echo
						'delete_rugby_match_score.php?id=' . $score['id'] . '&amp;d=' . $id; ?>"
						  method="post" role="form">
						<input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
					</form>
					<a href="#"
					   onclick="if (confirm('<?php echo $label_array[44]; ?>')) {
						   document.getElementById('<?php echo 'd_mg_' . $score['id']; ?>').submit(); } return false;
						   "><?php echo $label_array[42]; ?></a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php
}
// RUGBY MATCH SCORE INSERT FORM - END
?>
<form action="" method="post" enctype="multipart/form-data" role="form" onsubmit="return insertCard(this);">
	<table class="table" style="background-color: #FCF4BC;">
		<tr>
			<td><?php echo $label_array[45]; ?></td>
		</tr>
		<tr>
		<tr>
			<td>
				<div class="form-group">
					<label><?php echo $label_array[36]; ?></label>
					<select name="match_player_id" class="form-control">
						<option value="" disabled="disabled" selected="selected"><?php echo $label_array[37];
							?></option>
						<?php foreach ($players_team1 as $player) {
							?>
							<option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['name']);
								?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label><?php echo $label_array[46]; ?></label>
					<select name="card_type" class="form-control">
						<option value="" disabled="disabled" selected="selected"><?php echo $label_array[47];
							?></option>
						<option value="yellow"><?php echo $label_array[48]; ?></option>
						<option value="red"><?php echo $label_array[49]; ?></option>
					</select>
				</div>
				<div class="form-group">
					<label><?php echo $label_array[40]; ?></label>
					<input type="text" name="minute" class="form-control" style="width:160px;"/>
				</div>
				<input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/>
				<input type="hidden" name="insert_card" value="1">
				<input type="submit" name="Submit" value="<?php echo $label_array[50]; ?>"/>
			</td>
		</tr>
	</table>
</form>
<table class="table table-bordered">
	<tr class="info">
		<td colspan="4"><?php echo $label_array[45]; ?></td>
	</tr>
	<tr>
		<th><?php echo $label_array[36]; ?></th>
		<th><?php echo $label_array[51]; ?></th>
		<th><?php echo $label_array[40]; ?></th>
		<th><?php echo $label_array[42]; ?></th>
	</tr>
	<?php
	foreach ($cards_team1 as $card) {
		$card_type = $label_array[48];

		switch ($card['card_type']) {
			case 'yellow':
				$card_type = $label_array[48];
				break;
			case 'red':
				$card_type = $label_array[49];
				break;
		}
		?>
		<tr>
			<td align="left"><?php echo htmlspecialchars($card['name']); ?></td>
			<td align="left"><?php echo htmlspecialchars($card_type); ?></td>
			<td align="left"><?php echo htmlspecialchars($card['card_minute']); ?></td>
			<td align="left">
				<form id="d_mc_<?php echo $card['id']; ?>" action="<?php echo
					'delete_match_card.php?id=' . $card['id'] . '&amp;d=' . $id; ?>"
					  method="post" role="form">
					<input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
				</form>
				<a href="#"
				   onclick="if (confirm('<?php echo $label_array[44]; ?>')) { document.getElementById('<?php echo
					   'd_mc_' . $card['id']; ?>').submit(); } return false;"><?php echo $label_array[42]; ?></a>
			</td>
		</tr>
	<?php } ?>
</table>
<form action="" method="post" enctype="multipart/form-data" role="form" onsubmit="return insertSubstitution(this);">
	<table class="table" style="background-color: #E4ECF1;">
		<tr>
			<td><?php echo $label_array[52]; ?></td>
		</tr>
		<tr>
		<tr>
			<td>
				<div class="form-group">
					<label><?php echo $label_array[53]; ?></label>
					<select name="match_player_id_in" class="form-control">
						<option value="" disabled="disabled" selected="selected"><?php echo $label_array[37];
							?></option>
						<?php foreach ($players_team1 as $player) {
							?>
							<option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['name']);
								?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label><?php echo $label_array[54]; ?></label>
					<select name="match_player_id_out" class="form-control">
						<option value="" disabled="disabled" selected="selected"><?php echo $label_array[37];
							?></option>
						<?php foreach ($players_team1 as $player) {
							?>
							<option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['name']);
								?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label><?php echo $label_array[40]; ?></label>
					<input type="text" name="minute" class="form-control" style="width:160px;"/>
				</div>
				<input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/>
				<input type="hidden" name="insert_substitution" value="1">
				<input type="submit" name="Submit" value="<?php echo $label_array[55]; ?>"/>
			</td>
		</tr>
	</table>
</form>
<table class="table table-bordered">
	<tr class="info">
		<td colspan="4"><?php echo $label_array[52]; ?></td>
	</tr>
	<tr>
		<th><?php echo $label_array[56]; ?></th>
		<th><?php echo $label_array[57]; ?></th>
		<th><?php echo $label_array[40]; ?></th>
		<th><?php echo $label_array[42]; ?></th>
	</tr>
	<?php foreach ($substitutions_team1 as $substitution) {
		?>
		<tr>
			<td align="left"><?php echo htmlspecialchars($substitution['name_in']); ?></td>
			<td align="left"><?php echo htmlspecialchars($substitution['name_out']); ?></td>
			<td align="left"><?php echo htmlspecialchars($substitution['substitution_minute']); ?></td>
			<td align="left">
				<form id="d_ms_<?php echo $substitution['id']; ?>" action="<?php echo
					'delete_match_substitution.php?id=' . $substitution['id'] . '&amp;d=' . $id; ?>"
					  method="post" role="form">
					<input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
				</form>
				<a href="#"
				   onclick="if (confirm('<?php echo $label_array[44]; ?>')) { document.getElementById('<?php echo
					   'd_ms_' .
					   $substitution['id'];
				   ?>').submit(); } return false;"><?php echo $label_array[42]; ?></a>
			</td>
		</tr>
	<?php } ?>
</table>
<table class="table table-bordered">
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
				<?php
				echo ($player['goal_sum'] > 0 ? '<b>' . $player['goal_sum'] . '</b> ' : ' ') .
					htmlspecialchars($player['name']);
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
				<?php
				echo ($player['goal_sum'] > 0 ? '<b>' . $player['goal_sum'] . '</b> ' : ' ') .
					htmlspecialchars($player['name']);
				?>
			</td>
		</tr>
	<?php } ?>
</table>
</td>
<td style="background-color: #F2F2F2;">
	<form action="" method="post" enctype="multipart/form-data" role="form">
		<p><strong><?php echo $label_array[60]; ?></strong></p>

		<div class="form-group">
			<select name="status" class="form-control">
				<option value="temp" <?php if ($row_match_details['status'] == 'temp') {
					?> selected="selected"<?php } ?>><?php echo $label_array[61]; ?>
				</option>
				<option value="not_started" <?php if ($row_match_details['status'] == 'not_started') {
					?> selected="selected"<?php } ?>><?php echo $label_array[62]; ?>
				</option>
				<option value="first_half" <?php if ($row_match_details['status'] == 'first_half') {
					?> selected="selected"<?php } ?>><?php echo $label_array[63]; ?>
				</option>
				<option value="half_time" <?php if ($row_match_details['status'] == 'half_time') {
					?> selected="selected"<?php } ?>><?php echo $label_array[64]; ?>
				</option>
				<option value="second_half" <?php if ($row_match_details['status'] == 'second_half') {
					?> selected="selected"<?php } ?>><?php echo $label_array[65]; ?>
				</option>
				<option value="finished" <?php if ($row_match_details['status'] == 'finished') {
					?> selected="selected"<?php } ?>><?php echo $label_array[66]; ?>
				</option>
			</select>
		</div>
		<input type="hidden" name="csrf_e" value="<?php echo $_SESSION['csrf_e']; ?>"/>
		<input type="hidden" name="update_status" value="1">
		<input type="submit" name="Submit" value="<?php echo $label_array[67]; ?>"/>
	</form>
	<?php if (in_array($row_match_details['status'], array('first_half', 'second_half'))) {
		?>
		<br>
		<form action="" method="post" enctype="multipart/form-data" role="form"
			  onsubmit="return adjustMatchTime(this);">
			<p><strong><?php echo $label_array[68]; ?></strong></p>
			<table class="table table-bordered" style="background-color: #F9FBFF;">
				<tr>
					<td><?php echo $label_array[40]; ?>:</td>
					<td><input type="text" name="minute" style="width:160px;"/></td>
				</tr>
				<tr>
				<tr>
					<td></td>
					<td>
						<input type="hidden" name="csrf_e" value="<?php echo $_SESSION['csrf_e']; ?>"/>
						<input type="hidden" name="set_time" value="1">
						<input type="submit" name="Submit" value="<?php echo $label_array[69]; ?>"/>
					</td>
				</tr>
			</table>
		</form>
	<?php } ?>
	<table class="table table-bordered table-striped" style="margin-top: 15px;">
		<tr>
			<td align="left" colspan="2" style="background-color: #FFF;">
				<form action="" method="post" enctype="multipart/form-data" role="form">
					<table class="table">
						<tr>
							<td style="border-top: none;"><?php echo $label_array[77]; ?></td>
							<td style="border-top: none;"><input type="text" name="minute" style="width:160px;"/>
							
								<div class="radio">
									<label>
										<input type="radio"
											   name="auto_minute"
											   value="on"
											<?php if ($auto_minute == 'on') {
												echo ' checked="checked"';
											}
											?>>
										<?php echo $label_array[133]; ?>
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio"
											   name="auto_minute"
											   value="off"
											<?php if ($auto_minute == 'off') {
												echo ' checked="checked"';
											}
											?>>
										<?php echo $label_array[134]; ?>
									</label>
								</div>

							</td>
						</tr>
						<tr>
							<td>
								<?php echo $label_array[70]; ?>
							</td>
							<td>
								<div class="form-group">
									<select name="comment_type" class="form-control">
										<option value="standard" selected="selected"><?php echo $label_array[71];
											?></option>
										<option value="goal"><?php echo $label_array[72]; ?></option>
										<option value="yellow"><?php echo $label_array[73]; ?></option>
										<option value="red"><?php echo $label_array[74]; ?></option>
										<option value="substitution"><?php echo $label_array[75]; ?></option>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo $label_array[76]; ?>
							</td>
							<td>
								<textarea name="comment" rows="3" cols="1" style="width: 400px;"></textarea>
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/>
								<input type="hidden" name="insert_comment" value="1">
								<input type="submit" name="Submit" value="<?php echo $label_array[78]; ?>"/>
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
		<tr class="info">
			<th><?php echo $label_array[76]; ?></th>
			<th><?php echo $label_array[79]; ?></th>
		</tr>
		<?php
		foreach ($match_comments as $comment) {
			$color              = '#000';
			$bg_color           = '#FFF';
			$is_different_color = false;

			switch ($comment['comment_type']) {
				case 'standard':
					break;
				case 'goal':
					$is_different_color = true;
					$bg_color           = '#C6EB8D';
					break;
				case 'yellow':
					$is_different_color = true;
					$bg_color           = '#FCF967';
					break;
				case 'red':
					$is_different_color = true;
					$bg_color           = '#FB0000';
					$color              = '#FFF';
					break;
				case 'substitution':
					break;
			}
			?>
			<tr>
				<td align="left" <?php if ($is_different_color) {
					?> style="color:<?php echo $color; ?>; background-color:<?php echo $bg_color; ?>;"<?php } ?>>
					<?php
					echo htmlspecialchars(
						($comment['comment_minute'] == "" ? "" : $comment['comment_minute'] . ' - ') .
						nl2br($comment['comment'])
					);
					?>
				</td>
				<td align="left" <?php if ($is_different_color) {
					?> style="color:<?php echo $color; ?>; background-color:<?php echo $bg_color; ?>;"<?php } ?>>
					<form id="d_c_<?php echo $comment['id']; ?>" action="<?php echo
						'delete_match_comment.php?id=' . $comment['id'] . '&amp;d=' . $id; ?>"
						  method="post" role="form">
						<input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
					</form>
					<a href="#"
					   onclick="if (confirm('<?php echo $label_array[44]; ?>')) { document.getElementById('<?php echo
						   'd_c_' .
						   $comment['id'];
					   ?>').submit(); } return false;"><?php echo $label_array[42]; ?></a>
				</td>
			</tr>
		<?php } ?>
	</table>
</td>
<td style="background-color: black;">
<?php
// SOCCER MATCH GOAL INSERT FORM - BEGIN
if ($script_match_type == 'soccer') {
?>
<form action="" method="post" enctype="multipart/form-data" role="form" onsubmit="return insertGoal(this);">
	<table class="table" style="background-color: #E2F3E9;">
		<tr>
			<td><?php echo $label_array[35]; ?></td>
		</tr>
		<tr>
		<tr>
			<td>
				<div class="form-group">
					<label><?php echo $label_array[36]; ?></label>
					<select name="match_player_id" class="form-control">
						<option value="" disabled="disabled" selected="selected"><?php echo $label_array[37];
							?></option>
						<?php foreach ($players_team2 as $player) {
							?>
							<option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['name']);
								?></option>
						<?php } ?>
					</select>
				</div>
				<div class="checkbox">
					<label>
						<input name="is_penalty_goal" type="checkbox"><?php echo $label_array[38]; ?>
					</label>
				</div>
				<div class="checkbox">
					<label>
						<input name="is_own_goal" type="checkbox"><?php echo $label_array[39]; ?>
					</label>
				</div>
				<div class="form-group">
					<label><?php echo $label_array[40]; ?></label>
					<input type="text" name="minute" class="form-control" style="width:160px;"/>
				</div>
				<input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/>
				<input type="hidden" name="insert_goal" value="1">
				<input type="submit" name="Submit" value="<?php echo $label_array[41]; ?>"/>
			</td>
		</tr>
	</table>
</form>
<table class="table table-bordered">
	<tr class="info">
		<td colspan="3"><?php echo $label_array[35]; ?></td>
	</tr>
	<tr>
		<th><?php echo $label_array[36]; ?></th>
		<th><?php echo $label_array[40]; ?></th>
		<th><?php echo $label_array[42]; ?></th>
	</tr>
	<?php foreach ($goals_team2 as $goal) {
		?>
		<tr>
			<td align="left"><?php echo $goal['name']; ?></td>
			<td align="left">
				<?php
				echo htmlspecialchars(
					$goal['goal_minute'] . ($goal['is_penalty_goal'] == true ? ' (' . $label_array[43] . ') ' :
						($goal['is_own_goal'] == true ? ' (' . $label_array[80] . ')' : ''))
				);
				?>
			</td>
			<td align="left">
				<form id="d_mg_<?php echo $goal['id']; ?>" action="<?php echo
					'delete_match_goal.php?id=' . $goal['id'] . '&amp;d=' . $id; ?>"
					  method="post" role="form">
					<input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
				</form>
				<a href="#"
				   onclick="if (confirm('<?php echo $label_array[44]; ?>')) { document.getElementById('<?php echo
					   'd_mg_' . $goal['id'];
				   ?>')
					   .submit(); } return false;"><?php echo $label_array[42]; ?></a>
			</td>
		</tr>
	<?php } ?>
</table>
<?php
}
// SOCCER MATCH GOAL INSERT FORM - END

// RUGBY MATCH SCORE INSERT FORM - BEGIN
if ($script_match_type == 'rugby') {
?>
	<form action="" method="post" enctype="multipart/form-data" role="form" onsubmit="return insertRugbyScore(this);">
		<table class="table" style="background-color: #E2F3E9;">
			<tr>
				<td><?php echo $label_array[126]; ?></td>
			</tr>
			<tr>
			<tr>
				<td>
					<div class="form-group">
						<label><?php echo $label_array[36]; ?></label>
						<select name="match_player_id" class="form-control">
							<option value="" disabled="disabled" selected="selected"><?php echo $label_array[37];
								?></option>
							<?php foreach ($players_team2 as $player) {
								?>
								<option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars
									($player['name']);
									?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label><?php echo $label_array[128]; ?></label>
						<select name="rugby_score_type_id" class="form-control">
							<option value="" disabled="disabled" selected="selected"><?php echo $label_array[129];
								?></option>
							<?php foreach ($rugby_score_types as $key => $score_type) {
								?>
								<option value="<?php echo $key; ?>"><?php echo htmlspecialchars(
										(isset($label_array[130][$score_type['type']]) ?
											$label_array[130][$score_type['type']] : $score_type['type']));
									?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label><?php echo $label_array[40]; ?></label>
						<input type="text" name="minute" class="form-control" style="width:160px;"/>
					</div>

					<input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/>
					<input type="hidden" name="insert_score" value="1">
					<input type="submit" name="Submit" value="<?php echo $label_array[127]; ?>"/>
				</td>
			</tr>
		</table>
	</form>
	<table class="table table-bordered">
		<tr class="info">
			<td colspan="3"><?php echo $label_array[126]; ?></td>
		</tr>
		<tr>
			<th><?php echo $label_array[36]; ?></th>
			<th><?php echo $label_array[40]; ?></th>
			<th><?php echo $label_array[42]; ?></th>
		</tr>
		<?php foreach ($rugby_scores_team2 as $score) {
			?>
			<tr>
				<td align="left"><?php echo htmlspecialchars($score['name']); ?></td>
				<td align="left">
					<?php
					echo htmlspecialchars(
						$score['score_minute'] . ' (' . (isset($label_array[130][$score['type']]) ?
							$label_array[130][$score['type']] : $score['type']) . ')'
					);
					?>
				</td>
				<td align="left">
					<form id="d_mg_<?php echo $score['id']; ?>" action="<?php echo
						'delete_rugby_match_score.php?id=' . $score['id'] . '&amp;d=' . $id; ?>"
						  method="post" role="form">
						<input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
					</form>
					<a href="#"
					   onclick="if (confirm('<?php echo $label_array[44]; ?>')) {
						   document.getElementById('<?php echo 'd_mg_' . $score['id']; ?>').submit(); } return false;
						   "><?php echo $label_array[42]; ?></a>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php
}
// RUGBY MATCH SCORE INSERT FORM - END
?>

<form action="" method="post" enctype="multipart/form-data" role="form" onsubmit="return insertCard(this);">
	<table class="table" style="background-color: #FCF4BC;">
		<tr>
			<td><?php echo $label_array[45]; ?></td>
		</tr>
		<tr>
		<tr>
			<td>
				<div class="form-group">
					<label><?php echo $label_array[36]; ?></label>
					<select name="match_player_id" class="form-control">
						<option value="" disabled="disabled" selected="selected"><?php echo $label_array[37];
							?></option>
						<?php foreach ($players_team2 as $player) {
							?>
							<option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['name']);
								?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label><?php echo $label_array[46]; ?></label>
					<select name="card_type" class="form-control">
						<option value="" disabled="disabled" selected="selected"><?php echo $label_array[47];
							?></option>
						<option value="yellow"><?php echo $label_array[48]; ?></option>
						<option value="red"><?php echo $label_array[49]; ?></option>
					</select>
				</div>
				<div class="form-group">
					<label><?php echo $label_array[40]; ?></label>
					<input type="text" name="minute" class="form-control" style="width:160px;"/>
				</div>
				<input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/>
				<input type="hidden" name="insert_card" value="1">
				<input type="submit" name="Submit" value="<?php echo $label_array[50]; ?>"/>
			</td>
		</tr>
	</table>
</form>
<table class="table table-bordered">
	<tr class="info">
		<td colspan="4"><?php echo $label_array[45]; ?></td>
	</tr>
	<tr>
		<th><?php echo $label_array[36]; ?></th>
		<th><?php echo $label_array[51]; ?></th>
		<th><?php echo $label_array[40]; ?></th>
		<th><?php echo $label_array[42]; ?></th>
	</tr>
	<?php
	foreach ($cards_team2 as $card) {
		$card_type = $label_array[48];

		switch ($card['card_type']) {
			case 'yellow':
				$card_type = $label_array[48];
				break;
			case 'red':
				$card_type = $label_array[49];
				break;
		}
		?>
		<tr>
			<td align="left"><?php echo htmlspecialchars($card['name']); ?></td>
			<td align="left"><?php echo htmlspecialchars($card_type); ?></td>
			<td align="left"><?php echo htmlspecialchars($card['card_minute']); ?></td>
			<td align="left">
				<form id="d_mc_<?php echo $card['id']; ?>" action="<?php echo
					'delete_match_card.php?id=' . $card['id'] . '&amp;d=' . $id; ?>"
					  method="post" role="form">
					<input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
				</form>
				<a href="#"
				   onclick="if (confirm('<?php echo $label_array[44]; ?>')) { document.getElementById('<?php echo
					   'd_mc_' . $card['id'];
				   ?>').submit(); } return false;"><?php echo $label_array[42]; ?></a>
			</td>
		</tr>
	<?php } ?>
</table>
<form action="" method="post" enctype="multipart/form-data" role="form" onsubmit="return insertSubstitution(this);">
	<table class="table" style="background-color: #E4ECF1;">
		<tr>
			<td><?php echo $label_array[52]; ?></td>
		</tr>
		<tr>
		<tr>
			<td>
				<div class="form-group">
					<label><?php echo $label_array[53]; ?></label>
					<select name="match_player_id_in" class="form-control">
						<option value="" disabled="disabled" selected="selected"><?php echo $label_array[37];
							?></option>
						<?php foreach ($players_team2 as $player) {
							?>
							<option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['name']);
								?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label><?php echo $label_array[54]; ?></label>
					<select name="match_player_id_out" class="form-control">
						<option value="" disabled="disabled" selected="selected"><?php echo $label_array[37];
							?></option>
						<?php foreach ($players_team2 as $player) {
							?>
							<option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['name']);
								?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label><?php echo $label_array[40]; ?></label>
					<input type="text" name="minute" class="form-control" style="width:160px;"/>
				</div>
				<input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/>
				<input type="hidden" name="insert_substitution" value="1">
				<input type="submit" name="Submit" value="<?php echo $label_array[55]; ?>"/>
			</td>
		</tr>
	</table>
</form>
<table class="table table-bordered">
	<tr class="info">
		<td colspan="4"><?php echo $label_array[52]; ?></td>
	</tr>
	<tr>
		<th><?php echo $label_array[56]; ?></th>
		<th><?php echo $label_array[57]; ?></th>
		<th><?php echo $label_array[40]; ?></th>
		<th><?php echo $label_array[42]; ?></th>
	</tr>
	<?php foreach ($substitutions_team2 as $substitution) {
		?>
		<tr>
			<td align="left"><?php echo htmlspecialchars($substitution['name_in']); ?></td>
			<td align="left"><?php echo htmlspecialchars($substitution['name_out']); ?></td>
			<td align="left"><?php echo htmlspecialchars($substitution['substitution_minute']); ?></td>
			<td align="left">
				<form id="d_ms_<?php echo $substitution['id']; ?>" action="<?php echo
					'delete_match_substitution.php?id=' . $substitution['id'] . '&amp;d=' . $id; ?>"
					  method="post" role="form">
					<input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
				</form>
				<a href="#"
				   onclick="if (confirm('<?php echo $label_array[44]; ?>')) { document.getElementById('<?php echo
					   'd_ms_' .
					   $substitution['id'];
				   ?>').submit(); } return false;"><?php echo $label_array[42]; ?></a>
			</td>
		</tr>
	<?php } ?>
</table>
<table class="table table-bordered">
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
				<?php
				echo ($player['goal_sum'] > 0 ? '<b>' . $player['goal_sum'] . '</b> ' : ' ') .
					htmlspecialchars($player['name']);
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
				<?php
				echo ($player['goal_sum'] > 0 ? '<b>' . $player['goal_sum'] . '</b> ' : ' ') .
					htmlspecialchars($player['name']);
				?>
			</td>
		</tr>
	<?php } ?>
</table>
</td>
</tr>

</table>
</div>
</div>
</body>
</html>