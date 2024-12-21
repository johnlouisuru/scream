<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user data from the form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $croppedImage = $_FILES["profile_picture"]["name"] ?? ''; // Get the base64 image data

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo json_encode(['error' => 'Passwords do not match!']);
        exit;
    }

    // Password validation (optional, for example: length check)
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        echo json_encode(['error' => 'Password is weak.']);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if image was uploaded and process it
    if ($croppedImage) {
        // Generate the path to save the image
        $userDir = 'users/' . $username;

        // Create the necessary directories if they do not exist
        if (!is_dir($userDir)) {
            mkdir($userDir, 0777, true);
        }
        if (!is_dir($userDir . '/gfx')) {
            mkdir($userDir . '/gfx', 0777, true);
        }
        if (!is_dir($userDir . '/vox_lessons')) {
            mkdir($userDir . '/vox_lessons', 0777, true);
        }
        if (!is_dir($userDir . '/user_data')) {
            mkdir($userDir . '/user_data', 0777, true);
        }

        // Generate the file path for the profile image
        $imagePath = $userDir . '/'. $croppedImage;

        // Save the cropped image (Base64 to image file)
        //file_put_contents($imagePath, base64_decode(str_replace('data:image/jpeg;base64,', '', $croppedImage)));
        //file_put_contents($imagePath,base64_decode($croppedImage));
        //move_uploaded_file($_FILES["profile_picture"]["tmp_name"],$imagePath);
        //file_put_contents($imagePath, file_get_contents($croppedImage));
        $profile_picture = NULL;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            //$target_dir = "uploads/";
            $target_file = $userDir . '/' . basename($_FILES["profile_picture"]["name"]);
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file; // Save the file path to the database
            }
        }

    } else {
        $imagePath = '';  // No image provided
    }

    // Connect to the database
    include_once("config.php");

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['error' => 'Email is already registered.']);
         header('Location:sign_in.php');
        exit;
    }

    // Insert the user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $profile_picture);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $last_id = $conn->insert_id;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_id'] = $last_id;  
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['image'] = $profile_picture;
        echo json_encode(['success' => 'User registered successfully!']);
        header('Location:user-profile.php');
    } else {
        echo json_encode(['error' => 'An error occurred during registration.']);
        header('Location:sign_in.php');
    }

    $stmt->close();
    $conn->close();
}
?>
