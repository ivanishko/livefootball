<?php
require_once(dirname(__FILE__) . '/../config/database.php');

$conn = mysqli_connect($conn_host, $conn_user, $conn_password, $conn_database) or die("Could not connect to database.
Please check your database credentials.");

mysqli_query($conn, "SET NAMES 'utf8'");
mysqli_query($conn, "SET CHARACTER SET utf8");
mysqli_query($conn, "SET COLLATION_CONNECTION = 'utf8_unicode_ci'");

function prepare_for_db($data, $convert_html_special_chars = false)
{
    global $conn;

    if (get_magic_quotes_gpc())
    {
        $data = stripslashes($data);
    }

    if ($convert_html_special_chars == true)
    {
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    return mysqli_real_escape_string($conn, trim($data));
}