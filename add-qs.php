<?php

include('config.php');

session_start();
$data = [];

	$query2 = mysqli_query($conn,"INSERT INTO question(message,profile_owner,other_id,is_read)
                            VALUES('$_POST[message]','$_SESSION[user_id]','$_SESSION[user_id]','0')");
	if($query2){
    $data['message'] = 'Message Sent.';
	}
	else {
		$data['message'] = 'Message Not Sent.';
	}
	echo json_encode($data);
?>