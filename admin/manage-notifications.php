<?php
require_once 'config.php';
require_once 'admin-auth.php';

$message = '';
$error = '';

// Send notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    $title      = trim($_POST['title'] ?? '');
    $msg        = trim($_POST['message'] ?? '');
    $type       = $_POST['type'] ?? 'General';
    $target     = $_POST['target'] ?? 'all';
    $pass_id    = intval($_POST['passenger_id'] ?? 0);

    if (empty($title) || empty($msg)) {
        $error = 'Title and message are required.';
    } else {
        if ($target === 'all') {
            // Broadcast to every passenger
            $passengers = $pdo->query("SELECT Passenger_ID FROM passenger")->fetchAll();
            foreach ($passengers as $p) {
                $stmt = $pdo->prepare("INSERT INTO notification (Passenger_ID, Title, Message, Type) VALUES (?, ?, ?, ?)");
                $stmt->execute([$p['Passenger_ID'], $title, $msg, $type]);
            }
            $message = 'Notification sent to all passengers.';
        } else {
            if (!$pass_id) {
                $error = 'Please select a passenger.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO notification (Passenger_ID, Title, Message, Type) VALUES (?, ?, ?, ?)");
                $stmt->execute([$pass_id, $title, $msg, $type]);
                $message = 'Notification sent to passenger.';
            }
        }
    }
}

// Delete notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $pdo->prepare("DELETE FROM notification WHERE Notification_ID = ?")->execute([$_POST['delete_id']]);
    $message = 'Notification deleted.';
}

$notifications = $pdo->query("
    SELECT n.*, p.Name AS passenger_name
    FROM notification n
    LEFT JOIN passenger p ON n.Passenger_ID = p.Passenger_ID
    ORDER BY n.Created_At DESC
    LIMIT 100
")->fetchAll();

$passengers = $pdo->query("SELECT Passenger_ID, Name FROM passenger ORDER BY Name")->fetchAll();

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
    <title>Manage Notifications - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .admin-table{
        width:100%;
        border-collapse:collapse;
        background:#15191d;
        border-radius:10px;
        overflow:hidden;
        border:1px solid rgba(255,255,255,.08);
    }

    .admin-table thead tr{
        background:#1e2328;
        text-align:left;
    }

    .admin-table th{
        padding:12px 14px;
        color:#fff;
        font-size:13px;
        font-weight:600;
    }

    .admin-table td{
        padding:12px 14px;
        color:#d4d4d4;
        border-top:1px solid rgba(255,255,255,.06);
        vertical-align:top;
        font-size:13px;
    }

    .admin-table tr:hover td{
        background:rgba(255,255,255,.03);
    }

    .type-badge{
        display:inline-block;
        padding:3px 10px;
        border-radius:20px;
        font-size:11px;
        font-weight:600;
    }

    /* Notification Form */
    .notification-grid{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:18px;
        margin-bottom:18px;
    }

    .notification-grid .form-group{
        margin:0;
    }

    .notification-grid textarea{
        grid-column:1/-1;
    }

    @media(max-width:900px){
        .notification-grid{
            grid-template-columns:repeat(2,1fr);
        }
    }

    @media(max-width:600px){
        .notification-grid{
            grid-template-columns:1fr;
        }
    }
    </style>
</head>
<body>

<header class="header">
    <div class="header-inner">
        <a href="admin-dashboard.php" class="logo" style="text-decoration:none">
            <div class="logo-icon">PR</div>
            <div class="logo-text">
                <span class="main">PAKISTAN RAILWAYS</span>
                <span class="sub">ADMIN PANEL</span>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="admin-dashboard.php">Dashboard</a></li>
            <li><a href="manage-passengers.php">Passengers</a></li>
            <li><a href="manage-trains.php">Trains</a></li>
            <li><a href="manage-schedule.php">Schedule</a></li>
            <li><a href="manage-tickets.php">Tickets</a></li>
            <li><a href="manage-notifications.php" class="active">Notifications</a></li>
        </ul>
        <div class="nav-auth">
            <span style="font-size:13px;color:var(--fg-secondary)"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="admin-logout.php" class="btn btn-outline btn-sm">Log out</a>
        </div>
    </div>
</header>

<div class="main-content">
    <div class="section-header">
        <h2>Manage Notifications</h2>
        <p>Send train updates to passengers</p>
    </div>

    <?php if ($message): ?><div class="alert alert-success">&#10003; <?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger">&#9888; <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <!-- Send Form -->
    <div class="auth-card" style="max-width:none;margin-bottom:28px">
        <h3 style="margin-bottom:20px;font-size:18px">
📢 Send New Notification
</h3>
        <form method="POST">
            <div class="notification-grid">

                <div class="form-group" style="margin:0">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Train Delayed" required>
                </div>

                <div class="form-group" style="margin:0">
                    <label>Type</label>
                    <select name="type" class="form-control">
                        <option value="General">&#128276; General</option>
                        <option value="Arrival">&#128994; Arrival</option>
                        <option value="Departure">&#128309; Departure</option>
                        <option value="Delay">&#128993; Delay</option>
                    </select>
                </div>

                <div class="form-group" style="margin:0">
                    <label>Send To</label>
                    <select name="target" class="form-control">
                        <option value="all">All Passengers</option>
                        <option value="one">Specific Passenger</option>
                    </select>
                </div>

                <div class="form-group" style="margin:0">
                    <label>Select Passenger</label>
                    <select name="passenger_id" class="form-control">
                        <option value="">Select...</option>
                        <?php foreach ($passengers as $p): ?>
                            <option value="<?php echo $p['Passenger_ID']; ?>"><?php echo htmlspecialchars($p['Name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="message" class="form-control" rows="3"
                    placeholder="e.g. Shalimar Express from Faisalabad is delayed by 45 minutes due to track maintenance. New departure time: 14:30." required
                    style="resize:vertical"></textarea>
            </div>

            <button
                type="submit"
                name="send"
                value="1"
                class="btn btn-primary"
                style="margin-top:20px;width:auto;min-width:220px;padding:12px 24px;">
                🔔 Send Notification
            </button>
        </form>
    </div>

    <!-- Notification History -->
    <div class="section-header">
        <h2 style="font-size:20px;margin-bottom:18px;">
📋 Notification History
</h2>
    </div>

    <div style="overflow-x:auto">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Message</th>
                <th>Type</th>
                <th>Sent To</th>
                <th>Read</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($notifications)): ?>
                <tr><td colspan="8" style="text-align:center;padding:20px">No notifications sent yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($notifications as $n): ?>
            <tr>
                <td>#<?php echo $n['Notification_ID']; ?></td>
                <td style="font-weight:600"><?php echo htmlspecialchars($n['Title']); ?></td>
                <td style="max-width:260px;color:#aaa"><?php echo htmlspecialchars(mb_strimwidth($n['Message'], 0, 80, '...')); ?></td>
                <td>
                    <span class="type-badge" style="background:<?php echo $type_colors[$n['Type']] ?? '#888'; ?>22;color:<?php echo $type_colors[$n['Type']] ?? '#888'; ?>">
                        <?php echo $n['Type']; ?>
                    </span>
                </td>
                <td>
                    <?php if(!empty($n['passenger_name'])): ?>
                        <span style="color:#fff;font-weight:600">
                            <?php echo htmlspecialchars($n['passenger_name']); ?>
                        </span>
                    <?php else: ?>
                        <span style="color:#22c55e;font-weight:600">
                            All Passengers
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if(!empty($n['Is_Read']) && $n['Is_Read'] == 1): ?>
                        <span style="color:#22c55e;font-weight:600;">✔ Read</span>
                    <?php else: ?>
                        <span style="color:#f59e0b;font-weight:600;">Unread</span>
                    <?php endif; ?>
                </td>
                <td style="white-space:nowrap"><?php echo date('d M Y h:i A', strtotime($n['Created_At'])); ?></td>
                <td>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="delete_id" value="<?php echo $n['Notification_ID']; ?>">
                        <button
                            type="submit"
                            class="btn btn-outline btn-sm">
                            🗑 Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
</body>
</html>