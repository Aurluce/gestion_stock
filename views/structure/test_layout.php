<?php
$title = "Test";
ob_start();
?>
<h1>Test avec layout</h1>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
