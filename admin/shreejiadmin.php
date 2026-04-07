<?php
session_start();

// Connect to the database
$servername = "localhost";
$username = "root"; // Change this if necessary
$password = ""; // Change this if necessary
$dbname = "dwk"; // Database name

$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


   // Redirect based on role
   //if ($user['role'] === 'admin') {
    //header("Location: !admin.php");
//} else {
  //  header("Location: index.php");
//}
//exit;
// Handle adding new bike card
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_bike'])) {
    $id = $_POST['id'] ?? '';
    $model = $_POST['model'] ?? '';
    $color = $_POST['color'] ?? '';
    $deposite = $_POST['deposite'] ?? '';
    $price_per_day = $_POST['price_per_day'] ?? '';
    $address = $_POST['address'] ?? '';
    $image = $_FILES['image']['name'] ?? '';
    $target = 'uploads/' . basename($image);

    // Check if image was uploaded successfully
    if ($_FILES['image']['error'] != 0) {
        echo "Error uploading file: " . $_FILES['image']['error'];
    } else {
        // Check if the image type is allowed (e.g., PNG, JPG, JPEG)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);
        if (!in_array($file_type, $allowed_types)) {
            echo "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        } else {
            // Move the uploaded image file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                // Insert bike data into the database
                $stmt = $mysqli->prepare("INSERT INTO abike (id, model, color, deposite, price_per_day, address, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $id, $model, $color, $deposite, $price_per_day, $address, $image);
                if ($stmt->execute()) {
                    echo "Bike card added successfully!";
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Failed to upload image.";
            }
        }
    }
}

// Activate/Deactivate a bike
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "UPDATE `abike` SET status = NOT status WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    
    // Redirect to the same page without URL parameters
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}
// Delete a bike
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM `abike` WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
}

// Fetch all bikes
$sql = "SELECT * FROM `abike` where 'address' like 'shreeji%'";
$result = $mysqli->query($sql);
// Fetch user details
$booking_results = $mysqli->query("SELECT * FROM abookings  where 'address' like 'shreeji%' ORDER BY booking_date");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Bikes</title>
    <link rel="stylesheet" href="../css/addash.css">
    <link rel="icon" href="logo/urbanride1.ico"  sizes="1080x1080" type="image/x-icon">

</head>
<body>

<h2>Add a New Bike</h2>
<form method="POST" action="" enctype="multipart/form-data">
    <label for="id">ID:</label>
    <input type="text" name="id" required><br>
    <label for="address">Address:</label> 
    <input type="text" name="address" maxlength="150" required><br>
    <label for="model">Model:</label>
    <input type="text" name="model" required><br>
    <label for="color">Color:</label>
    <input type="text" name="color" required><br>
    <label for="price_per_day">price_per_day:</label>
    <input type="number" name="price_per_day" required><br>
    <label for="deposite">Deposit:</label>
    <input type="text" name="deposite" required><br>
    <label for="image">Image:</label>
    <input type="file" name="image" required><br>
    <button type="submit" name="add_bike">Add Bike</button>
</form>

<h2>Bike List</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Model</th>
        <th>Color</th>
        <th>Deposit</th>
        <th>price_per_day</th>
        <th>Image</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($bike = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $bike['id']; ?></td>
        <td><?php echo $bike['model']; ?></td>
        <td><?php echo $bike['color']; ?></td>
        <td><?php echo $bike['deposite']; ?></td>
        <td><?php echo $bike['price_per_day']; ?></td>
        <td>
            <?php if (!empty($bike['image'])): ?>
                <img src="uploads/<?php echo $bike['image']; ?>" alt="Bike Image" width="100">
            <?php else: ?>
                No Image
            <?php endif; ?>
        </td>
        <td><?php echo $bike['status'] ? 'Active' : 'Inactive'; ?></td>
        <td>
            <a href="?toggle_status=1&id=<?php echo $bike['id']; ?>">
                <?php echo $bike['status'] ? 'Deactivate' : 'Activate'; ?>
            </a>
            <a href="?delete=1&id=<?php echo $bike['id']; ?>" onclick="return confirm('Are you sure you want to delete this bike?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- Display User Bookings -->
<h2>User Booking Details</h2>
<table class="booking-table">
    <thead>
<tr>
            <th>Bike_ID</th>
            <th>Name</th>
            <th>Booking ID</th>
            <th>ID Proof</th>
            <th>Booked Date</th>
            <th>Pick up time</th>
            <th>Return Date</th>
            <th>Drop of time</th>
            <th>Mobile Number</th>
            <th>Booking Status</th>
            <th>Total Price </th>
            <th>Payment Method</th>
        </tr>
    </thead>
    <tbody>
      <!--  php  while ($booking = $booking_results->fetch_assoc()): ?>--> 
            <?php 
        $counter = 0; // Counter to track rows
        while ($booking = $booking_results->fetch_assoc()): 
            $counter++;
        ?>
            <tr class="<?php echo $counter > 5 ? 'hidden-row' : ''; ?>">
            
                <td><?php echo $booking['bike_id']; ?></td>
                <td><?php echo $booking['name']; ?></td>
                <td><?php echo $booking['booking_id']; ?></td>
                <td><?php echo $booking['idProof']; ?></td>
                <td><?php echo $booking['booking_date']; ?></td>
                <td><?php echo $booking['pick_up_time']; ?></td>
                <td><?php echo $booking['return_date']; ?></td>
                <td><?php echo $booking['drop_off_time']; ?></td>
                <td><?php echo $booking['mobile']; ?></td>
                <td><?php echo $booking['booking_status']; ?></td>
                <td><?php echo $booking['total_price']; ?></td>
                <td><?php echo $booking['paymentMethod']; ?></td>
                <td>
                   <!-- <form method="POST" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                        <input type="hidden" name="delete_id" value="<?php echo $booking['bike_id']; ?>">
                        <button type="submit">Delete</button>
                    </form>-->
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<div class="toggle-container">
    <button id="toggle-button" onclick="toggleRows()">Show More</button>
</div>
<style>
.hidden-row {
    display: none;
}

.toggle-container {
    text-align: center;
    margin-top: 10px;
}

#toggle-button {
    background-color: #ff3c00;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 14px;
}

#toggle-button:hover {
    background-color: #e63600;
}

</style>
<script>
    function toggleRows() {
    const rows = document.querySelectorAll('.hidden-row');
    const button = document.getElementById('toggle-button');

    if (button.innerText === 'Show More') {
        rows.forEach(row => row.style.display = 'table-row');
        button.innerText = 'Show Less';
    } else {
        rows.forEach(row => row.style.display = 'none');
        button.innerText = 'Show More';
    }
}

</script>

</body>
</html>

<?php $mysqli->close(); ?>
