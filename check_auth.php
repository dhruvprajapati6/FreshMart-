<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $firstName = $_SESSION['first_name'] ?? '';
    $lastName = $_SESSION['last_name'] ?? '';
    
    // Create initial
    $initial = strtoupper(substr($firstName, 0, 1));
    if (empty($initial)) $initial = 'U';

    echo json_encode([
        'loggedin' => true,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'full_name' => trim($firstName . ' ' . $lastName),
        'email' => $_SESSION['email'] ?? '',
        'initial' => $initial,
        'profile_photo' => $_SESSION['profile_photo'] ?? 'default.png'
    ]);
} else {
    echo json_encode(['loggedin' => false]);
}
