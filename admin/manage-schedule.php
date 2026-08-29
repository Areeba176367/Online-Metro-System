<?php
require_once 'config.php';
require_once 'admin-auth.php';

 $message = '';
 $error = '';

// Add new schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $train_id = (int)($_POST['train_id'] ?? 0);
    $station_id = (int)($_POST['station_id'] ?? 0);
    $arrival = $_POST['arrival_time'] ?? '';
    $departure = $_POST['departure_time'] ?? '';
    $date = $_POST['date'] ?? '';

    if (!$train_id || !$station_id || empty($arrival) || empty($departure) || empty($date)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO schedule (Train_ID, Station_ID, Arrival_Time, Departure_Time, Date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$train_id, $station_id, $arrival, $departure, $date]);
        $message = 'Schedule added successfully.';
    }
}

// Delete schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM schedule WHERE Schedule_ID = ?");
        $stmt->execute([$id]);
        $message = 'Schedule deleted successfully.';
    } catch (PDOException $e) {
        $error = 'Cannot delete this schedule — tickets may be linked to it.';
    }
}

 $schedules = $pdo->query("
    SELECT s.*, t.Train_Name, st.Station_Name
    FROM schedule s
    JOIN train t ON s.Train_ID = t.Train_ID
    JOIN station st ON s.Station_ID = st.Station_ID
    ORDER BY s.Date DESC, s.Departure_Time ASC
")->fetchAll();

 $trains = $pdo->query("SELECT Train_ID, Train_Name FROM train ORDER BY Train_Name")->fetchAll();
 $stations = $pdo->query("SELECT Station_ID, Station_Name FROM station ORDER BY Station_Name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule - Admin</title>
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
            <li><a href="manage-schedule.php" class="active">Schedule</a></li>
            <li><a href="manage-tickets.php">Tickets</a></li>
            <li><a href="manage-notifications.php">Notifications</a></li>
        </ul>
        <div class="nav-auth">
            <span style="font-size:13px;color:var(--fg-secondary)"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="admin-logout.php" class="btn btn-outline btn-sm">Log out</a>
        </div>
    </div>
</header>

<div class="main-content">
    <div class="section-header">
        <h2>Manage Schedule</h2>
        <p>Add or remove train schedules</p>
    </div>

    <?php if ($message): ?><div class="alert alert-success">&#10003; <?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger">&#9888; <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="auth-card" style="max-width:none;margin-bottom:28px">
        <h3 style="margin-bottom:16px;font-size:16px">Add New Schedule</h3>
        <form method="POST" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;align-items:end">
            <div class="form-group" style="margin:0">
                <label>Train</label>
                <select name="train_id" class="form-control" required>
                    <option value="">Select train</option>
                    <?php foreach ($trains as $t): ?>
                        <option value="<?php echo $t['Train_ID']; ?>"><?php echo htmlspecialchars($t['Train_Name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>Station</label>
                <select name="station_id" class="form-control" required>
                    <option value="">Select station</option>
                    <?php foreach ($stations as $s): ?>
                        <option value="<?php echo $s['Station_ID']; ?>"><?php echo htmlspecialchars($s['Station_Name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="form-group" style="margin:0">
                <label>Arrival Time</label>
                <input type="time" name="arrival_time" class="form-control" required>
            </div>
            <div class="form-group" style="margin:0">
                <label>Departure Time</label>
                <input type="time" name="departure_time" class="form-control" required>
            </div>
            <button type="submit" name="add_schedule" class="btn btn-primary">Add Schedule</button>
        </form>
    </div>

    <div style="overflow-x:auto">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Train</th>
                <th>Station</th>
                <th>Date</th>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($schedules)): ?>
                <tr><td colspan="7" style="padding:20px;text-align:center">No schedules found.</td></tr>
            <?php endif; ?>
            <?php foreach ($schedules as $s): ?>
            <tr>
                <td>#<?php echo $s['Schedule_ID']; ?></td>
                <td><?php echo htmlspecialchars($s['Train_Name']); ?></td>
                <td><?php echo htmlspecialchars($s['Station_Name']); ?></td>
                <td><?php echo date('d M Y', strtotime($s['Date'])); ?></td>
                <td><?php echo date('h:i A', strtotime($s['Arrival_Time'])); ?></td>
                <td><?php echo date('h:i A', strtotime($s['Departure_Time'])); ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Delete this schedule?');" style="display:inline">
                        <input type="hidden" name="delete_id" value="<?php echo $s['Schedule_ID']; ?>">
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