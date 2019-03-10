<?php
require_once('../config/site.php');

session_start();
if (!isset($_SESSION['script_user'])) {
    header('Location:index.php');
    exit();
}

require_once('../Connections/conn.php');

if (isset($_POST['username'])
    && trim($_POST['username']) != ''
    && isset($_POST['password'])
    && trim($_POST['password']) != ''
    && isset($_POST['csrf_e'])
    && isset($_SESSION['csrf_e'])
    && $_POST['csrf_e'] == $_SESSION['csrf_e']
) {
    if (!is_writable('access_credentials.php')) {
        die($label_array[4]); //'access_credentials.php is not writable! Please correct file mode first.'
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (get_magic_quotes_gpc()) {
        $username = stripslashes($_POST['username']);
        $password = stripslashes($_POST['password']);
    }

    $file_contents = <<<EOF
<?php
\$username = "{$username}";
\$password = "{$password}";
EOF;

    $file = fopen("access_credentials.php", 'w');

    fwrite($file, "$file_contents");

    fclose($file);

    header("Location: edit_access_credentials.php");
    exit();
}

$current_credentials = file_get_contents("access_credentials.php");
$username = '';
$password = '';

if (preg_match('/\$username = \"([^\"]*)\"/', $current_credentials, $result)) {
    $username = $result[1];
}

if (preg_match('/\$password = \"([^\"]*)\"/', $current_credentials, $result)) {
    $password = $result[1];
}

$_SESSION['csrf_e'] = md5(uniqid());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $label_array[3]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include('menu_section.php'); ?>
<div class="container">
    <div class="row" style="margin-top: 10px;">
        <p><strong><?php echo $label_array[3]; ?></strong></p>

        <form action="" method="post" enctype="multipart/form-data" role="form">
            <table class="table table-bordered" style="width: 400px;">
                <tr>
                    <td>
                        <?php echo $label_array[0]; ?>
                    </td>
                    <td>
                        <input type="text" name="username"
                               value="<?php echo htmlspecialchars($username); ?>"
                               style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[1]; ?>
                    </td>
                    <td>
                        <input type="password" name="password"
                               value="<?php echo htmlspecialchars($password); ?>"
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