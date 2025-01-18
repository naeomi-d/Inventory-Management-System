<?php
session_start(); 
include('connection.php'); // This includes your connection.php file

try {
    // Query to get the total number of categories
    $stmt_categories = $conn->query("SELECT COUNT(*) AS total_categories FROM categories");
    $categories_count = $stmt_categories->fetch(PDO::FETCH_ASSOC)['total_categories'];

    // Query to get the total number of orders
    $stmt_orders = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
    $orders_count = $stmt_orders->fetch(PDO::FETCH_ASSOC)['total_orders'];

    // Query to get the total number of users
    $stmt_users = $conn->query("SELECT COUNT(*) AS total_users FROM users");
    $users_count = $stmt_users->fetch(PDO::FETCH_ASSOC)['total_users'];

    // Query to get the total number of products
    $stmt_products = $conn->query("SELECT COUNT(*) AS total_products FROM products");
    $products_count = $stmt_products->fetch(PDO::FETCH_ASSOC)['total_products'];

    // Query to get the total number of suppliers (optional, if needed)
    $stmt_suppliers = $conn->query("SELECT COUNT(*) AS total_suppliers FROM supplier");
    $supplier_count = $stmt_suppliers->fetch(PDO::FETCH_ASSOC)['total_suppliers'];

} catch (PDOException $e) {
    // In case of an error, display the error message
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management Dashboard</title>

    <!-- Link to Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('https://as1.ftcdn.net/v2/jpg/08/85/99/74/1000_F_885997431_Se5USf7dUYSSgVJQ4aeqykrK8I0XByvi.jpg');
            background-size: cover;
            background-position: center;
            color: #fff;
            overflow-x: hidden;
        }

        header {
            text-align: center;
            padding: 30px 0;
            background-color: rgba(0, 0, 0, 0.6);
            border-bottom: 3px solid #fff;
        }

        h1 {
            font-size: 36px;
            margin: 0;
        }

        p {
            font-size: 18px;
            margin-top: 10px;
        }

        .sidebar {
            width: 0;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #333;
            padding-top: 20px;
            height: 100vh;
            overflow-x: hidden;
            transition: 0.5s;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
        }

        .sidebar h2 {
            text-align: center;
            font-size: 24px;
            color: #fff;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
            text-align: center;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 18px;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar ul li a:hover {
            background-color: #555;
        }

        .main-content {
            padding: 40px;
            background-color: rgba(0, 0, 0, 0.6);
            height: 100vh;
            transition: margin-left 0.5s;
        }

    .login-btn {
    position: fixed;
    top: 10px;
    right: 20px;
    font-size: 18px;
    color: #fff; /* Set to white for contrast */
    background-color: #007bff; /* Updated for more visibility */
    padding: 10px 20px;
    border-radius: 30px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center; /* Center text inside the button */
}


        .login-btn i {
            margin-right: 8px;
        }

        .login-btn:hover {
            background-color: #555;
        }

        .login-icon {
            font-size: 30px;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }

        .menu-btn {
            font-size: 30px;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px 15px;
            border-radius: 50%;
            position: absolute;
            top: 20px;
            left: 20px;
            cursor: pointer;
        }

        .menu-btn:hover {
            background-color: #555;
        }

        /* Close Button inside Sidebar */
        .close-btn {
            font-size: 36px;
            color: #fff;
            position: absolute;
            top: 20px;
            right: 25px;
            cursor: pointer;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            width: 300px;
            border-radius: 8px;
            text-align: center;
            color: #333;
            position: relative;
        }

        .modal-content h2 {
            margin-top: 0;
        }

        .modal-content input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-content button {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color:white ;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }
        .login-modal {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .login-modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: black;
        }
        .login-close-btn {

            margin-top: 10px;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        /* General container for dashboard stats */
/* General container for dashboard stats */
.dashboard-stats {
    display: flex;
    justify-content: space-around; /* Space evenly between the boxes */
    gap: 10px;
    flex-wrap: nowrap; /* Prevents wrapping of boxes */
    padding: 20px;
}

/* Individual stat boxes */
.stat-box {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 8px;
    width: 180px; /* Smaller width for each box */
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

/* Hover effect to lift up the boxes */
.stat-box:hover {
    transform: translateY(-5px);
}

/* Stat box title styling */
.stat-box h3 {
    font-size: 16px;
    color: #333;
    margin-bottom: 10px;
}

/* Stat count styling */
.stat-box p {
    font-size: 22px;
    font-weight: bold;
    color: #007bff;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .dashboard-stats {
        flex-direction: column; /* Stack the boxes vertically on smaller screens */
        align-items: center;
    }

    .stat-box {
        width: 100%; /* Full width on smaller screens */
        margin-bottom: 10px; /* Space between stacked boxes */
    }
}


    </style>
</head>
<body>
<?php
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    echo "<script type='text/javascript'>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('login-modal').style.display = 'flex';
        });
    </script>";
    unset($_SESSION['success_message']); // Clear the message after displaying it
}
?>

<!-- Modal HTML -->
<div id="login-modal" class="login-modal">
    <div class="login-modal-content">
        <p>Logged in successfully</p>
        <button class="login-close-btn" onclick="document.getElementById('login-modal').style.display='none'">Close</button>
    </div>
</div>
    <!-- Menu Button -->
    <span class="menu-btn" onclick="openSidebar()">
        <i class="fas fa-bars"></i>
    </span>

    <!-- Sidebar -->
    <div class="sidebar">
        <span class="close-btn" onclick="closeSidebar()">&times;</span>
        <h2>Dashboard</h2>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="orders.php">Orders</a></li>
            <li><a href="suppliers.php">Suppliers</a></li>
            <li><a href="users">Users</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h1>Inventory Management System</h1>
            <p>Welcome to the inventory dashboard. Manage products, categories, customers, orders, and users here.</p>
        </header>
     <!-- Dashboard Stats -->
     <div class="dashboard-stats">
            <div class="stat-box">
                <h3>Total Orders</h3>
                <p><?php echo $orders_count; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Categories</h3>
                <p><?php echo $categories_count; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Users</h3>
                <p><?php echo $users_count; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Products</h3>
                <p><?php echo $products_count; ?></p>
            </div>
        
        <div class="stat-box">
        <h3>Total Suppliers</h3>
    <p><?php echo $supplier_count; ?></p>
</div>
    </div>

    <!-- Conditionally display login or logout button -->
    <div class="login-btn" onclick="showModal('loginModal')" 
            style="display: <?php echo isset($_SESSION['user_id']) ? 'none' : 'block'; ?>;">Login</div>
        
        <!-- Logout Button (only visible if the user is logged in) -->
        <div class="logout-btn" onclick="window.location.href='logout.php'"
            style="display: <?php echo isset($_SESSION['user_id']) ? 'block' : 'none'; ?>;">
            Logout
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal" id="loginModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('loginModal')">&times;</span>
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <p>Not registered? <a href="#" onclick="switchModal('loginModal', 'registerModal'); event.preventDefault();">Register here</a></p>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal" id="registerModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('registerModal')">&times;</span>
            <h2>Register</h2>
            <form action="register.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="#" onclick="switchModal('registerModal', 'loginModal'); event.preventDefault();">Login here</a></p>
        </div>
    </div>

    <script>
        // Sidebar functions
        function openSidebar() {
            document.querySelector('.sidebar').style.width = '250px';
            document.querySelector('.main-content').style.marginLeft = '250px';
        }

        function closeSidebar() {
            document.querySelector('.sidebar').style.width = '0';
            document.querySelector('.main-content').style.marginLeft = '0';
        }

        // Modal functions
        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Switch between login and register modals
        function switchModal(currentModal, targetModal) {
            closeModal(currentModal);
            showModal(targetModal);
        }
    </script>

</body>
</html>