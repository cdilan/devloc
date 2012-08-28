<?php

require_once(dirname (__FILE__) .'/profile.php');

// ------------------------------------------------------------------------------
function lb_loginRedirect()
{
    $redirectURL = lb_getSetting("lb_loginRedirect");

        switch ( $redirectURL )
        {
            case 'home':
                $redirectURL = get_home_url();
                break;
            case 'profile':
                $redirectURL = get_home_url() . "/wp-admin";
                break;
            case 'custom':
                $redirectURL = lb_getSetting("lb_redirectCustom");
                break;        
            default:
                $redirectURL = get_home_url();
                break;
        }
     return $redirectURL;
} 
//------------------------------------------------------------------------------
function lb_SigninUser($wp_user_id)
{
    wp_set_auth_cookie($wp_user_id);

    session_unset();
    session_destroy();    
    
    $redirectURL = $_SERVER['REQUEST_URI'];
    
    if (preg_match("/wp-login\.php/", $redirectURL)): //when the login request comes from login form        
       $redirectURL =  lb_loginRedirect();
    endif;
    
    header("Location: $redirectURL");
    exit;
}

//------------------------------------------------------------------------------
function lb_Logout()
{
    session_unset();
    session_destroy();
    
    $redirectURL = lb_loginRedirect();
     
    header("Location: $redirectURL");
    exit;
}
//---------------------------------------------------------------------------------

function lb_LanobaPost()
{
    if ( !isset($_POST['token']) )
       return;
    
    $response = lb_post("authenticate", array( 'token' => $_POST['token'] ));

    if ( $response['status'] != 'ok' )
       return;
   
    $_SESSION['response'] = $response;
    $_SESSION['token'] = $_POST['token'];

//check the existence of the user by id    
    if ( isset($response['primary_key']) )
    {
        $user = get_user_by('id', $response['primary_key']);
        
    // a user with a connected account exists
        if ($user !== false)
        {
           if ( isset($user->lanoba_id) )
              $lanoba_id = $user->lanoba_id;

           if ( isset($user->map_user) )
              $map_user = $user->map_user;

           if ((@$map_user) && (@$lanoba_id == $response['user_id']) )        
            lb_SigninUser($user->ID);        
        }        
    }
    
// check the existence of the user by email  
    
    if (isset($response['profile']['email']) && !empty($response['profile']['email']) && lb_getSetting('lb_autoLinkUser')) 
    {
         $user = get_user_by('email', $response['profile']['email']);
        
    // a user with a connected account exists
        if ($user !== false)
        {
//if linking with Lanoba is successful proceed with update and signin            
            if (lb_MapUser($user->ID, $response['user_id']))
            {
               update_usermeta($user->ID, 'lanoba_id', $response['user_id']);
               update_usermeta($user->ID, 'map_user', "1");

               lb_SigninUser($user->ID);
            }
        }
    }

//if the user needs to register and auto registration is enabled
    if ( get_option("users_can_register") && lb_getSetting("lb_autoRegister") )
    {
        lb_SaveUserData();

        if ( !lb_ValidateData() )
           lb_Redirect(1);       // request valid data from the user
        
        lb_RegisterUser();   //if everything is fine register and signin the user
    }
}
//-----------------------------------------------------------------
function lb_uniqueUsername($username)
{
    if (!empty($username))
    {
       $counter = 1;  
       $orgUsername = $username;
      
       while(username_exists($username))
       {
         $username = $orgUsername.$counter++;   
       }
    }
    return $username;
}
//-----------------------------------------------------------------
function lb_generateUsername()
{
//start cheking the validity and uniqueness of the username provided from social networks     
    $username = lb_uniqueUsername(trim($_SESSION['response']['profile']['username']));
    $first_name = trim($_SESSION['response']['profile']['first_name']);
    $last_name = trim($_SESSION['response']['profile']['last_name']);
    
    // if no username retrieved from networks  generate it from name and surname
    if (empty($username) || !validate_username($username))
    {
        $username = lb_uniqueUsername($first_name.$last_name);
    }
    
//last solution, use random username    
    if (empty($username) || !validate_username($username))
    {
        $username = lb_uniqueUsername('wp_user');
    }  
    
    return $username;
}
//-----------------------------------------------------------------
function lb_SaveUserData()
{      
    $email = trim($_SESSION['response']['profile']['email']); 
   
//user has no email and settings set to no prompt, generate automatically the email   
    if (!is_email($email))
    {
        if (!lb_getSetting('lb_promptEmail'))
        {
            $email = 'user'.uniqid()."@yoursite.com";
            
            while (email_exists($email))
            {
               $email = 'user'.uniqid()."@yoursite.com";  
            }
        }
    }  

    $_SESSION['user_email'] = $email;
    $_SESSION['user_login'] = lb_generateUsername();
}

//------------------------------------------------------------------------------
function lb_ValidateData()
{
    $user_email = $_SESSION['user_email'];
    $user_login = $_SESSION['user_login'];

    $isValid = !username_exists($user_login) && !email_exists($user_email) &&
            is_email($user_email) && validate_username($user_login);

    return $isValid;
}

//------------------------------------------------------------------------------
function lb_RegisterUser()
{
    try
    {
        if ( !isset($_SESSION['response']) )
        {
            lb_Redirect();
        }

        $lb_profile = $_SESSION['response']['profile'];

        $display_name = trim($lb_profile['first_name'] . ' ' . $lb_profile['last_name']);

        if ( empty($display_name) )
            $display_name = $_SESSION['user_login'];

        $user_login = $_SESSION['user_login'];
        $user_email = $_SESSION['user_email'];
        $user_pass = wp_generate_password();

        $wp_user_id = wp_insert_user(array( 'ID' => '',
            'user_pass' => $user_pass,
            'user_login' => $user_login,
            'user_email' => $user_email,
            'user_url' => $lb_profile['link'],
            'first_name' => $lb_profile['first_name'],
            'last_name' => $lb_profile['last_name'],
            'display_name' => $display_name ));

        if ( is_int($wp_user_id) )
        {
            //associate the account with Lanoba if successfully registered
            lb_MapUser($wp_user_id, $_SESSION['response']['user_id']);

            update_usermeta($wp_user_id, 'lanoba_id', $_SESSION['response']['user_id']);
            update_usermeta($wp_user_id, 'map_user', "1");

            lb_SigninUser($wp_user_id);
        }
    } catch ( Exception $e )
    {
        lb_Redirect();
    }
}

//--------------------------------------------------------------------------------
function lb_RegisterFormPost()
{

    global $lb_error;

    if ( !isset($_SESSION['response']) )
    {
        return; //if no user session related to Lanoba exists do nothing
    }

    if ( isset($_POST['user_login']) )
        $_SESSION['user_login'] = $_POST['user_login'];

    if ( isset($_POST['user_email']) )
        $_SESSION['user_email'] = $_POST['user_email'];

    if ( lb_ValidateData() )
    {
        lb_RegisterUser();
    } else
    { 
        $lb_error = lb_getSetting('lb_promptEmailTxt');
    }
}
