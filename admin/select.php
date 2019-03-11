<?php
if (count($_POST) > 0){

    echo "<pre>";
    echo "hello world!";
    var_dump($_POST);
    echo "</pre>";
}
?>

<form action="" method="post">
    <select name="playerselect" id="">
        <option value="player1">Веркашанский</option>
        <option value="player2">Иванов</option>
    </select>
    <input type="submit" name="submit">
</form>

