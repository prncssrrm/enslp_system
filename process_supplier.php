<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'config.php';

// Check kung naka-login
if (!isset($_SESSION['user_id'])) { 
    exit("Unauthorized access"); 
}

// --- DELETE LOGIC ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        // Redirect na may success status
        header("Location: suppliers.php?status=deleted");
        exit();
    }
}

// --- ADD / SAVE LOGIC ---
if (isset($_POST['save_supplier'])) {
    $name = $_POST['supplier_name'];
    $contact = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $items = $_POST['supplied_items'];

    $sql = "INSERT INTO suppliers (supplier_name, contact_person, phone, email, address, supplied_items) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$name, $contact, $phone, $email, $address, $items])) {
        header("Location: suppliers.php?status=added");
        exit();
    }
}

// --- UPDATE LOGIC ---
if (isset($_POST['update_supplier'])) {
    $id = $_POST['supplier_id'];
    $name = $_POST['supplier_name'];
    $contact = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $items = $_POST['supplied_items'];

    $sql = "UPDATE suppliers SET supplier_name=?, contact_person=?, phone=?, email=?, address=?, supplied_items=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$name, $contact, $phone, $email, $address, $items, $id])) {
        header("Location: suppliers.php?status=updated");
        exit();
    }
}

// Kung may error o walang action, balik lang sa main page
header("Location: suppliers.php");
exit();
?>