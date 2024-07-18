<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle product addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['photo'])) {
    $name = $_POST['name'];
    $serial_number = $_POST['serial_number'];
    $location = $_POST['location'];
    $photo = $_FILES['photo']['name'];
    $target = "uploads/" . basename($photo);

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
        $sql = "INSERT INTO products (name, serial_number, location, photo) VALUES ('$name', '$serial_number', '$location', '$photo')";
        $conn->query($sql);
    } else {
        echo "Failed to upload file.";
    }
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM products WHERE id=$id";
    $conn->query($sql);
    header("Location: admin.php");
}

// Handle product editing
if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $serial_number = $_POST['serial_number'];
    $location = $_POST['location'];
    if ($_FILES['photo']['name']) {
        $photo = $_FILES['photo']['name'];
        $target = "uploads/" . basename($photo);
        move_uploaded_file($_FILES['photo']['tmp_name'], $target);
        $sql = "UPDATE products SET name='$name', serial_number='$serial_number', location='$location', photo='$photo' WHERE id=$id";
    } else {
        $sql = "UPDATE products SET name='$name', serial_number='$serial_number', location='$location' WHERE id=$id";
    }
    $conn->query($sql);
    header("Location: admin.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
       body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.form-container {
    margin-bottom: 40px;
}

.logo-container {
    text-align: center;
    margin-bottom: 20px;
}

.logo {
    width: 100px;
}

h2 {
    margin-bottom: 20px;
}

h3 {
    margin-top: 0;
}

form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

input[type="text"], input[type="file"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
}

input[type="submit"], button {
    padding: 10px;
    background: #007bff;
    border: none;
    color: white;
    border-radius: 4px;
    cursor: pointer;
}

input[type="submit"]:hover, button:hover {
    background: #0056b3;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #007bff;
    color: white;
}

td img {
    width: 100px;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

    </style>
</head>
<body>
    <div class="container form-container">
        <img src="logo.png" alt="Logo" class="logo"> <!-- Placeholder for the logo -->
        <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

        <h3>Add Product</h3>
        <form method="post" action="admin.php" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="text" name="serial_number" placeholder="Serial Number" required>
            <input type="text" name="location" placeholder="Location" required>
            <input type="file" name="photo" required>
            <input type="submit" value="Add Product">
        </form>
    </div>

    <div class="container">
        <h3>Product List</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Serial Number</th>
                <th>Location</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
            <?php
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['serial_number'] . "</td>";
                echo "<td>" . $row['location'] . "</td>";
                echo "<td><img src='uploads/" . $row['photo'] . "'></td>";
                echo "<td class='action-buttons'>
                        <form method='post' action='admin.php' enctype='multipart/form-data' style='display: inline-block;'>
                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                            <input type='text' name='name' placeholder='Product Name' value='" . $row['name'] . "' required>
                            <input type='text' name='serial_number' placeholder='Serial Number' value='" . $row['serial_number'] . "' required>
                            <input type='text' name='location' placeholder='Location' value='" . $row['location'] . "' required>
                            <input type='file' name='photo'>
                            <input type='submit' name='edit_product' value='Edit'>
                        </form>
                        <form method='get' action='admin.php' style='display: inline-block;'>
                            <input type='hidden' name='delete' value='" . $row['id'] . "'>
                            <button type='submit'>Delete</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
