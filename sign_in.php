<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In / Sign Up</title>
    <style>
        body {
            margin: 0;
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            width: 100%;
            max-width: 500px;
            padding: 30px;
            background-color: #131313;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.5);
            text-align: center;
        }

        .container input, .container select, .container button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 2px solid #ff0000;
            background-color: #000;
            color: #fff;
            font-size: 16px;
        }

        .container button:hover, .container input:hover, .container select:hover {
            border-color: #cc0000;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.7);
        }

        .container button:active, .container input:active, .container select:active {
            box-shadow: 0 0 15px rgba(255, 0, 0, 1);
        }

        #sign-in-form, #sign-up-form {
            display: block;
        }

        #sign-up-form {
            display: none;
        }

        .password-message {
            color: #f00;
            font-size: 12px;
            display: none;
        }

        .password-match-message {
            color: #f00;
            font-size: 12px;
            display: none;
        }

        .secure-password {
            color: #0f0;
            font-size: 12px;
            display: none;
        }

        .upload-section {
            margin-top: 20px;
            color: #fff;
        }

        .upload-section input[type="file"] {
            background-color: #ff0000;
            padding: 10px;
            cursor: pointer;
        }

        #image-preview {
            margin-top: 20px;
            max-width: 350px;
            max-height: 300px;
            width: auto;
            height: auto;
            border-radius: 10px;
        }

        #saved-message {
            display: none;
            color: #0f0;
            font-size: 18px;
            margin-top: 10px;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
</head>
<body>
    <div class="container">
        <!-- Sign-in Form -->
        <div id="sign-in-form">
            <h2>Sign In</h2>
            <form action="sign-up.php" method="POST">
                <input type="email" name="email" placeholder="Enter your email" required>
                <input type="password" name="password" placeholder="Enter your password" required>
                <button type="submit">Sign In</button>
            </form>
            <p>Don't have an account? <a href="javascript:void(0);" onclick="toggleSignUp()">Sign Up</a></p>
        </div>

        <!-- Sign-up Form -->
        <div id="sign-up-form">
            <h2>Sign Up</h2>
            <form action="sign-in.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="username" placeholder="Enter your username" required>
                <input type="email" name="email" placeholder="Enter your email" required>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
                <div id="password-message" class="password-message"></div>
                <div id="secure-password" class="secure-password" style="display:none;">Secure Password!</div>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                <div id="password-match-message" class="password-match-message"></div>
				<div class="upload-section">
                <input type="file" name="profile_picture" id="image-upload" accept="image/*" onchange="handleImageUpload(event)">
                <div>
                    <img id="image-preview" src="" alt="Image Preview">
                </div>
                <button type="button" id="crop-button" onclick="getCroppedImage()" style="display:none;">Crop Image</button>
                <p id="saved-message">Saved!</p>
				</div>
                <button type="submit" id="sign-up-button" disabled>Sign Up</button>
            </form>
            <p>Already have an account? <a href="javascript:void(0);" onclick="toggleSignIn()">Sign In</a></p>
            <p> <a href="./index/">Back to Homepage</a></p>

            <!-- Image Upload and Cropper -->

        </div>
    </div>
    <script>
        // Initialize cropper variable
        let cropper;

        // Function to handle file input and crop image
        function handleImageUpload(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const image = document.getElementById('image-preview');
                image.src = e.target.result;

                // Destroy previous cropper if any
                if (cropper) {
                    cropper.destroy();
                }

                // Initialize cropper
                cropper = new Cropper(image, {
                    aspectRatio: 350 / 300, // Set aspect ratio for cropping
                    viewMode: 1, // Set crop box to stay within the image
                    autoCropArea: 0.65, // Initial crop area
                    minContainerWidth: 350, // Minimum container size
                    minContainerHeight: 300, // Minimum container size
                });

                // Show the "Get Cropped Image" button
                document.getElementById('crop-button').style.display = 'block';
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }

        // Function to get the cropped image and display the result
        function getCroppedImage() {
            const canvas = cropper.getCroppedCanvas({
                width: 350,
                height: 300,
            });
            const imageData = canvas.toDataURL("image/jpeg");
            document.getElementById('image-preview').src = imageData;
            document.getElementById('image-upload').src = imageData;

            // Hide the "Get Cropped Image" button and show "Saved!"
            document.getElementById('crop-button').style.display = 'none';
            document.getElementById('saved-message').style.display = 'block';

            // Destroy the cropper instance after saving the image
            cropper.destroy();
        }

        function toggleSignUp() {
            document.getElementById('sign-in-form').style.display = 'none';
            document.getElementById('sign-up-form').style.display = 'block';
        }

        function toggleSignIn() {
            document.getElementById('sign-up-form').style.display = 'none';
            document.getElementById('sign-in-form').style.display = 'block';
        }

        // Password validation and password matching check
        function validatePassword() {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm-password').value;
            var passwordMessage = document.getElementById('password-message');
            var passwordMatchMessage = document.getElementById('password-match-message');
            var securePasswordMessage = document.getElementById('secure-password');
            var signUpButton = document.getElementById('sign-up-button');

            // Password strength check (at least 8 characters, 1 letter, 1 number, 1 special char)
            var passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordRegex.test(password)) {
                passwordMessage.style.display = 'block';
                passwordMessage.textContent = 'Password is weak. It must be at least 8 characters long, include a letter, a number, and a special character.';
                securePasswordMessage.style.display = 'none';
                signUpButton.disabled = true;
            } else {
                passwordMessage.style.display = 'none';
                securePasswordMessage.style.display = 'block';
                securePasswordMessage.textContent = 'Secure Password!';
                signUpButton.disabled = false;
            }

            // Check if passwords match
            if (password !== confirmPassword) {
                passwordMatchMessage.style.display = 'block';
                passwordMatchMessage.textContent = 'Passwords do not match!';
            } else {
                passwordMatchMessage.style.display = 'none';
            }
        }

        // Attach event listeners to password fields
        document.getElementById('password').addEventListener('input', validatePassword);
        document.getElementById('confirm-password').addEventListener('input', validatePassword);
    </script>
</body>
</html>
