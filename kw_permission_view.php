<?php

class KW_PermissionView {
    function __construct() {
        add_action( 'init', [$this, 'init'] );
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
        add_filter( 'the_content', [$this, 'the_content'] );
    }
    
    function init() {
        add_rewrite_rule('^(permission-view)/(\d+)/?', 'index.php?pagename=$matches[1]&ritual_id=$matches[2]', 'top');
        add_filter( 'query_vars', [$this, 'query_vars'] );
    }
    
    function enqueue_scripts() {
        global $post;
        
        if (is_404()) {
            return;
        }
     
        if( is_page('permission-view') ) {
            wp_enqueue_style('kw_permission_view', plugins_url('css/permission_view.css', __FILE__), null, 0.01);
            wp_enqueue_script('kw_permission_view', plugins_url('js/permission_view.js', __FILE__), ['jquery'], 0.01, true);   
        }
    }
    
    function query_vars($vars) {
        $vars[] = 'ritual_id';
        return $vars;
    }
    
    function the_content($content) {
        global $wpdb;
        
        $row = [];
        
        $pagename = get_query_var('pagename');
        
        if ($pagename != 'permission-view') {
            return $content;
        }
              
        $ritual_id = get_query_var('ritual_id');
        
        $query = $wpdb->prepare(
            "SELECT wp_ritual.id, wp_ritual.registration_number, wp_ritual.name, wp_ritual.surname, wp_ritual.patronymic, wp_ritual.date_birth, wp_ritual.date_death, wp_ritual.date_dburial, wp_ritual.cemetery_name, wp_ritual.site, wp_ritual.row, wp_ritual.grave, wp_ritual.comment, wp_ritual.map, wp_cemeteries.iframe
			FROM {$wpdb->prefix}ritual LEFT JOIN {$wpdb->prefix}cemeteries 
            ON  {$wpdb->prefix}ritual.cemetery_name = {$wpdb->prefix}cemeteries.name WHERE  wp_ritual.id=%d LIMIT 1",
            $ritual_id
        );


        
        $row = $wpdb->get_row($query, ARRAY_A);
        
        if (!empty($row)) {
            $date_birth = date_create_from_format("d.m.Y", $row['date_birth']);
            $date_death = date_create_from_format("d.m.Y", $row['date_death']);
            $diff=date_diff($date_birth, $date_death);
            $row['age'] = $diff->format("%Y");
            
            if ($this->is_json($row['iframe'])) {
                $coords = json_decode($row['iframe'], true);
                $row['map_cemetery'] = 'https://static-maps.yandex.ru/1.x/';
                $row['map_cemetery'] .= '?ll=' . $coords[1] . ',' . $coords[0];
                $row['map_cemetery'] .= '&z=18&l=map';
                $row['map_cemetery'] .= '&pt=' . $coords[1] . ',' . $coords[0];
                $row['map_cemetery'] .= '&size=650,450';
            }
        }
        
        $data = [
            'row' => $row
        ];
        
        $html_code = $this->render('permission-view.php', $data);
                
        return $html_code . $content;
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
    
    function is_json($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
}
