<style>
    #user_grid {
        font-family: Arial;
        font-size: 0.85em;
    }
    #user_grid tr td { text-align:center; }
    .even { background-color: #eeeeee; }
</style>
<div>
    <h3>Add New</h3>
    <div>
        <form action="/createuser" method="post">
        Username: <input name="username" /> Password: <input name="pword" /><input type="submit" value="Save" />
        </form>
    </div>
</div>
<?php if (isset($users_raw)): ?>
<h3>Total Pending Bets</h3>
<div id="total_pending"></div>
<h3>Total Current (If positive, then the book is paying out)</h3>
<div id="total_current"></div>
<div>
    <table id="user_grid">
        <?php 
            $first_element = array_shift($users_raw);
            $properties = get_object_vars($first_element);
            array_unshift($users_raw, $first_element);
            $total_pending = $total_current = 0.00;
        ?>
        <tr>
        <?php foreach($properties as $property=>$value):?>
            <th><?php echo $property?></th>
        <?php endforeach;?>
            <th>Current</th>
            <th>Pending</th>
            <th></th><th></th><th></th>
        </tr>
        <?php $ctr = 0?>
        <?php foreach ($users_raw as $user):?>
            <tr <?php echo ($ctr%2==0?'class="even"':'');?>>
            <?php if (!isset($pending[$user->user_id])) $pending[$user->user_id] = 0.00?>
            <?php foreach ($properties as $property=>$value):?>
                <?php if ($property == 'is_active'):?>
                <td id="active_<?php echo $user->user_id?>">
                <?php else:?>
                <td>
                <?php endif;?>
                    <?php if ($property == 'username'):?>
                        <a href="/admin/showbets/<?php echo $user->user_id?>"><?php echo $user->$property?></a>
                    <?php else:?>
                        <?php echo $user->$property?>
                    <?php endif;?>
                </td>
            <?php endforeach;?>
            <td><?php echo number_format($user->balance - $user->default_balance + $pending[$user->user_id],2)?></td>
            <td><?php echo number_format($pending[$user->user_id],2)?></td>
            <?php $total_pending += (!$user->is_admin)?$pending[$user->user_id]:0.00;?>
            <?php $total_current += (!$user->is_admin)?($user->balance - $user->default_balance + $pending[$user->user_id]):0.00?>
            <td><button class="deactivate" user_id="<?php echo $user->user_id?>">Deactivate</button></td>
            <td><button class="activate" user_id="<?php echo $user->user_id?>">Activate</button></td>
            <td><button class="edit" user_id="<?php echo $user->user_id?>"><a href="/admin/edit/<?php echo $user->user_id?>">Edit</a></button></td>
            </tr>
            <?php $ctr++?>
        <?php endforeach;?>

    </table>
</div>
<?php endif;?>
<script src="/assets/js/jquery.js"></script>
<script>
    $(document).ready(function() {
        $('#total_pending').html('<?php echo $total_pending?>');
        $('#total_current').html('<?php echo $total_current?>');

        $('.deactivate').click(function() {
            var user_id = $(this).attr('user_id');
            $.post('/ajax/deactivateUser',
                {
                    'user_id': user_id
                },function(data) {
                    if (data == '1') {
                        $('#active_'+user_id).html('0');
                    }
                });
        });
        $('.activate').click(function() {
            var user_id = $(this).attr('user_id');
            $.post('/ajax/activateUser',
                {
                    'user_id': user_id
                },function(data) {
                    if (data == '1') {
                        $('#active_'+user_id).html('1');
                    }
                });
        });
                

    });
</script>
