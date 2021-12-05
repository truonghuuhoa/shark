<?php
/* Template Name:  Page Login
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
                                <h5 class="text-primary">Login</h5>
                                <p>Nếu chưa có tài khoản, <a href="<?php bloginfo('url'); ?>/dang-ky">đăng ký tại đây</a>.</p>
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
                  </div>
                <div class="p-2">
                  <div class="login-area">
                    <div class="thong-bao">
                    <?php
                  			$login  = (isset($_GET['login']) ) ? $_GET['login'] : 0;
                  			if ( $login === "failed" ) {
                  				echo '<p class="text-danger"><strong>ERROR:</strong>Sai tên tài khoản hoặc mật khẩu.</p>';
                  			} elseif ( $login === "empty" ) {
                  				echo '<p class="text-danger"><strong>ERROR:</strong>Tên tài khoản và mật khẩu không thể bỏ trống.</p>';
                  			} elseif ( $login === "false" ) {
                  				echo '<p class="text-danger"><strong>ERROR:</strong>Bạn đã thoát ra.</p>';
                  			}
                  		?>

                    </div>

                  	<div class="form">
                  		<?php
                  			$args = array(
                  				'redirect'       => site_url( $_SERVER['REQUEST_URI'] ),
                  				'form_id'        => 'dangnhap', //Để dành viết CSS
                  				'label_username' => __( 'Tên tài khoản' ),
                  				'label_password' => __( 'Mật khẩu' ),
                  				'label_remember' => __( 'Ghi nhớ' ),
                  				'label_log_in'   => __( 'Đăng nhập' ),
                  			);
                  			wp_login_form($args);
                  		?>
                      <style>
                                  .thong-bao {
                                    margin-left: 6%;
                                    }
                                  form{
                                    width: 88%;
                                    max-width: 1400px;
                                    margin-left: auto;
                                    margin-right: auto;
                                  }
                                  @media (max-width: 600px) {
                                    .form { width: 100% ; }
                                  }
                                  #user_login {
                                    width: 100%;
                                    padding: 8px 19px;
                                    font-size: .8125rem;
                                    font-weight: 400;
                                    line-height: 1.5;
                                    color: #495057;
                                    background-color: #fff;
                                    background-clip: padding-box;
                                    border: 1px solid #ced4da;
                                    border-radius: .25rem;
                                  }
                                  #user_pass {
                                    width: 100%;
                                    padding: 8px 19px;
                                    font-size: .8125rem;
                                    font-weight: 400;
                                    line-height: 1.5;
                                    color: #495057;
                                    background-color: #fff;
                                    background-clip: padding-box;
                                    border: 1px solid #ced4da;
                                    border-radius: .25rem;
                                  }
                                  input#wp-submit {
                                    width: 100%;
                                    font-size: 18px;
                                    color: #fff;
                                    background: #556ee6;
                                    border-color: #556ee6;
                                    border-radius: .25rem;
                                }
                                input#rememberme {
                                      margin-right: 7px;
                                  }
                            </style>                          
                  	</div>
                    <p class="woocommerce-LostPassword lost_password">
              				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
              			</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php get_footer(); ?>
