<?php
// File: feedback.php
include('../includes/db.php');

// Fetch child ID from query string
$child_id = isset($_GET['child_id']) ? intval($_GET['child_id']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guardian_id = $_POST['guardian_id'];
    $feedback_text = $_POST['feedback_text'];
    $rating = $_POST['rating'];

    // Insert feedback into database
    $query = "INSERT INTO user_feedback (child_id, guardian_id, feedback_text, rating) 
              VALUES (:child_id, :guardian_id, :feedback_text, :rating)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':child_id', $child_id, PDO::PARAM_INT);
    $stmt->bindParam(':guardian_id', $guardian_id, PDO::PARAM_INT);
    $stmt->bindParam(':feedback_text', $feedback_text, PDO::PARAM_STR);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: feedback.php?child_id=$child_id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Give Feedback</h1>
        <?php if (isset($_GET['success']) && $_GET['success'] == 1) { ?>
            <div class="success-message">Feedback submitted successfully!</div>
        <?php } ?>

        <form action="feedback.php?child_id=<?php echo $child_id; ?>" method="POST">
            <input type="hidden" name="guardian_id" value="1"> <!-- Replace with dynamic guardian_id -->
            <label for="feedback_text">Feedback:</label>
            <textarea id="feedback_text" name="feedback_text" required></textarea><br>

            <label for="rating">Rating (1-5):</label>
            <input type="number" id="rating" name="rating" min="1" max="5" required><br>

            <button type="submit">Submit Feedback</button>
        </form>
    </div>
</body>
</html>
