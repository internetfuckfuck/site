<?php
// Задаём основную кодировку страницы.
header('Content-Type: text/html; charset=utf-8');

// Отключаем вывод ошибок.
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('opcache.revalidate_freq', 0);

// Ограничиваем время выполнения скрипта.
set_time_limit(4);

// Нахожение в пространстве LR.
define('IN_LR', true);

// Версия шаблона
define('VERSION', '2.0.11');

// Основная директория вэб-приложения.
define('APP', 'app/');

// Основная директория вэб-приложения.
define('STORAGE', 'storage/');

// Директория содержащая основные блоки вэб-приложения.
define('PAGE', APP . 'page/general/');

// Директория содержащая дополнительные блоки вэб-приложения.
define('PAGE_CUSTOM', APP . 'page/custom/');

// Директория с модулями.
define('MODULES', APP . 'modules/');

// Директория с шаблонами
define('TEMPLATES', APP . 'templates/');

// Директория с основными конфигурационными файлами.
define('INCLUDES', APP . 'includes/');

// Директория содержащая графические кэш-файлы.
define('CACHE', STORAGE . 'cache/');

define('MODULESCACHE', STORAGE . 'modules_cache/');

// Директория с ресурсами.
define('ASSETS', STORAGE . 'assets/');

// Директория с основными кэш-файлами.
define('SESSIONS', CACHE . 'sessions/');

// Директория содержащая логи.
define('LOGS', CACHE . 'logs/');

// Директория содержащая изображения.
define('IMG', CACHE . 'img/');

// Директория с CSS шаблонами.
define('ASSETS_CSS', ASSETS . 'css/');

// Директория с JS библиотеками.
define('ASSETS_JS', ASSETS . 'js/');

// Директория с изображениями рангов.
define('RANKS_PACK', IMG . 'ranks/');

// Временные константы ( Постоянные времени ) - Минута.
define('MINUTE_IN_SECONDS', 60);

// Временные константы ( Постоянные времени ) - Час.
define('HOUR_IN_SECONDS', 3600);

// Временные константы ( Постоянные времени ) - День.
define('DAY_IN_SECONDS', 86400);

// Временные константы ( Постоянные времени ) - Неделя.
define('WEEK_IN_SECONDS', 604800);

// Временные константы ( Постоянные времени ) - Месяц.
define('MONTH_IN_SECONDS', 2592000);

// Временные константы ( Постоянные времени ) - Год.
define('YEAR_IN_SECONDS', 31536000);

// Регистраниция основных функций.
require INCLUDES . 'functions.php';

// Создание/возобновление сессии.
session_start();

unset($_SESSION['iframe']);

// Включение буферизации.
ob_start();

// Импортирование класса отвечающего за работу с языками и переводами.
use app\ext\Translate;

// Импортирование глобального класса отвечающего за работу с базами данных.
use app\ext\Db;

// Импортирование основного глобального класса.
use app\ext\General;

// Импортирование глобального класса отвечающего за работу с модулями.
use app\ext\Modules;

// Импортирование класса уведомлений.
use app\ext\Notifications;

// Импортирование глобального класса отвечающего за работу с авторизованными пользователями.
use app\ext\Auth;

// Импортирование графического класса.
use app\ext\Graphics;

//Использование роутера страницы
use app\ext\AltoRouter;

use app\ext\ErrorsHandler;

// __autoload()
spl_autoload_register( function( $class ) {
    $path = str_replace( '\\', '/', $class . '.php' );
    file_exists( $path ) && require $path;
} );

$ErrorsHandler  = new ErrorsHandler;

$ErrorsHandler->setErrors();

// Создание экземпляра класса работающего с языками и переводами.
$Translate = new Translate;

// Создание экземпляра класса работающего с базами данных.
$Db = new Db;

// Создание экземпляра класса уведомлений.
$Notifications = new Notifications ( $Translate, $Db );

// Создание основного экземпляра класса.
$General = new General ( $Db );

// Проверка пользователей, которые в бане
$blocked_users = require SESSIONS . 'blockedusers.php';
$steamid = isset($_SESSION['steamid64']) ? $_SESSION['steamid64'] : null;
foreach ($blocked_users as $blocked_user) {
    if (($blocked_user['ip'] == $General->get_client_ip_cdn()) || ($steamid && $blocked_user['steam_id'] == $steamid)) {
        get_iframe('I1', 'Вы получили блокировку') && die();
    }
}

// AntiVPN
if (!isset($_SESSION['user_admin']) && !empty($General->arr_general['antivpn'])) {
    if ($General->check_vpn($General->get_client_ip_cdn())) {
        get_iframe('I2', 'Обнаружено использование VPN. Доступ к сайту ограничен') && die();
    }
}

if (!isset($_SESSION['user_admin']) && !empty($General->arr_general['thoseworks'])) {
    get_iframe('I3', 'На сайте включены тех. работы') && die();
}

// Импортирование класса с роутингом
$Router = new AltoRouter;

empty( $General->arr_general['site'] ) && $General->arr_general['site'] = '//' . preg_replace('/^(https?:)?(\/\/)?(www\.)?/', '', $_SERVER['HTTP_REFERER']);

// Добавление корневого роута
$Router->setBasePath( parse_url($General->arr_general['site'], PHP_URL_PATH));

// Создание экземпляра класса работающего с модулями.
$Modules = new Modules ( $General, $Translate, $Notifications, $Router );

// Создание экземпляра класса работающего с авторизацией пользователей.
$Auth = new Auth ( $General, $Db );

// Создание экземпляра графического класса.
$Graphics = new Graphics ( $Translate, $General, $Modules, $Db, $Auth, $Notifications, $Router );

// Запуск счетчика переходов(поситителей)
$General->online_stats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOTA 2 AHAHAHAHA</title>
    <!-- Подключение стилей и скриптов из particles.php -->
    <?php include 'particles.php'; ?>
</head>
<body>
    <?php include 'stars.php'; ?>
    <nav>
    </nav>
</body>
</html>
