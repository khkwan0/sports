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
<?php
    $started = false;
    if ($bets && isset($bets) && count($bets)) {
        foreach ($bets as $bet) {
            if (strtotime($bet->MatchTime)<time()) {
                $started = true;
            }
        }
    }
?>
<div>
    <?php if ($this->session->userdata('is_admin')==1 && ($bets && isset($bets) && count($bets))):?>
        <div>
           <h2>User ID: <a href="/admin/showbets/<?php echo $bets[0]->user_id?>"><?php echo $bets[0]->user_id?></a></h2> 
        </div>
    <?php endif;?>
    <h3>Parlay ID: <?php if (isset($bets) && count($bets)) echo $bets[0]->parlay_id?></h3>
    <table id="bet_grid">
        <tr><th>Bet ID</th><th>Bet Time</th><th>Wager</th><th>Type</th><th></th><th>Winner (Chosen)</th><th></th><th>Loser (Chosen)</th><th>Money Line</th><th>Spread</th><th>Spread Line</th><th>OverUnder</th><th>Match Time</th><th>Result</th><th>Final</th>
        <?php if ($this->session->userdata('is_admin')==1):?>
            <th>Event ID</th>
        <?php endif;?>
        <th></th></tr>
        <?php $ctr = 0?>
        <?php if ($bets && isset($bets) && count($bets)):?>
            <?php foreach ($bets as $bet):?>
                <?php $no_logo = false;?>
                <?php if ($bet->sport == 'ncaaf' || $bet->sport == 'ncaab'):?>
                    <?php $win_logo = strtolower(str_replace(' ', '_', str_replace('.','',($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam))).'.gif')?>
                    <?php $lose_logo = strtolower(str_replace(' ', '_', str_replace('.','',($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam))).'.gif')?>
                    <?php $win_logo = str_replace('&', '_and_', $win_logo);?>
                    <?php $lose_logo = str_replace('&', '_and_', $lose_logo);?>
                    <?php $win_logo = str_replace('\'', '', $win_logo);?>
                    <?php $lose_logo = str_replace('\'', '', $lose_logo);?>
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
                    <tr class="<?php echo $parlay->parlay_result=='tbd'?$bet->bet_result:$parlay->parlay_result?>">
                    <td><?php echo $bet->bet_id?></td>
                    <td><?php echo date('Y-m-d g:i A',strtotime($bet->datetime))?></td>
                    <td><?php echo number_format($bet->bet_amt,2)?></td>
                    <td><?php echo $bet->BetType?></td>
                    <td><?php if (!$no_logo):?><img src="/assets/images/<?php echo $bet->sport?>/<?php echo $win_logo?>" width='25px' height='25px' /><?php endif;?></td>
                    <td><a target="_blank" href="https://www.google.com?gws_rd=ssl#q=<?php echo urlencode($bet->HomeTeam)?>+vs+<?php echo urlencode($bet->AwayTeam)?>"><?php echo ($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam)?></a></td>
                    <td><?php if (!$no_logo):?><img src="/assets/images/<?php echo $bet->sport?>/<?php echo $lose_logo?>" width='25px' height='25px' /><?php endif;?></td>
                    <td><a target="_blank" href="https://www.google.com?gws_rd=ssl#q=<?php echo urlencode($bet->AwayTeam)?>+vs+<?php echo urlencode($bet->HomeTeam)?>"><?php echo ($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam)?></a></td>
                    <td><?php if ($bet->BetType=='moneyline') echo ($bet->MoneyLine>0?'+':'').$bet->MoneyLine?></td>
                    <td><?php if ($bet->BetType=='spread') echo ($bet->PointSpread>0?'+':'').number_format($bet->PointSpread,1)?></td>
                    <td><?php if ($bet->BetType=='spread') echo ($bet->SpreadLine>0?'+':'').$bet->SpreadLine?></td>
                    <?php $overunder = 'N/A';?>
                    <?php if ($bet->BetType== 'over'):?>
                    <?php $overunder = 'OV '.$bet->OverLine?>
                    <?php elseif ($bet->BetType== 'under'):?>
                    <?php $overunder = 'UN '.$bet->UnderLine?>
                    <?php endif;?>
                    <td><?php if (($bet->BetType=='over' || $bet->BetType=='under')) echo $bet->TotalNumber.' '.$overunder?></td>
                    <td><?php echo date('Y-m-d g:i A',strtotime($bet->MatchTime))?></td>
                    <td><?php echo $bet->bet_result?></td>
                    <td><?php echo $bet->actual_winner?> (<?php echo ($bet->actual_home_score>$bet->actual_away_score?$bet->actual_home_score.' - '.$bet->actual_away_score:$bet->actual_away_score.' - '.$bet->actual_home_score)?>)</td>
                    <?php if ($this->session->userdata('is_admin') == 1):?>
                    <td><?php echo $bet->event_id?></td>
                    <?php endif;?>
                    <td id="status_<?php echo $bet->bet_id?>" width="200px"><?php if (time()<(strtotime($bet->datetime)+1800) && $bet->active && $parlay->parlay_result == 'tbd' && !$started) echo '<button class="delete_bet" bet_id="'.$bet->bet_id.'" id="del_'.$bet->bet_id.'">Delete</button>'?><span class="confirm" id="confirm_<?php echo $bet->bet_id?>">Are you sure? <button class="confirm_no" bet_id="<?php echo $bet->bet_id?>">No</button><button bet_id="<?php echo $bet->bet_id?>" class="confirm_yes">Yes</button></span><?php if (isset($bet->status)) { echo $bet->status; }?></td>
                </tr>
                <?php $ctr++;?>
            <?php endforeach;?>
        <?php endif;?>
    </table>
</div>
<div>
    <?php
        function determineFactor ($bet) {
            switch ($bet->BetType) {
                case 'spread':$line=$bet->SpreadLine;break;
                case 'moneyline':$line=$bet->MoneyLine;break;
                case 'over':$line=$bet->OverLine;break;
                case 'under':$line=$bet->UnderLine;break;
                default:$line=0;break;
            }
            if ($line<0) {
                $factor = (100.0 - $line)/abs($line);
            } else {
                $factor = (100.0 + $line)/100.0;
            }
            return $factor;
        }
       
        $factor = 1.0;
        $win = 0;
        $push = 0;
        $lose = 0;
        foreach ($bets as $bet) {
            if ($bet->bet_result != 'lose' && $bet->bet_result != 'push' && $bet->manually_removed == 0) {
                $factor *= determineFactor($bet);
            }
            if ($bet->bet_result == 'win' || $bet->bet_result == 'tbd') {
                $win++;
            }
            if ($bet->bet_result == 'push') {
                $push++;
            }
            if ($bet->bet_result == 'lose') {
                $lose++;
            }
        }
        if ($win) {
            $factor -= 1.0;
        } else {
            $factor = 0;
        }
        if ($win == 1) {
            $juice = 1.0;
        } else {
            $juice = $parlay->juice;
        }
        $payout = $parlay->bet_amt * $factor * $juice;
        $total_payout = 0.00;
        if ($lose>0) {
            $payout = 0.00;
        }
    ?>
    <span>Risking </span><span style="font-style:italic"><?php echo $parlay->bet_amt?></span> to win <span style="font-style:italic; font-weight: bold"><?php echo number_format($payout,2)?></span>
    
</div>
<script>
$(document).ready(function() {
    $('.delete_bet').click(function(data) {
        bet_id = $(this).attr('bet_id');
        $(this).hide();
        $('#confirm_'+bet_id).show();
    });

    $('.confirm_no').click(function(data) {
        bet_id = $(this).attr('bet_id');
        $('#del_'+bet_id).show();
        $('#confirm_'+bet_id).hide();
    });

    $('.confirm_yes').click(function(data) {
        bet_id = $(this).attr('bet_id');
        $('#confirm_'+bet_id).hide();
        $('.delete_bet').prop('disabled',true);
        $.post('/ajax/deleteBet',
            {
                'bet_id':   bet_id
            }, function(data) {
                $('#status_'+bet_id).html(data['status']);
                avail = parseFloat($('#available_balance').html().replace(/,/g,""));
                pending= parseFloat($('#pending_balance').html().replace(/,/g,""));
                avail += parseFloat(data['amt']);
                pending -= parseFloat(data['amt']);
                $('#available_balance').html(avail.toFixed(2));
                $('#pending_balance').html(pending.toFixed(2));
                $('.delete_bet').prop('disabled',false);
            },'json'
        );
    });
});
</script>
