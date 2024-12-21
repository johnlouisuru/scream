// Function to send a chat message


	// Add event listeners

	
		let currentSocialLink = null; // Track the selected social media link

		// Open the social link modal
		function openSocialLinkModal(anchorElement) {
			currentSocialLink = anchorElement; // Save the clicked anchor element
			const socialLinkModal = document.getElementById('social-link-modal');
			const socialLinkInputField = document.getElementById('social-link-input');

			// Pre-fill the input field with the current link
			socialLinkInputField.value = anchorElement.href;

			// Show the modal
			socialLinkModal.style.display = 'flex';
		}

		// Close the social link modal
		function closeSocialLinkModal() {
			const socialLinkModal = document.getElementById('social-link-modal');
			socialLinkModal.style.display = 'none';
		}

		// Save the updated social media link
		function saveSocialLinkUpdate() {
			const socialLinkInputField = document.getElementById('social-link-input');

			if (currentSocialLink) {
				// Update the href attribute of the selected link
				currentSocialLink.href = socialLinkInputField.value;
			}

			// Close the modal
			closeSocialLinkModal();
		}

		// Attach the openSocialLinkModal function to each social media link
		document.querySelectorAll('.social-thumbnails a').forEach(anchor => {
			anchor.addEventListener('click', (event) => {
				event.preventDefault(); // Prevent the link's default behavior
				openSocialLinkModal(anchor); // Open the modal
			});
		});
		
		

	
		let currentAnchor = null; // To track the link being edited or added

		// Open popup for editing/adding videos
		function openPopup(anchor) {
			currentAnchor = anchor; // Save the current anchor element
			const popup = document.getElementById('popup-modal');
			const inputUrl = document.getElementById('youtube-url'); // YouTube URL input
			const inputTitle = document.getElementById('video-title'); // Video title input

			// Pre-fill fields if editing
			if (anchor && anchor.dataset && anchor.dataset.youtubeId) {
				inputUrl.value = `https://www.youtube.com/watch?v=${anchor.dataset.youtubeId}`;
				inputTitle.value = anchor.dataset.videoTitle || ''; // Set existing title or leave blank
			} else {
				inputUrl.value = ''; // Clear the input for new additions
				inputTitle.value = ''; // Clear the title input
			}

			// Show the popup
			popup.style.display = 'flex';
		}

		// Close popup
		function closePopup() {
			const popup = document.getElementById('popup-modal');
			popup.style.display = 'none';
		}

		// Save or update video
		function saveLink() {
			const inputUrl = document.getElementById('youtube-url').value.trim();
			const inputTitle = document.getElementById('video-title').value.trim();

			// Validate the YouTube URL
			const videoIdMatch = inputUrl.match(/(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([\w-]+)/) 
							  || inputUrl.match(/(?:https?:\/\/)?(?:www\.)?youtu\.be\/([\w-]+)/);

			if (!videoIdMatch) {
				alert('Invalid YouTube URL. Please provide a valid link.');
				return;
			}

			const videoId = videoIdMatch[1];

			if (currentAnchor) {
				// Update existing video if editing
				currentAnchor.dataset.youtubeId = videoId; // Save video ID in the dataset
				currentAnchor.dataset.videoTitle = inputTitle; // Save title in the dataset
				const iframe = currentAnchor.querySelector('iframe');
				const titleDiv = currentAnchor.querySelector('.video-title');

				if (iframe) iframe.src = `https://www.youtube.com/embed/${videoId}`;
				if (titleDiv) titleDiv.textContent = inputTitle || 'New Tutorial'; // Default title if empty
			} else {
				// Add new video if adding
				const tutorialsContainer = document.getElementById('tutorials');
				const newVideoDiv = document.createElement('div');
				newVideoDiv.style.marginLeft = "25px";
				newVideoDiv.className = 'video-thumbnail';
				newVideoDiv.innerHTML = `
					<iframe class="video-youtube" 
							src="https://www.youtube.com/embed/${videoId}" 
							title="YouTube video player" 
							frameborder="0" 
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
							referrerpolicy="strict-origin-when-cross-origin" 
							allowfullscreen>
					</iframe>
					<div class="video-title">${inputTitle || 'New Tutorial'}</div>
				`;
				tutorialsContainer.appendChild(newVideoDiv);

				var formData = {
                    new_link : inputUrl,
					new_title: inputTitle
                };
                $.ajax({
                    type: "POST",
                    url: "add-link.php",
                    data: formData,
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    console.log(data['message']);
                });


			}

			// Close the popup
			closePopup();
		
		}

		// Attach click event to the "Add Video" button
		document.getElementById('add-video').addEventListener('click', (event) => {
			event.preventDefault();
			openPopup(null); // Pass null for adding a new video
		});
		
		function moveCursorToEnd() {
            const textarea = document.getElementById('bio-form');
            const textLength = textarea.value.length;

            // Set the cursor position to the end of the text
            textarea.selectionStart = textLength;
            textarea.selectionEnd = textLength;

            // Focus the textarea to make the cursor visible
            textarea.focus();
        }
		
		function updateBio(x) {
			//alert('ypoiu clicked!');
			let currentBio = document.getElementById('bio-info');
			let buttonContainer = document.getElementById('save-discard-container');
			let profileChange = document.getElementById('profile-change');

			if (x == 0) {
				// When profile change button is clicked, make bio editable
				currentBio.innerHTML = '<textarea id="bio-form" maxlength="478" rows="5" cols="40">' + currentBio.innerHTML + '</textarea>';
				buttonContainer.innerHTML = '<button class="save-discard-buttons" onclick="updateBio(2);">Save</button><button class="save-discard-buttons" onclick="updateBio(3);">Discard</button>';
				profileChange.onclick = '#'; // Disable further editing until save or discard
				moveCursorToEnd();
			}

			if (x == 2) {
				// Save changes
				let newBioText = document.getElementById('bio-form').value; // Get updated text
				let mess_holder;
				currentBio.innerHTML = newBioText; // Replace bio with updated text
				buttonContainer.innerHTML = ''; // Remove save and discard buttons
				profileChange.onclick = function() { updateBio(0); }; // Re-enable editing by clicking profile change
				var formData = {
                    mess_holder : newBioText
                };
                $.ajax({
                    type: "POST",
                    url: "update-bio.php",
                    data: formData,
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    console.log(data['message']);
                });
			}

			if (x == 3) {
				// Discard changes
				let originalBio = 'Alex Terrible is known for his powerful and intense vocal techniques, pioneering extreme vocal styles in metal music. With a deep passion for metal, he has pushed the boundaries of vocal performance, incorporating various screaming and growling techniques that captivate audiences worldwide. This is placeholder text describing his background, achievements, and influence on the genre.'; // Reset to original bio
				currentBio.innerHTML = originalBio; // Set the bio back to original text
				buttonContainer.innerHTML = ''; // Remove save and discard buttons
				profileChange.onclick = function() { updateBio(0); }; // Re-enable editing by clicking profile change
			}
		}
        // Get Elements
        const profilePicture = document.getElementById('profile-picture');
        const fileInput = document.getElementById('fileInput');
        const overlay = document.getElementById('overlay');
        const cropperModal = document.getElementById('cropper-modal');
        const imageToCrop = document.getElementById('image-to-crop');
        const saveButton = document.getElementById('save-cropped-image');
        const discardButton = document.getElementById('discard-cropped-image');
		
        let cropper;  // Cropper.js instance

        // Open file input when clicking on the profile picture
        profilePicture.addEventListener('click', function(event) {
            event.preventDefault();
            fileInput.click();
        });

        // Handle file input change event (image selected)
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                // Create an object URL for the selected file
                const url = URL.createObjectURL(file);

                // Show the modal and image
                overlay.style.display = 'block';
                cropperModal.style.display = 'block';
                imageToCrop.src = url;

                // Initialize Cropper.js once the image is loaded
                imageToCrop.onload = function() {
                    if (cropper) {
                        cropper.destroy();  // Destroy any existing cropper instance
                    }
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 350 / 300,  // Maintain the same aspect ratio as the profile picture
                        viewMode: 1,  // Contain the image in the canvas
                        minCropBoxWidth: 100,
                        minCropBoxHeight: 100,
                        cropBoxResizable: true,
                        cropBoxMovable: true,
                    });
                };
            }
        });

        // Save the cropped image and update the profile picture
        saveButton.addEventListener('click', function() {
            const croppedCanvas = cropper.getCroppedCanvas();
            const croppedImageURL = croppedCanvas.toDataURL();

            // Update the profile picture with the cropped image
            profilePicture.src = croppedImageURL;

            // Close the modal
            closeModal();
        });

        // Discard the changes and close the modal
        discardButton.addEventListener('click', function() {
            // Close the modal without saving
            closeModal();
        });

        // Close the modal and reset the overlay
        function closeModal() {
            overlay.style.display = 'none';
            cropperModal.style.display = 'none';
            if (cropper) {
                cropper.destroy();  // Clean up cropper instance
            }
        }
		
		
        function showTab(tabId) {
            const tabContents = document.querySelectorAll('.tab-content');
            const promptContainer = document.getElementById("prompt-container");

            tabContents.forEach(content => content.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');

            // Show or hide the prompt based on the active tab
            if (tabId === "questions") {
                promptContainer.style.display = "flex";
            } else {
                promptContainer.style.display = "none";
            }
        }

        function formatTime(hours, minutes) {
            const period = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            return `${hours}:${minutes.toString().padStart(2, '0')} ${period}`;
        }

        function sendMessage() {
            const message = document.getElementById("message-input").value;
            //const profile_owner = document.getElementById("profile_owner").value;
            if (message.trim()) {
                const container = document.getElementById("conversation-container");
                const now = new Date();
                const hours = now.getHours();
                const minutes = now.getMinutes();
                const formattedTime = formatTime(hours, minutes);

                const userMessage = document.createElement("div");
                userMessage.className = "message user-message";
                userMessage.innerHTML = `
                    <div>
                        <div class="user-name"><?=$_SESSION['username']?><span class="timestamp">${formattedTime}</span></div>
                        <div class="message-bubble">${message}</div>
                    </div>
                    <div class="profile-picture-circle">
                        <img src="<?=$_SESSION['image']?>" alt="User Profile">
                    </div>
                `;
                container.prepend(userMessage);
                document.getElementById("message-input").value = "";
            }
            var formData = {
                    message : message
                };
                $.ajax({
                    type: "POST",
                    url: "add-qs.php",
                    data: formData,
                    dataType: "json",
                    encode: true,
                }).done(function(data) {
                    console.log(data['message']);
                });
        }

        document.getElementById("send-button").addEventListener("click", sendMessage);

        document.getElementById("message-input").addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                sendMessage();
            }
        });