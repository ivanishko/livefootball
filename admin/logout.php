<?php
require_once('../config/site.php');

session_start();
session_destroy();
header("Location: index.php");