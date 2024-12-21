<?php 
include_once("config.php");
session_start();
require('fb_time_ago.php');
if(@$_SESSION['user_id'] != '' || !empty(@$_SESSION['user_id'])){
    //echo $_SESSION['user_id'];
}
else {
    header('Location: index/');
}

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teach Me How To Scream</title>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
    
</head>
<body>
    <!-- Navigation Bar -->
    <div id="nav-bar">
        <div id="banner">Teach Me How To Scream AI</div>
        <img id="paypal-button" src="gfx/paypal.png" alt="PayPal Button">
    </div>

    <!-- Profile Section -->
    <div id="profile-section">
		<a href="#" id="profile-picture-anchor">
        <img id="profile-picture" src="<?=$_SESSION['image']?>" alt="Profile Picture">
        </a>
    <!-- Overlay for the popup window -->
    <!-- Hidden File Input for Image Upload -->
    <input type="file" id="fileInput" accept="image/*">

    <!-- Modal for Image Cropping -->
    <div id="cropper-modal">
        <img id="image-to-crop" src="" alt="Image to crop">
        <div class="buttons">
            <button id="save-cropped-image">Save</button>
            <button id="discard-cropped-image">Discard</button>
        </div>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>
    <div id="bio">
    <a href="user-profile.php">Back ↩</a>
            <?php 
            $query = mysqli_query($conn,"select * from user_detail WHERE user_id=$_SESSION[user_id]");
            $result = $query->fetch_assoc();  
            if($result){ ?>
            
            <div id="name-info"><?=@$_SESSION['username']?></div><div id='save-discard-container'></div>
            <a><div id="band-info">Bands: <?=$result['band']?></div></a>
            <a href="#" id='profile-change' style="text-decoration: none;" onclick="javascript: updateBio(0);">
            <div id="bio-info">
                <?=$result['bio']?>
            </div>
            </a>
            <?php } else { ?>
            <div id="name-info"><?=@$_SESSION['username']?></div><div id='save-discard-container'></div>
            <div id="band-info">Bands: [N/A]</div>
            <a href="#" id='profile-change' style="text-decoration: none;" onclick="javascript: updateBio(0);">
            <div id="bio-info">
                [N/A]
            </div>
            </a>
            <?php } ?>
        </div>
        
        <!--    <div id="bio">
            <div id="name-info">Alex Terrible</div><div id='save-discard-container'></div>
            <a><div id="band-info">Bands: Slaughter to Prevail</div></a>
			<a href="#" id='profile-change' style="text-decoration: none;" onclick="javascript: updateBio(0);">
            <div id="bio-info">Alex Terrible is known for his powerful and intense vocal techniques, pioneering extreme vocal styles in metal music. With a deep passion for metal, he has pushed the boundaries of vocal performance, incorporating various screaming and growling techniques that captivate audiences worldwide. This is placeholder text describing his background, achievements, and influence on the genre.</div>
			</a>
        </div> -->
        <div id="ad-1">
            <img style="width: 100%; height: 100%;" src="gfx/ad.png" alt="Advertisement">
        </div>
    </div>

    <!-- Tabs Section -->
    <div id="tabs-container">
        <!-- Tab Buttons -->
        <div id="tab-buttons">
            <div class="tab-button" onclick="showTab('tutorials')">Tutorials</div>
            <div class="tab-button" onclick="showTab('questions')">Questions</div>
            <div class="tab-button" onclick="showTab('social')">Social</div>
        </div>

        <!-- Tab Content -->
        <div id="tutorials" class="tab-content">
            <h2>Tutorials</h2>
             <a class="video-thumbnail" id="add-video">
				<img src="gfx/add-button.png" alt="Add New Video">
			</a>
            <?php
                $get_tutorials = mysqli_query($conn,"SELECT * FROM tutorials WHERE user_id = '".$_SESSION['user_id']."'");
                if(mysqli_num_rows($get_tutorials)>= 1){ 
                    while($tuts = mysqli_fetch_array($get_tutorials)){
                    ?>
                    <div class="video-thumbnail">
                        <iframe class="video-youtube" src="<?=$tuts['yt_link']?>" title="<?=$tuts['yt_title']?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        <div class="video-title"><?=$tuts['yt_title']?></div>
                    </div>
                <?php 
                    }
                }
            ?>
        </div>
        
		<!-- Modal for Adding Video -->
	<div id="popup-modal" class="modal">
		<div class="modal-content">
			<h3>Add or Edit Video</h3>
			<label for="youtube-url">YouTube URL:</label>
			<input type="text" id="youtube-url" placeholder="Enter YouTube link here...">
			<label for="video-title">Video Title:</label>
			<input type="text" id="video-title" placeholder="Enter video title here...">
			<div class="modal-buttons">
				<button id="save-button" onclick="saveLink()">Save</button>
				<button id="cancel-button" onclick="closePopup()">Cancel</button>
			</div>
		</div>
	</div>
    <div id="questions" class="tab-content">
            <h2>Questions</h2>
            <div class="conversation-container" id="conversation-container">
            <?php
                $get_qs = mysqli_query($conn,"SELECT * FROM question WHERE profile_owner = '".$_SESSION['user_id']."' ORDER BY date DESC");
                if(mysqli_num_rows($get_qs)>= 1){ 
                    while($qs = mysqli_fetch_array($get_qs)){
                        $date_filed = date("d-M-Y H:i", strtotime($qs['date']));
                        if($_SESSION['user_id'] == $qs['other_id']){ //This is if Owner is the meesage
                        ?>
                            <div class="message artist-message">
                                <div class="profile-picture-circle">
                                    <img src="<?=$_SESSION['image']?>" alt="Artist Profile">
                                </div>
                                <div>
                                    <div class="artist-name"><?=$_SESSION['username']?> <span class="timestamp"><?=facebook_time_ago($date_filed)?></span></div>
                                    <div class="message-bubble"><?=$qs['message']?></div>
                                </div>
                            </div>
                        <?php
                        }
                        else { ?>
                        <div class="message user-message">
                            <div>
                                <div class="user-name">XGuest_UserX <span class="timestamp"><?=facebook_time_ago($date_filed)?></span></div>
                                <div class="message-bubble"><?=$qs['message']?></div>
                            </div>
                            <div class="profile-picture-circle">
                                <img src="gfx/q_user.png" alt="User Profile">
                            </div>
                        </div>
                        <?php

                        }
                    
                    }
                }
                else { ?>
                <div class="message user-message">
                    <p>No Questions/Message yet.</p>             
                <!-- <div>
                                <div class="user-name">XDummyX <span class="timestamp">Yesterday</span></div>
                                <div class="message-bubble">This is a Dummy Text...</div>
                            </div>
                            <div class="profile-picture-circle">
                                <img src="gfx/q_user.png" alt="User Profile">
                            </div> -->
                        </div>
            <?php } ?>

                
            </div>
        </div>

        <div id="social" class="tab-content">
            <h2>Social</h2>
			<div class="social-thumbnails">
				<a href="https://www.facebook.com/" target="_blank"><img src="gfx/facebook.png" alt="Facebook"></a>
				<a href="https://www.instagram.com/" target="_blank"><img src="gfx/instagram.png" alt="Instagram"></a>
				<a href="https://www.twitter.com/" target="_blank"><img src="gfx/x.png" alt="X"></a>
				<a href="https://www.youtube.com/" target="_blank"><img src="gfx/youtube.png" alt="YouTube"></a>
				<a href="https://www.soundcloud.com/" target="_blank"><img src="gfx/soundcloud.png" alt="SoundCloud"></a>
				<a href="https://www.spotify.com/" target="_blank"><img src="gfx/spotify.png" alt="Spotify"></a>
			</div>

			<!-- Modal for Editing Social Media Links -->
			<div id="social-link-modal" class="social-modal">
				<div class="social-modal-content">
					<h3>Edit Social Media Link</h3>
					<label for="social-link-input">Social Media URL:</label>
					<input type="text" id="social-link-input" placeholder="Enter new social media link">
					<div class="social-modal-buttons">
						<button id="save-social-link-button" onclick="saveSocialLinkUpdate()">Save</button>
						<button id="cancel-social-link-button" onclick="closeSocialLinkModal()">Cancel</button>
					</div>
				</div>
			</div>

        <!-- Prompt Section -->
        <div id="prompt-container" style="display: none;">
            <input id="message-input" type="text" placeholder="Type a message..." />
            <button id="send-button">Send</button>
        </div>
    </div>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
