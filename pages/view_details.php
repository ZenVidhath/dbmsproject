<?php
// File: view_details.php
include('../includes/db.php');

// Fetch the child and guardian details based on the child ID from the query string
if (isset($_GET['id'])) {
    $child_id = $_GET['id'];

    $query = "SELECT c.child_id, c.name AS child_name, c.age, c.gender, c.last_seen_location, c.last_seen_date,
                     g.guardian_name, g.contact_info, g.relationship 
              FROM children c
              LEFT JOIN guardians g ON c.child_id = g.child_id
              WHERE c.child_id = :child_id";  // Use c.child_id instead of c.id

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':child_id', $child_id, PDO::PARAM_INT);
    $stmt->execute();
    $child = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$child) {
        die("Child not found.");
    }
}

// Check if the notification was sent successfully
$successMessage = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = "Notification sent successfully!";
}

// Handle the location form submission (if the form is submitted)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['location_name'])) {
    $location_name = $_POST['location_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Insert location into the database
    $insertQuery = "INSERT INTO locations (child_id, location_name, address, city, state, country, latitude, longitude)
                    VALUES (:child_id, :location_name, :address, :city, :state, :country, :latitude, :longitude)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bindParam(':child_id', $child_id);
    $stmt->bindParam(':location_name', $location_name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':state', $state);
    $stmt->bindParam(':country', $country);
    $stmt->bindParam(':latitude', $latitude);
    $stmt->bindParam(':longitude', $longitude);

    if ($stmt->execute()) {
        // Send notification to the user
        $notificationText = "Child found at $location_name, $address, $city, $state, $country.";
        $notificationQuery = "INSERT INTO notifications (child_id, notification_text) 
                              VALUES (:child_id, :notification_text)";
        $stmt = $conn->prepare($notificationQuery);
        $stmt->bindParam(':child_id', $child_id);
        $stmt->bindParam(':notification_text', $notificationText);

        if ($stmt->execute()) {
            header("Location: view_details.php?id=$child_id&success=1");
            exit();
        } else {
            $successMessage = "Failed to send notification.";
        }
    } else {
        $successMessage = "Failed to add location.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child and Guardian Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Child and Guardian Details</h1>

        <?php if (!empty($successMessage)) { ?>
            <div class="success-message">
                <?php echo $successMessage; ?>
            </div>
        <?php } ?>

        <h2>Child Details</h2>
        <p><strong>Child Name:</strong> <?php echo htmlspecialchars($child['child_name']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($child['age']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($child['gender']); ?></p>
        <p><strong>Last Seen Location:</strong> <?php echo htmlspecialchars($child['last_seen_location']); ?></p>
        <p><strong>Last Seen Date:</strong> <?php echo htmlspecialchars($child['last_seen_date']); ?></p>

        <h2>Guardian Details</h2>
        <p><strong>Guardian Name:</strong> <?php echo htmlspecialchars($child['guardian_name']); ?></p>
        <p><strong>Relationship to Child:</strong> <?php echo htmlspecialchars($child['relationship']); ?></p>
        <p><strong>Contact Information:</strong> <?php echo htmlspecialchars($child['contact_info']); ?></p>

        <h3>Enter Location of Found Child</h3>
        <form action="view_details.php?id=<?php echo $child['child_id']; ?>" method="POST">
            <label for="location_name">Location Name:</label>
            <input type="text" id="location_name" name="location_name" required><br>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required><br>

            <label for="city">City:</label>
            <input type="text" id="city" name="city" required><br>

            <label for="state">State:</label>
            <input type="text" id="state" name="state" required><br>

            <label for="country">Country:</label>
            <input type="text" id="country" name="country" required><br>

            <label for="latitude">Latitude:</label>
            <input type="number" id="latitude" name="latitude" step="0.0001" required><br>

            <label for="longitude">Longitude:</label>
            <input type="number" id="longitude" name="longitude" step="0.0001" required><br>

            <button type="submit">Submit Location</button>
        </form>

        <h3>Send Notification</h3>
        <form action="send_notification.php" method="POST">
            <label for="notification_text">Notification Text:</label>
            <textarea id="notification_text" name="notification_text" required></textarea><br>

            <input type="hidden" name="child_id" value="<?php echo $child['child_id']; ?>">
            <button type="submit">Send Notification</button>
        </form>
    </div>
</body>
</html>
