<?php

require('key_file');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($jodds_key));

        $dbh = mysqli_connect('localhost','chiefaction',$db_password,'chiefaction');
        if ($dbh) {
            $active_bets = getActiveBets();
            if (isset($active_bets) && count($active_bets)) {
                foreach ($active_bets as $bet) {
                    curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/results/".$bet['event_id']);
                    $res = curl_exec($ch);
                    $result = json_decode($res);
    //                print_r($bet['event_id']);
    //                print_r($result);
                    if (isset($result) && count($result) && isset($result[0]->Final) && $result[0]->Final) {
                        $orig_home_score = $home_score = $result[0]->HomeScore;
                        $orig_away_score = $away_score = $result[0]->AwayScore;
                        $bet_result = null;
                        $set_scores = null;
                        if ($bet['BetType'] == 'moneyline') {
                            if (isset($result[0]->TennisFinalScore)) {
                                $set_scores = explode('-', $result[0]->TennisFinalScore);
                                $away_score = $set_scores[0];
                                $home_score = $set_scores[1];
                            }
                            if ($bet['team'] == 'home') {
                                if ($home_score > $away_score) {
                                    $bet_result = 'win';
                                } else if ($home_score == $away_score) {
                                    if ($bet['sport'] == 'soccer') {
                                        $bet_result = 'lose';
                                    } else {
                                        $bet_result = 'push';
                                    }
                                } else {
                                    $bet_result = 'lose';
                                }
                            } else if ($bet['team'] == 'draw') {
                                if ($away_score == $home_score) {
                                    $bet_result = 'win';
                                } else {
                                    $bet_result = 'lose';
                                }
                            } else {
                                if ($away_score > $home_score) {
                                    $bet_result = 'win';
                                } else if ($away_score == $home_score) {
                                    if ($bet['sport'] == 'soccer') {
                                        $bet_result = 'lose';
                                    } else {
                                        $bet_result = 'push';
                                    }
                                } else {
                                    $bet_result = 'lose';
                                }
                            }
                        }
                        if ($bet['BetType'] == 'spread') {
                            if ($bet['team'] == 'home') {
                                if (($home_score+$bet['PointSpread']) > $away_score) {
                                    $bet_result = 'win';
                                } else if (($home_score+$bet['PointSpread']) == $away_score) {
                                    $bet_result = 'push';
                                } else {
                                    $bet_result = 'lose';
                                }
                            } else {
                                if (($away_score+$bet['PointSpread']) > $home_score) {
                                    $bet_result = 'win';
                                } else if (($away_score+$bet['PointSpread']) == $home_score) {
                                    $bet_result = 'push';
                                } else {
                                    $bet_result = 'lose';
                                }
                            }
                        }
                        if ($bet['BetType'] == 'under') {
                            if (($home_score + $away_score) < $bet['TotalNumber']) {
                                $bet_result = 'win';
                            } else if (($home_score + $away_score) == $bet['TotalNumber']) {
                                $bet_result = 'push';
                            } else {
                                $bet_result = 'lose';
                            }
                        }
                        if ($bet['BetType'] == 'over') {
                            if (($home_score + $away_score) < $bet['TotalNumber']) {
                                $bet_result = 'lose';
                            } else if (($home_score + $away_score) == $bet['TotalNumber']) {
                                $bet_result = 'push';
                            } else {
                                $bet_result = 'win';
                            }
                        }
                        $total_payout = 0.00;
                        $payout = 0.00;
                        if ($bet_result == 'win' || $bet_result == 'push') {
                            if ($bet_result == 'win') {
                                if ($bet['BetType'] == 'moneyline') {
                                    $bet_line = $bet['MoneyLine'];
                                }
                                if ($bet['BetType'] == 'spread') {
                                    $bet_line = $bet['SpreadLine'];
                                }
                                if ($bet['BetType'] == 'over') {
                                    $bet_line = $bet['OverLine'];
                                }
                                if ($bet['BetType'] == 'under') {
                                    $bet_line = $bet['UnderLine'];
                                }
                                if ($bet_line > 0.00) {
                                    $payout = $bet['bet_amt']*$bet_line/100.00;
                                } else {
//                                    if ($bet['MoneyLine'] != 0) {
                                        $payout = $bet['bet_amt']*100.00/abs($bet_line);
//                                    }
                                }
                            }
                            $total_payout = $bet['bet_amt'] + $payout;
                        }
                        updateBet($bet, $bet_result, $payout, $orig_home_score, $orig_away_score, $set_scores,$dbh);
                        if ($bet['parlay_id'] == 0) {
                            updateBalanceAndLog($total_payout, $bet, $bet_result, $dbh);
                        }
                    }
                }
            }
            $parlays = getActiveParlays($dbh);
            if (count($parlays)) {
                foreach ($parlays as $parlay_id) {
                    $parlay_bets = getParlayBets($dbh, $parlay_id);
                    $parlay_result = 'tbd';
                    $lose = 0;
                    $tbd = 0;
                    $factor = 1.0;
                    $bet_amt = 0.00;
                    $user_id = 0;
                    $juice = 0.9;
                    $win = 0;
                    if (count($parlay_bets)) {
                        foreach ($parlay_bets as $parlay_bet) {
                            $bet_amt = $parlay_bet->bet_amt; // all bet amounts for each parlay bet should be the same
                            $user_id = $parlay_bet->user_id; // should be the same user_id for each parlay_id
                            if ($parlay_bet->bet_result == 'lose') {
                                $lose++;
                            }
                            if ($parlay_bet->bet_result == 'win') {
                                $factor *= determineParlayFactor($parlay_bet);
                                $win++;
                            }
                            if ($parlay_bet->bet_result == 'tbd') {
                                $tbd++;
                            }
                        }
                        if ($lose) {
                            updateParlayResult($dbh, $parlay_id, 'lose', $factor);
                            $bet_info = array(
                                    'user_id'       =>  $user_id,
                                    'event_id'      =>  $parlay_id,
                                    'bet_amt'       =>  $bet_amt,
                                    );
                            updateBalanceAndLog(0.00, $bet_info, 'lose', $dbh);
                            deactivateParlay($dbh, $parlay_id);
                        }
                        if ($lose == 0 && $tbd == 0) { // win
                            $factor -= 1.00; // remove inital bet
                            if (count($parlay_bets) == 1 || $win == 1) {  // this is now only one bet, a straight up bet
                                $juice = 1.00;
                            } else {
                                $juice = getJuice($dbh, $parlay_id);
                            }
                            $total_payout = $bet_amt * $factor * $juice + $parlay_bet->bet_amt;
                            updateParlayResult($dbh, $parlay_id, 'win', $factor, $juice);
                            $bet_info = array(
                                    'user_id'       =>  $user_id,
                                    'event_id'      =>  $parlay_id,
                                    'bet_amt'       =>  $bet_amt,
                                    );
                            updateBalanceAndLog($total_payout, $bet_info, 'win', $dbh);
                            deactivateParlay($dbh, $parlay_id);
                        }
                    }
                }
            }
            mysqli_close($dbh);
            curl_close($ch);
        }

    function updateBalanceAndLog($total_payout = 0.00, $bet, $bet_result, $dbh) {
        $query = 'update users set balance=balance+'.$total_payout.' where user_id='.$bet['user_id'];
        mysqli_query($dbh, $query);
        if ($bet_result == 'win' || $bet_result == 'push') {
            $query = 'insert into realized values(0,"'.date('Y-m-d H:i:s', time()).'","out","'.$bet['event_id'].'",'.$bet['bet_amt'].','.$total_payout.','.$bet['user_id'].',1)';
        } else {
            $query = 'insert into realized values(0,"'.date('Y-m-d H:i:s', time()).'","in","'.$bet['event_id'].'",'.$bet['bet_amt'].',0.00,'.$bet['user_id'].',1)';
        }
        mysqli_query($dbh, $query);
    }

    function getActiveBets() {
        $bets = array();
        $dbh = mysqli_connect('localhost','chiefaction',$db_password,'chiefaction');
        $query = 'select * from bets where active=1';
        $res = mysqli_query($dbh, $query);
        while ($row = mysqli_fetch_assoc($res)) {
            $bets[] = $row;
        }
        mysqli_close($dbh);
        return $bets;
    }

    function updateBet($bet, $bet_result, $payout, $home_score, $away_score, $set_scores, $dbh) {
        $query = 'update bets set bet_result="'.$bet_result.'" where bet_id='.$bet['bet_id'];
        mysqli_query($dbh, $query);
        if (mysqli_affected_rows($dbh) < 1) {
            echo 'datbase error: '.$query."\n";
        }
        $query = 'update bets set active=0 where bet_id='.$bet['bet_id'];
        mysqli_query($dbh, $query);
        if (mysqli_affected_rows($dbh) < 1) {
            echo 'database error: '.$query."\n";
        }
        $query = 'update bets set payout='.$payout.' where bet_id='.$bet['bet_id'];
        mysqli_query($dbh, $query);
        if (mysqli_affected_rows($dbh) < 1) {
            echo 'database error: '.$query."\n";
        }

        if (isset($set_scores) && count($set_scores) == 2) {
            $tennis_away = $set_scores[0];
            $tennis_home = $set_scores[1];
            if ($tennis_home>$tennis_away) {
                $winner = $bet['HomeTeam'];
            } else {
                $winner = $bet['AwayTeam'];
            }
        } else if ($home_score>$away_score) {
            $winner = $bet['HomeTeam'];
        } else if ($home_score == $away_score) {
            $winner = 'tie';
        } else {
            $winner = $bet['AwayTeam'];
        }
        $query = 'update bets set actual_winner="'.$winner.'" where bet_id='.$bet['bet_id'];
        mysqli_query($dbh, $query);
        if (mysqli_affected_rows($dbh) < 1) {
            echo 'database error: '.$query."\n";
        }
        $query = 'update bets set actual_home_score='.$home_score.' where bet_id='.$bet['bet_id'];
        mysqli_query($dbh, $query);
        if (mysqli_affected_rows($dbh) < 1) {
            echo 'database error: '.$query."\n";
        }
        $query = 'update bets set actual_away_score='.$away_score.' where bet_id='.$bet['bet_id'];
        mysqli_query($dbh, $query);
        if (mysqli_affected_rows($dbh) < 1) {
            echo 'database error: '.$query."\n";
        }
        if (isset($set_scores) && count($set_scores)==2) {
            $query = 'update bets set status="('.$set_scores[0].'-'.$set_scores[1].') '.date('Y-m-d H:i:s', time()).'" where bet_id='.$bet['bet_id'];
        } else {
            $query = 'update bets set status="'.date('Y-m-d H:i:s', time()).'" where bet_id='.$bet['bet_id'];
        }
        mysqli_query($dbh, $query);
        if (mysqli_affected_rows($dbh) < 1) {
            echo 'database error: '.$query."\n";
        }
        
    }

    function getActiveParlays($dbh) {
        $parlay_ids = array();
        $query = 'select parlay_id from parlay where active=1';
        $res = mysqli_query($dbh, $query);
        while ($row = mysqli_fetch_row($res)) {
            $parlay_ids[] = $row[0];
        }
        return $parlay_ids;
    }

    function getParlayBets($dbh, $parlay_id) {
        $parlay_bets = array();
        $query = 'select * from bets where parlay_id='.$parlay_id.' and manually_removed=0';
        $res = mysqli_query($dbh, $query);
        while ($row = mysqli_fetch_object($res)) {
            $parlay_bets[] = $row;
        }
        return $parlay_bets;
    }

    function determineParlayFactor($bet) {
        switch ($bet->BetType) {
            case 'spread':
                $line = $bet->SpreadLine;
                break;
            case 'moneyline':
                $line = $bet->MoneyLine;
                break;
            case 'over':
                $line = $bet->OverLine;
                break;
            case'under':
                $line = $bet->UnderLine;
                break;
            default:break;
        }
        if ($line < 0) {
            $factor = (100.0 - $line)/abs($line);
        } else {
            $factor = (100.0 + $line)/100.0;
        }
        return $factor;
    }

    function updateParlayResult($dbh, $parlay_id, $result = '', $factor = 1.0, $juice = 1.0) {
        $query = 'update parlay set parlay_result="'.$result.'", factor='.$factor.', juice='.$juice.' where parlay_id='.$parlay_id;
        mysqli_query($dbh, $query);
    }

    function getJuice($dbh, $parlay_id= 0) {
        $query = 'select juice from parlay where parlay_id='.$parlay_id;
        $res = mysqli_query($dbh, $query);
        $row = mysqli_fetch_row($res);
        $juice = $row[0];
        return $juice;
    }

    function deactivateParlay($dbh, $parlay_id) {
        $query = 'update parlay set active=0 where parlay_id='.$parlay_id;
        mysqli_query($dbh, $query);
    }
?>
