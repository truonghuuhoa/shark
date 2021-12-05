<?php

class Header_Menu_Walker extends Walker_Nav_Menu {
    private $menu_item_parent = false;

    private $menu__id = '';


    /**
    * Phương thức start_lvl()
    * Được sử dụng để hiển thị các thẻ bắt đầu cấu trúc của một cấp độ mới trong menu. (ví dụ: <ul class="sub-menu">)
    * @param string $output | Sử dụng để thêm nội dung vào những gì hiển thị ra bên ngoài
    * @param interger $depth | Cấp độ hiện tại của menu. Cấp độ 0 là lớn nhất.
    * @param array $args | Các tham số trong hàm wp_nav_menu()
    **/
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent";
        $output .= "<div class=\"dropdown-menu\" aria-labelledby=\"topnav_$this->menu__id\">\n";
    }

    /**
    * Phương thức end_lvl()
    * Được sử dụng để hiển thị đoạn kết thúc của một cấp độ mới trong menu. (ví dụ: </ul> )
    * @param string $output | Sử dụng để thêm nội dung vào những gì hiển thị ra bên ngoài
    * @param interger $depth | Cấp độ hiện tại của menu. Cấp độ 0 là lớn nhất.
    * @param array $args | Các tham số trong hàm wp_nav_menu()
    **/
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent";
        $output .= "</div>\n";
    }

    /**
    * Phương thức start_el()
    * Được sử dụng để hiển thị đoạn bắt đầu của một phần tử trong menu. (ví dụ: <li id="menu-item-5"> )
    * @param string $output | Sử dụng để thêm nội dung vào những gì hiển thị ra bên ngoài
    * @param string $item | Dữ liệu của các phần tử trong menu
    * @param interger $depth | Cấp độ hiện tại của menu. Cấp độ 0 là lớn nhất.
    * @param array $args | Các tham số trong hàm wp_nav_menu()
    * @param interger $id | ID của phần tử hiện tại
    **/
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $classes = array('nav-item');

        if ($this->has_children) {
            $classes[] =  'dropdown';
        }

        if (!empty($item->menu_item_parent)) {
            $this->menu_item_parent = true;
        }


        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $output .= $indent;
        if (empty($item->menu_item_parent)) {
            $output .= '<li' . $class_names .'>' . "\n";
        }

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';
        $atts['class']   = ! empty( $item->class )      ? $item->class     : 'nav-link';

        $icon   = ! empty( $item->icon )      ? $item->icon     : '';


        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = $args->before;

        $this->menu__id = $item->ID;

        if ($this->has_children) {
            $item_output .= '<a href="#" role="button" id="topnav_' . $this->menu__id . '" class="nav-link dropdown-toggle arrow-none" title="' . apply_filters( 'the_title', $item->title, $item->ID ) . '">';
        } else {
            $item_output .= '<a'. $attributes .' title="' . apply_filters( 'the_title', $item->title, $item->ID ) . '">';
        }

        // if (isset($icon)) {
        //     $item_output .= "\n<i class=\"fa fa-car me-2\">" . $icon . "</i>\n";
        // }

        /** This filter is documented in wp-includes/post-template.php */
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

        if ($this->has_children) {
            $item_output .= '<div class="arrow-down"></div>';
        }

        $item_output .= "\n</a>\n";

        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
    * Phương thức end_el()
    * Được sử dụng để hiển thị đoạn kết thúc của một phần tử trong menu. (ví dụ: </li> )
    * @param string $output | Sử dụng để thêm nội dung vào những gì hiển thị ra bên ngoài
    * @param string $item | Dữ liệu của các phần tử trong menu
    * @param interger $depth | Cấp độ hiện tại của menu. Cấp độ 0 là lớn nhất.
    * @param array $args | Các tham số trong hàm wp_nav_menu()
    * @param interger $id | ID của phần tử hiện tại
    **/
    public function end_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        if (empty($item->menu_item_parent)) {
            $output .= "</li>\n";
        }
    }
}
