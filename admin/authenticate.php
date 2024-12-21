<?php
include_once("../config.php");
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
// Get form inputs
//$user_name = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
//$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt the password
//$sql = "SELECT * FROM users WHERE email = ?";

$stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while($row = mysqli_fetch_assoc($result)) {

            if ($password === $row['password']) {
                echo 'Password is valid!';
                echo json_encode(['success' => 'Successful Logged In.']);
                $_SESSION['user_email'] = $email; 
                $_SESSION['fullname'] = $_POST['fullname'];
                header('Location: index.php');
            die();
            } else {
                echo 'Invalid password.';
            }
        }
            
            
    }
    else {
        echo json_encode(['error'=> 'Email or Password Not Found.']);
        session_destroy();
        header('Location: admin-login.php');
        die();
    }

// if ($conn->query($sql) === TRUE) {
//     echo "Account Created with email: <i>".$email."</i><br />";
// } else {
//     echo "Error: " . $sql . "<br>" . $conn->error;
// }


// if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//     echo "Email address '$email' is considered valid.\n";
//     die();
// }

// Handle Profile Picture Upload
// $profile_picture = NULL;
// if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
//     $target_dir = "uploads/";
//     $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
//     if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
//         $profile_picture = $target_file; // Save the file path to the database
//     }
// }

// Insert user data into the database
// $sql = "INSERT INTO users (username, email, password, image) 
//         VALUES ('$user_name', '$email', '$password', '$profile_picture')";

// if ($conn->query($sql) === TRUE) {
//     echo "Account Created with email: <i>".$email."</i><br />";
// } else {
//     echo "Error: " . $sql . "<br>" . $conn->error;
// }


// $conn->close();
}
?>