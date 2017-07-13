<?php
/**
 * @package Survicate
 */
/*
Plugin Name: Survicate
Plugin URI: http://survicate.com/
Description: Survicate helps you understand your Customers with targeted website surveys, drive conversions via notifications and profile visitors for better marketing campaigns.
Version: 2.0.2
Author: Survicate
Author URI: http://survicate.com/
*/

if ( !function_exists( 'add_action' ) ) {
    exit;
}

/* MENU */
add_action( 'admin_menu', 'survicate_admin_menu' );
function survicate_admin_menu() {
    add_options_page( 'Survicate', 'Survicate', 'manage_options', 'survicate', 'survicate_options_page' );
}

/* OPTIONS PAGE */
function survicate_options_page() {
    if(get_option('survicate-cms-key') !== false){ //LOGGED IN
        if(get_option('survicate-tracking-code') !== false){ // DOMAIN SELECTED
            survicate_dashboard();
        }else{ // SEL DOMAIN
            survicate_select_domain();
        }
    }else{ // NOT LOGGED IN
        if(function_exists('curl_version')){
            if($_GET['action']=='signup'){
                survicate_sign_up();
            }elseif($_GET['action']=='manual'){
                survicate_get_tracking_code();
            }else{
                if(get_option('survicate-tracking-code') !== false){
                    survicate_dashboard();
                }else{
                    survicate_sign_in();
                }
            }
        }else{
            if(get_option('survicate-tracking-code') !== false){
                survicate_dashboard();
            }else{
                survicate_get_tracking_code();
            }
        }
    }
}

/* SIGN IN */
function survicate_sign_in(){
    $current_user = wp_get_current_user();
    ?>
    <div class="wrap">
        <img src="//survicate.com/wp-content/uploads/2014/11/survicate-whitebg_transp_cropped_50px-high.png" alt="Survicate" />
    <?php if(get_option('survicate-error') !== false): ?>
        <div class="card">
            <p style="color: red;"><?php echo get_option('survicate-error'); ?></p>
        </div>
    <?php delete_option('survicate-error'); endif; ?>
        <div class="card">
            <h1 class="title">Hello</h1>
            <p>Please sign in to your Survicate account</p>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <input type="hidden" name="action" value="survicate_signin">
                <table class="form-table">
                    <tr valign="top">
                    <th scope="row">Email</th>
                    <td><input type="text" name="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" autocomplete="off" /></td>
                    </tr>
                    <tr valign="top">
                    <th scope="row">Password</th>
                    <td><input type="password" name="password" value="" autocomplete="off"/></td>
                    </tr>
                </table>
                <?php submit_button('Sign in'); ?>
            </form>
        </div>
        <div class="card" style="text-align: center;">
            <h4>Don't have an account yet?</h4>
            <a href="?page=survicate&amp;action=signup" class="page-title-action">Create account</a>
        </div>
        <div class="card" style="text-align: center;">
            <p>If for some reason you cannot login or create your account from here, please go to <a href="https://survicate.com" target="_blank">https://survicate.com</a> to create Survicate account, follow installation instruction and paste a code dedicated to Wordpress.
            </p>
            <a href="?page=survicate&amp;action=manual" class="page-title-action">Enter tracking code</a>
        </div>
        
    </div>
    <?php
}

/* SIGN UP / CREATE ACCOUNT */
function survicate_sign_up(){
    $current_user = wp_get_current_user();
    $user_name = '';
    if($current_user->user_firstname || $current_user->user_lastname){
        $user_name = $current_user->user_firstname.' '.$current_user->user_lastname;
    }
    ?>
    <div class="wrap">
        <img src="//survicate.com/wp-content/uploads/2014/11/survicate-whitebg_transp_cropped_50px-high.png" alt="Survicate" />
    <?php if(get_option('survicate-error') !== false): ?>
        <div class="card">
            <p style="color: red;"><?php echo get_option('survicate-error'); ?></p>
        </div>
    <?php delete_option('survicate-error'); endif; ?>

        <div class="card">
            <h1 class="title">Save your account</h1>
            <p>You start with a <strong>lifetime free</strong> account. You can order a business plan right after saving.</p>

            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <input type="hidden" name="action" value="survicate_signup">
                <table class="form-table">
                    <tr valign="top">
                    <th scope="row">Domain</th>
                    <td><input type="text" name="domain" value="<?php echo esc_attr( $_SERVER['SERVER_NAME'] ); ?>" autocomplete="off" /></td>
                    </tr>
                    <tr valign="top">
                    <th scope="row">Email</th>
                    <td><input type="text" name="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" autocomplete="off" /></td>
                    </tr>
                    <tr valign="top">
                    <th scope="row">Password</th>
                    <td><input type="password" name="password" value="" autocomplete="off"/></td>
                    </tr>
                </table>
                <?php submit_button('Sign up'); ?>
            </form>
        </div>
        <div class="card" style="text-align: center;">
          <h4>Already have an account?</h4>
          <a href="?page=survicate" class="page-title-action">Sign in</a>
        </div>
    </div>
    <?php
}

/* MANUAL TRACKING CODE */
function survicate_get_tracking_code(){
    ?>
    <div class="wrap">
        <img src="//survicate.com/wp-content/uploads/2014/11/survicate-whitebg_transp_cropped_50px-high.png" alt="Survicate" />
    <?php if(get_option('survicate-error') !== false): ?>
        <div class="card">
            <p style="color: red;"><?php echo get_option('survicate-error'); ?></p>
        </div>
    <?php delete_option('survicate-error'); endif; ?>
        <div class="card">
            <h1 class="title">Hello</h1>
            <?php if(!function_exists('curl_version')): ?>
            <p style="color: red;">This plugin requires PHP CURL extension.</p>
            <p>Please ask your hosting administrator to enable it or sign up at <a href="https://survicate.com" target="_blank">https://survicate.com</a> and follow instructions regarding Wordpress installation. It's easy, you'll be asked to pased a piece of code here.</p>
            <?php else: ?>
            <p>If for some reason you cannot login or create your account from here, please go to <a href="https://survicate.com" target="_blank">https://survicate.com</a> to create Survicate account, follow installation instruction and paste a code dedicated to Wordpress.
            </p>
            <?php endif; ?>
            <p>Please drop us a line at <a href="mailto:hello@survicate.com">hello@survicate.com</a> if you need any assistance.</p>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <input type="hidden" name="action" value="survicate_tracking_code">
                <table class="form-table">
                    <tr valign="top">
                    <th scope="row">Tracking code</th>
                    <td><input type="text" name="tracking_code" value="" autocomplete="off" /></td>
                    </tr>
                </table>
                <?php submit_button('Save'); ?>
            </form>
        </div>
    </div>
    <?php
}


/* DASHBOARD */
function survicate_dashboard(){
    ?>
    <div class="wrap">
        <img src="//survicate.com/wp-content/uploads/2014/11/survicate-whitebg_transp_cropped_50px-high.png" alt="Survicate" />
        <div class="card" style="text-align: center;">
            <h1 class="title">Survicate is enabled on your site.</h1>
            <!-- TRACKING CODE: <?php echo get_option('survicate-tracking-code'); ?> -->
        </div>
        <div class="card" style="text-align: center;">
            <h4 class="title">Identity for wordpress users is currently <?php echo ( get_option( 'survicate-push-identity' ) ? 'enabled' : 'disabled'); ?>.</h4>
            <?php if(get_option( 'survicate-push-identity' )): ?>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <input type="hidden" name="action" value="survicate_identity">
                <?php submit_button('Disable', 'delete', 'submit', false); ?>
            </form>
            <?php else: ?>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <input type="hidden" name="action" value="survicate_identity">
                <?php submit_button('Enable', 'primary', 'submit', false); ?>
            </form>
            <?php endif; ?>
        </div>
        <div class="card" style="text-align: center;">
          <h4>Want to disable survicate or switch your account?</h4>
          <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
              <input type="hidden" name="action" value="survicate_logout">
              <?php submit_button('Log out', 'delete', 'submit', false); ?>
          </form>
        </div>
    </div>
    <?php

}

/* SELECT DOMAIN */
function survicate_select_domain(){
    $serviceUrl = 'https://api.survicate.com/cms/domains?cms_key='.get_option('survicate-cms-key');
    $curl = curl_init($serviceUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);
    curl_close($curl);
    if($curl_response){
        $status = json_decode($curl_response);

    ?>
    <?php if(get_option('survicate-error') !== false): ?>
        <div class="card">
            <p style="color: red;"><?php echo get_option('survicate-error'); ?></p>
        </div>
    <?php delete_option('survicate-error'); endif; ?>

    <div class="wrap">
        <img src="//survicate.com/wp-content/uploads/2014/11/survicate-whitebg_transp_cropped_50px-high.png" alt="Survicate" />
        <div class="card">
            <h1 class="title">Please select your domain:</h1>

            <?php foreach ($status->domains as $domain) {
                ?>
                    <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                        <input type="hidden" name="action" value="survicate_select_domain">
                        <input type="hidden" name="id" value="<?php echo $domain->id; ?>">
                        <?php submit_button($domain->host); ?>
                    </form>
                <?php
            } ?>
        </div>
    </div>
    <?php
      die();
    }
    delete_option('survicate-cms-key');
    delete_option('survicate-tracking-code');
    echo "login error";
    die();


}


/*==================================== ACTIONS ===================================================*/

/* SIGN IN ACTION */
add_action( 'admin_post_survicate_signin', 'survicate_sign_in_action' );
function survicate_sign_in_action(){
    $serviceUrl = 'https://api.survicate.com/cms/login_user';
    $curl = curl_init($serviceUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    $data = array(
        'email'=>$_POST['email'],
        'password'=>$_POST['password'],
        'source' => 'wp'
    );
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);
    curl_close($curl);
    if($curl_response){
        $status = json_decode($curl_response);
        if(get_option('survicate-cms-key') !== false){
            update_option('survicate-cms-key', $status->user->cms_key);
        }else{
            add_option('survicate-cms-key',$status->user->cms_key,'','no');
        }
        wp_redirect(admin_url('/options-general.php?page=survicate'), 301);
        die();
    }
    add_option('survicate-error','Incorrect email or password','','no');
    wp_redirect(admin_url('/options-general.php?page=survicate'), 301);
    die();
  
}

/* SELECT DOMAIN ACTION */
add_action( 'admin_post_survicate_select_domain', 'survicate_select_domain_action' );
function survicate_select_domain_action(){
    $serviceUrl = 'https://api.survicate.com/cms/domains?cms_key='.get_option('survicate-cms-key');
    $curl = curl_init($serviceUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);
    curl_close($curl);
    if($curl_response){
        $status = json_decode($curl_response);

        foreach ($status->domains as $domain) {
            if($domain->id == $_POST['id']){
              if(get_option('survicate-tracking-code') !== false){
                  update_option('survicate-tracking-code', $domain->tracking_code);
              }else{
                  add_option('survicate-tracking-code',$domain->tracking_code,'','no');
              }
            }
        }

    }
    wp_redirect(admin_url('/options-general.php?page=survicate'), 301);
    die();
}


/* SIGN UP ACTION */
add_action( 'admin_post_survicate_signup', 'survicate_sign_up_action' );
function survicate_sign_up_action(){
    $serviceUrl = 'https://api.survicate.com/cms/register_user';
    $curl = curl_init($serviceUrl);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    $data = array(
        'email'=>$_POST['email'],
        'password'=>$_POST['password'],
        'domain' => $_POST['domain'],
        'source' => 'wp'
    );
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);
    curl_close($curl);

    if($curl_response){
        $status = json_decode($curl_response);
        if($status->status){
          if(get_option('survicate-cms-key') !== false){
              update_option('survicate-cms-key', $status->user->cms_key);
          }else{
              add_option('survicate-cms-key',$status->user->cms_key,'','no');
          }
          //autoselect domain

          $serviceUrl = 'https://api.survicate.com/cms/domains?cms_key='.get_option('survicate-cms-key');
          $curl = curl_init($serviceUrl);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          $curl_response = curl_exec($curl);
          curl_close($curl);
          if($curl_response){
              $status = json_decode($curl_response);
              foreach ($status->domains as $domain) {
                  if($domain->host == $_POST['domain']){
                    if(get_option('survicate-tracking-code') !== false){
                        update_option('survicate-tracking-code', $domain->tracking_code);
                    }else{
                        add_option('survicate-tracking-code',$domain->tracking_code,'','no');
                    }
                  }
              }
          }

          wp_redirect(admin_url('/options-general.php?page=survicate'), 301);
          die();

        }else{
            add_option('survicate-error',$status->message,'','no');
            wp_redirect(admin_url('/options-general.php?page=survicate&action=signup'), 301);
            die();
        }
    }
    add_option('survicate-error','unknown register error','','no');
    wp_redirect(admin_url('/options-general.php?page=survicate&action=signup'), 301);
    die();
}


/* LOG OUT ACTION */
add_action( 'admin_post_survicate_logout', 'survicate_log_out_action' );
function survicate_log_out_action(){
    delete_option('survicate-cms-key');
    delete_option('survicate-tracking-code');
    wp_redirect(admin_url('/options-general.php?page=survicate'), 301);
}

/* TRACKING CODE ACTION */
add_action( 'admin_post_survicate_tracking_code', 'survicate_tracking_code_action' );
function survicate_tracking_code_action(){
    if(get_option('survicate-tracking-code') !== false){
        update_option('survicate-tracking-code', $_POST['tracking_code']);
    }else{
        add_option('survicate-tracking-code',$_POST['tracking_code'],'','no');
    }
    wp_redirect(admin_url('/options-general.php?page=survicate'), 301);
}

/* IDENTITY ACTION */
add_action( 'admin_post_survicate_identity', 'survicate_identity_action' );
function survicate_identity_action(){
    if(get_option( 'survicate-push-identity' )){
        delete_option('survicate-push-identity');
    }else{
        add_option('survicate-push-identity','yes','','no');
    }
    wp_redirect(admin_url('/options-general.php?page=survicate'), 301);
}

/* FOOTER */
add_action('wp_footer','survicate_script');
function survicate_script() {
    $tracking_code = esc_attr( get_option( 'survicate-tracking-code' ) );
    $push_identity = get_option( 'survicate-push-identity' );

    if( strlen($tracking_code) != 32 )
        return;

    if($push_identity) {
        global $current_user;
        get_currentuserinfo();
        $identity = '';
        if($current_user->user_email)
            $identity = $current_user->user_email;
    }

    ?>
    <script type="text/javascript">
     (function(w) {
       w['_sv'] = {trackingCode: '<?php echo $tracking_code; ?>'<?php echo $push_identity ? ", identity: '$identity'" : '' ?>};
       var s = document.createElement('script');
       s.src = '//api.survicate.com/assets/survicate.js';
       s.async = true;
       var e = document.getElementsByTagName('script')[0];
       e.parentNode.insertBefore(s, e);
     })(window);
    </script><noscript><a href="http://survicate.com">Website Survey</a></noscript>
    <?php
}
