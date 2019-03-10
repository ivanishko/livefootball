<?php
require_once('../config/site.php');

session_start();
if (!isset($_SESSION['script_user'])) {
    header('Location:index.php');
    exit();
}

require_once('../Connections/conn.php');

$_SESSION['csrf_d'] = md5(uniqid());

if (isset($_POST['c_name'])
    && trim($_POST['c_name']) != ''
    && isset($_POST['c_username'])
    && trim($_POST['c_username']) != ''
    && isset($_POST['c_password'])
    && trim($_POST['c_password']) != ''
    && isset($_POST['csrf_i'])
    && isset($_SESSION['csrf_i'])
    && $_POST['csrf_i'] == $_SESSION['csrf_i']
) {
    $name = prepare_for_db($_POST['c_name']);
    $username    = prepare_for_db($_POST['c_username']);
    $password = prepare_for_db($_POST['c_password']);

    $sql = "INSERT INTO commentators(name, username, password) VALUES ('{$name}', '{$username}', '{$password}')";

    //SQL
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
    header("Location: commentators.php");
    exit();
}

$_SESSION['csrf_i'] = md5(uniqid());

$query_Recordset_List = "SELECT id, name FROM commentators WHERE is_deleted = 0 ORDER BY name ASC";
$Recordset_List = mysqli_query($conn, $query_Recordset_List) or die(mysqli_error($conn));
$row_Recordset_List = mysqli_fetch_assoc($Recordset_List);
$totalRows_Recordset_List = mysqli_num_rows($Recordset_List);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $label_array[117]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?php include('menu_section.php'); ?>
<div class="container">
    <div class="row" style="margin-top: 10px;">
        <p><strong><?php echo $label_array[118]; ?></strong></p>

        <form action="" method="post" enctype="multipart/form-data" role="form">
            <table class="table table-bordered" style="width: 500px;">
                <tr>
                    <td>
                        <?php echo $label_array[119]; ?>
                    </td>
                    <td>
                        <input type="text" name="c_name" autocomplete="off" style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[0]; ?>
                    </td>
                    <td>
                        <input type="text" name="c_username" autocomplete="off" value="" style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo $label_array[1]; ?>
                    </td>
                    <td>
                        <input type="password" name="c_password" autocomplete="off" value="" style="width:160px;"/>
                    </td>
                </tr>
                <tr>
                    <td><input type="hidden" name="csrf_i" value="<?php echo $_SESSION['csrf_i']; ?>"/></td>
                    <td>
                        <input type="submit" name="Submit" value="<?php echo $label_array[95]; ?>"/>
                    </td>
                </tr>
            </table>
        </form>
        <?php
        if ($totalRows_Recordset_List != 0) {
            ?>
            <p><strong><?php echo $label_array[117]; ?></strong></p>
            <table class="table table-bordered table-striped" style="width: 500px;">
                <tr class="info">
                    <th><?php echo $label_array[119]; ?></th>
                    <th><?php echo $label_array[87]; ?></th>
                    <th><?php echo $label_array[42]; ?></th>
                </tr>
                <?php
                do {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row_Recordset_List['name']); ?></td>
                        <td><a href="edit_commentator.php?id=<?php echo $row_Recordset_List['id']; ?>"><?php echo
                                $label_array[87]; ?></a></td>
                        <td>
                            <form id="d_c_<?php echo $row_Recordset_List['id']; ?>"
                                  action="<?php echo 'delete_commentator.php?id=' . $row_Recordset_List['id']; ?>"
                                  method="post" role="form">
                                <input type="hidden" name="csrf_d" value="<?php echo $_SESSION['csrf_d']; ?>"/>
                            </form>
                            <a href="#"
                               onclick="if (confirm('<?php echo $label_array[44]; ?>')) { document.getElementById
                                   ('<?php echo 'd_c_' .
                                   $row_Recordset_List['id']; ?>').submit(); } return false;"><?php echo
                                $label_array[42]; ?></a>
                        </td>
                    </tr>
                <?php
                } while ($row_Recordset_List = mysqli_fetch_assoc($Recordset_List));
                ?>
            </table>
        <?php } ?>
    </div>
</div>
</body>
</html>