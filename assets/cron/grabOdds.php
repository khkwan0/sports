<?php
    date_default_timezone_set('America/Los_Angeles');
    truncate_latest();
    $sport = 'mlb';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('JsonOdds-API-Key:14b51561-2341-4666-b654-b7ec84a2676a'));

    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);

    $sport = 'nfl';
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);

    $sport = 'ncaaf';
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);

    $sport = 'soccer';
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);

    $sport = 'nba';
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);

    $sport = 'ncaab';
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);

    $sport = 'nhl';
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);

    $sport = 'mma';
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);

/*  stopped via request from Leon 28Jan2016
    $sport = 'tennis';
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);
    */

    curl_close($ch);

    function truncate_latest() {
        $dbh = mysqli_connect('localhost','chiefaction','khkwan0','chiefaction');
        $query = 'truncate latest';
        mysqli_query($dbh, $query);
        mysqli_close($dbh);
    }

    function saveOdds($odds, $sport = '') {
        if ($sport) {
            if (isset($odds) && count($odds)) {
                foreach($odds as $event) {
                    $data = array(
                            'id'                    =>  $event->Odds[0]->ID,
                            'event_id'              =>  $event->ID,
                            'sport'                 =>  $event->Sport,
                            'MatchTime'             =>  date('Y-m-d H:i:s',strtotime($event->MatchTime)),
                            'HomeTeam'              =>  $event->HomeTeam,
                            'AwayTeam'              =>  $event->AwayTeam,
                            'MoneyLineHome'         =>  $event->Odds[0]->MoneyLineHome,
                            'MoneyLineAway'         =>  $event->Odds[0]->MoneyLineAway,
                            'PointSpreadHome'       =>  $event->Odds[0]->PointSpreadHome,
                            'PointSpreadAway'       =>  $event->Odds[0]->PointSpreadAway,
                            'PointSpreadHomeLine'       =>  $event->Odds[0]->PointSpreadHomeLine,
                            'PointSpreadAwayLine'       =>  $event->Odds[0]->PointSpreadAwayLine,
                            'OverLine'                  =>  $event->Odds[0]->OverLine,
                            'UnderLine'                 =>  $event->Odds[0]->UnderLine,
                            'DrawLine'                  =>  $event->Odds[0]->DrawLine,
                            'timestamp'                 =>  date('Y-m-d H:i:s', time()),
                            );
                    $dbh = mysqli_connect('localhost','chiefaction','khkwan0','chiefaction');
                    if ($dbh) {
                        $query2 = 'insert into latest values(';
                        $query = 'insert into odds values(';
                        $cnt = 1;
                        foreach ($data as $key=>$value) {
                            if ($cnt<sizeof($data)) {
                                $query .= '"'.$value.'",';
                                $query2 .= '"'.$value.'",';
                            } else {
                                $query .= '"'.$value.'"';
                                $query2 .= '"'.$value.'"';
                            }
                            $cnt++;
                        }
                        $query2 .= ',0,'.$event->Odds[0]->TotalNumber.')';
                        $query .= ',0,'.$event->Odds[0]->TotalNumber.')';
                        $query .= ' on duplicate key update MoneyLineHome="'.$data['MoneyLineHome'].'"'; 
                        $query .= ', MoneyLineAway="'.$data['MoneyLineAway'].'"';
                        $query .= ', PointSpreadHome="'.$data['PointSpreadHome'].'"';
                        $query .= ', PointSpreadAway="'.$data['PointSpreadAway'].'"';
                        $query .= ', PointSpreadHomeLine="'.$data['PointSpreadHomeLine'].'"';
                        $query .= ', PointSpreadAwayLine="'.$data['PointSpreadAwayLine'].'"';
                        $query .= ', OverLine="'.$data['OverLine'].'"';
                        $query .= ', UnderLine="'.$data['UnderLine'].'"';
                        $query .= ', DrawLine="'.$data['DrawLine'].'"';
                        $query .= ', TotalNumber="'.$event->Odds[0]->TotalNumber.'"';
                        $query .= ', timestamp="'.date('Y-m-d H:i:s', time()).'"';
                        $res = mysqli_query($dbh, $query);
                        $res2 = mysqli_query($dbh, $query2);
                    }
                    mysqli_close($dbh);
                }
            } else {
                echo 'no odds for '.$sport;
            }
        }
    }
?>
