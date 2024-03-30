#!/usr/bin/php
<?php 

//create loop to 100
for($i = 1; $i <= 100; $i++) {
    //check if divisible by 5 and 3 first
    if($i % 5 == 0 && $i % 3 == 0) {
        echo 'foobar,';
    }
    //check if divisible by 3 but not 5
    if($i % 3 == 0 && $i % 5 != 0) {
        echo 'foo,';
    }
    //check if divisible by 5 but not 3
    if($i % 3 != 0 && $i % 5 == 0) {
        echo 'bar,';
    }
    //echo numbers that arent divisible
    if($i % 3 != 0 && $i % 5 != 0) {
        echo $i.',';
    }
}

?>