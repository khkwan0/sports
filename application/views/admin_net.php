<div>
<?php foreach($balances as $date=>$users):?>
    <div>
        <h2>Week ending on: <?php echo date('l F dS,Y',strtotime($date)-7200)?> at 11:59 PM</h2>
    </div>
    <div>
        <table>
            <?php $net = $loss = 0.00;?>
            <tr><th>User ID</th><th>Name</th><th>Beginning Balance</th><th>Ending Balance</th><th>Gain/Loss</th></tr>
    <?php foreach ($users as $u):?>
        <?php if (!$u->is_admin):?>
            <tr>
                <td><?php echo $u->user_id?></td>
                <td><?php echo $u->username?></td>
                <td><?php echo $u->beginning_balance?></td>
                <td><?php echo number_format($u->balance,2,'.','')?></td>
                <?php $diff = $u->beginning_balance-$u->balance;?>
                <?php
                    if ($diff<0) {
                        $loss += abs($diff);
                    } else {
                        $net += $diff;
                    }
                ?>
                <td><?php echo number_format($diff,2,'.','')?></td>
            </tr>
        <?php endif;?>
    <?php endforeach;?>
        <table>
        <div>
            <ul>
                <li>Losses: <?php echo $loss?></li>
                <li>Gains : <?php echo $net?></li>
                <li>Total : <?php echo number_format($net-$loss,2,'.','')?></li>
            </ul>
        </div>
<?php endforeach;?>
</div>
