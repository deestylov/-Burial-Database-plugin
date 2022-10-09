<?php

class KW_CemeteriesAdmin extends Kw_Ritual
{
    private $parent_slug = 'kw_ritual_admin';
    protected $slug = 'kw_cemeteries_admin';

    function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_init', [$this, 'session_init']);
    }

    function admin_menu()
    {
        $page_title = 'Список кладбищ';
        $menu_title = 'Список кладбищ';
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

    function enqueue_scripts($hook_suffix)
    {
        if (mb_strpos($hook_suffix, $this->slug) === false) {
            return;
        }

        wp_enqueue_style(
            'kw_sweetalert',
            'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css',
            null,
            null
        );
        wp_enqueue_style(
            'kw_style',
            plugins_url('css/style.css', __FILE__),
            null,
            0.01
        );

        wp_enqueue_script(
            'kw_sweetalert',
            'https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js',
            null,
            null,
            true
        );
        wp_enqueue_script(
            'kw_script',
            plugins_url('js/script.js', __FILE__),
            ['jquery', 'kw_sweetalert'],
            0.01,
            true
        );
    }

    /*
     * Список всех кладбищ
     */
    function page_index()
    {
        global $wpdb;

        $count_rows = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}cemeteries"
        );
        $page_number = isset($_GET['page_number'])
            ? (int) $_GET['page_number']
            : 1;
        $page_number = $page_number == 0 ? 1 : $page_number;
        $limit = 50;
        $index = 'page_number=';
        $pagination = new Pagination($count_rows, $page_number, $limit, $index);
        $offset = ($page_number - 1) * $limit;

        $rows = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}cemeteries LIMIT {$limit} OFFSET {$offset}",
            ARRAY_A
        );

        $data = [
            'messages' => $this->get_messages(),
            'slug' => $this->slug,
            'count_rows' => $count_rows,
            'rows' => $rows,
            'pagination' => $pagination
        ];

        $this->render('cemeteries_rows.php', $data);
    }

    /*
     * Добавление записи
     */
    function page_add()
    {
        global $wpdb;
        $has_errors = false;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = array_map('trim', $_POST);

            $form = [
                'name' => 'Наименование кладбища'
            ];

            foreach ($form as $key => $value) {
                if (empty($_POST[$key])) {
                    $has_errors = true;
                    add_settings_error(
                        $this->slug,
                        'notice',
                        "Заполните поле \"{$value}\"",
                        'error'
                    );
                }
            }

            $count_rows = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}cemeteries WHERE name=%s",
                    $this->post_var('name')
                )
            );

            if ($count_rows > 0) {
                $has_errors = true;
                add_settings_error(
                    $this->slug,
                    'notice',
                    "С таким наименованием \"" .
                        $this->post_var('name') .
                        "\" в базе  существует запись",
                    'error'
                );
            }

            if (!$has_errors) {
                $query = $wpdb->prepare(
                    "INSERT INTO {$wpdb->prefix}cemeteries (name, iframe) VALUES (%s, %s)",
                    $this->post_var('name'),
                    $this->post_var('iframe')
                );

                $wpdb->query($query);

                $this->add_message('success', "Запись успешно добавлена!");

                $action = http_build_query([
                    'page' => $this->slug,
                    'action' => 'index'
                ]);
                $url = $_SERVER['PHP_SELF'] . '?' . $action;
                $data = [
                    'url' => $url,
                    'slug' => $this->slug
                ];

                $this->render('redirect.php', $data);
                exit();
            }
        }

        $action = http_build_query(['page' => $this->slug, 'action' => 'add']);

        $data = [
            'action' => $action,
            'slug' => $this->slug,
            'title' => 'Добавить запись',
            'name' => $this->post_var('name'),
            'iframe' => $this->post_var('iframe')
        ];

        $this->render('cemetery-form.php', $data);
    }

    /*
     * Редактирование записи
     */
    function page_edit()
    {
        if (!isset($_GET['id'])) {
            $this->no_row();
        }

        $row_id = (int) $_GET['id'];

        if ($row_id == 0) {
            $this->no_row();
        }

        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cemeteries WHERE id=%d",
            $row_id
        );
        $row = $wpdb->get_row($query, ARRAY_A);

        if (empty($row)) {
            $this->no_row();
        }

        $has_errors = false;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = array_map('trim', $_POST);

            $form = [
                'name' => 'Наименование кладбища'
            ];

            foreach ($form as $key => $value) {
                if (empty($_POST[$key])) {
                    $has_errors = true;
                    add_settings_error(
                        $this->slug,
                        'notice',
                        "Заполните поле \"{$value}\"",
                        'error'
                    );
                }
            }

            $row_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}cemeteries WHERE name=%s",
                    $this->post_var('name')
                )
            );

            if (!empty($row_id) && $row_id != $this->get_var('id')) {
                $has_errors = true;
                add_settings_error(
                    $this->slug,
                    'notice',
                    "С таким наименованием \"" .
                        $this->post_var('name') .
                        "\" в базе  существует запись",
                    'error'
                );
            }
            if (!$has_errors) {
                $old_cemetery_name = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT name FROM {$wpdb->prefix}cemeteries  WHERE id=%d",
                        $this->get_var('id')
                    )
                );

                $query = $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}cemeteries SET name=%s, iframe=%s WHERE id=%d",
                    $this->post_var('name'),
                    $this->post_var('iframe'),
                    $this->get_var('id')
                );

                $wpdb->query($query);

                $query = $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}ritual SET cemetery_name=%s WHERE cemetery_name=%s",
                    $this->post_var('name'),
                    $old_cemetery_name
                );

                $wpdb->query($query);

                $this->add_message('success', "Запись успешно изменена!");

                $action = http_build_query([
                    'page' => $this->slug,
                    'action' => 'index'
                ]);
                $url = $_SERVER['PHP_SELF'] . '?' . $action;
                $data = [
                    'url' => $url,
                    'slug' => $this->slug
                ];

                $this->render('redirect.php', $data);
                exit();
            }

            $row['name'] = $this->post_var('name');
            $row['iframe'] = $this->post_var('iframe');
        }

        $action = http_build_query([
            'page' => $this->slug,
            'action' => 'edit',
            'id' => $this->get_var('id')
        ]);

        $data = [
            'action' => $action,
            'slug' => $this->slug,
            'title' => 'Редактировать запись',
            'name' => $row['name'],
            'iframe' => $row['iframe']
        ];

        $this->render('cemetery-form.php', $data);
    }

    /*
     * Удаление записи
     */
    function page_delete()
    {
        if (!isset($_GET['id'])) {
            $this->no_row();
        }

        $row_id = (int) $_GET['id'];

        if ($row_id == 0) {
            $this->no_row();
        }

        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cemeteries WHERE id=%d",
            $row_id
        );
        $row = $wpdb->get_row($query, ARRAY_A);

        if (empty($row)) {
            $this->no_row();
        }

        $query = $wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}cemeteries WHERE id=%d",
            $this->get_var('id')
        );

        $wpdb->query($query);

        $this->add_message('success', "Запись успешно удалена!");

        $action = http_build_query([
            'page' => $this->slug,
            'action' => 'index'
        ]);
        $url = $_SERVER['PHP_SELF'] . '?' . $action;
        $data = [
            'url' => $url
        ];

        $this->render('redirect.php', $data);
        exit();
    }
}
