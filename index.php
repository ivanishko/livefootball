<?php

/*
 * In this page all matches with the status other than "Temprorary match record." are listed.
 */
require_once('config/site.php');
require_once('Connections/conn.php');

$query_Recordset_List = <<<EOF
SELECT
t1.id,
t1.title,
t1.description,
t1.match_date,
t2.name AS home_team_name,
t3.name AS away_team_name
FROM matches t1
LEFT JOIN teams t2 ON t1.team1_id = t2.id
LEFT JOIN teams t3 ON t1.team2_id = t3.id
WHERE
t1.is_deleted = 0
AND NOT t1.status = 'temp'
ORDER BY t1.id DESC
EOF;

$Recordset_List = mysqli_query($conn, $query_Recordset_List) or die(mysqli_error($conn));
$totalRows_Recordset_List = mysqli_num_rows($Recordset_List);

$matches = array();

while ($row_Recordset_List = mysqli_fetch_assoc($Recordset_List)) {
    if ($use_sef_links == true){
        $sef_link = '';
        $sef_link .= $row_Recordset_List['title'] . ' ';
        $sef_link .= '(' . $row_Recordset_List['home_team_name'] . ' ';
        $sef_link .= 'vs ' . $row_Recordset_List['away_team_name'] . ') ';
        $sef_link .= $row_Recordset_List['match_date'];
        $sef_link = str_replace("-", " ", $sef_link);
        $sef_link = preg_replace('#\s{1,}#', " ", $sef_link);
        $sef_link = str_replace(" ", "-", $sef_link);
        $sef_link = preg_replace('#[^a-zA-Z0-9-]#', "", $sef_link);
        $sef_link .= '_m'. $row_Recordset_List['id'] . '.html';

        $row_Recordset_List['link'] = $sef_link;
    }
    else{
        $row_Recordset_List['link'] = 'match.php?id=' . $row_Recordset_List['id'];
    }

    $matches[] = $row_Recordset_List;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $label_array[102]; ?></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="margin:0;padding:0;">

<div class="container">
    <div class="row" style="margin-top: 10px;">
        <table class="table table-bordered table-striped">
            <tr class="info">
                <th><?php echo $label_array[102]; ?></th>
            </tr>
            <?php
            foreach ($matches as $match) {
                ?>
                <tr>
                    <td>
                        <h4><?php echo htmlspecialchars($match['title']); ?> (<?php echo htmlspecialchars
                            (
                                $match['home_team_name']
                            ); ?>
                            vs <?php echo $match['away_team_name']; ?>) <?php echo htmlspecialchars($match['match_date']);
                            ?></h4>
                        <?php if ($match['description'] != '') {
                            echo '<p>' . nl2br(htmlspecialchars($match['description'])) . '</p>';
                        } ?>
                        <p><a href="<?php echo $match['link']; ?>"><?php echo $label_array[103]; ?></a></p>
                    </td>
                </tr>
            <?php
            }
            if (count($matches) == 0) {
                ?>
                <tr>
                    <td>
                        <?php echo $label_array[104]; //No match record exists. ?>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>