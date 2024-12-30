<?php
// File: index.php
include('../includes/db.php');

// Fetch child_id from URL
$child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : null;

// Fetch notifications for the specific child
$notifications = [];
if ($child_id) {
    $query = "SELECT * FROM notifications WHERE child_id = :child_id";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bindParam(':child_id', $child_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []; // Fallback to an empty array if fetchAll returns false
        } else {
            // Handle execution failure
            error_log("Failed to execute query for notifications.");
        }
    } else {
        // Handle preparation failure
        error_log("Failed to prepare statement for notifications.");
    }
}

// Check if the success message should be displayed
$successMessage = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Child reported successfully!";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Report Missing Child</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f7f6;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    font-size: 2rem;
    color: #333;
}

h2 {
    color: #555;
    margin-top: 20px;
}

/* Success Message */
.success-message {
    background-color: #4CAF50;
    color: white;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    text-align: center;
    font-size: 1rem;
}

/* Form Styling */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

label {
    font-weight: bold;
    color: #333;
}

input[type="text"],
input[type="number"],
input[type="date"],
select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    width: 100%;
    box-sizing: border-box;
}

button[type="submit"] {
    padding: 12px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1.2rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

/* Notifications Section */
.notifications {
    margin-top: 40px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.notifications p {
    font-size: 1rem;
    color: #333;
}

/* Custom Styles for Header Section */
.logo-container {
    text-align: center;
    margin: 20px 0;
}

.logo-container img {
    max-width: 150px;
    position: relative;
    left: 10px;
}

.header-text {
    text-align: center;
    color: #2c3e50;
}

.header-text h1 {
    color: #2c3e50;
}

.header-text h1 span {
    color: #8cc63f;
}

.subtitle {
    color: #666;
    margin-bottom: 5px;
}

.ministry-text {
    color: #e67e22;
    font-size: 0.9em;
}

.button-container {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
    gap: 20px;
}

.action-button {
    flex: 1;
    padding: 15px 20px;
    text-align: center;
    text-decoration: none;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.sighting-button {
    background-color: #ff9a9a;
    color: white;
}

.missing-button {
    background-color: #4ecdc4;
    color: white;
}

.search-button {
    background-color: #ffcb7d;
    color: white;
}

.action-button:hover {
    opacity: 0.9;
}

/* Specific Form Styling */
form input[type="text"],
form input[type="number"],
form input[type="date"],
form select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    width: 100%;
    box-sizing: border-box;
    margin-top: 5px;
}

form label {
    font-size: 1rem;
    color: #333;
    margin-top: 10px;
}

form button[type="submit"] {
    background-color: #4ecdc4;
    color: white;
    font-weight: bold;
    padding: 15px 0;
    border-radius: 5px;
    font-size: 1.2rem;
}

form button[type="submit"]:hover {
    background-color: #35b8a7;
}

h2#guardiandetails {
    color: #333;
    font-size: 2rem;
    margin-top: 30px;
    position: relative;
    left:400px !important;
}

h1#reportchild {
    font-size: 2rem;
    color: #333;
    margin-top: 30px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        width: 90%;
        margin: 10px auto;
    }

    h1 {
        font-size: 1.5rem;
    }

    label, input, select, button {
        font-size: 0.9rem;
    }

    .logo-container img {
        left: 0;
    }
}

    </style>
</head>
<body>
    <div class="container">
    <div class="logo-container">
            <img src="../assets/images/logo.png" alt="TrackChild Logo">
        </div>
        <div class="header-text">
            <h1>Track<span>Child</span> 2.0</h1>
            <p class="subtitle">National Tracking System for Missing and Vulnerable Children</p>
            <p class="ministry-text">An initiative of Ministry of Women and Child Development</p>
        </div>
        <div class="button-container">
            <a href="#reportchild" class="action-button sighting-button">Enter Child Details</a>
            <a href="#guardiandetails" class="action-button missing-button">Enter Guardian Details</a>
            
        </div>
        <h1 id="reportchild">Report a Missing Child</h1>

        <?php if (!empty($successMessage)) { ?>
            <div class="success-message">
                <?php echo $successMessage; ?>
            </div>
        <?php } ?>

        <form action="submit_child.php" method="POST">
            <label for="child_name">Child Name:</label>
            <input type="text" id="child_name" name="name" required><br>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required><br>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select><br>

            <label for="last_seen_location">Last Seen Location:</label>
            <input type="text" id="last_seen_location" name="last_seen_location" required><br>

            <label for="last_seen_date">Last Seen Date:</label>
            <input type="date" id="last_seen_date" name="last_seen_date" required><br>

            <!-- Guardian Details Section -->
            <h2 id="guardiandetails">Guardian Details</h2>
            <label for="guardian_name">Guardian Name:</label>
            <input type="text" id="guardian_name" name="guardian_name" required><br>

            <label for="relationship">Relationship to Child:</label>
            <input type="text" id="relationship" name="relationship" required><br>

            <label for="contact_info">Contact Information:</label>
            <input type="text" id="contact_info" name="contact_info" required><br>

            <button type="submit">Submit</button>
        </form>

        <h2>Notifications</h2>
        <div class="notifications">
    <?php foreach ($notifications as $row) { 
        // Check if the notification contains "Child found at"
        if (strpos($row['notification_text'], 'Child found at') !== false) { 
            // Extract child_id for linking
            $childId = $row['child_id'];
            ?>
            <p>
                <?php echo htmlspecialchars($row['notification_text']); ?> 
                <a href="view_location.php?child_id=<?php echo $childId; ?>">View Details</a>
            </p>
        <?php } else { ?>
            <p><?php echo htmlspecialchars($row['notification_text']); ?></p>
        <?php } ?>
    <?php } ?>
</div>

    </div>
</body>
</html>
