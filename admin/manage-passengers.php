]<?php
require_once 'config.php';
require_once 'admin-auth.php';

 $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM passenger WHERE Passenger_ID = ?");
    $stmt->execute([$id]);
    $message = 'Passenger deleted successfully.';
}

 $passengers = $pdo->query("SELECT * FROM passenger ORDER BY Passenger_ID DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Passengers - Admin</title>
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
            <li><a href="manage-passengers.php" class="active">Passengers</a></li>
            <li><a href="manage-trains.php">Trains</a></li>
            <li><a href="manage-schedule.php">Schedule</a></li>
            <li><a href="manage-tickets.php">Tickets</a></li>
        </ul>
        <div class="nav-auth">
            <span style="font-size:13px;color:var(--fg-secondary)"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="admin-logout.php" class="btn btn-outline btn-sm">Log out</a>
        </div>
    </div>
</header>

<div class="main-content">
    <div class="section-header">
        <h2>Manage Passengers</h2>
        <p>View and remove passenger accounts</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">&#10003; <?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div style="overflow-x:auto">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>CNIC</th>
                <th>Registered</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($passengers)): ?>
                <tr><td colspan="7" style="padding:20px;text-align:center">No passengers found.</td></tr>
            <?php endif; ?>
            <?php foreach ($passengers as $p): ?>
            <tr>
                <td>#<?php echo $p['Passenger_ID']; ?></td>
                <td><?php echo htmlspecialchars($p['Name']); ?></td>
                <td><?php echo htmlspecialchars($p['Email']); ?></td>
                <td><?php echo htmlspecialchars($p['Phone']); ?></td>
                <td><?php echo htmlspecialchars($p['CNIC'] ?: '—'); ?></td>
                <td><?php echo $p['Register_Date'] ? date('d M Y', strtotime($p['Register_Date'])) : '—'; ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Delete this passenger? This cannot be undone.');" style="display:inline">
                        <input type="hidden" name="delete_id" value="<?php echo $p['Passenger_ID']; ?>">
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