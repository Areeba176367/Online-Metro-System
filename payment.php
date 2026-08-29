<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

 $ticket_id = intval($_GET['ticket_id'] ?? 0);

if (!$ticket_id) {
    redirect('dashboard.php');
}

// Get ticket details with train info
 $stmt = $pdo->prepare("
    SELECT t.*, tr.Train_Name, tr.Route
    FROM ticket t
    JOIN train tr ON t.Train_ID = tr.Train_ID
    WHERE t.Ticket_ID = ? AND t.Passenger_ID = ?
");
 $stmt->execute([$ticket_id, $_SESSION['passenger_id']]);
 $ticket = $stmt->fetch();

if (!$ticket) {
    redirect('dashboard.php');
}

// Check if already paid
 $stmt = $pdo->prepare("SELECT * FROM payment WHERE Ticket_ID = ?");
 $stmt->execute([$ticket_id]);
 $existing_payment = $stmt->fetch();

if ($existing_payment) {
    redirect('ticket.php?id=' . $ticket_id);
}

 $error = '';
 $success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['payment_method'] ?? '';

    if (empty($method)) {
        $error = 'Please select a payment method.';
    } elseif (!in_array($method, ['Cash', 'EasyPaisa'])) {
        $error = 'Invalid payment method.';
    } else {
        try {
            $pdo->beginTransaction();

            // Generate transaction ID
            $transaction_id = strtoupper(uniqid('TXN-'));

            // Insert payment record
            $stmt = $pdo->prepare("
                INSERT INTO payment (Ticket_ID, Method, Amount, Status, Payment_Date, Transaction_ID)
                VALUES (?, ?, ?, 'Completed', NOW(), ?)
            ");
            $stmt->execute([
                $ticket_id,
                $method,
                $ticket['Price'],
                $transaction_id
            ]);

            $pdo->commit();
            redirect('ticket.php?id=' . $ticket_id . '&paid=1');

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Payment failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Pakistan Railways</title>
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
            <li><a href="dashboard.php">My Bookings</a></li>
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
            <h2>Complete Payment</h2>
            <p>Confirm your booking by completing the payment below</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">&#9888; <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Ticket Summary -->
        <div class="payment-summary">
            <div class="summary-row">
                <span>Ticket</span>
                <span style="color:var(--fg);font-weight:600">
                    #TKT-<?php echo str_pad($ticket['Ticket_ID'], 6, '0', STR_PAD_LEFT); ?>
                </span>
            </div>
            <div class="summary-row">
                <span>Train</span>
                <span style="color:var(--fg);font-weight:600"><?php echo htmlspecialchars($ticket['Train_Name']); ?></span>
            </div>
            <div class="summary-row">
                <span>Route</span>
                <span style="color:var(--fg-secondary);font-size:13px"><?php echo htmlspecialchars($ticket['Route']); ?></span>
            </div>
            <div class="summary-row">
                <span>Date</span>
                <span style="color:var(--fg);font-weight:600"><?php echo date('d M Y', strtotime($ticket['Date'])); ?></span>
            </div>
            <div class="summary-row">
                <span>Seat</span>
                <span style="color:var(--fg);font-weight:600"><?php echo htmlspecialchars($ticket['Seat_No']); ?></span>
            </div>
            <div class="summary-row">
                <span>Status</span>
                <span style="color:var(--accent);font-weight:600"><?php echo htmlspecialchars($ticket['Status']); ?></span>
            </div>
            <div class="summary-row total">
                <span>Amount to Pay</span>
                <span>PKR <?php echo number_format($ticket['Price']); ?></span>
            </div>
        </div>

        <!-- Payment Method Form -->
        <div class="search-card" style="margin-top:24px">
            <h2>&#128179; Choose Payment Method</h2>
            <form method="POST" class="auth-form">

                <div class="form-group">
                    <label>Payment Method</label>
                    <div style="display:flex;gap:12px;margin-top:8px;flex-wrap:wrap">

                        <!-- Cash Option -->
                        <label style="flex:1;min-width:140px;cursor:pointer">
                            <input type="radio" name="payment_method" value="Cash"
                                   <?php echo ($_POST['payment_method'] ?? '') === 'Cash' ? 'checked' : ''; ?>
                                   class="pay-radio">
                            <div class="pay-option" >
                                <div style="font-size:28px;margin-bottom:6px">&#128181;</div>
                                <div style="font-weight:600;font-size:14px">Cash</div>
                                <div style="font-size:12px;color:var(--fg-secondary);margin-top:4px">Pay at counter</div>
                            </div>
                        </label>

                        <!-- EasyPaisa Option -->
                        <label style="flex:1;min-width:140px;cursor:pointer">
                            <input type="radio" name="payment_method" value="EasyPaisa"
                                   <?php echo ($_POST['payment_method'] ?? '') === 'EasyPaisa' ? 'checked' : ''; ?>
                                   class="pay-radio">
                            <div class="pay-option" >
                                <div style="font-size:28px;margin-bottom:6px">📱</div>
                                <div style="font-weight:600;font-size:14px">EasyPaisa</div>
                                <div style="font-size:12px;color:var(--fg-secondary);margin-top:4px">Mobile wallet</div>
                            </div>
                        </label>

                    </div>
                </div>

                <div id="easypasisa-note" style="<?php echo (($_POST['payment_method'] ?? '')=='EasyPaisa')?'':'display:none;'; ?>background:rgba(0,180,100,0.08);border:1px solid rgba(0,180,100,0.2);border-radius:8px;padding:12px 16px;font-size:13px;margin-bottom:16px">
                    &#128241; EasyPaisa: Send <strong>PKR <?php echo number_format($ticket['Price']); ?></strong>
                    to <strong>0300-1234567</strong> and click Pay Now to confirm.
                </div>

                <button type="submit" class="btn btn-primary btn-lg"
                        style="width:100%;justify-content:center;margin-top:8px">
                    &#10003; Pay PKR <?php echo number_format($ticket['Price']); ?> Now
                </button>

            </form>
        </div>

        <div style="text-align:center;margin-top:16px">
            <a href="dashboard.php" style="font-size:13px;color:var(--fg-secondary)">&#8592; Back to My Bookings</a>
        </div>

    </div>
</div>

<footer class="footer">
    <div class="footer-bottom" style="margin-top:0">
        &copy; <?php echo date('Y'); ?> Pakistan Railways. All rights reserved.
    </div>
</footer>

<style>
.pay-option {
    border: 2px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    transition: all 0.2s;
}
.pay-option:hover {
    border-color: var(--accent);
}
.pay-radio{position:absolute;opacity:0;}
.pay-radio:checked + .pay-option {
    border-color: var(--accent);
    background: rgba(0,200,100,0.08);
}
</style>
</body>
</html>