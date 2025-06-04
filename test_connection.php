<?php
require_once 'config/database.php';

try {
    $conn = getDbConnection();
    echo "Connection successful! Database is properly linked to the website.";
} catch(Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}