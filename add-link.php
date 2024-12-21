<?php

include('config.php');
//$message_holder = "'".$_POST['message']."'";
$is_read = 0;
session_start();
$data = [];
$temp_link = '';

$whatIWant = substr($_POST['new_link'], strpos($_POST['new_link'], "=") + 1);
// $data['message'] = $whatIWant;
// echo json_encode($data);
// die();

$temp_link = "https://www.youtube.com/embed/".$whatIWant;
	$query = $conn->prepare("INSERT INTO tutorials(yt_link,yt_title,user_id)
												VALUES(?,?,?)");
                            // VALUES('$_POST[message]','$_SESSION[user_id]','$_SESSION[user_id]','0')");
	$query->bind_param("sss", $temp_link, $_POST['new_title'], $_SESSION['user_id']);
	
	if($query->execute()){
    $data['message'] = 'Message Sent.';
	}
	else {
		$data['message'] = 'Message Not Sent.';
	}
	echo json_encode($data);

    //<iframe width="1280" height="720" src="https://www.youtube.com/embed/xp1HeUx6z2o" 
?>