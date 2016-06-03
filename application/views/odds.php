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

a:visited {
    color:black;
}

a:link {
    color:black;
}
</style>
<div style="background-image:url('/assets/images/basketball_background.jpg')">
    <div>
        <?php $no_logo = false?>
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
        <?php elseif ($sport == 'tennis'):?>
            <img src='/assets/images/tennis/tennis.png' width="125" />
        <?php elseif ($sport =='mma'):?>
            <img src='/assets/images/mma/mma.jpg' width="125" />
        <?php endif;?>
    </div>
    <div>
        <input type="submit" value="Place bets" />
        <table class="board_grid">
            <tr class="t_header" style="background-color:rgba(51,152,153,0.5);color:white;font-weight:bold;font-size:25px;text-align:left;"><th></th><th>Sport: <?php echo $sport?></th><th>Spread</th><th>Money Line</th><th>Total Points</th></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
        <?php $ctr = 0;?>
        <?php if (isset($odds) && count($odds)):?>
            <?php foreach ($odds as $game):?>
                <?php $class = ($ctr%2)?'white':'gray'?>
                <tr class="<?php echo $class?>">
                    <td style="text-align:center;font-weight:bold;color:#d1231a;" colspan="5">
                    <?php echo strtoupper(date('l, F jS, Y g:i A',strtotime($game->MatchTime)))?>
                    </td>
                </tr>
                <tr class="<?php echo $class?>"><td colspan="5">&nbsp;</td></tr>
                <tr class="<?php echo $class?>" style="font-weight:bold">
                    <td colspan="2">Maximum Wager:</td>
                    <td>300.00</td>
                    <td>300.00</td>
                    <td>300.00</td>
                </tr>
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
                    <?php elseif ($sport == 'nhl' || $sport =='nba'):?>
                        <?php $home_logo = str_replace(' ', '_', str_replace('.','',$game->HomeTeam)).'.svg'?>
                        <?php $away_logo = str_replace(' ', '_', str_replace('.','',$game->AwayTeam)).'.svg'?>
                    <?php elseif ($sport == 'mma' || $sport == 'tennis'):?>
                        <?php $no_logo = true;?>
                    <?php else:?>
                        <?php $home_logo = str_replace(' ', '_', str_replace('.','',$game->HomeTeam)).'_logo.svg'?>
                        <?php $away_logo = str_replace(' ', '_', str_replace('.','',$game->AwayTeam)).'_logo.svg'?>
                    <?php endif;?>



                    <tr class="<?php echo $class?>">
                        <td>
                            <?php if ($no_logo):?>
                                &nbsp;
                            <?php else:?>
                                <img width="40px" height="40px" src="/assets/images/<?php echo strtolower($sport)?>/<?php echo $away_logo?>" />
                            <?php endif;?>
                        </td>
                        <td><a target="_blank" style="text-decoration:none" href="https://www.google.com?q=<?php echo urlencode($game->HomeTeam)?>+vs+<?php echo urlencode($game->AwayTeam)?>"><?php echo $game->AwayTeam?></a></td>
                        <td>
                            <?php if ($game->PointSpreadAway != 0.0 || $game->PointSpreadAwayLine != 0):?>
                                <input type="hidden" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'spread','MoneyLine'=>$game->MoneyLineAway,'PointSpread'=>$game->PointSpreadAway,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadAwayLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_away_h_spread'?>" /><input size="7" name="<?php echo $game->event_id.'_away_bet_spread'?>" /><span><select><option><?php echo $game->PointSpreadAway.' '.($game->PointSpreadAwayLine>0?'+':'').$game->PointSpreadAwayLine?></option></select></span>
                            <?php endif;?>
                        </td>
                        <td>
                            <?php if ($game->MoneyLineAway != 0.0):?>
                                <input type="hidden" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'moneyline','MoneyLine'=>$game->MoneyLineAway,'PointSpread'=>$game->PointSpreadAway,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadAwayLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_away_h_moneyline'?>" /><input size="7" name="<?php echo $game->event_id.'_away_bet_moneyline'?>" /><span><select><option><?php echo ($game->MoneyLineAway>0?'+':'').$game->MoneyLineAway?></option></select</span>
                            <?php endif;?>
                        </td>
                        <td>
                            <?php if ($game->UnderLine!= 0.0):?>
                                <input type="hidden" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'under','MoneyLine'=>$game->MoneyLineAway,'PointSpread'=>$game->PointSpreadAway,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadAwayLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_away_h_under'?>" /><input size="7" name="<?php echo $game->event_id.'_away_bet_under'?>" /><span><select><option>UN <?php echo $game->TotalNumber.' '.($game->UnderLine>0?'+':'').$game->UnderLine?></option></select</span>
                            <?php endif;?>
                        </td>
                    </tr>

                <tr class="<?php echo $class?>">
                    <td style="width:40px">
                        <?php if ($no_logo):?>
                            &nbsp;
                        <?php else:?>
                            <img width="40px" height="40px" src="/assets/images/<?php echo strtolower($sport)?>/<?php echo $home_logo?>" />
                        <?php endif;?>
                    </td>
                    <td><a target="_blank" style="text-decoration:none" href="https://www.google.com?q=<?php echo urlencode($game->HomeTeam)?>+vs+<?php echo urlencode($game->AwayTeam)?>"><?php echo $game->HomeTeam?></a></td>
                    <td>
                        <?php if ($game->PointSpreadHome != 0.0 || $game->PointSpreadHomeLine != 0.0):?>
                            <input type="hidden" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'spread','MoneyLine'=>$game->MoneyLineHome,'PointSpread'=>$game->PointSpreadHome,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadHomeLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_home_h_spread'?>" /><input size="7" name="<?php echo $game->event_id.'_home_bet_spread'?>" /><span><select><option><?php echo $game->PointSpreadHome.' '.($game->PointSpreadHomeLine>0?'+':'').$game->PointSpreadHomeLine?></option></select></span>
                        <?php endif?>
                    </td>
                    <td>
                        <?php if ($game->MoneyLineHome):?>
                            <input type="hidden" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'moneyline','MoneyLine'=>$game->MoneyLineHome,'PointSpread'=>$game->PointSpreadHome,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadHomeLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_home_h_moneyline'?>" /><input size="7" name="<?php echo $game->event_id.'_home_bet_moneyline'?>" /><span><select><option><?php echo ($game->MoneyLineHome>0?'+':'').$game->MoneyLineHome?></option></select></span>
                        <?php endif;?>
                    </td>
                    <td>
                        <?php if ($game->OverLine):?>
                            <input type="hidden" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'over','MoneyLine'=>$game->MoneyLineHome,'PointSpread'=>$game->PointSpreadHome,'OverLine'=>$game->OverLine,'UnderLine'=>$game->UnderLine,'TotalNumber'=>$game->TotalNumber,'SpreadLine'=>$game->PointSpreadHomeLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_home_h_over'?>" /><input size="7" name="<?php echo $game->event_id.'_home_bet_over'?>" /><span><select><option>OV <?php echo $game->TotalNumber.' '.($game->OverLine>0?'+':'').$game->OverLine?></option></select></span>
                        <?php endif;?>
                    </td></tr>

                    <?php if ($game->DrawLine>0):?>
                        <tr class="<?php echo $class?>">
                        <td><img width="40px" height="40px" src="/assets/images/<?php echo strtolower($sport)?>/<?php echo $away_logo?>" /></td>
                        <td>Draw</td>
                        <td></td>
                        <td>
                            <?php if ($game->DrawLine>0):?>
                                <input type="hidden" value='<?php echo openssl_encrypt(base64_encode(json_encode(array('BetType'=>'moneyline','MoneyLine'=>$game->DrawLine,'id'=>$game->id,'HomeTeam'=>$game->HomeTeam,'AwayTeam'=>$game->AwayTeam,'MatchTime'=>$game->MatchTime,'sport'=>strtolower($sport)))),'AES-256-CBC', 'letmeinbdr',0,'f#r?a=t4KiN-1BdH')?>' name="<?php echo $game->event_id.'_draw_h_moneyline'?>" /><input size="7" name="<?php echo $game->event_id.'_draw_bet_moneyline'?>" /><span><select><option><?php echo ($game->DrawLine>0?'+':'').$game->DrawLine?></option></select</span>
                            <?php endif;?>
                        </td>
                        <td></td>
                        </tr>
                    <?php endif;?>
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
            <?php endforeach;?>
        <?php else:?>
            <div style="font-weight:bold;color:#d1231a;font-size:2em;background:rgba(225,225,255,0.5)">Updating Odds - Check back momentarily OR No odds yet</div>
        <?php endif;?>
        </table>
        <input type="submit" value="Place bets" />
    </div>
</div>
