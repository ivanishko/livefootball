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
t1.id,
t1.last_start_time,
t1.status
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
    <title><?php echo $label_array[26]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script language="javascript1.2" type="text/javascript">
        var i = <?php echo $start_time; ?>;
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
            minutes = Math.floor(i / 60);
            seconds = i - (minutes * 60);
            if (minutes < 10) {
                minutes = "0" + minutes.toString();
            }
            if (seconds < 10) {
                seconds = "0" + seconds.toString();
            }
            document.getElementById('time_elapsed').innerHTML = minutes + " : " + seconds;
            i++;
            if (match_status == 'first_half' || match_status == 'second_half') {
                setTimeout('show_elapsed_time()', 1000);
            }
        }

        function refresh_page() {
            location.reload(true);
        }
    </script>
</head>

<body onload="show_elapsed_time();">
<div class="container center-block">
    <div class="row" style="text-align: center;">
        <span id="time_elapsed" style="font-size:22px; font-weight:bold;"></span>
    </div>
</div>
</body>
</html>