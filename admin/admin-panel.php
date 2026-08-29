<?php
require_once 'config.php';

 $message = '';
 $created = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $message = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE Email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $message = 'An admin with this email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admin (Name, Email, Password, Role) VALUES (?, ?, ?, 'Admin')");
            $stmt->execute([$name, $email, $hashed]);
            $created = true;
            $message = 'Admin account created successfully. You can now delete this file for security.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin - Setup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card animate-in">
        <div style="text-align:center;margin-bottom:24px">
            <div class="logo-icon" style="margin:0 auto 12px;width:56px;height:56px;font-size:24px;border-radius:14px">PR</div>
        </div>
        <h2>Create Admin Account</h2>
        <p class="subtitle">One-time setup &mdash; delete this file after use</p>

        <?php if ($message): ?>
            <div class="alert <?php echo $created ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $created ? '&#10003; ' : '&#9888; '; ?><?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!$created): ?>
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Admin Name"
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="admin@example.com"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Choose a strong password" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Admin</button>
        </form>
        <?php else: ?>
            <a href="admin-login.php" class="btn btn-primary" style="width:100%;text-align:center;display:block">Go to Admin Login</a>
        <?php endif; ?>

        
        <div class="auth-footer">
            Already have an admin  account?<a href="admin-login.php">Login</a>
        </div>
    </div>
</div>

</body>
</html>