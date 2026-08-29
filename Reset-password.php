<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

 $error = '';
 $success = false;
 $show_form = false;
 $token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    $error = 'Invalid or missing reset link.';
} else {
    $stmt = $pdo->prepare("SELECT * FROM passenger WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'This reset link is invalid or has already been used.';
    } elseif (strtotime($user['reset_token_expiry']) < time()) {
        $error = 'This reset link has expired. Please request a new one.';
    } else {
        $show_form = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($password) || empty($confirm_password)) {
                $error = 'Please fill in both password fields.';
            } elseif ($password !== $confirm_password) {
                $error = 'Passwords do not match.';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters long.';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE passenger SET Password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE Passenger_ID = ?");
                $stmt->execute([$hashed, $user['Passenger_ID']]);
                $success = true;
                $show_form = false;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Pakistan Railways</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card animate-in">
        <div style="text-align:center;margin-bottom:24px">
            <div class="logo-icon" style="margin:0 auto 12px;width:56px;height:56px;font-size:24px;border-radius:14px">PR</div>
        </div>
        <h2>Reset Password</h2>
        <p class="subtitle">Enter your new password below</p>

        <?php if ($error): ?>
            <div class="alert alert-danger">&#9888; <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">&#10003; Your password has been reset successfully.</div>
            <a href="login.php?reset=1" class="btn btn-primary" style="width:100%;text-align:center;display:block;margin-top:16px">Go to Login</a>
        <?php elseif ($show_form): ?>
            <form method="POST" class="auth-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                </div>
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
        <?php endif; ?>

        <?php if (!$success): ?>
        <div class="auth-footer">
            <a href="login.php">Back to login</a>
            </div>
                <?php endif; ?>
            </div>
        </div>

</body>
</html>