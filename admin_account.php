<?php
session_start();

$firstname = isset($_SESSION['firstname']) ? $_SESSION['firstname'] : '';
$lastname = isset($_SESSION['lastname']) ? $_SESSION['lastname'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$UserID = isset($_SESSION['user_id']);

require "database.php";


$sql = "SELECT * FROM user_account_tbl";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin System</title>
    <link id="favicon" rel="icon" type="image/png" href="images/logo.png">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">
    <link href="https://fonts.cdnfonts.com/css/gilroy-bold" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_account.css">
</head>
<body>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="images/logo.png" alt="Logo">
        <span>Admin System</span>
    </div>
    <a href="admin_page.php" class="sidebar-item" id="dashboardItem">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </a>
    <div class="sidebar-item dropdown" id="inventoryDropdown">
        <i class="fas fa-box"></i>
        <span>Inventory</span>
        <i class="fas fa-chevron-down dropdown-icon"></i>
    </div>
    <div class="dropdown-items" id="dropdownItems">
        <a href="admin_inventory.php" class="sidebar-item" id="viewInventory">
            <i class="fas fa-eye"></i>
            <span>View Inventory</span>
        </a>
        <a href="pos.html" class="sidebar-item">
            <i class="fas fa-credit-card"></i>
            <span>POS</span>
        </a>
        <a href="admin_discount.php" class="sidebar-item" id="viewDiscount">
            <i class="fas fa-percent"></i>
            <span>Discount</span>
        </a>
        <a href="admin_archive.php" class="sidebar-item" id="viewArchive">
            <i class="fas fa-archive"></i>
            <span>Archive</span>
        </a>
    </div>
    <a href="admin_account.php" class="sidebar-item active" id="accountsItem">
        <i class="fas fa-user-circle"></i>
        <span>Accounts</span>
    </a>
    <a href="admin_transac.php" class="sidebar-item" id="transactionsItem">
        <i class="fas fa-exchange-alt"></i>
        <span>Transactions</span>
    </a>
    <a href="reports.html" class="sidebar-item" id="reportsItem">
        <i class="fas fa-chart-line"></i>
        <span>Reports</span>
    </a>

    <div class="user-profile">
        <div class="profile-img-container">
        <img src="path_to_user_image.jpg" alt="User Profile" class="profile-img" onerror="this.onerror=null; 
        this.src='default-profile.png';">
        <div class="profile-img-no-image">
            <span class="profile-initials"><?php echo htmlspecialchars($lastname)?></span> <!-- You can dynamically insert initials here -->
        </div>
        </div>
        <div class="user-info">
            <p class="user-name"><?php echo htmlspecialchars($firstname) . " " . htmlspecialchars($lastname); ?></p>
            <p class="user-role"><?php echo htmlspecialchars($role)?></p>
        </div>
        <button class="btn btn-primary open-profile-btn" onclick="openProfile()">Open Profile</button>
    </div>

</div>

<button class="toggle-btn" id="toggleBtn">
    <i class="fas fa-chevron-left"></i>
</button>

<div class="content" id="content">
<div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; margin-top: 30px;">
    <div style="display: flex; align-items: center;">
        <h2 style="font-family: 'Gilroy-Bold', sans-serif; margin-left: 15px;">User Accounts</h2>
    </div>
    <button 
        class="btn btn-primary" 
        style="font-family: 'Gilroy-Bold', sans-serif; margin-right: 15px; background-color: #604be8;" 
        data-bs-toggle="modal" 
        data-bs-target="#addUserModal">
        Add User
    </button>
</div>
<div style="display: flex; align-items: center; margin-left: 15px;">
    <form style="margin: 0; display: flex; align-items: center; position: relative;">
        <i class="fas fa-search" 
            style="
                position: absolute; 
                left: 10px; 
                top: 50%; 
                transform: translateY(-50%); 
                color: #604be8;
            ">
        </i>
        <input 
            type="text" 
            placeholder="Search name, role, email, etc." 
            id="searchBar"
            style="
                padding: 10px 10px 10px 40px; 
                border: 1px solid #fff; 
                border-radius: 10px; 
                outline: none; 
                width: 350px;
                font-family: 'Gilroy', sans-serif;
            "
            onkeyup="searchUser()"
        >
    </form>
</div>
<div style="margin: 15px;">
    <table class="table table-striped" style="background-color: white;" id="useraccount_table">
        <thead style="background-color: #06d6a0; color: white;">
            <tr>
                <th scope="col">Full Name</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Birth Date</th>
                <th scope="col">Contact Number</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    $status = $UserID ? "Active" : "Inactive";
                    
                    echo "<tr data-user-id='" . $row["UserID"] . "'>";
                    echo "<td>" . $row["firstname"] . " " . $row["lastname"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    echo "<td>" . $row["role"] . "</td>";
                    echo "<td>" . $row["birthdate"] . "</td>";
                    echo "<td>" . $row["contact_number"] . "</td>";
                    echo "<td>" . htmlspecialchars($status) . "</td>";
                    echo "<td class='text-center'>
                        <button class='btn btn-success btn-sm' title='Edit' style='margin-right: 8px;'>
                            <i class='fas fa-edit'></i>
                        </button>
                        <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteModal' title='Delete'  data-user-id='" . $row['UserID'] . "'>
                            <i class='fas fa-trash'></i>
                        </button>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No items found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addUserForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <small id="emailFeedback" class="text-danger" style="display: none;"></small>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="" disabled selected>Role</option>
                            <option value="Admin">Admin</option>
                            <option value="Manager">Manager</option>
                            <option value="Inventory Staff">Inventory Staff</option>
                            <option value="Cashier">Cashier</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">Birth Date</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" required onblur="checkAge()">
                        <small id="birthdateFeedback" class="text-danger" style="display: none;"></small>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
<script src="js/search_bar2.js"></script>
<script src="js/header.js"></script>
<script src="js/admin_account.js"></script>
</html>
