<?php
require_once __DIR__ . '/config/database.php';

$page = $_GET['page'] ?? 'login';

switch ($page) {
    case 'login':
        require_once __DIR__ . '/views/auth/login.php';
        break;
    case 'logout':
        require_once __DIR__ . '/logout.php';
        break;
    case 'register':
        require_once __DIR__ . '/views/auth/register.php';
        break;
    case 'profile':
        require_once __DIR__ . '/views/user/profile.php';
        break;
    case 'projects':
        require_once __DIR__ . '/views/project/index.php';
        break;
    case 'project-create':
        require_once __DIR__ . '/views/project/create.php';
        break;
    case 'project-edit':
        require_once __DIR__ . '/views/project/edit.php';
        break;
    case 'project-delete':
        require_once __DIR__ . '/views/project/delete.php';
        break;
    case 'users':
        require_once __DIR__ . '/views/user/index.php';
        break;
    case 'user-create':
        require_once __DIR__ . '/views/user/create.php';
        break;
    case 'user-delete':
        require_once __DIR__ . '/views/user/delete.php';
        break;
        
    default:
        header("Location: index.php?page=projects");
        exit;
}
?>

