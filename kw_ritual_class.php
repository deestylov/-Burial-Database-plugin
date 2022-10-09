<?php

class KW_Ritual {
	protected $slug = '';
	protected $messages = [];
			
	function session_init() {
		if (!session_id()) {
			session_start();
		}
	}
	
	function callback_option_page() {
		
		if (!isset($_GET['action'])) {
			$this->page_index();
			return;
		}
		
		$action = $_GET['action'];
		$page = 'page_' . $action;
		
		$has_page = method_exists($this, $page);
		
		if ($has_page) {
			$this->$page();
		} else {
			$this->page_not_found();
		}
	}
	

	function no_row() {
		$this->add_message('error', 'Такой страницы не сушествует!');
			
		$action = http_build_query(['page' => $this->slug, 'action' => 'index']);
		$url = $_SERVER['PHP_SELF'] . '?' . $action;
		$data = [
			'url' => $url
		];

		$this->render('redirect.php', $data);
		exit();
	}
	
	/*
	 * Не существующая страница
	 */
	function page_not_found() {
		$this->render('404.php');
	}
	
	function render($template, $data = null) {
		if (is_array($data)) {
			extract($data);
		}
		
		$filepath = dirname(__FILE__) . '/templates/' . $template;
		include($filepath);
	}

	function get_var($key) {
		return isset($_GET[$key]) ? trim($_GET[$key]) : '';
	}
	
	function post_var($key) {
		return isset($_POST[$key]) ? trim($_POST[$key]) : '';
	}
		
	function add_message($type, $text) {
		$_SESSION['kw_messages'][] = ['type' => $type, 'text' => $text];
	}

	function get_messages() {
        if (isset($_SESSION['kw_messages'])) {
			$message = $_SESSION['kw_messages'];
            unset($_SESSION['kw_messages']);
            return $message;
        } 
        
        return [];
    }
}


