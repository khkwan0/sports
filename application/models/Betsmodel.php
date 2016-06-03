<?php

class Betsmodel extends CI_Model {
    function __construct() {
        parent::__construct();
    }

    function savebet($event_id, $locale, $bet_info) {
        $res = 0;
        $data = array(
                   'user_id'         =>  $this->session->userdata('user_id'),
                   'datetime'       =>  date('Y-m-d H:i:s', time()),
                   'bet_amt'        =>  $bet_info['bet'],
                   'team'           =>  $locale,
                   'MoneyLine'      =>  $bet_info['MoneyLine'],
                   'PointSpread'    =>  $bet_info['PointSpread'],
                   'id'             =>  $bet_info['id'],
                   'event_id'       =>  $event_id,
                   'HomeTeam'       =>  $bet_info['HomeTeam'],
                   'AwayTeam'       =>  $bet_info['AwayTeam'],
                   'MatchTime'      =>  $bet_info['MatchTime'],
                   'sport'          =>  $bet_info['sport'],
                   'BetType'        =>  $bet_info['BetType'],
                   'TotalNumber'    =>  $bet_info['TotalNumber'],
                   'SpreadLine'     =>  $bet_info['SpreadLine'],
                   'OverLine'       =>  $bet_info['OverLine'],
                   'UnderLine'      =>  $bet_info['UnderLine'],
                   'parlay_id'      =>  $bet_info['ParlayId'],
                );
        $this->db->insert('bets', $data);
        $res = $this->db->affected_rows();
        return $res;
    }

    function determineFactor($bet) {
        $line = 0.0;
        $factor = 0.0;
        switch($bet->BetType) {
            case 'moneyline': $line = $bet->MoneyLine; break;
            case 'spread': $line = $bet->SpreadLine; break;
            case 'over': $line = $bet->OverLine; break;
            case 'under': $line = $bet->UnderLine; break;
            default:break;
        }
        if ($line<0) {
            $factor = (100.0 - $line)/abs($line);
        } else {
            $factor = (100.0 + $line)/100.0;
        }
        return $factor;
    }

    function computeParlayFactor($parlay_id = 0) {
        $factor = 1.00;
        if ($parlay_id) {
            $this->db->from('bets');
            $this->db->where('parlay_id', $parlay_id);
            $this->db->where('manually_removed', 0);
            $query = $this->db->get();
            if ($query && $query->result()) {
                foreach ($query->result() as $row) {
                    if ($row->bet_result == 'lose') {
                        $factor = 0.00;
                        break;
                    }
                    if ($row->bet_result != 'push') {
                        $factor *= $this->determineFactor($row);
                    }
                }
            }
        }
        $factor -= 1.0;
        return $factor;
    }

    function updateParlayFactor($parlay_id = 0, $factor = 0.0) {
        $data = array(
                'factor'    =>$factor,
                );
        $this->db->where('parlay_id', $parlay_id);
        $this->db->update('parlay', $data);
    }

    function deleteBet($bet_id, $user_id) {
        $rv = array(
                'status'        =>  '0',
                'amt'           =>  0.00,
                );
        $this->db->from('bets');
        $this->db->where('bet_id', $bet_id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        $match_time = time();
        $orig_bet = null;
        if ($query && $query->result()) {
            $res = $query->result();
            $row = $res[0];
            $match_time = strtotime($row->MatchTime);
            $orig_bet = $row;
        }
        if (time() >= $match_time) {
            $rv['status'] = 'Match already started';
            return $rv;
        }
        $status = 'User removed '.date('Y-m-d H:i:s', time()). ' '.$this->input->ip_address();
        $data = array(
                'active'            =>  0,
                'manually_removed'  =>  1,
                'status'            => $status,
                );
        $this->db->where('bet_id', $bet_id);
        $this->db->where('user_id', $user_id);
        $this->db->update('bets', $data);
        if ($this->db->affected_rows()>0) {
            $rv['status'] = $status;
            $update_balance = true;
            if ($orig_bet->parlay_id) {  // only refund money if all other parylays are inactive/deleted
                $this->db->from('bets');
                $this->db->where('parlay_id', $orig_bet->parlay_id);
                $this->db->where('active', 1);
                $query = $this->db->get();
                if ($query && $query->result()) {
                    if ($query->num_rows() > 0) {
                        $update_balance = false;
                    }
                }
            }
            $factor = $this->computeParlayFactor($orig_bet->parlay_id);
            $this->updateParlayFactor($orig_bet->parlay_id, $factor);
            if ($update_balance) {
                $sql = 'update users set balance=balance+'.$orig_bet->bet_amt.' where user_id='.$user_id;
                $query = $this->db->query($sql); 
                $sql = 'update parlay set active=0 where parlay_id='.$orig_bet->parlay_id;
                $query = $this->db->query($sql); 
                $rv['amt'] = $orig_bet->bet_amt;
            } else {
                $rv['amt'] = 0.00;
            }
        }
        return $rv;
    }

    function getAllBetsByUserID($user_id = 0, $active_only = true, $unfinished = false, $inactive_only = false) {
        $res = null;
        $this->db->from('bets');
        if ($active_only) {
            $this->db->where('active', 1);
        }
        if ($inactive_only) {
            $this->db->where('active', 0);
        }
        if ($unfinished) {
            $this->db->where('bet_result like','tbd');
        }
        if ($user_id) {
            $this->db->where('user_id',$user_id);
        }
        $this->db->where('parlay_id !=',9999999);
        $this->db->order_by('datetime','desc');
        $query = $this->db->get();
        if ($query && $query->result()) {
            $rows = $query->result();
            $parlay_flag = array();
            foreach ($rows as $row) {
                /*
                $this->db->from('odds');
                $this->db->where('event_id',$row->event_id);
                $this->db->order_by('timestamp','desc');
                $this->db->limit(1);
                $query2 = $this->db->get();
                if ($query2 && $query2->result()) {
                    $res2 = $query2->result();
                    $row->HomeTeam = $res2[0]->HomeTeam;
                    $row->AwayTeam = $res2[0]->AwayTeam;
                    $row->MatchTime = $res2[0]->MatchTime;
                }
*/
                $row->parlay_result = 'na';
                $row->parlay_factor = 0.0;
                $row->juice = 0.0;
                if ($row->parlay_id) {
                    $this->db->from('parlay');
                    $this->db->where('parlay_id', $row->parlay_id);
                    if ($inactive_only) {
                        $this->db->where('active',0);
                    }
                    if ($active_only) {
                        $this->db->where('active',1);
                    }
                    $query2 = $this->db->get();
                    if ($query2 && $query2->result()) {
                        $temp = $query2->result();
                        $row->parlay_result = $temp[0]->parlay_result;
                        $row->parlay_factor = $temp[0]->factor;
                        $row->parlay_active = $temp[0]->active;
                        $row->juice = $temp[0]->juice;
                        if (!isset($parlay_flag[$row->parlay_id])) {
                            $res[] = $row;
                            $parlay_flag[$row->parlay_id] = 1;
                        }
                    }
                } else {
                    $res[] = $row;
                }
            }
        }
        return $res;
    }

    public function getPendingAmount($user_id = 0) {
        $total = 0.00;
        $this->db->from('bets');
        $this->db->where('active', 1);
        $this->db->where('user_id', $user_id);
        $this->db->where('parlay_id', 0);
        $this->db->select_sum('bet_amt');
        $query = $this->db->get();
        if ($query && $query->result()) {
            $res = $query->result();
            $total = $res[0]->bet_amt;
        }
        $this->db->from('parlay');
        $this->db->where('active', 1);
        $this->db->where('user_id', $user_id);
        $this->db->select('bet_amt');
        $query = $this->db->get();
        if ($query && $query->result()) {
            foreach ($query->result() as $row) {
                $total += $row->bet_amt;
            }
        }
        return $total;
    }

    public function getAllPending() {
        $res = array();
        $this->db->from('bets');
        $this->db->where('active', 1);
        $this->db->where('parlay_id', 0);
        $this->db->select_sum('bet_amt');
        $this->db->select('user_id');
        $this->db->group_by('user_id');
        $query = $this->db->get();
        if ($query && $query->result()) {
            foreach ($query->result() as $row) {
                $res[$row->user_id] = $row->bet_amt;
            }
        }
        $this->db->from('parlay');
        $this->db->where('active', 1);
        $this->db->select('user_id');
        $this->db->group_by('user_id');
        $this->db->select_sum('bet_amt');
        $query = $this->db->get();
        if ($query && $query->result()) {
            foreach ($query->result() as $row) {
                if (!isset($res[$row->user_id])) {
                    $res[$row->user_id] = $row->bet_amt;
                } else {
                    $res[$row->user_id] += $row->bet_amt;
                }
            }
        }
        return $res;
    }

    public function createParlay($user_id = 0, $bet_amt = 0.00, $parlay_juice = 0.00) {
        $parlay_id = 9999999;
        if ($user_id && $bet_amt>0.0) {
            $data = array(
                    'user_id'       =>  $user_id,
                    'datetime'      =>  date('Y-m-d H:i:s', time()),
                    'bet_amt'       =>  $bet_amt,
                    'juice'         =>  $parlay_juice,
                    );
            $this->db->insert('parlay', $data);
            $parlay_id = $this->db->insert_id();
        }
        return $parlay_id;
    }

    public function saveParlayFactor($parlay_id, $factor = 0.0) {
        $data = array(
                    'factor'    =>  $factor,
                );
        $this->db->where('parlay_id', $parlay_id);
        $this->db->update('parlay', $data);
        return $this->db->affected_rows();
    }

    public function getAllParlaysByParlayIDUserID($user_id = 0, $parlay_id = 0) {
        $res = array();
        if ($user_id && $parlay_id) {
            $this->db->from('bets');
            $this->db->where('parlay_id', $parlay_id);
            if (!$this->session->userdata('is_admin')) {
                $this->db->where('user_id', $user_id);
            }
            $query = $this->db->get();
            if ($query && $query->result()) {
                foreach ($query->result() as $row) {
                    $res[] = $row;
                }
            }
        }
        return $res;
    }

    public function getParlayByParlayID($parlay_id = 0) {
        $res = null;
        if ($parlay_id) {
            $this->db->where('parlay_id', $parlay_id);
            $this->db->from('parlay');
            $query = $this->db->get();
            if ($query && $query->result()) {
                $row = $query->result();
                $res = $row[0];
            }
        }
        return $res;
        
    }
}
