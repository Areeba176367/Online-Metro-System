<?php
require_once 'config.php';

 $from_station = intval($_GET['from_station'] ?? 0);
 $to_station   = intval($_GET['to_station'] ?? 0);
 $travel_date  = $_GET['travel_date'] ?? '';
 $travel_class = $_GET['travel_class'] ?? 'Economy';

// Get station names
 $from_name = '';
 $to_name   = '';
if ($from_station) {
    $stmt = $pdo->prepare("SELECT Station_Name FROM station WHERE Station_ID = ?");
    $stmt->execute([$from_station]);
    $from_name = $stmt->fetchColumn();
}
if ($to_station) {
    $stmt = $pdo->prepare("SELECT Station_Name FROM station WHERE Station_ID = ?");
    $stmt->execute([$to_station]);
    $to_name = $stmt->fetchColumn();
}

// Price multiplier per class
 $class_prices = [
    'Economy'    => 1.0,
    'Business'   => 1.8,
    'AC Sleeper' => 2.5,
    'First Class'=> 3.5
];
 $base_price = 500;

 $results = [];

if ($from_station && $travel_date) {
    $sql = "
        SELECT
            s.Schedule_ID,
            s.Train_ID,
            t.Train_Name,
            t.Route,
            st.Station_Name AS from_station_name,
            s.Departure_Time AS dep_time,
            s.Arrival_Time   AS arr_time,
            s.Date,
            t.TotalSeats
        FROM schedule s
        JOIN train   t  ON s.Train_ID   = t.Train_ID
        JOIN station st ON s.Station_ID = st.Station_ID
        WHERE s.Station_ID = ?
          AND s.Date        = ?
          AND t.Status      = 'Active'
        ORDER BY s.Departure_Time
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$from_station, $travel_date]);
    $results = $stmt->fetchAll();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Pakistan Railways</title>
    <link rel="stylesheet" href="style.css">
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
            <?php if (isLoggedIn()): ?>
                <li><a href="dashboard.php">My Bookings</a></li>
                <li>
                    <a href="notifications.php">&#128276; Notifications</a>
                </li>
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
        <h2>Search Results</h2>
        <p>
            <?php if ($from_name): ?>
                Departing from <strong><?php echo htmlspecialchars($from_name); ?></strong>
                <?php if ($to_name): ?>
                    &rarr; <strong><?php echo htmlspecialchars($to_name); ?></strong>
                <?php endif; ?>
                &nbsp;|&nbsp; <?php echo date('d M Y', strtotime($travel_date)); ?>
                &nbsp;|&nbsp; <?php echo htmlspecialchars($travel_class); ?>
            <?php else: ?>
                Please select a departure station
            <?php endif; ?>
        </p>
    </div>

    <div class="results-container">
        <div class="results-header">
            <h3>Available Trains</h3>
            <span class="results-count"><?php echo count($results); ?> found</span>
        </div>

        <?php if (empty($results)): ?>
            <div class="no-results">
                <div class="nr-icon">&#128646;</div>
                <h3>No Trains Found</h3>
                <p>No trains available from <strong><?php echo htmlspecialchars($from_name ?: 'selected station'); ?></strong> on <?php echo date('d M Y', strtotime($travel_date)); ?>.</p>
                <a href="index.php" class="btn btn-primary" style="margin-top:20px">Search Again</a>
            </div>
        <?php else: ?>
            <?php foreach ($results as $r): ?>
                <?php
                    
                    $price_multiplier = $class_prices[$travel_class] ?? 1.0;
                    $price            = $base_price * $price_multiplier;
                ?>
                <div class="train-card">
                    <!-- : Departure station — from_station_name use karo (admin ne jo add kiya) -->
                    <div class="station-point">
                        <div class="time"><?php echo date('h:i A', strtotime($r['dep_time'])); ?></div>
                        <div class="station"><?php echo htmlspecialchars($r['from_station_name']); ?></div>
                        <div class="train-info" style="margin-top:8px">
                            <div class="train-name"><?php echo htmlspecialchars($r['Train_Name']); ?></div>
                        </div>
                    </div>

                    <div class="route-line">
                        <div class="line"></div>
                    </div>

                    <!-- FIX 4: Destination — to_name use karo jo passenger ne select kiya -->
                    <div class="station-point">
                        <div class="time">--</div>
                        <div class="station">
                            <?php echo $to_name ? htmlspecialchars($to_name) : 'Final Destination'; ?>
                        </div>
                    </div>

                    <div class="book-section">
                        <div class="price">PKR <?php echo number_format($price); ?></div>
                        <div class="class-label"><?php echo htmlspecialchars($travel_class); ?></div>
                        <?php if (isLoggedIn()): ?>
                            <a href="book.php?schedule_id=<?php echo $r['Schedule_ID']; ?>&class=<?php echo urlencode($travel_class); ?>&price=<?php echo $price; ?>"
                               class="btn btn-primary btn-sm">Book Now</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline btn-sm">Login to Book</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-inner">
        <div>
            <h4>PAKISTAN RAILWAYS</h4>
            <p>The national railway company of Pakistan, serving millions since 1861.</p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <p><a href="index.php">Home</a></p>
            <p><a href="timings.php">Train Timings</a></p>
        </div>
        <div>
            <h4>Contact</h4>
            <p>Helpline: 117</p>
            <p>Email: info@pakrailways.gov.pk</p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?php echo date('Y'); ?> Pakistan Railways. All rights reserved.
    </div>
</footer>

</body>
</html>