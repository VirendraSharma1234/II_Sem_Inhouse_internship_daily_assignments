<?php session_start();
$_SESSION['user'] = array(
    "name" => "Mahesh",
    "role" => "Admin",
    "city" => "Jaipur"
);

echo $_SESSION['user']['name'];
echo "<br>";
echo $_SESSION['user']['role'];
