<?php
session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "inventory");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Page</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        
        .order-container { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .buttons { margin-bottom: 20px; }
        .total-order-value { text-align: right; font-weight: bold; margin-top: 20px; }
        .menu-btn { position: fixed; top: 20px; left: 20px; cursor: pointer; }

    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<span class="menu-btn" onclick="openSidebar()">
    <i class="fas fa-bars"></i>
</span>
<div class="order-container">
    <h2>Order</h2>
    <div class="buttons">
        <button class="btn btn-success" onclick="openProductModal()">Add Product</button>
        <button class="btn btn-warning" onclick="clearOrder()">Clear</button>
    </div>

    <div class="customer-info">
        <p>User: 
            <span id="customer-info">
                <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
            </span>
        </p>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="order-items">
            <!-- Dynamic rows for each added product -->
        </tbody>
    </table>

    <div class="total-order-value">
        <p>Total Order Value: <span id="total-value">$0.00</span></p>
    </div>

    <button class="btn btn-primary" onclick="createOrder()">Create Order</button>
</div>

<!-- Product Modal -->
<div class="modal" id="productModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Product</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Picture</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody id="product-list">
                        <?php
                        $sql = "SELECT product_id, image, name, category_id, description, quantity, price FROM products";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr onclick=\"addProductToOrder({id: '{$row['product_id']}', name: '{$row['name']}', price: {$row['price']}, image: '{$row['image']}'})\">";
                                echo "<td>{$row['product_id']}</td>";
                                echo "<td><img src='{$row['image']}' alt='Product Image' width='50'></td>";
                                echo "<td>{$row['name']}</td>";
                                echo "<td>{$row['category_id']}</td>";
                                echo "<td>{$row['description']}</td>";
                                echo "<td>{$row['quantity']}</td>";
                                echo "<td>{$row['price']}</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No products found</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS CDN -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function openProductModal() {
        $('#productModal').modal('show');
    }

    function addProductToOrder(product) {
        $('#productModal').modal('hide');
        let orderTable = document.getElementById("order-items");
        let newRow = orderTable.insertRow();
        newRow.innerHTML = `
            <td>${product.id}</td>
            <td>${product.name}</td>
            <td><input type="number" value="1" min="1" onchange="updateTotal(this, ${product.price})"></td>
            <td>${product.price}</td>
            <td>${product.price}</td>
            <td><button class="btn btn-danger btn-sm" onclick="removeProduct(this)">Remove</button></td>
        `;
        updateOrderTotal();
    }

    function updateTotal(element, price) {
        let row = element.parentNode.parentNode;
        let quantity = element.value;
        let total = quantity * price;
        row.cells[4].innerText = total.toFixed(2);
        updateOrderTotal();
    }

    function updateOrderTotal() {
        let orderTable = document.getElementById("order-items");
        let total = 0;
        for (let i = 0; i < orderTable.rows.length; i++) {
            total += parseFloat(orderTable.rows[i].cells[4].innerText);
        }
        document.getElementById("total-value").innerText = `$${total.toFixed(2)}`;
    }

    function clearOrder() {
        document.getElementById("order-items").innerHTML = "";
        updateOrderTotal();
    }

    function removeProduct(button) {
        let row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
        updateOrderTotal();
    }

    function createOrder() {
        let orderData = [];
        let orderTable = document.getElementById("order-items");
        for (let i = 0; i < orderTable.rows.length; i++) {
            let cells = orderTable.rows[i].cells;
            let item = {
                product_id: cells[0].innerText,
                product: cells[1].innerText,
                quantity: cells[2].children[0].value,
                price: cells[3].innerText,
                total: cells[4].innerText
            };
            orderData.push(item);
        }

        $.ajax({
            url: '',
            method: 'POST',
            data: { orderData: JSON.stringify(orderData) },
            success: function(response) {
                alert("Order created successfully!");
                clearOrder();
            },
            error: function() {
                alert("Failed to create order.");
            }
        });
    }
</script>

<?php
// Check if order data is received via POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['orderData'])) {
    $orderData = json_decode($_POST['orderData'], true);
    $conn = new mysqli("localhost", "root", "", "inventory");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Loop through each item and insert into the orders table
    foreach ($orderData as $item) {
        $stmt = $conn->prepare("INSERT INTO orders (product_id, quantity, price, total) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iidd", $item['product_id'], $item['quantity'], $item['price'], $item['total']);
        if (!$stmt->execute()) {
            echo "<script>alert('Error creating order: " . $conn->error . "');</script>";
        }
    }

    $conn->close();
    echo "<script>alert('Order created successfully!');</script>";
    exit;  // Make sure to stop further execution
}
?>

</body>
</html>
