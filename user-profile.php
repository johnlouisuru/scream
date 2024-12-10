<?php 
include_once("config.php");
session_start();
require('fb_time_ago.php');

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teach Me How To Scream</title>
    <link href="style.css" rel="stylesheet">
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
</head>
<body>
    <!-- Navigation Bar -->
    <div id="nav-bar">
        <div id="banner">Teach Me How To Scream AI</div>
        <a href="logout.php"><img id="paypal-button" src="gfx/paypal.png" alt="PayPal Button"></a>
    </div>

    <!-- Profile Section -->
    <div id="profile-section">
        <img id="profile-picture" src="<?=@$_SESSION['image']?>" alt="Profile Picture">
        
        <div id="bio">
            <?php 
            $query = mysqli_query($conn,"select * from user_detail WHERE user_id=$_SESSION[user_id]");
            $result = $query->fetch_assoc();  
            
            ?>
            <div id="name-info"><?=@$_SESSION['username']?></div>
            <div id="band-info">Bands: <?=$result['band']?></div>
            <div id="bio-info">
                <?=$result['bio']?>
            </div>
        </div>
        
        <div id="ad-1">
            <img style="width: auto; height: 90%;" src="gfx/ad.png" alt="Advertisement">
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

                ?>
                
            </div>
        </div>

        <div id="social" class="tab-content">
            <h2>Social</h2>
            <div class="social-thumbnails">
                <a href="https://www.facebook.com/" target="_blank"><img src="gfx/facebook.png" alt="Facebook"></a>
                <a href="https://www.instagram.com/" target="_blank"><img src="gfx/instagram.png" alt="Instagram"></a>
                <a href="https://www.twitter.com/" target="_blank"><img src="gfx/twitter.png" alt="Twitter"></a>
                <a href="https://www.youtube.com/" target="_blank"><img src="gfx/youtube.png" alt="YouTube"></a>
                <a href="https://www.soundcloud.com/" target="_blank"><img src="gfx/soundcloud.png" alt="SoundCloud"></a>
                <a href="https://www.spotify.com/" target="_blank"><img src="gfx/spotify.png" alt="Spotify"></a>
            </div>
        </div>

        <!-- Prompt Section -->
        <div id="prompt-container" style="display: none;">
            <input type="hidden" value="<?=$_SESSION['user_id']?>" id="profile_owner">
            <input id="message-input" type="text" placeholder="Type a message..." />
            <button id="send-button">Send</button>
        </div>
    </div>

    <script>
        // This is to save the comment on the database
        $(document).ready(function() {
                
                
                   
            });
        //END

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
    </script>
</body>
</html>
