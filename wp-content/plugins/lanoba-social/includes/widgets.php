<?php 

error_reporting(5); 

ob_start();

if ( !session_id() )
    session_start();
//---------------------------------------------------------------------------------
function lb_post($action, $postFields)
{
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => "https://api.lanoba.com/$action",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array_merge($postFields, array( 'api_secret' => lb_getSetting('lb_apiSecret') ))
    ));

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $response;
}
//-------------------------------------------------------------------------------

function lb_getSetting($settingName)
{      
   $settingsTxt = array("lb_loginformTxt"      => "<b>Or sign in using:</b>",
                        "lb_pageloginTxt"      => "<b>Sign in using:</b>", 
                        "lb_accountLink"       => "<b>Connect your account to:</b>",
                        "lb_promptEmailTxt"    => "<b>Lanoba Social User Auto-Registration</b><br/><br/>".
                                                  "<p>An email was not retrieved from your social account.</p><br/>".
                                                  "<p>Please fill it up below to proceed with the registration process.</p><br/>",
                        "lb_shareButton"       => "<img  src='//cdn.lanoba.com/img/wp_share.png' class='lb_sharebutton'/>",
                        "lb_redirectCustom"    => "/",
                        "lb_loginMeta"         => 1,
                        "lb_loginMetaDisable"  => 1,
                        "lb_loginForm"         => 1,       
                        "lb_regForm"           => 1,
                        "lb_loginComment"      => 1,
                        "lb_autoLinkUser"      => 1,
                        "lb_apiSecret"         => get_option('apiSecret'),
                        "lb_hostName"          => get_option('hostName'), 
                        "lb_autoRegister"      => get_option('autoRegister')); 
   
   $lbSetting = trim(get_option($settingName)); 
   
   if ((strlen($lbSetting) == 0))        
   { 
      if (isset($settingsTxt[$settingName]))
      {
          $lbSetting = $settingsTxt[$settingName];
          
      }else{          
          $lbSetting = 0;
      }      
   }
   return $lbSetting;
}
//------------------------------------------------------------------------------
function lb_Redirect($errorCode)
{
    if ( !empty($errorCode) )
        $errMsg = "&lbRedirect=$errorCode";

    header('Location: ' . get_home_url() . "/wp-login.php?action=register{$errMsg}");
    exit;
}
//------------------------------------------------------------------------------
function lb_LoginErrors($error)
{
    global $lb_error;

    return $lb_error . $error;
}
//-----------------------------------------------------------------------------------
function lb_RegisterFormInit()
{
    if ( !isset($_SESSION['response']) )
    {
        return; //if no user session related to Lanoba exists do nothing
    }

    if ( !isset($_GET['lbRedirect']) )
    {
        return; //if no user session related to Lanoba exists do nothing
    }
    ?>
    <script type='text/javascript'>
        jQuery(function($){
            $("#user_login").val('<?php echo $_SESSION['user_login'] ?>');
            $("#user_email").val('<?php echo $_SESSION['user_email'] ?>');
            $("#registerform").submit();
        });
    </script>

    <?php
}

//-------------------------------------------------------------------------------

function lb_ShareWidget($content)
{
    try
    {
        if ( !class_exists("DOMDocument") )
            return $content;

        $doc = new DOMDocument();

        if ( !$doc )
            return $content;

        if ( !$doc->loadHTML($content) )
            return $content;

        $imageTags = $doc->getElementsByTagName('img');

        if ( count($imageTags) )
            foreach ( $imageTags as $tag )
            {
                $photo = $tag->getAttribute('src');
                break;
            }
        else
            $photo = "http://www.lanoba.com/images/logo.png";

        $protocol = ($_SERVER['HTTPS'] ? 'https' : 'http') . "://";

        if ( strlen($photo) && !preg_match("/^(.*[:])?\/\//", $photo) )
        {

            if ( preg_match("/^\//i", $photo) )
            {
                $photo = $_SERVER['HTTP_HOST'] . $photo;
            } else
            {
                $photo = $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/" . $photo;
                $photo = $protocol . str_replace("//", "/", $photo);
            }
        }

        $stripped_content = preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x1F\x80-\xFF]/', ' ', strip_tags($content)));

        $title = rawurlencode(get_the_title($post->post_parent));
        $link = rawurlencode(get_permalink($post->post_parent));
        $description = rawurlencode(substr($stripped_content, 0, 200) . (strlen($stripped_content) > 200 ? '...' : ''));
        $photo = rawurlencode($photo);

        $widget = "<div style='width: 100%; display: inline-block'>" .
                  "<a style='float: right;' class='lb_sharebutton' href='javascript:void(0)' onclick='social.widgets.share({\"title\":\"$title\", \"description\":\"$description\"})'>" .
                lb_getSetting("lb_shareButton") . "</a></div>";

        return $widget . $content;
    } catch ( Exception $e )
    {
        return $content;
    }
} 

// ------------------------------------------------------------------------------

function lb_LoginWidget($txt, $disableLoggedIn = 0, $display = 1, $profilePage = 0)
{
    if (!$profilePage && $disableLoggedIn && is_user_logged_in())
    return;
    
    if (!$profilePage)
    {
//check whether there is a return post from the widget to proceed with authentication instead of displaying the widget        
       lb_LanobaPost(); 
    }    
    $widgetID = 'login' . uniqid();

    $widget = "$txt".(empty($txt)?"":"<br/>")."<div id='$widgetID'></div>
         <script type='text/javascript'>
           (social.widgets.login({ container: '$widgetID' ".($profilePage?", link: true":"")."})); 
         </script>";

    if ( $display )
    {
        echo $widget;
    } else
    {
        return $widget;
    }
}
//------------------------------------------------------------------------------
function lb_loginForm()
{              
  lb_LoginWidget(lb_getSetting("lb_loginformTxt"));  
}

//----------------------------------------------------------------------
function lb_metaPanel()
{
  lb_LoginWidget(lb_getSetting("lb_pageloginTxt"), lb_getSetting('lb_loginMetaDisable'));
}
//------------------------------------------------------------------------------
function lb_displayWidgets()
{
    if (lb_getSetting('lb_loginForm')) 
    {
        add_action('login_form', 'lb_loginForm');
    }    
    if (lb_getSetting('lb_regForm')) 
    {
        add_action('register_form', 'lb_loginForm');
    }
    if ( lb_getSetting("lb_loginMeta") ) 
    { 
        add_action('wp_meta', "lb_metaPanel");
    }
    
    if ( lb_getSetting('lb_loginComment') )
    {
        add_filter('comment_form_defaults', 'lb_addLoginToNeedLogin');
        add_action ('comment_form_top', 'lb_addLoginToCommentFormTop');
    }
}

//---------------------------------------------------------------------------------
function lb_addLoginToNeedLogin($default_fields)
{
    //show the login widget near the Needs Login link    
    if ( is_array($default_fields) && comments_open() && !is_user_logged_in() )
    {   
         if ( !isset($default_fields['must_log_in']) )
         {
            $default_fields['must_log_in'] = '';
         }
         $default_fields['must_log_in'] .= lb_LoginWidget(lb_getSetting("lb_pageloginTxt"), 1, 0);
         
    }
    return $default_fields;
}
//---------------------------------------------------------------------------------

function lb_addLoginToCommentFormTop()
{
    //Comments are open and the user is not logged in
    if (comments_open () && !is_user_logged_in ())
    {  
        lb_LoginWidget(lb_getSetting("lb_pageloginTxt"), 1, 0);	
    }
}