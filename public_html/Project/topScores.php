<?php require_once(__DIR__ . "/../../partials/nav.php"); ?>

<?php
$query = $_GET["score"];
$result = [];
if($query == "Top weekly") {
    get10week();
    }

elseif($query == "Top monthly"){
    get10month();
}
elseif($query == "Top Lifetime"){
    get10lifetime();
}
 else {
	flash("Something went wrong, try again");
}

?>

<?php require(__DIR__ . "/../../partials/flash.php");
