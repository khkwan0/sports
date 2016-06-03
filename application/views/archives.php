<style>
    #archive_nav li {display: inline;}
</style>
<div>
    <p>
        Historical data on available bets and line changes over time with game results.
    </p>
</div>
<div>
    <ul id="archive_nav">
        <li><a href="/archives/sport/mlb">MLB</a></li>
        <li><a href="/archives/sport/nfl">NFL</a></li>
        <li><a href="/archives/sport/nba">NBA</a></li>
        <li><a href="/archives/sport/nhl">NHL</a></li>
        <li><a href="/archives/sport/ncaaf">NCAAF</a></li>
        <li><a href="/archives/sport/ncaab">NCAAB</a></li>
        <li><a href="/archives/sport/soccer">Soccer</a></li>
        <li><a href="/archives/sport/mma">MMA</a></li>
        <li><a href="/archives/sport/tennis">Tennis</a></li>
        <li><a href="/archives">ALL</a></li>
    </ul>
</div>
<div>
    <?php if (($start+$limit)>$res[0]):?>
        <?php echo $start+1?> thru <?php echo $res[0]?>/<?php echo $res[0]?>
    <?php else:?>
        <?php echo $start+1?> thru <?php echo $start+$limit?>/<?php echo $res[0]?>
    <?php endif;?>
</div>
<div>
    <button>
    <?php if (!isset($the_sport)) { $the_sport = 'all'; }?>
    <?php if (($start+$limit)>$limit):?>
        <a href="/archives/sport/<?php echo $the_sport?>/<?php echo $start-$limit?>/<?php echo $limit?>">Previous <?php echo $limit?></a>
    <?php else:?>
        Previous <?php echo $limit?>
    <?php endif;?>
    </button>
    <button>
     <?php if (($start+$limit)>$res[0]):?>
        Next <?php echo $limit?>
    <?php else:?>
        <a href="/archives/sport/<?php echo $the_sport?>/<?php echo $start+$limit?>/<?php echo $limit?>">Next <?php echo $limit?></a>
    <?php endif?>
    </button>
</div>
<div>
<table>
    <th>Match Time</th><th></th><th>Home</th><th></th><th></th><th>Away</th><th></th><th>Spread/H</th><th>Spread/A</th><th>Moneyline/H</th><th>Moneyline/A</th><th>DrawLine</th><th>O/U</th><th>Event ID</th>
    <?php if (isset($res) && count($res)):?>
        <?php foreach ($res as $idx=>$result):?>
            <?php $no_logo = false?>
            <?php if ($idx>0):?>
                <?php
                    switch($result->sport) {
                        case 0:$sport = 'mlb';break;
                        case 7:$sport = 'soccer';break;
                        case 4:$sport = 'nfl';break;
                        case 1:$sport = 'nba';break;
                        case 5:$sport = 'nhl';break;
                        case 2:$sport = 'ncaab';break;
                        case 3:$sport = 'ncaaf';break;
                        case 11:$sport = 'mma';break;
                        case 9:$sport = 'tennis';break;
                        default:break;
                    }
                ?>
                <?php if ($sport == 'ncaaf' || $sport == 'ncaab'):?>
                    <?php $home_logo = strtolower(str_replace(' ', '_', str_replace('.','',$result->HomeTeam)).'.gif')?>
                    <?php $away_logo = strtolower(str_replace(' ', '_', str_replace('.','',$result->AwayTeam)).'.gif')?>
                    <?php $home_logo = str_replace('&', '_and_', $home_logo);?>
                    <?php $away_logo = str_replace('&', '_and_', $away_logo);?>
                    <?php $home_logo = str_replace('\'', '', $home_logo);?>
                    <?php $away_logo = str_replace('\'', '', $away_logo);?>
                <?php elseif ($sport == 'soccer'):?>
                    <?php $home_logo = 'Soccer_ball.svg'?>
                    <?php $away_logo = 'Soccer_ball.svg'?>
                <?php elseif ($sport == 'nhl' || $sport =='nba'):?>
                    <?php $home_logo = str_replace(' ', '_', str_replace('.','',$result->HomeTeam)).'.svg'?>
                    <?php $away_logo = str_replace(' ', '_', str_replace('.','',$result->AwayTeam)).'.svg'?>
                <?php elseif ($sport == 'tennis' || $sport == 'mma'):?>
                    <?php $no_logo = true;?>
                <?php else:?>
                    <?php $home_logo = str_replace(' ', '_', str_replace('.','',$result->HomeTeam)).'_logo.svg'?>
                    <?php $away_logo = str_replace(' ', '_', str_replace('.','',$result->AwayTeam)).'_logo.svg'?>
                <?php endif;?>
                <tr>
                    <td style="font-size:0.6em"><?php echo $result->MatchTime?></td>
                    <td><?php if (!$no_logo):?><img width="25px" height=25px" src="/assets/images/<?php echo strtolower($sport)?>/<?php echo $home_logo?>" /><?php endif;?></td>
                    <td style="font-weigh:bold"><?php echo $result->HomeTeam?></td>
                    <td style="font-weight:bold"><?php echo $result->HomeScore?></td>
                    <td><?php if (!$no_logo):?><img width="25px" height=25px" src="/assets/images/<?php echo strtolower($sport)?>/<?php echo $away_logo?>" /><?php endif;?></td>
                    <td><?php echo $result->AwayTeam?></td>
                    <td style="font-weight:bold"><?php echo $result->AwayScore?></td>
                    <td><?php echo ($result->PointSpreadHome>0?'+':'').$result->PointSpreadHome.' '.($result->PointSpreadHomeLine>0?'+':'').$result->PointSpreadHomeLine?></td>
                    <td><?php echo ($result->PointSpreadAway>0?'+':'').$result->PointSpreadAway.' '.($result->PointSpreadAwayLine>0?'+':'').$result->PointSpreadAwayLine?></td>
                    <td><?php echo ($result->MoneyLineHome>0?'+':'').$result->MoneyLineHome?></td>
                    <td><?php echo ($result->MoneyLineAway>0?'+':'').$result->MoneyLineAway?></td>
                    <td><?php echo ($result->DrawLine>0?'+':'').$result->DrawLine?></td>
                    <td><?php echo $result->TotalNumber.' U:'.($result->UnderLine>0?'+':'').$result->UnderLine.' O:'.($result->OverLine>0?'+':'').$result->OverLine?></td>
                    <td style="font-size:0.6em"><?php echo $result->event_id?></td>
                </tr>
            <?php endif;?>
        <?php endforeach;?>
    <?php endif;?>
</table>
<div>
    <button>
    <?php if (!isset($the_sport)) { $the_sport = 'all'; }?>
    <?php if (($start+$limit)>$limit):?>
        <a href="/archives/sport/<?php echo $the_sport?>/<?php echo $start-$limit?>/<?php echo $limit?>">Previous <?php echo $limit?></a>
    <?php else:?>
        Previous <?php echo $limit?>
    <?php endif;?>
    </button>
    <button>
     <?php if (($start+$limit)>$res[0]):?>
        Next <?php echo $limit?>
    <?php else:?>
        <a href="/archives/sport/<?php echo $the_sport?>/<?php echo $start+$limit?>/<?php echo $limit?>">Next <?php echo $limit?></a>
    <?php endif?>
    </button>
</div>
</div>
