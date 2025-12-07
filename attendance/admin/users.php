<?php
session_start();

// OPTIONAL: Block if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../pages/login.php");
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "attendance_db");
if ($mysqli->connect_error) {
    die("DB connection failed: " . $mysqli->connect_error);
}

/* -----------------------------------------------------
   DELETE USER
----------------------------------------------------- */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: users.php");
    exit;
}

/* -----------------------------------------------------
   UPDATE USER
----------------------------------------------------- */
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $username = $_POST['username'];
    $password = trim($_POST['password']);

    if (!empty($password)) {
        // New password entered → hash it
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("UPDATE users SET username=?, password=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $hashed, $id);
    } else {
        // Username only update
        $stmt = $mysqli->prepare("UPDATE users SET username=? WHERE id=?");
        $stmt->bind_param("si", $username, $id);
    }

    $stmt->execute();
    header("Location: users.php");
    exit;
}

$users = $mysqli->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html> 
   
<head>
   
    <title>Manage Users</title>
    <link rel="stylesheet" href="..\styles\users_style.css">
 
</head>
<body>

<h2 style="text-align:center;">Admin Panel - Manage Users</h2>
<a href="..\admin\index-admin.php" class="back-btn">🔙 Dashboard</a>
<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Password (Hashed)</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['password']) ?></td>
            <td>
                <a href="users.php?edit=<?= $row['id'] ?>"><button class="edit">Edit</button></a>
                <a href="users.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this user?')">
                    <button class="delete">Delete</button>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php if (isset($_GET['edit'])):
    $id = intval($_GET['edit']);
    $user = $mysqli->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();
?>
<div class="form-container">
    <h3>Edit User</h3>

    <form method="POST" action="users.php">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">

        <label>Username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>

        <label>New Password (leave empty to keep old):</label><br>
        <input type="password" name="password" placeholder="Enter new password"><br><br>

        <button type="submit" name="update">Save Changes</button>
    </form>
</div>
<?php endif; ?>

</body>
</html>
