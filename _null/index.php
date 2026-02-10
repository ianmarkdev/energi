<?php
$output = [];
exec("/usr/local/bin/wkhtmltopdf https://heizoel-kalkulator.de/ output.pdf", $output);
print_r($output);
?>
