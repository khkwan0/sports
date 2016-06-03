<?php
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('JsonOdds-API-Key:14b51561-2341-4666-b654-b7ec84a2676a'));
    $dbh = mysqli_connect('localhost','chiefaction','khkwan0','chiefaction');
    if ($dbh) {

        $sport = 'nfl';
        $res = grabResults($ch, $sport);
        saveResults($dbh, $res);

        $sport = 'mlb';
        $res = grabResults($ch, $sport);
        saveResults($dbh, $res);

        $sport = 'nba';
        $res = grabResults($ch, $sport);
        saveResults($dbh, $res);

        $sport = 'soccer';
        $res = grabResults($ch, $sport);
        saveResults($dbh, $res);

        $sport = 'nhl';
        $res = grabResults($ch, $sport);
        saveResults($dbh, $res);

        $sport = 'ncaab';   
        $res = grabResults($ch, $sport);
        saveResults($dbh, $res);

        $sport = 'ncaaf';
        $res = grabResults($ch, $sport);
        saveResults($dbh, $res);

        $sport = 'mma';
        $res = grabResults($ch, $sport);
        saveResults($dbh, $res);

        $sport = 'tennis';
        $res = grabResults($ch, $sport);
        saveResults($dbh, $res);
    }
    mysqli_close($dbh);
    curl_close($ch);

function grabResults($ch, $sport) {
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/results/".$sport);
    $res = curl_exec($ch);
    return json_decode($res);
}

function saveResults($dbh, $res) {
    foreach ($res as $result) {
        $data = array(
                'event_id'      =>  $result->ID,
                'HomeScoore'    =>  $result->HomeScore,
                'AwayScore'     =>  $result->AwayScore,
                );
        if ($result->Final == 1) {
            $query = 'insert into archives values(0,';
            foreach ($data as $key=>$value) {
                $query .= '"'.$value.'",';
            }
            $query .= '"'.date('Y-m-d H:i:s', time()).'")';
            mysqli_query($dbh, $query);
        }
    }
}
?>
