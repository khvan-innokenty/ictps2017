<?php

include_once "lib/PHPMailer/PHPMailerAutoload.php";

$f3=require('lib/base.php');

if ((float)PCRE_VERSION<7.9)
	trigger_error('PCRE vesion is out of date');

$f3->config('config.ini');
$f3->config('data/event.ini');

$f3->set('dates', getEventsDates());

\Template::instance()->filter('svg', 'inline_svg');
\Template::instance()->filter('price', 'price');
\Template::instance()->filter('strip', 'strip_tags');

$f3->route('GET /',
	function($f3) {
        $lang = explode(',', $f3->get('LANGUAGE'));
        $lang = $lang[0];
        if ($lang === 'en') $f3->reroute('/en');
        $f3->reroute('/ru');
	}
);

$f3->route('GET /ru',
	function($f3) {
        $f3->set('LANG', 'ru');
        $f3->set('LANGUAGE', 'ru');
        $f3->set('other_lang', 'en');
        loadData();
        loadTickets();
        loadBusiness();
        loadExpo();
        echo \Template::instance()->render('index.html');
	}
);

$f3->route('GET /en',
	function($f3) {
        $f3->set('LANG', 'en');
        $f3->set('LANGUAGE', 'en');
        $f3->set('other_lang', 'ru');
        loadData();
        loadTickets();
		loadBusiness();
        loadExpo();
        echo \Template::instance()->render('index.html');
	}
);

$f3->route('GET /location',
    function ($f3){
        $ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_SANITIZE_STRING);
        $location = getLocation( $ip );
        echo $location;
    }
);

$f3->route('POST /register', 'register');

$f3->run();


/* ======================================================================================= */


/**
 * Зарегистрировать пользователя
 * Todo: реализовать запись данных пользователя в CRM
 * @param $f3 Base
 */
function register($f3) {
	$post = $f3->get('POST');
	$ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_SANITIZE_STRING);

	$post = filter_var_array($post, FILTER_SANITIZE_STRING);

	$tickets = $f3->get('tickets');
	$ticketId = intval($post['ticket']);
	$ticketTitle = "";
	for ($i = 0; $i<count($tickets); $i++) {
		if ($ticketId === intval($tickets[$i]['id']) ) {
			$ticketTitle = $tickets[$i]['title'];
			if (is_array($ticketTitle)) $ticketTitle = implode(" ", $ticketTitle);
			$ticketTitle .= " " . $tickets[$i]['comment'];
			break;
		}
	}

	$data[] = "---------------------------------------";
	$data[] = date('d.m.Y H:i:s');
	$data[] = "РЕГИСТРАЦИЯ";
	$data[] = "Пакет участия: " . $ticketTitle . " (id=" . $ticketId . ")";
	$data[] = "ФИО: " . $post['fullname'];
	$data[] = "Телефон: " . $post['tel'];
	$data[] = "Почта: " . $post['email'];
	$data[] = "Город (по IP-адресу): " . $post['city'];
	$data[] = "Уже посещал ICTPS ранее: " . ($post['regular'] ? 'ДА' : 'нет');
	$data[] = "Ординатор/интерн/аспирант: " . ($post['student'] ? 'ДА' : 'нет');
	$data[] = "Член РОПРЭХ: " . ($post['spras'] ? 'ДА' : 'нет');
	$data[] = "Пойдёт на гала-ужин: " . ($post['dinner'] ? 'ДА' : 'нет');
	$data[] = "Нужна помощь с проживанием: " . ($post['appointment'] ? 'ДА' : 'нет');
	$data[] = "Цена: " . $post['price'] . ' руб.';
	$data[] = "IP: " . $ip;
	$data[] = "---------------------------------------";
	$data[] = "";

	sendEmail('d.medentsov@bioconcept.ru', 'Регистрация на ICTPS (' . $post['fullname'] . ')', $data );
	sendEmail('events@bioconcept.ru', 'Регистрация на ICTPS (' . $post['fullname'] . ')', $data );

	file_put_contents( $f3->get('ROOT') .'/registration.txt', implode("\r\n", $data), FILE_APPEND);

	/*
	$myCurl = curl_init();
	curl_setopt_array($myCurl, array(
		CURLOPT_URL => 'http://www.ictps.ru/events/new_order.php',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query($post)
	));
	$response = curl_exec($myCurl);
	curl_close($myCurl);
	*/

	$response = '{"status":1, "msg":"Вы успешно зарегистрированы на мероприятие."}';

	echo $response;
}


/**
 * Загрузить прайслист
 */
function loadTickets() {
    $f3 = Base::instance();

    // Загружаем и обрабатываем даты смены цен
    $pricelistDates = $f3->get('pricelist.dates');
    $pricelistTitles = array();
    $pricelistClasses = array();
    $currentPricePos = null;
    $timestamp = strtotime(date('Y-m-d'));
    for ($i=0; $i < count($pricelistDates); $i++) {
        $date = trim($pricelistDates[$i]);
        if ( $date === "+" && count($pricelistTitles) ) {
            $nextDay = date('Y-m-d', strtotime('+1 day', strtotime($pricelistDates[$i-1])));
            array_push($pricelistTitles, "с " . shortDateFormat( $nextDay ));
            if ( is_null($currentPricePos) ) {
                array_push($pricelistClasses, 'current');
                $currentPricePos = $i;
            }
        } else {
            array_push($pricelistTitles, "до " . shortDateFormat( $date ));
            $class = strtotime($date) < $timestamp ? "past" : "";
            if ($class === "" && !count($pricelistClasses) ) $class = "current";
            if ($class === "" && count($pricelistClasses) && $pricelistClasses[ count($pricelistClasses)-1 ] === "past" ) $class = "current";
            if ($class === "current") $currentPricePos = $i;
            array_push($pricelistClasses, $class);
        }
    }
    $f3->set('pricelist.titles', $pricelistTitles);
    $f3->set('pricelist.classes', $pricelistClasses);

    // Загружаемы и обрабратываем варианты участия
    $tickets = $f3->get('tickets');
    $pricelistCurrent = array();
    for($i=0; $i < count($tickets); $i++) {
        // Если заголовок массив - преобразуем его в строку, обертывая каждый элемент в тег <span/>
        if ( is_array($tickets[$i]['title']) )
            $tickets[$i]['title'] = "<span>" . implode("</span> <span>", $tickets[$i]['title']) . "</span>";

        // Если прайслист - не массив - преобразуем в массив
        if ( !is_array($tickets[$i]['pricelist']) )
            $tickets[$i]['pricelist'] = array($tickets[$i]['pricelist']);

        // Если количество цен не совпадает с количеством периодов дат - добавляем в конец последние цены
        $diff = count($pricelistTitles) - count($tickets[$i]['pricelist']);
        if ( $diff > 0 ) {
            $price = $tickets[$i]['pricelist'][ count($tickets[$i]['pricelist']) - 1 ];
            for ($j=0; $j < $diff; $j++ ) {
                array_push($tickets[$i]['pricelist'], $price);
            }
        }

        // Сохраняем текущую цену
        array_push($pricelistCurrent, $tickets[$i]['pricelist'][$currentPricePos]);
    }
    $f3->set('tickets', $tickets);
    $f3->set('pricelist.current', $pricelistCurrent);
}


/**
 * Загрузить данные о бизнес-практикуме для руководителей
 */
function loadBusiness() {
	$f3 = Base::instance();

	// Загружаем и обрабатываем даты смены цен
	$pricelistDates = $f3->get('business.pricelist.dates');
	$pricelistTitles = array();
	$pricelistClasses = array();
	$currentPricePos = null;
	$timestamp = strtotime(date('Y-m-d'));
	for ($i=0; $i < count($pricelistDates); $i++) {
		$date = trim($pricelistDates[$i]);
		if ( $date === "+" && count($pricelistTitles) ) {
			$nextDay = date('Y-m-d', strtotime('+1 day', strtotime($pricelistDates[$i-1])));
			array_push($pricelistTitles, "с " . shortDateFormat( $nextDay ));
			if ( is_null($currentPricePos) ) {
				array_push($pricelistClasses, 'current');
				$currentPricePos = $i;
			}
		} else {
			array_push($pricelistTitles, "до " . shortDateFormat( $date ));
			$class = strtotime($date) < $timestamp ? "past" : "";
			if ($class === "" && !count($pricelistClasses) ) $class = "current";
			if ($class === "" && count($pricelistClasses) && $pricelistClasses[ count($pricelistClasses)-1 ] === "past" ) $class = "current";
			if ($class === "current") $currentPricePos = $i;
			array_push($pricelistClasses, $class);
		}
	}
	$f3->set('business.pricelist.titles', $pricelistTitles);
	$f3->set('business.pricelist.classes', $pricelistClasses);

	// Загружаемы и обрабратываем варианты участия
	$tickets = $f3->get('business.tickets');
	for($i=0; $i < count($tickets); $i++) {
		// Если фишки - не массив - преобразуем в массив
		if ( !is_array($tickets[$i]['features']) )
			$tickets[$i]['features'] = array($tickets[$i]['features']);

		// Если прайслист - не массив - преобразуем в массив
		if ( !is_array($tickets[$i]['pricelist']) )
			$tickets[$i]['pricelist'] = array($tickets[$i]['pricelist']);

		// Если количество цен не совпадает с количеством периодов дат - добавляем в конец последние цены
		$diff = count($pricelistTitles) - count($tickets[$i]['pricelist']);
		if ( $diff > 0 ) {
			$price = $tickets[$i]['pricelist'][ count($tickets[$i]['pricelist']) - 1 ];
			for ($j=0; $j < $diff; $j++ ) {
				array_push($tickets[$i]['pricelist'], $price);
			}
		}
	}
	$f3->set('business.tickets', $tickets);
}


/**
 * Загрузить данные из файлов
 */
function loadData() {
    $f3 = Base::instance();
    $lang = $f3->get('LANG');
    $folder = $f3->get('ROOT') . "/data/" . $lang . '/';

    /* Чтение научного совета */
    $data = loadFile( $folder . 'committee.txt' );
    $committee = array();
    foreach ($data as $item) {
        array_push($committee, array(
            'fullname' => $item[0],
            'image' => $item[1],
            'description' => $item[2]
        ));
    }
    $f3->set('committee', $committee);

    /* Чтение спикеров */
    $data = loadFile( $folder . 'speakers.txt' );
    $speakers = array();
    foreach ($data as $item) {
        $speaker = array(
            'fullname' => $item[0],
            'image' => $item[1],
            'description' => $item[2]
        );

        $urls = array_slice($item, 3);
        foreach( $urls as $url) {
            if ( strpos($url, 'twitter.com') !== false ) $speaker['twitter'] = $url;
            if ( strpos($url, 'facebook.com') !== false ) $speaker['facebook'] = $url;
            if ( strpos($url, 'instagram.com') !== false ) $speaker['instagram'] = $url;
        }

        array_push($speakers, $speaker);
    }

    $f3->set('speakers', $speakers);
}


/**
 * Чтение файла в массив
 * Элементы массива в файле должны быть разделены хотя бы одной пустой строкой
 * Элементы массива в свою очередь представляют собой массивы строк
 * @param $filename
 * @return array
 */
function loadFile( $filename ) {
    $file = file($filename);
    $result = array();
    $item = array();

    foreach ($file as $line) {
        $line = trim($line);
        if ($line === '') {
            if (count($item)) {
                array_push($result, $item);
                $item = array();
            }
        } else {
            array_push($item, $line);
        }
    }

    if (count($item)) array_push($result, $item);

    return $result;
}


/**
 * Загрузить экспонентов
 */
function loadExpo() {
    $f3 = Base::instance();
    $folder = $f3->get('ROOT') . '/expo/';

    foreach ( array('ictps', 'iectc') as $item ) {
        $files = glob($folder . $item . '/*.{jpg,jpeg,png}', GLOB_BRACE );
        for ($i=0; $i < count($files); $i++) {
            $files[$i] = "/expo/" . $item . "/" . pathinfo($files[$i], PATHINFO_BASENAME );
        }
        $f3->set('expo.' . $item, $files );
    }
}

/**
 * День недели
 * Поддерживает локали
 * @param $date
 * @return string
 */
function dof( $date ) {
    $dofs = array(
        'ru' => array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'),
        'en' => array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')
    );

    $f3 = Base::instance();
    $lang = $f3->get('LANG');
    $week = date('w', strtotime($date));
    return $dofs[$lang][$week];
}


/**
 * Формирует массив дат от начала до конца мероприятия
 * Наример, ['2017-04-01', '2017-04-02', '2017-04-03']
 * @return array
 */
function getEventsDates() {
    $f3 = Base::instance();
    $date1 = new DateTime($f3->get('course.start'));
    $date2 = new DateTime($f3->get('course.end'));
    $date2->modify('+1 day');

    $array = array();
    $period = new DatePeriod(
        $date1,
        new DateInterval('P1D'),
        $date2
    );
    foreach( $period as $date) { array_push($array, $date->format('Y-m-d')); }

    return $array;
}

/**
 * Текстовое представление месяца
 * Поддерживает локали
 * @param $month
 * @return string
 */
function month( $month ) {
    $months = array(
        'ru' => array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'),
        'en' => array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec')
    );

    $f3 = Base::instance();
    $lang = $f3->get('LANG');

    return $months[$lang][$month-1];
}


/**
 * Форматирование даты
 * Поддерживает локали
 * @param $date
 * @param string $space
 * @return string
 */
function dateFormat($date, $space="&nbsp;") {
    $date = explode("-", $date);
    $m = month( $date[1] );
    return $date[2] . $space . $m . $space . $date[0];
}


/**
 * Форматирование даты
 * Поддерживает локали
 * @param $date
 * @param string $space
 * @return string
 */
function shortDateFormat($date, $space="&nbsp;") {
    $date = explode("-", $date);
    $m = month( $date[1] );
    return $date[2] . $space . $m;
}


/**
 * Форматирование периода дат
 * Поддерживает локали
 * @param $date1
 * @param $date2
 * @param string $space
 * @return string
 */
function periodFormat($date1, $date2, $space=" ") {
    $date1a = explode("-", $date1);
    $date2a = explode("-", $date2);

    // Года совпадают
    if ( $date1a[0] === $date2a[0] ) {
        // Месяцы совпадают
        if ($date1a[1] === $date2a[1]) {
            if ($date1a[2] === $date2a[2]) return dateFormat($date1, $space);
            return $date1a[2] . $space . "&ndash;" . $space . $date2a[2] . $space . month($date2a[1]) . $space . $date1a[0];
        }
        // Месяцы разные
        else {
            return $date1a[2] . $space . month($date1a[1]) . $space . "&ndash;" . $space . $date2a[2] . $space . month($date2a[1]) . $space . $date1a[0];
        }
    }
    // Года разные
    else {
        return dateFormat($date1, $space) . $space . "&ndash;" . $space . dateFormat($date2, $space);
    }
}


/** Определить город по IP по всему миру (на анг. языке)
 * @param $ip
 * @return mixed
 */
function getGlobalLocation( $ip ) {
    $options['http']['timeout'] = 1;
    $context = stream_context_create($options);
    try {
        $result = @file_get_contents("http://ip-api.com/json/$ip", 0, $context);
    }
    catch (Exception $e) {
        $result = false;
    }
    return $result === false ? null : json_decode($result, true);
}


/** Определить город по IP по России (на русском языке)
 * @param $ip
 * @return mixed
 */
function getRussiaLocation( $ip ) {
    $options = array(
        'http' => array(
            'timeout' => 1,
            'method'  => 'GET',
            'header'  => array(
                'Content-type: application/json',
                'Authorization: Token fd3b196616a42864c5504ebef165f605c4597b49'
            )
        )
    );

    $context = stream_context_create($options);
    $result = @file_get_contents("https://dadata.ru/api/v2/detectAddressByIp?ip=".$ip, false, $context);
    return $result === false ? null : json_decode($result, true);
}


/**
 * Определить город по IP
 * @param $ip
 * @return string
 */
function getLocation( $ip ) {
    $local = getRussiaLocation( $ip );

    $result = "";
    $country = "";

    // Нашли в России IP-адрес
    if ( $local !== null && $local['location'] !== null ) {
        $city_type = $local['location']['data']['city_type']; // считываем тип поселения
        $city = $local['location']['data']['city']; // считываем название поселения
        // Если определили название
        if ( $city !== null ) {
            $result  = $city_type !== null ? $city_type." " : ""; // если тип известен - записываем
            $result .= $city; // добавляем название
            $country = "RU";
        }
    }

    // если ничего не нашли
    if ( $result === "" ) {
        $global = getGlobalLocation( $ip );
        if ( $global !== null && $global['status'] === 'success' ) {
            $country = $global['countryCode'];
            $result = $global['city'].", ".$global['country'];
        }
    }

    return $result . ($country ? ' (' . $country . ')' : "");
}


/**
 * Inline SVG
 * @param $icon
 * @return string
 */
function inline_svg( $icon ) {
    $file = Base::instance()->get('ROOT') . '/img/' . $icon . '.svg';
    if ( !file_exists($file) ) return '';
    return file_get_contents($file);
}


/**
 * Форматирование цены в рублях
 * @param $value
 */
function price( $value ) {
    $value = intval( $value );
    return number_format($value, 0, '.', '&nbsp;') . " <span class='rubles'>руб.</span>";
}



/**
 * Отправить E-mail
 * @param $to
 * @param $subject
 * @param $template
 * @param $data
 * @return bool
 * @throws Exception
 * @throws phpmailerException
 */
function sendEmail( $to, $subject, $data ) {
	$f3 = Base::instance();

	if ( intval($f3->get('DEBUG')) ) {
		return true;
	}

	$mail = new PHPMailer;
	$mail->setLanguage('ru');

	$mail->isSMTP();
	$mail->SMTPAuth = true;
	$mail->Host = 'smtp.yandex.ru';
	$mail->Port = 465;
	$mail->SMTPSecure = 'ssl';
	$mail->CharSet = 'UTF-8';

	$mail->Username = 'no-reply@medesse.com';
	$mail->Password = "41k#wGaiiT";
	$mail->From = 'no-reply@medesse.com';
	$mail->FromName = 'MEDESSE';
	$mail->isHTML(false);

	$mail->Subject = $subject;
	$mail->addAddress( $to );

	$mail->Body = implode("\r\n", $data);

	return $mail->send();
}
