<?php

$testDbFile = $GLOBALS['DB_FILE'];

register_shutdown_function(function() use ($testDbFile) {
    if (file_exists($testDbFile)) {
        unlink($testDbFile);
    }
});

?>
