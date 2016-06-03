<style>
    .right_header {
        font-size:2em;
        font-family:Arial, Helvetica, Sans-Serif;
        padding: 10px 10px 10px 10px;
        background-image: url('/assets/images/banner_background.jpg'); 
        height: 200px;
    }
</style>
<div class="right_header">
    <div>
        <span style="padding-left:100px; font-size:1.0em;color:yellow;font-weight:bold;">Server Time: <?php echo date('l, F jS, Y g:i A', time())?></span>
        <?php if ($this->session->userdata('is_admin')):?>
            <span style="padding-left:50px; font-size:0.5em;"><a href="/admin">Admin</a></span>
        <?php endif;?>
    </div>
</div>
