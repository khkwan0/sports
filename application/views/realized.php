<div>
    <table>
        <?php foreach ($res as $value):?>
            <tr>
            <?php $properties = get_object_vars($value)?>
            <?php foreach ($properties as $property=>$val):?>
            <?php
                switch($property) {
                    case 'valid':
                    case 'id':$show = false; break;
                    default:$show = true;
                }
            ?>
            <?php if ($show):?>
                <td>
                <?php if ($property=='user_id'):?>
                    <a href="/admin/showBets/<?php echo $val?>">
                <?php endif;?>
                <?php echo $val?>
                <?php if ($property=='user_id'):?>
                    </a>
                <?php endif;?>
                </td>
            <?php endif;?>
            <?php endforeach;?>
            </tr>
        <?php endforeach;?>
    </table>
</div>
