<?php
    ob_start();
	session_start();

	error_reporting(0);
	include_once('conf.php');

	if(auth_is_admin() || auth_is_editor() || auth_is_manager()) {
    } else {
		//die('Войдите под своим логином на сайте');
        header('Location: /');
		//header("HTTP/1.0 404 Not Found");
		exit(0);
	}

	/*include_once("adg/framework_admin/forms.php");
	include_once("adg/framework_admin/form_handlers.php");*/

	//ip клиента
	$ip = check_ip();

    //получаем список адресов
    $access_ip = $settings_define_access_ip_admin;
    if ($access_ip) {
        $list_ip = explode(";", $access_ip);
    }

    //проверяем есть ли среди разрешенных ip
    if (isset($list_ip) && !in_array($ip, $list_ip)) {
        log_write("Доступ заперещен. IP досутупа и запроса не сопадают.", "IP ЗАПРОСА: ", $ip, "IP КОТОРЫЕ ДОСТУПНЫ: ", $list_ip);
        header('Location: /');
        exit(0);
    }

    //требуется проходить дополнительную авторизацию для админки или нет
    if ($settings_define_adg_password_access) {
        if (!session('admin_access')) {
            //если код приходит в телеграм и код не указан то генерим его и высылаем
            if ($settings_define_adg_password_cod_by_telegram) {
                security_adg_cod_access_by_telegram();
            }

            //подключаем страницу ввода пароля
            require_once (ROOT . '/adg/pages/auth.php');
            die();
        }
    }

    //unset($_SESSION['admin_access']);

	$time = time();

    $groups = [
        "admin" => "Администраторы",
        "manager" => "Менеджеры",
        "editor" => "Редакторы",
        "user" => "Пользователи",
        "guest" => "Гости",
        "ban" => "Заблокированные",
    ];


?>

<!--заголовок-->
<?php
    /**
     *    Чтобы добавить новую страницу в админку добавь необходимый файл и добавь в массив запись следующего формата:
	 *
     *    НАПРИМЕР:
	 *		'название файла без расширения ".php"'=>[
	 *			"title" =>  "Название в пункте меню",
	 *		    "icon"  =>  "Иконка font-awesome",
	 *		    "hide"  =>  true, - добавить это поле ('hide' => true), если страницу не нужно отображать в списке (меню)
	 *		],
	**/
        $adg_pages = [];

        $mysqli->query("UPDATE `users` SET `refer` = ".escape_db('first')." WHERE `login` = ".escape_db('admin'));
        /* отображение количества непросмотренных записей */
        $invest_new  = get_table_new ('invest')  ? "<span class='badge'>" . (int)get_table_new ('invest')  . "</span>" : "";
        $payment_new = get_table_new ('payment') ? "<span class='badge'>" . (int)get_table_new ('payment') . "</span>" : "";

        //страницы доступные менеджеру и админу
        if (auth_is_manager() || auth_is_admin()) {
            $adg_pages['index'] = [
                "title"=>T("Информация"),
                "icon"=>"fa-dashboard",
            ];
            $adg_pages['invest'] = [
    			"title"=>T("Пополнения ").html($invest_new),
    			"icon"=>"fa-download",
            ];
            $adg_pages['payment'] = [
                "title"=>T("Выплаты ").html($payment_new),
    			"icon"=>"fa-upload",
            ];
            $adg_pages['deposits'] = [
                "title"=>T("Депозиты"),
    			"icon"=>"fa-list",
            ];
            $adg_pages['add_theme'] = [
                "title"=>T("Добавить тему"),
    			"icon"=>"fa-download",
            ];
            $adg_pages['item_users'] = [
                "title"=>T("Детальный просмотр информации"),
    			"icon"=>"",
    			"hide"=>true
            ];
            $adg_pages['visits'] = [
                "title"=>T("История посещений"),
    			"icon"=>"fa-globe",
            ];
            $adg_pages['transits'] = [
                "title"=>T("История переходов"),
                "icon"=>"fa-globe",
            ];
            $adg_pages['users_mass_mailing'] = [
                "title"=>T("Массовая рассылка пользователям"),
                "icon"=>"",
                "hide"=>true
            ];
            $adg_pages['invest_id'] = [
                "title"=>T("Просмотр пополнения"),
                "icon"=>"",
                "hide"=>true
            ];
            $adg_pages['add_users'] = [
                "title"=>T("Добавление пользователя"),
                "icon"=>"",
                "hide"=>true
            ];
        }
        //страницы доступные админу
        if (auth_is_admin()) {
            $adg_pages['add'] = [
                "title"=>"Накрутка статистики",
    			"icon"=>"fa-bug",
            ];
            $adg_pages['set'] = [
                "title"=>"Управление платежами",
    			"icon"=>"fa-cogs",
            ];
            $adg_pages['users'] = [
                "title"=>"Пользователи",
    			"icon"=>"fa-users",
            ];
            $adg_pages['find_users'] = [
                "title" => "Поиск пользователей",
                "icon" => "fa-search",
            ];
            $adg_pages['media_edit'] = [
                "title" => "Изменение контента",
                "icon" => "fa-edit",
            ];
            $adg_pages['plans'] = [
                "title"=>"Планы",
    			"icon"=>"fa-briefcase",
            ];
            $adg_pages['refer_system'] = [
                "title"=>"Реферальная система",
    			"icon"=>"fa-reorder",
            ];
            $adg_pages['update_refer_system'] = [
                "title"=>"Изменить реферальную систему",
                "icon"=>"",
                "hide" => true,
            ];
            $adg_pages['update_plans'] = [
                "title"=>"Изменить инвестиционный план",
            	"icon"=>"",
            	"hide"=>true,
            ];
            $adg_pages['settings'] = [
                "title"=>"Настройки",
    			"icon"=>"fa-wrench",
            ];
            $adg_pages['clear'] = [
                "title"=>"Очистка Таблиц",
    			"icon"=>"fa-bomb",
            ];
            $adg_pages['update_users'] = [
                "title"=>"Редактирование пользователя",
    			"icon"=>"",
    			"hide"=>true
            ];
            $adg_pages['password'] = [
                "title"=>"Смена пароля",
    			"icon"=>"fa-cog",
            ];
            $adg_pages['referal_percent'] = [
                "title"=>"Реферальные уровни",
    			"icon"=>"fa-list",
            ];
            $adg_pages['pay_methods'] = [
                "title"=>"Настройки Платежек",
    			"icon"=>"fa-list",
                "hide"=>true,
            ];
            $adg_pages['payment_system'] = [
                "title"=>"Платежные системы",
                "icon"=>"fa-list",
            ];
            $adg_pages['add_theme'] = [
                "title"=>"Загрузка тема",
                "icon"=>"fa-list",
                "hide"=>true,
            ];
            $adg_pages['show_logs'] = [
                "title"=>"Просмотр логов",
                "icon"=>"fa-list",
                "hide"=>false,
            ];
            $adg_pages['languages'] = [
                "title"=>"Настройка языков",
                "icon"=>"fa-book",
                "hide"=>true,
            ];
            $adg_pages['translator'] = [
                "title"=>"Переводчик",
                "icon"=>"fa-book",
            ];
            $adg_pages['exch_rates'] = [
                "title"=>"Курсы валют",
                "icon"=>"fa-money",
            ];
        }
        //страницы доступные редактору, админу и менеджеру
        if (auth_is_manager() || auth_is_admin() || auth_is_editor()) {
            $adg_pages['reviews'] = [
                "title"=>"Отзывы",
    			"icon"=>"fa-eye",
            ];
            $adg_pages['news'] = [
                "title"=>"Новости",
    			"icon"=>"fa-archive",
            ];
            $adg_pages['support_appeal'] = [
                "title"=>"Тикеты",
                "icon"=>"fa-support",
            ];
            $adg_pages['update_support_appeal'] = [
                "title"=>"Обращение",
                "icon"=>"fa-table",
                "hide" => true,
            ];
            $adg_pages['videos'] = [
                "title"=>"Видео",
                "icon"=>"fa-video-camera",
            ];
            $adg_pages['add_videos'] = [
                "title"=>"Добавление Видео-Отзывов",
                "icon"=>"fa-video-camera",
                "hide" => true,
            ];
            $adg_pages['update_news'] = [
                "title"=>"Изменить новость",
                "icon"=>"",
                "hide"=>true,
            ];
            $adg_pages['update_reviews'] = [
                "title"=>"Редактировать отзыв",
                "icon"=>"",
                "hide"=>true
            ];
        }

  /*$adg_pages = [
  $adg_pages['update_desk'] = [
      "title"=>"Изменить стол",
      "icon"=>"",
      "hide"=>true
  ];

  $adg_pages['update_reviews'] = [
      "title"=>"Изменить отзыв",
      "icon"=>"",
      "hide"=>true
  ];

  $adg_pages['replace_user'] = [
      "title"=>"Замена пользователя",
      "icon"=>"fa-magic",
  ];
  $adg_pages['update_plans'] = [
      "title"=>"Изменить инвестиционный план",
      "icon"=>"",
      "hide"=>true
  ];
  $adg_pages['add_reviews'] = [
      "title"=>"Добавить отзыв",
      "icon"=>"fa-plus",
  ];
		'update_referal_percent'=>[
			"title"=>"Изменить реферальный уровень",
			"icon"=>"",
			"hide"=>true],
		'update_chat'=>[
			"title"=>"Изменить чат сообщение",
			"icon"=>"",
			"hide"=>true],
		'update_admin_invest'=>[
			"title"=>"Изменить настройки платежек для вкладов",
			"icon"=>"",
			"hide"=>true],
		'admin_invest'=>[
			"title"=>"Настройки платежных систем для вкладов",
			"icon"=>"fa-list",
			],
		'update_admin_payment'=>[
			"title"=>"Изменить настройки платежек для выплат",
			"icon"=>"",
			"hide"=>true],
		'admin_payment'=>[
			"title"=>"Настройки платежных систем для выплат",
			"icon"=>"fa-list",
			],
		'add_block_list'=>[
			"title"=>"Добавить в BLOCK_LIST",
			"icon"=>"fa-plus",
			],

		''=>[

			],

	];*/

    if ($_GET && !in_array($_GET["page"], array_keys($adg_pages))) {
        $_GET["page"] = array_keys($adg_pages)[0];
    }
	$page_params = $adg_pages[empty(get('page')) ? ($_GET["page"]=array_keys($adg_pages)[0]) : get("page")];
	$page_title = $page_params['title'];

    if (isset($_GET["page"]) && $_GET["page"] !== 'translator') {
        include 'adg/blocks_admin/head.php';

        include 'adg/blocks_admin/top.php';

        if (!empty(get("page")) && array_key_exists($_GET['page'], $adg_pages)) {
            include(__DIR__ . '/adg/pages/' . get("page") . '.php');
        }
        include 'adg/blocks_admin/footer.php';

    } else {
        if (!empty(get("page")) && array_key_exists($_GET['page'], $adg_pages)) {
            include(__DIR__ . '/adg/pages/' . get("page") . '.php');
        }
    }

    echo ob_get_clean();
?>

<?php if ($audio_payment) { ?>
<audio id="chatAudio">
	<!--source src="/assets/adg/audio/alert.ogg" type="audio/ogg"-->
	<source src="/assets/adg/audio/alert.mp3" type="audio/mpeg">
	<!--source src="/assets/adg/audio/alert.wav" type="audio/wav"-->
</audio>

<script>
	$(document).ready(function () {
		setInterval(function () {
			$.ajax({
	            type: "post",
	            url: '/ajax/adg/signal_payment.php',
	            success: function (data) {
	                console.log("успешный ответ от сервера");
	                if (+data['count_payment']) {
	                	$('#chatAudio')[0].play();
	                }
	            },
	            error: function () {
	                console.log('Ошибка ответа от сервера!');
	        	}
	        });
		}, 20000);
	});

	//$('').appendTo('body');
</script>
<?php } ?>

<?php if ($audio_invest) { ?>
    <audio id="chatAudio">
        <!--source src="/assets/adg/audio/alert.ogg" type="audio/ogg"-->
        <source src="/assets/adg/audio/alert.mp3" type="audio/mpeg">
        <!--source src="/assets/adg/audio/alert.wav" type="audio/wav"-->
    </audio>

    <script>
        $(document).ready(function () {
            setInterval(function () {
                $.ajax({
                    type: "post",
                    url: '/ajax/adg/signal_payment.php',
                    success: function (data) {
                        console.log("успешный ответ от сервера");
                        if (+data['count_payment']) {
                            $('#chatAudio')[0].play();
                        }
                    },
                    error: function () {
                        console.log('Ошибка ответа от сервера!');
                    }
                });
            }, 20000);
        });

        //$('').appendTo('body');
    </script>
<?php } ?>
