<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

 $schedule_id  = intval($_GET['schedule_id'] ?? 0);
 $travel_class = $_GET['class'] ?? 'Economy';
 $price        = floatval($_GET['price'] ?? 500);

// Get schedule + train + station info
 $schedule = null;
if ($schedule_id) {
    $stmt = $pdo->prepare("
        SELECT s.*, t.Train_Name, t.Route, t.TotalSeats, t.Train_ID,
               st.Station_Name
        FROM schedule s
        JOIN train   t  ON s.Train_ID   = t.Train_ID
        JOIN station st ON s.Station_ID = st.Station_ID
        WHERE s.Schedule_ID = ?
    ");
    $stmt->execute([$schedule_id]);
    $schedule = $stmt->fetch();
}

if (!$schedule) {
    die("Invalid schedule. <a href='index.php'>Go back</a>");
}

// Get already-booked seats for this train on this date
 $booked_seats = [];
 $stmt = $pdo->prepare("
    SELECT Seat_No FROM ticket
    WHERE Train_ID = ? AND Date = ? AND Status = 'Booked'
");
 $stmt->execute([$schedule['Train_ID'], $schedule['Date']]);
foreach ($stmt->fetchAll() as $row) {
    $booked_seats[] = $row['Seat_No'];
}

 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seat_no = strtoupper(trim($_POST['seat_number'] ?? ''));

    if (empty($seat_no)) {
        $error = 'Please enter a seat number.';
    } elseif (!preg_match('/^\d{1,2}[A-D]$/', $seat_no)) {
        $error = 'Invalid format. Use number + letter, e.g. 12A, 5B.';
    } elseif (in_array($seat_no, $booked_seats)) {
        $error = 'Seat ' . htmlspecialchars($seat_no) . ' is already booked. Please choose another.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO ticket (Passenger_ID, Train_ID, Date, Seat_No, Price, Status)
                VALUES (?, ?, ?, ?, ?, 'Booked')
            ");
            $stmt->execute([
                $_SESSION['passenger_id'],
                $schedule['Train_ID'],
                $schedule['Date'],
                $seat_no,
                $price
            ]);
            $ticket_id = $pdo->lastInsertId();

            $pdo->commit();
            redirect('payment.php?ticket_id=' . $ticket_id);

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Booking failed: ' . $e->getMessage();
        }
    }
}

// Generate available seat suggestion
 $suggested_seat = '';
for ($n = 1; $n <= 40; $n++) {
    foreach (['A','B','C','D'] as $l) {
        $s = $n . $l;
        if (!in_array($s, $booked_seats)) {
            $suggested_seat = $s;
            break 2;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ticket - Pakistan Railways</title>
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
            <li><a href="dashboard.php" class="active">My Bookings</a></li>
        </ul>
        <div class="nav-auth">
            <span style="font-size:13px;color:var(--fg-secondary)"><?php echo htmlspecialchars($_SESSION['passenger_name']); ?></span>
            <a href="logout.php" class="btn btn-outline btn-sm">Log out</a>
        </div>
    </div>
</header>

<div class="main-content">
    <div class="payment-container">
        <div class="section-header">
            <h2>Book Your Ticket</h2>
            <p>Review journey details and confirm your seat</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">&#9888; <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Journey Summary -->
        <div class="payment-summary">
            <div class="summary-row">
                <span>Train</span>
                <span style="color:var(--fg);font-weight:600"><?php echo htmlspecialchars($schedule['Train_Name']); ?></span>
            </div>
            <div class="summary-row">
                <span>Route</span>
                <span style="color:var(--fg-secondary);font-size:13px"><?php echo htmlspecialchars($schedule['Route']); ?></span>
            </div>
            <div class="summary-row">
                <span>Departure Station</span>
                <span style="color:var(--fg);font-weight:600"><?php echo htmlspecialchars($schedule['Station_Name']); ?></span>
            </div>
            <div class="summary-row">
                <span>Date</span>
                <span style="color:var(--fg);font-weight:600"><?php echo date('d M Y', strtotime($schedule['Date'])); ?></span>
            </div>
            <div class="summary-row">
                <span>Departure</span>
                <span style="color:var(--fg);font-weight:600"><?php echo date('H:i', strtotime($schedule['Departure_Time'])); ?></span>
            </div>
            <div class="summary-row">
                <span>Arrival</span>
                <span style="color:var(--fg);font-weight:600"><?php echo date('H:i', strtotime($schedule['Arrival_Time'])); ?></span>
            </div>
            <div class="summary-row">
                <span>Class</span>
                <span style="color:var(--accent);font-weight:600"><?php echo htmlspecialchars($travel_class); ?></span>
            </div>
            <div class="summary-row total">
                <span>Total Price</span>
                <span>PKR <?php echo number_format($price); ?></span>
            </div>
        </div>

        <!-- Already booked seats warning -->
        <?php if (!empty($booked_seats)): ?>
        <div class="alert" style="background:rgba(255,180,0,0.08);border:1px solid rgba(255,180,0,0.3);border-radius:8px;padding:10px 16px;font-size:13px;margin-bottom:16px;color:var(--fg-secondary)">
            &#9888; Already booked seats for this train on this date:
            <strong style="color:var(--fg)"><?php echo implode(', ', $booked_seats); ?></strong>
        </div>
        <?php endif; ?>

        <!-- Seat Selection Form -->
        <div class="search-card">
            <h2>&#128186; Select Your Seat</h2>
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label>Seat Number</label>
                    <input type="text" name="seat_number" class="form-control"
                           placeholder="e.g. <?php echo $suggested_seat ?: '12A'; ?>"
                           value="<?php echo htmlspecialchars($_POST['seat_number'] ?? ''); ?>"
                           maxlength="3" required>
                    <span style="font-size:12px;color:var(--fg-secondary);margin-top:4px;display:block">
                        Format: number + letter (e.g. 12A, 5B, 3C).
                        <?php if ($suggested_seat): ?>
                            First available: <strong><?php echo $suggested_seat; ?></strong>
                        <?php endif; ?>
                    </span>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center">
                    Confirm Booking &rarr;
                </button>
            </form>
        </div>

        <div style="text-align:center;margin-top:16px">
            <a href="javascript:history.back()" style="font-size:13px;color:var(--fg-secondary)">&#8592; Back to search results</a>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-bottom" style="margin-top:0">
        &copy; <?php echo date('Y'); ?> Pakistan Railways. All rights reserved.
    </div>
</footer>

</body>
</html>