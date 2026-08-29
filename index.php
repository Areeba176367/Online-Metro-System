<?php
require_once 'config.php';
require_once 'notif_helper.php';

$stmt = $pdo->query("SELECT * FROM station ORDER BY Station_Name");
$stations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Metro System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute;
            inset: 0;
            background-image: url('background.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 0;
        }
        .hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(10, 15, 13, 0.65);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 120px 2rem 60px;
            max-width: 800px;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="header-inner">
        <a href="index.php" class="logo" style="text-decoration:none">
            <div class="logo-icon">PR</div>
            <div class="logo-text">
                <span class="main">Metro system</span>
                <span class="sub">EASYWAY</span>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="timings.php">Train Timings</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="dashboard.php">My Bookings</a></li>
                <li>
                    <a href="notifications.php">
                        &#128276; Notifications
                        <?php if ($notif_unread > 0): ?>
                            <span style="background:#22c55e;color:#000;border-radius:50%;padding:1px 6px;font-size:11px;font-weight:700;margin-left:4px">
                                <?php echo $notif_unread; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="nav-auth">
            <?php if (isLoggedIn()): ?>
                <span style="font-size:13px;color:var(--fg-secondary)">
                    <?php echo htmlspecialchars($_SESSION['passenger_name']); ?>
                </span>
                <a href="logout.php" class="btn btn-outline btn-sm">Log out</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline btn-sm">Log in</a>
                <a href="register.php" class="btn btn-primary btn-sm">Sign up</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content animate-in">
        <div class="hero-badge">National Railway Service</div>
        <h1>Travel Across <span>Pakistan</span> with Ease</h1>
        <p>Book your train tickets online and enjoy a comfortable journey across Pakistan's extensive railway network.</p>
        <div class="search-card">
            <h2>&#128646; Find Your Train</h2>
            <form action="search.php" method="GET">
                <div class="search-grid">
                    <div class="form-group">
                        <label for="from_station">From</label>
                        <select name="from_station" id="from_station" class="form-control" required>
                            <option value="">Select Departure</option>
                            <?php foreach ($stations as $s): ?>
                                <option value="<?php echo $s['Station_ID']; ?>">
                                    <?php echo htmlspecialchars($s['Station_Name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="to_station">To</label>
                        <select name="to_station" id="to_station" class="form-control" required>
                            <option value="">Select Destination</option>
                            <?php foreach ($stations as $s): ?>
                                <option value="<?php echo $s['Station_ID']; ?>">
                                    <?php echo htmlspecialchars($s['Station_Name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="travel_date">Date</label>
                        <input type="date" name="travel_date" id="travel_date" class="form-control"
                               value="<?php echo date('Y-m-d'); ?>"
                               min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="travel_class">Class</label>
                        <select name="travel_class" id="travel_class" class="form-control">
                            <option value="Economy">Economy</option>
                            <option value="Business">Business</option>
                            <option value="AC Sleeper">AC Sleeper</option>
                            <option value="First Class">First Class</option>
                        </select>
                    </div>
                </div>
                <div class="search-actions">
                    <button type="submit" class="btn btn-primary btn-lg">&#128269; Search Trains</button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="features">
    <div class="section-header" style="text-align:center">
        <h2>Why Choose Pakistan Railways</h2>
        <p>Comfortable, affordable, and reliable travel across the nation</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">&#128646;</div>
            <h3>Extensive Network</h3>
            <p>Connect to 15+ major cities across Pakistan with our comprehensive railway network covering the entire nation.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#128176;</div>
            <h3>Affordable Prices</h3>
            <p>Enjoy budget-friendly travel without compromising on comfort. Multiple class options for every budget.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#9201;</div>
            <h3>Real-time Tracking</h3>
            <p>Check live train schedules and never miss your ride with up-to-date arrival and departure information.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#128274;</div>
            <h3>Secure Booking</h3>
            <p>Your transactions and personal data are protected with industry-standard security measures.</p>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="footer-inner">
        <div>
            <h4>PAKISTAN RAILWAYS</h4>
            <p>The national railway company of Pakistan, serving millions of passengers since 1861.</p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <p><a href="index.php">Home</a></p>
            <p><a href="timings.php">Train Timings</a></p>
            <p><a href="login.php">Login</a></p>
            <p><a href="register.php">Register</a></p>
            <p><a href="admin-login.php">Admin Login</a></p>
        </div>
        <div>
            <h4>Contact</h4>
            <p>Helpline: 117</p>
            <p>Email: info@pakrailways.gov.pk</p>
            <p>Islamabad, Pakistan</p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?php echo date('Y'); ?> Pakistan Railways. All rights reserved.
    </div>
</footer>

</body>
</html>