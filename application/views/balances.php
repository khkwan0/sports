<style>
    div {
        font-family: Arial, Helvetica, Sans-Serif;
    }

    li a {
        text-decoration:none;
    }
</style>
<div style="padding-left:10px;padding-right:10px;padding-top:10px;font-family:Arial, Helvetica, Sans_Serif;">
    <div>
        <h2>Chief Action Gaming</h2>
    </div>
    <div>
        <table>
            <tr><td>Current:</td><td id="current_balance"><?php echo number_format($balance - $default_balance + $pending,2)?></td></tr>
            <tr><td>Available:</td><td id="available_balance"><?php echo number_format($balance,2)?></td></tr>
            <tr><td>Pending:</td><td id="pending_balance"><?php echo number_format($pending,2)?></td></tr>
        </table>
    </div>
</div>
<div>
    <ul>
        <li><a href="/odds">Bet the Board</a></li>
        <li><a href="/parlay">Parlay</a></li> 
        <li><a href="/bets">Pending Wagers</a></li>
        <li><a href="/bets/history">Bet History</a></li>
        <li><a href="/user/account">Change Password</a></li>
        <li><a href="/archives">Archives</a></li>
        <li><a href="/logout">Logout</a></li>
        <?php if ($this->session->is_admin):?>
            <li><a href="/admin">Admin</a></li>
        <?php endif;?>
    </ul>
</div>
<!--
<div>
    <h2>News</h2>
    <div>
        <p>Nov 12, 2015</p>
        <p>NCAAMB odds available</p>
    </div>
</div>
-->
