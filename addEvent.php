<?php
include "config.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $datetime = $_POST["datetime"];
    $location = $_POST["location"];
    $price = $_POST["price"];
    $conn->query("INSERT INTO events (event_name, event_datetime, location, price) VALUES ('$name', '$datetime', '$location', '$price')");
    header("Location: manageEvents.php");
    exit();
}
?>

<!-- HTML Form -->
<form method="post">
    <input name="name" placeholder="Event Name" required />
    <input name="datetime" type="datetime-local" required />
    <input name="location" placeholder="Location" required />
    <input name="price" type="number" placeholder="Ticket Price" required />
    <button type="submit">Add Event</button>
</form>
