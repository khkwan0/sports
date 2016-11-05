<?php
require 'key_file.php';
    date_default_timezone_set('America/Los_Angeles');
    truncate_latest();
    $sport = 'mlb';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($jodds_key));

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

/*  stopped via request from Leon 28Jan2016 */
/* restarted by Ken 03Agf2016 */
    $sport = 'tennis';
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);
//    */

    $sport = 'horse-racing';
    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/odds/".$sport);
    $res = curl_exec($ch);
    $odds = json_decode($res);
    saveOdds($odds, $sport);

    curl_close($ch);

    function truncate_latest() {
        global $db_password;
        $dbh = mysqli_connect('localhost','chiefaction',$db_password,'chiefaction');
        $query = 'truncate latest';
        mysqli_query($dbh, $query);
        mysqli_close($dbh);
    }

    function saveOdds($odds, $sport = '') {
        global $db_password;
        if ($sport) {
            if (isset($odds) && count($odds)) {
                foreach($odds as $event) {
                    $datetime = new DateTime(date('Y-m-d H:i:s', strtotime($event->MatchTime)), new DateTimeZone("Etc/GMT+1"));
                    $datetime->setTimezone(new DateTimeZone("America/Los_Angeles"));
                    if ($event->Odds[0]->OddType === 'Game' || !isset($event->Odds[0]->OddType)) {
                        $odd_type = 0;
                    } else {
                        $odd_type = 1;
                    }
                    $data = array(
                            'id'                    =>  $event->Odds[$odd_type]->ID,
                            'event_id'              =>  $event->ID,
                            'sport'                 =>  $event->Sport,
                            'MatchTime'             =>  $datetime->format('Y-m-d H:i:s'),
                            'HomeTeam'              =>  $event->HomeTeam,
                            'AwayTeam'              =>  $event->AwayTeam,
                            'MoneyLineHome'         =>  $event->Odds[$odd_type]->MoneyLineHome,
                            'MoneyLineAway'         =>  $event->Odds[$odd_type]->MoneyLineAway,
                            'PointSpreadHome'       =>  $event->Odds[$odd_type]->PointSpreadHome,
                            'PointSpreadAway'       =>  $event->Odds[$odd_type]->PointSpreadAway,
                            'PointSpreadHomeLine'       =>  $event->Odds[$odd_type]->PointSpreadHomeLine,
                            'PointSpreadAwayLine'       =>  $event->Odds[$odd_type]->PointSpreadAwayLine,
                            'OverLine'                  =>  $event->Odds[$odd_type]->OverLine,
                            'UnderLine'                 =>  $event->Odds[$odd_type]->UnderLine,
                            'DrawLine'                  =>  $event->Odds[$odd_type]->DrawLine,
                            'timestamp'                 =>  date('Y-m-d H:i:s', time()),
                            );
                    $dbh = mysqli_connect('localhost','chiefaction',$db_password,'chiefaction');
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
                        $query2 .= ',0,'.$event->Odds[$odd_type]->TotalNumber.')';
                        $query .= ',0,'.$event->Odds[$odd_type]->TotalNumber.')';
                        $query .= ' on duplicate key update MoneyLineHome="'.$data['MoneyLineHome'].'"'; 
                        $query .= ', MoneyLineAway="'.$data['MoneyLineAway'].'"';
                        $query .= ', PointSpreadHome="'.$data['PointSpreadHome'].'"';
                        $query .= ', PointSpreadAway="'.$data['PointSpreadAway'].'"';
                        $query .= ', PointSpreadHomeLine="'.$data['PointSpreadHomeLine'].'"';
                        $query .= ', PointSpreadAwayLine="'.$data['PointSpreadAwayLine'].'"';
                        $query .= ', OverLine="'.$data['OverLine'].'"';
                        $query .= ', UnderLine="'.$data['UnderLine'].'"';
                        $query .= ', DrawLine="'.$data['DrawLine'].'"';
                        $query .= ', TotalNumber="'.$event->Odds[$odd_type]->TotalNumber.'"';
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
