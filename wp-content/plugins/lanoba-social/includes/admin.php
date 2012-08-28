<?php

//------------------------------------------------------------------------------

function lb_enqueue_js()
{
    wp_enqueue_script("jquery");
    wp_enqueue_script("socialJS", ($_SERVER['HTTPS'] ? 'https' : 'http') . "://" . lb_getSetting('lb_hostName') . '/social.js');
    wp_enqueue_script("lanobaJS", plugins_url('lanoba.js', __FILE__), false, '2-1-10');
}
//----------------------------------------------------------------------------
function lb_enqueue_styles()
{
    wp_enqueue_style("lanobaCSS", plugins_url('lanoba.css', __FILE__), false, '2-1-10');
}

//------------------------------------------------------------------------------
function lb_PluginOptions()
{     
    global $adminErrorMessage;        
    
    if ( !current_user_can('manage_options') )
    {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
 
    if (strlen($adminErrorMessage)):
?>        
  <p><b>Lanoba social plugin requires PHP 5.2 or newer with JSON support, php cURL and sessions to function! <br/>
          The following errors were detected:</b><br/><br/>
         
       <span style="color: maroon;">
           <?php  echo $adminErrorMessage;?>
       </span>
   </p>
<?php    
        return;
    endif;           
   
   $lb_fields = array('lb_apiSecret', 'lb_hostName', 'lb_autoRegister', 'lb_promptEmail',
                       'lb_promptEmailTxt', 'lb_pageloginTxt', 'lb_loginformTxt',
                       'lb_shareButton','lb_loginRedirect', 'lb_redirectCustom', 'lb_autoLinkUser',
                       'lb_loginMeta', 'lb_loginMetaDisable', 'lb_loginComment', 
                       'lb_accountLink', 'lb_loginForm', 'lb_regForm'); 

    if (isset($_POST['Submit']))
    {
        foreach ($lb_fields as $val)
        {  
            if (isset($_POST[$val]))
              update_option($val, stripslashes(trim($_POST[$val])));               
            else           
                update_option($val, "0");           
        }
    } 
    ?> 
    <br><br>
    <h2>Lanoba Social Plugin Settings</h2> 	
    <div class="wrap"> 
        <form name="form1" method="post" action="">
            <table class='settingsTBL' cellpadding="1" cellspacing="1">   
                <tr><td colspan="2" class="tbl_label">Lanoba Account</td></tr>
                <tr><td colspan="2">
                        <p>This plugin requires an '<b>API Secret</b>' and a '<b>Hostname</b>' to function. 
                        This information can be found in the 'Settings' section of your Lanoba Account. If do not already have one, you can sign up
                        for a free account with Lanoba here: <a href="http://www.lanoba.com/manager/login" target="_blank">Signup for Lanoba</a>.
                   </p>
                   <p>In your <a href='https://www.lanoba.com/manager/settings'>Lanoba Account Manager</a> you need to set up at least one social network (Facebook, Twitter, etc...) to benefit from the login and sharing
                    capabilities of the plugin. The steps for setting up a social network can be found in the 'Network Setup' section on the left sidebar of the 
                      <a href="http://www.lanoba.com/documentation/getting-started/introduction"> Lanoba Developers Page</a>.
                    </p>
                    <p style="font-size: 11pt;"><img src="<?php echo WP_PLUGIN_URL."/lanoba-social/images/hint.jpg"?>"/>
                        <b>Hint:</b> You can reset a text value of any of the settings below to its default values by simply clearing its content and saving the changes.</p>
                </td></tr>    
                <tr><td colspan="2" class="tbl_label">Setup</td></tr>
                <tr><td> <b>API Secret</b> (e.g; A1bcD2e4FkW_M.89_z3i.XyzXa8lkwt...)</td>
                    <td><input type="text" name="lb_apiSecret" style='font-size: 8pt;' value="<?php echo lb_getSetting('lb_apiSecret'); ?>" size="80" /></td></tr>
                <tr><td><b>Hostname</b> (e.g; s87654321.lanoba.com) </td>
                    <td><input type="text" name="lb_hostName" value="<?php echo lb_getSetting('lb_hostName'); ?>" size="30" /></td></tr>                
                <tr><td colspan="2" class="tbl_label">Registration</td></tr>
                <tr><td><b>Automatically Register Users</b></td>
                    <td><input type="checkbox" name="lb_autoRegister" <?php echo lb_getSetting('lb_autoRegister') ? 'checked="checked"' : "" ?> />
                        &nbsp;&nbsp;<span style="color: maroon">(Membership <b>'Anyone can register'</b> option in your general settings needs to be enabled)</span></td>
                </tr> 
                 <tr><td><b>If a registered user with the same email address is detected</b></td>
                    <td><input type="checkbox" name="lb_autoLinkUser" <?php echo lb_getSetting('lb_autoLinkUser') ? 'checked="checked"' : "" ?> />
                    &nbsp;&nbsp;Automatically link to Lanoba</td>
                </tr> 
                <tr><td><b>When no email is retrieved from social network</b></td>
                    <td><input type="radio" name="lb_promptEmail" value='0' <?php echo lb_getSetting('lb_promptEmail') ?  "" : 'checked="checked"' ?>/> Don't prompt for email, use arbitrary email and continue. (<b>Default</b>)<br/>
                        <input type="radio" name="lb_promptEmail" value='1' <?php echo lb_getSetting('lb_promptEmail') ? 'checked="checked"' : "" ?>/> Prompt user for email, use the following message:<br/><br/>
                        <textarea cols="60" rows="5" name="lb_promptEmailTxt"><?php echo htmlspecialchars(lb_getSetting('lb_promptEmailTxt')); ?></textarea>
                    </td></tr>
                <tr><td colspan="2" class="tbl_label">Login</td></tr>   
                 <tr><td><b>When user is logged in</b></td>
                    <td><input type="checkbox" name="lb_loginMetaDisable" <?php echo lb_getSetting('lb_loginMetaDisable') ? 'checked="checked"' : "" ?> /> 
                        Hide login widget from the 'Meta' side panel</td>
                </tr> 
                <tr><td><b>Show login widget in 'Meta' sidebar</b></td>
                    <td><input type="checkbox" name="lb_loginMeta" <?php echo lb_getSetting('lb_loginMeta') ? 'checked="checked"' : "" ?> /></td>
                </tr>                 
                 <tr><td><b>Show login widget on login form</b></td>
                    <td><input type="checkbox" name="lb_loginForm" <?php echo lb_getSetting('lb_loginForm') ? 'checked="checked"' : "" ?> /></td>
                </tr> 
                 <tr><td><b>Show login widget on registration form</b></td>
                    <td><input type="checkbox" name="lb_regForm" <?php echo lb_getSetting('lb_regForm') ? 'checked="checked"' : "" ?> /></td>
                </tr> 
                 <tr><td><b>Show login widget on top of comment form</b></td>
                    <td><input type="checkbox" name="lb_loginComment" <?php echo lb_getSetting('lb_loginComment') ? 'checked="checked"' : "" ?> /></td>
                </tr> 
                <tr><td><b>After user authentication (from Login Form) redirect user to</b></td>
                    <td><?php $redirectURL = lb_getSetting('lb_loginRedirect');?>
                        <input type="radio" name="lb_loginRedirect" value="home"    <?php echo ($redirectURL== 'home') ? 'checked="checked"':'' ?>/> Homepage (<b>Default</b>)<br/>
                        <input type="radio" name="lb_loginRedirect" value="profile" <?php echo ($redirectURL == 'profile') ? 'checked="checked"' : "" ?>/> Profile Dashboard<br/>
                        <input type="radio" name="lb_loginRedirect" value="custom"  <?php echo ($redirectURL == 'custom') ? 'checked="checked"' : "" ?>/> Custom URL:&nbsp;
                        <input type="text" size="50" name="lb_redirectCustom" value="<?php echo htmlspecialchars(lb_getSetting('lb_redirectCustom')); ?>"/>
                    </td>
                </tr>
                 <tr><td><b>Text to show above the widget in the login page</b></td>
                    <td><textarea cols="60" rows="2" name="lb_loginformTxt"><?php echo htmlspecialchars(lb_getSetting('lb_loginformTxt')); ?></textarea>
                    </td>
                </tr> 
                 <tr><td><b>Text to show above the widget in the other pages </b></td>
                    <td><textarea cols="60" rows="2" name="lb_pageloginTxt"><?php echo htmlspecialchars(lb_getSetting('lb_pageloginTxt')); ?></textarea>
                    </td>
                </tr>  
                 <tr><td><b>Text to show above the widget in the user profile page</b></td>
                    <td><textarea cols="60" rows="2" name="lb_accountLink"><?php echo htmlspecialchars(lb_getSetting('lb_accountLink')); ?></textarea>
                    </td>
                </tr> 
                  <tr><td colspan="2" class="tbl_label">Sharing</td></tr>
                  <tr><td><b>Sharing widget's button</b></td>
                    <td><b>Image tag</b> (e.g;  &lt;img src='your_image_url'&gt;) <b>or plain text </b>(e.g; 'Share This'):<br/>
                        <input type="text" size="70" name="lb_shareButton" value="<?php echo htmlspecialchars(lb_getSetting('lb_shareButton')); ?>"/>
                    </td>
                </tr>                
                <tr><td colspan=2 align="right">
                        <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
                    </td>
                </tr> 
            </table>
        </form>
    </div>

    <?php
}   
?>
