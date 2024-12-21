<?php

include('config.php');
//$message_holder = "'".$_POST['message']."'";
$is_read = 0;
session_start();
$data = [];

	$query = $conn->prepare("UPDATE user_detail SET bio=? WHERE user_id=?");
                            // VALUES('$_POST[message]','$_SESSION[user_id]','$_SESSION[user_id]','0')");
	$query->bind_param("ss", $_POST['mess_holder'], $_SESSION['user_id']);
	
	if($query->execute()){
    $data['message'] = 'New Link Submitted';
	}
	else {
		$data['message'] = 'New Link Not Submitted.';
	}
	echo json_encode($data);
?>