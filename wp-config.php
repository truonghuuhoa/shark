<?php
/**
 * Cấu hình cơ bản cho WordPress
 *
 * Trong quá trình cài đặt, file "wp-config.php" sẽ được tạo dựa trên nội dung 
 * mẫu của file này. Bạn không bắt buộc phải sử dụng giao diện web để cài đặt, 
 * chỉ cần lưu file này lại với tên "wp-config.php" và điền các thông tin cần thiết.
 *
 * File này chứa các thiết lập sau:
 *
 * * Thiết lập MySQL
 * * Các khóa bí mật
 * * Tiền tố cho các bảng database
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Thiết lập MySQL - Bạn có thể lấy các thông tin này từ host/server ** //
/** Tên database MySQL */
define( 'DB_NAME', 'hoa' );

/** Username của database */
define( 'DB_USER', 'hoa' );

/** Mật khẩu của database */
define( 'DB_PASSWORD', 'bincute12' );

/** Hostname của database */
define( 'DB_HOST', 'localhost' );

/** Database charset sử dụng để tạo bảng database. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Kiểu database collate. Đừng thay đổi nếu không hiểu rõ. */
define('DB_COLLATE', '');

/**#@+
 * Khóa xác thực và salt.
 *
 * Thay đổi các giá trị dưới đây thành các khóa không trùng nhau!
 * Bạn có thể tạo ra các khóa này bằng công cụ
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Bạn có thể thay đổi chúng bất cứ lúc nào để vô hiệu hóa tất cả
 * các cookie hiện có. Điều này sẽ buộc tất cả người dùng phải đăng nhập lại.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'RP&yfU,4opF]siU=t@O#2X20LbNWtzviM3hd::NeZ*D$ *IA7+&S`Q<LNrp F#D1' );
define( 'SECURE_AUTH_KEY',  'HnHq[:bl[nUn.,F3CHP*3(^sG%4(A`|zn*>C2_:-M+N&V+*~;+L[:?^PxPC_5c]{' );
define( 'LOGGED_IN_KEY',    'Z,4JYi% ^L[QC;0{&jcc6KMJM<KT-K._caPWPZ*skH<4f:di&AV&5{^Kg$UDM.KD' );
define( 'NONCE_KEY',        '+B`_&kbq*okm@leB}l!BUMd>ZnzEuhj-H.3Si;9;1Qs+92}%[2LtDl4Scx[?FGT%' );
define( 'AUTH_SALT',        '{?Gf3:U2SI}dLH,+GDG`<Y<oCfI=yU$+r!#:wQ6q=`|sa-HzTn;6jO/iOt&D/S(,' );
define( 'SECURE_AUTH_SALT', 'W~}pgP!W1d,y7L#&7l7=lS884+o>:[v~oI0f/ECW0.7?;/DV1bjOYuK[zIgT[<mM' );
define( 'LOGGED_IN_SALT',   'XekTZa.sH`C)_?`D>*UQz`P{^WeI;7hDB.$>&6uR=xbH(~x[]z& M6Vix$h;6BOo' );
define( 'NONCE_SALT',       'eK};b+t *(F~+z0 PZWCJ-;]#[ H *uR6)E+dx:y)cTP4-8hF#zx^/4oa}_?s?`4' );

/**#@-*/

/**
 * Tiền tố cho bảng database.
 *
 * Đặt tiền tố cho bảng giúp bạn có thể cài nhiều site WordPress vào cùng một database.
 * Chỉ sử dụng số, ký tự và dấu gạch dưới!
 */
$table_prefix = 'wp_ac';

/**
 * Dành cho developer: Chế độ debug.
 *
 * Thay đổi hằng số này thành true sẽ làm hiện lên các thông báo trong quá trình phát triển.
 * Chúng tôi khuyến cáo các developer sử dụng WP_DEBUG trong quá trình phát triển plugin và theme.
 *
 * Để có thông tin về các hằng số khác có thể sử dụng khi debug, hãy xem tại Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Đó là tất cả thiết lập, ngưng sửa từ phần này trở xuống. Chúc bạn viết blog vui vẻ. */

/** Đường dẫn tuyệt đối đến thư mục cài đặt WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Thiết lập biến và include file. */
require_once(ABSPATH . 'wp-settings.php');
