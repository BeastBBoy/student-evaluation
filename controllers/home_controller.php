<?php
// Make sure there's no AuthController class defined in this file
// If there is, rename it or move it to the proper file

class HomeController {
    public function index() {
        require_once VIEWS_PATH . '/templates/header.php';
        require_once VIEWS_PATH . '/home/index.php';
        require_once VIEWS_PATH . '/templates/footer.php';
    }
    
    // Other methods...
}