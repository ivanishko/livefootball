<?php
/**
 * This file is used for easily set write permissions to the following files and directories:
 * - "admin/access_credentials.php"
 * - "img/logo"
 * you can delete this file once you have correct settings.
 */

require_once('config/site.php');

if (!is_writable('admin/access_credentials.php')) {
    if (file_exists('admin/access_credentials.php')) {
        die($label_array[107]); //'admin/access_credentials.php' doesn't exits!
    } else {
        if (!chmod('admin/access_credentials.php', 0755)) {
            die($label_array[125]); //can't set write permission for 'admin/access_credentials.php'!
        }
    }
}

if (!is_writable('img/logo')) {
    if (file_exists('img/logo')) {
        die("img/logo doesn't exits!");
    } else {
        if (!chmod('img/logo', 0755)) {
            die($label_array[105]); //"can't set write permission for 'img/logo'!
        }
    }
}

echo $label_array[106]; //ALL FILES HAVE CORRECT SETTINGS NOW!