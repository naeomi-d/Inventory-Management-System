<?php
session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "inventory");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add/Edit/Delete Category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $action = $_POST['action'];

    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $category_name);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Category added successfully!";
        } else {
            $_SESSION['message'] = "Error adding category.";
        }
    } elseif ($action === 'edit' && $category_id) {
        $stmt = $conn->prepare("UPDATE categories SET name=? WHERE category_id=?");
        $stmt->bind_param("si", $category_name, $category_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Category updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating category.";
        }
    } elseif ($action === 'delete' && $category_id) {
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id=?");
        $stmt->bind_param("i", $category_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Category deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting category.";
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh to avoid form re-submission
    exit();
}

// AJAX request to load products for selected category
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $products_result = $conn->query("SELECT product_id,  name, description, price FROM products WHERE category_id = $category_id");

    if ($products_result->num_rows > 0) {
        while ($product = $products_result->fetch_assoc()) {
            echo "<tr>
                    <td>{$product['product_id']}</td>
                    
                    <td>{$product['name']}</td>
                    <td>{$product['description']}</td>
                    <td>{$product['price']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No products found</td></tr>";
    }
    exit(); // End execution here to only output the product rows
}

// Fetch categories for display in the main HTML content
$categories_result = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            color: #343a40;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .message {
            color: #28a745;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="hidden"] {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        button {
            padding: 12px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td button {
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        td button:hover {
            background-color: #218838;
        }

        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .product-description {
            font-size: 14px;
            color: #6c757d;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            overflow: auto;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 800px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 25px;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

    </style>
    <script>
        function viewProducts(categoryId, categoryName) {
            document.getElementById('selectedCategory').textContent = categoryName;

            // AJAX request to load products for selected category
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "categories.php?category_id=" + categoryId, true); // Calls this PHP file with category_id in the query string
            xhr.onload = function() {
                document.getElementById('productList').innerHTML = this.responseText; // Load response into productList
                document.getElementById('productModal').style.display = "block"; // Open modal
            };
            xhr.send();
        }

        // Close the modal
        function closeModal() {
            document.getElementById('productModal').style.display = "none";
        }
    </script>
</head>
<body>
<?php include 'sidebar.php'; ?>
<span class="menu-btn" onclick="openSidebar()">
    <i class="fas fa-bars"></i>
</span>
    <div class="container">
        

        <h2>Category List</h2>
        <!-- Categories Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $categories_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['category_id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><button type="button" onclick="viewProducts(<?= $row['category_id'] ?>, '<?= $row['name'] ?>')">View Products</button></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal to view products -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Products in <span id="selectedCategory"></span> Category</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody id="productList">
                    <!-- Products will be loaded here via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

