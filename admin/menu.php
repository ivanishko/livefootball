<?php
require_once('../config/site.php');

session_start();
if (!isset($_SESSION['script_user'])) {
    header('Location:index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $label_array[90]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="margin:0;padding:0;">
<?php
include('menu_section.php');
?>

<div class="container">
    <div class="row" style="margin-top: 10px;">
        <p><?php echo $label_array[90]; ?></p>
    </div>
</div>
</body>
</html>