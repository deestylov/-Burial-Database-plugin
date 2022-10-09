<?php

class KW_ImportAdmin  extends Kw_Ritual {
    private $parent_slug = 'kw_ritual_admin';
	protected $slug = 'kw_import_admin';
    
    function __construct() {
	    add_action( 'admin_menu', [$this, 'admin_menu'] );
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
        add_action( 'wp_ajax_kwimport', [$this, 'handler_import'] );  
        add_action( 'wp_ajax_kwtruncate', [$this, 'handler_truncate'] );  
        add_action( 'wp_ajax_kwcemeteries', [$this, 'handler_cemeteries'] );  
    }
    
    function admin_menu() {
		$page_title = 'Импорт csv файла';
		$menu_title = 'Импорт csv файла';
		$capability = 'manage_options';
		$callback = [$this, 'callback_option_page'];

		add_submenu_page(
            $this->parent_slug,
            $page_title,
            $menu_title,
            $capability,
            $this->slug,
            $callback
        );
	}
    
    function enqueue_scripts($hook_suffix) {
                
        if (mb_strpos($hook_suffix, $this->slug) === false) {
            return;
        }
        
        wp_enqueue_style('kw_sweetalert', 'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css', null, null);
        wp_enqueue_style(
            'kw_style',
            plugins_url('css/style.css', __FILE__),
            null,
            0.01
        );
        
        wp_enqueue_script('kw_sweetalert', 'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js', null, null, true);
        wp_enqueue_script('kw_script', plugins_url('js/script.js', __FILE__), ['jquery', 'kw_sweetalert'], 0.04, true);
        
        wp_localize_script('kw_script', 'kw_script_data', array(
            'url' => esc_url(admin_url('admin-ajax.php')),
            'action' => 'kwimport',
            'action_truncate' => 'kwtruncate',
            'action_cemeteries' => 'kwcemeteries'
        ));
    }
    
    function callback_option_page() {
        global $wpdb;
        
        $data = [
            'slug' => $this->slug,
            'messages' => $this->get_messages()
        ];
        $this->render('import.php', $data);
    }
    
    function handler_import() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_POST['sliced_rows'])) {
            wp_die(json_encode(['status' => 'error', 'message' => 'Ошибка импорт csv файла']));
        }
        
        global $wpdb;
                        
        $rows = json_decode(base64_decode($_POST['sliced_rows']), true);

        

        $values = [];
        
        foreach ($rows as $row) {
            $row = str_getcsv($row, ',', '"');
            $count_columns = count($row);
            
            if ($count_columns == 13) {
                $values[] = $wpdb->prepare(
                    "(%d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    $row
                );
            }
        }
        
        $values = implode( ",\n", $values );

        // wp_die(json_encode($values));
        
        $query = "INSERT INTO {$wpdb->prefix}ritual (`id`, `registration_number`, `surname`, `name`, `patronymic`, `date_birth`, `date_death`, `date_dburial`, `cemetery_name`, `site`, `row`, `grave`, `comment`) VALUES {$values}";
        
        $wpdb->query($query);
        // wp_die(json_encode( $query));
        // wp_die(json_encode(['status' => 'success']));
    }
    
    function handler_truncate() {
        
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_POST['truncate'])) {
            wp_die(json_encode(['status' => 'error', 'message' => 'Ошибка импорт csv файла']));
        }
        
        global $wpdb;

        $wpdb->query("TRUNCATE {$wpdb->prefix}ritual");
        
        wp_die(json_encode(['status' => 'success']));
    }
    
    function handler_cemeteries() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_POST['cemeteries'])) {
            wp_die(json_encode(['status' => 'error', 'message' => 'Ошибка импорт csv файла']));
        }
        
        global $wpdb;
        
        $cemeteries = $wpdb->get_col("SELECT DISTINCT cemetery_name FROM `{$wpdb->prefix}ritual` WHERE cemetery_name NOT IN (SELECT DISTINCT name FROM `{$wpdb->prefix}cemeteries`)");
                        
        $values = [];
        
        foreach ($cemeteries as $cemetery) {
            $values[] = $wpdb->prepare(
                "(%s, %s)",
                $cemetery, ''
            );
        }
        
        $values = implode( ",\n", $values );
        
        if (!empty($values)) {
        
            $query = "INSERT INTO {$wpdb->prefix}cemeteries (name, iframe) VALUES {$values}";
            
            $wpdb->query($query);
        }
        
        wp_die(json_encode(['status' => 'success']));
        
    }
    
}
