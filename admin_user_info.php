<?php
session_start();
include('connect.php');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}


if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    
    try {
        // Start transaction
        $conn->begin_transaction();

        
        $delete_user = "DELETE FROM users WHERE user_id = ?";
        $stmt_user = $conn->prepare($delete_user);
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();

        $conn->commit();

  
        echo "<script>alert('User deleted successfully!');</script>";
    } catch (Exception $e) {
        
        $conn->rollback();
        echo "<script>alert('Error deleting user: " . $e->getMessage() . "');</script>";
    }
}


if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    try {
       
        $conn->begin_transaction();

      
        $update_user = "UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?";
        $stmt_user = $conn->prepare($update_user);
        $stmt_user->bind_param("sssi", $username, $email, $role, $user_id);
        $stmt_user->execute();


        $conn->commit();


        echo "<script>alert('User updated successfully!');</script>";
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo "<script>alert('Error updating user: " . $e->getMessage() . "');</script>";
    }
}


$query = "SELECT user_id, username, email FROM users ORDER BY user_id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 220px;
            padding: 20px;
            background-color: #fff;
        }

        .users-header h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        /* Table Styles */
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .users-table th, .users-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .users-table th {
            background-color: #4CAF50;
            color: white;
            font-size: 14px;
        }

        .users-table td {
            font-size: 14px;
        }

        /* Action Buttons */
        .edit-btn, .delete-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .edit-btn:hover, .delete-btn:hover {
            background-color: #0056b3;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }

        .modal-buttons {
            margin-top: 20px;
        }

        .modal-buttons button {
            margin: 0 10px;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .confirm-delete {
            background-color: #dc3545;
            color: white;
        }

        .cancel-delete {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar and Navigation -->
    <div class="main-content">
        <div class="users-header">
            <h1>Manage Users</h1>
        </div>

        <div class="users-container">
            <table class="users-table" id="usersTable">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($row['created_at']))); ?></td>
                        <td>
                            <button class="edit-btn" onclick="editUser(<?php echo $row['user_id']; ?>)">Edit</button>
                            <button class="delete-btn" onclick="confirmDelete(<?php echo $row['user_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit User</h3>
            <form id="editForm" method="POST">
                <input type="hidden" name="user_id" id="editUserId">
                <label for="username">Username:</label>
                <input type="text" name="username" id="editUsername" required><br><br>
                <label for="email">Email:</label>
                <input type="email" name="email" id="editEmail" required><br><br>
                <label for="role">Role:</label>
                <select name="role" id="editRole" required>
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                </select><br><br>
                <button type="submit" name="update_user" class="confirm-delete">Update</button>
                <button type="button" class="cancel-delete" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal for Deletion -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this user?</p>
            <div class="modal-buttons">
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <button type="submit" name="delete_user" class="confirm-delete">Yes, Delete</button>
                    <button type="button" class="cancel-delete" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editUser(userId) {
            // Fetch user data from the backend (this can be done via AJAX)
            // For simplicity, we set data manually here
            document.getElementById('editUserId').value = userId;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('deleteModal').style.display = 'none';
        }

        function confirmDelete(userId) {
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteModal').style.display = 'block';
        }
    </script>
</body>
</html>
