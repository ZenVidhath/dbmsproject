<?php
// File: admin.php
include('../includes/db.php');

// Fetch all children with their guardian details from the database
$query = "SELECT c.child_id, c.name AS child_name, c.age, c.gender, c.last_seen_location, c.last_seen_date, 
                 g.guardian_name, g.contact_info, g.relationship 
          FROM children c
          LEFT JOIN guardians g ON c.child_id = g.child_id";  // Corrected the join condition

$children = $conn->query($query);

// Check if the success message should be displayed
$successMessage = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Notification sent successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Report Missing Child</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

table th, table td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
    font-size: 16px;
}

table th {
    background-color: #333;
    color: #fff;
    font-size: 18px;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #e0e0e0;
}

table a {
    color: #4ecdc4;
    text-decoration: none;
    font-weight: bold;
}

table a:hover {
    text-decoration: underline;
}

/* Success Message */
.success-message {
    background-color: #4CAF50;
    color: white;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    text-align: center;
    font-weight: bold;
}


    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>

        <?php if (!empty($successMessage)) { ?>
            <div class="success-message">
                <?php echo $successMessage; ?>
            </div>
        <?php } ?>

        <h2>Children and Guardian Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Child Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Last Seen Location</th>
                    <th>Last Seen Date</th>
                    <th>Guardian Name</th>
                    <th>Contact Info</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $children->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['child_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_seen_location']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_seen_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['guardian_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
                        <td><a href="view_details.php?id=<?php echo $row['child_id']; ?>">View Details</a></td> <!-- Corrected column reference -->
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
