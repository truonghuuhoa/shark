<?php
/* Template Name:  Đăng ký
*/
?>
<?php get_header(); ?>
<div class="main-content" style="background: url('<?php echo get_template_directory_uri(); ?>/assets/images/build.png') bottom no-repeat;">
  <div class="account-pages my-5 pt-sm-5">
      <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
              <div class="card overflow-hidden">
                <div class="bg-primary bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-4 mt-4">
                                <h5 class="text-primary">Register</h5>
                                <p>Bạn đã có tài khoản, <a href="<?php bloginfo('url'); ?>/login">đăng nhập tại đây.</a></p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/profile-img.png" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                      <div>
                        <a href="<?php bloginfo('url'); ?>">
                            <div class="avatar-md profile-user-wid mb-4">
                                <span class="avatar-title rounded-circle bg-light">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo_cam.png" alt="" class="rounded-circle" height="50">
                                </span>
                            </div>
                        </a>
                      </div>

                  <div class="p-2">
                  <?php if(is_user_logged_in()) {
                      $user_id = get_current_user_id();
                      $current_user = wp_get_current_user();
                      $profile_url = get_author_posts_url($user_id);
                      $edit_profile_url = get_edit_profile_url($user_id);
                  ?>
                  <div class="da-dang-nhap">
                    Bạn đã đăng nhập với tài khoản <a href="<?php echo $profile_url ?>"><?php echo $current_user->display_name; ?></a> Hãy truy cập <a href="/wp-admin">Quản trị viên</a> hoặc <a href="<?php echo esc_url(wp_logout_url($current_url)); ?>">Đăng xuất tài khoản</a>
                  </div>
                  <?php } else { ?>

                  <?php
                  $err = '';
                  $success = '';

                  global $wpdb, $PasswordHash, $current_user, $user_ID;

                  if(isset($_POST['task']) && $_POST['task'] == 'register' ) {


                  $pwd1          = $wpdb->escape(trim($_POST['pwd1']));
                  $pwd2          = $wpdb->escape(trim($_POST['pwd2']));
                  $first_name    = $wpdb->escape(trim($_POST['first_name']));
                  $last_name     = $wpdb->escape(trim($_POST['last_name']));
                  $email         = $wpdb->escape(trim($_POST['email']));
                  $username      = $wpdb->escape(trim($_POST['username']));

                  if( $email == "" || $pwd1 == "" || $pwd2 == "" || $username == "" || $first_name == "" || $last_name == "") {
                  $err = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Vui lòng không bỏ trống các thông tin!</div>';
                  } else if (! filter_var( $email, FILTER_VALIDATE_EMAIL)) {
                    $err = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Địa chỉ email không hợp lệ!</div>';
                  } else if ( email_exists($email) ) {
                    $err = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Email đã tồn tại!</div>';
                  } else {

                  $user_id = wp_insert_user( array (
                  'first_name'  => apply_filters('pre_user_first_name', $first_name),
                  'last_name'   => apply_filters('pre_user_last_name', $last_name),
                  'user_pass'   => apply_filters('pre_user_user_pass', $pwd1),
                  'user_login'  => apply_filters('pre_user_user_login', $username),
                  'user_email'  => apply_filters('pre_user_user_email', $email),
                  'role' => 'subscriber' ) );
                  if( is_wp_error($user_id) ) {
                      $err = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Lỗi đăng ký tài khoản</div>';
                  } else {
                      do_action('user_register', $user_id);
                      $success = '<div class="alert alert-success" role="alert">
                                                Bạn đã đăng ký thành công! Hãy đăng nhập để trải nghiệm mua sắm nhé <i class="mdi mdi-heart text-danger"></i>
                                            </div>';
                  }

                  }

                  }
                  ?>
                  <!--display error/success message-->
                  <div id="message">
                      <?php
                      if(! empty($err) ) :
                      echo '<p class="error">'.$err.'';
                      endif;
                      ?>

                      <?php
                      if(! empty($success) ) :
                      echo '<p class="error">'.$success.'';
                      endif;
                      ?>
                  </div>
                  <form class="needs-validation" method="post">
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input class="form-control" type="text" value="" name="last_name" id="last_name"  placeholder="Enter last Name" required/>
                    </div>
                    <div class="mb-3">
                        <label>First Name</label>
                        <input class="form-control" type="text" value="" name="first_name" id="first_name" placeholder="Enter first Name" required/>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input class="form-control" type="text" value="" name="email" id="email" placeholder="Enter email" required/>
                    </div>
                    <div class="mb-3">
                        <label>UserName</label>
                        <input class="form-control" type="text" value="" name="username" id="username" placeholder="Enter username" required/>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input class="form-control" type="password" value="" name="pwd1" id="pwd1" placeholder="Enter password" required/>
                    </div>
                    <div class="mb-3">
                        <label>Enter the password</label>
                        <input class="form-control" type="password" value="" name="pwd2" id="pwd2" placeholder="Enter the password" required/>
                    </div>
                  <!-- <div class="message"><p>
                      <?php if ( $sucess != "") {
                        echo $sucess; } ?>
                      <?php if($err != "") {
                        echo $err; } ?>
                  </p></div> -->
                  <div class="mt-4 d-grid">
                      <button type="submit" name="btnregister" id="nut-dk" class="btn btn-primary waves-effect waves-light">Register</button>
                    </div>
                  <input type="hidden" name="task" value="register" />
                  <label class="mt-4">Bạn đã có tài khoản, <a href="<?php bloginfo('url'); ?>/login">đăng nhập tại đây.</a></label>
                  </form>
                    <?php } ?>
                  </div>
                  <!-- <button type="button" class="btn btn-primary waves-effect waves-light" id="sa-success">Click me</button> -->

                </div>

            </div>
          </div>
        </div>
        </div> <!-- container-fluid -->
    </div>
</div>
<?php get_footer(); ?>
