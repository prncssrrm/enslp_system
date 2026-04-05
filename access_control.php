<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function check_access($allowed_roles = []) {
    $role = strtolower($_SESSION['role'] ?? '');

    if (!isset($_SESSION['user_id']) || !in_array($role, $allowed_roles)) {
        echo "
        <div style='text-align:center;margin-top:120px;font-family:Arial'>
            <h1>⛔ Access Denied</h1>
            <p>You do not have permission to access this page.</p>
            
        </div>
        ";
        exit();
    }
}