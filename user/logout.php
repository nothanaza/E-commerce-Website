<?php
session_start();
echo "<pre>"; var_dump($_SESSION); echo "</pre>";
session_unset();
session_destroy();
echo "<pre>"; var_dump(session_status(), session_id()); echo "</pre>";
header("Location: /E-commerce-Website/index.php");
exit;
?>