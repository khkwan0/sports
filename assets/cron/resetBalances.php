<?php

$dbh = mysqli_connect('localhost','chiefaction','khkwan0','chiefaction');
if ($dbh) {
    $query = 'select * from users';
    $res = mysqli_query($dbh, $query);
    if (!$res) {
        echo mysqli_error($dbh);
    } else {
        while ($row = mysqli_fetch_object($res)) {
            // get any pending bet amounts
            $pending_bets = 0.00;
            $query = 'select sum(bet_amt) from bets where active=1 and manually_removed=0 and parlay_id=0 and user_id='.$row->user_id;
            $res2 = mysqli_query($dbh, $query);
            $row2 = mysqli_fetch_row($res2);
            $pending_bets = $row2[0];

            $query = 'select sum(bet_amt) from parlay where user_id='.$row->user_id.' and active=1';
            $res2 = mysqli_query($dbh, $query);
            $row2 = mysqli_fetch_row($res2);
            $pending_bets += $row2[0];

            $query = 'update users set balance='.($row->default_balance - $pending_bets).' where user_id='.$row->user_id;
            mysqli_query($dbh, $query);

            $query = 'insert into weekly_log values(0,';
            $query .= $row->user_id.',"';
            $query .= date('Y-m-d H:i:s', time()).'",';
            $query .= ($row->balance+$pending_bets).','.$row->default_balance.','.time().')';
            mysqli_query($dbh, $query);
        }
    }
}
mysqli_close($dbh);
?>
