<?php
/*
 * The contents of this file are shown in the commentary section in the match page.
 */
require_once('config/site.php');
require_once('Connections/conn.php');

$id = (isset($_GET['id']) ? intval($_GET['id']) : 0);

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
ORDER BY t1.comment_minute DESC
EOF;

$Recordset_Comments = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$match_comments = array();

while ($row_comment = mysqli_fetch_assoc($Recordset_Comments)) {
    $match_comments[] = $row_comment;
}
//match comments - end

$r = array('#FFFFFF', '#F2F2F2');
$i = 0;
?>
<table class="table table-bordered table-striped" style="font-size: 14px;">
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
            <td width="60" align="center" valign="top" <?php if ($is_different_color) {
                ?> style="color:<?php echo $color; ?>; background-color:<?php echo $bg_color; ?>;"<?php } ?>>
                <strong><?php echo htmlspecialchars($comment['comment_minute']); ?></strong>
            </td>
            <td align="left" <?php if ($is_different_color) {
                ?> style="color:<?php echo $color; ?>; background-color:<?php echo $bg_color; ?>;"<?php } ?>>
                <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
            </td>
        </tr>
    <?php } ?>
</table>