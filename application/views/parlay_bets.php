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
                <?php $no_logo = false;?>
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
                <?php elseif ($bet->sport == 'tennis' || $bet->sport == 'mma'):?>
                    <?php $no_logo = true;?>
                <?php else:?>
                    <?php $win_logo = str_replace(' ','_',str_replace('.','',($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam))).'_logo.svg'?>
                    <?php $lose_logo = str_replace(' ','_',str_replace('.','',($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam))).'_logo.svg'?>
                <?php endif;?>
                <tr  class="<?php echo $bet->bet_result?>">
                    <td><?php echo $bet->BetType?></td>
                    <td><?php if (!$no_logo):?><img src="/assets/images/<?php echo $bet->sport?>/<?php echo $win_logo?>" width='25px' height='25px' /><?php endif;?></td>
                    <td><?php echo ($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam)?></td>
                    <td><?php if (!$no_logo):?><img src="/assets/images/<?php echo $bet->sport?>/<?php echo $lose_logo?>" width='25px' height='25px' /><?php endif;?></td>
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
    <?php if ($bets && isset($bets) && count($bets)):?>
        <?php if (count($bets) == 1) $juice = 1;?>
        <?php $factor *= $juice;?>
        <div>
            <form method="post" action="/bets/finalparlaysave">
                <input type="hidden" value="<?php echo base64_encode(json_encode($bets))?>" name="bets" />

                <div>
                    <input id="risk_radio" type="radio" name="wager_type" value="risk" checked /><span>Wager:</span><span><input name="bet_amt" id="bet_amt" value="" placeholder="0.00" /></span><span><input type="submit" value="Submit" /></span><span style="padding-left:10px">to Win: </span><span id="payout">0.00</span>
                </div>
                <div style="margin-top:10px">
                    <input id="to_win_radio" type="radio" name="wager_type" value="to_win" /><span>To Win:</span><span><input name="win_amt" id="win_amt" value="" placeholder="0.00" /></span></span><span style="padding-left:10px">you must risk: </span><span id="to_risk"><input readonly value="" id="bet_amt_win" name="bet_amt_win" /></span><span><input type="submit" value="Submit" /></span>
                </div>
            </form>
        </div>
    <?php endif;?>
</div>
<script src="/assets/js/jquery.js"></script>
<script>
    $(document).ready(function() {
        $('#bet_amt').keyup(function() {
            $('#payout').html(($(this).val()*<?php echo $factor?>).toFixed(2));
        });
        $('#win_amt').keyup(function() {
            $('#bet_amt_win').val(($(this).val()/(<?php echo $factor?>)).toFixed(2));
        });
        $('#win_amt').click(function() {
            $('#to_win_radio').prop('checked', true);
        });
        $('#bet_amt').click(function() {
            $('#risk_radio').prop('checked', true);
        });
    });
</script>
