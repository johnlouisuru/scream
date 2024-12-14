<?php

include('config.php');
//$message_holder = "'".$_POST['message']."'";
$is_read = 0;
session_start();
$data = [];

	$query = $conn->prepare("INSERT INTO question(message,profile_owner,other_id,is_read)
												VALUES(?,?,?,?)");
                            // VALUES('$_POST[message]','$_SESSION[user_id]','$_SESSION[user_id]','0')");
	$query->bind_param("ssss", $_POST['message'],$_SESSION['user_id'],$_SESSION['user_id'], $is_read);
	
	if($query->execute()){
    $data['message'] = 'Message Sent.';
	}
	else {
		$data['message'] = 'Message Not Sent.';
	}
	echo json_encode($data);
?>