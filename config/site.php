<?php
$display_errors = true; // set true if you want to show error messages
$time_zone      = 'UTC'; // pick one from supported timezones: http://www.php.net/manual/en/timezones.php
$language = 'russian'; //language (make sure that the language file exists in lang folder)
$script_match_type = 'soccer'; //soccer or rugby
$use_sef_links = false; //whether or not to use sef links


if ($display_errors == false) {
    ini_set('display_errors', '0');
} else {
    ini_set('display_errors', '1');
}

date_default_timezone_set($time_zone);

set_time_limit(0);

require_once(dirname(__FILE__) . '/../lang/english.php');

if ($language != 'english'
    && file_exists(dirname(__FILE__) . "/../lang/$language.php")){
    require_once(dirname(__FILE__) . "/../lang/$language.php");
}

$script_match_full_length = 90;
$script_match_first_half_length = 45;
if ($script_match_type == 'rugby'){
    $script_match_full_length = 80;
    $script_match_first_half_length = 40;
}