<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM passenger WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['passenger_id'] = $user['Passenger_ID'];
            $_SESSION['passenger_name'] = $user['Name'];
            $_SESSION['passenger_email'] = $user['Email'];
            redirect('dashboard.php');
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pakistan Railways</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card animate-in">
        <div style="text-align:center;margin-bottom:24px">
            <div class="logo-icon" style="margin:0 auto 12px;width:56px;height:56px;font-size:24px;border-radius:14px">PR</div>
        </div>
        <h2>Welcome Back</h2>
        <p class="subtitle">Log in to manage your bookings</p>

        <?php if ($error): ?>
            <div class="alert alert-danger">&#9888; <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">&#10003; Registration successful! Please log in.</div>
        <?php endif; ?>

        <?php if (isset($_GET['reset'])): ?>
            <div class="alert alert-success">&#10003; Password reset successful! Please log in with your new password.</div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="you@example.com"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                <div style="text-align:right;margin-top:6px">
                    <a href="forgot-password.php" style="font-size:13px">Forgot password?</a>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Log in</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Sign up</a>
        </div>
    </div>
</div>

</body>
</html>