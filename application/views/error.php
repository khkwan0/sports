<div>
<?php
switch($msg) {
    case '1': $final_msg = 'duplicate username maybe?';
              break;
    default:break;
}
?>
    <p><?php echo $msg?> - <?php echo $final_msg?></p>
</div>
<div>
    <a href="/<?php echo $url?>">Back</a>
</div>
