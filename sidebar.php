<!-- sidebar.php -->
<div class="sidebar">
    <span class="close-btn" onclick="closeSidebar()">&times;</span>
    <h2>Dashboard</h2>
    <ul>
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="categories.php">Categories</a></li>
        <li><a href="orders.php">Orders</a></li>
        <li><a href="suppliers.php">Suppliers</a></li>
        <li><a href="users.php">Users</a></li>
    </ul>
</div>

<!-- Sidebar Styles and JavaScript (Include if not globally defined) -->
<style>
    /* Sidebar and related styles here */
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
</style>

<script>
    // Sidebar functions here
    function openSidebar() {
        document.querySelector('.sidebar').style.width = '250px';
        document.querySelector('.main-content').style.marginLeft = '250px';
    }

    function closeSidebar() {
        document.querySelector('.sidebar').style.width = '0';
        document.querySelector('.main-content').style.marginLeft = '0';
    }
</script>
