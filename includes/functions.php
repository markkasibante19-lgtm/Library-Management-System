<?php
// Helper function to sanitize output
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>
