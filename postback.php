<?php
// Include WordPress functions
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

error_log(print_r($_GET, true)); // This will log the parameters in the server's PHP error log
file_put_contents('postback_log.txt', print_r($_GET, true), FILE_APPEND); // This logs to a file

// Get variables from OGAds postback
$user_id = $_GET['user_id'];  // Assuming user_id is passed in aff_sub
$offer_id = $_GET['offer_id']; // ID of the completed offer
$payout = $_GET['payout'];    // Reward payout from OGAds
$secret_key = 'KYRvAM7a1abDQQ5nX2cKKuWIF5oGPwtz'; // Secret key for security

// Security: Validate the secret key
if (isset($_GET['key']) && $_GET['key'] !== $secret_key) {
    die('Invalid request'); // Stop processing if the key doesn't match
}

error_log(print_r($_GET, true)); // This will log the parameters in the server's PHP error log

// Check if the required variables are present
if (isset($user_id) && isset($offer_id) && isset($payout)) {

    // Multiply the payout by 100 to get the coin value
    $coins = $payout * 26;

    // Make sure the user ID exists in WordPress
    if (get_userdata($user_id) !== false) {
        
        // Check if myCred is installed and active
        if (function_exists('mycred_add')) {
            // Add coins to the user via myCred
            mycred_add(
                'completed_offer',        // Log reference for tracking
                $user_id,                 // The user ID to credit
                $coins,                   // Amount of coins to add (payout * 100)
                'Completed offer ID ' . $offer_id . ' with payout of ' . $payout // Optional log entry text
            );

            // Return success response to OGAds
            echo "OK"; // OGAds expects this response to confirm postback success
        } else {
            // If myCred is not active or installed
            echo "myCred plugin not installed or active.";
        }
    } else {
        // User ID doesn't exist in WordPress
        echo "Invalid user ID.";
    }
} else {
    // Missing parameters from the postback
    echo "ERROR: Missing parameters.";
}