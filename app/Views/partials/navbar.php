<?php
session_start();
$role = $_SESSION['role'] ?? 'guest';
?>

<nav>
  <ul>
    <li><a href="index.php">Home</a></li>
    <?php if ($role === 'guest'): ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    <?php endif; ?>

    <?php if ($role === 'staff'): ?>
        <li><a href="bookings.php">Manage Bookings</a></li>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
        <li><a href="dashboard.php">Admin Dashboard</a></li>
        <li><a href="users.php">User Management</a></li>
    <?php endif; ?>

    <?php if ($role !== 'guest'): ?>
        <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
  </ul>
</nav>
