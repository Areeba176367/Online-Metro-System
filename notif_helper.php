<?php
// notif_helper.php - include this in any passenger page to get unread count
// Usage: require_once 'notif_helper.php';  then use $notif_unread in header

 $notif_unread = 0;
if (isLoggedIn() && isset($_SESSION['passenger_id'])) {
    $s = $pdo->prepare("SELECT COUNT(*) FROM notification WHERE Passenger_ID = ? AND Is_Read = 0");
    $s->execute([$_SESSION['passenger_id']]);
    $notif_unread = (int)$s->fetchColumn();
}