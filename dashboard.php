<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

 $passenger_id = $_SESSION['passenger_id'];

// Get passenger info
 $stmt = $pdo->prepare("SELECT * FROM passenger WHERE Passenger_ID = ?");
 $stmt->execute([$passenger_id]);
 $passenger = $stmt->fetch();

// Get all tickets for this passenger
 $stmt = $pdo->prepare("
    SELECT t.*, s.Departure_Time, s.Arrival_Time, st.Station_Name, tr.Train_Name,
           pay.Method AS payment_method
    FROM ticket t
    JOIN train tr ON t.Train_ID = tr.Train_ID
    JOIN schedule s ON s.Train_ID = t.Train_ID AND s.Date = t.Date
    JOIN station st ON s.Station_ID = st.Station_ID
    LEFT JOIN payment pay ON pay.Ticket_ID = t.Ticket_ID
    WHERE t.Passenger_ID = ?
    ORDER BY t.Ticket_ID DESC
");
 $stmt->execute([$passenger_id]);
 $tickets = $stmt->fetchAll();

// Stats
 $total_tickets = count($tickets);
 $active_tickets = count(array_filter($tickets, fn($t) => $t['Status'] === 'Booked'));
 $total_spent = array_sum(array_map(fn($t) => $t['Status'] === 'Booked' ? $t['Price'] : 0, $tickets));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings </title>
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
            <li><a href="timings.php">Train Timings</a></li>
            <li><a href="dashboard.php" class="active">My Bookings</a></li>
        </ul>
        <div class="nav-auth">
            <span style="font-size:13px;color:var(--fg-secondary)"><?php echo htmlspecialchars($passenger['Name']); ?></span>
            <a href="logout.php" class="btn btn-outline btn-sm">Log out</a>
        </div>
    </div>
</header>

<div class="main-content">
    <div class="section-header">
        <h2>My Dashboard</h2>
        <p>Manage your bookings and account</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_tickets; ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $active_tickets; ?></div>
            <div class="stat-label">Active Tickets</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">PKR <?php echo number_format($total_spent); ?></div>
            <div class="stat-label">Total Spent</div>
        </div>
    </div>

    <!-- Tickets -->
    <div class="section-header">
        <h2 style="font-size:20px">Booking History</h2>
    </div>

    <div class="ticket-list">
        <?php if (empty($tickets)): ?>
            <div class="no-results">
                <div class="nr-icon">&#128646;</div>
                <h3>No Bookings Yet</h3>
                <p>You haven't booked any tickets yet. Start by searching for trains.</p>
                <a href="index.php" class="btn btn-primary" style="margin-top:16px">Search Trains</a>
            </div>
        <?php else: ?>
            <?php foreach ($tickets as $t): ?>
                <div class="ticket-item">
                    <div>
                        <div class="ticket-header">
                            <span class="ticket-id-badge">#TKT-<?php echo str_pad($t['Ticket_ID'], 6, '0', STR_PAD_LEFT); ?></span>
                            <span class="ticket-status status-<?php echo strtolower($t['Status']); ?>">
                                <?php echo htmlspecialchars($t['Status']); ?>
                            </span>
                        </div>
                        <div class="route-display">
                            <strong><?php echo htmlspecialchars($t['Station_Name']); ?></strong>
                            <span class="arrow">&#8594;</span>
                            <strong><?php echo htmlspecialchars($t['Train_Name']); ?></strong>
                        </div>
                        <div class="ticket-meta">
                            <?php echo date('d M Y', strtotime($t['Date'])); ?> 
                            &bull; Dep: <?php echo date('H:i', strtotime($t['Departure_Time'])); ?>
                            &bull; Seat: <?php echo htmlspecialchars($t['Seat_No']); ?>
                            <?php if ($t['payment_method']): ?>
                                &bull; Paid via <?php echo htmlspecialchars($t['payment_method']); ?>
                            <?php else: ?>
                                &bull; <span style="color:var(--warning)">Payment pending</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div class="ticket-price">PKR <?php echo number_format($t['Price']); ?></div>
                        <div style="margin-top:8px;display:flex;gap:8px;flex-direction:column">
                            <a href="ticket.php?id=<?php echo $t['Ticket_ID']; ?>" class="btn btn-primary btn-sm">View</a>
                            <?php if (!$t['payment_method'] && $t['Status'] === 'Booked'): ?>
                                <a href="payment.php?ticket_id=<?php echo $t['Ticket_ID']; ?>" class="btn btn-outline btn-sm">Pay Now</a>
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
