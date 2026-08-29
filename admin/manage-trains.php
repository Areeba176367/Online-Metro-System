<?php
require_once 'config.php';
require_once 'admin-auth.php';

 $message = '';
 $error = '';

// Add new train
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_train'])) {
    $name = trim($_POST['train_name'] ?? '');
    $route = trim($_POST['route'] ?? '');
    $seats = (int)($_POST['total_seats'] ?? 0);
    $status = $_POST['status'] ?? 'Active';

    if (empty($name) || empty($route) || $seats <= 0) {
        $error = 'Please fill in all fields correctly.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO train (Train_Name, Route, TotalSeats, Status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $route, $seats, $status]);
        $message = 'Train added successfully.';
    }
}

// Update train status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['train_id'];
    $status = $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE train SET Status = ? WHERE Train_ID = ?");
    $stmt->execute([$status, $id]);
    $message = 'Train status updated.';
}

// Delete train
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM train WHERE Train_ID = ?");
        $stmt->execute([$id]);
        $message = 'Train deleted successfully.';
    } catch (PDOException $e) {
        $error = 'Cannot delete this train — it has existing schedules or tickets linked to it.';
    }
}

 $trains = $pdo->query("SELECT * FROM train ORDER BY Train_ID DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trains - Admin</title>
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
            <li><a href="manage-trains.php" class="active">Trains</a></li>
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
        <h2>Manage Trains</h2>
        <p>Add, update, or remove trains</p>
    </div>

    <?php if ($message): ?><div class="alert alert-success">&#10003; <?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger">&#9888; <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="auth-card" style="max-width:none;margin-bottom:28px">
        <h3 style="margin-bottom:16px;font-size:16px">Add New Train</h3>
        <form method="POST" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;align-items:end">
            <div class="form-group" style="margin:0">
                <label>Train Name</label>
                <input type="text" name="train_name" class="form-control" placeholder="e.g. Green Line Express" required>
            </div>
            <div class="form-group" style="margin:0">
                <label>Route</label>
                <input type="text" name="route" class="form-control" placeholder="e.g. Karachi - Islamabad" required>
            </div>
            <div class="form-group" style="margin:0">
                <label>Total Seats</label>
                <input type="number" name="total_seats" class="form-control" placeholder="e.g. 350" min="1" required>
            </div>
            <div class="form-group" style="margin:0">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" name="add_train" class="btn btn-primary">Add Train</button>
        </form>
    </div>

    <div style="overflow-x:auto">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Train Name</th>
                <th>Route</th>
                <th>Seats</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($trains)): ?>
                <tr><td colspan="6" style="padding:20px;text-align:center">No trains found.</td></tr>
            <?php endif; ?>
            <?php foreach ($trains as $t): ?>
            <tr>
                <td>#<?php echo $t['Train_ID']; ?></td>
                <td><?php echo htmlspecialchars($t['Train_Name']); ?></td>
                <td><?php echo htmlspecialchars($t['Route']); ?></td>
                <td><?php echo $t['TotalSeats']; ?></td>
                <td>
                    <form method="POST" style="display:flex;gap:6px;align-items:center">
                        <input type="hidden" name="train_id" value="<?php echo $t['Train_ID']; ?>">
                        <select name="new_status" class="form-control" style="padding:4px 8px;font-size:13px">
                            <option value="Active" <?php echo $t['Status']==='Active'?'selected':''; ?>>Active</option>
                            <option value="Inactive" <?php echo $t['Status']==='Inactive'?'selected':''; ?>>Inactive</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-outline btn-sm">Update</button>
                    </form>
                </td>
                <td>
                    <form method="POST" onsubmit="return confirm('Delete this train? This cannot be undone.');" style="display:inline">
                        <input type="hidden" name="delete_id" value="<?php echo $t['Train_ID']; ?>">
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