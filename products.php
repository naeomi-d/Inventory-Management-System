<?php
session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "inventory");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions for adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $image = $_POST['image']; // Ensure you handle file upload appropriately

    // Insert new product into the database
    $sql = "INSERT INTO products (name, category_id, description, quantity, price, image) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisids", $name, $category_id, $description, $quantity, $price, $image);

    if ($stmt->execute()) {
        echo "<script>alert('Product added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding product: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Handle deletion of a product
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete product from the database
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting product: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Handle updating a product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    // Update the product in the database
    $sql = "UPDATE products SET name=?, category_id=?, description=?, quantity=?, price=?, image=? WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisidsi", $name, $category_id, $description, $quantity, $price, $image, $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating product: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Check if an edit is requested and fetch the product details
$product_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product_data = $result->fetch_assoc();
    $stmt->close();
}

// Retrieve all products from the database
$sql = "SELECT product_id, image, name, category_id, description, quantity, price FROM products";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        h2{
            text-align: center;
        }
        /* Basic styling for the page */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: center; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
        .edit-btn, .delete-btn, .add-btn { background-color: #4CAF50; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; }
        .delete-btn { background-color: #f44336; }
        .add-product-form, .edit-product-form { margin-bottom: 20px; }
        
        /* Modal styling */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            float: right;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        input, button {
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<span class="menu-btn" onclick="openSidebar()">
    <i class="fas fa-bars"></i>
</span>

<h2>Products</h2>

<!-- Add Product Button -->
<button class="add-btn" id="addProductBtn">Add Product</button>

<!-- Products Table -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Picture</th>
            <th>Name</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['product_id'] . "</td>";
                echo "<td><img src='" . $row['image'] . "' alt='Product Image' width='50'></td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['quantity'] . "</td>";
                echo "<td>" . $row['price'] . "</td>";
                echo "<td>";
                echo "<a href='?edit_id=" . $row['product_id'] . "' class='edit-btn' id='editBtn'>Edit</a> ";
                echo "<a href='?delete_id=" . $row['product_id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this product?\");'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No products found</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Modal for Adding Product -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Add Product</h3>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="number" name="category_id" placeholder="Category ID" required>
            <input type="text" name="description" placeholder="Description" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="text" name="price" placeholder="Price" required>
            <input type="text" name="image" placeholder="Image URL" required>
            <button type="submit" name="add_product">Add Product</button>
        </form>
    </div>
</div>

<!-- Modal for Editing Product -->
<?php if ($product_data): ?>
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Edit Product</h3>
        <form method="POST" action="">
            <input type="hidden" name="product_id" value="<?php echo $product_data['product_id']; ?>">
            <input type="text" name="name" value="<?php echo $product_data['name']; ?>" required>
            <input type="text" name="description" value="<?php echo $product_data['description']; ?>" required>
            <input type="number" name="quantity" value="<?php echo $product_data['quantity']; ?>" required>
            <input type="text" name="price" value="<?php echo $product_data['price']; ?>" required>
            <input type="text" name="image" value="<?php echo $product_data['image']; ?>" required>
            <button type="submit" name="edit_product">Update Product</button>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
    // Open the add product modal
    document.getElementById('addProductBtn').addEventListener('click', function() {
        document.getElementById('addProductModal').style.display = 'block';
    });

    // Open the edit product modal if an edit is requested
    <?php if (isset($product_data)): ?>
        document.getElementById('editProductModal').style.display = 'block';
    <?php endif; ?>

    // Close modal when clicking the close button or outside the modal
    function closeModal() {
        document.getElementById('addProductModal').style.display = 'none';
        document.getElementById('editProductModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target === document.getElementById('addProductModal')) {
            closeModal();
        } else if (event.target === document.getElementById('editProductModal')) {
            closeModal();
        }
    }
</script>

</body>
</html>
