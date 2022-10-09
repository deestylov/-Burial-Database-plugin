<?php

class KW_RitualAdmin extends Kw_Ritual {
    protected $slug = 'kw_ritual_admin';
    
    function __construct() {
        add_action( 'admin_menu', [$this, 'admin_menu'] );
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
        add_action( 'admin_init', [$this, 'session_init'] );
    }
        
    function admin_menu() {
        $page_title = 'Настройка Ритуал';
        $menu_title = 'Ритуал';
        $capability = 'manage_options';
        $callback = [$this, 'callback_option_page'];
        $icon = 'dashicons-clock';

        add_menu_page($page_title, $menu_title, $capability, $this->slug, $callback, $icon);
    }
    
    function enqueue_scripts($hook_suffix) {
        if ($hook_suffix != 'toplevel_page_kw_ritual_admin') return;
        
        wp_enqueue_style('kw_sweetalert', 'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css', null, null);
        wp_enqueue_style('kw_jquery_ui', plugins_url('css/jquery-ui.min.css', __FILE__), null, 0.01);
        wp_enqueue_style('kw_jquery_ui_theme', plugins_url('css/jquery-ui.theme.min.css', __FILE__), ['kw_jquery_ui'], 0.01);
        wp_enqueue_style('kw_style', plugins_url('css/style.css', __FILE__), null, 0.01);
        
        wp_enqueue_script('kw_sweetalert', 'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js', null, null, true);
        wp_enqueue_script('kw_jquery_ui', plugins_url('js/jquery-ui.min.js', __FILE__), ['jquery'], 0.01, true);
        wp_enqueue_script('kw_datepicker_ru', plugins_url('js/datepicker-ru.js', __FILE__), ['jquery', 'kw_jquery_ui'], 0.01, true);
        wp_enqueue_script('kw_script', plugins_url('js/script.js', __FILE__), ['jquery', 'kw_jquery_ui', 'kw_datepicker_ru', 'kw_sweetalert'], 0.01, true);
    }
    
    /*
     * Список всех записей 
     */
    function page_index() {
        global $wpdb;
        
        $list_cemeteries = $wpdb->get_col("SELECT name FROM {$wpdb->prefix}cemeteries");
                
        $count_rows = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ritual");
        $page_number = isset($_GET['page_number']) ? (int)$_GET['page_number'] : 1;
        $page_number = ($page_number) == 0 ? 1 : $page_number;
        $limit = 50;
        $index = 'page_number=';
        $pagination = new Pagination($count_rows, $page_number, $limit, $index);
        $offset = ($page_number - 1) * $limit;
        
        $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ritual LIMIT {$limit} OFFSET {$offset}", ARRAY_A);
                
        $data = [
            'messages' => $this->get_messages(),
            'slug' => $this->slug,
            'list_cemeteries' => $list_cemeteries,
            'count_rows' => $count_rows,
            'rows' => $rows,
            'pagination' => $pagination
        ];
        
        $this->render('rows.php', $data);
    }
    
    /*
     * Поиск
     */
    function page_search() {
        global $wpdb;
        
        $list_cemeteries = $wpdb->get_col("SELECT name FROM {$wpdb->prefix}cemeteries");
        
        $_GET = array_map('trim', $_GET);
        
        $columns = ['surname', 'name', 'patronymic', 'cemetery_name'];
        $cond = [];
        $values = [];
        
        foreach ($_GET as $key => $value) {
            if (in_array($key, $columns)) {
                if (empty($value)) continue;
                
                if ($key == 'surname') {
                    $is_numeric = preg_match('/^\d+$/', $value);
                    if ($is_numeric) {
                        $cond[] = 'registration_number=%d';
                    } else {
                        $cond[] = $key . '=%s';
                    }
                    $values[] = $value;
                } else {
                    $cond[] = $key . '=%s';
                    $values[] = $value;
                }
            }
        }
        
        $cond_str = implode(' AND ', $cond);
        
        if (empty($_GET['surname']) && empty($cond_str)) {
            $action = http_build_query(['page' => $this->slug, 'action' => 'index']);
            $url = $_SERVER['PHP_SELF'] . '?' . $action;
            $data = [
                'url' => $url, 
                'slug' => $this->slug,
            ];

            $this->render('redirect.php', $data);
            exit();
        }
        
        $query_count_rows = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}ritual 
            WHERE {$cond_str}",
            $values
        
        );
        $count_rows = $wpdb->get_var($query_count_rows);
        
        $page_number = isset($_GET['page_number']) ? (int)$_GET['page_number'] : 1;
        $page_number = ($page_number) == 0 ? 1 : $page_number;
        $limit = 50;
        $index = 'page_number=';
        $pagination = new Pagination($count_rows, $page_number, $limit, $index);
        $offset = ($page_number - 1) * $limit;
        
        $query_rows = $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ritual WHERE {$cond_str} LIMIT {$limit} OFFSET {$offset}",
            $values
        );
        $rows = $wpdb->get_results($query_rows, ARRAY_A);
        
        $data = [
            'count_rows' => $count_rows,
            'rows' => $rows,
            'list_cemeteries' => $list_cemeteries,
            'pagination' => $pagination,
            'surname' => $this->get_var('surname'),
            'name' => $this->get_var('name'),
            'patronymic' => $this->get_var('patronymic'),
            'cemetery_name' => $this->get_var('cemetery_name'),
            'slug' => $this->slug
        ];
                
        $this->render('search.php', $data);
    }
    
    /*
     * Добавление записи
     */
    function page_add() {
        global $wpdb;
        $has_errors = false;
                
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = array_map('trim', $_POST);
        
            $form = [
                'registration_number' => 'Регистрационный номер',
                'surname' => 'Фамилия/№',
                'name' => 'Имя',
                'patronymic' => 'Отчество',
                'date_birth' => 'Дата рождения',
                'date_death' => 'Дата смерти',
                'date_dburial' => 'Дата захоронения',
                'cemetery_name' => 'Кладбище',
                'site' => 'Участок',
                'grave' => 'Могила',
                'comment' => 'Комментарий',
                'map' => 'Код карты',

            ];
            
            foreach ($form as $key => $value) {
                if (empty($_POST[$key])) {
                    if (empty($_POST['grave']) || empty($_POST['comment']) || empty($_POST['map'])) {
                        $has_errors = false;
                        continue;
                    }
                    $has_errors = true;
                    add_settings_error($this->slug, 'notice', "Заполните поле \"{$value}\"", 'error');
                }
            }
            
            if (!preg_match('/^\d+$/', $_POST['registration_number'])) {
                $has_errors = true;
                add_settings_error($this->slug, 'notice', "В поле \"" . $form['registration_number'] . "\" должны быть тольцо цифры", 'error');
            }
            
            $date_birth = $this->parse_date($_POST['date_birth']);
            

            if (!is_array($date_birth) || !checkdate($date_birth['month'], $date_birth['day'], $date_birth['year'])) {
                $has_errors = true;
                add_settings_error($this->slug, 'notice', "Укажите правильно дату в поле \"" . $form['date_birth'] . "\"", 'error');
            }
            
            $date_death = $this->parse_date($_POST['date_death']);
            
            if (!is_array($date_death) || !checkdate($date_death['month'], $date_death['day'], $date_death['year'])) {
                $has_errors = true;
                add_settings_error($this->slug, 'notice', "Укажите правильно дату в поле \"" . $form['date_death'] . "\"", 'error');
            }
            
            $date_dburial = $this->parse_date($_POST['date_dburial']);
            
            if (!is_array($date_dburial) || !checkdate($date_dburial['month'], $date_dburial['day'], $date_dburial['year'])) {
                $has_errors = true;
                add_settings_error($this->slug, 'notice', "Укажите правильно дату в поле \"" . $form['date_dburial'] . "\"", 'error');
            }
            
            if (!$has_errors) {                
                $query = $wpdb->prepare(
                    "INSERT INTO {$wpdb->prefix}ritual (`registration_number`, `surname`, `name`, `patronymic`, `date_birth`, `date_death`, `date_dburial`, `cemetery_name`, `site`, `row`, `grave`, `comment`, `map`) VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                    $this->post_var('registration_number'),
                    $this->post_var('surname'),
                    $this->post_var('name'),
                    $this->post_var('patronymic'),
                    $this->post_var('date_birth'),
                    $this->post_var('date_death'),
                    $this->post_var('date_dburial'),
                    $this->post_var('cemetery_name'),
                    $this->post_var('site'),
                    $this->post_var('row'),
                    $this->post_var('grave'),
                    $this->post_var('comment'),
                    $this->post_var('map')
                    
                );
                
                $wpdb->query($query);
                
                $this->add_message('success', "Запись успешно добавлена!");
                
                $action = http_build_query(['page' => $this->slug, 'action' => 'index']);
                $url = $_SERVER['PHP_SELF'] . '?' . $action;
                $data = [
                    'url' => $url, 
                    'slug' => $this->slug,
                ];

                $this->render('redirect.php', $data);
                exit();
            }
        }
        
        $action = http_build_query(['page' => $this->slug, 'action' => 'add']);
        $list_cemeteries = $wpdb->get_col("SELECT name FROM {$wpdb->prefix}cemeteries");
                
        $data = [
            'action' => $action,
            'slug' => $this->slug,
            'title' => 'Добавить запись',
            'list_cemeteries' => $list_cemeteries,
            'registration_number' => $this->post_var('registration_number'),
            'surname' => $this->post_var('surname'),
            'name' => $this->post_var('name'),
            'patronymic' => $this->post_var('patronymic'),
            'date_birth' => $this->post_var('date_birth'),
            'date_death' => $this->post_var('date_death'),
            'date_dburial' => $this->post_var('date_dburial'),
            'cemetery_name' => $this->post_var('cemetery_name'),
            'site' => $this->post_var('site'),
            'row' => $this->post_var('row'),
            'grave' => $this->post_var('grave'),
            'comment' => $this->post_var('comment'),
            'map' => $this->post_var('map')
        ];
        
        $this->render('ritual-form.php', $data);
    }
    
    /*
     * Редактирование записи
     */
    function page_edit() {
        
        if (!isset($_GET['id'])) {
            $this->no_row();
        }
        
        $row_id = (int)$_GET['id'];
        
        if ($row_id == 0) {
            $this->no_row();
        }
        
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}ritual WHERE id=%d", $row_id);
        $row = $wpdb->get_row($query, ARRAY_A);
        
        if (empty($row)) {
            $this->no_row();
        }
        
        $has_errors = false;
                        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = array_map('trim', $_POST);
            
            $form = [
                'registration_number' => 'Регистрационный номер',
                'surname' => 'Фамилия/№',
                'name' => 'Имя',
                'patronymic' => 'Отчество',
                'date_birth' => 'Дата рождения',
                'date_death' => 'Дата смерти',
                'date_dburial' => 'Дата захоронения',
                'cemetery_name' => 'Кладбище',
                'site' => 'Участок',
                'grave' => 'Могила',
                'comment' => 'Комментарий',
                'map' => 'Код карты (Java Script)'
            ];
            
            foreach ($form as $key => $value) {
                if (empty($_POST[$key])) {
                    if (empty($_POST['grave']) || empty($_POST['comment']) || empty($_POST['map'])) {
                        $has_errors = false;
                        continue;
                    }
                    $has_errors = true;
                    add_settings_error($this->slug, 'notice', "Заполните поле \"{$value}\"", 'error');
                }
            }
            
            
            if (!preg_match('/^\d+$/', $_POST['registration_number'])) {
                $has_errors = true;
                add_settings_error($this->slug, 'notice', "В поле \"" . $form['registration_number'] . "\" должны быть тольцо цифры", 'error');
            }
            
            $date_birth = $this->parse_date($_POST['date_birth']);
            

            if (!is_array($date_birth) || !checkdate($date_birth['month'], $date_birth['day'], $date_birth['year'])) {
                $has_errors = true;
                add_settings_error($this->slug, 'notice', "Укажите правильно дату в поле \"" . $form['date_birth'] . "\"", 'error');
            }
            
            $date_death = $this->parse_date($_POST['date_death']);
            
            if (!is_array($date_death) || !checkdate($date_death['month'], $date_death['day'], $date_death['year'])) {
                $has_errors = true;
                add_settings_error($this->slug, 'notice', "Укажите правильно дату в поле \"" . $form['date_death'] . "\"", 'error');
            }
            
            $date_dburial = $this->parse_date($_POST['date_dburial']);
            
            if (!is_array($date_dburial) || !checkdate($date_dburial['month'], $date_dburial['day'], $date_dburial['year'])) {
                $has_errors = true;
                add_settings_error($this->slug, 'notice', "Укажите правильно дату в поле \"" . $form['date_dburial'] . "\"", 'error');
            }
          
            if (!$has_errors) {                
                $query = $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}ritual SET registration_number=%d, surname=%s, name=%s, patronymic=%s, date_birth=%s, date_death=%s, date_dburial=%s, cemetery_name=%s, site=%s, `row`=%s, `grave`=%s, `comment`=%s, `map`=%s WHERE id=%d",
                    $this->post_var('registration_number'),
                    $this->post_var('surname'),
                    $this->post_var('name'),
                    $this->post_var('patronymic'),
                    $this->post_var('date_birth'),
                    $this->post_var('date_death'),
                    $this->post_var('date_dburial'),
                    $this->post_var('cemetery_name'),
                    $this->post_var('site'),
                    $this->post_var('row'),
                    $this->post_var('grave'),
                    $this->post_var('comment'),
                    $this->post_var('map'),
                    $this->get_var('id')
                );
                
                $wpdb->query($query);
                
                $this->add_message('success', "Запись успешно отредактирована!");
                
                $action = http_build_query(['page' => $this->slug, 'action' => 'index']);
                $url = $_SERVER['PHP_SELF'] . '?' . $action;
                $data = [
                    'url' => $url
                ];

                $this->render('redirect.php', $data);
                exit();
            }
            
            $row['registration_number'] = $this->post_var('registration_number');
            $row['surname'] = $this->post_var('surname');
            $row['name'] = $this->post_var('name');
            $row['patronymic'] = $this->post_var('patronymic');
            $row['date_birth'] = $this->post_var('date_birth');
            $row['date_death'] = $this->post_var('date_death');
            $row['date_dburial'] = $this->post_var('date_dburial');
            $row['cemetery_name'] = $this->post_var('cemetery_name');
            $row['site'] = $this->post_var('site');
            $row['row'] = $this->post_var('row');
            $row['grave'] = $this->post_var('grave');
            $row['comment'] = $this->post_var('comment');
            $row['map'] = $this->post_var('map');
        }
        
        $action = http_build_query([
            'page' => $this->slug, 
            'action' => 'edit',
            'id' => $this->get_var('id')
        ]);
        $list_cemeteries = $wpdb->get_col("SELECT name FROM {$wpdb->prefix}cemeteries");
                
        $data = [
            'action' => $action,
            'title' => 'Редактировать запись',
            'slug' => $this->slug,
            'list_cemeteries' => $list_cemeteries,
            'registration_number' => $row['registration_number'],
            'surname' => $row['surname'],
            'name' => $row['name'],
            'patronymic' => $row['patronymic'],
            'date_birth' => $row['date_birth'],
            'date_death' => $row['date_death'],
            'date_dburial' => $row['date_dburial'],
            'cemetery_name' => $row['cemetery_name'],
            'site' => $row['site'],
            'row' => $row['row'],
            'grave' => $row['grave'],
            'comment' => $row['comment'],
            'map' => $row['map'],
        ];
        
        $this->render('ritual-form.php', $data);
    }
        
    /*
     * Удаление записи
     */
    function page_delete() {
        if (!isset($_GET['id'])) {
            $this->no_row();
        }
        
        $row_id = (int)$_GET['id'];
        
        if ($row_id == 0) {
            $this->no_row();
        }
        
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}ritual WHERE id=%d", $row_id);
        $row = $wpdb->get_row($query, ARRAY_A);
        
        if (empty($row)) {
            $this->no_row();
        }
        
        $query = $wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}ritual WHERE id=%d",
            $this->get_var('id')
        );
        
        $wpdb->query($query);
        
        $this->add_message('success', "Запись успешно удалена!");
        
        $action = http_build_query(['page' => $this->slug, 'action' => 'index']);
        $url = $_SERVER['PHP_SELF'] . '?' . $action;
        $data = [
            'url' => $url
        ];

        $this->render('redirect.php', $data);
        exit();
    }
            
    function parse_date($text) {
        if (preg_match('/(?P<day>\d{2})\.(?P<month>\d{2})\.(?P<year>\d+)/', $text, $result)) {
            return [
                'day' => $result['day'], 
                'month' => $result['month'], 
                'year' => $result['year'], 
            ];
        }
        return null;
    }

}


