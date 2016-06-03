<script src="/assets/js/jquery.js"></script>
<script src="/assets/js/jquery.canvasjs.min.js"></script>
<style>
    #bet_grid {
        font-family: Arial;
        font-size: 0.85em;
    }
    #bet_grid tr td { text-align:center; } 
    .tbd { background-color:rgba(252,255,150,0.3); }
    .win { background-color:rgba(0,255,0,0.3); }
    .lose { background-color:rgba(255,0,0,0.3); }
    .push { background-color:rgba(0,0,255,0.3); }
    .confirm { display:none; }
    .removed { display:none; }
    #chartarea {
        position:relative;
        width: 50%;
        height: 300px;
    }
</style>
<div id="bets_spacer" style="padding-top:20px"></div>
<div>
    <?php if ($this->session->userdata('is_admin')==1 && ($bets && isset($bets) && count($bets))):?>
        <div>
           <h2>User ID: <a href="/admin/showbets/<?php echo $bets[0]->user_id?>"><?php echo $bets[0]->user_id?></a></h2> 
        </div>
        <div id="chartarea">
        </div>
    <?php endif;?>
    <button id="show_removed">Show deleted</button><button id="hide_removed">Hide deleted</button>
    <div>
    <table id="bet_grid">
        <tr><th>Bet ID</th><th>Bet Time</th><th>Wager</th><th></th><th></th><th>Type</th><th></th><th>Winner (Chosen)</th><th></th><th>Loser (Chosen)</th><th>Money Line</th><th>Spread</th><th>Spread Line</th><th>OverUnder</th><th>Match Time</th><th>Result</th><th>Final</th><th>Parlay ID</th><th>Comments</th>
<?php if ($this->session->userdata('is_admin')==1):?>
	<th>Event ID</th>
<?php endif?>
	</tr>
        <?php $ctr = 0?>
        <?php if ($bets && isset($bets) && count($bets)):?>
            <?php foreach ($bets as $bet):?>
                <?php
                    if ($bet->BetType == 'moneyline') {
                        if ($bet->MoneyLine<0) {
                            $payout = $bet->bet_amt*100.0/abs($bet->MoneyLine);
                        } else {
                            $payout = $bet->bet_amt*$bet->MoneyLine/100.0;
                        }
                    }
                    if ($bet->BetType == 'spread') {
                        if ($bet->SpreadLine<0) {
                            $payout = $bet->bet_amt*100.0/abs($bet->SpreadLine);
                        } else {
                            $payout = $bet->bet_amt*$bet->SpreadLine/100.0;
                        }
                    }
                    if ($bet->BetType == 'over') {
                        if ($bet->OverLine<0) {
                            $payout = $bet->bet_amt*100.0/abs($bet->OverLine);
                        } else {
                            $payout = $bet->bet_amt*$bet->OverLine/100.0;
                        }
                    }
                    if ($bet->BetType == 'under') {
                        if ($bet->UnderLine<0) {
                            $payout = $bet->bet_amt*100.0/abs($bet->UnderLine);
                        } else {
                            $payout = $bet->bet_amt*$bet->UnderLine/100.0;
                        }
                    }
                    if ($bet->parlay_id) {
                        $payout = $bet->bet_amt * $bet->parlay_factor * $bet->juice;
                    }
                ?>
                <?php $no_logo = false?>
                <?php if ($bet->sport == 'ncaaf' || $bet->sport == 'ncaab'):?>
                    <?php $win_logo = strtolower(str_replace(' ', '_', str_replace('.','',($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam))).'.gif')?>
                    <?php $lose_logo = strtolower(str_replace(' ', '_', str_replace('.','',($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam))).'.gif')?>
                    <?php $win_logo = str_replace('&', '_and_', $win_logo);?>
                    <?php $lose_logo = str_replace('&', '_and_', $lose_logo);?>
                    <?php $win_logo = str_replace("'", '', $win_logo);?>
                    <?php $lose_logo = str_replace("'", '', $lose_logo);?>
                <?php elseif ($bet->sport=='soccer'):?>
                    <?php $win_logo=$lose_logo = 'Soccer_ball.svg'?>
                <?php elseif ($bet->sport == 'nhl' || $bet->sport == 'nba'):?>
                    <?php $win_logo = str_replace(' ','_',str_replace('.','',($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam))).'.svg'?>
                    <?php $lose_logo = str_replace(' ','_',str_replace('.','',($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam))).'.svg'?>
                <?php elseif ($bet->sport == 'mma' || $bet->sport == 'tennis'):?>
                    <?php $no_logo = true;?>
                <?php else:?>
                    <?php $win_logo = str_replace(' ','_',str_replace('.','',($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam))).'_logo.svg'?>
                    <?php $lose_logo = str_replace(' ','_',str_replace('.','',($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam))).'_logo.svg'?>
                <?php endif;?>
                <?php if ($bet->parlay_result == 'win'):?>
                        <tr class="win">
                    <?php elseif ($bet->parlay_result == 'lose'):?>
                        <tr class="lose">
                    <?php elseif (($bet->manually_removed && !isset($bet->parlay_active)) || (isset($bet->parlay_active) && $bet->parlay_active == 0)):?>
                        <tr class="removed">
                    <?php elseif ($bet->parlay_result == 'tbd'):?>
                        <tr class="tbd">
                    <?php else:?>
                        <tr  class="<?php echo $bet->bet_result?>">
                <?php endif;?>
                <?php if (($bet->bet_result == 'lose' || $bet->bet_result == 'push' || $bet->parlay_result == 'lose') && $bet->parlay_result != 'win'):?>
                    <?php $payout = 0;?>
                <?php endif;?>
                    <td><?php if (!$bet->parlay_id) echo $bet->bet_id?></td>
                    <td><?php echo date('Y-m-d g:i A',strtotime($bet->datetime))?></td>
                    <?php if ($bet->team == 'draw'):?>
                        <td><?php echo number_format($bet->bet_amt,2)?></td><td> to DRAW </td><td> <?php echo number_format($payout,2)?></td>
                    <?php else:?>
                        <td><?php echo number_format($bet->bet_amt,2)?></td><td> to win </td><td> <?php echo number_format($payout,2)?></td>
                    <?php endif;?>
                    <td><?php echo $bet->parlay_id?'Parlay':$bet->BetType?></td>
                    <td><?php if (!$bet->parlay_id && !$no_logo):?><img src="/assets/images/<?php echo $bet->sport?>/<?php echo $win_logo?>" width='25px' height='25px' /><?php endif;?></td>
                    <td><?php if (!$bet->parlay_id):?>
                        <a target="_blank" href="https://www.google.com/search?gws_rd=ssl#q=<?php echo urlencode($bet->HomeTeam)?>+vs+<?php echo urlencode($bet->AwayTeam)?>">
                        <?php echo ($bet->team=='home'?$bet->HomeTeam:$bet->AwayTeam)?>
                        </a></td>
                        <?php endif;?></a>
                    </td>
                    <td><?php if (!$bet->parlay_id && !$no_logo):?><img src="/assets/images/<?php echo $bet->sport?>/<?php echo $lose_logo?>" width='25px' height='25px' /><?php endif;?></td>
                    <td>
                    <?php if (!$bet->parlay_id):?>
                        <a target="_blank" href="https://www.google.com/search?gws_rd=ssl#q=<?php echo urlencode($bet->AwayTeam)?>+vs+<?php echo urlencode($bet->HomeTeam)?>">
                        <?php echo ($bet->team=='home'?$bet->AwayTeam:$bet->HomeTeam)?>
                        </a></td>
                    <?php endif;?>
                    <td><?php if ($bet->BetType=='moneyline' && !$bet->parlay_id) echo ($bet->MoneyLine>0?'+':'').$bet->MoneyLine?></td>
                    <td><?php if ($bet->BetType=='spread' && !$bet->parlay_id) echo ($bet->PointSpread>0?'+':'').number_format($bet->PointSpread,1)?></td>
                    <td><?php if ($bet->BetType=='spread' && !$bet->parlay_id) echo ($bet->SpreadLine>0?'+':'').$bet->SpreadLine?></td>
                    <?php $overunder = 'N/A';?>
                    <?php if ($bet->BetType== 'over' && !$bet->parlay_id):?>
                    <?php $overunder = 'OV '.$bet->OverLine?>
                    <?php elseif ($bet->BetType== 'under' && !$bet->parlay_id):?>
                    <?php $overunder = 'UN '.$bet->UnderLine?>
                    <?php endif;?>
                    <td><?php if (($bet->BetType=='over' || $bet->BetType=='under') && !$bet->parlay_id) echo $bet->TotalNumber.' '.$overunder?></td>
                    <td><?php if (!$bet->parlay_id) echo date('Y-m-d g:i A',strtotime($bet->MatchTime))?></td>
                    <td><?php echo $bet->parlay_id?$bet->parlay_result:$bet->bet_result?></td>
                    <td><?php if (!$bet->parlay_id) echo $bet->actual_winner?> <?php if (!$bet->parlay_id) echo '('.($bet->actual_home_score>$bet->actual_away_score?$bet->actual_home_score.' - '.$bet->actual_away_score:$bet->actual_away_score.' - '.$bet->actual_home_score).')'?></td>
                    <td><?php if ($bet->parlay_id):?><a href="/bets/showParlay/<?php echo $bet->parlay_id?>"><?php echo $bet->parlay_id?></a><?php endif;?></td>
                    <td id="status_<?php echo $bet->bet_id?>" width="200px"><?php if (time()<(strtotime($bet->datetime)+1800) && $bet->active && !$bet->parlay_id) echo '<button class="delete_bet" bet_id="'.$bet->bet_id.'" id="del_'.$bet->bet_id.'">Delete</button>'?><span class="confirm" id="confirm_<?php echo $bet->bet_id?>">Are you sure? <button class="confirm_no" bet_id="<?php echo $bet->bet_id?>">No</button><button bet_id="<?php echo $bet->bet_id?>" class="confirm_yes">Yes</button></span><?php if ((isset($bet->status) && (isset($bet->parlay_active) && !($bet->parlay_active))) || (isset($bet->status) && !isset($bet->parlay_active))) { echo $bet->status; }?></td>
<?php if ($this->session->userdata('is_admin')==1):?>
	<td><?php echo $bet->event_id?></td>
<?php endif?>
                </tr>
                <?php $ctr++;?>
            <?php endforeach;?>
        <?php endif;?>
    </table>
    </div>
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

    $('#show_removed').click(function(data) {
        $('.removed').show();
    });
    $('#hide_removed').click(function(data) {
        $('.removed').hide();
    });

    $.post('/ajax/getRealizedByUser',
            {
                'user_id':<?php echo $bets[0]->user_id?>
            },
            function(data) {
                var points = new Array();
                var total = <?php echo $default_balance?>;
                points[0] = {label:"2015-12-14 00:00:00", y:total};
                for (var key in data) {
                    total += data[key].total_payout - data[key].bet_amt;
                    //var point = '{x:"'+data[key].datetime+'",y:' + total + '},';
                    var point = {label: data[key].datetime, y:total};
                    points[key] = point;
                }
                console.log(JSON.stringify(points));
                var options = {
                    axisY:{
                        stripLines:[{
                            value:<?php echo number_format($default_balance,0,'.','')?>,
                            color: "#ff0000"
                        }]
                    },
                    title: {
                        text: "Balance trend Current Week"
                    },
                    animationEnabled: true,
                    animationDuration: 3000,
                    data: [{
                        type: "spline",
                        dataPoints: points
                    }]
                }

                $('#chartarea').CanvasJSChart(options);
            }, 'json'
          );
});
</script>
