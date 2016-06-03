<div>
    <form action="/admin/update_user" method="post">
        <input type="hidden" name="user_id" value="<?php echo $user->user_id?>" />
        <table>
            <?php $properties = get_object_vars($user)?>
            <?php foreach ($properties as $property=>$value):?>
                <tr>
                    <?php
                        switch($property) {
                            case 'user_id':$read_only = true;break;
                            case 'datetime_created':$read_only = true;break;
                            case 'balance':$read_only = true;break;
                            default:$read_only = false;break;
                        }
                    ?>
                    <td><?php echo $property?></td>
                    <td>
                        <?php if ($read_only):?>
                            <?php echo $value?>
                        <?php else:?>
                            <input name="<?php echo $property?>" value="<?php echo $value?>" />
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
        <div>
            <button><a href="/admin">Cancel</a></button>
            <input type="submit" value="Update" />
        </div>
    </form>
</div>
