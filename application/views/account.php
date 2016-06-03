<div>
    <div>
        <?php if (strlen($error_msg)):?>
        <div>
            <?php
                switch($error_msg) {
                    case '1':
                        echo 'Password Confirmation Failure';
                        break;
                    case '2':
                        echo 'Success';
                        break;
                    case '3':
                        echo 'Invalid old password';
                        break;
                    default:break;
                }
            ?>
        </div>
        <?php endif;?>
        <div><h2>Change Password</h2></div>
        <form method="post" action="/user/changepassword">
            <div>
                <table>
                    <tr><td>Old password</td><td><input type="password" name="old" /></td></tr>
                    <tr><td>New password</td><td><input type="password" name="new" /></td></tr>
                    <tr><td>Confirm</td><td><input type="password" name="confirm" /></td></tr>
                </table>
            </div>
            <div><input type="submit" value="Change password" /></div>
        </form>
    </div>
</div>
