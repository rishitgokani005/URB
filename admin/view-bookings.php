<?php
include('header.php');
?>

<div class="header-row">
    <h1>All User Bookings</h1>
</div>

<div class="card table-responsive mobile-stack">
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>User</th>
                <th>Bike</th>
                <th>Agency</th>
                <th>Dates</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT b.*, u.name as user_name, a.model as bike_name, ag.name as agency_name 
                    FROM abookings b
                    LEFT JOIN users u ON b.user_id = u.user_id
                    LEFT JOIN abike a ON b.bike_id = a.id
                    LEFT JOIN agencies ag ON b.agency_id = ag.id
                    ORDER BY b.booking_date DESC";

            $res = $conn->query($sql);
            while ($row = $res->fetch_assoc()):
                ?>
                <tr>
                    <td data-label="Booking ID"><?php echo $row['booking_id']; ?></td>
                    <td data-label="User"><?php echo $row['user_name']; ?></td>
                    <td data-label="Bike"><?php echo $row['bike_name']; ?></td>
                    <td data-label="Agency"><?php echo $row['agency_name']; ?></td>
                    <td data-label="Dates"><?php echo $row['booking_date']; ?> to <?php echo $row['return_date']; ?></td>
                    <td data-label="Total">₹<?php echo $row['total_price']; ?></td>
                    <td data-label="Status">
                        <span style="color: <?php echo $row['booking_status'] == 'active' ? 'green' : 'red'; ?>; font-weight: 600;">
                            <?php echo ucfirst($row['booking_status']); ?>
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>


</body>

</html>