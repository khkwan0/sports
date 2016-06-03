<style>
    .opaque_background {
        background-color:#d3d3d3;
    }
    .board_grid {
        vertical-align:middle;
        width:100%;
        font-family:Arial, Helvetica, Sans-Serif;
        border:0;
        border-collapse:collapse;
    }

    .board_grid tr td { border:none; }

    .board_grid tr td span {
        padding-left:5px;
    }
    .t_header { border-collapse:separate; }
    .t_header td {
        border-radius:4px;
        padding:5px 3px 5px 5px;
        text-align:left;
    }
.white {
    background-color: rgba(238,238,238,0.5);
}
.gray {
    background-color: rgba(200,200,200,0.75);
}

a:link {
color:black;
  }

a:visited {
color:black;
  }
</style>
<div style="background-image:url('/assets/images/basketball_background.jpg')">
    <div style="background-color:rgba(200,200,200,0.75)"><h2>Max bets per parlay = 6</h2></div>
    <div>
        <?php if ($sport == 'mlb'):?>
            <img src='/assets/images/mlb/mlb.png' width="125" />
        <?php elseif ($sport == 'nfl'):?>
            <img src='/assets/images/nfl/nfl.png' width="125" />
        <?php elseif ($sport == 'soccer'):?>
            <img src='/assets/images/soccer/Soccer_ball.svg' width="125" />
        <?php elseif ($sport == 'ncaaf'):?>
            <img src='/assets/images/ncaaf/ncaaf_logo.png' width="125" />
        <?php elseif ($sport == 'ncaab'):?>
            <img src='/assets/images/ncaab/ncaa_basketball.png' width="125" />
        <?php elseif ($sport == 'nba'):?>
            <img src='/assets/images/nba/nba_logo.svg' width="125" />
        <?php elseif ($sport == 'nhl'):?>
            <img src='/assets/images/nhl/nhl_logo.svg' width="125" />
        <?php elseif ($sport == 'mma'):?>
            <img src='/assets/images/mma/mma.jpg' width="125" />
        <?php elseif ($sport == 'tennis'):?>
            <img src='/assets/images/tennis/tennis.png' width="125" />
        <?php endif;?>
    </div>
    <div>
        <table class="board_grid">
            <tr class="t_header" style="background-color:rgba(51,152,153,0.5);color:white;font-weight:bold;font-size:25px;text-align:left;"><th></th><th>Sport: <?php echo $sport?></th><th>Spread</th><th>Money Line</th><th>Total Points</th></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
        <?php $ctr = 0;?>
        <?php if (isset($odds) && count($odds)):?>
            <?php foreach ($odds as $game):?>
                <?php if ($sport != 'soccer' || ($game->PointSpreadHome != 0.0 && $game->PointSpreadAway != 0.0)):?>
                <?php $class = ($ctr%2)?'white':'gray'?>
                <tr class="<?php echo $class?>">
                    <td style="text-align:center;font-weight:bold;color:#d1231a;" colspan="5">
                    <?php echo strtoupper(date('l, F jS, Y g:i A',strtotime($game->MatchTime)))?>
                    </td>
                </tr>
                <tr class="<?php echo $class?>"><td colspan="5">&nbsp;</td></tr>
                <?php $no_logo = false;?>
                <?php $ctr++;?>
                    <?php if ($sport == 'ncaaf' || $sport == 'ncaab'):?>
                        <?php $home_logo = strtolower(str_replace(' ', '_', str_replace('.','',$game->HomeTeam)).'.gif')?>
                        <?php $away_logo = strtolower(str_replace(' ', '_', str_replace('.','',$game->AwayTeam)).'.gif')?>
                        <?php $home_logo = str_replace('&', '_and_', $home_logo);?>
                        <?php $away_logo = str_replace('&', '_and_', $away_logo);?>
                        <?php $home_logo = str_replace('\'', '', $home_logo);?>
                        <?php $away_logo = str_replace('\'', '', $away_logo);?>
                    <?php elseif ($sport == 'soccer'):?>
                        <?php $home_logo = 'Soccer_ball.svg'?>
                        <?php $away_logo = 'Soccer_ball.svg'?>
                    <?php elseif ($sport == 'nhl' || $sport == 'nba'):?>
                        <?php $home_logo = str_replace(' ', '_', str_replace('.','',$game->HomeTeam)).'.svg'?>
                        <?php $away_logo = str_replace(' ', '_', str_replace('.','',$game->AwayTeam)).'.svg'?>
                    <?php elseif ($sport == 'mma' || $sport == 'tennis'):?>
                        <?php $no_logo = true;?>
                    <?php else:?>
                        <?php $home_logo = str_replace(' ', '_', str_replace('.','',$game->HomeTeam)).'_logo.svg'?>
                        <?php $away_logo = str_replace(' ', '_', str_replace('.','',$game->AwayTeam)).'_logo.svg'?>
                    <?php endif;?>


                    <tr class="<?php echo $class?>">
                        <td><?php if (!$no_logo):?><img width="20px" height="20px" src="/assets/images/<?php echo strtolower($sport)?>/<?php echo $away_logo?>" /><?php endif;?></td>
                        <td><a target="_blank" style="text-decoration:none" href="https://www.google.com?q=<?php echo urlencode($game->HomeTeam)?> vs <?php echo urlencode($game->AwayTeam)?>"><?php echo $game->AwayTeam?></a></td>
                        <td>
                            <?php if ($game->PointSpreadAway != 0.0 || $game->PointSpreadAwayLine != 0.0):?>
                                <input type="checkbox" class="<?php echo $game->event_id?>" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'spread','MoneyLine'=>$game->MoneyLineAway,'PointSpread'=>$game->PointSpreadAway,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadAwayLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_away_h_spread'?>" /><span><select><option><?php echo $game->PointSpreadAway.' '.($game->PointSpreadAwayLine>0?'+':'').$game->PointSpreadAwayLine?></option></select></span>
                            <?php endif;?>
                        </td>
                        <td>
                            <?php if ($game->MoneyLineAway != 0.0):?>
                                <input type="checkbox" class="<?php echo $game->event_id?>" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'moneyline','MoneyLine'=>$game->MoneyLineAway,'PointSpread'=>$game->PointSpreadAway,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadAwayLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_away_h_moneyline'?>" /><span><select><option><?php echo ($game->MoneyLineAway>0?'+':'').$game->MoneyLineAway?></option></select</span>
                            <?php endif;?>
                        </td>
                        <td>
                            <?php if ($game->TotalNumber != 0 || $game->UnderLine != 0.0):?>
                                <input type="checkbox" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'under','MoneyLine'=>$game->MoneyLineAway,'PointSpread'=>$game->PointSpreadAway,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadAwayLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_away_h_under'?>" /><span><select><option>UN <?php echo $game->TotalNumber.' '.($game->UnderLine>0?'+':'').$game->UnderLine?></option></select</span>
                            <?php endif;?>
                        </td>
                    </tr>
                <tr class="<?php echo $class?>">
                    <td style="width:20px"><?php if (!$no_logo):?><img width="20px" height="20px" src="/assets/images/<?php echo strtolower($sport)?>/<?php echo $home_logo?>" /><?php endif;?></td>
                    <td><a target="_blank" style="text-decoration:none" href="https://www.google.com?q=<?php echo urlencode($game->HomeTeam)?> vs <?php echo urlencode($game->AwayTeam)?>"><?php echo $game->HomeTeam?></a></td>
                    <td>
                        <?php if ($game->PointSpreadHome != 0.0 || $game->PointSpreadHomeLine != 0.0):?>
                            <input class="<?php echo $game->event_id?>" type="checkbox" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'spread','MoneyLine'=>$game->MoneyLineHome,'PointSpread'=>$game->PointSpreadHome,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadHomeLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_home_h_spread'?>" /><span><select><option><?php echo $game->PointSpreadHome.' '.($game->PointSpreadHomeLine>0?'+':'').$game->PointSpreadHomeLine?></option></select></span>
                        <?php endif;?>
                    </td>
                    <td>
                        <?php if ($game->MoneyLineHome != 0.0):?>
                            <input type="checkbox" class="<?php echo $game->event_id?>" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'moneyline','MoneyLine'=>$game->MoneyLineHome,'PointSpread'=>$game->PointSpreadHome,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadHomeLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_home_h_moneyline'?>" /><span><select><option><?php echo ($game->MoneyLineHome>0?'+':'').$game->MoneyLineHome?></option></select></span>
                        <?php endif;?>
                    </td>
                    <td><?php if ($game->TotalNumber != 0 || $game->OverLine != 0.0):?>
                            <input type="checkbox" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'over','MoneyLine'=>$game->MoneyLineHome,'PointSpread'=>$game->PointSpreadHome,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadHomeLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_home_h_over'?>" /><span><select><option>OV <?php echo $game->TotalNumber.' '.($game->OverLine>0?'+':'').$game->OverLine?></option></select></span>
                        <?php endif;?>
                    </td></tr>



                    <tr>
                        <td></td><td></td><td><button class="clear" event_id="<?php echo $game->event_id?>">Clear</button></td><td><button class="clear" event_id="<?php echo $game->event_id?>">Clear</button></td>
                    </tr>
                <tr><td>&nbsp;</td></tr>
                <?php endif;?>
            <?php endforeach;?>
        <?php else:?>
            <div style="font-weight:bold;color:#d1231a;font-size:2em;background:rgba(225,225,255,0.5)">Updating Odds - Check back momentarily OR No odds yet</div>
        <?php endif;?>
        <input id="continue" type="submit" value="Continue" />
        </table>
    </div>
</div>
<script src="/assets/js/jquery.js"></script>
<script>
    $(document).ready(function() {
        var countChecked = function() {
            var n = $('input:checked').length;
            return n;
        }

        $('input[type=checkbox]').click(function(data) {
            var this_class = $(this).prop('class');
            if (this_class.length > 0) {
                $('.'+this_class).prop('checked',false);
                $(this).prop('checked',true);
            }
            var n = countChecked();
            if (n>6) {
                console.log('Maximum parlay bets reached.  Max = 6');
                $(this).prop('checked',false);
            }
        });

        $('.clear').click(function(data) {
            var event_id = $(this).attr('event_id');
            console.log(event_id);
            $('.'+event_id).prop('checked',false);
            data.preventDefault();
        });
    });
</script>
