<?php
    $bm_version = '#version 4.6.18.6';

    /* настройки проекта */
    $data_settings = get_list("settings", "status = ".escape_db("1"));
    foreach ($data_settings as $item) {
        if (trim($item["value"]) === "false") {
            $GLOBALS[$item["name"]] = false;
            continue;
        }
        if (trim($item["value"]) === "true") {
            $GLOBALS[$item["name"]] = true;
            continue;
        }
        $GLOBALS[$item["name"]] = trim($item["value"]);
    }

    /* языки поддерживаемые в проекте. */
    $data_languages = get_list("language", "status = ".escape_db("1"), "priority");
    /* Больше одного языка, то выбор языка выводим  */
    /*$data_languages = count($data_languages) > 1 ? $data_languages : [];*/

    $list_languages = [];

    foreach ($data_languages as $item) {
        $array = [
            $item["label"] => [
                $item["flags"] => $item["title"],
            ],
        ];
        $list_languages = array_merge($list_languages, $array);
    }

    /* Перенести в модули, в индекс, для девелопмента, позволяет переключить быстро тему */
    if (DEVELOPMENT) {
        if (get('t') && $_SERVER["SERVER_ADDR"] === "127.0.0.1") {
            install_theme(get('t'));
            redirect('/');
        }
    }

    /* Запустим активирование профитов */
    activate_profits();

    //разделение по валютам, использтвать заперщено! Тесты не проходило
    $settings_define_separation_of_currency = false;

    //параметр реферальной ссылки
    $settings_define_href_refer = 'ref';
    //показывать параметр языка в реферальной ссылке
    $settings_define_language_refer = false;

    $system_id_in_paykassa = [
        "payeer"            => 1,
        "perfectmoney"      => 2,
        "qiwi"              => 3,
        "advcash"           => 4,
        "yandexmoney"       => 5,
        "berty"             => 7,
        "card"              => 9,
        "bitcoin"           => 11,
        "ethereum"          => 12,
        "litecoin"          => 14,
        "dogecoin"          => 15,
        "dash"              => 16,
        "bitcoincash"       => 18,
        "zcash"             => 19,
        "monero"            => 20,
        "ethereumclassic"   => 21,
        "ripple"            => 22,
        "neo"               => 23,
        "gas"               => 24,
    ];

    $system_id_in_casepay = [
        'card'          => 'card_rub',
        'yandexmoney'   => 'yandexmoney_rub',
        'qiwi'          => 'qiwi_rub',
    ];

    //время кэширования курсов
    define("CURRENCY_CASH_TIME", 4*3600); /* 4 часа */

    /*$list_currencys = [
        "RUB", //рубли
        "USD", //доллары
        "EUR", //евро
        "BTC", //биткойны
        "LTC", //лайткойны
        "DOGE",//догкойны
        "ETH", //этериум
        "DASH", //ДашКоин
        "BCH", //БиткоинКэш
        "ZEC", //Zcash
    ];

    function get_list_currencys() {
        global $list_currencys;
        return $list_currencys;
    }*/

    function get_list_currencies() {
        return array_values(array_flip(array_change_key_case(array_flip(get_column('exch_rates', 'in')), CASE_UPPER)));
    }

    function get_list_languages() {
        global $list_languages;
        return $list_languages;
    }

    $country = [
        "Russian Federation",
        "Ukraine",
        "Belarus",
        "Kazakhstan",
        "Asia/Pacific Region",
        "Europe",
        "Andorra",
        "United Arab Emirates",
        "Afghanistan",
        "Antigua and Barbuda",
        "Anguilla",
        "Albania",
        "Armenia",
        "Netherlands Antilles",
        "Angola",
        "Antarctica",
        "Argentina",
        "American Samoa",
        "Austria",
        "Australia",
        "Aruba",
        "Azerbaijan",
        "Bosnia and Herzegovina",
        "Barbados",
        "Bangladesh",
        "Belgium",
        "Burkina Faso",
        "Bulgaria",
        "Bahrain",
        "Burundi",
        "Benin",
        "Bermuda",
        "Brunei Darussalam",
        "Bolivia",
        "Brazil",
        "Bahamas",
        "Bhutan",
        "Bouvet Island",
        "Botswana",
        "Belize",
        "Canada",
        "Cocos (Keeling) Islands",
        "Congo The Democratic Republic of the",
        "Central African Republic",
        "Congo",
        "Switzerland",
        "Cote D'Ivoire",
        "Cook Islands",
        "Chile",
        "Cameroon",
        "China",
        "Colombia",
        "Costa Rica",
        "Cuba",
        "Cape Verde",
        "Christmas Island",
        "Cyprus",
        "Czech Republic",
        "Germany",
        "Djibouti",
        "Denmark",
        "Dominica",
        "Dominican Republic",
        "Algeria",
        "Ecuador",
        "Estonia",
        "Egypt",
        "Western Sahara",
        "Eritrea",
        "Spain",
        "Ethiopia",
        "Finland",
        "Fiji",
        "Falkland Islands (Malvinas)",
        "Micronesia Federated States of",
        "Faroe Islands",
        "France",
        "France Metropolitan",
        "Gabon",
        "United Kingdom",
        "Grenada",
        "Georgia",
        "French Guiana",
        "Ghana",
        "Gibraltar",
        "Greenland",
        "Gambia",
        "Guinea",
        "Guadeloupe",
        "Equatorial Guinea",
        "Greece",
        "South Georgia and the South Sandwich Islands",
        "Guatemala",
        "Guam",
        "Guinea-Bissau",
        "Guyana",
        "Hong Kong",
        "Heard Island and McDonald Islands",
        "Honduras",
        "Croatia",
        "Haiti",
        "Hungary",
        "Indonesia",
        "Ireland",
        "Israel",
        "India",
        "British Indian Ocean Territory",
        "Iraq",
        "Iran Islamic Republic of",
        "Iceland",
        "Italy",
        "Jamaica",
        "Jordan",
        "Japan",
        "Kenya",
        "Kyrgyzstan",
        "Cambodia",
        "Kiribati",
        "Comoros",
        "Saint Kitts and Nevis",
        "Korea Democratic People's Republic of",
        "Korea Republic of",
        "Kuwait",
        "Cayman Islands",
        "Lao People's Democratic Republic",
        "Lebanon",
        "Saint Lucia",
        "Liechtenstein",
        "Sri Lanka",
        "Liberia",
        "Lesotho",
        "Lithuania",
        "Luxembourg",
        "Latvia",
        "Libyan Arab Jamahiriya",
        "Morocco",
        "Monaco",
        "Moldova Republic of",
        "Madagascar",
        "Marshall Islands",
        "Macedonia",
        "Mali",
        "Myanmar",
        "Mongolia",
        "Macau",
        "Northern Mariana Islands",
        "Martinique",
        "Mauritania",
        "Montserrat",
        "Malta",
        "Mauritius",
        "Maldives",
        "Malawi",
        "Mexico",
        "Malaysia",
        "Mozambique",
        "Namibia",
        "New Caledonia",
        "Niger",
        "Norfolk Island",
        "Nigeria",
        "Nicaragua",
        "Netherlands",
        "Norway",
        "Nepal",
        "Nauru",
        "Niue",
        "New Zealand",
        "Oman",
        "Panama",
        "Peru",
        "French Polynesia",
        "Papua New Guinea",
        "Philippines",
        "Pakistan",
        "Poland",
        "Saint Pierre and Miquelon",
        "Pitcairn Islands",
        "Puerto Rico",
        "Palestinian Territory",
        "Portugal",
        "Palau",
        "Paraguay",
        "Qatar",
        "Reunion",
        "Romania",
        "Rwanda",
        "Saudi Arabia",
        "Solomon Islands",
        "Seychelles",
        "Sudan",
        "Sweden",
        "Singapore",
        "Saint Helena",
        "Slovenia",
        "Svalbard and Jan Mayen",
        "Slovakia",
        "Sierra Leone",
        "San Marino",
        "Senegal",
        "Somalia",
        "Suriname",
        "Sao Tome and Principe",
        "El Salvador",
        "Syrian Arab Republic",
        "Swaziland",
        "Turks and Caicos Islands",
        "Chad",
        "French Southern Territories",
        "Togo",
        "Thailand",
        "Tajikistan",
        "Tokelau",
        "Turkmenistan",
        "Tunisia",
        "Tonga",
        "Timor-Leste",
        "Turkey",
        "Trinidad and Tobago",
        "Tuvalu",
        "Taiwan",
        "Tanzania United Republic of",
        "Uganda",
        "United States Minor Outlying Islands",
        "United States",
        "Uruguay",
        "Uzbekistan",
        "Holy See (Vatican City State)",
        "Saint Vincent and the Grenadines",
        "Venezuela",
        "Virgin Islands British",
        "Virgin Islands U.S.",
        "Vietnam",
        "Vanuatu",
        "Wallis and Futuna",
        "Samoa",
        "Yemen",
        "Mayotte",
        "Serbia",
        "South Africa",
        "Zambia",
        "Montenegro",
        "Zimbabwe",
        "Anonymous Proxy",
        "Satellite Provider",
        "Other",
        "Aland Islands",
        "Guernsey",
        "Isle of Man",
        "Jersey",
        "Saint Barthelemy",
        "Saint Martin",
    ];

    //    /**
    //     *  Функциии переопределения,
    //     *  settings__  - настройка
    //     *  do__        - действие
    //     *  Ответ в формате ЕФОД
    //     */


    //    /* Переопределяем создание депозита */
    //    function settings__add_deposits_plans() {
    //        //return message_success('Депозит успешно создан.', ['deposit_id' => $deposits_id]);
    //        return [
    //            'define' => true, /* true - переопределена */
    //            'func' => 'do__add_deposits_plans', /* название функции которую вызываем, передаем id пополнения */
    //        ];
    //    }
    //
    //    /* Функция переопределения создание депозита */
    //    function do__add_deposits_plans($login, $amount, $invest_id, $system=false, $plan_id=false) {
    //        var_dump($login, $amount, $invest_id, $system, $plan_id, 'Ура');die();
    //    }


    //    /* Переопределить функцию активации пополнения */
    //    function settings__active_invest() {
    //        return [
    //            'define' => false, /* true - переопределена */
    //            'func' => 'do__active_invest' /* название функции которую вызываем, передаем id пополнения */
    //        ];
    //    }
    //
    //    /* Функция переопределения активации пополнения по id */
    //    function do__active_invest($id) {
    //        var_dump($id, 'Ура');die();
    //    }


    //    /* Переопределяем вывод html флагов */
    //    function settings__get_language_flags() {
    //        return [
    //            'define' => true, /* true - переопределена */
    //            'func' => 'do__get_language_flags', /* название функции которую вызываем, передаем id пополнения */
    //        ];
    //    }
    //
    //    /* Функция переопределения создание депозита */
    //    function do__get_language_flags($type, $style="", $style_list="", $show_label=false) {
    //        var_dump($type, $style, $style_list, $show_label, 'Ура');die();
    //    }