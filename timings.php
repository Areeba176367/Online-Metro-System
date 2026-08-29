<?php
require_once 'config.php';

// Filter inputs check karo (GET request ke zariye)
$filter_train = $_GET['train'] ?? '';
$filter_date  = $_GET['date'] ?? '';

// Base query likho
$query = "
    SELECT s.*, t.Train_Name, st.Station_Name 
    FROM schedule s 
    JOIN train t ON s.Train_ID = t.Train_ID 
    JOIN station st ON s.Station_ID = st.Station_ID 
";

$params = [];
$conditions = [];

// Agar user ne train select ki ho
if (!empty($filter_train)) {
    $conditions[] = "t.Train_Name = ?";
    $params[] = $filter_train;
}

// Agar user ne date select ki ho
if (!empty($filter_date)) {
    $conditions[] = "s.Date = ?";
    $params[] = $filter_date;
}

// Agar koi condition ho to query mein WHERE clause add karo
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY s.Date DESC, t.Train_Name, s.Departure_Time";

// Prepared statement execute karo
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$all_schedules = $stmt->fetchAll();

// Group by train and date
$grouped = [];
foreach ($all_schedules as $s) {
    $key = $s['Train_Name'] . ' | ' . $s['Date'];
    $grouped[$key][] = $s;
}

// Dropdown filter ke liye trains ki list nikalen
$stmt = $pdo->query("SELECT * FROM train ORDER BY Train_Name");
$trains = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Train Timings - Pakistan Railways</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Header -->
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
            <li><a href="timings.php" class="active">Train Timings</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="dashboard.php">My Bookings</a></li>
            <?php endif; ?>
        </ul>
        <div class="nav-auth">
            <?php if (isLoggedIn()): ?>
                <span style="font-size:13px;color:var(--fg-secondary)"><?php echo htmlspecialchars($_SESSION['passenger_name']); ?></span>
                <a href="logout.php" class="btn btn-outline btn-sm">Log out</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline btn-sm">Log in</a>
                <a href="register.php" class="btn btn-primary btn-sm">Sign up</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="main-content">
    <div class="section-header">
        <h2>Train Timings</h2>
        <p>Complete schedule of all Pakistan Railways trains</p>
    </div>

    <!-- Filter Form (PHP GET Method) -->
    <div class="search-card" style="margin-bottom:24px">
        <form method="GET" action="timings.php" class="search-grid" style="grid-template-columns: 1fr 1fr auto auto; align-items: end; gap: 12px; display: grid;">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Filter by Train</label>
                <select name="train" class="form-control">
                    <option value="">All Trains</option>
                    <?php foreach ($trains as $t): ?>
                        <option value="<?php echo htmlspecialchars($t['Train_Name']); ?>" <?php echo $filter_train === $t['Train_Name'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($t['Train_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Filter by Date</label>
                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="height: 42px;">Filter</button>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <a href="timings.php" class="btn btn-outline" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">Reset</a>
            </div>
        </form>
    </div>

    <?php foreach ($grouped as $key => $schedules): ?>
        <?php
            $parts = explode(' | ', $key);
            $train_name = $parts[0];
            $date = $parts[1];
        ?>
        <div class="results-container schedule-group" style="margin-bottom:24px">
            <div class="results-header">
                <h3><?php echo htmlspecialchars($train_name); ?></h3>
                <span class="results-count"><?php echo date('d M Y', strtotime($date)); ?></span>
            </div>
            <table class="timings-table">
                <thead>
                    <tr>
                        <th>Station</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Stop Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $s): ?>
                        <tr>
                            <td style="font-weight:600"><?php echo htmlspecialchars($s['Station_Name']); ?></td>
                            <td>
                                <?php if ($s['Departure_Time']): ?>
                                    <?php echo date('h:i A', strtotime($s['Departure_Time'])); ?>
                                <?php else: ?>
                                    <span style="color:var(--fg-secondary)">--</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($s['Arrival_Time']): ?>
                                    <?php echo date('h:i A', strtotime($s['Arrival_Time'])); ?>
                                <?php else: ?>
                                    <span style="color:var(--fg-secondary)">--</span>
                                <?php endif; ?>
                            </td>
                            
                            <td style="color:var(--fg-secondary)">
                                <?php
                                if ($s['Arrival_Time'] && $s['Departure_Time']) {
                                    $diff = strtotime($s['Departure_Time']) - strtotime($s['Arrival_Time']);
                                    if ($diff > 0) {
                                        echo floor($diff / 60) . ' min';
                                    } else {
                                        echo '--';
                                    }
                                } else {
                                    echo 'Terminus';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>

    <?php if (empty($grouped)): ?>
        <div class="no-results">
            <div class="nr-icon">&#128646;</div>
            <h3>No Schedules Available</h3>
            <p>No train schedules found matching your search.</p>
        </div>
    <?php endif; ?>
</div>

<footer class="footer">
    <div class="footer-inner">
        <div>
            <h4>PAKISTAN RAILWAYS</h4>
            <p>The national railway company of Pakistan.</p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <p><a href="index.php">Home</a></p>
            <p><a href="timings.php">Train Timings</a></p>
        </div>
        <div>
            <h4>Contact</h4>
            <p>Helpline: 117</p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?php echo date('Y'); ?> Pakistan Railways. All rights reserved.
    </div>
</footer>

</body>
</html>