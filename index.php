<?php

function oddEven($number) {
	
	if ($number % 2 == 1) {
		
		echo $number . " is odd";
	} else {
		echo $number . " is even";
		
	}
		
}

echo oddEven(5)

?>