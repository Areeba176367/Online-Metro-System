<?php
require_once 'config.php';

 $ticket_id = intval($_GET['id'] ?? 0);

// Get full ticket info — fixed column names to match actual DB schema
 $stmt = $pdo->prepare("
    SELECT t.*,
           p.Name AS passenger_name, p.Email, p.Phone,
           tr.Train_Name, tr.Route,
           pay.Method AS payment_method,
           pay.Amount AS paid_amount,
           pay.Payment_Date AS payment_date
    FROM ticket t
    JOIN passenger p  ON t.Passenger_ID = p.Passenger_ID
    JOIN train tr     ON t.Train_ID = tr.Train_ID
    LEFT JOIN payment pay ON pay.Ticket_ID = t.Ticket_ID
    WHERE t.Ticket_ID = ?
");
 $stmt->execute([$ticket_id]);
 $ticket = $stmt->fetch();

if (!$ticket) {
    die("Ticket not found. <a href='index.php'>Go back</a>");
}

// Only the ticket owner can view it
if (isLoggedIn() && $ticket['Passenger_ID'] != $_SESSION['passenger_id']) {
    die("Access denied. <a href='index.php'>Go back</a>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo str_pad($ticket_id, 6, '0', STR_PAD_LEFT); ?> - Pakistan Railways</title>
    <link rel="stylesheet" href="style.css">
    <style>
        @media print {
            .header, .footer, .no-print { display: none !important; }
            body { background: #fff; }
            .ticket-print { box-shadow: none; border: 2px solid #ddd; }
        }
    </style>
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
            <?php endif; ?>
        </ul>
        <div class="nav-auth">
            <?php if (isLoggedIn()): ?>
                <a href="logout.php" class="btn btn-outline btn-sm">Log out</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline btn-sm">Log in</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="main-content">
    <div style="max-width:700px;margin:0 auto">

        <?php if (isset($_GET['paid'])): ?>
            <div class="alert alert-success" style="margin-bottom:20px">
                &#10003; Payment successful! Your ticket has been confirmed.
            </div>
        <?php endif; ?>

        <div class="section-header">
            <h2>Your Ticket</h2>
            <p>Booking confirmation and travel details</p>
        </div>

        <div class="ticket-print">
            <!-- Ticket Header -->
            <div class="ticket-print-header">
                <div>
                    <div style="font-size:12px;color:var(--fg-secondary);text-transform:uppercase;letter-spacing:1px">Pakistan Railways</div>
                    <div style="font-size:18px;font-weight:700;margin-top:4px"><?php echo htmlspecialchars($ticket['Train_Name']); ?></div>
                    <div style="font-size:12px;color:var(--fg-secondary);margin-top:2px"><?php echo htmlspecialchars($ticket['Route']); ?></div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:12px;color:var(--fg-secondary)">Booking ID</div>
                    <div style="font-size:16px;font-weight:700;color:var(--accent)">
                        #TKT-<?php echo str_pad($ticket_id, 6, '0', STR_PAD_LEFT); ?>
                    </div>
                </div>
            </div>

            <!-- Ticket Body -->
            <div class="ticket-print-body">

                <div class="ticket-print-row">
                    <div class="ticket-print-field">
                        <label>Passenger</label>
                        <div class="value"><?php echo htmlspecialchars($ticket['passenger_name']); ?></div>
                    </div>
                    <div class="ticket-print-field">
                        <label>Phone</label>
                        <div class="value"><?php echo htmlspecialchars($ticket['Phone']); ?></div>
                    </div>
                </div>

                <div class="ticket-print-row">
                    <div class="ticket-print-field">
                        <label>Journey Date</label>
                        <div class="value"><?php echo date('d M Y', strtotime($ticket['Date'])); ?></div>
                    </div>
                    <div class="ticket-print-field">
                        <label>Seat No</label>
                        <div class="value" style="font-size:20px;font-weight:800;color:var(--accent)">
                            <?php echo htmlspecialchars($ticket['Seat_No']); ?>
                        </div>
                    </div>
                </div>

                <div class="ticket-print-row">
                    <div class="ticket-print-field">
                        <label>Status</label>
                        <div class="value">
                            <span class="ticket-status status-<?php echo strtolower($ticket['Status']); ?>">
                                <?php echo htmlspecialchars($ticket['Status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="ticket-print-field">
                        <label>Price</label>
                        <div class="value" style="font-size:20px;font-weight:800">
                            PKR <?php echo number_format($ticket['Price']); ?>
                        </div>
                    </div>
                </div>

                <!-- Payment section -->
                <?php if ($ticket['payment_method']): ?>
                <div style="margin-top:16px;padding-top:16px;border-top:1px dashed rgba(255,255,255,0.15)">
                    <div class="ticket-print-row" style="margin-bottom:0">
                        <div class="ticket-print-field">
                            <label>Payment Method</label>
                            <div class="value"><?php echo htmlspecialchars($ticket['payment_method']); ?></div>
                        </div>
                        <div class="ticket-print-field">
                            <label>Payment Date</label>
                            <div class="value">
                                <?php echo $ticket['payment_date']
                                    ? date('d M Y H:i', strtotime($ticket['payment_date']))
                                    : '—'; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div style="margin-top:16px;padding-top:16px;border-top:1px dashed rgba(255,255,255,0.15)">
                    <span style="color:var(--warning,#f59e0b);font-size:13px">
                        &#9888; Payment pending
                    </span>
                    <a href="payment.php?ticket_id=<?php echo $ticket_id; ?>"
                       class="btn btn-primary btn-sm" style="margin-left:12px">Pay Now</a>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Action Buttons -->
        <div class="no-print" style="text-align:center;margin-top:24px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
            <a href="dashboard.php" class="btn btn-primary">My Bookings</a>
            <button onclick="window.print()" class="btn btn-outline">&#128438; Print Ticket</button>
            <a href="index.php" class="btn btn-outline">Book Another</a>
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