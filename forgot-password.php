<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

 $error = '';
 $success = '';
 $reset_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM passenger WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("UPDATE passenger SET reset_token = ?, reset_token_expiry = ? WHERE Passenger_ID = ?");
            $stmt->execute([$token, $expiry, $user['Passenger_ID']]);

            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset-password.php?token=" . $token;
            $success = 'A password reset link has been generated.';

            // NOTE: In a production system, $reset_link would be emailed to the user
            // rather than displayed on screen. Local XAMPP environments usually have
            // no working mail server, so the link is shown directly here for demo purposes.
        } else {
            // Same generic message whether or not the email exists, to avoid leaking
            // which emails are registered.
            $success = 'If that email exists in our system, a reset link has been generated.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Pakistan Railways</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card animate-in">
        <div style="text-align:center;margin-bottom:24px">
            <div class="logo-icon" style="margin:0 auto 12px;width:56px;height:56px;font-size:24px;border-radius:14px">PR</div>
        </div>
        <h2>Forgot Password</h2>
        <p class="subtitle">Enter your email to reset your password</p>

        <?php if ($error): ?>
            <div class="alert alert-danger">&#9888; <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">&#10003; <?php echo htmlspecialchars($success); ?></div>

            <?php if ($reset_link): ?>
            <div style="display:block;background:#f0f4ff;border:1px solid #c7d6ff;border-radius:8px;padding:12px 16px;font-size:13px;margin-bottom:16px;color:#1a1a2e;line-height:1.5">
                <strong style="color:#1a1a2e">Demo mode:</strong> No mail server is configured on this local environment, so the reset link is shown here directly instead of being emailed:
                <div style="word-break:break-all;margin-top:8px">
                    <a href="<?php echo htmlspecialchars($reset_link); ?>" style="color:#2563eb"><?php echo htmlspecialchars($reset_link); ?></a>
                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="you@example.com"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>
        <?php endif; ?>

        <div class="auth-footer">
            Remember your password? <a href="login.php">Log in</a>
        </div>
    </div>
</div>

</body>
</html>