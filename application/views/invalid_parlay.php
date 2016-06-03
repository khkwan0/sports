<script src="/assets/js/jquery.js"></script>
<style>
    #bet_grid {
        font-family: Arial;
        font-size: 0.85em;
    }
    #bet_grid tr td { text-align:center; } 
    .tbd { background-color:white; }
    .win { background-color:rgba(0,255,0,0.3); }
    .lose { background-color:rgba(255,0,0,0.3); }
    .push { background-color:rgba(0,0,255,0.3); }
    .confirm { display:none; }
</style>
<div id="bets_spacer" style="padding-top:60px"></div>
<div>
<fieldset style="background-color:yellow">
<legend><h2>INVALID PARLAY</h2></legend>
<?php if ($reason == 2):?>
    <h3>At least one of the matches has aleady started</h3>
<?php endif;?>
    <h2>Parlay Review</h2>
    <table id="bet_grid">
        <tr><th>Type</th><th></th><th>Winner (Chosen)</th><th></th><th>Loser Chosen)</th><th>Money Line</th><th>Spread</th><th>Spread Line</th><th>OverUnder</th><th>Match Time</th>
        <?php $ctr = 0?>
        <?php if ($bets && isset($bets) && count($bets)):?>
            <?php $factor = 1.0;?>
            <?php foreach ($bets as $event_id =>$a_bet):?>
                <?php $bet = json_decode(base64_decode(openssl_decrypt($a_bet, 'AES-256-CBC','letmeinbdr',0,'f#r?a=t4KiN-1BdH')));?>
                <?php $bet->bet_result = 'tbd'?>
                <?php $bet->team = (strstr($event_id, 'home'))?'home':'away';?>
                <?php if ($bet->sport == 'ncaaf' || $bet->sport == 'ncaab'):?>
                    <?php $win_logo = strtolower(str_replace(' ', '_', str_replace('.','',($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam))).'.gif')?>
                    <?php $lose_logo = strtolower(str_replace(' ', '_', str_replace('.','',($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam))).'.gif')?>
                    <?php $win_logo = str_replace('&', '_and_', $win_logo);?>
                    <?php $lose_logo = str_replace('&', '_and_', $lose_logo);?>
                <?php elseif ($bet->sport=='soccer'):?>
                    <?php $win_logo=$lose_logo = 'Soccer_ball.svg'?>
                <?php elseif ($bet->sport == 'nhl' || $bet->sport == 'nba'):?>
                    <?php $win_logo = str_replace(' ','_',str_replace('.','',($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam))).'.svg'?>
                    <?php $lose_logo = str_replace(' ','_',str_replace('.','',($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam))).'.svg'?>
                <?php else:?>
                    <?php $win_logo = str_replace(' ','_',str_replace('.','',($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam))).'_logo.svg'?>
                    <?php $lose_logo = str_replace(' ','_',str_replace('.','',($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam))).'_logo.svg'?>
                <?php endif;?>
                <tr  class="<?php echo $bet->bet_result?>">
                    <td><?php echo $bet->BetType?></td>
                    <td><img src="/assets/images/<?php echo $bet->sport?>/<?php echo $win_logo?>" width='25px' height='25px' /></td>
                    <td><?php echo ($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam)?></td>
                    <td><img src="/assets/images/<?php echo $bet->sport?>/<?php echo $lose_logo?>" width='25px' height='25px' /></td>
                    <td><?php echo ($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam)?></td>
                    <td><?php echo $bet->MoneyLine?></td>
                    <td><?php echo number_format($bet->PointSpread,1)?></td>
                    <td><?php echo ($bet->SpreadLine>0?'+':'').$bet->SpreadLine?></td>
                    <?php $overunder = 'N/A';?>
                    <?php if ($bet->BetType== 'over'):?>
                    <?php $overunder = 'OV '.$bet->OverLine?>
                    <?php elseif ($bet->BetType== 'under'):?>
                    <?php $overunder = 'UN '.$bet->UnderLine?>
                    <?php endif;?>
                    <td><?php echo $bet->TotalNumber.' '.$overunder?></td>
                    <td><?php echo date('Y-m-d g:i A',strtotime($bet->MatchTime))?></td>
                    <?php
                        switch($bet->BetType) {
                            case 'spread':
                                $line = ($bet->SpreadLine);
                                break;
                            case 'moneyline':
                                $line = ($bet->MoneyLine);
                                break;
                            case 'over':
                                $line = ($bet->OverLine);
                                break;
                           case  'under':
                                $line = ($bet->UnderLine);
                                break;
                            default:break;
                        }
                        if ($line<0) {
                            $factor *= (100-$line)/abs($line);
                        } else {
                            $factor *= ($line+100)/100.0;
                        }
                    ?>
                </tr>
                <?php $ctr++;?>
            <?php endforeach;?>
            <?php $factor -= 1.0;?>
        <?php endif;?>
    </table>
    </fieldset>
</div>
<div>
<a href="/parlay"><h2>Back to parlays</h2></a>
</div>
