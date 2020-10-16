<?php // header.php
session_start();
echo "<!DOCTYPE html>\n<html><head><script src='OSC.js'></script>";
include 'functions.php';

echo "<title>$appname</title><link rel='stylesheet'" .
	"href='styles.css' type='text/css' />" .
	"</head><body><div class='appname'>$appname</div>";
	
echo "<ul class='menu'>".
	"<li><a href='index.php'>Summary</a></li>" .
	"<li><a href='computers.php'>Computers</a></li>" .
	"<li><a href='users.php'>Users</a></li>" .
	"<li><a href='settings.php'>Settings</a></li>" .
	"</ul><br />";
?>

