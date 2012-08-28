<?php
require_once(dirname (__FILE__) .'/widgets.php'); 
// ---------------------------------------------------------------------------------

function lb_MapUser($wp_user_id, $user_id, $map = true)
{
    try
    {
        if ( $map )
        {
            $response = lb_post("map", array( 'primary_key' => $wp_user_id, 'user_id' => $user_id ));
        } else
        {
            $response = lb_post("unmap", array( 'user_id' => $user_id ));
        }
        return ($response['status'] == 'ok');
    } catch ( Exception $e )
    {
        echo "<div style='color: maroon'>Internal System Error! Error Message: " . $e->getMessage() . "</div>";
    }
}

//------------------------------------------------------------------------------
function lb_SaveProfile($user_id)
{
    $user = get_userdata($user_id);
    $map_user = isset($_POST['map_user']) ? '1' : '';

    update_usermeta($user_id, 'map_user', $map_user);
    lb_MapUser($user_id, $user->lanoba_id, $map_user);
}

//------------------------------------------------------------------------------
function lb_ShowMapping()
{
    $user = wp_get_current_user();

    if ( isset($_POST['token']) )
    {
        $response = lb_post("authenticate", array( 'token' => $_POST['token'] ));

        if ( $response['status'] == 'ok' )
        {
            $_SESSION['token'] = $_POST['token'];
            $_SESSION['response'] = $response;

            if ( lb_MapUser($user->ID, $response['user_id']) )
            {
                update_usermeta($user->ID, 'map_user', '1');
                update_usermeta($user->ID, 'lanoba_id', $response['user_id']);

                $msg = "<span style='color: green'>Your social account has been sucessfully linked to WordPress. You may now use it to access this site next time you login.</span>";
            } else
            {
                $msg = "<span style='color: maroon'>The account mapping process failed. Your Lanoba session might have expired, please try logging again.</span>";
            }
        }
    }

    $user = get_userdata($user->ID);

    $map_user = @$user->map_user;
    ?>
    <h3>Lanoba Social Account Connections</h3>

    <?php if ( $map_user ) : ?>
        <table  cellpadding='1' cellspacing='1' style="background-color: #e8e8e8; width: 100%">
            <tr><td style="width: 250px; background-color: #e8e8e8; height: 30px; padding: 2px;">Connect this account to social networks</td>
                <td style=" background-color: white;  padding: 5px;">
                    <input name="map_user" type="checkbox" id="map_user" value="1" checked='checked'/></td></tr>
        </table><br>
    <?php endif; ?>

    <div style="font-size: 10pt;">
        Use the Lanoba widget below to connect your Wordpress account to one or more social network accounts (Facebook, Twitter, etc...)
        <br/>This will allow you to sign-in to this site by simply signing in to any of these connected social networks. 
    </div><br> <?php echo $msg; ?><br>

    <?php
    lb_LoginWidget(lb_getSetting("lb_accountLink"), 0, 1, 1);
}
