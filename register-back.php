<?php
// register-back.php 

include_once "DB.php";
include_once "Validator.php";

// var_dump($_POST); exit(); 

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// If request is called with the wrong method we give a generic error message.
// As a general rule we dont give more information than necessary to the responses especially when the request is suspicious.
//  You don't want to give attackers hints about your backend structure or potential vulnerabilities.
session_start();
if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
    $_SESSION['message'] = "Access denied.";
    $_SESSION['mtype'] = "danger";
    header("Location: register-front.php"); exit();
}

/////////////////////////////////////////////////////////////////////

// CSRF Token Validation
if( !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'] ) {
    $_SESSION['message'] = "Access denied.";
    $_SESSION['mtype'] = "danger";
    header("Location: register-front.php"); exit();
}

// This is a validation rule format inspired from the Laravel validator. It is easy to read and easy to modify in case of more/less fields.
$rules = [
    'firstname'  => [ 'required', 'sanitized_text', 'max_length' => 255, ],
    'lastname'   => [ 'required', 'sanitized_text', 'max_length' => 255, ],
    'email'      => [ 'required', 'email', 'unique' => 'users' ],
    'country'    => [ 'required', 'selection' => [ 1=>'Greece', 2=>'Cyprus', 3=>'United Kindom' ], ],
    'phone'      => [ 'required', 'numeric', 'length' => 10, ],
    'phone_code' => [ 'required', 'selection' => [ 1=>'+30', 2=>'+357', 3=>'+44' ], ],
    'experience' => [ 'required', 'selection' => [ 1=>'Entry', 2=>'Mid-Level', 3=>'Proficient' ], ],
    'terms'      => [ 'required', ],
];


// Find and return the errors 
$errors = Validator::validate($_POST, $rules);
if( $errors ) {
    $_SESSION['old'] = $_POST;
    $_SESSION['errors'] = $errors;
    $_SESSION['message'] = "Please correct the errors below.";
    $_SESSION['mtype'] = "danger";
    header("Location: register-front.php"); exit();
}

/////////////////////////////////////////////////////////////////////

// Write to the database
if( DB::insertUser($_POST) ) {
    $_SESSION['message'] = "Registration successful! You can now log in.";
    $_SESSION['mtype'] = "success";
} else {
    // This message is a rare occasion of a database error. 
    // In this scenario we don't give information that concerning the server maintainers and the developers.
    $_SESSION['message'] = "Registration failed. Please try again later.";
    $_SESSION['mtype'] = "danger";
}
header("Location: register-front.php"); exit();
?>
