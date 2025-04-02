<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../');
    exit;
}
?>
<h1>hello favory</h1>