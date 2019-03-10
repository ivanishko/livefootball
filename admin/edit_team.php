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
t1.manager
FROM teams t1
WHERE
t1.id='{$id}'
AND t1.is_deleted = 0
EOF;

    $Recordset_TeamDetails = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $row_team_details = mysqli_fetch_assoc($Recordset_TeamDetails);

    if (mysqli_num_rows($Recordset_TeamDetails) == 0) {
        header("Location: teams.php");
        exit();
    }
} else {
    header("Location: teams.php");
    exit();
}

if (isset($_POST['name'])
    && trim($_POST['name']) != ''
    && isset($_POST['manager'])
    && trim($_POST['manager']) != ''
    && isset($_POST['csrf_e'])
    && isset($_SESSION['csrf_e'])
    && $_POST['csrf_e'] == $_SESSION['csrf_e']
) {
    $name    = prepare_for_db($_POST['name']);
    $manager = prepare_for_db($_POST['manager']);

    $sql = <<<EOF
UPDATE teams
SET
name = '{$name}',
manager = '{$manager}'
WHERE
id = '{$id}'
EOF;

    //save team
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    header("Location: teams.php");
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
    <title><?php echo $label_array[15]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include('menu_section.php'); ?>
<div class="container">
    <div class="row" style="margin-top: 10px;">
        <p><strong><?php echo $label_array[15]; ?></strong></p>

        <form action="" method="post" enctype="multipart/form-data" role="form">
            <table class="table table-bordered" style="width: 400px;">
                <tr>
                    <td>
                        <?php echo $label_array[16]; ?>
                    </td>
                    <td>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($row_team_details['name']); ?>"
                               style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[17]; ?>
                    </td>
                    <td>
                        <input type="text" name="manager"
                               value="<?php echo htmlspecialchars($row_team_details['manager']); ?>" style="width:160px;"/>
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