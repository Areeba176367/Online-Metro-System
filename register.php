<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

 $error = '';
 $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $cnic = trim($_POST['cnic'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($phone) || empty($cnic) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif (!preg_match('/^\d{5}-\d{7}-\d{1}$/', $cnic)) {
        $error = 'CNIC must be in the format XXXXX-XXXXXXX-X.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT Passenger_ID FROM passenger WHERE Email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        } else {
            // Check if CNIC exists
            $stmt = $pdo->prepare("SELECT Passenger_ID FROM passenger WHERE CNIC = ?");
            $stmt->execute([$cnic]);
            if ($stmt->fetch()) {
                $error = 'This CNIC is already registered.';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO passenger (Name, Email, Phone, CNIC, Password, Register_Date) VALUES (?, ?, ?, ?, ?, NOW())");
                if ($stmt->execute([$name, $email, $phone, $cnic, $hashed])) {
                    redirect('login.php?registered=1');
                } else {
                    $error = 'Registration failed. Please try again.';
                }
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
    <title>Register - Pakistan Railways</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card animate-in">
        <div style="text-align:center;margin-bottom:24px">
            <div class="logo-icon" style="margin:0 auto 12px;width:56px;height:56px;font-size:24px;border-radius:14px">PR</div>
        </div>
        <h2>Create Account</h2>
        <p class="subtitle">Sign up to book train tickets online</p>

        <?php if ($error): ?>
            <div class="alert alert-danger">&#9888; <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Muhammad Ali"
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="you@example.com"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" class="form-control" placeholder="03XX-XXXXXXX"
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>CNIC</label>
                <input type="text" name="cnic" class="form-control" placeholder="XXXXX-XXXXXXX-X"
                value="<?php echo htmlspecialchars($_POST['cnic'] ?? ''); ?>"
                pattern="\d{5}-\d{7}-\d{1}" maxlength="15" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Account</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
</div>

</body>
</html>