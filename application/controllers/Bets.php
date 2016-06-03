<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bets extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('oddsmodel');
        $this->load->model('betsmodel');
        $this->load->model('users');
        $this->load->library('session');
        $this->load->helper('url');
    }

    private function showStandardHeader() {
            $this->load->view('header');
            $this->load->view('left_side_begin');
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
            $this->load->view('balances', array('balance'=>$balances['balance'],'pending'=>$pending,'default_balance'=>$balances['default_balance']));
            $this->load->view('left_side_end');
            $this->load->view('right_side_begin');
            $this->load->view('right_header');
    }

    private function showStandardFooter() {
        $this->load->view('right_side_end');
        $this->load->view('footer');
    }

    public function index() {
        if ($this->session->userdata('user_id')) {
            $this->showStandardHeader();
            $bets = $this->betsmodel->getAllBetsByUserID($this->session->userdata('user_id'), true, false,false);
            $this->load->view('bets', array('bets'=>$bets));
            $this->showStandardFooter();
        } else {
            redirect('/','refresh');
        }
    }

    public function history() {
        if ($this->session->userdata('user_id')) {
            $this->load->view('header');
            $this->load->view('left_side_begin');
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
            $this->load->view('balances', array('balance'=>$balances['balance'],'pending'=>$pending,'default_balance'=>$balances['default_balance']));
            $this->load->view('left_side_end');
            $this->load->view('right_side_begin');
            $this->load->view('right_header');
            $bets = $this->betsmodel->getAllBetsByUserID($this->session->userdata('user_id'), false, false, true);
            $this->load->view('bets', array('bets'=>$bets));
            $this->load->view('right_side_end');
            $this->load->view('footer');
        } else {
            redirect('/','refresh');
        }
    }

	public function savebet() {
        if ($this->session->userdata('user_id')) {
            $total = 0.00;
            $to_win = false;  // the better either risks an amount to win, or requests final to_win.
            foreach ($this->input->post() as $event_id=>$info) {
                if (strstr($event_id,'home')) {
                    $locale = 'home';
                } else if (strstr($event_id, 'away')) {
                    $locale = 'away';
                } else {
                    $locale = 'draw';
                }
                if (strstr($event_id,'_bet')) {
                    $bet_type = substr($event_id, strrpos($event_id, '_')+1);
                    //echo $event_id.' bet: '.$info.'<br />';
                    $bet[$bet_type][strstr($event_id,'_', true)][$locale]['bet'] = $info;
                    //echo '<pre>';print_r($bet);echo '</pre>';
                } elseif ($event_id == 'wager_type') {
                    if ($info == 'win') {
                        $to_win = true;
                    }
                } else {
                    $bet_info =  json_decode(base64_decode(openssl_decrypt($info, 'AES-256-CBC','letmeinbdr',0,'f#r?a=t4KiN-1BdH')));
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['MoneyLine'] = isset($bet_info->MoneyLine)?$bet_info->MoneyLine:0;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['PointSpread'] = isset($bet_info->PointSpread)?$bet_info->PointSpread:0;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['id'] = $bet_info->id;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['HomeTeam'] = $bet_info->HomeTeam;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['AwayTeam'] = $bet_info->AwayTeam;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['MatchTime'] = $bet_info->MatchTime;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['sport'] = $bet_info->sport;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['BetType'] = $bet_info->BetType;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['TotalNumber'] = isset($bet_info->TotalNumber)?$bet_info->TotalNumber:0;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['SpreadLine'] = isset($bet_info->SpreadLine)?$bet_info->SpreadLine:0;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['OverLine'] = isset($bet_info->OverLine)?$bet_info->OverLine:0;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['UnderLine'] = isset($bet_info->UnderLine)?$bet_info->UnderLine:0;
                    $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['ParlayId'] = 0;
                }
            }

            $max_bet = $this->users->getMaxBet($this->session->userdata('user_id'));
            foreach ($bet as $bet_type=>$the_bet) {
                foreach ($the_bet as $event_id=>$locale) {
                    foreach ($locale as $place=>$bet_info) {
                        if (isset($bet_info['bet']) && is_numeric($bet_info['bet'])) {
                            if ($to_win) {
                                switch ($bet_info['BetType']) {
                                    case 'moneyline': $line = $bet_info['MoneyLine']; break;
                                    case 'spread': $line = $bet_info['SpreadLine']; break;
                                    case 'over': $line = $bet_info['OverLine']; break;
                                    case 'under': $line = $bet_info['UnderLine']; break;
                                    default:break;
                                }
                                if ($line < 0) {
                                    $bet[$bet_type][$event_id][$place]['bet'] = $bet_info['bet'] * abs($line) / 100.0;
                                    $bet_info['bet'] = $bet_info['bet'] * abs($line) / 100.0;
                                } else {
                                    $bet[$bet_type][$event_id][$place]['bet'] = $bet_info['bet'] * 100.0/$line;
                                    $bet_info['bet'] = $bet_info['bet'] * 100.0/$line;
                                }
                            }
                            if ($bet_info['bet'] > $max_bet) { $bet[$bet_type][$event_id][$place]['bet'] = $max_bet; }
                            $total += $bet_info['bet'];
                        }
                    }
                }
            }
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            if ($total <= $balances['balance']) {
                foreach ($bet as $bet_type=>$the_bet) {
                    foreach ($the_bet as $event_id=>$locale) {
                        foreach ($locale as $place=>$bet_info) {
                            if (isset($bet_info['bet']) && is_numeric($bet_info['bet'])) {
                                if (strtotime($bet_info['MatchTime']) > time()) {
                                    $this->betsmodel->savebet($event_id, $place, $bet_info);
                                    $this->users->deductBalanceByUserID($this->session->userdata('user_id'), $bet_info['bet']);
                                }
                            }
                        }
                    }
                }
                redirect('/bets/','refresh');
            } else {
                $this->load->view('header', array('balance'=>$balances['balance']));
                $this->load->view('funds_issue',array('total'=>$total));
            }
        }
	}

    public function validateParlay($raw_bet = array()) {
        $is_valid = 0;
        if (isset($raw_bet) && count($raw_bet)) {
            foreach ($raw_bet as $event_id=>$a_bet) {
                $bet = json_decode(base64_decode(openssl_decrypt($a_bet, 'AES-256-CBC','letmeinbdr',0,'f#r?a=t4KiN-1BdH')));
                if ($bet->BetType == 'moneyline') {
                    if (isset($check[strstr($event_id,'_',true)]['spread'])) {
                        return 1;
                    } else {
                        $check[strstr($event_id,'_',true)]['moneyline'] = 1;
                    }
                } else if ($bet->BetType == 'spread') {
                    if (isset($check[strstr($event_id,'_',true)]['moneyline'])) {
                        return 1;
                    } else {
                        $check[strstr($event_id,'_',true)]['spread'] = 1;
                    }
                }
		if (strtotime($bet->MatchTime) < time()) {
		    return 2;
		}
            }
        }
        return $is_valid;
    }

    public function saveParlay() {
        if ($this->session->userdata('user_id')) {
            $raw_bet = $this->input->post();

            $this->load->view('header');
            $this->load->view('left_side_begin');
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
            $juice = $this->users->getParlayJuice($this->session->userdata('user_id'));
            $this->load->view('balances', array('balance'=>$balances['balance'],'pending'=>$pending,'default_balance'=>$balances['default_balance']));
            $this->load->view('left_side_end');
            $this->load->view('right_side_begin');
            $this->load->view('right_header');
            $valid = $this->validateParlay($raw_bet);
            if ($valid == 0) {
                $this->load->view('parlay_bets',array('bets'=>$raw_bet,'juice'=>$juice));
            } else if ($valid ==1) {
                $this->load->view('invalid_parlay',array('bets'=>$raw_bet,'juice'=>$juice,'reason'=>$valid));
            } else if ($valid == 2) {
                $this->load->view('invalid_parlay',array('bets'=>$raw_bet,'juice'=>$juice,'reason'=>$valid));
            }
            $this->load->view('right_side_end');
            $this->load->view('footer');
        }
    }

    private function determineParlayFactor($bet_info = array()) {
        $factor = 0.0;
        switch ($bet_info['BetType']) {
            case 'moneyline':
                if ($bet_info['MoneyLine']<0) {
                    $factor = (100.0 - $bet_info['MoneyLine'])/abs($bet_info['MoneyLine']);
                } else {
                    $factor = (100.0 + $bet_info['MoneyLine'])/100.0;
                }
                break;
            case 'spread':
                if ($bet_info['SpreadLine']<0) {
                    $factor = (100.0 - $bet_info['SpreadLine'])/abs($bet_info['SpreadLine']);
                } else {
                    $factor = (100.0 + $bet_info['SpreadLine'])/100.0;
                }
                break;
            case 'over':
                if ($bet_info['OverLine']<0) {
                    $factor = (100.0 - $bet_info['OverLine'])/abs($bet_info['OverLine']);
                } else {
                    $factor = (100.0 + $bet_info['OverLine'])/100.0;
                }
                break;
            case 'under':
                if ($bet_info['UnderLine']<0) {
                    $factor = (100.0 - $bet_info['UnderLine'])/abs($bet_info['UnderLine']);
                } else {
                    $factor = (100.0 + $bet_info['UnderLine'])/100.0;
                }
                break;
            default:break;
        }
        return $factor;
    }

    public function finalParlaySave() {
        if ($this->session->userdata('user_id')) {
            $bets = json_decode(base64_decode($this->input->post('bets')));
            if ($this->input->post('wager_type') == 'to_win') {
                $bet_amt = $this->input->post('bet_amt_win');
            } else {
                $bet_amt = $this->input->post('bet_amt');
            }
            $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
            $balance = $balances['balance'];
            $max_bet = $this->users->getMaxBet($this->session->userdata('user_id'));
            if ($bet_amt <= $balance && $bet_amt <= $max_bet) {
                $parlay_juice = $this->users->getParlayJuice($this->session->userdata('user_id'));
                $parlay_id = $this->betsmodel->createParlay($this->session->userdata('user_id'), $bet_amt, $parlay_juice);
                if ($parlay_id != 9999999)  {// error code
                    foreach ($bets as $event_id=>$a_bet) {
                        $locale = (strstr($event_id,'home'))?'home':'away';
                        $bet_info =  json_decode(base64_decode(openssl_decrypt($a_bet, 'AES-256-CBC','letmeinbdr',0,'f#r?a=t4KiN-1BdH')));
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['MoneyLine'] = $bet_info->MoneyLine;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['PointSpread'] = $bet_info->PointSpread;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['id'] = $bet_info->id;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['HomeTeam'] = $bet_info->HomeTeam;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['AwayTeam'] = $bet_info->AwayTeam;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['MatchTime'] = $bet_info->MatchTime;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['sport'] = $bet_info->sport;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['BetType'] = $bet_info->BetType;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['TotalNumber'] = $bet_info->TotalNumber;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['SpreadLine'] = $bet_info->SpreadLine;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['OverLine'] = $bet_info->OverLine;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['UnderLine'] = $bet_info->UnderLine;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['ParlayId'] = $parlay_id;
                        $bet[$bet_info->BetType][strstr($event_id,'_', true)][$locale]['bet'] = $bet_amt;
                    }
                    $factor = 1.00;
                    foreach ($bet as $bet_type=>$the_bet) {
                        foreach ($the_bet as $event_id=>$locale) {
                            foreach ($locale as $place=>$bet_info) {
                                if ($bet_info['bet'] && is_numeric($bet_info['bet'])) {
                                    if (strtotime($bet_info['MatchTime']) > time()) {
                                        $this->betsmodel->savebet($event_id, $place, $bet_info);
                                        $factor *= $this->determineParlayFactor($bet_info);
                                    }
                                }
                            }
                        }
                    }
                    $factor -= 1.0;
                    $this->betsmodel->saveParlayFactor($parlay_id, $factor);
                    $this->users->deductBalanceByUserID($this->session->userdata('user_id'), $bet_amt);
                    redirect('/bets/','refresh');
                }
            } else {
                $this->load->view('header', array('balance'=>$balance));
                $this->load->view('funds_issue',array('total'=>$bet_amt));
            }
        }
    }

    public function showParlay($parlay_id = 0) {
        if ($this->session->userdata('user_id')) {
            if ($parlay_id ) {
                $this->load->view('header');
                $this->load->view('left_side_begin');
                $balances = $this->users->getBalancebyUserID($this->session->userdata('user_id'));
                $pending = $this->betsmodel->getPendingAmount($this->session->userdata('user_id'));
                $this->load->view('balances', array('balance'=>$balances['balance'],'pending'=>$pending,'default_balance'=>$balances['default_balance']));
                $this->load->view('left_side_end');
                $this->load->view('right_side_begin');
                $this->load->view('right_header');
                if ($this->session->userdata('is_admin')) $this->load->view('admin_nav');
                $parlay = $this->betsmodel->getParlayByParlayID($parlay_id); 
                $bets = $this->betsmodel->getAllParlaysByParlayIDUserID($this->session->userdata('user_id'), $parlay_id, true, false);
                $this->load->view('parlay_bets_view', array('bets'=>$bets, 'parlay'=>$parlay));
                $this->load->view('right_side_end');
                $this->load->view('footer');
            }
        } else {
            redirect('/', 'refresh');
        }
    }
}
