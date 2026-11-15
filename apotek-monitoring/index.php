<?php
require_once __DIR__ . '/config/config.php';

if (!empty($_SESSION['id_user'])) {
    header('Location: ' . BASE_URL . '/modules/dashboard/index.php');
} else {
    header('Location: ' . BASE_URL . '/modules/auth/login.php');
}
exit;
