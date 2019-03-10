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
t1.username,
t1.password
FROM commentators t1
WHERE
t1.id='{$id}'
AND t1.is_deleted = 0
EOF;

    $Recordset_CommentatorDetails = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $row_commentator_details = mysqli_fetch_assoc($Recordset_CommentatorDetails);

    if (mysqli_num_rows($Recordset_CommentatorDetails) == 0) {
        header("Location: commentators.php");
        exit();
    }
} else {
    header("Location: commentators.php");
    exit();
}

if (isset($_POST['c_name'])
    && trim($_POST['c_name']) != ''
    && isset($_POST['c_username'])
    && trim($_POST['c_username']) != ''
    && isset($_POST['c_password'])
    && trim($_POST['c_password']) != ''
    && isset($_POST['csrf_e'])
    && isset($_SESSION['csrf_e'])
    && $_POST['csrf_e'] == $_SESSION['csrf_e']
) {
    $name     = prepare_for_db($_POST['c_name']);
    $username = prepare_for_db($_POST['c_username']);
    $password = prepare_for_db($_POST['c_password']);

    $sql = <<<EOF
UPDATE commentators
SET
name = '{$name}',
username = '{$username}',
password = '{$password}'
WHERE
id = '{$id}'
EOF;

    //save commentator
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    header("Location: commentators.php");

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
    <title><?php echo $label_array[121]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include('menu_section.php'); ?>
<div class="container">
    <div class="row" style="margin-top: 10px;">
        <p><strong><?php echo $label_array[121]; ?></strong></p>

        <form action="" method="post" enctype="multipart/form-data" role="form">
            <table class="table table-bordered" style="width: 400px;">
                <tr>
                    <td>
                        <?php echo $label_array[119]; ?>
                    </td>
                    <td>
                        <input type="text" name="c_name"
                               value="<?php echo htmlspecialchars($row_commentator_details['name']); ?>"
                               style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[0]; ?>
                    </td>
                    <td>
                        <input type="text" name="c_username"
                               value="<?php echo htmlspecialchars($row_commentator_details['username']); ?>"
                               style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[1]; ?>
                    </td>
                    <td>
                        <input type="password" name="c_password"
                               value="<?php echo htmlspecialchars($row_commentator_details['password']); ?>"
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