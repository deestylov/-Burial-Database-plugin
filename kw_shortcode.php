<?php

// Класс отображения поиска во Фронтэнде - - - 

class KW_Shortcode  extends Kw_Ritual {
    function __construct() {
       add_action('init', [$this, 'init']);
       add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
       add_action( 'wp_ajax_nopriv_kw_search', [$this, 'handler_search_nopriv'] );  
       add_action( 'wp_ajax_kw_search', [$this, 'handler_search'] );  

    }
    
    function init() {
         add_shortcode( 'kw_ritual', [$this, 'generate_html_code'] );
         add_shortcode( 'kw_ritual-results', [$this, 'generate_html_resuts'] );
    }
    
    function enqueue_scripts() {
        global $post;
  
        if (is_404()) {
            return;
        }
             
        if( ( is_single() || is_page() || is_home() || is_frontpage() ) ) {
            wp_enqueue_style('kw_ritual', plugins_url('css/ritual.css', __FILE__), null, 0.01);
            wp_enqueue_script('kw_vue', plugins_url('js/vue.min.js', __FILE__), null, 0.01, true);  
            wp_enqueue_script('kw_ritual', plugins_url('js/ritual.js', __FILE__), ['jquery', 'kw_vue'], 0.02, true);  
            wp_localize_script('kw_ritual', 'kw_script_data', array(
                'url' => esc_url(admin_url('admin-ajax.php')),
                'action' => 'kw_search',
                'baseUrl' => get_home_url()
            ));  
        }
    }
    
    function generate_html_code() {
        global $wpdb;
        
        $list_cemeteries = $wpdb->get_col("SELECT name FROM {$wpdb->prefix}cemeteries");
        
        $data = [
            'list_cemeteries' => $list_cemeteries
        ];
        
        return $this->render('ritual.php', $data);
    }

    function generate_html_resuts() {
        global $wpdb;
        
        $list_cemeteries = $wpdb->get_col("SELECT name FROM {$wpdb->prefix}cemeteries");
        
        $data = [
            'list_cemeteries' => $list_cemeteries
        ];
        
        return $this->render('ritual-results.php', $data);
    }
    
    function handler_search() {
        if (!(isset($_POST['surname']) && isset($_POST['name']) && isset($_POST['patronymic']) && isset($_POST['cemetery_name']))) {
            wp_die(json_encode(['status' => 'error', 'message' => 'Ошибка запроса']));
        }
        
        global $wpdb;
        
        $_POST = array_map('trim', $_POST);
            
        $columns = ['surname', 'name', 'patronymic', 'cemetery_name'];
        $cond = [];
        $values = [];
        
        foreach ($_POST as $key => $value) {
            if (in_array($key, $columns)) {
                if (empty($value)) continue;
                
                if ($key == 'surname') {
                    $is_numeric = preg_match('/^\d+$/', $value);
                    if ($is_numeric) {
                        $cond[] = 'registration_number=%d';
                    } else {
                        $cond[] = 'wp_ritual.' . $key . '=%s';
                    }
                    $values[] = $value;
                } else {
                    $cond[] = 'wp_ritual.' . $key . '=%s';
                    $values[] = $value;
                }
            }
        }
        
        $cond_str = implode(' AND ', $cond);
        
        if (empty($_POST['surname']) && empty($cond_str)) {
            $action = http_build_query(['page' => $this->slug, 'action' => 'index']);
            $url = $_SERVER['PHP_SELF'] . '?' . $action;
            $data = [
                'url' => $url, 
                'slug' => $this->slug,
            ];

            wp_die(json_encode(['status' => 'error', 'message' => 'Ошибка запроса']));
        }
        
        $query_count_rows = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}ritual 
            WHERE {$cond_str}",
            $values
        
        );
  

        $count_rows = $wpdb->get_var($query_count_rows);
        $page_number = isset($_POST['page_number']) ? (int)$_POST['page_number'] : 1;
        $page_number = ($page_number) == 0 ? 1 : $page_number;
        $limit = 50;
        $offset = ($page_number - 1) * $limit;
                
        $query_rows = $wpdb->prepare(
            "SELECT wp_ritual.id, wp_ritual.registration_number, wp_ritual.name, wp_ritual.surname, wp_ritual.patronymic, wp_ritual.date_birth, wp_ritual.date_death, wp_ritual.date_dburial, wp_ritual.cemetery_name, wp_ritual.site, wp_ritual.row, wp_ritual.grave, wp_ritual.comment, wp_ritual.map
             FROM {$wpdb->prefix}ritual
            LEFT JOIN {$wpdb->prefix}cemeteries 
            ON  {$wpdb->prefix}ritual.cemetery_name = {$wpdb->prefix}cemeteries.name
            WHERE {$cond_str} LIMIT {$limit} OFFSET {$offset}",
            $values
        );
        $rows = $wpdb->get_results($query_rows, ARRAY_A);

        $data = [
            'rows' => $rows,
            'count_rows' => $count_rows,
            'limit' => $limit,
        ];


      wp_die(json_encode($data));

 

    
    }


    function handler_search_nopriv() {
        $this->handler_search();
    }


    
    function render($template, $data = null) {
        if (is_array($data)) {
            extract($data);
        }
        
        $filepath = dirname(__FILE__) . '/templates/' . $template;
        ob_start();
        include($filepath);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}


