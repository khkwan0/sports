<?php

class Oddsmodel extends CI_Model {
    function __construct() {
        parent::__construct();
    }

    function getLatestOdds($sport = 0) {
        $res = array();
        $sql = 'select * from latest where sport='.$sport.' order by MatchTime asc';
        $query = $this->db->query($sql);
        foreach ($query->result() as $row) {
            $res[] = $row;
        }
        return $res;
    }

    private function getCode($sport) {
        switch($sport) {
            case 'soccer': return 7;break;
            case 'mlb': return 0;break;
            case 'nhl': return 5;break;
            case 'nfl': return 4;break;
            case 'nba': return 1;break;
            case 'mlb': return 7;break;
            case 'ncaaf': return 3;break;
            case 'ncaab': return 2;break;
            case 'mma': return 11;break;
            case 'tennis': return 9;break;
            default:break;
        }
    }

    function getArchives($start = 0, $number = 50, $sport = '') {
        $res = null;
        if ($sport) {
            $this->db->where('sport', $this->getCode($sport));
        }
        $this->db->join('archives', 'odds.event_id=archives.event_id');
        $query = $this->db->get('odds');
        $row_count = $query->num_rows();
        $res[0] = $row_count;

        if ($row_count>0) {
            $this->db->order_by('MatchTime', 'desc');
            if ($sport) {
                $this->db->where('sport', $this->getCode($sport));
            }
            $this->db->join('archives', 'odds.event_id=archives.event_id');
            $query = $this->db->get('odds',$number, $start);
            foreach ($query->result() as $row) {
                $res[] = $row;
            }
        }
        return $res;
    }
}
