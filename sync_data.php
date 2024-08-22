<?php
include('db.php'); // Include your database connection file

// sync_data.php

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the raw POST data
    $data = file_get_contents("php://input");
    $decodedData = json_decode($data, true);

    // Validate the data
    if (is_array($decodedData)) {
        // Example: Assume $decodedData contains an array of transactions
        foreach ($decodedData as $transaction) {
            // Extract necessary fields from the transaction data
            $date = $transaction['date'];
            $amount = $transaction['amount'];
            $customer = $transaction['customer'];

            // Insert data into the database
            // Assuming you have a database connection already established
            $sql = "INSERT INTO transactions (date, amount, customer) VALUES ('$date', '$amount', '$customer')";

            if ($conn->query($sql) === TRUE) {
                echo "Transaction recorded successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    } else {
        echo "Invalid data format.";
    }
} else {
    echo "Invalid request method.";
}

// Close the database connection
$conn->close();