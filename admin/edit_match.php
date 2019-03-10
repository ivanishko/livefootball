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
t1.title,
t1.match_date,
t1.commentator_id,
t1.description,
t1.referee_head,
t1.referee_assistant,
t1.referee_assistant2,
t1.referee_fourth,
t1.stadium
FROM matches t1
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

//commentator array - begin
$commentators = array();
$query_Recordset_Commentators =<<<EOF
SELECT
id,
name
FROM commentators
WHERE
is_deleted = 0
OR id = '{$row_match_details['commentator_id']}'
ORDER BY name ASC;
EOF;

$Recordset_Commentators = mysqli_query($conn, $query_Recordset_Commentators) or die(mysqli_error($conn));

while ($row_Recordset_Commentators = mysqli_fetch_assoc($Recordset_Commentators)) {
	$commentators[$row_Recordset_Commentators['id']] = $row_Recordset_Commentators;
}
//commentator array - end

if (isset($_POST['title'])
	&& trim($_POST['title']) != ''
	&& isset($_POST['match_date'])
	&& trim($_POST['match_date']) != ''
	&& isset($_POST['description'])
	&& isset($_POST['commentator_id'])
	&& isset($_POST['csrf_e'])
	&& isset($_SESSION['csrf_e'])
	&& $_POST['csrf_e'] == $_SESSION['csrf_e']
) {
	$title       = prepare_for_db($_POST['title']);
	$description = prepare_for_db($_POST['description']);
	$match_date  = prepare_for_db($_POST['match_date']);
	$commentator_id = (array_key_exists(intval($_POST['commentator_id']),
		$commentators) ? intval($_POST['commentator_id']) :
		0);

	$stadium            = prepare_for_db($_POST['stadium']);
	$referee_head       = prepare_for_db($_POST['referee_head']);
	$referee_assistant  = prepare_for_db($_POST['referee_assistant']);
	$referee_assistant2 = prepare_for_db($_POST['referee_assistant2']);
	$referee_fourth     = prepare_for_db($_POST['referee_fourth']);

	$sql = <<<EOF
UPDATE matches
SET
title = '{$title}',
description = '{$description}',
match_date = '{$match_date}',
stadium = '{$stadium}',
commentator_id = '{$commentator_id}',
referee_head = '{$referee_head}',
referee_assistant = '{$referee_assistant}',
referee_assistant2 = '{$referee_assistant2}',
referee_fourth = '{$referee_fourth}'
WHERE
id = '{$id}'
EOF;

	//save match
	mysqli_query($conn, $sql) or die(mysqli_error($conn));

	header("Location: matches.php");
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
	<title><?php echo $label_array[5]; ?></title>
	<!-- Bootstrap -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php
include('menu_section.php');
?>
<div class="container">
	<div class="row" style="margin-top: 10px;">
		<p><strong><?php echo $label_array[5]; ?></strong></p>

		<form action="" method="post" enctype="multipart/form-data" role="form">
			<table class="table table-bordered" style="width: 500px;">
				<tr>
					<td><?php echo $label_array[6]; ?>:</td>
					<td><input type="text" name="title" value="<?php echo htmlspecialchars($row_match_details['title']);
						?>" style="width:250px;"/></td>
				</tr>
				<tr>
					<td><?php echo $label_array[7]; ?>:</td>
					<td><input type="text" name="match_date"
							   value="<?php echo htmlspecialchars($row_match_details['match_date']); ?>"
							   style="width:250px;"/></td>
				</tr>
				<tr>
					<td><?php echo $label_array[8]; ?>:</td>
					<td><textarea name="description" rows="3" cols="1"
								  style="width: 250px;"><?php echo htmlspecialchars(
								$row_match_details['description']
							); ?></textarea>
					</td>
				</tr>
				<tr>
					<td><?php echo $label_array[9]; ?>:</td>
					<td><input type="text" name="stadium"
							   value="<?php echo htmlspecialchars($row_match_details['stadium']); ?>" style="width:250px;"/>
					</td>
				</tr>
				<tr>
					<td><?php echo $label_array[123]; ?>:</td>
					<td>
						<select name="commentator_id">
							<option value="0" <?php if ($row_match_details['commentator_id'] == 0) { echo
							'selected="selected"'; } ?>><?php echo $label_array[122];
							?></option>

							<?php
							foreach ($commentators as $commentator) { ?>
							<option value="<?php echo $commentator['id']; ?>" <?php if
							($row_match_details['commentator_id'] == $commentator['id']) { echo 'selected="selected"';
							 }
							?>><?php echo $commentator['name'];
								?></option>
							<?php
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
					<td><input type="text" name="referee_head"
							   value="<?php echo htmlspecialchars($row_match_details['referee_head']); ?>"
							   style="width:250px;"/></td>
				</tr>
				<tr>
					<td><?php echo $label_array[12]; ?>:</td>
					<td><input type="text" name="referee_assistant"
							   value="<?php echo htmlspecialchars($row_match_details['referee_assistant']); ?>"
							   style="width:250px;"/></td>
				</tr>
				<tr>
					<td><?php echo $label_array[13]; ?>:</td>
					<td><input type="text" name="referee_assistant2"
							   value="<?php echo htmlspecialchars($row_match_details['referee_assistant2']); ?>"
							   style="width:250px;"/></td>
				</tr>
				<tr>
					<td><?php echo $label_array[14]; ?>:</td>
					<td><input type="text" name="referee_fourth"
							   value="<?php echo htmlspecialchars($row_match_details['referee_fourth']); ?>"
							   style="width:250px;"/></td>
				</tr>
				<tr>
					<td><input type="hidden" name="csrf_e" value="<?php echo $_SESSION['csrf_e']; ?>"/></td>
					<td>
						<label><input type="submit" name="Submit" value="<?php echo $label_array[2]; ?>"/></label>
					</td>
				</tr>
			</table>
		</form>
		<br/>
	</div>
</div>
</body>
</html>