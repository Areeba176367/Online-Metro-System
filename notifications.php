<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$passenger_id = $_SESSION['passenger_id'];

// Mark all as read
if (isset($_GET['mark_read'])) {
    $pdo->prepare("UPDATE notification SET Is_Read = 1 WHERE Passenger_ID = ?")->execute([$passenger_id]);
    redirect('notifications.php');
}

// Mark single as read via URL
if (isset($_GET['read_id'])) {
    $pdo->prepare("UPDATE notification SET Is_Read = 1 WHERE Notification_ID = ? AND Passenger_ID = ?")->execute([$_GET['read_id'], $passenger_id]);
    redirect('notifications.php');
}

$notifications = $pdo->prepare("SELECT * FROM notification WHERE Passenger_ID = ? ORDER BY Created_At DESC");
$notifications->execute([$passenger_id]);
$notifications = $notifications->fetchAll();

$unread_count = count(array_filter($notifications, fn($n) => !$n['Is_Read']));

$type_icons = [
    'Arrival'   => '🟢',
    'Departure' => '🔵',
    'Delay'     => '🟡',
    'General'   => '🔔',
];
$type_colors = [
    'Arrival'   => '#22c55e',
    'Departure' => '#3b82f6',
    'Delay'     => '#f59e0b',
    'General'   => '#8b5cf6',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Pakistan Railways</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .notif-card {
            background: var(--card, #15191d);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 12px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }
        .notif-card.unread { border-left: 4px solid #22c55e; background: rgba(34,197,94,0.04); }
        .notif-icon { font-size: 24px; margin-top: 2px; min-width: 30px; text-align:center; }
        .notif-title { font-weight: 600; font-size: 15px; margin-bottom: 4px; }
        .notif-msg { font-size: 13px; color: var(--fg-secondary, #aaa); line-height: 1.5; }
        .notif-meta { font-size: 12px; color: #666; margin-top: 8px; }
        .type-badge { display:inline-block; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .empty-state { text-align:center; padding:60px 20px; color:var(--fg-secondary,#888); }
        .empty-state .icon { font-size: 48px; margin-bottom: 16px; }
        .mark-read-btn { font-size:12px; padding:4px 12px; }
    </style>
</head>
<body>

<header class="header">
    <div class="header-inner">
        <a href="index.php" class="logo" style="text-decoration:none">
            <div class="logo-icon">PR</div>
            <div class="logo-text">
                <span class="main">PAKISTAN RAILWAYS</span>
                <span class="sub">EASYWAY</span>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="timings.php">Train Timings</a></li>
            <li><a href="dashboard.php">My Bookings</a></li>
            <li><a href="notifications.php" class="active">&#128276; Notifications
                <?php if ($unread_count > 0): ?>
                    <span style="background:#22c55e;color:#000;border-radius:50%;padding:1px 6px;font-size:11px;font-weight:700;margin-left:4px"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a></li>
        </ul>
        <div class="nav-auth">
            <span style="font-size:13px;color:var(--fg-secondary)"><?php echo htmlspecialchars($_SESSION['passenger_name']); ?></span>
            <a href="logout.php" class="btn btn-outline btn-sm">Log out</a>
        </div>
    </div>
</header>

<div class="main-content">
    <div style="max-width:760px;margin:0 auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
            <div>
                <h2 style="margin:0">&#128276; Notifications</h2>
                <p style="margin:4px 0 0;color:var(--fg-secondary);font-size:13px">
                    <?php echo count($notifications); ?> total
                    <?php if ($unread_count > 0): ?>
                        &bull; <span style="color:#22c55e;font-weight:600"><?php echo $unread_count; ?> unread</span>
                    <?php endif; ?>
                </p>
            </div>
            <?php if ($unread_count > 0): ?>
                <a href="notifications.php?mark_read=1" class="btn btn-outline btn-sm mark-read-btn">&#10003; Mark all as read</a>
            <?php endif; ?>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="empty-state">
                <div class="icon">&#128276;</div>
                <h3>No Notifications Yet</h3>
                <p>You will receive updates here about your train arrival, departure, and delays.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <?php
                    $icon  = $type_icons[$n['Type']] ?? '🔔';
                    $color = $type_colors[$n['Type']] ?? '#888';
                    $is_unread = !$n['Is_Read'];
                ?>
                <div class="notif-card <?php echo $is_unread ? 'unread' : ''; ?>">
                    <div class="notif-icon"><?php echo $icon; ?></div>
                    <div style="flex:1">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;flex-wrap:wrap">
                            <div class="notif-title">
                                <?php if ($is_unread): ?>
                                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22c55e;margin-right:6px;vertical-align:middle"></span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($n['Title']); ?>
                            </div>
                            <span class="type-badge" style="background:<?php echo $color; ?>22;color:<?php echo $color; ?>">
                                <?php echo $n['Type']; ?>
                            </span>
                        </div>
                        <div class="notif-msg"><?php echo htmlspecialchars($n['Message']); ?></div>
                        <div class="notif-meta">
                            &#128337; <?php echo date('d M Y, H:i', strtotime($n['Created_At'])); ?>
                            <?php if ($is_unread): ?>
                                &bull; <a href="notifications.php?read_id=<?php echo $n['Notification_ID']; ?>" style="color:#22c55e;font-size:11px">Mark as read</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-bottom" style="margin-top:0">
        &copy; <?php echo date('Y'); ?> Pakistan Railways. All rights reserved.
    </div>
</footer>

</body>
</html>