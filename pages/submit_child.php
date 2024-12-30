<?php
// File: submit_child.php
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = $_POST['name'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $last_seen_location = $_POST['last_seen_location'];
        $last_seen_date = $_POST['last_seen_date'];
        $guardian_name = $_POST['guardian_name'];
        $relationship = $_POST['relationship'];
        $contact_info = $_POST['contact_info'];

        // Insert child record
        $stmt = $conn->prepare("INSERT INTO children (name, age, gender, last_seen_location, last_seen_date) VALUES (:name, :age, :gender, :location, :date)");
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':age', $age, PDO::PARAM_INT);
        $stmt->bindValue(':gender', $gender);
        $stmt->bindValue(':location', $last_seen_location);
        $stmt->bindValue(':date', $last_seen_date);
        $stmt->execute();

        // Get the last inserted child_id
        $child_id = $conn->lastInsertId();

        // Insert guardian record
        $stmt = $conn->prepare("INSERT INTO guardians (child_id, guardian_name, relationship, contact_info) VALUES (:child_id, :guardian_name, :relationship, :contact_info)");
        $stmt->bindValue(':child_id', $child_id, PDO::PARAM_INT);
        $stmt->bindValue(':guardian_name', $guardian_name);
        $stmt->bindValue(':relationship', $relationship);
        $stmt->bindValue(':contact_info', $contact_info);
        $stmt->execute();

        // Redirect with a success message
        header("Location: index.php?success=1&child_id=$child_id");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
