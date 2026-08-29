<?php
require_once 'config.php';
require_once 'admin-auth.php';

 $message = '';

// Update ticket status (e.g. cancel a booking)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['ticket_id'];
    $status = $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE ticket SET Status = ? WHERE Ticket_ID = ?");
    $stmt->execute([$status, $id]);
    $message = 'Ticket status updated.';
}

// Delete ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $pdo->prepare("DELETE FROM payment WHERE Ticket_ID = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM ticket WHERE Ticket_ID = ?")->execute([$id]);
    $message = 'Ticket deleted successfully.';
}

 $tickets = $pdo->query("
    SELECT t.*, p.Name AS passenger_name, tr.Train_Name, pay.Method AS payment_method
    FROM ticket t
    JOIN passenger p ON t.Passenger_ID = p.Passenger_ID
    JOIN train tr ON t.Train_ID = tr.Train_ID
    LEFT JOIN payment pay ON pay.Ticket_ID = t.Ticket_ID
    ORDER BY t.Ticket_ID DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tickets - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-table { width:100%; border-collapse:collapse; background:#15191d; border-radius:10px; overflow:hidden; border:1px solid rgba(255,255,255,0.08); }
        .admin-table thead tr { background:#1e2328; text-align:left; }
        .admin-table th { padding:12px 16px; color:#e6e6e6; font-weight:600; font-size:13px; }
        .admin-table td { padding:12px 16px; color:#d4d4d4; font-size:13px; border-top:1px solid rgba(255,255,255,0.06); }
        .admin-table tr:hover td { background:rgba(255,255,255,0.03); }
        .admin-table select.form-control { background:#1e2328; color:#e6e6e6; border:1px solid rgba(255,255,255,0.15); }
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
            <li><a href="manage-tickets.php" class="active">Tickets</a></li>
        </ul>
        <div class="nav-auth">
            <span style="font-size:13px;color:var(--fg-secondary)"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="admin-logout.php" class="btn btn-outline btn-sm">Log out</a>
        </div>
    </div>
</header>

<div class="main-content">
    <div class="section-header">
        <h2>Manage Tickets</h2>
        <p>View, update, or cancel passenger bookings</p>
    </div>

    <?php if ($message): ?><div class="alert alert-success">&#10003; <?php echo htmlspecialchars($message); ?></div><?php endif; ?>

    <div style="overflow-x:auto">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Passenger</th>
                <th>Train</th>
                <th>Seat</th>
                <th>Price</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tickets)): ?>
                <tr><td colspan="8" style="padding:20px;text-align:center">No tickets found.</td></tr>
            <?php endif; ?>
            <?php foreach ($tickets as $t): ?>
            <tr>
                <td>#TKT-<?php echo str_pad($t['Ticket_ID'], 6, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo htmlspecialchars($t['passenger_name']); ?></td>
                <td><?php echo htmlspecialchars($t['Train_Name']); ?></td>
                <td><?php echo htmlspecialchars($t['Seat_No']); ?></td>
                <td>PKR <?php echo number_format($t['Price']); ?></td>
                <td><?php echo $t['payment_method'] ? htmlspecialchars($t['payment_method']) : '—'; ?></td>
                <td>
                    <form method="POST" style="display:flex;gap:6px;align-items:center">
                        <input type="hidden" name="ticket_id" value="<?php echo $t['Ticket_ID']; ?>">
                        <select name="new_status" class="form-control" style="padding:4px 8px;font-size:13px">
                            <option value="Booked" <?php echo $t['Status']==='Booked'?'selected':''; ?>>Booked</option>
                            <option value="Cancelled" <?php echo $t['Status']==='Cancelled'?'selected':''; ?>>Cancelled</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-outline btn-sm">Update</button>
                    </form>
                </td>
                <td>
                    <form method="POST" onsubmit="return confirm('Delete this ticket permanently?');" style="display:inline">
                        <input type="hidden" name="delete_id" value="<?php echo $t['Ticket_ID']; ?>">
                        <button type="submit" class="btn btn-outline btn-sm">Delete</button>
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