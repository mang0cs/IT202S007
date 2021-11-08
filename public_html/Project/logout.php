<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php");
flash("You have successfully been logged out");
require(__DIR__. "/../../partials/flash.php");