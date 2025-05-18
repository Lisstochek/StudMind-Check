<?php
session_start();
session_unset();
session_destroy();
header("Location: StudMind Check.html");
exit();
?>