<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Page</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin-top: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h2 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .table {
            margin-top: 30px;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #007bff;
            color: #ffffff;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .modal-dialog {
            max-width: 500px;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .modal-header h4 {
            margin: 0;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-body input,
        .modal-body textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .modal-body textarea {
            height: 150px;
        }

        .modal-footer {
            text-align: right;
        }

        .close {
            color: #fff;
            font-size: 24px;
            font-weight: bold;
        }

        .close:hover {
            color: #fff;
            text-decoration: none;
        }

       

        @media (max-width: 768px) {
            .menu-btn {
                display: block;
                position: absolute;
                top: 20px;
                left: 20px;
                cursor: pointer;
                z-index: 1000;
            }

            .container {
                margin-top: 60px;
                padding: 10px;
            }

            .table th, .table td {
                font-size: 12px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<span class="menu-btn" onclick="openSidebar()">
    <i class="fas fa-bars"></i>
</span>
<div class="container">
    <h2>Suppliers</h2>
    <button class="btn btn-primary mb-3" onclick="openAddSupplierModal()">Add Supplier</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Supplier ID</th>
                <th>Supplier Name</th>
                <th>Location</th>
                <th>Email</th>
                <th>Products</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="supplierTable">
            <?php
            // Connect to the database
            $conn = new mysqli("localhost", "root", "", "inventory");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch suppliers
            $sql = "SELECT * FROM supplier";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr id='supplier-" . $row['supplier_id'] . "'>";
                    echo "<td>" . $row['supplier_id'] . "</td>";
                    echo "<td>" . $row['supplier_name'] . "</td>";
                    echo "<td>" . $row['supplier_location'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['products'] . "</td>";
                    echo "<td>
                            <button class='btn btn-warning btn-sm' onclick=\"openEditSupplierModal(" . $row['supplier_id'] . ",'" . $row['supplier_name'] . "','" . $row['supplier_location'] . "','" . $row['email'] . "','" . $row['products'] . "')\">Edit</button>
                            <button class='btn btn-danger btn-sm' onclick=\"openDeleteSupplierModal(" . $row['supplier_id'] . ")\">Delete</button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No suppliers found</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<!-- Add Supplier Modal -->
<div class="modal" id="addSupplierModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Supplier</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="text" id="supplier_name" class="form-control" placeholder="Supplier Name" required>
                <input type="text" id="supplier_location" class="form-control mt-2" placeholder="Location">
                <input type="email" id="email" class="form-control mt-2" placeholder="Email" required>
                <textarea id="products" class="form-control mt-2" placeholder="Products" rows="3"></textarea>
                <button onclick="addSupplier()" class="btn btn-success mt-3">Add Supplier</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal" id="editSupplierModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Supplier</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_supplier_id">
                <input type="text" id="edit_supplier_name" class="form-control" placeholder="Supplier Name" required>
                <input type="text" id="edit_supplier_location" class="form-control mt-2" placeholder="Location">
                <input type="email" id="edit_email" class="form-control mt-2" placeholder="Email" required>
                <textarea id="edit_products" class="form-control mt-2" placeholder="Products" rows="3"></textarea>
                <button onclick="editSupplier()" class="btn btn-primary mt-3">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Supplier Modal -->
<div class="modal" id="deleteSupplierModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Delete Supplier</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this supplier?</p>
                <input type="hidden" id="delete_supplier_id">
            </div>
            <div class="modal-footer">
                <button onclick="deleteSupplier()" class="btn btn-danger">Delete</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function openAddSupplierModal() {
        $('#addSupplierModal').modal('show');
    }

    function openEditSupplierModal(id, name, location, email, products) {
        $('#edit_supplier_id').val(id);
        $('#edit_supplier_name').val(name);
        $('#edit_supplier_location').val(location);
        $('#edit_email').val(email);
        $('#edit_products').val(products);
        $('#editSupplierModal').modal('show');
    }

    function openDeleteSupplierModal(id) {
        $('#delete_supplier_id').val(id);
        $('#deleteSupplierModal').modal('show');
    }

    function addSupplier() {
        const data = {
            action: 'add',
            supplier_name: $('#supplier_name').val(),
            supplier_location: $('#supplier_location').val(),
            email: $('#email').val(),
            products: $('#products').val(),
        };

        $.post('', data, function(response) {
            location.reload();
        });
    }

    function editSupplier() {
        const data = {
            action: 'edit',
            supplier_id: $('#edit_supplier_id').val(),
            supplier_name: $('#edit_supplier_name').val(),
            supplier_location: $('#edit_supplier_location').val(),
            email: $('#edit_email').val(),
            products: $('#edit_products').val(),
        };

        $.post('', data, function(response) {
            location.reload();
        });
    }

    function deleteSupplier() {
        const data = {
            action: 'delete',
            supplier_id: $('#delete_supplier_id').val(),
        };

        $.post('', data, function(response) {
            location.reload();
        });
    }
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "root", "", "inventory");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    switch ($_POST['action']) {
        case 'add':
            $stmt = $conn->prepare("INSERT INTO supplier (supplier_name, supplier_location, email, products) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $_POST['supplier_name'], $_POST['supplier_location'], $_POST['email'], $_POST['products']);
            $stmt->execute();
            break;
        case 'edit':
            $stmt = $conn->prepare("UPDATE supplier SET supplier_name=?, supplier_location=?, email=?, products=? WHERE supplier_id=?");
            $stmt->bind_param("ssssi", $_POST['supplier_name'], $_POST['supplier_location'], $_POST['email'], $_POST['products'], $_POST['supplier_id']);
            $stmt->execute();
            break;
        case 'delete':
            $stmt = $conn->prepare("DELETE FROM supplier WHERE supplier_id=?");
            $stmt->bind_param("i", $_POST['supplier_id']);
            $stmt->execute();
            break;
    }

    $conn->close();
    exit;
}
?>

</body>
</html>
