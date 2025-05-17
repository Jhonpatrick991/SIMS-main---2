<?php
$con = mysqli_connect("localhost", "sims", "sims", "sims");

session_start();

if(!$con) {
    die("Connection Failed". mysqli_connect_error());
}
?>