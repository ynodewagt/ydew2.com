<?php
    ob_start();
    session_start();
    include_once (__DIR__."/conf.php");

    /* проверка на технические работы */
    if ($settings_tech_work && !auth_get_adg_link()) {
        if (isset($_SESSION['lng'])) {
            $lng = $_SESSION['lng'];
        }
        session_unset();
        $_SESSION['lng'] = isset($lng) ? $lng : 'ru';
        $_SESSION['flash_datalogin'] = message_error('Технические работы');
    }

    echo '<!-- HYIP STUDIO SCRIPT-WIZARD.COM -->' . PHP_EOL;

    /* получение страницы */
    $page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";

    /* логин пострадавшего */
    $login = isset($_SESSION['login']) ? $_SESSION["login"] : "";

    $time = time();
    $start_time = get_start_time();
    $work_time = get_work_days(); //кол-во рабочих дней

    /* ip клиента */
    $ip = check_ip();

    /* определение реферала */
    if(!empty($_GET[$settings_define_href_refer])){
        session_unset();
        $_GET[$settings_define_href_refer] = preg_replace("#[^a-z\_\-0-9]+#i",'',$_GET[$settings_define_href_refer]);
        if($_GET[$settings_define_href_refer] != ''){
            $sql = "SELECT `login` FROM `users` WHERE `$settings_define_refer` = ".escape_db($_GET[$settings_define_href_refer]);
            $refq = $mysqli->query($sql);
            if($refq && $refq->num_rows > 0){
                $refm = $refq->fetch_row();
                $_SESSION['ref_login'] = $refm[0];
            }
            /* не работает на локальной машине */
            if (server('HTTP_REFERER')) $_SESSION['ref_site'] = server('HTTP_REFERER');
            $referer = escape_db(server('HTTP_REFERER'));
            $refm = escape_db($refm[0]);
            if (!empty($referer)) {
                $res = $mysqli->query("INSERT INTO `transits` (date, refer, site) VALUES ($time, $refm, $referer);");
                if ($res) server('HTTP_REFERER', '');
            }
            $link = '/';
            if (isset($_GET['lng'])) {
                $link = '/?lng='.$_GET['lng'];
            }
            redirect($link);
        }
    }

    if($start_time - $time > 0){
        $deposits = false;
        $invest = false;
        $payment = false;
        $close = "Запуск системы состоится ".date('j '.$mdate[date('n',$start_time)-1].' Y года в H:i МСК.',$start_time);
    }

    /* баланс системы */
    $balans_system = balans_invest() - balans_payment();

    $url = $_SERVER["REQUEST_URI"];
    $url = parse_url($url, PHP_URL_PATH);
    $url = trim($url, "/");
    $uri = $page = explode("/", $url);
    $dir = __DIR__."/theme/{$theme}/pages/".join("/", $uri);
    $file = "{$dir}.php";

    /* проверка на принадлежность к страницам кабинета */
    if (page_is_internal() && !auth_is_login()) {
        redirect('/login/');
    }



    if (get_page_intro()) {
        redirect('/intro');
    } else if (end($page) === 'exit') {
        if (isset($_SESSION['lng'])) { $lng = $_SESSION['lng']; }
        session_unset();
        $_SESSION["lng"] = isset($lng) ? $lng : "ru";
        redirect("/");
    } else if (is_dir($dir)) {
        $res = explode("/", $url);
        $page = end($res);
        require_once("{$dir}/index.php");
    } else if (is_file($file)) {
        $page = basename($file);
        $page = str_replace(".php", "", $page);
        /* Проверка на страницы forgot, login, signup (если пользователь авторизован - отправляем в кабинет) */
        auth_redir($page);
        require_once($file);
    } else {
        $page = "";
        header("HTTP/1.0 404 Not Found");
        if (require_once(ROOT."/404.php")) {

        } else {
            echo "404";
        }
        exit(0);
    }

    $sql = "UPDATE `users` SET  `login_page` =  ".escape_db(get_site_url().'/'.$url).", `login_page_time` =  ".escape_db(time())." WHERE  `login` = ".escape_db($login);
    $mysqli->query($sql);

    //$sql = "DELETE FROM invest WHERE status = ".escape_db(0)." AND date < ".escape_db(time() - 12 * 3600);
    //$mysqli->query($sql);