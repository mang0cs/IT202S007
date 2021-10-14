<?php
require(__DIR__."/../../partials/nav.php");
?>
<h1>Home</h1>
<?php
if(is_logged_in())
{
  echo "Welcome, " . get_username();
}
function is_logged_in($redirect = false, $destination = "login.php")
{
    if ($redirect) {
        die(header("Location: $destination"));
    }
    return isset($_SESSION["user"]); //se($_SESSION, "user", false, false);
}
?>