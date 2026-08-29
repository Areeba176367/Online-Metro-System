<?php
// Include this at the very top of every admin-only page, right after config.php
if (!isset($_SESSION['admin_id'])) {
    redirect('admin-login.php');
}