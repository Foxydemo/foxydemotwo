<?php

function check_subscriptionDate(){


    $start_date= strtotime('2022-02-22 05:20:00');
    $end_date= strtotime('2022-02-22 05:20:00');  

    $dueDate = ($end_date - $start_date)/60/60/24;
    $decimal=$dueDate-floor($dueDate);
    $hours=$decimal*24;
    $decihours=$hours-floor($hours);
    $mins=$decihours*60;
    $decimins=$mins-floor($mins);
    $seconds=round($decimins*60);


    if($dueDate>0){ //for active subscription, displays remaining days and time.
        return "Remaining Days: ".(int)$dueDate.' days, '.(int)$hours.' hours, '.(int)$mins. ' minutes and '.$seconds.' seconds.';
        // return $dueDate;
    }
    elseif($dueDate<=0){ //for expired subscription, displays 0, 0 means expired subscription.
        
        return 0;
    }
    
    
}