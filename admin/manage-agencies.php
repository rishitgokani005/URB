<?php
include('header.php');

// Handle Agency Addition
if (isset($_POST['add_agency'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $id = "AGY" . strtoupper(substr(uniqid(), -5));

    $sql = "INSERT INTO agencies (id, name, city, email, password) VALUES ('$id', '$name', '$city', '$email', '$password')";
    if ($conn->query($sql)) {
        echo "<script>alert('Agency added successfully. ID: $id');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM agencies WHERE id='$id'");
    echo "<script>window.location.href='manage-agencies.php';</script>";
}
?>

<div class="header-row reveal" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 style="font-family: var(--font-heading); font-size: 2rem;">Rental Partners</h2>
        <p style="color: var(--text-sub);">Manage your network of bike rental providers</p>
    </div>
    <button class="btn-signup" onclick="document.getElementById('addModal').style.display='flex'">Add New Partner</button>
</div>

<div class="table-card reveal">
    <table>
        <thead>
            <tr>
                <th>Partner ID</th>
                <th>Name</th>
                <th>City</th>
                <th>Login Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = $conn->query("SELECT * FROM agencies ORDER BY name ASC");
            while($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><b>#<?php echo $row['id']; ?></b></td>
                <td><?php echo $row['name']; ?></td>
                <td><i class="fas fa-location-dot" style="margin-right: 8px; color: var(--primary);"></i><?php echo $row['city']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td>
                    <a href="?delete=<?php echo $row['id']; ?>" style="color: #F87171; font-weight: 700; font-size: 0.85rem;" onclick="return confirm('Remove this partner permanently?')"><i class="fas fa-trash-can"></i> Remove</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add Agency Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <h2>Register New Partner</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Agency / Shop Name" required>
            <input type="text" name="city" placeholder="Operating City" required>
            <input type="email" name="email" placeholder="Business Email (Used for login)" required>
            <input type="password" name="password" placeholder="System Password" required>
            <div style="display: flex; gap: 15px; margin-top: 20px;">
                <button type="submit" name="add_agency" class="btn-signup" style="flex: 2;">Confirm Registration</button>
                <button type="button" class="btn-login" onclick="document.getElementById('addModal').style.display='none'" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('revealed');
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php include('footer.php'); ?>
