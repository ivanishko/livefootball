<?php
require_once('../config/site.php');

session_start();

if (isset($_SESSION['commentator_user'])) {
    header('Location:menu.php');
    exit();
}

require_once('../Connections/conn.php');

if (isset($_POST['username']) && isset($_POST['password'])) {

    $username = prepare_for_db($_POST['username']);
    $password = prepare_for_db($_POST['password']);

    $sql = <<<EOF
SELECT
t1.id
FROM commentators t1
WHERE
t1.username = '{$username}'
AND t1.password = '{$password}'
AND t1.is_deleted = 0
EOF;

    $Recordset_CommentatorDetails = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $row_commentator_details = mysqli_fetch_assoc($Recordset_CommentatorDetails);

    if (mysqli_num_rows($Recordset_CommentatorDetails) > 0) {
        $_SESSION['commentator_user'] = array();
        $_SESSION['commentator_user']['id'] = $row_commentator_details['id'];
        session_regenerate_id(true);
        header("Location:menu.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $label_array[27]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container">
    <div class="row" style="margin-top: 10px;">
        <p><strong><?php echo $label_array[27]; ?></strong></p>

        <form action="" method="post" enctype="multipart/form-data" role="form">
            <table class="table table-bordered" style="width: 400px;">
                <tr>
                    <td>
                        <?php echo $label_array[0]; ?>
                    </td>
                    <td>
                        <input type="text" name="username" style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[1]; ?>
                    </td>
                    <td>
                        <input type="password" name="password" style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="Submit" value="<?php echo $label_array[27]; ?>"/>
                    </td>
                </tr>
            </table>
        </form>
        <br/>
    </div>
</div>
</body>
</html>