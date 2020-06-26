<?php
///////////////////////////////////////////////////////////////////////////
// Version: HOSTER-4.4.3
// Created and developed by Greg Zemskov, Revisium Company
// Email: audit@revisium.com, http://revisium.com/ai/

// Commercial usage is not allowed without a license purchase or written permission of the author
// Source code and signatures usage is not allowed

// Certificated in Federal Institute of Industrial Property in 2012
// http://revisium.com/ai/i/mini_aibolit.jpg

////////////////////////////////////////////////////////////////////////////
// Запрещено использование скрипта в коммерческих целях без приобретения лицензии.
// Запрещено использование исходного кода скрипта и сигнатур.
//
// По вопросам приобретения лицензии обращайтесь в компанию "Ревизиум": http://www.revisium.com
// audit@revisium.com
// На скрипт получено авторское свидетельство в Роспатенте
// http://revisium.com/ai/i/mini_aibolit.jpg
///////////////////////////////////////////////////////////////////////////

ini_set('memory_limit', '1G');
ini_set('xdebug.max_nesting_level', 500);

$int_enc = @ini_get('mbstring.internal_encoding');

define('SHORT_PHP_TAG', strtolower(ini_get('short_open_tag')) == 'on' || strtolower(ini_get('short_open_tag')) == 1 ? true : false);

// Put any strong password to open the script from web
// Впишите вместо put_any_strong_password_here сложный пароль

define('PASS', '????????????????');

//////////////////////////////////////////////////////////////////////////
$vars = new Variables();

if (isCli()) {
    if (strpos('--eng', $argv[$argc - 1]) !== false) {
        define('LANG', 'EN');
    }
} else {
    if (PASS == '????????????????') {
        die('Forbidden');
    }

    define('NEED_REPORT', true);
}

if (!defined('LANG')) {
    define('LANG', 'RU');
}

// put 1 for expert mode, 0 for basic check and 2 for paranoid mode
// установите 1 для режима "Обычное сканирование", 0 для быстрой проверки и 2 для параноидальной проверки (диагностика при лечении сайтов)
define('AI_EXPERT_MODE', 2);

define('AI_HOSTER', 1);

define('CLOUD_ASSIST_LIMIT', 5000);

$defaults = array(
    'path'              => dirname(__FILE__),
    'scan_all_files'    => (AI_EXPERT_MODE == 2), // full scan (rather than just a .js, .php, .html, .htaccess)
    'scan_delay'        => 0, // delay in file scanning to reduce system load
    'max_size_to_scan'  => '650K',
    'max_size_to_cloudscan'  => '650K',
    'site_url'          => '', // website url
    'no_rw_dir'         => 0,
    'skip_ext'          => '',
    'skip_cache'        => false,
    'report_mask'       => JSONReport::REPORT_MASK_FULL,
);

define('DEBUG_MODE', 0);
define('DEBUG_PERFORMANCE', 0);

define('AIBOLIT_START_TIME', time());
define('START_TIME', microtime(true));

define('DIR_SEPARATOR', '/');

define('AIBOLIT_MAX_NUMBER', 200);

define('MAX_FILE_SIZE_FOR_CHECK', 268435456); //256Mb - The maximum possible file size for the initial checking

define('DOUBLECHECK_FILE', 'AI-BOLIT-DOUBLECHECK.php');

if ((isset($_SERVER['OS']) && stripos('Win', $_SERVER['OS']) !== false)) {
    define('DIR_SEPARATOR', '\\');
}

$g_SuspiciousFiles = array(
    'cgi',
    'pl',
    'o',
    'so',
    'py',
    'sh',
    'phtml',
    'php3',
    'php4',
    'php5',
    'php6',
    'php7',
    'pht',
    'shtml'
);
$g_SensitiveFiles  = array_merge(array(
    'php',
    'js',
    'json',
    'htaccess',
    'html',
    'htm',
    'tpl',
    'inc',
    'css',
    'txt',
    'sql',
    'ico',
    '',
    'susp',
    'suspected',
    'zip',
    'tar'
), $g_SuspiciousFiles);
$g_CriticalEntries = '^\s*<\?php|^\s*<\?=|^#!/usr|^#!/bin|\beval|assert|base64_decode|\bsystem|create_function|\bexec|\bpopen|\bfwrite|\bfputs|file_get_|call_user_func|file_put_|\$_REQUEST|ob_start|\$_GET|\$_POST|\$_SERVER|\$_FILES|\bmove|\bcopy|\barray_|reg_replace|\bmysql_|\bchr|fsockopen|\$GLOBALS|sqliteCreateFunction|EICAR-STANDARD-ANTIVIRUS-TEST-FILE';
$g_VirusFiles      = array(
    'js',
    'json',
    'html',
    'htm',
    'suspicious'
);
$g_VirusEntries    = '<script|<iframe|<object|<embed|fromCharCode|setTimeout|setInterval|location\.|document\.|window\.|navigator\.|\$(this)\.';
$g_PhishFiles      = array(
    'js',
    'html',
    'htm',
    'suspected',
    'php',
    'phtml',
    'pht',
    'php7'
);
$g_PhishEntries    = '<\s*title|<\s*html|<\s*form|<\s*body|bank|account';
$g_ShortListExt    = array(
    'php',
    'php3',
    'php4',
    'php5',
    'php7',
    'pht',
    'html',
    'htm',
    'phtml',
    'shtml',
    'khtml',
    '',
    'ico',
    'txt'
);

if (LANG == 'RU') {
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // RUSSIAN INTERFACE
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $msg1  = "\"Отображать по _MENU_ записей\"";
    $msg2  = "\"Ничего не найдено\"";
    $msg3  = "\"Отображается c _START_ по _END_ из _TOTAL_ файлов\"";
    $msg4  = "\"Нет файлов\"";
    $msg5  = "\"(всего записей _MAX_)\"";
    $msg6  = "\"Поиск:\"";
    $msg7  = "\"Первая\"";
    $msg8  = "\"Предыдущая\"";
    $msg9  = "\"Следующая\"";
    $msg10 = "\"Последняя\"";
    $msg11 = "\": активировать для сортировки столбца по возрастанию\"";
    $msg12 = "\": активировать для сортировки столбцов по убыванию\"";

    define('AI_STR_001', 'Отчет сканера <a href="https://revisium.com/ai/">AI-Bolit</a> v@@VERSION@@:');
    define('AI_STR_002', 'Обращаем внимание на то, что большинство CMS <b>без дополнительной защиты</b> рано или поздно <b>взламывают</b>.<p> Компания <a href="https://revisium.com/">"Ревизиум"</a> предлагает услугу превентивной защиты сайта от взлома с использованием уникальной <b>процедуры "цементирования сайта"</b>. Подробно на <a href="https://revisium.com/ru/client_protect/">странице услуги</a>. <p>Лучшее лечение &mdash; это профилактика.');
    define('AI_STR_003', 'Не оставляйте файл отчета на сервере, и не давайте на него прямых ссылок с других сайтов. Информация из отчета может быть использована злоумышленниками для взлома сайта, так как содержит информацию о настройках сервера, файлах и каталогах.');
    define('AI_STR_004', 'Путь');
    define('AI_STR_005', 'Изменение свойств');
    define('AI_STR_006', 'Изменение содержимого');
    define('AI_STR_007', 'Размер');
    define('AI_STR_008', 'Конфигурация PHP');
    define('AI_STR_009', "Вы установили слабый пароль на скрипт AI-BOLIT. Укажите пароль не менее 8 символов, содержащий латинские буквы в верхнем и нижнем регистре, а также цифры. Например, такой <b>%s</b>");
    define('AI_STR_010', "Сканер AI-Bolit запускается с паролем. Если это первый запуск сканера, вам нужно придумать сложный пароль и вписать его в файле ai-bolit.php в строке №34. <p>Например, <b>define('PASS', '%s');</b><p>
После этого откройте сканер в браузере, указав пароль в параметре \"p\". <p>Например, так <b>http://mysite.ru/ai-bolit.php?p=%s</b>. ");
    define('AI_STR_011', 'Текущая директория не доступна для чтения скрипту. Пожалуйста, укажите права на доступ <b>rwxr-xr-x</b> или с помощью командной строки <b>chmod +r имя_директории</b>');
    define('AI_STR_012', "Затрачено времени: <b>%s</b>. Сканирование начато %s, сканирование завершено %s");
    define('AI_STR_013', 'Всего проверено %s директорий и %s файлов.');
    define('AI_STR_014', '<div class="rep" style="color: #0000A0">Внимание, скрипт выполнил быструю проверку сайта. Проверяются только наиболее критические файлы, но часть вредоносных скриптов может быть не обнаружена. Пожалуйста, запустите скрипт из командной строки для выполнения полного тестирования. Подробнее смотрите в <a href="https://revisium.com/ai/faq.php">FAQ вопрос №10</a>.</div>');
    define('AI_STR_015', '<div class="title">Критические замечания</div>');
    define('AI_STR_016', 'Эти файлы могут быть вредоносными или хакерскими скриптами');
    define('AI_STR_017', 'Вирусы и вредоносные скрипты не обнаружены.');
    define('AI_STR_018', 'Эти файлы могут быть javascript вирусами');
    define('AI_STR_019', 'Обнаружены сигнатуры исполняемых файлов unix и нехарактерных скриптов. Они могут быть вредоносными файлами');
    define('AI_STR_020', 'Двойное расширение, зашифрованный контент или подозрение на вредоносный скрипт. Требуется дополнительный анализ');
    define('AI_STR_021', 'Подозрение на вредоносный скрипт');
    define('AI_STR_022', 'Символические ссылки (symlinks)');
    define('AI_STR_023', 'Скрытые файлы');
    define('AI_STR_024', 'Возможно, каталог с дорвеем');
    define('AI_STR_025', 'Не найдено директорий c дорвеями');
    define('AI_STR_026', 'Предупреждения');
    define('AI_STR_027', 'Подозрение на мобильный редирект, подмену расширений или автовнедрение кода');
    define('AI_STR_028', 'В не .php файле содержится стартовая сигнатура PHP кода. Возможно, там вредоносный код');
    define('AI_STR_029', 'Дорвеи, реклама, спам-ссылки, редиректы');
    define('AI_STR_030', 'Непроверенные файлы - ошибка чтения');
    define('AI_STR_031', 'Невидимые ссылки. Подозрение на ссылочный спам');
    define('AI_STR_032', 'Невидимые ссылки');
    define('AI_STR_033', 'Отображены только первые ');
    define('AI_STR_034', 'Подозрение на дорвей');
    define('AI_STR_035', 'Скрипт использует код, который часто встречается во вредоносных скриптах');
    define('AI_STR_036', 'Директории из файла .adirignore были пропущены при сканировании');
    define('AI_STR_037', 'Версии найденных CMS');
    define('AI_STR_038', 'Большие файлы (больше чем %s). Пропущено');
    define('AI_STR_039', 'Не найдено файлов больше чем %s');
    define('AI_STR_040', 'Временные файлы или файлы(каталоги) - кандидаты на удаление по ряду причин');
    define('AI_STR_041', 'Потенциально небезопасно! Директории, доступные скрипту на запись');
    define('AI_STR_042', 'Не найдено директорий, доступных на запись скриптом');
    define('AI_STR_043', 'Использовано памяти при сканировании: ');
    define('AI_STR_044', 'Просканированы только файлы, перечисленные в ' . DOUBLECHECK_FILE . '. Для полного сканирования удалите файл ' . DOUBLECHECK_FILE . ' и запустите сканер повторно.');
    define('AI_STR_045', '<div class="rep">Внимание! Выполнена экспресс-проверка сайта. Просканированы только файлы с расширением .php, .js, .html, .htaccess. В этом режиме могут быть пропущены вирусы и хакерские скрипты в файлах с другими расширениями. Чтобы выполнить более тщательное сканирование, поменяйте значение настройки на <b>\'scan_all_files\' => 1</b> в строке 50 или откройте сканер в браузере с параметром full: <b><a href="ai-bolit.php?p=' . PASS . '&full">ai-bolit.php?p=' . PASS . '&full</a></b>. <p>Не забудьте перед повторным запуском удалить файл ' . DOUBLECHECK_FILE . '</div>');
    define('AI_STR_050', 'Замечания и предложения по работе скрипта и не обнаруженные вредоносные скрипты присылайте на <a href="mailto:ai@revisium.com">ai@revisium.com</a>.<p>Также будем чрезвычайно благодарны за любые упоминания скрипта AI-Bolit на вашем сайте, в блоге, среди друзей, знакомых и клиентов. Ссылочку можно поставить на <a href="https://revisium.com/ai/">https://revisium.com/ai/</a>. <p>Если будут вопросы - пишите <a href="mailto:ai@revisium.com">ai@revisium.com</a>. ');
    define('AI_STR_051', 'Отчет по ');
    define('AI_STR_052', 'Эвристический анализ обнаружил подозрительные файлы. Проверьте их на наличие вредоносного кода.');
    define('AI_STR_053', 'Много косвенных вызовов функции');
    define('AI_STR_054', 'Подозрение на обфусцированные переменные');
    define('AI_STR_055', 'Подозрительное использование массива глобальных переменных');
    define('AI_STR_056', 'Дробление строки на символы');
    define('AI_STR_057', 'Сканирование выполнено в экспресс-режиме. Многие вредоносные скрипты могут быть не обнаружены.<br> Рекомендуем проверить сайт в режиме "Эксперт" или "Параноидальный". Подробно описано в <a href="https://revisium.com/ai/faq.php">FAQ</a> и инструкции к скрипту.');
    define('AI_STR_058', 'Обнаружены фишинговые страницы');
    define('AI_STR_059', 'Мобильных редиректов');
    define('AI_STR_060', 'Вредоносных скриптов');
    define('AI_STR_061', 'JS Вирусов');
    define('AI_STR_062', 'Фишинговых страниц');
    define('AI_STR_063', 'Исполняемых файлов');
    define('AI_STR_064', 'IFRAME вставок');
    define('AI_STR_065', 'Пропущенных больших файлов');
    define('AI_STR_066', 'Ошибок чтения файлов');
    define('AI_STR_067', 'Зашифрованных файлов');
    define('AI_STR_068', 'Подозрительных');
    define('AI_STR_069', 'Символических ссылок');
    define('AI_STR_070', 'Скрытых файлов');
    define('AI_STR_072', 'Рекламных ссылок и кодов');
    define('AI_STR_073', 'Пустых ссылок');
    define('AI_STR_074', 'Сводный отчет');

    define('AI_STR_075', 'Сканер бесплатный только для личного некоммерческого использования. Информация по <a href="https://revisium.com/ai/faq.php#faq11" target=_blank>коммерческой лицензии</a> (пункт №11). <a href="https://revisium.com/images/mini_aibolit.jpg">Авторское свидетельство</a> о гос. регистрации в РосПатенте №2012619254 от 12 октября 2012 г.');

    $tmp_str = <<<HTML_FOOTER
   <div class="disclaimer"><span class="vir">[!]</span> Отказ от гарантий: невозможно гарантировать обнаружение всех вредоносных скриптов. Поэтому разработчик сканера не несет ответственности за возможные последствия работы сканера AI-Bolit или неоправданные ожидания пользователей относительно функциональности и возможностей.
   </div>
   <div class="thanx">
      Замечания и предложения по работе скрипта, а также не обнаруженные вредоносные скрипты вы можете присылать на <a href="mailto:ai@revisium.com">ai@revisium.com</a>.<br/>
      Также будем чрезвычайно благодарны за любые упоминания сканера AI-Bolit на вашем сайте, в блоге, среди друзей, знакомых и клиентов. <br/>Ссылку можно поставить на страницу <a href="https://revisium.com/ai/">https://revisium.com/ai/</a>.<br/> 
     <p>Получить консультацию или задать вопросы можно по email <a href="mailto:ai@revisium.com">ai@revisium.com</a>.</p> 
    </div>
HTML_FOOTER;

    define('AI_STR_076', $tmp_str);
    define('AI_STR_077', "Подозрительные параметры времени изменения файла");
    define('AI_STR_078', "Подозрительные атрибуты файла");
    define('AI_STR_079', "Подозрительное местоположение файла");
    define('AI_STR_080', "Обращаем внимание, что обнаруженные файлы не всегда являются вирусами и хакерскими скриптами. Сканер минимизирует число ложных обнаружений, но это не всегда возможно, так как найденный фрагмент может встречаться как во вредоносных скриптах, так и в обычных.<p>Для диагностического сканирования без ложных срабатываний мы разработали специальную версию <u><a href=\"https://revisium.com/ru/blog/ai-bolit-4-ISP.html\" target=_blank style=\"background: none; color: #303030\">сканера для хостинг-компаний</a></u>.");
    define('AI_STR_081', "Уязвимости в скриптах");
    define('AI_STR_082', "Добавленные файлы");
    define('AI_STR_083', "Измененные файлы");
    define('AI_STR_084', "Удаленные файлы");
    define('AI_STR_085', "Добавленные каталоги");
    define('AI_STR_086', "Удаленные каталоги");
    define('AI_STR_087', "Изменения в файловой структуре");

    $l_Offer = <<<OFFER
    <div>
     <div class="crit" style="font-size: 17px; margin-bottom: 20px"><b>Внимание! Наш сканер обнаружил подозрительный или вредоносный код</b>.</div> 
     <p>Возможно, ваш сайт был взломан. Рекомендуем срочно <a href="https://revisium.com/ru/order/#fform" target=_blank>проконсультироваться со специалистами</a> по данному отчету.</p>
     <p><hr size=1></p>
     <p>Рекомендуем также проверить сайт бесплатным <b><a href="https://rescan.pro/?utm=aibolit" target=_blank>онлайн-сканером ReScan.Pro</a></b>.</p>
     <p><hr size=1></p>
         <div class="caution">@@CAUTION@@</div>
    </div>
OFFER;

    $l_Offer2 = <<<OFFER2
       <b>Наши продукты:</b><br/>
              <ul>
               <li style="margin-top: 10px"><font color=red><sup>[new]</sup></font><b><a href="https://revisium.com/ru/products/antivirus_for_ispmanager/" target=_blank>Антивирус для ISPmanager Lite</a></b> &mdash;  сканирование и лечение сайтов прямо в панели хостинга</li>
               <li style="margin-top: 10px"><b><a href="https://revisium.com/ru/blog/revisium-antivirus-for-plesk.html" target=_blank>Антивирус для Plesk</a> Onyx 17.x</b> &mdash;  сканирование и лечение сайтов прямо в панели хостинга</li>
               <li style="margin-top: 10px"><b><a href="https://cloudscan.pro/ru/" target=_blank>Облачный антивирус CloudScan.Pro</a> для веб-специалистов</b> &mdash; лечение сайтов в один клик</li>
               <li style="margin-top: 10px"><b><a href="https://revisium.com/ru/antivirus-server/" target=_blank>Антивирус для сервера</a></b> &mdash; для хостинг-компаний, веб-студий и агентств.</li>
              </ul>  
    </div>
OFFER2;

} else {
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ENGLISH INTERFACE
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $msg1  = "\"Display _MENU_ records\"";
    $msg2  = "\"Not found\"";
    $msg3  = "\"Display from _START_ to _END_ of _TOTAL_ files\"";
    $msg4  = "\"No files\"";
    $msg5  = "\"(total _MAX_)\"";
    $msg6  = "\"Filter/Search:\"";
    $msg7  = "\"First\"";
    $msg8  = "\"Previous\"";
    $msg9  = "\"Next\"";
    $msg10 = "\"Last\"";
    $msg11 = "\": activate to sort row ascending order\"";
    $msg12 = "\": activate to sort row descending order\"";

    define('AI_STR_001', 'AI-Bolit v@@VERSION@@ Scan Report:');
    define('AI_STR_002', '');
    define('AI_STR_003', 'Caution! Do not leave either ai-bolit.php or report file on server and do not provide direct links to the report file. Report file contains sensitive information about your website which could be used by hackers. So keep it in safe place and don\'t leave on website!');
    define('AI_STR_004', 'Path');
    define('AI_STR_005', 'iNode Changed');
    define('AI_STR_006', 'Modified');
    define('AI_STR_007', 'Size');
    define('AI_STR_008', 'PHP Info');
    define('AI_STR_009', "Your password for AI-BOLIT is too weak. Password must be more than 8 character length, contain both latin letters in upper and lower case, and digits. E.g. <b>%s</b>");
    define('AI_STR_010', "Open AI-BOLIT with password specified in the beggining of file in PASS variable. <br/>E.g. http://you_website.com/ai-bolit.php?p=<b>%s</b>");
    define('AI_STR_011', 'Current folder is not readable. Please change permission for <b>rwxr-xr-x</b> or using command line <b>chmod +r folder_name</b>');
    define('AI_STR_012', "<div class=\"rep\">%s malicious signatures known, %s virus signatures and other malicious code. Elapsed: <b>%s</b
>.<br/>Started: %s. Stopped: %s</div> ");
    define('AI_STR_013', 'Scanned %s folders and %s files.');
    define('AI_STR_014', '<div class="rep" style="color: #0000A0">Attention! Script has performed quick scan. It scans only .html/.js/.php files  in quick scan mode so some of malicious scripts might not be detected. <br>Please launch script from a command line thru SSH to perform full scan.');
    define('AI_STR_015', '<div class="title">Critical</div>');
    define('AI_STR_016', 'Shell script signatures detected. Might be a malicious or hacker\'s scripts');
    define('AI_STR_017', 'Shell scripts signatures not detected.');
    define('AI_STR_018', 'Javascript virus signatures detected:');
    define('AI_STR_019', 'Unix executables signatures and odd scripts detected. They might be a malicious binaries or rootkits:');
    define('AI_STR_020', 'Suspicious encoded strings, extra .php extention or external includes detected in PHP files. Might be a malicious or hacker\'s script:');
    define('AI_STR_021', 'Might be a malicious or hacker\'s script:');
    define('AI_STR_022', 'Symlinks:');
    define('AI_STR_023', 'Hidden files:');
    define('AI_STR_024', 'Files might be a part of doorway:');
    define('AI_STR_025', 'Doorway folders not detected');
    define('AI_STR_026', 'Warnings');
    define('AI_STR_027', 'Malicious code in .htaccess (redirect to external server, extention handler replacement or malicious code auto-append):');
    define('AI_STR_028', 'Non-PHP file has PHP signature. Check for malicious code:');
    define('AI_STR_029', 'This script has black-SEO links or linkfarm. Check if it was installed by yourself:');
    define('AI_STR_030', 'Reading error. Skipped.');
    define('AI_STR_031', 'These files have invisible links, might be black-seo stuff:');
    define('AI_STR_032', 'List of invisible links:');
    define('AI_STR_033', 'Displayed first ');
    define('AI_STR_034', 'Folders contained too many .php or .html files. Might be a doorway:');
    define('AI_STR_035', 'Suspicious code detected. It\'s usually used in malicious scrips:');
    define('AI_STR_036', 'The following list of files specified in .adirignore has been skipped:');
    define('AI_STR_037', 'CMS found:');
    define('AI_STR_038', 'Large files (greater than %s! Skipped:');
    define('AI_STR_039', 'Files greater than %s not found');
    define('AI_STR_040', 'Files recommended to be remove due to security reason:');
    define('AI_STR_041', 'Potentially unsafe! Folders which are writable for scripts:');
    define('AI_STR_042', 'Writable folders not found');
    define('AI_STR_043', 'Memory used: ');
    define('AI_STR_044', 'Quick scan through the files from ' . DOUBLECHECK_FILE . '. For full scan remove ' . DOUBLECHECK_FILE . ' and launch scanner once again.');
    define('AI_STR_045', '<div class="notice"><span class="vir">[!]</span> Ai-BOLIT is working in quick scan mode, only .php, .html, .htaccess files will be checked. Change the following setting \'scan_all_files\' => 1 to perform full scanning.</b>. </div>');
    define('AI_STR_050', "I'm sincerely appreciate reports for any bugs you may found in the script. Please email me: <a href=\"mailto:audit@revisium.com\">audit@revisium.com</a>.<p> Also I appriciate any reference to the script in your blog or forum posts. Thank you for the link to download page: <a href=\"https://revisium.com/aibo/\">https://revisium.com/aibo/</a>");
    define('AI_STR_051', 'Report for ');
    define('AI_STR_052', 'Heuristic Analyzer has detected suspicious files. Check if they are malware.');
    define('AI_STR_053', 'Function called by reference');
    define('AI_STR_054', 'Suspected for obfuscated variables');
    define('AI_STR_055', 'Suspected for $GLOBAL array usage');
    define('AI_STR_056', 'Abnormal split of string');
    define('AI_STR_057', 'Scanning has been done in simple mode. It is strongly recommended to perform scanning in "Expert" mode. See readme.txt for details.');
    define('AI_STR_058', 'Phishing pages detected:');

    define('AI_STR_059', 'Mobile redirects');
    define('AI_STR_060', 'Malware');
    define('AI_STR_061', 'JS viruses');
    define('AI_STR_062', 'Phishing pages');
    define('AI_STR_063', 'Unix executables');
    define('AI_STR_064', 'IFRAME injections');
    define('AI_STR_065', 'Skipped big files');
    define('AI_STR_066', 'Reading errors');
    define('AI_STR_067', 'Encrypted files');
    define('AI_STR_068', 'Suspicious');
    define('AI_STR_069', 'Symbolic links');
    define('AI_STR_070', 'Hidden files');
    define('AI_STR_072', 'Adware and spam links');
    define('AI_STR_073', 'Empty links');
    define('AI_STR_074', 'Summary');
    define('AI_STR_075', 'For non-commercial use only. In order to purchase the commercial license of the scanner contact us at ai@revisium.com');

    $tmp_str = <<<HTML_FOOTER
           <div class="disclaimer"><span class="vir">[!]</span> Disclaimer: We're not liable to you for any damages, including general, special, incidental or consequential damages arising out of the use or inability to use the script (including but not limited to loss of data or report being rendered inaccurate or failure of the script). There's no warranty for the program. Use at your own risk. 
           </div>
           <div class="thanx">
              We're greatly appreciate for any references in the social medias, forums or blogs to our scanner AI-BOLIT <a href="https://revisium.com/aibo/">https://revisium.com/aibo/</a>.<br/> 
             <p>Contact us via email if you have any questions regarding the scanner or need report analysis: <a href="mailto:ai@revisium.com">ai@revisium.com</a>.</p> 
            </div>
HTML_FOOTER;
    define('AI_STR_076', $tmp_str);
    define('AI_STR_077', "Suspicious file mtime and ctime");
    define('AI_STR_078', "Suspicious file permissions");
    define('AI_STR_079', "Suspicious file location");
    define('AI_STR_081', "Vulnerable Scripts");
    define('AI_STR_082', "Added files");
    define('AI_STR_083', "Modified files");
    define('AI_STR_084', "Deleted files");
    define('AI_STR_085', "Added directories");
    define('AI_STR_086', "Deleted directories");
    define('AI_STR_087', "Integrity Check Report");

    $l_Offer = <<<HTML_OFFER_EN
<div>
 <div class="crit" style="font-size: 17px;"><b>Attention! The scanner has detected suspicious or malicious files.</b></div> 
 <br/>Most likely the website has been compromised. Please, <a href="https://revisium.com/en/contacts/" target=_blank>contact web security experts</a> from Revisium to check the report or clean the malware.
 <p><hr size=1></p>
 Also check your website for viruses with our free <b><a href="http://rescan.pro/?en&utm=aibo" target=_blank>online scanner ReScan.Pro</a></b>.
</div>
<br/>
<div>
   Revisium contacts: <a href="mailto:ai@revisium.com">ai@revisium.com</a>, <a href="https://revisium.com/en/contacts/">https://revisium.com/en/home/</a>
</div>
<div class="caution">@@CAUTION@@</div>
HTML_OFFER_EN;

    $l_Offer2 = '<b>Special Offers:</b><br/>
              <ul>
               <li style="margin-top: 10px"><font color=red><sup>[new]</sup></font><b><a href="http://ext.plesk.com/packages/b71916cf-614e-4b11-9644-a5fe82060aaf-revisium-antivirus">Antivirus for Plesk Onyx</a></b> hosting panel with one-click malware cleanup and scheduled website scanning.</li>
               <li style="margin-top: 10px"><font color=red></font><b><a href="https://www.ispsystem.com/addons-modules/revisium">Antivirus for ISPmanager Lite</a></b> hosting panel with one-click malware cleanup and scheduled website scanning.</li>
               <li style="margin-top: 10px">Professional malware cleanup and web-protection service with 6 month guarantee for only $99 (one-time payment): <a href="https://revisium.com/en/home/#order_form">https://revisium.com/en/home/</a>.</li>
              </ul>  
    </div>';

    define('AI_STR_080', "Notice! Some of detected files may not contain malicious code. Scanner tries to minimize a number of false positives, but sometimes it's impossible, because same piece of code may be used either in malware or in normal scripts.");
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$l_Template = <<<MAIN_PAGE
<html>
<head>
<!-- revisium.com/ai/ -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<title>@@HEAD_TITLE@@</title>
<style type="text/css" title="currentStyle">
    @import "https://cdn.revisium.com/ai/media/css/demo_page2.css";
    @import "https://cdn.revisium.com/ai/media/css/jquery.dataTables2.css";
</style>

<script type="text/javascript" language="javascript" src="https://cdn.revisium.com/ai/jquery.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.revisium.com/ai/datatables.min.js"></script>

<style type="text/css">
 body 
 {
   font-family: Tahoma, sans-serif;
   color: #5a5a5a;
   background: #FFFFFF;
   font-size: 14px;
   margin: 20px;
   padding: 0;
 }

.header
 {
   font-size: 34px;
   margin: 0 0 10px 0;
 }

 .hidd
 {
    display: none;
 }
 
 .ok
 {
    color: green;
 }
 
 .line_no
 {
   -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   background: #DAF2C1;
   padding: 2px 5px 2px 5px;
   margin: 0 5px 0 5px;
 }
 
 .credits_header 
 {
  -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   background: #F2F2F2;
   padding: 10px;
   font-size: 11px;
    margin: 0 0 10px 0;
 }
 
 .marker
 {
    color: #FF0090;
    font-weight: 100;
    background: #FF0090;
    padding: 2px 0 2px 0;
    width: 2px;
 }
 
 .title
 {
   font-size: 24px;
   margin: 20px 0 10px 0;
   color: #9CA9D1;
}

.summary 
{
  float: left;
  width: 500px;
}

.summary TD
{
  font-size: 12px;
  border-bottom: 1px solid #F0F0F0;
  font-weight: 700;
  padding: 10px 0 10px 0;
}
 
.crit, .vir
{
  color: #D84B55;
}

.intitem
{
  color:#4a6975;
}

.spacer
{
   margin: 0 0 50px 0;
   clear:both;
}

.warn
{
  color: #F6B700;
}

.clear
{
   clear: both;
}

.offer
{
  -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   width: 500px;
   background: #F2F2F2;
   color: #747474;
   font-family: Helvetica, Arial, sans-serif;
   padding: 30px;
   margin: 20px 0 0 550px;
   font-size: 14px;
}

.offer2
{
  -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   width: 500px;
   background: #f6f5e0;
   color: #747474;
   font-family: Helvetica, Arial, sans-serif;
   padding: 30px;
   margin: 20px 0 0 550px;
   font-size: 14px;
}


HR {
  margin-top: 15px;
  margin-bottom: 15px;
  opacity: .2;
}
 
.flist
{
   font-family: Henvetica, Arial, sans-serif;
}

.flist TD
{
   font-size: 11px;
   padding: 5px;
}

.flist TH
{
   font-size: 12px;
   height: 30px;
   padding: 5px;
   background: #CEE9EF;
}


.it
{
   font-size: 14px;
   font-weight: 100;
   margin-top: 10px;
}

.crit .it A {
   color: #E50931; 
   line-height: 25px;
   text-decoration: none;
}

.warn .it A {
   color: #F2C900; 
   line-height: 25px;
   text-decoration: none;
}



.details
{
   font-family: Calibri, sans-serif;
   font-size: 12px;
   margin: 10px 10px 10px 0;
}

.crit .details
{
   color: #A08080;
}

.warn .details
{
   color: #808080;
}

.details A
{
  color: #FFF;
  font-weight: 700;
  text-decoration: none;
  padding: 2px;
  background: #E5CEDE;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;
}

.details A:hover
{
   background: #A0909B;
}

.ctd
{
   margin: 10px 0 10px 0;
   align:center;
}

.ctd A 
{
   color: #0D9922;
}

.disclaimer
{
   color: darkgreen;
   margin: 10px 10px 10px 0;
}

.note_vir
{
   margin: 10px 0 10px 0;
   //padding: 10px;
   color: #FF4F4F;
   font-size: 15px;
   font-weight: 700;
   clear:both;
  
}

.note_warn
{
   margin: 10px 0 10px 0;
   color: #F6B700;
   font-size: 15px;
   font-weight: 700;
   clear:both;
}

.note_int
{
   margin: 10px 0 10px 0;
   color: #60b5d6;
   font-size: 15px;
   font-weight: 700;
   clear:both;
}

.updateinfo
{
  color: #FFF;
  text-decoration: none;
  background: #E5CEDE;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;

  margin: 10px 0 10px 0;   
  padding: 10px;
}


.caution
{
  color: #EF7B75;
  text-decoration: none;
  margin: 20px 0 0 0;   
  font-size: 12px;
}

.footer
{
  color: #303030;
  text-decoration: none;
  background: #F4F4F4;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;

  margin: 80px 0 10px 0px;   
  padding: 10px;
}

.rep
{
  color: #303030;
  text-decoration: none;
  background: #94DDDB;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;

  margin: 10px 0 10px 0;   
  padding: 10px;
  font-size: 12px;
}

</style>

</head>
<body>

<div class="header">@@MAIN_TITLE@@ @@PATH_URL@@ (@@MODE@@)</div>
<div class="credits_header">@@CREDITS@@</div>
<div class="details_header">
   @@STAT@@<br/>
   @@SCANNED@@ @@MEMORY@@.
 </div>

 @@WARN_QUICK@@
 
 <div class="summary">
@@SUMMARY@@
 </div>
 
 <div class="offer">
@@OFFER@@
 </div>

 <div class="offer2">
@@OFFER2@@
 </div> 
 
 <div class="clear"></div>
 
 @@MAIN_CONTENT@@
 
    <div class="footer">
    @@FOOTER@@
    </div>
    
<script language="javascript">

function hsig(id) {
  var divs = document.getElementsByTagName("tr");
  for(var i = 0; i < divs.length; i++){
     
     if (divs[i].getAttribute('o') == id) {
        divs[i].innerHTML = '';
     }
  }

  return false;
}


$(document).ready(function(){
    $('#table_crit').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
        "paging": true,
       "iDisplayLength": 500,
        "oLanguage": {
            "sLengthMenu": $msg1,
            "sZeroRecords": $msg2,
            "sInfo": $msg3,
            "sInfoEmpty": $msg4,
            "sInfoFiltered": $msg5,
            "sSearch":       $msg6,
            "sUrl":          "",
            "oPaginate": {
                "sFirst": $msg7,
                "sPrevious": $msg8,
                "sNext": $msg9,
                "sLast": $msg10
            },
            "oAria": {
                "sSortAscending": $msg11,
                "sSortDescending": $msg12   
            }
        }

     } );

});

$(document).ready(function(){
    $('#table_vir').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
        "paging": true,
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
       "iDisplayLength": 500,
        "oLanguage": {
            "sLengthMenu": $msg1,
            "sZeroRecords": $msg2,
            "sInfo": $msg3,
            "sInfoEmpty": $msg4,
            "sInfoFiltered": $msg5,
            "sSearch":       $msg6,
            "sUrl":          "",
            "oPaginate": {
                "sFirst": $msg7,
                "sPrevious": $msg8,
                "sNext": $msg9,
                "sLast": $msg10
            },
            "oAria": {
                "sSortAscending":  $msg11,
                "sSortDescending": $msg12   
            }
        },

     } );

});

if ($('#table_warn0')) {
    $('#table_warn0').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
        "paging": true,
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
                     "iDisplayLength": 500,
                    "oLanguage": {
                        "sLengthMenu": $msg1,
                        "sZeroRecords": $msg2,
                        "sInfo": $msg3,
                        "sInfoEmpty": $msg4,
                        "sInfoFiltered": $msg5,
                        "sSearch":       $msg6,
                        "sUrl":          "",
                        "oPaginate": {
                            "sFirst": $msg7,
                            "sPrevious": $msg8,
                            "sNext": $msg9,
                            "sLast": $msg10
                        },
                        "oAria": {
                            "sSortAscending":  $msg11,
                            "sSortDescending": $msg12   
                        }
        }

     } );
}

if ($('#table_warn1')) {
    $('#table_warn1').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
        "paging": true,
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
                     "iDisplayLength": 500,
                    "oLanguage": {
                        "sLengthMenu": $msg1,
                        "sZeroRecords": $msg2,
                        "sInfo": $msg3,
                        "sInfoEmpty": $msg4,
                        "sInfoFiltered": $msg5,
                        "sSearch":       $msg6,
                        "sUrl":          "",
                        "oPaginate": {
                            "sFirst": $msg7,
                            "sPrevious": $msg8,
                            "sNext": $msg9,
                            "sLast": $msg10
                        },
                        "oAria": {
                            "sSortAscending":  $msg11,
                            "sSortDescending": $msg12   
                        }
        }

     } );
}


</script>
<!-- @@SERVICE_INFO@@  -->
 </body>
</html>
MAIN_PAGE;

$g_AiBolitAbsolutePath = dirname(__FILE__);

if (file_exists($g_AiBolitAbsolutePath . '/ai-design.html')) {
    $l_Template = file_get_contents($g_AiBolitAbsolutePath . '/ai-design.html');
}

$l_Template = str_replace('@@MAIN_TITLE@@', AI_STR_001, $l_Template);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$g_Mnemo = array();
$db_location = 'internal';

//BEGIN_SIG 26/11/2019 03:40:20
$g_DBShe = unserialize(gzinflate(/*1574772020*/base64_decode("S7QysKquBQA=")));
$gX_DBShe = unserialize(gzinflate(/*1574772020*/base64_decode("S7QysKquBQA=")));
$g_FlexDBShe = unserialize(gzinflate(/*1574772020*/base64_decode("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")));
$gX_FlexDBShe = unserialize(gzinflate(/*1574772020*/base64_decode("S7QysKquBQA=")));
$gXX_FlexDBShe = unserialize(gzinflate(/*1574772020*/base64_decode("S7QysKquBQA=")));
$g_ExceptFlex = unserialize(gzinflate(/*1574772020*/base64_decode("S7QysKquBQA=")));
$g_AdwareSig = unserialize(gzinflate(/*1574772020*/base64_decode("S7QysKquBQA=")));
$g_PhishingSig = unserialize(gzinflate(/*1574772020*/base64_decode("S7QysKquBQA=")));
$g_JSVirSig = unserialize(gzinflate(/*1574772020*/base64_decode("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")));
$gX_JSVirSig = unserialize(gzinflate(/*1574772020*/base64_decode("S7QysKquBQA=")));
$g_SusDB = unserialize(gzinflate(/*1574772020*/base64_decode("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")));
$g_SusDBPrio = unserialize(gzinflate(/*1574772020*/base64_decode("S7QysKquBQA=")));
$g_Mnemo = @array_flip(unserialize(gzinflate(/*1574772020*/base64_decode("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"))));
$g_DeMapper = unserialize(base64_decode("YTo1OntzOjEwOiJ3aXphcmQucGhwIjtzOjM3OiJjbGFzcyBXZWxjb21lU3RlcCBleHRlbmRzIENXaXphcmRTdGVwIjtzOjE3OiJ1cGRhdGVfY2xpZW50LnBocCI7czozNzoieyBDVXBkYXRlQ2xpZW50OjpBZGRNZXNzYWdlMkxvZygiZXhlYyI7czoxMToiaW5jbHVkZS5waHAiO3M6NDg6IkdMT0JBTFNbIlVTRVIiXS0+SXNBdXRob3JpemVkKCkgJiYgJGFyQXV0aFJlc3VsdCI7czo5OiJzdGFydC5waHAiO3M6NjA6IkJYX1JPT1QuJy9tb2R1bGVzL21haW4vY2xhc3Nlcy9nZW5lcmFsL3VwZGF0ZV9kYl91cGRhdGVyLnBocCI7czoxMDoiaGVscGVyLnBocCI7czo1ODoiSlBsdWdpbkhlbHBlcjo6Z2V0UGx1Z2luKCJzeXN0ZW0iLCJvbmVjbGlja2NoZWNrb3V0X3ZtMyIpOyI7fQ=="));
$db_meta_info = unserialize(base64_decode("YTozOntzOjEwOiJidWlsZC1kYXRlIjtzOjEwOiIxNTc0NzcxOTU0IjtzOjc6InZlcnNpb24iO3M6MTM6IjIwMTkxMTI2LTE0NDYiO3M6MTI6InJlbGVhc2UtdHlwZSI7czoxMDoicHJvZHVjdGlvbiI7fQ=="));

//END_SIG
////////////////////////////////////////////////////////////////////////////
if (!isCli() && !isset($_SERVER['HTTP_USER_AGENT'])) {
    echo "#####################################################\n";
    echo "# Error: cannot run on php-cgi. Requires php as cli #\n";
    echo "#                                                   #\n";
    echo "# See FAQ: http://revisium.com/ai/faq.php           #\n";
    echo "#####################################################\n";
    exit;
}


if (version_compare(phpversion(), '5.3.1', '<')) {
    echo "#####################################################\n";
    echo "# Warning: PHP Version < 5.3.1                      #\n";
    echo "# Some function might not work properly             #\n";
    echo "# See FAQ: http://revisium.com/ai/faq.php           #\n";
    echo "#####################################################\n";
    exit;
}

if (!(function_exists("file_put_contents") && is_callable("file_put_contents"))) {
    echo "#####################################################\n";
    echo "file_put_contents() is disabled. Cannot proceed.\n";
    echo "#####################################################\n";
    exit;
}

define('AI_VERSION', 'HOSTER-4.4.3');

////////////////////////////////////////////////////////////////////////////

$l_Res = '';

$g_SpecificExt = false;

$g_UpdatedJsonLog      = 0;
$g_FileInfo            = array();
$g_Iframer             = array();
$g_PHPCodeInside       = array();
$g_Base64              = array();
$g_HeuristicDetected   = array();
$g_HeuristicType       = array();
$g_UnixExec            = array();
$g_UnsafeFilesFound    = array();
$g_HiddenFiles         = array();

$g_RegExpStat = array();


if (!isCli()) {
    $defaults['site_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/';
}

define('CRC32_LIMIT', pow(2, 31) - 1);
define('CRC32_DIFF', CRC32_LIMIT * 2 - 2);

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
srand(time());

set_time_limit(0);
ini_set('max_execution_time', '900000');
ini_set('realpath_cache_size', '16M');
ini_set('realpath_cache_ttl', '1200');
ini_set('pcre.backtrack_limit', '1000000');
ini_set('pcre.recursion_limit', '200000');
ini_set('pcre.jit', '1');

if (!function_exists('stripos')) {
    function stripos($par_Str, $par_Entry, $Offset = 0) {
        return strpos(strtolower($par_Str), strtolower($par_Entry), $Offset);
    }
}

/**
 * Print file
 */
function printFile() {
    die("Not Supported");

    $l_FileName = $_GET['fn'];
    $l_CRC      = isset($_GET['c']) ? (int) $_GET['c'] : 0;
    $l_Content  = file_get_contents($l_FileName);
    $l_FileCRC  = realCRC($l_Content);
    if ($l_FileCRC != $l_CRC) {
        echo 'Доступ запрещен.';
        exit;
    }

    echo '<pre>' . htmlspecialchars($l_Content) . '</pre>';
}

/**
 *
 */
function realCRC($str_in, $full = false) {
    $in = crc32($full ? normal($str_in) : $str_in);
    return ($in > CRC32_LIMIT) ? ($in - CRC32_DIFF) : $in;
}


/**
 * Determine php script is called from the command line interface
 * @return bool
 */
function isCli() {
    return php_sapi_name() == 'cli';
}

function myCheckSum($str) {
    return hash('crc32b', $str);
}

function generatePassword($length = 9) {

    // start with a blank password
    $password = "";

    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);

    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
        $length = $maxlength;
    }

    // set up a counter for how many characters are in the password so far
    $i = 0;

    // add random characters to $password until $length is reached
    while ($i < $length) {

        // pick a random character from the possible ones
        $char = substr($possible, mt_rand(0, $maxlength - 1), 1);

        // have we already used this character in $password?
        if (!strstr($password, $char)) {
            // no, so it's OK to add it onto the end of whatever we've already got...
            $password .= $char;
            // ... and increase the counter by one
            $i++;
        }

    }

    // done!
    return $password;

}

/**
 * Print to console
 * @param mixed $text
 * @param bool $add_lb Add line break
 * @return void
 */
function stdOut($text, $add_lb = true) {
    if (!isCli())
        return;

    if (is_bool($text)) {
        $text = $text ? 'true' : 'false';
    } else if (is_null($text)) {
        $text = 'null';
    }
    if (!is_scalar($text)) {
        $text = print_r($text, true);
    }

    if ((!BOOL_RESULT) && (!JSON_STDOUT)) {
        @fwrite(STDOUT, $text . ($add_lb ? "\n" : ''));
    }
}

/**
 * Print progress
 * @param int $num Current file
 */
function printProgress($num, &$par_File, $vars) {
    global $g_Base64, $g_Iframer, $g_UpdatedJsonLog, $g_AddPrefix, $g_NoPrefix;

    $total_files  = $vars->foundTotalFiles;
    $elapsed_time = microtime(true) - START_TIME;
    $percent      = number_format($total_files ? $num * 100 / $total_files : 0, 1);
    $stat         = '';
    if ($elapsed_time >= 1) {
        $elapsed_seconds = round($elapsed_time, 0);
        $fs              = floor($num / $elapsed_seconds);
        $left_files      = $total_files - $num;
        if ($fs > 0) {
            $left_time = ($left_files / $fs); //ceil($left_files / $fs);
            $stat      = ' [Avg: ' . round($fs, 2) . ' files/s' . ($left_time > 0 ? ' Left: ' . seconds2Human($left_time) : '') . '] [Mlw:' . (count($vars->criticalPHP) + count($g_Base64) + count($vars->warningPHP)) . '|' . (count($vars->criticalJS) + count($g_Iframer) + count($vars->phishing)) . ']';
        }
    }

    $l_FN = $g_AddPrefix . str_replace($g_NoPrefix, '', $par_File);
    $l_FN = substr($par_File, -60);

    $text = "$percent% [$l_FN] $num of {$total_files}. " . $stat;
    $text = str_pad($text, 160, ' ', STR_PAD_RIGHT);
    stdOut(str_repeat(chr(8), 160) . $text, false);


    $data = array(
        'self' => __FILE__,
        'started' => AIBOLIT_START_TIME,
        'updated' => time(),
        'progress' => $percent,
        'time_elapsed' => $elapsed_seconds,
        'time_left' => round($left_time),
        'files_left' => $left_files,
        'files_total' => $total_files,
        'current_file' => substr($g_AddPrefix . str_replace($g_NoPrefix, '', $par_File), -160)
    );

    if (function_exists('aibolit_onProgressUpdate')) {
        aibolit_onProgressUpdate($data);
    }

    if (defined('PROGRESS_LOG_FILE') && (time() - $g_UpdatedJsonLog > 1)) {
        if (function_exists('json_encode')) {
            file_put_contents(PROGRESS_LOG_FILE, json_encode($data));
        } else {
            file_put_contents(PROGRESS_LOG_FILE, serialize($data));
        }

        $g_UpdatedJsonLog = time();
    }

    if (defined('SHARED_MEMORY')) {
        shmop_write(SHARED_MEMORY, str_repeat("\0", shmop_size(SHARED_MEMORY)), 0);
        if (function_exists('json_encode')) {
            shmop_write(SHARED_MEMORY, json_encode($data), 0);
        } else {
            shmop_write(SHARED_MEMORY, serialize($data), 0);
        }
    }
}

/**
 * Seconds to human readable
 * @param int $seconds
 * @return string
 */
function seconds2Human($seconds) {
    $r        = '';
    $_seconds = floor($seconds);
    $ms       = $seconds - $_seconds;
    $seconds  = $_seconds;
    if ($hours = floor($seconds / 3600)) {
        $r .= $hours . (isCli() ? ' h ' : ' час ');
        $seconds = $seconds % 3600;
    }

    if ($minutes = floor($seconds / 60)) {
        $r .= $minutes . (isCli() ? ' m ' : ' мин ');
        $seconds = $seconds % 60;
    }

    if ($minutes < 3)
        $r .= ' ' . $seconds + ($ms > 0 ? round($ms) : 0) . (isCli() ? ' s' : ' сек');

    return $r;
}

if (isCli()) {

    $cli_options = array(
        'y' => 'deobfuscate',
        'c:' => 'avdb:',
        'm:' => 'memory:',
        's:' => 'size:',
        'a' => 'all',
        'd:' => 'delay:',
        'l:' => 'list:',
        'r:' => 'report:',
        'f' => 'fast',
        'j:' => 'file:',
        'p:' => 'path:',
        'q' => 'quite',
        'e:' => 'cms:',
        'x:' => 'mode:',
        'k:' => 'skip:',
        'i:' => 'idb:',
        'n' => 'sc',
        'o:' => 'json_report:',
        't:' => 'php_report:',
        'z:' => 'progress:',
        'g:' => 'handler:',
        'b' => 'smart',
        'u:' => 'username:',
        'h' => 'help'
    );

    $cli_longopts = array(
        'deobfuscate',
        'avdb:',
        'cmd:',
        'noprefix:',
        'addprefix:',
        'scan:',
        'one-pass',
        'smart',
        'quarantine',
        'with-2check',
        'skip-cache',
        'username:',
        'imake',
        'icheck',
        'no-html',
        'json-stdout',
        'listing:',
        'encode-b64-fn',
        'cloud-assist:',
        'cloudscan-size:',
        'with-suspicious',
        'rapid-account-scan:',
        'rapid-account-scan-type:',
        'extended-report',
        'factory-config:',
        'shared-mem-progress:',
        'create-shared-mem',
        'max-size-scan-bytes:',
        'input-fn-b64-encoded',
        'use-heuristics',
        'use-heuristics-suspicious',
        'resident',
        'detached:'
    );

    $cli_longopts = array_merge($cli_longopts, array_values($cli_options));

    $options = getopt(implode('', array_keys($cli_options)), $cli_longopts);

    if (isset($options['h']) OR isset($options['help'])) {
        $memory_limit = ini_get('memory_limit');
        echo <<<HELP
Revisium AI-Bolit - an Intelligent Malware File Scanner for Websites.

Usage: php {$_SERVER['PHP_SELF']} [OPTIONS] [PATH]
Current default path is: {$defaults['path']}

  -j, --file=FILE                       Full path to single file to check
  -p, --path=PATH                       Directory path to scan, by default the file directory is used
                                        Current path: {$defaults['path']}
  -p, --listing=FILE                    Scan files from the listing. E.g. --listing=/tmp/myfilelist.txt
                                            Use --listing=stdin to get listing from stdin stream
      --extended-report                 To expand the report
  -x, --mode=INT                        Set scan mode. 0 - for basic, 1 - for expert and 2 for paranoic.
  -k, --skip=jpg,...                    Skip specific extensions. E.g. --skip=jpg,gif,png,xls,pdf
      --scan=php,...                    Scan only specific extensions. E.g. --scan=php,htaccess,js
      --cloud-assist=TOKEN              Enable cloud assisted scanning. Disabled by default.
      --with-suspicious                 Detect suspicious files. Disabled by default.
      --rapid-account-scan=<dir>        Enable rapid account scan. Use <dir> for base db dir. Need to set only root permissions(700)
      --rapid-account-scan-type=<type>  Type rapid account scan. <type> = NONE|ALL|SUSPICIOUS, def:SUSPICIOUS
      --use-heuristics                  Enable heuristic algorithms and mark found files as malicious.
      --use-heuristics-suspicious       Enable heuristic algorithms and mark found files as suspicious.
  -r, --report=PATH
  -o, --json_report=FILE                Full path to create json-file with a list of found malware
  -l, --list=FILE                       Full path to create plain text file with a list of found malware
      --no-html                         Disable HTML report
      --encode-b64-fn                   Encode file names in a report with base64 (for internal usage)
      --input-fn-b64-encoded            Base64 encoded input filenames in listing or stdin
      --smart                           Enable smart mode (skip cache files and optimize scanning)
  -m, --memory=SIZE                     Maximum amount of memory a script may consume. Current value: $memory_limit
                                        Can take shorthand byte values (1M, 1G...)
  -s, --size=SIZE                       Scan files are smaller than SIZE with signatures. 0 - All files. Current value: {$defaults['max_size_to_scan']}
      --max-size-scan-bytes=SIZE        Scan first <bytes> for large(can set by --size) files with signatures.
      --cloudscan-size                  Scan files are smaller than SIZE with cloud assisted scan. 0 - All files. Current value: {$defaults['max_size_to_cloudscan']}
  -d, --delay=INT                       Delay in milliseconds when scanning files to reduce load on the file system (Default: 1)
  -a, --all                             Scan all files (by default scan. js,. php,. html,. htaccess)
      --one-pass                        Do not calculate remaining time
      --quarantine                      Archive all malware from report
      --with-2check                     Create or use AI-BOLIT-DOUBLECHECK.php file
      --imake
      --icheck
      --idb=file                        Integrity Check database file

  -z, --progress=FILE                   Runtime progress of scanning, saved to the file, full path required. 
      --shared-mem-progress=<ID>        Runtime progress of scanning, saved to the shared memory <ID>.
      --create-shared-mem               Need to create shared memory segment <ID> for --shared-mem-progress. 
  -u, --username=<username>             Run scanner with specific user id and group id, e.g. --username=www-data
  -g, --hander=FILE                     External php handler for different events, full path to php file required.
      --cmd="command [args...]"         Run command after scanning

      --help                            Display this help and exit

* Mandatory arguments listed below are required for both full and short way of usage.

HELP;
        exit;
    }

    $l_FastCli = false;

    if ((isset($options['memory']) AND !empty($options['memory']) AND ($memory = $options['memory'])) OR (isset($options['m']) AND !empty($options['m']) AND ($memory = $options['m']))) {
        $memory = getBytes($memory);
        if ($memory > 0) {
            $defaults['memory_limit'] = $memory;
            ini_set('memory_limit', $memory);
        }
    }


    $avdb = '';
    if ((isset($options['avdb']) AND !empty($options['avdb']) AND ($avdb = $options['avdb'])) OR (isset($options['c']) AND !empty($options['c']) AND ($avdb = $options['c']))) {
        if (file_exists($avdb)) {
            $defaults['avdb'] = $avdb;
        }
    }

    if ((isset($options['file']) AND !empty($options['file']) AND ($file = $options['file']) !== false) OR (isset($options['j']) AND !empty($options['j']) AND ($file = $options['j']) !== false)) {
        define('SCAN_FILE', $file);
    }


    if (isset($options['deobfuscate']) OR isset($options['y'])) {
        define('AI_DEOBFUSCATE', true);
    }

    if ((isset($options['list']) AND !empty($options['list']) AND ($file = $options['list']) !== false) OR (isset($options['l']) AND !empty($options['l']) AND ($file = $options['l']) !== false)) {

        define('PLAIN_FILE', $file);
    }

    if ((isset($options['listing']) AND !empty($options['listing']) AND ($listing = $options['listing']) !== false)) {

        if (file_exists($listing) && is_file($listing) && is_readable($listing)) {
            define('LISTING_FILE', $listing);
        }

        if ($listing == 'stdin') {
            define('LISTING_FILE', $listing);
        }
    }

    if ((isset($options['json_report']) AND !empty($options['json_report']) AND ($file = $options['json_report']) !== false) OR (isset($options['o']) AND !empty($options['o']) AND ($file = $options['o']) !== false)) {
        define('JSON_FILE', $file);

        if (!function_exists('json_encode')) {
            die('json_encode function is not available. Enable json extension in php.ini');
        }
    }

    if ((isset($options['php_report']) AND !empty($options['php_report']) AND ($file = $options['php_report']) !== false) OR (isset($options['t']) AND !empty($options['t']) AND ($file = $options['t']) !== false)) {
        define('PHP_FILE', $file);
    }

    if (isset($options['smart']) OR isset($options['b'])) {
        define('SMART_SCAN', 1);
    }

    if ((isset($options['handler']) AND !empty($options['handler']) AND ($file = $options['handler']) !== false) OR (isset($options['g']) AND !empty($options['g']) AND ($file = $options['g']) !== false)) {
        if (file_exists($file)) {
            define('AIBOLIT_EXTERNAL_HANDLER', $file);
        }
    }

    if ((isset($options['progress']) AND !empty($options['progress']) AND ($file = $options['progress']) !== false) OR (isset($options['z']) AND !empty($options['z']) AND ($file = $options['z']) !== false)) {
        define('PROGRESS_LOG_FILE', $file);
    }

    if (isset($options['create-shared-mem'])) {
        define('CREATE_SHARED_MEMORY', true);
    } else {
        define('CREATE_SHARED_MEMORY', false);
    }

    if (isset($options['shared-mem-progress']) AND !empty($options['shared-mem-progress']) AND ($sh_mem = $options['shared-mem-progress']) !== false) {
        if (CREATE_SHARED_MEMORY) {
            @$shid = shmop_open(intval($sh_mem), "n", 0666, 5000);
        } else {
            @$shid = shmop_open(intval($sh_mem), "w", 0, 0);
        }
        if (!empty($shid)) {
            define('SHARED_MEMORY', $shid);
        } else {
            die('Error with shared-memory.');
        }
    }

    if ((isset($options['size']) AND !empty($options['size']) AND ($size = $options['size']) !== false) OR (isset($options['s']) AND !empty($options['s']) AND ($size = $options['s']) !== false)) {
        $size                         = getBytes($size);
        $defaults['max_size_to_scan'] = $size > 0 ? $size : 0;
    }

    if (isset($options['cloudscan-size']) AND !empty($options['cloudscan-size']) AND ($cloudscan_size = $options['cloudscan-size']) !== false) {
        $cloudscan_size                         = getBytes($cloudscan_size);
        $defaults['max_size_to_cloudscan'] = $cloudscan_size > 0 ? $cloudscan_size : 0;
    }

    if (isset($options['max-size-scan-bytes']) && !empty($options['max-size-scan-bytes'])) {
        define('MAX_SIZE_SCAN_BYTES', getBytes($options['max-size-scan-bytes']));
    } else {
        define('MAX_SIZE_SCAN_BYTES', 0);
    }

    if ((isset($options['username']) AND !empty($options['username']) AND ($username = $options['username']) !== false) OR (isset($options['u']) AND !empty($options['u']) AND ($username = $options['u']) !== false)) {

        if (!empty($username) && ($info = posix_getpwnam($username)) !== false) {
            posix_setgid($info['gid']);
            posix_setuid($info['uid']);
            $defaults['userid']  = $info['uid'];
            $defaults['groupid'] = $info['gid'];
        } else {
            echo ('Invalid username');
            exit(-1);
        }
    }

    if ((isset($options['file']) AND !empty($options['file']) AND ($file = $options['file']) !== false) OR (isset($options['j']) AND !empty($options['j']) AND ($file = $options['j']) !== false) AND (isset($options['q']))) {
        $BOOL_RESULT = true;
    }

    if (isset($options['json-stdout'])) {
        define('JSON_STDOUT', true);
    } else {
        define('JSON_STDOUT', false);
    }

    if (isset($options['f'])) {
        $l_FastCli = true;
    }

    if (isset($options['q']) || isset($options['quite'])) {
        $BOOL_RESULT = true;
    }

    if (isset($options['x'])) {
        define('AI_EXPERT', $options['x']);
    } else if (isset($options['mode'])) {
        define('AI_EXPERT', $options['mode']);
    } else {
        define('AI_EXPERT', AI_EXPERT_MODE);
    }

    if (AI_EXPERT < 2) {
        $g_SpecificExt              = true;
        $defaults['scan_all_files'] = false;
    } else {
        $defaults['scan_all_files'] = true;
    }

    define('BOOL_RESULT', $BOOL_RESULT);

    if ((isset($options['delay']) AND !empty($options['delay']) AND ($delay = $options['delay']) !== false) OR (isset($options['d']) AND !empty($options['d']) AND ($delay = $options['d']) !== false)) {
        $delay = (int) $delay;
        if (!($delay < 0)) {
            $defaults['scan_delay'] = $delay;
        }
    }

    if ((isset($options['skip']) AND !empty($options['skip']) AND ($ext_list = $options['skip']) !== false) OR (isset($options['k']) AND !empty($options['k']) AND ($ext_list = $options['k']) !== false)) {
        $defaults['skip_ext'] = $ext_list;
    }

    if (isset($options['n']) OR isset($options['skip-cache'])) {
        $defaults['skip_cache'] = true;
    }

    if (isset($options['scan'])) {
        $ext_list = strtolower(trim($options['scan'], " ,\t\n\r\0\x0B"));
        if ($ext_list != '') {
            $l_FastCli        = true;
            $g_SensitiveFiles = explode(",", $ext_list);
            for ($i = 0; $i < count($g_SensitiveFiles); $i++) {
                if ($g_SensitiveFiles[$i] == '.') {
                    $g_SensitiveFiles[$i] = '';
                }
            }

            $g_SpecificExt = true;
        }
    }
    
    if (isset($options['cloud-assist'])) {
        define('CLOUD_ASSIST_TOKEN', $options['cloud-assist']);
    }
    

    if (isset($options['rapid-account-scan'])) {
        define('RAPID_ACCOUNT_SCAN', $options['rapid-account-scan']);
    }
    
    if (defined('RAPID_ACCOUNT_SCAN')) {
        if (isset($options['rapid-account-scan-type'])) {
            define('RAPID_ACCOUNT_SCAN_TYPE', $options['rapid-account-scan-type']);
        }
        else {
            define('RAPID_ACCOUNT_SCAN_TYPE', 'SUSPICIOUS');
        }
    }

    if (isset($options['with-suspicious'])) {
        define('AI_EXTRA_WARN', true);
    }

    if (isset($options['extended-report'])) {
        define('EXTENDED_REPORT', true);
    }

    if (isset($options['all']) OR isset($options['a'])) {
        $defaults['scan_all_files'] = true;
        $g_SpecificExt              = false;
    }

    if (isset($options['cms'])) {
        define('CMS', $options['cms']);
    } else if (isset($options['e'])) {
        define('CMS', $options['e']);
    }


    if (!defined('SMART_SCAN')) {
        define('SMART_SCAN', 0);
    }

    if (!defined('AI_DEOBFUSCATE')) {
        define('AI_DEOBFUSCATE', false);
    }

    if (!defined('AI_EXTRA_WARN')) {
        define('AI_EXTRA_WARN', false);
    }


    $l_SpecifiedPath = false;
    if ((isset($options['path']) AND !empty($options['path']) AND ($path = $options['path']) !== false) OR (isset($options['p']) AND !empty($options['p']) AND ($path = $options['p']) !== false)) {
        $defaults['path'] = $path;
        $l_SpecifiedPath  = true;
    }

    if (isset($options['noprefix']) AND !empty($options['noprefix']) AND ($g_NoPrefix = $options['noprefix']) !== false) {
    } else {
        $g_NoPrefix = '';
    }

    if (isset($options['addprefix']) AND !empty($options['addprefix']) AND ($g_AddPrefix = $options['addprefix']) !== false) {
    } else {
        $g_AddPrefix = '';
    }

    if (isset($options['use-heuristics'])) {
        define('USE_HEURISTICS', true);
    }

    if (isset($options['use-heuristics-suspicious'])) {
        define('USE_HEURISTICS_SUSPICIOUS', true);
    }

    if (defined('USE_HEURISTICS') && defined('USE_HEURISTICS_SUSPICIOUS')) {
        die('You can not use --use-heuristic and --use-heuristic-suspicious the same time.');
    }

    $l_SuffixReport = str_replace('/var/www', '', $defaults['path']);
    $l_SuffixReport = str_replace('/home', '', $l_SuffixReport);
    $l_SuffixReport = preg_replace('~[/\\\.\s]~', '_', $l_SuffixReport);
    $l_SuffixReport .= "-" . rand(1, 999999);

    if ((isset($options['report']) AND ($report = $options['report']) !== false) OR (isset($options['r']) AND ($report = $options['r']) !== false)) {
        $report = str_replace('@PATH@', $l_SuffixReport, $report);
        $report = str_replace('@RND@', rand(1, 999999), $report);
        $report = str_replace('@DATE@', date('d-m-Y-h-i'), $report);
        define('REPORT', $report);
        define('NEED_REPORT', true);
    }

    if (isset($options['no-html'])) {
        define('REPORT', 'no@email.com');
    }

    defined('ENCODE_FILENAMES_WITH_BASE64') || define('ENCODE_FILENAMES_WITH_BASE64', isset($options['encode-b64-fn']));
    
    defined('INPUT_FILENAMES_BASE64_ENCODED') || define('INPUT_FILENAMES_BASE64_ENCODED', isset($options['input-fn-b64-encoded']));
    
    if ((isset($options['idb']) AND ($ireport = $options['idb']) !== false)) {
        $ireport = str_replace('@PATH@', $l_SuffixReport, $ireport);
        $ireport = str_replace('@RND@', rand(1, 999999), $ireport);
        $ireport = str_replace('@DATE@', date('d-m-Y-h-i'), $ireport);
        define('INTEGRITY_DB_FILE', $ireport);
    }


    defined('REPORT') OR define('REPORT', 'AI-BOLIT-REPORT-' . $l_SuffixReport . '-' . date('d-m-Y_H-i') . '.html');

    defined('INTEGRITY_DB_FILE') OR define('INTEGRITY_DB_FILE', 'AINTEGRITY-' . $l_SuffixReport . '-' . date('d-m-Y_H-i'));

    $last_arg = max(1, sizeof($_SERVER['argv']) - 1);
    if (isset($_SERVER['argv'][$last_arg])) {
        $path = $_SERVER['argv'][$last_arg];
        if (substr($path, 0, 1) != '-' AND (substr($_SERVER['argv'][$last_arg - 1], 0, 1) != '-' OR array_key_exists(substr($_SERVER['argv'][$last_arg - 1], -1), $cli_options))) {
            $defaults['path'] = $path;
        }
    }

    define('ONE_PASS', isset($options['one-pass']));

    define('IMAKE', isset($options['imake']));
    define('ICHECK', isset($options['icheck']));

    if (IMAKE && ICHECK)
        die('One of the following options must be used --imake or --icheck.');

    // BEGIN of configuring the factory
    $factoryConfig = [
        RapidAccountScan::class => RapidAccountScan::class,
        RapidScanStorage::class => RapidScanStorage::class,
        DbFolderSpecification::class => DbFolderSpecification::class,
        CriticalFileSpecification::class => CriticalFileSpecification::class,
        CloudAssistedRequest::class => CloudAssistedRequest::class,
        JSONReport::class => JSONReport::class,
        DetachedMode::class => DetachedMode::class,
        ResidentMode::class => ResidentMode::class,
    ];

    if (isset($options['factory-config'])) {
        $optionalFactoryConfig = require($options['factory-config']);
        $factoryConfig = array_merge($factoryConfig, $optionalFactoryConfig);
    }

    Factory::configure($factoryConfig);
    // END of configuring the factory

} else {
    define('AI_EXPERT', AI_EXPERT_MODE);
    define('ONE_PASS', true);
}

if (ONE_PASS && defined('CLOUD_ASSIST_TOKEN')) {
    die('Both parameters(one-pass and cloud-assist) not supported');
}

if (defined('RAPID_ACCOUNT_SCAN') && !defined('CLOUD_ASSIST_TOKEN')) { 
    die('CloudScan should be enabled');
}

if (defined('CREATE_SHARED_MEMORY') && CREATE_SHARED_MEMORY == true && !defined('SHARED_MEMORY')) {
    die('shared-mem-progress should be enabled and ID specified.');
}

if (defined('RAPID_ACCOUNT_SCAN')) {
    @mkdir(RAPID_ACCOUNT_SCAN, 0700, true);
    $specification = Factory::instance()->create(DbFolderSpecification::class);
    if (!$specification->satisfiedBy(RAPID_ACCOUNT_SCAN)) {
        @unlink(RAPID_ACCOUNT_SCAN);
        die('Rapid DB folder error! Please check the folder.');
    }
}

if (defined('RAPID_ACCOUNT_SCAN_TYPE') && !in_array(RAPID_ACCOUNT_SCAN_TYPE, array('NONE', 'ALL', 'SUSPICIOUS'))) {
    die('Wrong Rapid account scan type');
}

if (defined('RAPID_ACCOUNT_SCAN') && !extension_loaded('leveldb')) { 
    die('LevelDB extension needed for Rapid DB');
}

$vars->blackFiles = [];

if (isset($defaults['avdb']) && file_exists($defaults['avdb'])) {
    $avdb = explode("\n", gzinflate(base64_decode(str_rot13(strrev(trim(file_get_contents($defaults['avdb'])))))));

    $g_DBShe       = explode("\n", base64_decode($avdb[0]));
    $gX_DBShe      = explode("\n", base64_decode($avdb[1]));
    $g_FlexDBShe   = explode("\n", base64_decode($avdb[2]));
    $gX_FlexDBShe  = explode("\n", base64_decode($avdb[3]));
    $gXX_FlexDBShe = explode("\n", base64_decode($avdb[4]));
    $g_ExceptFlex  = explode("\n", base64_decode($avdb[5]));
    $g_AdwareSig   = explode("\n", base64_decode($avdb[6]));
    $g_PhishingSig = explode("\n", base64_decode($avdb[7]));
    $g_JSVirSig    = explode("\n", base64_decode($avdb[8]));
    $gX_JSVirSig   = explode("\n", base64_decode($avdb[9]));
    $g_SusDB       = explode("\n", base64_decode($avdb[10]));
    $g_SusDBPrio   = explode("\n", base64_decode($avdb[11]));
    $g_DeMapper    = array_combine(explode("\n", base64_decode($avdb[12])), explode("\n", base64_decode($avdb[13])));
    $g_Mnemo    = @array_flip(@array_combine(explode("\n", base64_decode($avdb[14])), explode("\n", base64_decode($avdb[15]))));

    // get meta information
    $avdb_meta_info = json_decode(base64_decode($avdb[16]), true);
    $db_meta_info['build-date'] = $avdb_meta_info ? $avdb_meta_info['build-date'] : 'n/a';
    $db_meta_info['version'] = $avdb_meta_info ? $avdb_meta_info['version'] : 'n/a';
    $db_meta_info['release-type'] = $avdb_meta_info ? $avdb_meta_info['release-type'] : 'n/a';

    if (count($g_DBShe) <= 1) {
        $g_DBShe = array();
    }

    if (count($gX_DBShe) <= 1) {
        $gX_DBShe = array();
    }

    if (count($g_FlexDBShe) <= 1) {
        $g_FlexDBShe = array();
    }

    if (count($gX_FlexDBShe) <= 1) {
        $gX_FlexDBShe = array();
    }

    if (count($gXX_FlexDBShe) <= 1) {
        $gXX_FlexDBShe = array();
    }

    if (count($g_ExceptFlex) <= 1) {
        $g_ExceptFlex = array();
    }

    if (count($g_AdwareSig) <= 1) {
        $g_AdwareSig = array();
    }

    if (count($g_PhishingSig) <= 1) {
        $g_PhishingSig = array();
    }

    if (count($gX_JSVirSig) <= 1) {
        $gX_JSVirSig = array();
    }

    if (count($g_JSVirSig) <= 1) {
        $g_JSVirSig = array();
    }

    if (count($g_SusDB) <= 1) {
        $g_SusDB = array();
    }

    if (count($g_SusDBPrio) <= 1) {
        $g_SusDBPrio = array();
    }
    $db_location = 'external';
    stdOut('Loaded external signatures from ' . $defaults['avdb']);
}

// use only basic signature subset
if (AI_EXPERT < 2) {
    $gX_FlexDBShe  = array();
    $gXX_FlexDBShe = array();
    $gX_JSVirSig   = array();
}

if (isset($defaults['userid'])) {
    stdOut('Running from ' . $defaults['userid'] . ':' . $defaults['groupid']);
}

$sign_count = count($g_JSVirSig) + count($gX_JSVirSig) + count($g_DBShe) + count($gX_DBShe) + count($gX_DBShe) + count($g_FlexDBShe) + count($gX_FlexDBShe) + count($gXX_FlexDBShe);

if (AI_EXTRA_WARN) {
    $sign_count += count($g_SusDB);
}

stdOut('Malware signatures: ' . $sign_count);

if ($g_SpecificExt) {
    stdOut("Scan specific extensions: " . implode(',', $g_SensitiveFiles));
}

// Black list database
try {
    $file = dirname(__FILE__) . '/AIBOLIT-BINMALWARE.db';
    if (isset($defaults['avdb'])) {
        $file = dirname($defaults['avdb']) . '/AIBOLIT-BINMALWARE.db';
    }
    $vars->blacklist = FileHashMemoryDb::open($file);
    stdOut("Binary malware signatures: " . ceil($vars->blacklist->count()));
} catch (Exception $e) {
    $vars->blacklist = null;
}

if (!DEBUG_PERFORMANCE) {
    OptimizeSignatures();
} else {
    stdOut("Debug Performance Scan");
}

$g_DBShe  = array_map('strtolower', $g_DBShe);
$gX_DBShe = array_map('strtolower', $gX_DBShe);

if (!defined('PLAIN_FILE')) {
    define('PLAIN_FILE', '');
}

// Init
define('MAX_ALLOWED_PHP_HTML_IN_DIR', 600);
define('BASE64_LENGTH', 69);
define('MAX_PREVIEW_LEN', 120);
define('MAX_EXT_LINKS', 1001);

if (defined('AIBOLIT_EXTERNAL_HANDLER')) {
    include_once(AIBOLIT_EXTERNAL_HANDLER);
    stdOut("\nLoaded external handler: " . AIBOLIT_EXTERNAL_HANDLER . "\n");
    if (function_exists("aibolit_onStart")) {
        aibolit_onStart();
    }
}

// Perform full scan when running from command line
if (isset($_GET['full'])) {
    $defaults['scan_all_files'] = 1;
}

if ($l_FastCli) {
    $defaults['scan_all_files'] = 0;
}

if (!isCli()) {
    define('ICHECK', isset($_GET['icheck']));
    define('IMAKE', isset($_GET['imake']));

    define('INTEGRITY_DB_FILE', 'ai-integrity-db');
}

define('SCAN_ALL_FILES', (bool) $defaults['scan_all_files']);
define('SCAN_DELAY', (int) $defaults['scan_delay']);
define('MAX_SIZE_TO_SCAN', getBytes($defaults['max_size_to_scan']));
define('MAX_SIZE_TO_CLOUDSCAN', getBytes($defaults['max_size_to_cloudscan']));

if ($defaults['memory_limit'] AND ($defaults['memory_limit'] = getBytes($defaults['memory_limit'])) > 0) {
    ini_set('memory_limit', $defaults['memory_limit']);
    stdOut("Changed memory limit to " . $defaults['memory_limit']);
}

define('ROOT_PATH', realpath($defaults['path']));

if (!ROOT_PATH) {
    if (isCli()) {
        die(stdOut("Directory '{$defaults['path']}' not found!"));
    }
} elseif (!is_readable(ROOT_PATH)) {
    if (isCli()) {
        die2(stdOut("Cannot read directory '" . ROOT_PATH . "'!"));
    }
}

define('CURRENT_DIR', getcwd());
chdir(ROOT_PATH);

if (isCli() AND REPORT !== '' AND !getEmails(REPORT)) {
    $report      = str_replace('\\', '/', REPORT);
    $abs         = strpos($report, '/') === 0 ? DIR_SEPARATOR : '';
    $report      = array_values(array_filter(explode('/', $report)));
    $report_file = array_pop($report);
    $report_path = realpath($abs . implode(DIR_SEPARATOR, $report));

    define('REPORT_FILE', $report_file);
    define('REPORT_PATH', $report_path);

    if (REPORT_FILE AND REPORT_PATH AND is_file(REPORT_PATH . DIR_SEPARATOR . REPORT_FILE)) {
        @unlink(REPORT_PATH . DIR_SEPARATOR . REPORT_FILE);
    }
}

if (defined('REPORT_PATH')) {
    $l_ReportDirName = REPORT_PATH;
}

$path                       = $defaults['path'];
$report_mask                = $defaults['report_mask'];
$extended_report            = defined('EXTENDED_REPORT') && EXTENDED_REPORT;
$rapid_account_scan_report  = defined('RAPID_ACCOUNT_SCAN');

$reportFactory = function () use ($g_Mnemo, $path, $db_location, $db_meta_info, $report_mask, $extended_report, $rapid_account_scan_report) {
    return Factory::instance()->create(JSONReport::class, [$g_Mnemo, $path, $db_location, $db_meta_info['version'], $report_mask, $extended_report, $rapid_account_scan_report, AI_VERSION, AI_HOSTER, AI_EXTRA_WARN]);
};

if (isset($options['detached'])) {
    Factory::instance()->create(DetachedMode::class, [$options['detached'], $vars, LISTING_FILE, START_TIME, $reportFactory, INPUT_FILENAMES_BASE64_ENCODED]);
    exit(0);
}

if (isset($options['resident'])) {
    Factory::instance()->create(ResidentMode::class, [$reportFactory, $vars->blacklist]);
    exit(0);
}

define('QUEUE_FILENAME', ($l_ReportDirName != '' ? $l_ReportDirName . '/' : '') . 'AI-BOLIT-QUEUE-' . md5($defaults['path']) . '-' . rand(1000, 9999) . '.txt');

if (function_exists('phpinfo')) {
    ob_start();
    phpinfo();
    $l_PhpInfo = ob_get_contents();
    ob_end_clean();

    $l_PhpInfo = str_replace('border: 1px', '', $l_PhpInfo);
    preg_match('|<body>(.*)</body>|smi', $l_PhpInfo, $l_PhpInfoBody);
}

////////////////////////////////////////////////////////////////////////////
$l_Template = str_replace("@@MODE@@", AI_EXPERT . '/' . SMART_SCAN, $l_Template);

if (AI_EXPERT == 0) {
    $l_Result .= '<div class="rep">' . AI_STR_057 . '</div>';
}

$l_Template = str_replace('@@HEAD_TITLE@@', AI_STR_051 . $g_AddPrefix . str_replace($g_NoPrefix, '', ROOT_PATH), $l_Template);

define('QCR_INDEX_FILENAME', 'fn');
define('QCR_INDEX_TYPE', 'type');
define('QCR_INDEX_WRITABLE', 'wr');
define('QCR_SVALUE_FILE', '1');
define('QCR_SVALUE_FOLDER', '0');

/**
 * Extract emails from the string
 * @param string $email
 * @return array of strings with emails or false on error
 */
function getEmails($email) {
    $email = preg_split('~[,\s;]~', $email, -1, PREG_SPLIT_NO_EMPTY);
    $r     = array();
    for ($i = 0, $size = sizeof($email); $i < $size; $i++) {
        if (function_exists('filter_var')) {
            if (filter_var($email[$i], FILTER_VALIDATE_EMAIL)) {
                $r[] = $email[$i];
            }
        } else {
            // for PHP4
            if (strpos($email[$i], '@') !== false) {
                $r[] = $email[$i];
            }
        }
    }
    return empty($r) ? false : $r;
}

/**
 * Get bytes from shorthand byte values (1M, 1G...)
 * @param int|string $val
 * @return int
 */
function getBytes($val) {
    $val  = trim($val);
    $last = strtolower($val{strlen($val) - 1});
    switch ($last) {
        case 't':
            $val *= 1024;
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return intval($val);
}

/**
 * Format bytes to human readable
 * @param int $bites
 * @return string
 */
function bytes2Human($bites) {
    if ($bites < 1024) {
        return $bites . ' b';
    } elseif (($kb = $bites / 1024) < 1024) {
        return number_format($kb, 2) . ' Kb';
    } elseif (($mb = $kb / 1024) < 1024) {
        return number_format($mb, 2) . ' Mb';
    } elseif (($gb = $mb / 1024) < 1024) {
        return number_format($gb, 2) . ' Gb';
    } else {
        return number_format($gb / 1024, 2) . 'Tb';
    }
}

///////////////////////////////////////////////////////////////////////////
function needIgnore($par_FN, $par_CRC) {
    global $g_IgnoreList;

    for ($i = 0; $i < count($g_IgnoreList); $i++) {
        if (strpos($par_FN, $g_IgnoreList[$i][0]) !== false) {
            if ($par_CRC == $g_IgnoreList[$i][1]) {
                return true;
            }
        }
    }

    return false;
}

///////////////////////////////////////////////////////////////////////////
function makeSafeFn($par_Str, $replace_path = false) {
    global $g_AddPrefix, $g_NoPrefix;
    if ($replace_path) {
        $lines = explode("\n", $par_Str);
        array_walk($lines, function(&$n) {
            global $g_AddPrefix, $g_NoPrefix;
            $n = $g_AddPrefix . str_replace($g_NoPrefix, '', $n);
        });

        $par_Str = implode("\n", $lines);
    }

    return htmlspecialchars($par_Str, ENT_SUBSTITUTE | ENT_QUOTES);
}

function replacePathArray($par_Arr) {
    global $g_AddPrefix, $g_NoPrefix;
    array_walk($par_Arr, function(&$n) {
        global $g_AddPrefix, $g_NoPrefix;
        $n = $g_AddPrefix . str_replace($g_NoPrefix, '', $n);
    });

    return $par_Arr;
}

///////////////////////////////////////////////////////////////////////////
function printList($par_List, $vars, $par_Details = null, $par_NeedIgnore = false, $par_SigId = null, $par_TableName = null) {
    global $g_NoPrefix, $g_AddPrefix;

    $i = 0;

    if ($par_TableName == null) {
        $par_TableName = 'table_' . rand(1000000, 9000000);
    }

    $l_Result = '';
    $l_Result .= "<div class=\"flist\"><table cellspacing=1 cellpadding=4 border=0 id=\"" . $par_TableName . "\">";

    $l_Result .= "<thead><tr class=\"tbgh" . ($i % 2) . "\">";
    $l_Result .= "<th width=70%>" . AI_STR_004 . "</th>";
    $l_Result .= "<th>" . AI_STR_005 . "</th>";
    $l_Result .= "<th>" . AI_STR_006 . "</th>";
    $l_Result .= "<th width=90>" . AI_STR_007 . "</th>";
    $l_Result .= "<th width=0 class=\"hidd\">CRC32</th>";
    $l_Result .= "<th width=0 class=\"hidd\"></th>";
    $l_Result .= "<th width=0 class=\"hidd\"></th>";
    $l_Result .= "<th width=0 class=\"hidd\"></th>";

    $l_Result .= "</tr></thead><tbody>";

    for ($i = 0; $i < count($par_List); $i++) {
        if ($par_SigId != null) {
            $l_SigId = 'id_' . $par_SigId[$i];
        } else {
            $l_SigId = 'id_z' . rand(1000000, 9000000);
        }

        $l_Pos = $par_List[$i];
        if ($par_NeedIgnore) {
            if (needIgnore($vars->structure['n'][$par_List[$i]], $vars->structure['crc'][$l_Pos])) {
                continue;
            }
        }

        $l_Creat = $vars->structure['c'][$l_Pos] > 0 ? date("d/m/Y H:i:s", $vars->structure['c'][$l_Pos]) : '-';
        $l_Modif = $vars->structure['m'][$l_Pos] > 0 ? date("d/m/Y H:i:s", $vars->structure['m'][$l_Pos]) : '-';
        $l_Size  = $vars->structure['s'][$l_Pos] > 0 ? bytes2Human($vars->structure['s'][$l_Pos]) : '-';

        if ($par_Details != null) {
            $l_WithMarker = preg_replace('|__AI_MARKER__|smi', '<span class="marker">&nbsp;</span>', $par_Details[$i]);
            $l_WithMarker = preg_replace('|__AI_LINE1__|smi', '<span class="line_no">', $l_WithMarker);
            $l_WithMarker = preg_replace('|__AI_LINE2__|smi', '</span>', $l_WithMarker);

            $l_Body = '<div class="details">';

            if ($par_SigId != null) {
                $l_Body .= '<a href="#" onclick="return hsig(\'' . $l_SigId . '\')">[x]</a> ';
            }

            $l_Body .= $l_WithMarker . '</div>';
        } else {
            $l_Body = '';
        }

        $l_Result .= '<tr class="tbg' . ($i % 2) . '" o="' . $l_SigId . '">';

        if (is_file($vars->structure['n'][$l_Pos])) {
            $l_Result .= '<td><div class="it"><a class="it">' . makeSafeFn($g_AddPrefix . str_replace($g_NoPrefix, '', $vars->structure['n'][$l_Pos])) . '</a></div>' . $l_Body . '</td>';
        } else {
            $l_Result .= '<td><div class="it"><a class="it">' . makeSafeFn($g_AddPrefix . str_replace($g_NoPrefix, '', $vars->structure['n'][$par_List[$i]])) . '</a></div></td>';
        }

        $l_Result .= '<td align=center><div class="ctd">' . $l_Creat . '</div></td>';
        $l_Result .= '<td align=center><div class="ctd">' . $l_Modif . '</div></td>';
        $l_Result .= '<td align=center><div class="ctd">' . $l_Size . '</div></td>';
        $l_Result .= '<td class="hidd"><div class="hidd">' . $vars->structure['crc'][$l_Pos] . '</div></td>';
        $l_Result .= '<td class="hidd"><div class="hidd">' . 'x' . '</div></td>';
        $l_Result .= '<td class="hidd"><div class="hidd">' . $vars->structure['m'][$l_Pos] . '</div></td>';
        $l_Result .= '<td class="hidd"><div class="hidd">' . $l_SigId . '</div></td>';
        $l_Result .= '</tr>';

    }

    $l_Result .= "</tbody></table></div><div class=clear style=\"margin: 20px 0 0 0\"></div>";

    return $l_Result;
}

///////////////////////////////////////////////////////////////////////////
function printPlainList($par_List, $vars, $par_Details = null, $par_NeedIgnore = false, $par_SigId = null, $par_TableName = null) {
    global $g_NoPrefix, $g_AddPrefix;

    $l_Result = "";

    $l_Src = array(
        '&quot;',
        '&lt;',
        '&gt;',
        '&amp;',
        '&#039;'
    );
    $l_Dst = array(
        '"',
        '<',
        '>',
        '&',
        '\''
    );

    for ($i = 0; $i < count($par_List); $i++) {
        $l_Pos = $par_List[$i];
        if ($par_NeedIgnore) {
            if (needIgnore($vars->structure['n'][$par_List[$i]], $vars->structure['crc'][$l_Pos])) {
                continue;
            }
        }


        if ($par_Details != null) {

            $l_Body = preg_replace('|(L\d+).+__AI_MARKER__|smi', '$1: ...', $par_Details[$i]);
            $l_Body = preg_replace('/[^\x20-\x7F]/', '.', $l_Body);
            $l_Body = str_replace($l_Src, $l_Dst, $l_Body);

        } else {
            $l_Body = '';
        }

        if (is_file($vars->structure['n'][$l_Pos])) {
            $l_Result .= $g_AddPrefix . str_replace($g_NoPrefix, '', $vars->structure['n'][$l_Pos]) . "\t\t\t" . $l_Body . "\n";
        } else {
            $l_Result .= $g_AddPrefix . str_replace($g_NoPrefix, '', $vars->structure['n'][$par_List[$i]]) . "\n";
        }

    }

    return $l_Result;
}

///////////////////////////////////////////////////////////////////////////
function extractValue(&$par_Str, $par_Name) {
    if (preg_match('|<tr><td class="e">\s*' . $par_Name . '\s*</td><td class="v">(.+?)</td>|sm', $par_Str, $l_Result)) {
        return str_replace('no value', '', strip_tags($l_Result[1]));
    }
}

///////////////////////////////////////////////////////////////////////////
function QCR_ExtractInfo($par_Str) {
    $l_PhpInfoSystem    = extractValue($par_Str, 'System');
    $l_PhpPHPAPI        = extractValue($par_Str, 'Server API');
    $l_AllowUrlFOpen    = extractValue($par_Str, 'allow_url_fopen');
    $l_AllowUrlInclude  = extractValue($par_Str, 'allow_url_include');
    $l_DisabledFunction = extractValue($par_Str, 'disable_functions');
    $l_DisplayErrors    = extractValue($par_Str, 'display_errors');
    $l_ErrorReporting   = extractValue($par_Str, 'error_reporting');
    $l_ExposePHP        = extractValue($par_Str, 'expose_php');
    $l_LogErrors        = extractValue($par_Str, 'log_errors');
    $l_MQGPC            = extractValue($par_Str, 'magic_quotes_gpc');
    $l_MQRT             = extractValue($par_Str, 'magic_quotes_runtime');
    $l_OpenBaseDir      = extractValue($par_Str, 'open_basedir');
    $l_RegisterGlobals  = extractValue($par_Str, 'register_globals');
    $l_SafeMode         = extractValue($par_Str, 'safe_mode');

    $l_DisabledFunction = ($l_DisabledFunction == '' ? '-?-' : $l_DisabledFunction);
    $l_OpenBaseDir      = ($l_OpenBaseDir == '' ? '-?-' : $l_OpenBaseDir);

    $l_Result = '<div class="title">' . AI_STR_008 . ': ' . phpversion() . '</div>';
    $l_Result .= 'System Version: <span class="php_ok">' . $l_PhpInfoSystem . '</span><br/>';
    $l_Result .= 'PHP API: <span class="php_ok">' . $l_PhpPHPAPI . '</span><br/>';
    $l_Result .= 'allow_url_fopen: <span class="php_' . ($l_AllowUrlFOpen == 'On' ? 'bad' : 'ok') . '">' . $l_AllowUrlFOpen . '</span><br/>';
    $l_Result .= 'allow_url_include: <span class="php_' . ($l_AllowUrlInclude == 'On' ? 'bad' : 'ok') . '">' . $l_AllowUrlInclude . '</span><br/>';
    $l_Result .= 'disable_functions: <span class="php_' . ($l_DisabledFunction == '-?-' ? 'bad' : 'ok') . '">' . $l_DisabledFunction . '</span><br/>';
    $l_Result .= 'display_errors: <span class="php_' . ($l_DisplayErrors == 'On' ? 'ok' : 'bad') . '">' . $l_DisplayErrors . '</span><br/>';
    $l_Result .= 'error_reporting: <span class="php_ok">' . $l_ErrorReporting . '</span><br/>';
    $l_Result .= 'expose_php: <span class="php_' . ($l_ExposePHP == 'On' ? 'bad' : 'ok') . '">' . $l_ExposePHP . '</span><br/>';
    $l_Result .= 'log_errors: <span class="php_' . ($l_LogErrors == 'On' ? 'ok' : 'bad') . '">' . $l_LogErrors . '</span><br/>';
    $l_Result .= 'magic_quotes_gpc: <span class="php_' . ($l_MQGPC == 'On' ? 'ok' : 'bad') . '">' . $l_MQGPC . '</span><br/>';
    $l_Result .= 'magic_quotes_runtime: <span class="php_' . ($l_MQRT == 'On' ? 'bad' : 'ok') . '">' . $l_MQRT . '</span><br/>';
    $l_Result .= 'register_globals: <span class="php_' . ($l_RegisterGlobals == 'On' ? 'bad' : 'ok') . '">' . $l_RegisterGlobals . '</span><br/>';
    $l_Result .= 'open_basedir: <span class="php_' . ($l_OpenBaseDir == '-?-' ? 'bad' : 'ok') . '">' . $l_OpenBaseDir . '</span><br/>';

    if (phpversion() < '5.3.0') {
        $l_Result .= 'safe_mode (PHP < 5.3.0): <span class="php_' . ($l_SafeMode == 'On' ? 'ok' : 'bad') . '">' . $l_SafeMode . '</span><br/>';
    }

    return $l_Result . '<p>';
}

///////////////////////////////////////////////////////////////////////////
function addSlash($dir) {
    return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
}

///////////////////////////////////////////////////////////////////////////
function QCR_Debug($par_Str = "") {
    if (!DEBUG_MODE) {
        return;
    }

    $l_MemInfo = ' ';
    if (function_exists('memory_get_usage')) {
        $l_MemInfo .= ' curmem=' . bytes2Human(memory_get_usage());
    }

    if (function_exists('memory_get_peak_usage')) {
        $l_MemInfo .= ' maxmem=' . bytes2Human(memory_get_peak_usage());
    }

    stdOut("\n" . date('H:i:s') . ': ' . $par_Str . $l_MemInfo . "\n");
}


///////////////////////////////////////////////////////////////////////////
function QCR_ScanDirectories($l_RootDir, $vars) {
    global $defaults, $g_UrlIgnoreList, $g_DirIgnoreList, $g_UnsafeDirArray, $g_UnsafeFilesFound, $g_HiddenFiles, $g_UnixExec, $g_IgnoredExt, $g_SensitiveFiles, $g_SuspiciousFiles, $g_ShortListExt, $l_SkipSample;

    static $l_Buffer = '';

    $l_DirCounter          = 0;
    $l_DoorwayFilesCounter = 0;
    $l_SourceDirIndex      = $vars->counter - 1;

    $l_SkipSample = array();

    QCR_Debug('Scan ' . $l_RootDir);

    $l_QuotedSeparator = quotemeta(DIR_SEPARATOR);
    $l_DIRH = @opendir($l_RootDir);
    if ($l_DIRH === false) {
        return;
    }
    while (($l_FileName = readdir($l_DIRH)) !== false) {
            
        if ($l_FileName == '.' || $l_FileName == '..') {
            continue;
        }
        $l_FileName = $l_RootDir . DIR_SEPARATOR . $l_FileName;
        $l_Type = filetype($l_FileName);
            
        if ($l_Type == "link") {
            $vars->symLinks[] = $l_FileName;
            continue;
        } 
        elseif ($l_Type != "file" && $l_Type != "dir") {
            continue;
        }

        $l_Ext   = strtolower(pathinfo($l_FileName, PATHINFO_EXTENSION));
        $l_IsDir = is_dir($l_FileName);
            
        // which files should be scanned
        $l_NeedToScan = SCAN_ALL_FILES || (in_array($l_Ext, $g_SensitiveFiles));

        if (in_array(strtolower($l_Ext), $g_IgnoredExt)) {
            $l_NeedToScan = false;
        }

        // if folder in ignore list
        $l_Skip = false;
        for ($dr = 0; $dr < count($g_DirIgnoreList); $dr++) {
            if (($g_DirIgnoreList[$dr] != '') && preg_match('#' . $g_DirIgnoreList[$dr] . '#', $l_FileName, $l_Found)) {
                if (!in_array($g_DirIgnoreList[$dr], $l_SkipSample)) {
                    $l_SkipSample[] = $g_DirIgnoreList[$dr];
                } 
                else {
                    $l_Skip       = true;
                    $l_NeedToScan = false;
                }
            }
        }

        if ($l_IsDir) {
            // skip on ignore
            if ($l_Skip) {
                $vars->skippedFolders[] = $l_FileName;
                continue;
            }

            $l_BaseName = basename($l_FileName);

            if (ONE_PASS) {
                $vars->structure['n'][$vars->counter] = $l_FileName . DIR_SEPARATOR;
            } 
            else {
                $l_Buffer .= FilepathEscaper::encodeFilepathByBase64($l_FileName . DIR_SEPARATOR) . "\n";
            }

            $l_DirCounter++;

            if ($l_DirCounter > MAX_ALLOWED_PHP_HTML_IN_DIR) {
                $vars->doorway[]  = $l_SourceDirIndex;
                $l_DirCounter = -655360;
            }

            $vars->counter++;
            $vars->foundTotalDirs++;

            QCR_ScanDirectories($l_FileName, $vars);
        } 
        elseif ($l_NeedToScan) {
            $vars->foundTotalFiles++;
            if (in_array($l_Ext, $g_ShortListExt)) {
                $l_DoorwayFilesCounter++;

                if ($l_DoorwayFilesCounter > MAX_ALLOWED_PHP_HTML_IN_DIR) {
                    $vars->doorway[]           = $l_SourceDirIndex;
                    $l_DoorwayFilesCounter = -655360;
                }
            }

            if (ONE_PASS) {
                QCR_ScanFile($l_FileName, $vars, null, $vars->counter++);
            } 
            else {
                $l_Buffer .= FilepathEscaper::encodeFilepathByBase64($l_FileName) . "\n";
            }

            $vars->counter++;
        }

        if (strlen($l_Buffer) > 32000) {
            file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
            $l_Buffer = '';
        }

    }

    closedir($l_DIRH);

    if (($l_RootDir == ROOT_PATH) && !empty($l_Buffer)) {
        file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
        $l_Buffer = '';
    }

}


///////////////////////////////////////////////////////////////////////////
function getFragment($par_Content, $par_Pos) {
//echo "\n *********** --------------------------------------------------------\n";

    $l_MaxChars = MAX_PREVIEW_LEN;

    $par_Content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '~', $par_Content);

    $l_MaxLen   = strlen($par_Content);
    $l_RightPos = min($par_Pos + $l_MaxChars, $l_MaxLen);
    $l_MinPos   = max(0, $par_Pos - $l_MaxChars);

    $l_FoundStart = substr($par_Content, 0, $par_Pos);
    $l_FoundStart = str_replace("\r", '', $l_FoundStart);
    $l_LineNo     = strlen($l_FoundStart) - strlen(str_replace("\n", '', $l_FoundStart)) + 1;

//echo "\nMinPos=" . $l_MinPos . " Pos=" . $par_Pos . " l_RightPos=" . $l_RightPos . "\n";
//var_dump($par_Content);
//echo "\n-----------------------------------------------------\n";


    $l_Res = '__AI_LINE1__' . $l_LineNo . "__AI_LINE2__  " . ($l_MinPos > 0 ? '…' : '') . substr($par_Content, $l_MinPos, $par_Pos - $l_MinPos) . '__AI_MARKER__' . substr($par_Content, $par_Pos, $l_RightPos - $par_Pos - 1);

    $l_Res = makeSafeFn(UnwrapObfu($l_Res));

    $l_Res = str_replace('~', ' ', $l_Res);

    $l_Res = preg_replace('~[\s\t]+~', ' ', $l_Res);

    $l_Res = str_replace('' . '?php', '' . '?php ', $l_Res);

//echo "\nFinal:\n";
//var_dump($l_Res);
//echo "\n-----------------------------------------------------\n";
    return $l_Res;
}

///////////////////////////////////////////////////////////////////////////
function escapedHexToHex($escaped) {
    $GLOBALS['g_EncObfu']++;
    return chr(hexdec($escaped[1]));
}
function escapedOctDec($escaped) {
    $GLOBALS['g_EncObfu']++;
    return chr(octdec($escaped[1]));
}
function escapedDec($escaped) {
    $GLOBALS['g_EncObfu']++;
    return chr($escaped[1]);
}

///////////////////////////////////////////////////////////////////////////
if (!defined('T_ML_COMMENT')) {
    define('T_ML_COMMENT', T_COMMENT);
} else {
    define('T_DOC_COMMENT', T_ML_COMMENT);
}

function UnwrapObfu($par_Content) {
    $GLOBALS['g_EncObfu'] = 0;

    $search      = array(
        ' ;',
        ' =',
        ' ,',
        ' .',
        ' (',
        ' )',
        ' {',
        ' }',
        '; ',
        '= ',
        ', ',
        '. ',
        '( ',
        '( ',
        '{ ',
        '} ',
        ' !',
        ' >',
        ' <',
        ' _',
        '_ ',
        '< ',
        '> ',
        ' $',
        ' %',
        '% ',
        '# ',
        ' #',
        '^ ',
        ' ^',
        ' &',
        '& ',
        ' ?',
        '? '
    );
    $replace     = array(
        ';',
        '=',
        ',',
        '.',
        '(',
        ')',
        '{',
        '}',
        ';',
        '=',
        ',',
        '.',
        '(',
        ')',
        '{',
        '}',
        '!',
        '>',
        '<',
        '_',
        '_',
        '<',
        '>',
        '$',
        '%',
        '%',
        '#',
        '#',
        '^',
        '^',
        '&',
        '&',
        '?',
        '?'
    );
    $par_Content = str_replace('@', '', $par_Content);
    $par_Content = preg_replace('~\s+~smi', ' ', $par_Content);
    $par_Content = str_replace($search, $replace, $par_Content);
    $par_Content = preg_replace_callback('~\bchr\(\s*([0-9a-fA-FxX]+)\s*\)~', function($m) {
        return "'" . chr(intval($m[1], 0)) . "'";
    }, $par_Content);

    $par_Content = preg_replace_callback('/\\\\x([a-fA-F0-9]{1,2})/i', 'escapedHexToHex', $par_Content);
    $par_Content = preg_replace_callback('/\\\\([0-9]{1,3})/i', 'escapedOctDec', $par_Content);

    $par_Content = preg_replace('/[\'"]\s*?\.+\s*?[\'"]/smi', '', $par_Content);
    $par_Content = preg_replace('/[\'"]\s*?\++\s*?[\'"]/smi', '', $par_Content);

    $content = str_replace('<?$', '<?php$', $content);
    $content = str_replace('<?php', '<?php ', $content);

    return $par_Content;
}

///////////////////////////////////////////////////////////////////////////
// Unicode BOM is U+FEFF, but after encoded, it will look like this.
define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));

function detect_utf_encoding($text) {
    $first2 = substr($text, 0, 2);
    $first3 = substr($text, 0, 3);
    $first4 = substr($text, 0, 3);

    if ($first3 == UTF8_BOM)
        return 'UTF-8';
    elseif ($first4 == UTF32_BIG_ENDIAN_BOM)
        return 'UTF-32BE';
    elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM)
        return 'UTF-32LE';
    elseif ($first2 == UTF16_BIG_ENDIAN_BOM)
        return 'UTF-16BE';
    elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM)
        return 'UTF-16LE';

    return false;
}

///////////////////////////////////////////////////////////////////////////
function QCR_SearchPHP($src) {
    if (preg_match("/(<\?php[\w\s]{5,})/smi", $src, $l_Found, PREG_OFFSET_CAPTURE)) {
        return $l_Found[0][1];
    }

    if (preg_match("/(<script[^>]*language\s*=\s*)('|\"|)php('|\"|)([^>]*>)/i", $src, $l_Found, PREG_OFFSET_CAPTURE)) {
        return $l_Found[0][1];
    }

    return false;
}


///////////////////////////////////////////////////////////////////////////
function knowUrl($par_URL) {
    global $g_UrlIgnoreList;

    for ($jk = 0; $jk < count($g_UrlIgnoreList); $jk++) {
        if (stripos($par_URL, $g_UrlIgnoreList[$jk]) !== false) {
            return true;
        }
    }

    return false;
}

///////////////////////////////////////////////////////////////////////////

function makeSummary($par_Str, $par_Number, $par_Style) {
    return '<tr><td class="' . $par_Style . '" width=400>' . $par_Str . '</td><td class="' . $par_Style . '">' . $par_Number . '</td></tr>';
}

///////////////////////////////////////////////////////////////////////////

function CheckVulnerability($par_Filename, $par_Index, $par_Content, $vars) {
    global $g_CmsListDetector;


    $l_Vuln = array();

    $par_Filename = strtolower($par_Filename);

    if ((strpos($par_Filename, 'libraries/joomla/session/session.php') !== false) && (strpos($par_Content, '&& filter_var($_SERVER[\'HTTP_X_FORWARDED_FOR') === false)) {
        $l_Vuln['id']   = 'RCE : https://docs.joomla.org/Security_hotfixes_for_Joomla_EOL_versions';
        $l_Vuln['ndx']  = $par_Index;
        $vars->vulnerable[] = $l_Vuln;
        return true;
    }

    if ((strpos($par_Filename, 'administrator/components/com_media/helpers/media.php') !== false) && (strpos($par_Content, '$format == \'\' || $format == false ||') === false)) {
        if ($g_CmsListDetector->isCms(CmsVersionDetector::CMS_JOOMLA, '1.5')) {
            $l_Vuln['id']   = 'AFU : https://docs.joomla.org/Security_hotfixes_for_Joomla_EOL_versions';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if ((strpos($par_Filename, 'joomla/filesystem/file.php') !== false) && (strpos($par_Content, '$file = rtrim($file, \'.\');') === false)) {
        if ($g_CmsListDetector->isCms(CmsVersionDetector::CMS_JOOMLA, '1.5')) {
            $l_Vuln['id']   = 'AFU : https://docs.joomla.org/Security_hotfixes_for_Joomla_EOL_versions';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if ((strpos($par_Filename, 'editor/filemanager/upload/test.html') !== false) || (stripos($par_Filename, 'editor/filemanager/browser/default/connectors/php/') !== false) || (stripos($par_Filename, 'editor/filemanager/connectors/uploadtest.html') !== false) || (strpos($par_Filename, 'editor/filemanager/browser/default/connectors/test.html') !== false)) {
        $l_Vuln['id']   = 'AFU : FCKEDITOR : http://www.exploit-db.com/exploits/17644/ & /exploit/249';
        $l_Vuln['ndx']  = $par_Index;
        $vars->vulnerable[] = $l_Vuln;
        return true;
    }

    if ((strpos($par_Filename, 'inc_php/image_view.class.php') !== false) || (strpos($par_Filename, '/inc_php/framework/image_view.class.php') !== false)) {
        if (strpos($par_Content, 'showImageByID') === false) {
            $l_Vuln['id']   = 'AFU : REVSLIDER : http://www.exploit-db.com/exploits/35385/';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if ((strpos($par_Filename, 'elfinder/php/connector.php') !== false) || (strpos($par_Filename, 'elfinder/elfinder.') !== false)) {
        $l_Vuln['id']   = 'AFU : elFinder';
        $l_Vuln['ndx']  = $par_Index;
        $vars->vulnerable[] = $l_Vuln;
        return true;
    }

    if (strpos($par_Filename, 'includes/database/database.inc') !== false) {
        if (strpos($par_Content, 'foreach ($data as $i => $value)') !== false) {
            $l_Vuln['id']   = 'SQLI : DRUPAL : CVE-2014-3704';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, 'engine/classes/min/index.php') !== false) {
        if (strpos($par_Content, 'tr_replace(chr(0)') === false) {
            $l_Vuln['id']   = 'AFD : MINIFY : CVE-2013-6619';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if ((strpos($par_Filename, 'timthumb.php') !== false) || (strpos($par_Filename, 'thumb.php') !== false) || (strpos($par_Filename, 'cache.php') !== false) || (strpos($par_Filename, '_img.php') !== false)) {
        if (strpos($par_Content, 'code.google.com/p/timthumb') !== false && strpos($par_Content, '2.8.14') === false) {
            $l_Vuln['id']   = 'RCE : TIMTHUMB : CVE-2011-4106,CVE-2014-4663';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, 'components/com_rsform/helpers/rsform.php') !== false) {
        if (preg_match('~define\s*\(\s*\'_rsform_version\'\s*,\s*\'([^\']+)\'\s*\)\s*;~msi', $par_Content, $version)) {
            $version = $version[1];
            if (version_compare($version, '1.5.2') !== 1) {
                $l_Vuln['id']   = 'RCE : RSFORM : rsform.php, LINE 1605';
                $l_Vuln['ndx']  = $par_Index;
                $vars->vulnerable[] = $l_Vuln;
                return true;
            }
        }
        return false;
    }


    if (strpos($par_Filename, 'fancybox-for-wordpress/fancybox.php') !== false) {
        if (strpos($par_Content, '\'reset\' == $_REQUEST[\'action\']') !== false) {
            $l_Vuln['id']   = 'CODE INJECTION : FANCYBOX';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }


    if (strpos($par_Filename, 'cherry-plugin/admin/import-export/upload.php') !== false) {
        if (strpos($par_Content, 'verify nonce') === false) {
            $l_Vuln['id']   = 'AFU : Cherry Plugin';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }


    if (strpos($par_Filename, 'tiny_mce/plugins/tinybrowser/tinybrowser.php') !== false) {
        $l_Vuln['id']   = 'AFU : TINYMCE : http://www.exploit-db.com/exploits/9296/';
        $l_Vuln['ndx']  = $par_Index;
        $vars->vulnerable[] = $l_Vuln;

        return true;
    }

    if (strpos($par_Filename, '/bx_1c_import.php') !== false) {
        if (strpos($par_Content, '$_GET[\'action\']=="getfiles"') !== false) {
            $l_Vuln['id']   = 'AFD : https://habrahabr.ru/company/dsec/blog/326166/';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;

            return true;
        }
    }

    if (strpos($par_Filename, 'scripts/setup.php') !== false) {
        if (strpos($par_Content, 'PMA_Config') !== false) {
            $l_Vuln['id']   = 'CODE INJECTION : PHPMYADMIN : http://1337day.com/exploit/5334';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, '/uploadify.php') !== false) {
        if (strpos($par_Content, 'move_uploaded_file($tempFile,$targetFile') !== false) {
            $l_Vuln['id']   = 'AFU : UPLOADIFY : CVE: 2012-1153';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, 'com_adsmanager/controller.php') !== false) {
        if (strpos($par_Content, 'move_uploaded_file($file[\'tmp_name\'], $tempPath.\'/\'.basename($file[') !== false) {
            $l_Vuln['id']   = 'AFU : https://revisium.com/ru/blog/adsmanager_afu.html';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, 'wp-content/plugins/wp-mobile-detector/resize.php') !== false) {
        if (strpos($par_Content, 'file_put_contents($path, file_get_contents($_REQUEST[\'src\']));') !== false) {
            $l_Vuln['id']   = 'AFU : https://www.pluginvulnerabilities.com/2016/05/31/aribitrary-file-upload-vulnerability-in-wp-mobile-detector/';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }


    if (strpos($par_Filename, 'core/lib/drupal.php') !== false) {
        $version = '';
        if (preg_match('|VERSION\s*=\s*\'(8\.\d+\.\d+)\'|smi', $par_Content, $tmp_ver)) {
            $version = $tmp_ver[1];
        }

        if (($version !== '') && (version_compare($version, '8.5.1', '<'))) {
            $l_Vuln['id']   = 'Drupageddon 2 : SA-CORE-2018–002';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }


        return false;
    }

    if (strpos($par_Filename, 'changelog.txt') !== false) {
        $version = '';
        if (preg_match('|Drupal\s+(7\.\d+),|smi', $par_Content, $tmp_ver)) {
            $version = $tmp_ver[1];
        }

        if (($version !== '') && (version_compare($version, '7.58', '<'))) {
            $l_Vuln['id']   = 'Drupageddon 2 : SA-CORE-2018–002';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, 'phpmailer.php') !== false) {
        $l_Detect = false;
        if (strpos($par_Content, 'PHPMailer') !== false) {
            $l_Found = preg_match('~Version:\s*(\d+)\.(\d+)\.(\d+)~', $par_Content, $l_Match);

            if ($l_Found) {
                $l_Version = $l_Match[1] * 1000 + $l_Match[2] * 100 + $l_Match[3];

                if ($l_Version < 2520) {
                    $l_Detect = true;
                }
            }

            if (!$l_Found) {

                $l_Found = preg_match('~Version\s*=\s*\'(\d+)\.*(\d+)\.(\d+)~i', $par_Content, $l_Match);
                if ($l_Found) {
                    $l_Version = $l_Match[1] * 1000 + $l_Match[2] * 100 + $l_Match[3];
                    if ($l_Version < 5220) {
                        $l_Detect = true;
                    }
                }
            }


            if ($l_Detect) {
                $l_Vuln['id']   = 'RCE : CVE-2016-10045, CVE-2016-10031';
                $l_Vuln['ndx']  = $par_Index;
                $vars->vulnerable[] = $l_Vuln;
                return true;
            }
        }

        return false;
    }
}

///////////////////////////////////////////////////////////////////////////
function CloudAssitedFilter($files_list, &$vars)
{
    $black_files = [];
    $white_files = [];
    try {
        $car                = Factory::instance()->create(CloudAssistedRequest::class, [CLOUD_ASSIST_TOKEN]);
        $cloud_assist_files = new CloudAssistedFiles($car, $files_list);
        $white_files        = $cloud_assist_files->getWhiteList();
        $black_files        = $cloud_assist_files->getBlackList();
        unset($cloud_assist_files);
    }
    catch (\Exception $e) {
        QCR_Debug($e->getMessage());
    }
    $vars->blackFiles = array_merge($vars->blackFiles, $black_files);
    return array_diff($files_list, array_keys($black_files), array_keys($white_files));
}

///////////////////////////////////////////////////////////////////////////
function QCR_GoScan($s_file, $vars, $callback = null, $base64_encoded = true, $skip_first_line = false)
{
    QCR_Debug('QCR_GoScan ');
    try {
        $i = 0;
        $filesForCloudAssistedScan = [];

        foreach ($s_file as $index => $filepath_encoded) {
            if ($skip_first_line && $index == 0) {
                $i = 1;
                continue;
            }

            $filepath = $base64_encoded ? FilepathEscaper::decodeFilepathByBase64($filepath_encoded) : $filepath_encoded;
            $filepath = trim($filepath);

            if (!file_exists($filepath) || !is_file($filepath) || !is_readable($filepath)) {
                stdOut("Error:" . $filepath . " either is not a file or readable");
                continue;
            }
            
            $filesize = filesize($filepath);
            if ($filesize > MAX_FILE_SIZE_FOR_CHECK) {
                stdOut('Error:' . $filepath . ' is too big');
                continue;
            }

            if (substr($filepath, -1) == DIR_SEPARATOR || !defined('CLOUD_ASSIST_TOKEN')) {
                QCR_ScanFile($filepath, $vars, $callback, $i++);
                continue;
            }
            
            if (isFileTooBigForCloudscan($filesize)) {
                QCR_ScanFile($filepath, $vars, $callback, $i++);
                continue;
            }

            // collecting files to scan with Cloud Assistant
            $filesForCloudAssistedScan[] = $filepath;
        }

        if (count($filesForCloudAssistedScan) == 0) {
            return;
        }

        if (defined('RAPID_ACCOUNT_SCAN')) {
            $storage = Factory::instance()->create(RapidScanStorage::class, [RAPID_ACCOUNT_SCAN]);
            /** @var RapidAccountScan $scanner */
            $scanner = Factory::instance()->create(RapidAccountScan::class, [$storage, &$vars, $i]);
            $scanner->scan($filesForCloudAssistedScan, $vars, constant('RapidAccountScan::RESCAN_' . RAPID_ACCOUNT_SCAN_TYPE));
            if ($scanner->getStrError()) {
                QCR_Debug('Rapid scan log: ' . $scanner->getStrError());
            }
            $vars->rescanCount += $scanner->getRescanCount();
        } else {
            $scan_bufer_files = function ($files_list, &$i) use ($callback, $vars) {
                $files_to_scan = CloudAssitedFilter($files_list, $vars);
                foreach ($files_to_scan as $filepath) {
                    QCR_ScanFile($filepath, $vars, $callback, $i++);
                }
            };
            $files_bufer = [];
            foreach ($filesForCloudAssistedScan as $l_Filename) {
                $files_bufer[] = $l_Filename;
                if (count($files_bufer) >= CLOUD_ASSIST_LIMIT) {
                    $scan_bufer_files($files_bufer, $i);
                    $files_bufer = [];
                }
            }
            if (count($files_bufer)) {
                $scan_bufer_files($files_bufer, $i);
            }
            unset($files_bufer);
        }
    } catch (Exception $e) {
        QCR_Debug($e->getMessage());
    }
}

///////////////////////////////////////////////////////////////////////////
function QCR_ScanFile($l_Filename, $vars, $callback = null, $i = 0, $show_progress = true)
{
    static $_files_and_ignored = 0;
    
    $return = array(RapidScanStorageRecord::RX_GOOD, '', '');

    $g_Content = '';
    $vars->crc = 0;

    $l_CriticalDetected = false;
    $l_Stat             = stat($l_Filename);

    if (substr($l_Filename, -1) == DIR_SEPARATOR) {
        // FOLDER
        $vars->structure['n'][$i] = $l_Filename;
        $vars->totalFolder++;
        printProgress($_files_and_ignored, $l_Filename, $vars);

        return null;
    }

    QCR_Debug('Scan file ' . $l_Filename);
    if ($show_progress) {
        printProgress(++$_files_and_ignored, $l_Filename, $vars);
    }

    $fd = @fopen($l_Filename, 'r');
    $firstFourBytes = @fread($fd, 4);
    @fclose($fd);

    if ($firstFourBytes === chr(127) . 'ELF') {
        if(defined('USE_HEURISTICS') || defined('USE_HEURISTICS_SUSPICIOUS')) {
            $vars->crc = sha1_file($l_Filename);
            AddResult($l_Filename, $i, $vars, $g_Content);

            if (defined('USE_HEURISTICS')) {
                $vars->criticalPHP[] = $i;
                $vars->criticalPHPFragment[] = 'SMW-HEUR-ELF';
                $vars->criticalPHPSig[] = 'SMW-HEUR-ELF';
            }

            if (defined('USE_HEURISTICS_SUSPICIOUS')) {
                $vars->warningPHP[] = $i;
                $vars->warningPHPFragment[] = 'SMW-HEUR-ELF';
                $vars->warningPHPSig[] = 'SMW-HEUR-ELF';
            }

            $return = array(RapidScanStorageRecord::HEURISTIC, 'SMW-HEUR-ELF', 'SMW-HEUR-ELF');

            return $return;
        }

        return null;
    }

    // FILE
    $is_too_big = isFileTooBigForScanWithSignatures($l_Stat['size']);
    $hash = sha1_file($l_Filename);
    if (check_binmalware($hash, $vars)) {
        $vars->totalFiles++;

        $vars->crc = $hash;

        AddResult($l_Filename, $i, $vars, $g_Content);

        $vars->criticalPHP[] = $i;
        $vars->criticalPHPFragment[] = "BIN-" . $vars->crc;
        $vars->criticalPHPSig[] = "bin_" . $vars->crc;
        $return = array(RapidScanStorageRecord::RX_MALWARE, "bin_" . $vars->crc, "BIN-" . $vars->crc);
    } elseif (!MAX_SIZE_SCAN_BYTES && $is_too_big) {
        $vars->bigFiles[] = $i;

        if (function_exists('aibolit_onBigFile')) {
            aibolit_onBigFile($l_Filename);
        }

        AddResult($l_Filename, $i, $vars, $g_Content);

        /** @var CriticalFileSpecification $criticalFileSpecification */
        $criticalFileSpecification = Factory::instance()->create(CriticalFileSpecification::class);
        if ((!AI_HOSTER) && $criticalFileSpecification->satisfiedBy($l_Filename)) {
            $vars->criticalPHP[]         = $i;
            $vars->criticalPHPFragment[] = "BIG FILE. SKIPPED.";
            $vars->criticalPHPSig[]      = "big_1";
        }
    } else {
        $vars->totalFiles++;

        $l_TSStartScan = microtime(true);

        $l_Ext = strtolower(pathinfo($l_Filename, PATHINFO_EXTENSION));
        $l_Content = '';

        if (filetype($l_Filename) == 'file') {
            if ($is_too_big && MAX_SIZE_SCAN_BYTES) {
                $handle     = @fopen($l_Filename, 'r');
                $l_Content  = @fread($handle, MAX_SIZE_SCAN_BYTES);
                @fclose($handle);
            } else {
                $l_Content  = @file_get_contents($l_Filename);
            }
            $l_Unwrapped = @php_strip_whitespace($l_Filename);
            $g_Content = $l_Content;
        }

        if (($l_Content == '' || $l_Unwrapped == '') && $l_Stat['size'] > 0) {
            $vars->notRead[] = $i;
            if (function_exists('aibolit_onReadError')) {
                aibolit_onReadError($l_Filename, 'io');
            }
            $return = array(RapidScanStorageRecord::CONFLICT, 'notread','');
            AddResult('[io] ' . $l_Filename, $i, $vars, $g_Content);
            return $return;
        }

        // ignore itself
        if (strpos($l_Content, '2737685240d323859b4cef650a98a314') !== false) {
            return false;
        }

        $vars->crc = _hash_($l_Unwrapped);

        $l_UnicodeContent = detect_utf_encoding($l_Content);
        //$l_Unwrapped = $l_Content;

        // check vulnerability in files
        $l_CriticalDetected = CheckVulnerability($l_Filename, $i, $l_Content, $vars);

        if ($l_UnicodeContent !== false) {
            if (function_exists('iconv')) {
                $l_Unwrapped = iconv($l_UnicodeContent, "CP1251//IGNORE", $l_Unwrapped);
            } else {
                $vars->notRead[] = $i;
                if (function_exists('aibolit_onReadError')) {
                    aibolit_onReadError($l_Filename, 'ec');
                }
                $return = array(RapidScanStorageRecord::CONFLICT, 'no_iconv', '');
                AddResult('[ec] ' . $l_Filename, $i, $vars, $g_Content);
            }
        }

        // critical
        $g_SkipNextCheck = false;

        if ((!AI_HOSTER) || AI_DEOBFUSCATE) {
            $l_DeobfObj = new Deobfuscator($l_Unwrapped, $l_Content);
            $l_DeobfType = $l_DeobfObj->getObfuscateType($l_Unwrapped);
        }

        if ($l_DeobfType != '') {
            $hangs = 0;
            while($l_DeobfObj->getObfuscateType($l_Unwrapped)!=='' && $hangs < 10) {
                $l_Unwrapped = $l_DeobfObj->deobfuscate();
                $l_DeobfObj = new Deobfuscator($l_Unwrapped);
                $hangs++;
            }
            $g_SkipNextCheck = checkFalsePositives($l_Filename, $l_Unwrapped, $l_DeobfType);
        } else {
            if (DEBUG_MODE) {
                stdOut("\n...... NOT OBFUSCATED\n");
            }
        }

        $l_Unwrapped = UnwrapObfu($l_Unwrapped);

        if ((!$g_SkipNextCheck) && CriticalPHP($l_Filename, $i, $l_Unwrapped, $l_Pos, $l_SigId)) {
            if ($l_Ext == 'js') {
                $vars->criticalJS[]         = $i;
                $vars->criticalJSFragment[] = getFragment($l_Unwrapped, $l_Pos);
                $vars->criticalJSSig[]      = $l_SigId;
            } else {
                $vars->criticalPHP[]         = $i;
                $vars->criticalPHPFragment[] = getFragment($l_Unwrapped, $l_Pos);
                $vars->criticalPHPSig[]      = $l_SigId;
            }
            $return = array(RapidScanStorageRecord::RX_MALWARE, $l_SigId, getFragment($l_Unwrapped, $l_Pos));
            $g_SkipNextCheck = true;
        } else {
            if ((!$g_SkipNextCheck) && CriticalPHP($l_Filename, $i, $l_Content, $l_Pos, $l_SigId)) {
                if ($l_Ext == 'js') {
                    $vars->criticalJS[]         = $i;
                    $vars->criticalJSFragment[] = getFragment($l_Content, $l_Pos);
                    $vars->criticalJSSig[]      = $l_SigId;
                } else {
                    $vars->criticalPHP[]         = $i;
                    $vars->criticalPHPFragment[] = getFragment($l_Content, $l_Pos);
                    $vars->criticalPHPSig[]      = $l_SigId;
                }
                $return = array(RapidScanStorageRecord::RX_MALWARE, $l_SigId, getFragment($l_Content, $l_Pos));
                $g_SkipNextCheck = true;
            }
        }

        $l_TypeDe = 0;

        // critical JS
        if (!$g_SkipNextCheck) {
            $l_Pos = CriticalJS($l_Filename, $i, $l_Unwrapped, $l_SigId);
            if ($l_Pos !== false) {
                if ($l_Ext == 'js') {
                    $vars->criticalJS[]         = $i;
                    $vars->criticalJSFragment[] = getFragment($l_Unwrapped, $l_Pos);
                    $vars->criticalJSSig[]      = $l_SigId;
                } else {
                    $vars->criticalPHP[]         = $i;
                    $vars->criticalPHPFragment[] = getFragment($l_Unwrapped, $l_Pos);
                    $vars->criticalPHPSig[]      = $l_SigId;
                }
                $return = array(RapidScanStorageRecord::RX_MALWARE, $l_SigId, getFragment($l_Unwrapped, $l_Pos));
                $g_SkipNextCheck = true;
            }
        }

        // warnings (suspicious)
        if (!$g_SkipNextCheck) {
            $l_Pos = WarningPHP($l_Filename, $i, $l_Unwrapped, $l_SigId);
            if ($l_Pos !== false) {
                $vars->warningPHP[]         = $i;
                $vars->warningPHPFragment[] = getFragment($l_Unwrapped, $l_Pos);
                $vars->warningPHPSig[]      = $l_SigId;

                $return = array(RapidScanStorageRecord::RX_SUSPICIOUS, $l_SigId, getFragment($l_Unwrapped, $l_Pos)) ;
                $g_SkipNextCheck = true;
            }
        }

        // phishing
        if (!$g_SkipNextCheck) {
            $l_Pos = Phishing($l_Filename, $i, $l_Unwrapped, $l_SigId, $vars);
            if ($l_Pos === false) {
                $l_Pos = Phishing($l_Filename, $i, $l_Content, $l_SigId, $vars);
            }

            if ($l_Pos !== false) {
                $vars->phishing[]            = $i;
                $vars->phishingFragment[]    = getFragment($l_Unwrapped, $l_Pos);
                $vars->phishingSigFragment[] = $l_SigId;

                $return = array(RapidScanStorageRecord::RX_SUSPICIOUS, $l_SigId, getFragment($l_Unwrapped, $l_Pos));
                $g_SkipNextCheck         = true;
            }
        }

        if (!$g_SkipNextCheck) {
            // warnings
            $l_Pos = '';

            // adware
            if (Adware($l_Filename, $l_Unwrapped, $l_Pos)) {
                $vars->adwareList[]         = $i;
                $vars->adwareListFragment[] = getFragment($l_Unwrapped, $l_Pos);
                $l_CriticalDetected     = true;
            }

            // articles
            if (stripos($l_Filename, 'article_index')) {
                $vars->adwareList[]     = $i;
                $l_CriticalDetected = true;
            }
        }
    } // end of if (!$g_SkipNextCheck) {

    //printProgress(++$_files_and_ignored, $l_Filename);
    delayWithCallback(SCAN_DELAY, $callback);
    $l_TSEndScan = microtime(true);
    if ($l_TSEndScan - $l_TSStartScan >= 0.5) {
        delayWithCallback(SCAN_DELAY, $callback);
    }

    if ($g_SkipNextCheck || $l_CriticalDetected) {
        AddResult($l_Filename, $i, $vars, $g_Content);
    }

    unset($l_Unwrapped);
    unset($l_Content);

    return $return;
}

function callCallback($callback)
{
    if ($callback !== null) {
        call_user_func($callback);
    }
}

function delayWithCallback($delay, $callback)
{
    $delay = $delay * 1000;
    callCallback($callback);
    while ($delay > 500000) {
        $delay -= 500000;
        usleep(500000);
        callCallback($callback);
    }
    usleep($delay);
    callCallback($callback);
}

function AddResult($l_Filename, $i, $vars, $g_Content = '')
{
    $l_Stat                 = stat($l_Filename);
    if (!isFileTooBigForScanWithSignatures($l_Stat['size']) && $g_Content == '') {
        $g_Content = file_get_contents($l_Filename);
    }
    $vars->structure['n'][$i]   = $l_Filename;
    $vars->structure['s'][$i]   = $l_Stat['size'];
    $vars->structure['c'][$i]   = $l_Stat['ctime'];
    $vars->structure['m'][$i]   = $l_Stat['mtime'];
    $vars->structure['e'][$i]   = time();
    $vars->structure['crc'][$i] = $vars->crc;

    if ($g_Content !== '') {
        $vars->structure['sha256'][$i] = hash('sha256', $g_Content);
        $g_Content = '';
    }
}

///////////////////////////////////////////////////////////////////////////
function WarningPHP($l_FN, $l_Index, $l_Content, &$l_SigId) {
    global $g_SusDB, $g_ExceptFlex, $gXX_FlexDBShe, $gX_FlexDBShe, $g_FlexDBShe, $gX_DBShe, $g_DBShe, $g_Base64, $g_Base64Fragment;

    if (AI_EXTRA_WARN) {
        foreach ($g_SusDB as $l_Item) {
            if (preg_match('~' . $l_Item . '~smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
                if (!CheckException($l_Content, $l_Found)) {
                    $l_Pos   = $l_Found[0][1];
                    $l_SigId = getSigId($l_Found);
                    return $l_Pos;
                }
            }
        }
    }
    return false;

}

///////////////////////////////////////////////////////////////////////////
function Adware($l_FN, $l_Content, &$l_Pos) {
    global $g_AdwareSig;

    $l_Res = false;

    foreach ($g_AdwareSig as $l_Item) {
        $offset = 0;
        while (preg_match('~' . $l_Item . '~smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {
            if (!CheckException($l_Content, $l_Found)) {
                $l_Pos = $l_Found[0][1];
                return true;
            }

            $offset = $l_Found[0][1] + 1;
        }
    }

    return $l_Res;
}

///////////////////////////////////////////////////////////////////////////
function CheckException(&$l_Content, &$l_Found) {
    global $g_ExceptFlex, $gX_FlexDBShe, $gXX_FlexDBShe, $g_FlexDBShe, $gX_DBShe, $g_DBShe, $g_Base64, $g_Base64Fragment;
    $l_FoundStrPlus = substr($l_Content, max($l_Found[0][1] - 10, 0), 70);

    foreach ($g_ExceptFlex as $l_ExceptItem) {
        if (@preg_match('~' . $l_ExceptItem . '~smi', $l_FoundStrPlus, $l_Detected)) {
            return true;
        }
    }

    return false;
}

///////////////////////////////////////////////////////////////////////////
function Phishing($l_FN, $l_Index, $l_Content, &$l_SigId, $vars) {
    global $g_PhishFiles, $g_PhishEntries, $g_PhishingSig;

    $l_Res = false;

    // need check file (by extension) ?
    $l_SkipCheck = SMART_SCAN;

    if ($l_SkipCheck) {
        foreach ($g_PhishFiles as $l_Ext) {
            if (strpos($l_FN, $l_Ext) !== false) {
                $l_SkipCheck = false;
                break;
            }
        }
    }

    // need check file (by signatures) ?
    if ($l_SkipCheck && preg_match('~' . $g_PhishEntries . '~smiS', $l_Content, $l_Found)) {
        $l_SkipCheck = false;
    }

    if ($l_SkipCheck && SMART_SCAN) {
        if (DEBUG_MODE) {
            echo "Skipped phs file, not critical.\n";
        }

        return false;
    }

    foreach ($g_PhishingSig as $l_Item) {
        $offset = 0;
        while (preg_match('~' . $l_Item . '~smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {
            if (!CheckException($l_Content, $l_Found)) {
                $l_Pos   = $l_Found[0][1];
                $l_SigId = getSigId($l_Found);

                if (DEBUG_MODE) {
                    echo "Phis: $l_FN matched [$l_Item] in $l_Pos\n";
                }

                return $l_Pos;
            }
            $offset = $l_Found[0][1] + 1;

        }
    }

    return $l_Res;
}

///////////////////////////////////////////////////////////////////////////
function CriticalJS($l_FN, $l_Index, $l_Content, &$l_SigId) {
    global $g_JSVirSig, $gX_JSVirSig, $g_VirusFiles, $g_VirusEntries, $g_RegExpStat;

    $l_Res = false;

    // need check file (by extension) ?
    $l_SkipCheck = SMART_SCAN;

    if ($l_SkipCheck) {
        foreach ($g_VirusFiles as $l_Ext) {
            if (strpos($l_FN, $l_Ext) !== false) {
                $l_SkipCheck = false;
                break;
            }
        }
    }

    // need check file (by signatures) ?
    if ($l_SkipCheck && preg_match('~' . $g_VirusEntries . '~smiS', $l_Content, $l_Found)) {
        $l_SkipCheck = false;
    }

    if ($l_SkipCheck && SMART_SCAN) {
        if (DEBUG_MODE) {
            echo "Skipped js file, not critical.\n";
        }

        return false;
    }


    foreach ($g_JSVirSig as $l_Item) {
        $offset = 0;
        if (DEBUG_PERFORMANCE) {
            $stat_start = microtime(true);
        }

        while (preg_match('~' . $l_Item . '~smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {

            if (!CheckException($l_Content, $l_Found)) {
                $l_Pos   = $l_Found[0][1];
                $l_SigId = getSigId($l_Found);

                if (DEBUG_MODE) {
                    echo "JS: $l_FN matched [$l_Item] in $l_Pos\n";
                }

                return $l_Pos;
            }

            $offset = $l_Found[0][1] + 1;

        }

        if (DEBUG_PERFORMANCE) {
            $stat_stop = microtime(true);
            $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
        }

    }

    if (AI_EXPERT > 1) {
        foreach ($gX_JSVirSig as $l_Item) {
            if (DEBUG_PERFORMANCE) {
                $stat_start = microtime(true);
            }

            if (preg_match('~' . $l_Item . '~smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
                if (!CheckException($l_Content, $l_Found)) {
                    $l_Pos   = $l_Found[0][1];
                    //$l_SigId = myCheckSum($l_Item);
                    $l_SigId = getSigId($l_Found);

                    if (DEBUG_MODE) {
                        echo "JS PARA: $l_FN matched [$l_Item] in $l_Pos\n";
                    }

                    return $l_Pos;
                }
            }

            if (DEBUG_PERFORMANCE) {
                $stat_stop = microtime(true);
                $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
            }

        }
    }

    return $l_Res;
}

////////////////////////////////////////////////////////////////////////////
define('SUSP_MTIME', 1); // suspicious mtime (greater than ctime)
define('SUSP_PERM', 2); // suspicious permissions 
define('SUSP_PHP_IN_UPLOAD', 3); // suspicious .php file in upload or image folder 

function get_descr_heur($type) {
    switch ($type) {
        case SUSP_MTIME:
            return AI_STR_077;
        case SUSP_PERM:
            return AI_STR_078;
        case SUSP_PHP_IN_UPLOAD:
            return AI_STR_079;
    }

    return "---";
}

///////////////////////////////////////////////////////////////////////////
function CriticalPHP($l_FN, $l_Index, $l_Content, &$l_Pos, &$l_SigId) {
    global $g_ExceptFlex, $gXX_FlexDBShe, $gX_FlexDBShe, $g_FlexDBShe, $gX_DBShe, $g_DBShe, $g_Base64, $g_Base64Fragment, $g_CriticalEntries, $g_RegExpStat;
    
    // need check file (by extension) ?
    $l_SkipCheck = SMART_SCAN;

    if ($l_SkipCheck) {
        /** @var CriticalFileSpecification $criticalFileSpecification */
        $criticalFileSpecification = Factory::instance()->create(CriticalFileSpecification::class);

        if ($criticalFileSpecification->satisfiedBy($l_FN) && (strpos($l_FN, '.js') === false)) {
            $l_SkipCheck = false;
        }
    }

    // need check file (by signatures) ?
    if ($l_SkipCheck && preg_match('~' . $g_CriticalEntries . '~smiS', $l_Content, $l_Found)) {
        $l_SkipCheck = false;
    }
    

    // if not critical - skip it 
    if ($l_SkipCheck && SMART_SCAN) {
        if (DEBUG_MODE) {
            echo "Skipped file, not critical.\n";
        }

        return false;
    }

    foreach ($g_FlexDBShe as $l_Item) {
        $offset = 0;

        if (DEBUG_PERFORMANCE) {
            $stat_start = microtime(true);
        }

        while (preg_match('~' . $l_Item . '~smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {
            if (!CheckException($l_Content, $l_Found)) {
                $l_Pos   = $l_Found[0][1];
                //$l_SigId = myCheckSum($l_Item);
                $l_SigId = getSigId($l_Found);

                if (DEBUG_MODE) {
                    echo "CRIT 1: $l_FN matched [$l_Item] in $l_Pos\n";
                }

                return true;
            }

            $offset = $l_Found[0][1] + 1;

        }

        if (DEBUG_PERFORMANCE) {
            $stat_stop = microtime(true);
            $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
        }

    }

    if (AI_EXPERT > 0) {
        foreach ($gX_FlexDBShe as $l_Item) {
            if (DEBUG_PERFORMANCE) {
                $stat_start = microtime(true);
            }

            if (preg_match('~' . $l_Item . '~smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
                if (!CheckException($l_Content, $l_Found)) {
                    $l_Pos   = $l_Found[0][1];
                    $l_SigId = getSigId($l_Found);

                    if (DEBUG_MODE) {
                        echo "CRIT 3: $l_FN matched [$l_Item] in $l_Pos\n";
                    }

                    return true;
                }
            }

            if (DEBUG_PERFORMANCE) {
                $stat_stop = microtime(true);
                $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
            }

        }
    }

    if (AI_EXPERT > 1) {
        foreach ($gXX_FlexDBShe as $l_Item) {
            if (DEBUG_PERFORMANCE) {
                $stat_start = microtime(true);
            }

            if (preg_match('~' . $l_Item . '~smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
                if (!CheckException($l_Content, $l_Found)) {
                    $l_Pos   = $l_Found[0][1];
                    $l_SigId = getSigId($l_Found);

                    if (DEBUG_MODE) {
                        echo "CRIT 2: $l_FN matched [$l_Item] in $l_Pos\n";
                    }

                    return true;
                }
            }

            if (DEBUG_PERFORMANCE) {
                $stat_stop = microtime(true);
                $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
            }

        }
    }

    $l_Content_lo = strtolower($l_Content);

    foreach ($g_DBShe as $l_Item) {
        $l_Pos = strpos($l_Content_lo, $l_Item);
        if ($l_Pos !== false) {
            $l_SigId = myCheckSum($l_Item);

            if (DEBUG_MODE) {
                echo "CRIT 4: $l_FN matched [$l_Item] in $l_Pos\n";
            }

            return true;
        }
    }

    if (AI_EXPERT > 0) {
        foreach ($gX_DBShe as $l_Item) {
            $l_Pos = strpos($l_Content_lo, $l_Item);
            if ($l_Pos !== false) {
                $l_SigId = myCheckSum($l_Item);

                if (DEBUG_MODE) {
                    echo "CRIT 5: $l_FN matched [$l_Item] in $l_Pos\n";
                }

                return true;
            }
        }
    }

    if (AI_HOSTER)
        return false;

    if (AI_EXPERT > 0) {
        if ((strpos($l_Content, 'GIF89') === 0) && (strpos($l_FN, '.php') !== false)) {
            $l_Pos = 0;

            if (DEBUG_MODE) {
                echo "CRIT 6: $l_FN matched [$l_Item] in $l_Pos\n";
            }

            return true;
        }
    }

    // detect uploaders / droppers
    if (AI_EXPERT > 1) {
        $l_Found = null;
        if ((filesize($l_FN) < 2048) && (strpos($l_FN, '.ph') !== false) && ((($l_Pos = strpos($l_Content, 'multipart/form-data')) > 0) || (($l_Pos = strpos($l_Content, '$_FILE[') > 0)) || (($l_Pos = strpos($l_Content, 'move_uploaded_file')) > 0) || (preg_match('|\bcopy\s*\(|smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)))) {
            if ($l_Found != null) {
                $l_Pos = $l_Found[0][1];
            }
            if (DEBUG_MODE) {
                echo "CRIT 7: $l_FN matched [$l_Item] in $l_Pos\n";
            }

            return true;
        }
    }

    return false;
}

///////////////////////////////////////////////////////////////////////////
if (!isCli()) {
    header('Content-type: text/html; charset=utf-8');
}

if (!isCli()) {

    $l_PassOK = false;
    if (strlen(PASS) > 8) {
        $l_PassOK = true;
    }

    if ($l_PassOK && preg_match('|[0-9]|', PASS, $l_Found) && preg_match('|[A-Z]|', PASS, $l_Found) && preg_match('|[a-z]|', PASS, $l_Found)) {
        $l_PassOK = true;
    }

    if (!$l_PassOK) {
        echo sprintf(AI_STR_009, generatePassword());
        exit;
    }

    if (isset($_GET['fn']) && ($_GET['ph'] == crc32(PASS))) {
        printFile();
        exit;
    }

    if ($_GET['p'] != PASS) {
        $generated_pass = generatePassword();
        echo sprintf(AI_STR_010, $generated_pass, $generated_pass);
        exit;
    }
}

if (!is_readable(ROOT_PATH)) {
    echo AI_STR_011;
    exit;
}

if (isCli()) {
    if (defined('REPORT_PATH') AND REPORT_PATH) {
        if (!is_writable(REPORT_PATH)) {
            die2("\nCannot write report. Report dir " . REPORT_PATH . " is not writable.");
        }

        else if (!REPORT_FILE) {
            die2("\nCannot write report. Report filename is empty.");
        }

        else if (($file = REPORT_PATH . DIR_SEPARATOR . REPORT_FILE) AND is_file($file) AND !is_writable($file)) {
            die2("\nCannot write report. Report file '$file' exists but is not writable.");
        }
    }
}


// detect version CMS
$g_KnownCMS        = array();
$tmp_cms           = array();
$g_CmsListDetector = new CmsVersionDetector(ROOT_PATH);
$l_CmsDetectedNum  = $g_CmsListDetector->getCmsNumber();
for ($tt = 0; $tt < $l_CmsDetectedNum; $tt++) {
    $vars->CMS[]                                              = $g_CmsListDetector->getCmsName($tt) . ' v' . makeSafeFn($g_CmsListDetector->getCmsVersion($tt));
    $tmp_cms[strtolower($g_CmsListDetector->getCmsName($tt))] = 1;
}

if (count($tmp_cms) > 0) {
    $g_KnownCMS = array_keys($tmp_cms);
    $len        = count($g_KnownCMS);
    for ($i = 0; $i < $len; $i++) {
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_WORDPRESS))
            $g_KnownCMS[] = 'wp';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_WEBASYST))
            $g_KnownCMS[] = 'shopscript';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_IPB))
            $g_KnownCMS[] = 'ipb';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_DLE))
            $g_KnownCMS[] = 'dle';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_INSTANTCMS))
            $g_KnownCMS[] = 'instantcms';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_SHOPSCRIPT))
            $g_KnownCMS[] = 'shopscript';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_DRUPAL))
            $g_KnownCMS[] = 'drupal';
    }
}


$g_DirIgnoreList = array();
$g_IgnoreList    = array();
$g_UrlIgnoreList = array();
$g_KnownList     = array();

$l_IgnoreFilename    = $g_AiBolitAbsolutePath . '/.aignore';
$l_DirIgnoreFilename = $g_AiBolitAbsolutePath . '/.adirignore';
$l_UrlIgnoreFilename = $g_AiBolitAbsolutePath . '/.aurlignore';

if (file_exists($l_IgnoreFilename)) {
    $l_IgnoreListRaw = file($l_IgnoreFilename);
    for ($i = 0; $i < count($l_IgnoreListRaw); $i++) {
        $g_IgnoreList[] = explode("\t", trim($l_IgnoreListRaw[$i]));
    }
    unset($l_IgnoreListRaw);
}

if (file_exists($l_DirIgnoreFilename)) {
    $g_DirIgnoreList = file($l_DirIgnoreFilename);

    for ($i = 0; $i < count($g_DirIgnoreList); $i++) {
        $g_DirIgnoreList[$i] = trim($g_DirIgnoreList[$i]);
    }
}

if (file_exists($l_UrlIgnoreFilename)) {
    $g_UrlIgnoreList = file($l_UrlIgnoreFilename);

    for ($i = 0; $i < count($g_UrlIgnoreList); $i++) {
        $g_UrlIgnoreList[$i] = trim($g_UrlIgnoreList[$i]);
    }
}


$l_SkipMask = array(
    '/template_\w{32}.css',
    '/cache/templates/.{1,150}\.tpl\.php',
    '/system/cache/templates_c/\w{1,40}\.php',
    '/assets/cache/rss/\w{1,60}',
    '/cache/minify/minify_\w{32}',
    '/cache/page/\w{32}\.php',
    '/cache/object/\w{1,10}/\w{1,10}/\w{1,10}/\w{32}\.php',
    '/cache/wp-cache-\d{32}\.php',
    '/cache/page/\w{32}\.php_expire',
    '/cache/page/\w{32}-cache-page-\w{32}\.php',
    '\w{32}-cache-com_content-\w{32}\.php',
    '\w{32}-cache-mod_custom-\w{32}\.php',
    '\w{32}-cache-mod_templates-\w{32}\.php',
    '\w{32}-cache-_system-\w{32}\.php',
    '/cache/twig/\w{1,32}/\d+/\w{1,100}\.php',
    '/autoptimize/js/autoptimize_\w{32}\.js',
    '/bitrix/cache/\w{32}\.php',
    '/bitrix/cache/.{1,200}/\w{32}\.php',
    '/bitrix/cache/iblock_find/',
    '/bitrix/managed_cache/MYSQL/user_option/[^/]+/',
    '/bitrix/cache/s1/bitrix/catalog\.section/',
    '/bitrix/cache/s1/bitrix/catalog\.element/',
    '/bitrix/cache/s1/bitrix/menu/',
    '/catalog.element/[^/]+/[^/]+/\w{32}\.php',
    '/bitrix/managed\_cache/.{1,150}/\.\w{32}\.php',
    '/core/cache/mgr/smarty/default/.{1,100}\.tpl\.php',
    '/core/cache/resource/web/resources/[0-9]{1,50}\.cache\.php',
    '/smarty/compiled/SC/.{1,100}/%%.{1,200}\.php',
    '/smarty/.{1,150}\.tpl\.php',
    '/smarty/compile/.{1,150}\.tpl\.cache\.php',
    '/files/templates_c/.{1,150}\.html\.php',
    '/uploads/javascript_global/.{1,150}\.js',
    '/assets/cache/rss/\w{32}',
    'сore/cache/resource/web/resources/\d+\.cache\.php',
    '/assets/cache/docid_\d+_\w{32}\.pageCache\.php',
    '/t3-assets/dev/t3/.{1,150}-cache-\w{1,20}-.{1,150}\.php',
    '/t3-assets/js/js-\w{1,30}\.js',
    '/temp/cache/SC/.{1,100}/\.cache\..{1,100}\.php',
    '/tmp/sess\_\w{32}$',
    '/assets/cache/docid\_.{1,100}\.pageCache\.php',
    '/stat/usage\_\w{1,100}\.html',
    '/stat/site\_\w{1,100}\.html',
    '/gallery/item/list/\w{1,100}\.cache\.php',
    '/core/cache/registry/.{1,100}/ext-.{1,100}\.php',
    '/core/cache/resource/shk\_/\w{1,50}\.cache\.php',
    '/cache/\w{1,40}/\w+-cache-\w+-\w{32,40}\.php',
    '/webstat/awstats.{1,150}\.txt',
    '/awstats/awstats.{1,150}\.txt',
    '/awstats/.{1,80}\.pl',
    '/awstats/.{1,80}\.html',
    '/inc/min/styles_\w+\.min\.css',
    '/inc/min/styles_\w+\.min\.js',
    '/logs/error\_log\.',
    '/logs/xferlog\.',
    '/logs/access_log\.',
    '/logs/cron\.',
    '/logs/exceptions/.{1,200}\.log$',
    '/hyper-cache/[^/]{1,50}/[^/]{1,50}/[^/]{1,50}/index\.html',
    '/mail/new/[^,]+,S=[^,]+,W=',
    '/mail/new/[^,]=,S=',
    '/application/logs/\d+/\d+/\d+\.php',
    '/sites/default/files/js/js_\w{32}\.js',
    '/yt-assets/\w{32}\.css',
    '/wp-content/cache/object/\w{1,5}/\w{1,5}/\w{32}\.php',
    '/catalog\.section/\w{1,5}/\w{1,5}/\w{32}\.php',
    '/simpla/design/compiled/[\w\.]{40,60}\.php',
    '/compile/\w{2}/\w{2}/\w{2}/[\w.]{40,80}\.php',
    '/sys-temp/static-cache/[^/]{1,60}/userCache/[\w\./]{40,100}\.php',
    '/session/sess_\w{32}',
    '/webstat/awstats\.[\w\./]{3,100}\.html',
    '/stat/webalizer\.current',
    '/stat/usage_\d+\.html'
);

$l_SkipSample = array();

if (SMART_SCAN) {
    $g_DirIgnoreList = array_merge($g_DirIgnoreList, $l_SkipMask);
}

QCR_Debug();

// Load custom signatures
if (file_exists($g_AiBolitAbsolutePath . "/ai-bolit.sig")) {
    try {
        $s_file = new SplFileObject($g_AiBolitAbsolutePath . "/ai-bolit.sig");
        $s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        foreach ($s_file as $line) {
            $g_FlexDBShe[] = preg_replace('#\G(?:[^~\\\\]+|\\\\.)*+\K~#', '\\~', $line); // escaping ~
        }

        stdOut("Loaded " . $s_file->key() . " signatures from ai-bolit.sig");
        $s_file = null; // file handler is closed
    }
    catch (Exception $e) {
        QCR_Debug("Import ai-bolit.sig " . $e->getMessage());
    }
}

QCR_Debug();

$defaults['skip_ext'] = strtolower(trim($defaults['skip_ext']));
if ($defaults['skip_ext'] != '') {
    $g_IgnoredExt = explode(',', $defaults['skip_ext']);
    for ($i = 0; $i < count($g_IgnoredExt); $i++) {
        $g_IgnoredExt[$i] = trim($g_IgnoredExt[$i]);
    }

    QCR_Debug('Skip files with extensions: ' . implode(',', $g_IgnoredExt));
    stdOut('Skip extensions: ' . implode(',', $g_IgnoredExt));
}

// scan single file
/**
 * @param Variables $vars
 * @param array $g_IgnoredExt
 * @param array $g_DirIgnoreList
 */
function processIntegrity(Variables $vars, array $g_IgnoredExt, array $g_DirIgnoreList)
{
    global $g_IntegrityDB;
// INTEGRITY CHECK
    IMAKE and unlink(INTEGRITY_DB_FILE);
    ICHECK and load_integrity_db();
    QCR_IntegrityCheck(ROOT_PATH, $vars);
    stdOut("Found $vars->foundTotalFiles files in $vars->foundTotalDirs directories.");
    if (IMAKE) {
        exit(0);
    }
    if (ICHECK) {
        $i = $vars->counter;
        $vars->crc = 0;
        $changes = array();
        $ref =& $g_IntegrityDB;
        foreach ($g_IntegrityDB as $l_FileName => $type) {
            unset($g_IntegrityDB[$l_FileName]);
            $l_Ext2 = substr(strstr(basename($l_FileName), '.'), 1);
            if (in_array(strtolower($l_Ext2), $g_IgnoredExt)) {
                continue;
            }
            for ($dr = 0; $dr < count($g_DirIgnoreList); $dr++) {
                if (($g_DirIgnoreList[$dr] != '') && preg_match('#' . $g_DirIgnoreList[$dr] . '#', $l_FileName,
                        $l_Found)) {
                    continue 2;
                }
            }
            $type = in_array($type, array(
                'added',
                'modified'
            )) ? $type : 'deleted';
            $type .= substr($l_FileName, -1) == '/' ? 'Dirs' : 'Files';
            $changes[$type][] = ++$i;
            AddResult($l_FileName, $i, $vars);
        }
        $vars->foundTotalFiles = count($changes['addedFiles']) + count($changes['modifiedFiles']);
        stdOut("Found changes " . count($changes['modifiedFiles']) . " files and added " . count($changes['addedFiles']) . " files.");
    }
}

if (isset($_GET['2check'])) {
    $options['with-2check'] = 1;
}

$use_doublecheck = isset($options['with-2check']) && file_exists(DOUBLECHECK_FILE);
$use_listingfile = defined('LISTING_FILE');

$listing = false;

if ($use_doublecheck) {
    $listing = DOUBLECHECK_FILE;
} elseif ($use_listingfile) {
    $listing = LISTING_FILE;
}
$base64_encoded = INPUT_FILENAMES_BASE64_ENCODED;

try {
    if (defined('SCAN_FILE')) {
        // scan single file
        $filepath = INPUT_FILENAMES_BASE64_ENCODED ? FilepathEscaper::decodeFilepathByBase64(SCAN_FILE) : SCAN_FILE;
        stdOut("Start scanning file '" . $filepath . "'.");
        if (file_exists($filepath) && is_file($filepath) && is_readable($filepath)) {
            $s_file[] = $filepath;
            $base64_encoded = false;
        } else {
            stdOut("Error:" . $filepath . " either is not a file or readable");
        }
    } elseif ($listing) {
        //scan listing
        if ($listing == 'stdin') {
            $lines = explode("\n", getStdin());
        } else {
            $lines = new SplFileObject($listing);
            $lines->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        }
        if (is_array($lines)) {
            $vars->foundTotalFiles = count($lines);
        } else if ($lines instanceof SplFileObject) {
            $lines->seek($lines->getSize());
            $vars->foundTotalFiles = $lines->key();
            $lines->seek(0);
        }

        $s_file = $lines;
        stdOut("Start scanning the list from '" . $listing . "'.\n");
    } else {
        //scan by path
        $base64_encoded = true;
        file_exists(QUEUE_FILENAME) && unlink(QUEUE_FILENAME);
        QCR_ScanDirectories(ROOT_PATH, $vars);
        stdOut("Found $vars->foundTotalFiles files in $vars->foundTotalDirs directories.");
        stdOut("Start scanning '" . ROOT_PATH . "'.\n");
        if (ICHECK || IMAKE) {
            processIntegrity($vars);
        }

        QCR_Debug();
        stdOut(str_repeat(' ', 160), false);
        $s_file = new SplFileObject(QUEUE_FILENAME);
        $s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
    }

    QCR_GoScan($s_file, $vars, null, $base64_encoded, $use_doublecheck);
    unset($s_file);
    @unlink(QUEUE_FILENAME);
    $vars->foundTotalDirs  = $vars->totalFolder;

    if (defined('PROGRESS_LOG_FILE') && file_exists(PROGRESS_LOG_FILE)) {
        @unlink(PROGRESS_LOG_FILE);
    }
    if (CREATE_SHARED_MEMORY) {
        shmop_delete(SHARED_MEMORY);
    }
    if (defined('SHARED_MEMORY')) {
        shmop_close(SHARED_MEMORY);
    }
} catch (Exception $e) {
    QCR_Debug($e->getMessage());
}
QCR_Debug();

if (true) {
    $g_HeuristicDetected = array();
    $g_Iframer           = array();
    $g_Base64            = array();
}
/**
 * @param Variables $vars
 * @return array
 */
function whitelisting(Variables $vars)
{
// whitelist

    $snum = 0;
    $list = check_whitelist($vars->structure['crc'], $snum);
    $keys = array(
        'criticalPHP',
        'criticalJS',
        'g_Iframer',
        'g_Base64',
        'phishing',
        'adwareList',
        'g_Redirect',
        'warningPHP'
    );

    foreach ($keys as $p) {
        if (empty($vars->{$p})) {
            continue;
        }
        $p_Fragment = $p . 'Fragment';
        $p_Sig      = $p . 'Sig';
        
        if ($p == 'g_Redirect') {
            $p_Fragment = $p . 'PHPFragment';
        }
        elseif ($p == 'g_Phishing') {
            $p_Sig = $p . 'SigFragment';
        }

        $count = count($vars->{$p});
        for ($i = 0; $i < $count; $i++) {
            $id = $vars->{$p}[$i];
            if ($vars->structure['crc'][$id] !== 0 && in_array($vars->structure['crc'][$id], $list)) {
                unset($vars->{$p}[$i]);
                unset($vars->{$p_Sig}[$i]);
                unset($vars->{$p_Fragment}[$i]);
            }
        }

        $vars->{$p}             = array_values($vars->{$p});
        $vars->{$p_Fragment}    = array_values($vars->{$p_Fragment});
        if (!empty($vars->{$p_Sig})) {
            $vars->{$p_Sig} = array_values($vars->{$p_Sig});
        }
    }
    return array($snum, $i);
}

whitelisting($vars);


////////////////////////////////////////////////////////////////////////////
if (AI_HOSTER) {
    $g_IframerFragment       = array();
    $g_Iframer               = array();
    $vars->redirect          = array();
    $vars->doorway           = array();
    $g_EmptyLink             = array();
    $g_HeuristicType         = array();
    $g_HeuristicDetected     = array();
    $vars->adwareList            = array();
    $vars->phishing              = array();
    $g_PHPCodeInside         = array();
    $g_PHPCodeInsideFragment = array();
    $vars->bigFiles              = array();
    $vars->redirectPHPFragment  = array();
    $g_EmptyLinkSrc          = array();
    $g_Base64Fragment        = array();
    $g_UnixExec              = array();
    $vars->phishingSigFragment   = array();
    $vars->phishingFragment      = array();
    $g_PhishingSig           = array();
    $g_IframerFragment       = array();
    $vars->CMS                  = array();
    $vars->adwareListFragment    = array();
}

if (BOOL_RESULT && (!defined('NEED_REPORT'))) {
    if ((count($vars->criticalPHP) > 0) OR (count($vars->criticalJS) > 0) OR (count($g_PhishingSig) > 0)) {
        exit(2);
    } else {
        exit(0);
    }
}
////////////////////////////////////////////////////////////////////////////
$l_Template = str_replace("@@SERVICE_INFO@@", htmlspecialchars("[" . $int_enc . "][" . $snum . "]"), $l_Template);

$l_Template = str_replace("@@PATH_URL@@", (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $g_AddPrefix . str_replace($g_NoPrefix, '', addSlash(ROOT_PATH))), $l_Template);

$time_taken = seconds2Human(microtime(true) - START_TIME);

$l_Template = str_replace("@@SCANNED@@", sprintf(AI_STR_013, $vars->totalFolder, $vars->totalFiles), $l_Template);

$l_ShowOffer = false;

stdOut("\nBuilding report [ mode = " . AI_EXPERT . " ]\n");

//stdOut("\nLoaded signatures: " . count($g_FlexDBShe) . " / " . count($g_JSVirSig) . "\n");

////////////////////////////////////////////////////////////////////////////
// save 
if (!(ICHECK || IMAKE)) {
    if (isset($options['with-2check']) || isset($options['quarantine'])) {
        if ((count($vars->criticalPHP) > 0) OR (count($vars->criticalJS) > 0) OR (count($g_Base64) > 0) OR (count($g_Iframer) > 0) OR (count($g_UnixExec))) {
            if (!file_exists(DOUBLECHECK_FILE)) {
                if ($l_FH = fopen(DOUBLECHECK_FILE, 'w')) {
                    fputs($l_FH, '<?php die("Forbidden"); ?>' . "\n");

                    $l_CurrPath = dirname(__FILE__);

                    if (!isset($vars->criticalPHP)) {
                        $vars->criticalPHP = array();
                    }
                    if (!isset($vars->criticalJS)) {
                        $vars->criticalJS = array();
                    }
                    if (!isset($g_Iframer)) {
                        $g_Iframer = array();
                    }
                    if (!isset($g_Base64)) {
                        $g_Base64 = array();
                    }
                    if (!isset($vars->phishing)) {
                        $vars->phishing = array();
                    }
                    if (!isset($vars->adwareList)) {
                        $vars->adwareList = array();
                    }
                    if (!isset($vars->redirect)) {
                        $vars->redirect = array();
                    }

                    $tmpIndex = array_merge($vars->criticalPHP, $vars->criticalJS, $vars->phishing, $g_Base64, $g_Iframer, $vars->adwareList, $vars->redirect);
                    $tmpIndex = array_values(array_unique($tmpIndex));

                    for ($i = 0; $i < count($tmpIndex); $i++) {
                        $tmpIndex[$i] = str_replace($l_CurrPath, '.', $vars->structure['n'][$tmpIndex[$i]]);
                    }

                    for ($i = 0; $i < count($g_UnixExec); $i++) {
                        $tmpIndex[] = str_replace($l_CurrPath, '.', $g_UnixExec[$i]);
                    }

                    $tmpIndex = array_values(array_unique($tmpIndex));

                    for ($i = 0; $i < count($tmpIndex); $i++) {
                        fputs($l_FH, $tmpIndex[$i] . "\n");
                    }

                    fclose($l_FH);
                } else {
                    stdOut("Error! Cannot create " . DOUBLECHECK_FILE);
                }
            } else {
                stdOut(DOUBLECHECK_FILE . ' already exists.');
                if (AI_STR_044 != '') {
                    $l_Result .= '<div class="rep">' . AI_STR_044 . '</div>';
                }
            }
        }
    }
}
////////////////////////////////////////////////////////////////////////////

$l_Summary = '<div class="title">' . AI_STR_074 . '</div>';
$l_Summary .= '<table cellspacing=0 border=0>';

if (count($vars->redirect) > 0) {
    $l_Summary .= makeSummary(AI_STR_059, count($vars->redirect), 'crit');
}

if (count($vars->criticalPHP) > 0) {
    $l_Summary .= makeSummary(AI_STR_060, count($vars->criticalPHP), "crit");
}

if (count($vars->criticalJS) > 0) {
    $l_Summary .= makeSummary(AI_STR_061, count($vars->criticalJS), "crit");
}

if (count($vars->phishing) > 0) {
    $l_Summary .= makeSummary(AI_STR_062, count($vars->phishing), "crit");
}

if (count($vars->notRead) > 0) {
    $l_Summary .= makeSummary(AI_STR_066, count($vars->notRead), "crit");
}

if (count($vars->warningPHP) > 0) {
    $l_Summary .= makeSummary(AI_STR_068, count($vars->warningPHP), "warn");
}

if (count($vars->bigFiles) > 0) {
    $l_Summary .= makeSummary(AI_STR_065, count($vars->bigFiles), "warn");
}

if (count($vars->symLinks) > 0) {
    $l_Summary .= makeSummary(AI_STR_069, count($vars->symLinks), "warn");
}

$l_Summary .= "</table>";

$l_ArraySummary                      = array();
$l_ArraySummary["redirect"]          = count($vars->redirect);
$l_ArraySummary["critical_php"]      = count($vars->criticalPHP);
$l_ArraySummary["critical_js"]       = count($vars->criticalJS);
$l_ArraySummary["phishing"]          = count($vars->phishing);
$l_ArraySummary["unix_exec"]         = 0; // count($g_UnixExec);
$l_ArraySummary["iframes"]           = 0; // count($g_Iframer);
$l_ArraySummary["not_read"]          = count($vars->notRead);
$l_ArraySummary["base64"]            = 0; // count($g_Base64);
$l_ArraySummary["heuristics"]        = 0; // count($g_HeuristicDetected);
$l_ArraySummary["symlinks"]          = count($vars->symLinks);
$l_ArraySummary["big_files_skipped"] = count($vars->bigFiles);
$l_ArraySummary["suspicious"]        = count($vars->warningPHP);

if (function_exists('json_encode')) {
    $l_Summary .= "<!--[json]" . json_encode($l_ArraySummary) . "[/json]-->";
}

$l_Summary .= "<div class=details style=\"margin: 20px 20px 20px 0\">" . AI_STR_080 . "</div>\n";

$l_Template = str_replace("@@SUMMARY@@", $l_Summary, $l_Template);

$l_Result .= AI_STR_015;

$l_Template = str_replace("@@VERSION@@", AI_VERSION, $l_Template);

////////////////////////////////////////////////////////////////////////////



if (function_exists("gethostname") && is_callable("gethostname")) {
    $l_HostName = gethostname();
} else {
    $l_HostName = '???';
}

$l_PlainResult = "# Malware list detected by AI-Bolit (https://revisium.com/ai/) on " . date("d/m/Y H:i:s", time()) . " " . $l_HostName . "\n\n";


$scan_time = round(microtime(true) - START_TIME, 1);
$json_report = $reportFactory();
$json_report->addVars($vars, $scan_time);

if (!AI_HOSTER) {
    stdOut("Building list of vulnerable scripts " . count($vars->vulnerable));

    if (count($vars->vulnerable) > 0) {
        $l_Result .= '<div class="note_vir">' . AI_STR_081 . ' (' . count($vars->vulnerable) . ')</div><div class="crit">';
        foreach ($vars->vulnerable as $l_Item) {
            $l_Result .= '<li>' . makeSafeFn($vars->structure['n'][$l_Item['ndx']], true) . ' - ' . $l_Item['id'] . '</li>';
            $l_PlainResult .= '[VULNERABILITY] ' . replacePathArray($vars->structure['n'][$l_Item['ndx']]) . ' - ' . $l_Item['id'] . "\n";
        }

        $l_Result .= '</div><p>' . PHP_EOL;
        $l_PlainResult .= "\n";
    }
}


stdOut("Building list of shells " . count($vars->criticalPHP));

if (count($vars->criticalPHP) > 0) {
    $vars->criticalPHP              = array_slice($vars->criticalPHP, 0, 15000);
    $l_Result .= '<div class="note_vir">' . AI_STR_016 . ' (' . count($vars->criticalPHP) . ')</div><div class="crit">';
    $l_Result .= printList($vars->criticalPHP, $vars, $vars->criticalPHPFragment, true, $vars->criticalPHPSig, 'table_crit');
    $l_PlainResult .= '[SERVER MALWARE]' . "\n" . printPlainList($vars->criticalPHP, $vars,  $vars->criticalPHPFragment, true, $vars->criticalPHPSig, 'table_crit') . "\n";
    $l_Result .= '</div>' . PHP_EOL;

    $l_ShowOffer = true;
} else {
    $l_Result .= '<div class="ok"><b>' . AI_STR_017 . '</b></div>';
}

stdOut("Building list of js " . count($vars->criticalJS));

if (count($vars->criticalJS) > 0) {
    $vars->criticalJS              = array_slice($vars->criticalJS, 0, 15000);
    $l_Result .= '<div class="note_vir">' . AI_STR_018 . ' (' . count($vars->criticalJS) . ')</div><div class="crit">';
    $l_Result .= printList($vars->criticalJS, $vars, $vars->criticalJSFragment, true, $vars->criticalJSSig, 'table_vir');
    $l_PlainResult .= '[CLIENT MALWARE / JS]' . "\n" . printPlainList($vars->criticalJS, $vars,  $vars->criticalJSFragment, true, $vars->criticalJSSig, 'table_vir') . "\n";
    $l_Result .= "</div>" . PHP_EOL;

    $l_ShowOffer = true;
}

stdOut("Building list of unread files " . count($vars->notRead));

if (count($vars->notRead) > 0) {
    $vars->notRead               = array_slice($vars->notRead, 0, AIBOLIT_MAX_NUMBER);
    $l_Result .= '<div class="note_vir">' . AI_STR_030 . ' (' . count($vars->notRead) . ')</div><div class="crit">';
    $l_Result .= printList($vars->notRead, $vars);
    $l_Result .= "</div><div class=\"spacer\"></div>" . PHP_EOL;
    $l_PlainResult .= '[SCAN ERROR / SKIPPED]' . "\n" . printPlainList($vars->notRead, $vars) . "\n\n";
}

if (!AI_HOSTER) {
    stdOut("Building list of phishing pages " . count($vars->phishing));

    if (count($vars->phishing) > 0) {
        $l_Result .= '<div class="note_vir">' . AI_STR_058 . ' (' . count($vars->phishing) . ')</div><div class="crit">';
        $l_Result .= printList($vars->phishing, $vars, $vars->phishingFragment, true, $vars->phishingSigFragment, 'table_vir');
        $l_PlainResult .= '[PHISHING]' . "\n" . printPlainList($vars->phishing, $vars,  $vars->phishingFragment, true, $vars->phishingSigFragment, 'table_vir') . "\n";
        $l_Result .= "</div>" . PHP_EOL;

        $l_ShowOffer = true;
    }

    stdOut('Building list of redirects ' . count($vars->redirect));
    if (count($vars->redirect) > 0) {
        $l_ShowOffer             = true;
        $l_Result .= '<div class="note_vir">' . AI_STR_027 . ' (' . count($vars->redirect) . ')</div><div class="crit">';
        $l_Result .= printList($vars->redirect, $vars, $vars->redirectPHPFragment, true);
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of symlinks " . count($vars->symLinks));

    if (count($vars->symLinks) > 0) {
        $vars->symLinks               = array_slice($vars->symLinks, 0, AIBOLIT_MAX_NUMBER);
        $l_Result .= '<div class="note_vir">' . AI_STR_022 . ' (' . count($vars->symLinks) . ')</div><div class="crit">';
        $l_Result .= nl2br(makeSafeFn(implode("\n", $vars->symLinks), true));
        $l_Result .= "</div><div class=\"spacer\"></div>";
    }

}

if (AI_EXTRA_WARN) {
    $l_WarningsNum = count($vars->warningPHP);
    if ($l_WarningsNum > 0) {
        $l_Result .= "<div style=\"margin-top: 20px\" class=\"title\">" . AI_STR_026 . "</div>";
    }

    stdOut("Building list of suspicious files " . count($vars->warningPHP));

    if ((count($vars->warningPHP) > 0) && JSONReport::checkMask($defaults['report_mask'], JSONReport::REPORT_MASK_FULL)) {
        $vars->warningPHP              = array_slice($vars->warningPHP, 0, AIBOLIT_MAX_NUMBER);
        $l_Result .= '<div class="note_warn">' . AI_STR_035 . ' (' . count($vars->warningPHP) . ')</div><div class="warn">';
        $l_Result .= printList($vars->warningPHP, $vars, $vars->warningPHPFragment, true, $vars->warningPHPSig, 'table_warn');
        $l_PlainResult .= '[SUSPICIOUS]' . "\n" . printPlainList($vars->warningPHP, $vars,  $vars->warningPHPFragment, true, $vars->warningPHPSig, 'table_warn') . "\n";
        $l_Result .= '</div>' . PHP_EOL;
    }
}
////////////////////////////////////
if (!AI_HOSTER) {
    $l_WarningsNum = count($g_HeuristicDetected) + count($g_HiddenFiles) + count($vars->bigFiles) + count($g_PHPCodeInside) + count($vars->adwareList) + count($g_EmptyLink) + count($vars->doorway) + count($vars->warningPHP) + count($vars->skippedFolders);

    if ($l_WarningsNum > 0) {
        $l_Result .= "<div style=\"margin-top: 20px\" class=\"title\">" . AI_STR_026 . "</div>";
    }

    stdOut("Building list of adware " . count($vars->adwareList));

    if (count($vars->adwareList) > 0) {
        $l_Result .= '<div class="note_warn">' . AI_STR_029 . '</div><div class="warn">';
        $l_Result .= printList($vars->adwareList, $vars, $vars->adwareListFragment, true);
        $l_PlainResult .= '[ADWARE]' . "\n" . printPlainList($vars->adwareList, $vars,  $vars->adwareListFragment, true) . "\n";
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of bigfiles " . count($vars->bigFiles));
    $max_size_to_scan = getBytes(MAX_SIZE_TO_SCAN);
    $max_size_to_scan = $max_size_to_scan > 0 ? $max_size_to_scan : getBytes('1m');

    if (count($vars->bigFiles) > 0) {
        $vars->bigFiles               = array_slice($vars->bigFiles, 0, AIBOLIT_MAX_NUMBER);
        $l_Result .= "<div class=\"note_warn\">" . sprintf(AI_STR_038, bytes2Human($max_size_to_scan)) . '</div><div class="warn">';
        $l_Result .= printList($vars->bigFiles, $vars);
        $l_Result .= "</div>";
        $l_PlainResult .= '[BIG FILES / SKIPPED]' . "\n" . printPlainList($vars->bigFiles, $vars) . "\n\n";
    }

    stdOut("Building list of doorways " . count($vars->doorway));

    if ((count($vars->doorway) > 0) && JSONReport::checkMask($defaults['report_mask'], JSONReport::REPORT_MASK_DOORWAYS)) {
        $vars->doorway              = array_slice($vars->doorway, 0, AIBOLIT_MAX_NUMBER);
        $l_Result .= '<div class="note_warn">' . AI_STR_034 . '</div><div class="warn">';
        $l_Result .= printList($vars->doorway, $vars);
        $l_Result .= "</div>" . PHP_EOL;

    }

    if (count($vars->CMS) > 0) {
        $l_Result .= "<div class=\"note_warn\">" . AI_STR_037 . "<br/>";
        $l_Result .= nl2br(makeSafeFn(implode("\n", $vars->CMS)));
        $l_Result .= "</div>";
    }
}

if (ICHECK) {
    $l_Result .= "<div style=\"margin-top: 20px\" class=\"title\">" . AI_STR_087 . "</div>";

    stdOut("Building list of added files " . count($changes['addedFiles']));
    if (count($changes['addedFiles']) > 0) {
        $l_Result .= '<div class="note_int">' . AI_STR_082 . ' (' . count($changes['addedFiles']) . ')</div><div class="intitem">';
        $l_Result .= printList($changes['addedFiles'], $vars);
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of modified files " . count($changes['modifiedFiles']));
    if (count($changes['modifiedFiles']) > 0) {
        $l_Result .= '<div class="note_int">' . AI_STR_083 . ' (' . count($changes['modifiedFiles']) . ')</div><div class="intitem">';
        $l_Result .= printList($changes['modifiedFiles'], $vars);
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of deleted files " . count($changes['deletedFiles']));
    if (count($changes['deletedFiles']) > 0) {
        $l_Result .= '<div class="note_int">' . AI_STR_084 . ' (' . count($changes['deletedFiles']) . ')</div><div class="intitem">';
        $l_Result .= printList($changes['deletedFiles'], $vars);
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of added dirs " . count($changes['addedDirs']));
    if (count($changes['addedDirs']) > 0) {
        $l_Result .= '<div class="note_int">' . AI_STR_085 . ' (' . count($changes['addedDirs']) . ')</div><div class="intitem">';
        $l_Result .= printList($changes['addedDirs'], $vars);
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of deleted dirs " . count($changes['deletedDirs']));
    if (count($changes['deletedDirs']) > 0) {
        $l_Result .= '<div class="note_int">' . AI_STR_086 . ' (' . count($changes['deletedDirs']) . ')</div><div class="intitem">';
        $l_Result .= printList($changes['deletedDirs'], $vars);
        $l_Result .= "</div>" . PHP_EOL;
    }
}

if (!isCli()) {
    $l_Result .= QCR_ExtractInfo($l_PhpInfoBody[1]);
}


if (function_exists('memory_get_peak_usage')) {
    $l_Template = str_replace("@@MEMORY@@", AI_STR_043 . bytes2Human(memory_get_peak_usage()), $l_Template);
}

$l_Template = str_replace('@@WARN_QUICK@@', ((SCAN_ALL_FILES || $g_SpecificExt) ? '' : AI_STR_045), $l_Template);

if ($l_ShowOffer) {
    $l_Template = str_replace('@@OFFER@@', $l_Offer, $l_Template);
} else {
    $l_Template = str_replace('@@OFFER@@', AI_STR_002, $l_Template);
}

$l_Template = str_replace('@@OFFER2@@', $l_Offer2, $l_Template);

$l_Template = str_replace('@@CAUTION@@', AI_STR_003, $l_Template);

$l_Template = str_replace('@@CREDITS@@', AI_STR_075, $l_Template);

$l_Template = str_replace('@@FOOTER@@', AI_STR_076, $l_Template);

$l_Template = str_replace('@@STAT@@', sprintf(AI_STR_012, $time_taken, date('d-m-Y в H:i:s', floor(START_TIME)), date('d-m-Y в H:i:s')), $l_Template);

////////////////////////////////////////////////////////////////////////////
$l_Template = str_replace("@@MAIN_CONTENT@@", $l_Result, $l_Template);

if (!isCli()) {
    echo $l_Template;
    exit;
}

if (!defined('REPORT') OR REPORT === '') {
    die2('Report not written.');
}

// write plain text result
if (PLAIN_FILE != '') {

    $l_PlainResult = preg_replace('|__AI_LINE1__|smi', '[', $l_PlainResult);
    $l_PlainResult = preg_replace('|__AI_LINE2__|smi', '] ', $l_PlainResult);
    $l_PlainResult = preg_replace('|__AI_MARKER__|smi', ' %> ', $l_PlainResult);

    if ($l_FH = fopen(PLAIN_FILE, "w")) {
        fputs($l_FH, $l_PlainResult);
        fclose($l_FH);
    }
}

// write json result
if (defined('JSON_FILE')) {
    $res = $json_report->write(JSON_FILE);
    if (JSON_STDOUT) {
        echo $res;
    }
}

// write serialized result
if (defined('PHP_FILE')) {
    $json_report->writePHPSerialized(PHP_FILE);
}

$emails = getEmails(REPORT);

if (!$emails) {
    if ($l_FH = fopen($file, "w")) {
        fputs($l_FH, $l_Template);
        fclose($l_FH);
        stdOut("\nReport written to '$file'.");
    } else {
        stdOut("\nCannot create '$file'.");
    }
} else {
    $headers = array(
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . ($defaults['email_from'] ? $defaults['email_from'] : 'AI-Bolit@myhost')
    );

    for ($i = 0, $size = sizeof($emails); $i < $size; $i++) {
        //$res = @mail($emails[$i], 'AI-Bolit Report ' . date("d/m/Y H:i", time()), $l_Result, implode("\r\n", $headers));
    }

    if ($res) {
        stdOut("\nReport sended to " . implode(', ', $emails));
    }
}

$time_taken = microtime(true) - START_TIME;
$time_taken = round($time_taken, 5);

stdOut("Scanning complete! Time taken: " . seconds2Human($time_taken));

if (DEBUG_PERFORMANCE) {
    $keys = array_keys($g_RegExpStat);
    for ($i = 0; $i < count($keys); $i++) {
        $g_RegExpStat[$keys[$i]] = round($g_RegExpStat[$keys[$i]] * 1000000);
    }

    arsort($g_RegExpStat);

    foreach ($g_RegExpStat as $r => $v) {
        echo $v . "\t\t" . $r . "\n";
    }

    die();
}

stdOut("\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
stdOut("Attention! DO NOT LEAVE either ai-bolit.php or AI-BOLIT-REPORT-<xxxx>-<yy>.html \nfile on server. COPY it locally then REMOVE from server. ");
stdOut("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");

if (isset($options['quarantine'])) {
    Quarantine();
}

if (isset($options['cmd'])) {
    stdOut("Run \"{$options['cmd']}\" ");
    system($options['cmd']);
}

QCR_Debug();

# exit with code

$l_EC1 = count($vars->criticalPHP);
$l_EC2 = count($vars->criticalJS) + count($vars->phishing) + count($vars->warningPHP);
$code  = 0;

if ($l_EC1 > 0) {
    $code = 2;
} else {
    if ($l_EC2 > 0) {
        $code = 1;
    }
}

$stat = array(
    'php_malware'   => count($vars->criticalPHP),
    'cloudhash'     => count($vars->blackFiles),
    'js_malware'    => count($vars->criticalJS),
    'phishing'      => count($vars->phishing)
);

if (function_exists('aibolit_onComplete')) {
    aibolit_onComplete($code, $stat);
}

stdOut('Exit code ' . $code);
exit($code);

############################################# END ###############################################

function Quarantine() {
    if (!file_exists(DOUBLECHECK_FILE)) {
        return;
    }

    $g_QuarantinePass = 'aibolit';

    $archive  = "AI-QUARANTINE-" . rand(100000, 999999) . ".zip";
    $infoFile = substr($archive, 0, -3) . "txt";
    $report   = REPORT_PATH . DIR_SEPARATOR . REPORT_FILE;


    foreach (file(DOUBLECHECK_FILE) as $file) {
        $file = trim($file);
        if (!is_file($file))
            continue;

        $lStat = stat($file);

        // skip files over 300KB
        if ($lStat['size'] > 300 * 1024)
            continue;

        // http://www.askapache.com/security/chmod-stat.html
        $p    = $lStat['mode'];
        $perm = '-';
        $perm .= (($p & 0x0100) ? 'r' : '-') . (($p & 0x0080) ? 'w' : '-');
        $perm .= (($p & 0x0040) ? (($p & 0x0800) ? 's' : 'x') : (($p & 0x0800) ? 'S' : '-'));
        $perm .= (($p & 0x0020) ? 'r' : '-') . (($p & 0x0010) ? 'w' : '-');
        $perm .= (($p & 0x0008) ? (($p & 0x0400) ? 's' : 'x') : (($p & 0x0400) ? 'S' : '-'));
        $perm .= (($p & 0x0004) ? 'r' : '-') . (($p & 0x0002) ? 'w' : '-');
        $perm .= (($p & 0x0001) ? (($p & 0x0200) ? 't' : 'x') : (($p & 0x0200) ? 'T' : '-'));

        $owner = (function_exists('posix_getpwuid')) ? @posix_getpwuid($lStat['uid']) : array(
            'name' => $lStat['uid']
        );
        $group = (function_exists('posix_getgrgid')) ? @posix_getgrgid($lStat['gid']) : array(
            'name' => $lStat['uid']
        );

        $inf['permission'][] = $perm;
        $inf['owner'][]      = $owner['name'];
        $inf['group'][]      = $group['name'];
        $inf['size'][]       = $lStat['size'] > 0 ? bytes2Human($lStat['size']) : '-';
        $inf['ctime'][]      = $lStat['ctime'] > 0 ? date("d/m/Y H:i:s", $lStat['ctime']) : '-';
        $inf['mtime'][]      = $lStat['mtime'] > 0 ? date("d/m/Y H:i:s", $lStat['mtime']) : '-';
        $files[]             = strpos($file, './') === 0 ? substr($file, 2) : $file;
    }

    // get config files for cleaning
    $configFilesRegex = 'config(uration|\.in[ic])?\.php$|dbconn\.php$';
    $configFiles      = preg_grep("~$configFilesRegex~", $files);

    // get columns width
    $width = array();
    foreach (array_keys($inf) as $k) {
        $width[$k] = strlen($k);
        for ($i = 0; $i < count($inf[$k]); ++$i) {
            $len = strlen($inf[$k][$i]);
            if ($len > $width[$k])
                $width[$k] = $len;
        }
    }

    // headings of columns
    $info = '';
    foreach (array_keys($inf) as $k) {
        $info .= str_pad($k, $width[$k], ' ', STR_PAD_LEFT) . ' ';
    }
    $info .= "name\n";

    for ($i = 0; $i < count($files); ++$i) {
        foreach (array_keys($inf) as $k) {
            $info .= str_pad($inf[$k][$i], $width[$k], ' ', STR_PAD_LEFT) . ' ';
        }
        $info .= $files[$i] . "\n";
    }
    unset($inf, $width);

    exec("zip -v 2>&1", $output, $code);

    if ($code == 0) {
        $filter = '';
        if ($configFiles && exec("grep -V 2>&1", $output, $code) && $code == 0) {
            $filter = "|grep -v -E '$configFilesRegex'";
        }

        exec("cat AI-BOLIT-DOUBLECHECK.php $filter |zip -@ --password $g_QuarantinePass $archive", $output, $code);
        if ($code == 0) {
            file_put_contents($infoFile, $info);
            $m = array();
            if (!empty($filter)) {
                foreach ($configFiles as $file) {
                    $tmp  = file_get_contents($file);
                    // remove  passwords
                    $tmp  = preg_replace('~^.*?pass.*~im', '', $tmp);
                    // new file name
                    $file = preg_replace('~.*/~', '', $file) . '-' . rand(100000, 999999);
                    file_put_contents($file, $tmp);
                    $m[] = $file;
                }
            }

            exec("zip -j --password $g_QuarantinePass $archive $infoFile $report " . DOUBLECHECK_FILE . ' ' . implode(' ', $m));
            stdOut("\nCreate archive '" . realpath($archive) . "'");
            stdOut("This archive have password '$g_QuarantinePass'");
            foreach ($m as $file)
                unlink($file);
            unlink($infoFile);
            return;
        }
    }

    $zip = new ZipArchive;

    if ($zip->open($archive, ZipArchive::CREATE | ZipArchive::OVERWRITE) === false) {
        stdOut("Cannot create '$archive'.");
        return;
    }

    foreach ($files as $file) {
        if (in_array($file, $configFiles)) {
            $tmp = file_get_contents($file);
            // remove  passwords
            $tmp = preg_replace('~^.*?pass.*~im', '', $tmp);
            $zip->addFromString($file, $tmp);
        } else {
            $zip->addFile($file);
        }
    }
    $zip->addFile(DOUBLECHECK_FILE, DOUBLECHECK_FILE);
    $zip->addFile($report, REPORT_FILE);
    $zip->addFromString($infoFile, $info);
    $zip->close();

    stdOut("\nCreate archive '" . realpath($archive) . "'.");
    stdOut("This archive has no password!");
}



///////////////////////////////////////////////////////////////////////////
function QCR_IntegrityCheck($l_RootDir, $vars) {
    global $defaults, $g_UrlIgnoreList, $g_DirIgnoreList, $g_UnsafeDirArray, $g_UnsafeFilesFound, $g_HiddenFiles, $g_UnixExec, $g_IgnoredExt, $g_SuspiciousFiles, $l_SkipSample;
    global $g_IntegrityDB, $g_ICheck;
    static $l_Buffer = '';

    $l_DirCounter          = 0;
    $l_DoorwayFilesCounter = 0;
    $l_SourceDirIndex      = $vars->g_counter - 1;

    QCR_Debug('Check ' . $l_RootDir);

    if ($l_DIRH = @opendir($l_RootDir)) {
        while (($l_FileName = readdir($l_DIRH)) !== false) {
            if ($l_FileName == '.' || $l_FileName == '..')
                continue;

            $l_FileName = $l_RootDir . DIR_SEPARATOR . $l_FileName;

            $l_Type  = filetype($l_FileName);
            $l_IsDir = ($l_Type == "dir");
            if ($l_Type == "link") {
                $vars->symLinks[] = $l_FileName;
                continue;
            } else if ($l_Type != "file" && (!$l_IsDir)) {
                $g_UnixExec[] = $l_FileName;
                continue;
            }

            $l_Ext = substr($l_FileName, strrpos($l_FileName, '.') + 1);

            $l_NeedToScan = true;
            $l_Ext2       = substr(strstr(basename($l_FileName), '.'), 1);
            if (in_array(strtolower($l_Ext2), $g_IgnoredExt)) {
                $l_NeedToScan = false;
            }

            // if folder in ignore list
            $l_Skip = false;
            for ($dr = 0; $dr < count($g_DirIgnoreList); $dr++) {
                if (($g_DirIgnoreList[$dr] != '') && preg_match('#' . $g_DirIgnoreList[$dr] . '#', $l_FileName, $l_Found)) {
                    if (!in_array($g_DirIgnoreList[$dr], $l_SkipSample)) {
                        $l_SkipSample[] = $g_DirIgnoreList[$dr];
                    } else {
                        $l_Skip       = true;
                        $l_NeedToScan = false;
                    }
                }
            }

            if (getRelativePath($l_FileName) == "./" . INTEGRITY_DB_FILE)
                $l_NeedToScan = false;

            if ($l_IsDir) {
                // skip on ignore
                if ($l_Skip) {
                    $vars->skippedFolders[] = $l_FileName;
                    continue;
                }

                $l_BaseName = basename($l_FileName);

                $l_DirCounter++;

                $vars->counter++;
                $vars->foundTotalDirs++;

                QCR_IntegrityCheck($l_FileName, $vars);

            } else {
                if ($l_NeedToScan) {
                    $vars->foundTotalFiles++;
                    $vars->counter++;
                }
            }

            if (!$l_NeedToScan)
                continue;

            if (IMAKE) {
                write_integrity_db_file($l_FileName);
                continue;
            }

            // ICHECK
            // skip if known and not modified.
            if (icheck($l_FileName))
                continue;

            $l_Buffer .= getRelativePath($l_FileName);
            $l_Buffer .= $l_IsDir ? DIR_SEPARATOR . "\n" : "\n";

            if (strlen($l_Buffer) > 32000) {
                file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
                $l_Buffer = '';
            }

        }

        closedir($l_DIRH);
    }

    if (($l_RootDir == ROOT_PATH) && !empty($l_Buffer)) {
        file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
        $l_Buffer = '';
    }

    if (($l_RootDir == ROOT_PATH)) {
        write_integrity_db_file();
    }

}


function getRelativePath($l_FileName) {
    return "./" . substr($l_FileName, strlen(ROOT_PATH) + 1) . (is_dir($l_FileName) ? DIR_SEPARATOR : '');
}

/**
 *
 * @return true if known and not modified
 */
function icheck($l_FileName) {
    global $g_IntegrityDB, $g_ICheck;
    static $l_Buffer = '';
    static $l_status = array('modified' => 'modified', 'added' => 'added');

    $l_RelativePath = getRelativePath($l_FileName);
    $l_known        = isset($g_IntegrityDB[$l_RelativePath]);

    if (is_dir($l_FileName)) {
        if ($l_known) {
            unset($g_IntegrityDB[$l_RelativePath]);
        } else {
            $g_IntegrityDB[$l_RelativePath] =& $l_status['added'];
        }
        return $l_known;
    }

    if ($l_known == false) {
        $g_IntegrityDB[$l_RelativePath] =& $l_status['added'];
        return false;
    }

    $hash = is_file($l_FileName) ? hash_file('sha1', $l_FileName) : '';

    if ($g_IntegrityDB[$l_RelativePath] != $hash) {
        $g_IntegrityDB[$l_RelativePath] =& $l_status['modified'];
        return false;
    }

    unset($g_IntegrityDB[$l_RelativePath]);
    return true;
}

function write_integrity_db_file($l_FileName = '') {
    static $l_Buffer = '';

    if (empty($l_FileName)) {
        empty($l_Buffer) or file_put_contents('compress.zlib://' . INTEGRITY_DB_FILE, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . INTEGRITY_DB_FILE);
        $l_Buffer = '';
        return;
    }

    $l_RelativePath = getRelativePath($l_FileName);

    $hash = is_file($l_FileName) ? hash_file('sha1', $l_FileName) : '';

    $l_Buffer .= "$l_RelativePath|$hash\n";

    if (strlen($l_Buffer) > 32000) {
        file_put_contents('compress.zlib://' . INTEGRITY_DB_FILE, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . INTEGRITY_DB_FILE);
        $l_Buffer = '';
    }
}

function load_integrity_db() {
    global $g_IntegrityDB;
    file_exists(INTEGRITY_DB_FILE) or die2('Not found ' . INTEGRITY_DB_FILE);

    $s_file = new SplFileObject('compress.zlib://' . INTEGRITY_DB_FILE);
    $s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

    foreach ($s_file as $line) {
        $i = strrpos($line, '|');
        if (!$i)
            continue;
        $g_IntegrityDB[substr($line, 0, $i)] = substr($line, $i + 1);
    }

    $s_file = null;
}


function getStdin()
{
    $stdin  = '';
    $f      = @fopen('php://stdin', 'r');
    while($line = fgets($f))
    {
        $stdin .= $line;
    }
    fclose($f);
    return $stdin;
}

function OptimizeSignatures() {
    global $g_DBShe, $g_FlexDBShe, $gX_FlexDBShe, $gXX_FlexDBShe;
    global $g_JSVirSig, $gX_JSVirSig;
    global $g_AdwareSig;
    global $g_PhishingSig;
    global $g_ExceptFlex, $g_SusDBPrio, $g_SusDB;

    (AI_EXPERT == 2) && ($g_FlexDBShe = array_merge($g_FlexDBShe, $gX_FlexDBShe, $gXX_FlexDBShe));
    (AI_EXPERT == 1) && ($g_FlexDBShe = array_merge($g_FlexDBShe, $gX_FlexDBShe));
    $gX_FlexDBShe = $gXX_FlexDBShe = array();

    (AI_EXPERT == 2) && ($g_JSVirSig = array_merge($g_JSVirSig, $gX_JSVirSig));
    $gX_JSVirSig = array();

    $count = count($g_FlexDBShe);

    for ($i = 0; $i < $count; $i++) {
        if ($g_FlexDBShe[$i] == '[a-zA-Z0-9_]+?\(\s*[a-zA-Z0-9_]+?=\s*\)')
            $g_FlexDBShe[$i] = '\((?<=[a-zA-Z0-9_].)\s*[a-zA-Z0-9_]++=\s*\)';
        if ($g_FlexDBShe[$i] == '([^\?\s])\({0,1}\.[\+\*]\){0,1}\2[a-z]*e')
            $g_FlexDBShe[$i] = '(?J)\.[+*](?<=(?<d>[^\?\s])\(..|(?<d>[^\?\s])..)\)?\g{d}[a-z]*e';
        if ($g_FlexDBShe[$i] == '$[a-zA-Z0-9_]\{\d+\}\s*\.$[a-zA-Z0-9_]\{\d+\}\s*\.$[a-zA-Z0-9_]\{\d+\}\s*\.')
            $g_FlexDBShe[$i] = '\$[a-zA-Z0-9_]\{\d+\}\s*\.\$[a-zA-Z0-9_]\{\d+\}\s*\.\$[a-zA-Z0-9_]\{\d+\}\s*\.';

        $g_FlexDBShe[$i] = str_replace('http://.+?/.+?\.php\?a', 'http://[^?\s]++(?<=\.php)\?a', $g_FlexDBShe[$i]);
        $g_FlexDBShe[$i] = preg_replace('~\[a-zA-Z0-9_\]\+\K\?~', '+', $g_FlexDBShe[$i]);
        $g_FlexDBShe[$i] = preg_replace('~^\\\\[d]\+&@~', '&@(?<=\d..)', $g_FlexDBShe[$i]);
        $g_FlexDBShe[$i] = str_replace('\s*[\'"]{0,1}.+?[\'"]{0,1}\s*', '.+?', $g_FlexDBShe[$i]);
        $g_FlexDBShe[$i] = str_replace('[\'"]{0,1}.+?[\'"]{0,1}', '.+?', $g_FlexDBShe[$i]);

        $g_FlexDBShe[$i] = preg_replace('~^\[\'"\]\{0,1\}\.?|^@\*|^\\\\s\*~', '', $g_FlexDBShe[$i]);
        $g_FlexDBShe[$i] = preg_replace('~^\[\'"\]\{0,1\}\.?|^@\*|^\\\\s\*~', '', $g_FlexDBShe[$i]);
    }

    optSig($g_FlexDBShe);

    optSig($g_JSVirSig);
    
    
    optSig($g_SusDB);
    //optSig($g_SusDBPrio);
    //optSig($g_ExceptFlex);

    // convert exception rules
    $cnt = count($g_ExceptFlex);
    for ($i = 0; $i < $cnt; $i++) {
        $g_ExceptFlex[$i] = trim(UnwrapObfu($g_ExceptFlex[$i]));
        if (!strlen($g_ExceptFlex[$i]))
            unset($g_ExceptFlex[$i]);
    }

    $g_ExceptFlex = array_values($g_ExceptFlex);
}

function optSig(&$sigs) {
    $sigs = array_unique($sigs);

    // Add SigId
    foreach ($sigs as &$s) {
        $s .= '(?<X' . myCheckSum($s) . '>)';
    }
    unset($s);

    $fix = array(
        '([^\?\s])\({0,1}\.[\+\*]\){0,1}\2[a-z]*e' => '(?J)\.[+*](?<=(?<d>[^\?\s])\(..|(?<d>[^\?\s])..)\)?\g{d}[a-z]*e',
        'http://.+?/.+?\.php\?a' => 'http://[^?\s]++(?<=\.php)\?a',
        '\s*[\'"]{0,1}.+?[\'"]{0,1}\s*' => '.+?',
        '[\'"]{0,1}.+?[\'"]{0,1}' => '.+?'
    );

    $sigs = str_replace(array_keys($fix), array_values($fix), $sigs);

    $fix = array(
        '~^\\\\[d]\+&@~' => '&@(?<=\d..)',
        '~^((\[\'"\]|\\\\s|@)(\{0,1\}\.?|[?*]))+~' => ''
    );

    $sigs = preg_replace(array_keys($fix), array_values($fix), $sigs);

    optSigCheck($sigs);

    $tmp = array();
    foreach ($sigs as $i => $s) {
        if (!preg_match('~^(?>(?!\.[*+]|\\\\\d)(?:\\\\.|\[.+?\]|.))+$~', $s)) {
            unset($sigs[$i]);
            $tmp[] = $s;
        }
    }

    usort($sigs, 'strcasecmp');
    $txt = implode("\n", $sigs);

    for ($i = 24; $i >= 1; ($i > 4) ? $i -= 4 : --$i) {
        $txt = preg_replace_callback('#^((?>(?:\\\\.|\\[.+?\\]|[^(\n]|\((?:\\\\.|[^)(\n])++\))(?:[*?+]\+?|\{\d+(?:,\d*)?\}[+?]?|)){' . $i . ',})[^\n]*+(?:\\n\\1(?![{?*+]).+)+#im', 'optMergePrefixes', $txt);
    }

    $sigs = array_merge(explode("\n", $txt), $tmp);

    optSigCheck($sigs);
}

function optMergePrefixes($m) {
    $limit = 8000;

    $prefix     = $m[1];
    $prefix_len = strlen($prefix);

    $len = $prefix_len;
    $r   = array();

    $suffixes = array();
    foreach (explode("\n", $m[0]) as $line) {

        if (strlen($line) > $limit) {
            $r[] = $line;
            continue;
        }

        $s = substr($line, $prefix_len);
        $len += strlen($s);
        if ($len > $limit) {
            if (count($suffixes) == 1) {
                $r[] = $prefix . $suffixes[0];
            } else {
                $r[] = $prefix . '(?:' . implode('|', $suffixes) . ')';
            }
            $suffixes = array();
            $len      = $prefix_len + strlen($s);
        }
        $suffixes[] = $s;
    }

    if (!empty($suffixes)) {
        if (count($suffixes) == 1) {
            $r[] = $prefix . $suffixes[0];
        } else {
            $r[] = $prefix . '(?:' . implode('|', $suffixes) . ')';
        }
    }

    return implode("\n", $r);
}

function optMergePrefixes_Old($m) {
    $prefix     = $m[1];
    $prefix_len = strlen($prefix);

    $suffixes = array();
    foreach (explode("\n", $m[0]) as $line) {
        $suffixes[] = substr($line, $prefix_len);
    }

    return $prefix . '(?:' . implode('|', $suffixes) . ')';
}

/*
 * Checking errors in pattern
 */
function optSigCheck(&$sigs) {
    $result = true;

    foreach ($sigs as $k => $sig) {
        if (trim($sig) == "") {
            if (DEBUG_MODE) {
                echo ("************>>>>> EMPTY\n     pattern: " . $sig . "\n");
            }
            unset($sigs[$k]);
            $result = false;
        }

        if (@preg_match('~' . $sig . '~smiS', '') === false) {
            $error = error_get_last();
            if (DEBUG_MODE) {
                echo ("************>>>>> " . $error['message'] . "\n     pattern: " . $sig . "\n");
            }
            unset($sigs[$k]);
            $result = false;
        }
    }

    return $result;
}

function _hash_($text) {
    static $r;

    if (empty($r)) {
        for ($i = 0; $i < 256; $i++) {
            if ($i < 33 OR $i > 127)
                $r[chr($i)] = '';
        }
    }

    return sha1(strtr($text, $r));
}

function check_whitelist($list, &$snum) {
    global $defaults;

    if (empty($list)) {
        return array();
    }

    $file = dirname(__FILE__) . '/AIBOLIT-WHITELIST.db';
    if (isset($defaults['avdb'])) {
        $file = dirname($defaults['avdb']) . '/AIBOLIT-WHITELIST.db';
    }

    try {
        $db = FileHashMemoryDb::open($file);
    } catch (Exception $e) {
        stdOut("\nAn error occurred while loading the white list database from " . $file . "\n");
        return array();
    }

    $snum = $db->count();
    stdOut("\nLoaded " . ceil($snum) . " known files from " . $file . "\n");

    return $db->find($list);
}

function check_binmalware($hash, $vars) {
    if (isset($vars->blacklist)) {
        return count($vars->blacklist->find(array($hash))) > 0;
    }

    return false;
}

function getSigId($l_Found) {
    foreach ($l_Found as $key => &$v) {
        if (is_string($key) AND $v[1] != -1 AND strlen($key) == 9) {
            return substr($key, 1);
        }
    }

    return null;
}

function die2($str) {
    if (function_exists('aibolit_onFatalError')) {
        aibolit_onFatalError($str);
    }
    die($str);
}

function checkFalsePositives($l_Filename, $l_Unwrapped, $l_DeobfType) {
    global $g_DeMapper;

    if ($l_DeobfType != '') {
        if (DEBUG_MODE) {
            stdOut("\n-----------------------------------------------------------------------------\n");
            stdOut("[DEBUG]" . $l_Filename . "\n");
            var_dump(getFragment($l_Unwrapped, $l_Pos));
            stdOut("\n...... $l_DeobfType ...........\n");
            var_dump($l_Unwrapped);
            stdOut("\n");
        }

        switch ($l_DeobfType) {
            case '_GLOBALS_':
                foreach ($g_DeMapper as $fkey => $fvalue) {
                    if (DEBUG_MODE) {
                        stdOut("[$fkey] => [$fvalue]\n");
                    }

                    if ((strpos($l_Filename, $fkey) !== false) && (strpos($l_Unwrapped, $fvalue) !== false)) {
                        if (DEBUG_MODE) {
                            stdOut("\n[DEBUG] *** SKIP: False Positive\n");
                        }

                        return true;
                    }
                }
                break;
        }


        return false;
    }
}

function convertToUTF8($text)
{
    if (function_exists('mb_convert_encoding')) {
        $text = @mb_convert_encoding($text, 'utf-8', 'auto');
        $text = @mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }

    return $text;
}

function isFileTooBigForScanWithSignatures($filesize)
{
    return (MAX_SIZE_TO_SCAN > 0 && $filesize > MAX_SIZE_TO_SCAN) || ($filesize < 0);
}

function isFileTooBigForCloudscan($filesize)
{
    return (MAX_SIZE_TO_CLOUDSCAN > 0 && $filesize > MAX_SIZE_TO_CLOUDSCAN) || ($filesize < 0);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// The following instructions should be written the same pattern,
/// because they are replaced by file content while building a release.
/// See the release_aibolit_ru.sh file for details.


class Variables
{
    public $structure = array();
    public $totalFolder = 0;
    public $totalFiles = 0;
    public $adwareList = array();
    public $criticalPHP = array();
    public $phishing = array();
    public $CMS = array();
    public $redirect = array();
    public $redirectPHPFragment = array();
    public $criticalJS = array();
    public $criticalJSFragment = array();
    public $blackFiles = array();
    public $notRead = array();
    public $bigFiles = array();
    public $criticalPHPSig = array();
    public $criticalPHPFragment = array();
    public $phishingSigFragment = array();
    public $phishingFragment = array();
    public $criticalJSSig = array();
    public $adwareListFragment = array();
    public $warningPHPSig = array();
    public $warningPHPFragment = array();
    public $warningPHP = array();
    public $blacklist = array();
    public $vulnerable = array();
    public $crc = 0;

    public $counter = 0;
    public $foundTotalDirs = 0;
    public $foundTotalFiles = 0;
    public $doorway = array();
    public $symLinks = array();
    public $skippedFolders = array();

    public $rescanCount = 0;
}


class CmsVersionDetector
{
    const CMS_BITRIX = 'Bitrix';
    const CMS_WORDPRESS = 'WordPress';
    const CMS_JOOMLA = 'Joomla';
    const CMS_DLE = 'Data Life Engine';
    const CMS_IPB = 'Invision Power Board';
    const CMS_WEBASYST = 'WebAsyst';
    const CMS_OSCOMMERCE = 'OsCommerce';
    const CMS_DRUPAL = 'Drupal';
    const CMS_MODX = 'MODX';
    const CMS_INSTANTCMS = 'Instant CMS';
    const CMS_PHPBB = 'PhpBB';
    const CMS_VBULLETIN = 'vBulletin';
    const CMS_SHOPSCRIPT = 'PHP ShopScript Premium';
    
    const CMS_VERSION_UNDEFINED = '0.0';

    private $root_path;
    private $versions;
    private $types;

    public function __construct($root_path = '.') {
        $this->root_path = $root_path;
        $this->versions  = array();
        $this->types     = array();

        $version = '';

        $dir_list   = $this->getDirList($root_path);
        $dir_list[] = $root_path;

        foreach ($dir_list as $dir) {
            if ($this->checkBitrix($dir, $version)) {
                $this->addCms(self::CMS_BITRIX, $version);
            }

            if ($this->checkWordpress($dir, $version)) {
                $this->addCms(self::CMS_WORDPRESS, $version);
            }

            if ($this->checkJoomla($dir, $version)) {
                $this->addCms(self::CMS_JOOMLA, $version);
            }

            if ($this->checkDle($dir, $version)) {
                $this->addCms(self::CMS_DLE, $version);
            }

            if ($this->checkIpb($dir, $version)) {
                $this->addCms(self::CMS_IPB, $version);
            }

            if ($this->checkWebAsyst($dir, $version)) {
                $this->addCms(self::CMS_WEBASYST, $version);
            }

            if ($this->checkOsCommerce($dir, $version)) {
                $this->addCms(self::CMS_OSCOMMERCE, $version);
            }

            if ($this->checkDrupal($dir, $version)) {
                $this->addCms(self::CMS_DRUPAL, $version);
            }

            if ($this->checkMODX($dir, $version)) {
                $this->addCms(self::CMS_MODX, $version);
            }

            if ($this->checkInstantCms($dir, $version)) {
                $this->addCms(self::CMS_INSTANTCMS, $version);
            }

            if ($this->checkPhpBb($dir, $version)) {
                $this->addCms(self::CMS_PHPBB, $version);
            }

            if ($this->checkVBulletin($dir, $version)) {
                $this->addCms(self::CMS_VBULLETIN, $version);
            }

            if ($this->checkPhpShopScript($dir, $version)) {
                $this->addCms(self::CMS_SHOPSCRIPT, $version);
            }

        }
    }

    function getDirList($target) {
        $remove      = array(
            '.',
            '..'
        );
        $directories = array_diff(scandir($target), $remove);

        $res = array();

        foreach ($directories as $value) {
            if (is_dir($target . '/' . $value)) {
                $res[] = $target . '/' . $value;
            }
        }

        return $res;
    }

    function isCms($name, $version) {
        for ($i = 0; $i < count($this->types); $i++) {
            if ((strpos($this->types[$i], $name) !== false) && (strpos($this->versions[$i], $version) !== false)) {
                return true;
            }
        }

        return false;
    }

    function getCmsList() {
        return $this->types;
    }

    function getCmsVersions() {
        return $this->versions;
    }

    function getCmsNumber() {
        return count($this->types);
    }

    function getCmsName($index = 0) {
        return $this->types[$index];
    }

    function getCmsVersion($index = 0) {
        return $this->versions[$index];
    }

    private function addCms($type, $version) {
        $this->types[]    = $type;
        $this->versions[] = $version;
    }

    private function checkBitrix($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/bitrix')) {
            $res = true;

            $tmp_content = @file_get_contents($this->root_path . '/bitrix/modules/main/classes/general/version.php');
            if (preg_match('|define\("SM_VERSION","(.+?)"\)|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkWordpress($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/wp-admin')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/wp-includes/version.php');
            if (preg_match('|\$wp_version\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }
        }

        return $res;
    }

    private function checkJoomla($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/libraries/joomla')) {
            $res = true;

            // for 1.5.x
            $tmp_content = @file_get_contents($dir . '/libraries/joomla/version.php');
            if (preg_match('|var\s+\$RELEASE\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];

                if (preg_match('|var\s+\$DEV_LEVEL\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                    $version .= '.' . $tmp_ver[1];
                }
            }

            // for 1.7.x
            $tmp_content = @file_get_contents($dir . '/includes/version.php');
            if (preg_match('|public\s+\$RELEASE\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];

                if (preg_match('|public\s+\$DEV_LEVEL\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                    $version .= '.' . $tmp_ver[1];
                }
            }


            // for 2.5.x and 3.x
            $tmp_content = @file_get_contents($dir . '/libraries/cms/version/version.php');

            if (preg_match('|const\s+RELEASE\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];

                if (preg_match('|const\s+DEV_LEVEL\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                    $version .= '.' . $tmp_ver[1];
                }
            }

        }

        return $res;
    }

    private function checkDle($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/engine/engine.php')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/engine/data/config.php');
            if (preg_match('|\'version_id\'\s*=>\s*"(.+?)"|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

            $tmp_content = @file_get_contents($dir . '/install.php');
            if (preg_match('|\'version_id\'\s*=>\s*"(.+?)"|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkIpb($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/ips_kernel')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/ips_kernel/class_xml.php');
            if (preg_match('|IP.Board\s+v([0-9\.]+)|si', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkWebAsyst($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/wbs/installer')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/license.txt');
            if (preg_match('|v([0-9\.]+)|si', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkOsCommerce($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/includes/version.php')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/includes/version.php');
            if (preg_match('|([0-9\.]+)|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkDrupal($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/sites/all')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/CHANGELOG.txt');
            if (preg_match('|Drupal\s+([0-9\.]+)|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        if (file_exists($dir . '/core/lib/Drupal.php')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/core/lib/Drupal.php');
            if (preg_match('|VERSION\s*=\s*\'(\d+\.\d+\.\d+)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        if (file_exists($dir . 'modules/system/system.info')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . 'modules/system/system.info');
            if (preg_match('|version\s*=\s*"\d+\.\d+"|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkMODX($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/manager/assets')) {
            $res = true;

            // no way to pick up version
        }

        return $res;
    }

    private function checkInstantCms($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/plugins/p_usertab')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/index.php');
            if (preg_match('|InstantCMS\s+v([0-9\.]+)|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkPhpBb($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/includes/acp')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/config.php');
            if (preg_match('|phpBB\s+([0-9\.x]+)|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkVBulletin($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        // removed dangerous code from here, see DEF-10390 for details

        return $res;
    }

    private function checkPhpShopScript($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/install/consts.php')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/install/consts.php');
            if (preg_match('|STRING_VERSION\',\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }
}


class CloudAssistedRequest
{
    const API_URL = 'https://api.imunify360.com/api/hashes/check';

    private $timeout    = 60;
    private $server_id  = '';

    public function __construct($server_id, $timeout = 60) 
    {
        $this->server_id    = $server_id;
        $this->timeout      = $timeout;
    }

    public function checkFilesByHash($list_of_hashes = array())
    {
        if (empty($list_of_hashes)) {
            return array(
                array(), 
                array(),
                'white' => array(),
                'black' => array(),
            );
        }

        $result = $this->request($list_of_hashes);

        $white  = isset($result['white']) ? $result['white'] : [];
        $black  = isset($result['black']) ? $result['black'] : [];

        return [
            $white,
            $black,
            'white' => $white,
            'black' => $black,
        ];
    }

    private function request($list_of_hashes)
    {
        $url = self::API_URL . '?server_id=' . urlencode($this->server_id) . '&indexed=1';

        $data = array(
            'hashes' => $list_of_hashes,
        );

        $json_hashes = json_encode($data);

        $info = [];
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL            , $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST  , 'GET');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , false);
            curl_setopt($ch, CURLOPT_TIMEOUT        , $this->timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , $this->timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
            curl_setopt($ch, CURLOPT_HTTPHEADER     , array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS     , $json_hashes);
            $response_data  = curl_exec($ch);
            $info           = curl_getinfo($ch);
            $errno          = curl_errno($ch);
            curl_close($ch);
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $http_code      = isset($info['http_code']) ? $info['http_code'] : 0;
        if ($http_code !== 200) {
            if ($errno == 28) {
                throw new Exception('Reuqest timeout! Return code: ' . $http_code . ' Curl error num: ' . $errno);
            }
            throw new Exception('Invalid response from the Cloud Assisted server! Return code: ' . $http_code . ' Curl error num: ' . $errno);
        }
        $result = json_decode($response_data, true);
        if (is_null($result)) {
            throw new Exception('Invalid json format in the response!');
        }
        if (isset($result['error'])) {
            throw new Exception('API server returned error!');
        }
        if (!isset($result['result'])) {
            throw new Exception('API server returned error! Cannot find field "result".');
        }

        return $result['result'];
    }
}

class JSONReport
{
    const REPORT_MASK_DOORWAYS  = 1<<2;
    const REPORT_MASK_SUSP      = 1<<3;
    const REPORT_MASK_FULL      = self::REPORT_MASK_DOORWAYS | self::REPORT_MASK_SUSP;
    
    private $raw_report = array();
    private $extended_report;
    private $rapid_account_scan;
    private $ai_extra_warn;
    private $ai_hoster;
    private $report_mask;
    public $noPrefix;
    public $addPrefix;
    public $mnemo;
    
    public function __construct($mnemo, $path, $db_location, $db_meta_info_version, $report_mask, $extended_report, $rapid_account_scan, $ai_version, $ai_hoster, $ai_extra_warn)
    {
        $this->mnemo = $mnemo;
        $this->ai_extra_warn = $ai_extra_warn;
        $this->extended_report = $extended_report;
        $this->rapid_account_scan = $rapid_account_scan;
        $this->ai_hoster = $ai_hoster;
        $this->report_mask = $report_mask;

        $this->raw_report = [];
        $this->raw_report['summary'] = array(
            'scan_path'     => $path,
            'report_time'   => time(),
            'ai_version'    => $ai_version,
            'db_location'   => $db_location,
            'db_version'    => $db_meta_info_version,
        );
    }

    public function addVars($vars, $scan_time)
    {
        $summary_counters                       = array();
        $summary_counters['redirect']           = count($vars->redirect);
        $summary_counters['critical_php']       = count($vars->criticalPHP);
        $summary_counters['critical_js']        = count($vars->criticalJS);
        $summary_counters['phishing']           = count($vars->phishing);
        $summary_counters['unix_exec']          = 0; // count($g_UnixExec);
        $summary_counters['iframes']            = 0; // count($g_Iframer);
        $summary_counters['not_read']           = count($vars->notRead);
        $summary_counters['base64']             = 0; // count($g_Base64);
        $summary_counters['heuristics']         = 0; // count($g_HeuristicDetected);
        $summary_counters['symlinks']           = count($vars->symLinks);
        $summary_counters['big_files_skipped']  = count($vars->bigFiles);
        $summary_counters['suspicious']         = count($vars->warningPHP);

        $this->raw_report['summary']['counters'] = $summary_counters;
        $this->raw_report['summary']['total_files'] = $vars->foundTotalFiles;
        $this->raw_report['summary']['scan_time'] = $scan_time;

        if ($this->extended_report && $this->rapid_account_scan) {
            $this->raw_report['summary']['counters']['rescan_count'] = $vars->rescanCount;
        }

        $this->raw_report['vulners'] = $this->getRawJsonVuln($vars->vulnerable, $vars);

        if (count($vars->criticalPHP) > 0) {
            $this->raw_report['php_malware'] = $this->getRawJson($vars->criticalPHP, $vars, $vars->criticalPHPFragment, $vars->criticalPHPSig);
        }

        if (count($vars->blackFiles) > 0) {
            $this->raw_report['cloudhash'] = $this->getRawBlackData($vars->blackFiles);
        }

        if (count($vars->criticalJS) > 0) {
            $this->raw_report['js_malware'] = $this->getRawJson($vars->criticalJS, $vars, $vars->criticalJSFragment, $vars->criticalJSSig);
        }

        if (count($vars->notRead) > 0) {
            $this->raw_report['not_read'] = $vars->notRead;
        }

        if ($this->ai_hoster) {
            if (count($vars->phishing) > 0) {
                $this->raw_report['phishing'] = $this->getRawJson($vars->phishing, $vars, $vars->phishingFragment, $vars->phishingSigFragment);
            }
            if (count($vars->redirect) > 0) {
                $this->raw_report['redirect'] = $this->getRawJson($vars->redirect, $vars, $vars->redirectPHPFragment);
            }
            if (count($vars->symLinks) > 0) {
                $this->raw_report['sym_links'] = $vars->symLinks;
            }
        }
        else {
            if (count($vars->adwareList) > 0) {
                $this->raw_report['adware'] = $this->getRawJson($vars->adwareList, $vars, $vars->adwareListFragment);
            }
            if (count($vars->bigFiles) > 0) {
                $this->raw_report['big_files'] = $this->getRawJson($vars->bigFiles, $vars);
            }
            if ((count($vars->doorway) > 0) && JSONReport::checkMask($this->report_mask, JSONReport::REPORT_MASK_DOORWAYS)) {
                $this->raw_report['doorway'] = $this->getRawJson($vars->doorway, $vars);
            }
            if (count($vars->CMS) > 0) {
                $this->raw_report['cms'] = $vars->CMS;
            }
        }

        if ($this->ai_extra_warn) {
            if ((count($vars->warningPHP) > 0) && JSONReport::checkMask($this->report_mask, JSONReport::REPORT_MASK_FULL)) {
                $this->raw_report['suspicious'] = $this->getRawJson($vars->warningPHP, $vars, $vars->warningPHPFragment, $vars->warningPHPSig);
            }
        }
    }
    
    public static function checkMask($mask, $need)
    {
        return (($mask & $need) == $need);
    }
    
    public function write($filepath)
    {
        $res = @json_encode($this->raw_report);
        if ($l_FH = fopen($filepath, 'w')) {
            fputs($l_FH, $res);
            fclose($l_FH);
        }
        return $res;
    }
    
    public function writePHPSerialized($filepath)
    {
        if ($l_FH = fopen($filepath, 'w')) {
            fputs($l_FH, serialize($this->raw_report));
            fclose($l_FH);
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    
    private function getRawJsonVuln($par_List, $vars) 
    {
        $results = array();
        $l_Src   = array(
            '&quot;',
            '&lt;',
            '&gt;',
            '&amp;',
            '&#039;',
            '<' . '?php.'
        );
        $l_Dst   = array(
            '"',
            '<',
            '>',
            '&',
            '\'',
            '<' . '?php '
        );

        for ($i = 0; $i < count($par_List); $i++) {
            $l_Pos      = $par_List[$i]['ndx'];

            $fn = $this->addPrefix . str_replace($this->noPrefix, '', $vars->structure['n'][$l_Pos]);
            if (ENCODE_FILENAMES_WITH_BASE64) {
                $res['fn'] = base64_encode($fn);
            } else {
                $res['fn']  = convertToUTF8($fn);
            }

            $res['sig'] = $par_List[$i]['id'];

            $res['ct']    = $vars->structure['c'][$l_Pos];
            $res['mt']    = $vars->structure['m'][$l_Pos];
            $res['et']    = $vars->structure['e'][$l_Pos];
            $res['sz']    = $vars->structure['s'][$l_Pos];
            $res['sigid'] = 'vuln_' . md5($vars->structure['n'][$l_Pos] . $par_List[$i]['id']);

            $results[] = $res;
        }

        return $results;
    }

    private function getRawJson($par_List, $vars, $par_Details = null, $par_SigId = null) 
    {
        global $g_NoPrefix, $g_AddPrefix;
        $results = array();
        $l_Src   = array(
            '&quot;',
            '&lt;',
            '&gt;',
            '&amp;',
            '&#039;',
            '<' . '?php.'
        );
        $l_Dst   = array(
            '"',
            '<',
            '>',
            '&',
            '\'',
            '<' . '?php '
        );

        for ($i = 0; $i < count($par_List); $i++) {
            if ($par_SigId != null) {
                $l_SigId = 'id_' . $par_SigId[$i];
            } else {
                $l_SigId = 'id_n' . rand(1000000, 9000000);
            }

            $l_Pos     = $par_List[$i];

            $fn = $this->addPrefix . str_replace($this->noPrefix, '', $vars->structure['n'][$l_Pos]);
            if (ENCODE_FILENAMES_WITH_BASE64) {
                $res['fn'] = base64_encode($fn);
            } else {
                $res['fn']  = convertToUTF8($fn);
            }

            if ($par_Details != null) {
                $res['sig'] = preg_replace('|(L\d+).+__AI_MARKER__|smi', '[$1]: ...', $par_Details[$i]);
                $res['sig'] = preg_replace('/[^\x20-\x7F]/', '.', $res['sig']);
                $res['sig'] = preg_replace('/__AI_LINE1__(\d+)__AI_LINE2__/', '[$1] ', $res['sig']);
                $res['sig'] = preg_replace('/__AI_MARKER__/', ' @!!!>', $res['sig']);
                $res['sig'] = str_replace($l_Src, $l_Dst, $res['sig']);
            }

            $res['sig'] = convertToUTF8($res['sig']);

            $res['ct']    = $vars->structure['c'][$l_Pos];
            $res['mt']    = $vars->structure['m'][$l_Pos];
            $res['sz']    = $vars->structure['s'][$l_Pos];
            $res['et']    = $vars->structure['e'][$l_Pos];
            $res['hash']  = $vars->structure['crc'][$l_Pos];
            $res['sigid'] = $l_SigId;
            if (isset($vars->structure['sha256'][$l_Pos])) {
                $res['sha256'] = $vars->structure['sha256'][$l_Pos];
            } else {
                $res['sha256'] = '';
            }


            if (isset($par_SigId) && isset($this->mnemo[$par_SigId[$i]])) {
                $res['sn'] = $this->mnemo[$par_SigId[$i]];
            } else {
                $res['sn'] = '';
            }

            $results[] = $res;
        }

        return $results;
    }

    private function getRawBlackData($black_list)
    {
        $result = array();
        foreach ($black_list as $filename => $hash)
        {
            try {
                $stat = stat($filename);
                $sz   = $stat['size'];
                $ct   = $stat['ctime'];
                $mt   = $stat['mtime'];
            }
            catch (Exception $e) {
                continue;
            }

            $result[] = array(
                'fn'    => $filename,
                'sig'   => '',
                'ct'    => $ct,
                'mt'    => $mt,
                'et'    => $hash['ts'],
                'sz'    => $sz,
                'hash'  => $hash['h'],
                'sigid' => crc32($filename),
                'sn'    => 'cld',
            );
        }
        return $result;
    }
}


class CloudAssistedFiles
{
    private $white = [];
    private $black = [];

    public function __construct(CloudAssistedRequest $car, $file_list)
    {
        $list_of_hash       = [];
        $list_of_filepath   = [];
        foreach ($file_list as $filepath)
        {
            if (!file_exists($filepath) || !is_readable($filepath) || is_dir($filepath)) {
                continue;
            }
            try {
                $list_of_hash[]     = hash('sha256', file_get_contents($filepath));
                $list_of_filepath[] = $filepath;
            }
            catch (Exception $e) {
                
            }
        }
        unset($file_list);
        
        try {
            list($white_raw, $black_raw) = $car->checkFilesByHash($list_of_hash);
        }
        catch (Exception $e) {
            throw $e;
        }
        
        $this->white = $this->getListOfFile($white_raw, $list_of_hash, $list_of_filepath);
        $this->black = $this->getListOfFile($black_raw, $list_of_hash, $list_of_filepath);
        
        unset($white_raw);
        unset($black_raw);
        unset($list_of_hash);
        unset($list_of_filepath);
    }
    
    public function getWhiteList()
    {
        return $this->white;
    }

    public function getBlackList()
    {
        return $this->black;
    }
    
    // =========================================================================
    
    private function getListOfFile($data_raw, $list_of_hash, $list_of_filepath)
    {
        $result = [];
        foreach ($data_raw as $index)
        {
            if (!isset($list_of_hash[$index])) {
                continue;
            }
            $result[$list_of_filepath[$index]]['h'] = $list_of_hash[$index];
            $result[$list_of_filepath[$index]]['ts'] = time();
        }
        return $result;
    }    
}


class DetachedMode
{
    protected $workdir;
    protected $scan_id;
    protected $pid_file;
    protected $report_file;
    protected $done_file;
    protected $vars;
    protected $start_time;
    protected $json_report;
    protected $sock_file;

    public function __construct($scan_id, $vars, $listing, $start_time, $json_report, $use_base64, $basedir = '/var/imunify360/aibolit/run', $sock_file = '/var/run/defence360agent/generic_sensor.sock.2')
    {
        $this->scan_id = $scan_id;
        $this->vars = $vars;
        $this->setWorkDir($basedir, $scan_id);
        $this->pid_file = $this->workdir . '/pid';
        $this->report_file = $this->workdir . '/report.json';
        $this->done_file = $this->workdir . '/done';
        $this->start_time = $start_time;
        $this->json_report = $json_report;
        $this->setSocketFile($sock_file);

        $this->checkSpecs($this->workdir, $listing);

        file_put_contents($this->pid_file, strval(getmypid()));

        $this->scan($listing, $use_base64);
        $this->writeReport();
        $this->complete();
    }

    protected function scan($listing, $use_base64)
    {
        $s_file = new SplFileObject($listing);
        $s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        if (function_exists('QCR_GoScan')) {
            QCR_GoScan($s_file, $this->vars, $use_base64, false);
            whitelisting($this->vars);
        }
        unset($s_file);
    }

    protected function checkSpecs($workdir, $listing)
    {
        if (!file_exists($workdir) && !mkdir($workdir)) {
            die('Error! Cannot create workdir ' . $workdir . ' for detached scan.');
        } elseif (file_exists($workdir) && !is_writable($workdir)) {
            die('Error! Workdir ' . $workdir . ' is not writable.');
        } elseif (!file_exists($listing) || !is_readable($listing)) {
            die('Error! Listing file ' . $listing . ' not exists or not readable');
        }
    }

    protected function writeReport()
    {
        $scan_time = round(microtime(true) - $this->start_time, 1);
        $json_report = $this->json_report->call($this);
        $json_report->addVars($this->vars, $scan_time);
        $json_report->write($this->report_file);
    }

    protected function complete()
    {
        @touch($this->done_file);
        $complete = array(
            'method' => 'MALWARE_SCAN_COMPLETE',
            'scan_id' => $this->scan_id,
        );
        $json_complete = json_encode($complete) . "\n";
        $socket = fsockopen('unix://' . $this->sock_file);
        stream_set_blocking($socket, false);
        fwrite($socket, $json_complete);
        fclose($socket);
    }

    protected function setWorkDir($dir, $scan_id)
    {
        $this->workdir = $dir . '/' . $scan_id;
    }

    protected function setSocketFile($sock)
    {
        $this->sock_file = $sock;
    }
}


/**
 * Class ResidentMode used to stay aibolit alive in memory and wait for a job.
 */
class ResidentMode
{
    /**
     * parent dir for all resident aibolit related
     * @var string
     */
    protected $resident_dir;
    /**
     * directory for all jobs to be processed by aibolit
     * @var string
     */
    protected $resident_in_dir;
    /**
     * directory with all the malicious files reports to be processed by imunify
     * @var string
     */
    protected $resident_out_dir;
    /**
     * resident aibolit pid
     * @var string
     */
    protected $aibolit_pid;
    /**
     * file lock used to make sure we start only one aibolit
     * @var string
     */
    protected $aibolit_start_lock;
    /**
     * status file used to make sure aibolit didn't get stuck
     * @var string
     */
    protected $aibolit_status_file;
    /**
     * number of seconds while aibolit will stay alive, while not receiving any work
     * @var int
     */
    protected $stay_alive;
    /**
     * maximum number of seconds without updating ABOLIT_STATUS_FILE,
     * used to track if AIBOLIT is stuck, should be killed
     * @var int
     */
    protected $stuck_timeout;
    /**
     * number of seconds scripts would wait for aibolit to finish / send signal
     * @var int
     */
    protected $upload_timeout;
    /**
     * max number of files to pick
     * @var int
     */
    protected $max_files_per_notify_scan;
    /**
     * timestamp of last scan
     * @var int
     */
    protected $last_scan_time;
    /**
     * time to sleep between lifecycle iterations in microseconds
     */
    protected $sleep_time;

    protected $scannedNotify = 0;

    protected $report;

    protected $resident_in_dir_notify;
    protected $resident_in_dir_upload;
    protected $blacklist;
    protected $watchdog_socket;
    protected $activation_socket;
    protected $systemd = false;
    protected $interval = 0;
    protected $lastKeepAlive = 0;

    /**
     * ResidentMode constructor.
     * @param $options
     */
    public function __construct(
        Closure $report,
        $blacklist = null,
        $resident_dir = '/var/imunify360/aibolit/resident',
        $stay_alive = 30,
        $stuck_timeout = 5,
        $upload_timeout = 10,
        $max_files_per_notify_scan = 500,
        $sleep_time = 100000
    ) {
        $this->setResidentDir($resident_dir);
        $this->resident_in_dir = $this->resident_dir . '/in';
        $this->resident_in_dir_upload = $this->resident_in_dir . '/upload-jobs';
        $this->resident_in_dir_notify = $this->resident_in_dir . '/notify-jobs';
        $this->resident_out_dir = $this->resident_dir . '/out';
        $this->aibolit_pid = $this->resident_dir . '/aibolit.pid';
        $this->aibolit_start_lock = $this->resident_dir . '/start.lock';
        $this->aibolit_status_file = $this->resident_dir . '/aibolit.status';
        $this->stay_alive = $stay_alive;
        $this->stuck_timeout = $stuck_timeout;
        $this->upload_timeout = $upload_timeout;
        /** @var int $max_files_per_notify_scan */
        if (!empty($max_files_per_notify_scan)) {
            $this->max_files_per_notify_scan = $max_files_per_notify_scan;
        }
        $this->sleep_time = $sleep_time;
        $this->report = $report;
        $this->blacklist = $blacklist;

        umask(0);
        if (!file_exists($this->resident_dir)) {
            mkdir($this->resident_dir, 0777, true);
        }
        if (!file_exists($this->resident_in_dir)) {
            mkdir($this->resident_in_dir, 0755);
        }
        if (!file_exists($this->resident_out_dir)) {
            mkdir($this->resident_out_dir, 0755);
        }
        if (!file_exists($this->resident_in_dir_notify)) {
            mkdir($this->resident_in_dir_notify, 0700);
        }
        if (!file_exists($this->resident_in_dir_upload)) {
            mkdir($this->resident_in_dir_upload, 01777);
        }

        $this->checkSpecs();

        $addr = getenv('NOTIFY_SOCKET');
        if ($addr[0] == '@') {
            $addr = "\0";
        }

        if ($addr) {
            $this->systemd = true;
        }

        if ($this->systemd) {
            $this->watchdog_socket = fsockopen('udg://' . $addr);
            stream_set_blocking($this->watchdog_socket, false);

            $this->activation_socket = fopen('php://fd/3', 'r');
            if ($this->activation_socket === false) {
                die("Something went wrong with activation socket.");
            }
            stream_set_blocking($this->activation_socket, false);

            if (getenv('WATCHDOG_USEC') !== false) {
                $this->interval = intval(getenv('WATCHDOG_USEC'));
            } else {
                $this->interval = 1000000;
            }
        }
        $this->lifeCycle();
    }

    protected function isRootWriteable($folder)
    {
        if (!file_exists($folder) || !is_dir($folder)) {
            return false;
        }

        $owner_id = (int)fileowner($folder);
        if (function_exists('posix_getpwuid')) {
            $owner = posix_getpwuid($owner_id);
            if (!isset($owner['name']) || $owner['name'] !== 'root') {
                return false;
            }
        } elseif ($owner_id != 0) {
            return false;
        }

        $perms = fileperms($folder);
        if (($perms & 0x0100)                           // owner r
            && ($perms & 0x0080)                        // owner w
            && ($perms & 0x0040) && !($perms & 0x0800)  // owner x
            && !($perms & 0x0010)                       // group without w
            && !($perms & 0x0002)                       // other without w
        ) {
            return true;
        }
        return false;
    }

    protected function isWorldWriteable($folder)
    {
        if (!file_exists($folder) || !is_dir($folder)) {
            return false;
        }

        $perms = fileperms($folder);
        if (($perms & 0x0004)                           // other r
            && ($perms & 0x0002)                        // other w
            && ($perms & 0x0200)                        // sticky bit
        ) {
            return true;
        }
        return false;
    }

    protected function checkSpecs()
    {
        if (!extension_loaded('posix')) {
            die('Error! For resident scan need posix extension.');
        } elseif (!$this->isRootWriteable($this->resident_in_dir_notify)) {
            die('Error! Notify in dir ' . $this->resident_in_dir_notify . ' must be root writeable.');
        } elseif (!$this->isWorldWriteable($this->resident_in_dir_upload)) {
            die('Error! Upload in dir ' . $this->resident_in_dir_upload . ' must be world writeable.');
        }
    }

    protected function setResidentDir($dir)
    {
        $this->resident_dir = $dir;
    }

    protected function writeReport($vars, $scan_time, $type, $file)
    {
        $report = $this->report->call($this);
        $malware = (count($vars->criticalPHP) > 0)
            || (count($vars->criticalJS) > 0)
            || (count($vars->blackFiles) > 0)
            || (count($vars->warningPHP) > 0);

        if ($type == 'upload') {
            $pid = intval(basename($file, '.upload_job'));
            if ($malware) {
                posix_kill($pid, SIGUSR1);
            } else {
                posix_kill($pid, SIGUSR2);
            }
        } elseif ($type == 'notify' && $malware) {
            $filename = basename($file, '.notify_job');
            $report->addVars($vars, $scan_time);
            $report->write($this->resident_out_dir . '/' . $filename . '.report.tmp');
            @rename($this->resident_out_dir . '/' . $filename . '.report.tmp', $this->resident_out_dir . '/' . $filename . '.report');
            unset($report);
        }
    }

    protected function isJobFileExists($pattern)
    {
        if (count(glob($this->resident_in_dir . $pattern)) > 0) {
            return true;
        }
        return false;
    }

    protected function isUploadJob()
    {
        if ($this->isJobFileExists('/upload-jobs/*.upload_job')) {
            return true;
        }
        return false;
    }

    protected function scanJob($job_file, $type)
    {
        $start_time = microtime(true);

        $vars = new Variables();
        $vars->blacklist = $this->blacklist;

        $files_to_scan = array();
        $count = 0;

        $job = json_decode(file_get_contents($job_file));

        if ($type == 'notify') {
            $files_to_scan = $job->files;
            $count = count($files_to_scan);

            if ($count > $this->max_files_per_notify_scan) {
                // TODO: show a warning: too many files to scan, the job was skipped
                return true;
            }

            if ($this->scannedNotify + $count > $this->max_files_per_notify_scan) {
                $this->scannedNotify = 0;
                unset($vars);
                unset($files_to_scan);
                return false;
            } else {
                $this->scannedNotify += $count;
            }
        } elseif ($type == 'upload') {
            $files_to_scan = $job->files;
            $count = count($files_to_scan);

            if ($count > 1) {
                // TODO: show a warning: too many files to scan, the job was skipped
                return true;
            }
        }

        $vars->foundTotalFiles = $count;

        if (function_exists('QCR_GoScan')) {
            if ($this->systemd) {
                QCR_GoScan($files_to_scan, $vars, array($this, 'keepAlive'), true, false);
            } else {
                QCR_GoScan($files_to_scan, $vars, null, true, false);
            }

            whitelisting($vars);
        }

        $scan_time = round(microtime(true) - $start_time, 1);
        $this->writeReport($vars, $scan_time, $type, $job_file);

        unset($vars);
        unset($files_to_scan);

        if (defined('PROGRESS_LOG_FILE') && file_exists(PROGRESS_LOG_FILE)) {
            @unlink(PROGRESS_LOG_FILE);
        }

        if (defined('CREATE_SHARED_MEMORY') && CREATE_SHARED_MEMORY) {
            shmop_delete(SHARED_MEMORY);
        }

        if (defined('SHARED_MEMORY')) {
            shmop_close(SHARED_MEMORY);
        }

        return true;
    }

    protected function isNotifyJob()
    {
        if ($this->isJobFileExists('/notify-jobs/*.notify_job')) {
            return true;
        }
        return false;
    }

    protected function scanUploadJob()
    {
        $files = glob($this->resident_in_dir_upload . '/*.upload_job');
        $this->scanJob($files[0], 'upload');
        unlink($files[0]);
    }

    protected function scanNotifyJob()
    {
        $files = glob($this->resident_in_dir_notify . '/*.notify_job');
        foreach ($files as $job) {
            $res = $this->scanJob($job, 'notify');
            if ($res) {
                unlink($job);
            } else {
                break;
            }
        }
    }

    public function keepAlive()
    {
        if (intval((microtime(true) - $this->lastKeepAlive) * 1000000) > $this->interval / 2) {
            while (fread($this->activation_socket, 1024)) {
                // do nothing but read all dat from the socket
            }
            fwrite($this->watchdog_socket, 'WATCHDOG=1');
            $this->lastKeepAlive = microtime(true);
        }
    }

    protected function lifeCycle()
    {
        $this->last_scan_time = time();
        while (true) {
            if ($this->systemd) {
                $this->keepAlive();
            }
            while ($this->isUploadJob()) {
                $this->last_scan_time = time();
                $this->scanUploadJob();
            }

            while ($this->isNotifyJob() && !$this->isUploadJob()) {
                $this->last_scan_time = time();
                $this->scanNotifyJob();
            }
            if ($this->last_scan_time + $this->stay_alive < time()) {
                break;
            }
            touch($this->aibolit_status_file);
            usleep($this->sleep_time); // 1\10 of second by default
        }
        if ($this->systemd) {
            fclose($this->watchdog_socket);
            fclose($this->activation_socket);
        }
        unlink($this->aibolit_status_file);
    }
}


/**
 * Class FileHashMemoryDb.
 *
 * Implements operations to load the file hash database into memory and work with it.
 */
class FileHashMemoryDb
{
    const HEADER_SIZE = 1024;
    const ROW_SIZE = 20;

    /**
     * @var int
     */
    private $count;
    /**
     * @var array
     */
    private $header;
    /**
     * @var resource
     */
    private $fp;
    /**
     * @var array
     */
    private $data;

    /**
     * Creates a new DB file and open it.
     *
     * @param $filepath
     * @return FileHashMemoryDb
     * @throws Exception
     */
    public static function create($filepath)
    {
        if (file_exists($filepath)) {
            throw new Exception('File \'' . $filepath . '\' already exists.');
        }

        $value = pack('V', 0);
        $header = array_fill(0, 256, $value);
        file_put_contents($filepath, implode($header));

        return new self($filepath);
    }

    /**
     * Opens a particular DB file.
     *
     * @param $filepath
     * @return FileHashMemoryDb
     * @throws Exception
     */
    public static function open($filepath)
    {
        if (!file_exists($filepath)) {
            throw new Exception('File \'' . $filepath . '\' does not exist.');
        }

        return new self($filepath);
    }

    /**
     * FileHashMemoryDb constructor.
     *
     * @param mixed $filepath
     * @throws Exception
     */
    private function __construct($filepath)
    {
        $this->fp = fopen($filepath, 'rb');

        if (false === $this->fp) {
            throw new Exception('File \'' . $filepath . '\' can not be opened.');
        }

        try {
            $this->header = unpack('V256', fread($this->fp, self::HEADER_SIZE));
            $this->count = (int) (max(0, filesize($filepath) - self::HEADER_SIZE) / self::ROW_SIZE);
            foreach ($this->header as $chunk_id => $chunk_size) {
                if ($chunk_size > 0) {
                    $str = fread($this->fp, $chunk_size);
                } else {
                    $str = '';
                }
                $this->data[$chunk_id] = $str;
            }
        } catch (Exception $e) {
            throw new Exception('File \'' . $filepath . '\' is not a valid DB file. An original error: \'' . $e->getMessage() . '\'');
        }
    }

    /**
     * Calculates and returns number of hashes stored in a loaded database.
     *
     * @return int number of hashes stored in a DB
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Find hashes in a DB.
     *
     * @param array $list of hashes to find in a DB
     * @return array list of hashes from the $list parameter that are found in a DB
     */
    public function find($list)
    {
        sort($list);
        
        $hash = reset($list);

        $found = array();

        foreach ($this->header as $chunk_id => $chunk_size) {
            if ($chunk_size > 0) {
                $str = $this->data[$chunk_id];

                do {
                    $raw = pack("H*", $hash);
                    $id  = ord($raw[0]) + 1;

                    if ($chunk_id == $id AND $this->binarySearch($str, $raw)) {
                        $found[] = (string)$hash;
                    }

                } while ($chunk_id >= $id AND $hash = next($list));

                if ($hash === false) {
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * Searches $item in the $str using an implementation of the binary search algorithm.
     *
     * @param $str
     * @param $item
     * @return bool
     */
    private function binarySearch($str, $item) {
        $item_size = strlen($item);
        if ($item_size == 0) {
            return false;
        }

        $first = 0;

        $last = floor(strlen($str) / $item_size);

        while ($first < $last) {
            $mid = $first + (($last - $first) >> 1);
            $b   = substr($str, $mid * $item_size, $item_size);
            if (strcmp($item, $b) <= 0) {
                $last = $mid;
            } else {
                $first = $mid + 1;
            }
        }

        $b = substr($str, $last * $item_size, $item_size);
        if ($b == $item) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * FileHashDB destructor.
     */
    public function __destruct()
    {
        fclose($this->fp);
    }
}

class FilepathEscaper
{
    public static function encodeFilepath($filepath)
    {
        return str_replace(array('\\', "\n", "\r"), array('\\\\', '\\n', '\\r'), $filepath);
    }
    
    public static function decodeFilepath($filepath)
    {
        return preg_replace_callback('~(\\\\+)(.)~', function ($matches) {
            $count = strlen($matches[1]);
            if ($count % 2 === 0) {
                return str_repeat('\\', $count/2) . $matches[2];
            }
            return str_repeat('\\', floor($count/2)) . stripcslashes('\\' . $matches[2]);
        }, $filepath);
    }
    
    public static function encodeFilepathByBase64($filepath)
    {
        return base64_encode($filepath);
    }
    
    public static function decodeFilepathByBase64($filepath_base64)
    {
        return base64_decode($filepath_base64);
    }
}


/**
 * Class RapidScanStorageRecord.
 *
 * Implements db record for RapidScan
 */
class RapidScanStorageRecord
{
    const WHITE = 1; // white listed file in cloud db
    const BLACK = 6; // black listed file in cloud db
    const DUAL_USE = 2; // dual used listed file in cloud db
    const UNKNOWN = 3; // unknown file --> file not listed in cloud db
    const HEURISTIC = 4; //detected as heuristic
    const CONFLICT = 5; // we have filename hashing conflict for this file
    const NEWFILE = 0; // this is a new file (or content changed)
    const RX_MALWARE = 7; // detected as malware by rx scan
    const RX_SUSPICIOUS = 8; // detected as suspicious by rx scan
    const RX_GOOD = 9; // detected as good by rx scan

    /**
     * @var string;
     */
    private $filename;
    /**
     * @var int
     */
    private $key;
    /**
     * @var int
     */
    private $updated_ts;
    /**
     * @var int
     */
    private $verdict;
    /**
     * @var string
     */
    private $sha2;
    /**
     * @var string
     */
    private $signature = '';
    /**
     * @var string
     */
    private $snippet = '';

    /**
     * RapidScanStorageRecord constructor.
     * @param $key
     * @param $updated_ts
     * @param int $verdict
     * @param $sha2
     * @param string $signature
     */
    private function __construct($key, $updated_ts, $verdict, $sha2, $signature, $filename, $snippet)
    {
        $this->filename = $filename;
        $this->key = $key;
        $this->updated_ts = $updated_ts;
        $this->verdict = $verdict;
        $this->sha2 = $sha2;
        $this->snippet = $snippet;
        if ($signature!=='') {
            $this->signature = self::padTo10Bytes($signature);
        }
    }

    /**
     * Create db storage record from file
     * @param $filename
     * @param string $signature
     * @param int $verdict
     * @return RapidScanStorageRecord
     * @throws Exception
     */
    public static function fromFile($filename, $signature = '', $verdict = self::UNKNOWN, $snippet = '')
    {
        if (!file_exists($filename)) {
            throw new Exception('File \'' . $filename . '\' doesn\'t exists.');
        }

        $key = intval(strval(self::fileNameHash($filename)) . strval(fileinode($filename)));
        $updated_ts = max(filemtime($filename), filectime($filename));
        $sha2 = '';
        if (!$verdict) {
            $verdict = self::NEWFILE;
        }
        if ($signature!=='') {
            $signature = self::padTo10Bytes($signature);
        }
        return new self($key, $updated_ts, $verdict, $sha2, $signature, $filename, $snippet);
    }

    /**
     * @param $array
     * @return RapidScanStorageRecord
     */
    public static function fromArray($array)
    {
        $key = $array['key'];
        $updated_ts = $array['updated_ts'];
        $sha2 = hex2bin($array['sha2']);
        $verdict = $array['verdict'];
        $signature = $array['signature'];
        return new self($key, $updated_ts, $verdict, $sha2, $signature, '', '');
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array['key'] = $this->key;
        $array['updated_ts'] = $this->updated_ts;
        $array['verdict'] = $this->verdict;
        $array['sha2'] = bin2hex($this->sha2);
        $array['signature'] = $this->signature;
        return $array;
    }

    /**
     * @return array
     */
    public function calcSha2()
    {
        $this->sha2 = hash('sha256', file_get_contents($this->filename), true);
    }

    /**
     * @param $verdict
     */
    public function setVerdict($verdict)
    {
        $this->verdict = $verdict;
    }

    /**
     * @return int
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param $signature
     */
    public function setSignature($signature)
    {
        if ($signature!=='') {
            $this->signature = self::padTo10Bytes($signature);
        }
    }

    /**
     * Unpack bytestring $value to RapidScanStorageRecord
     * @param $hash
     * @param $value
     * @return RapidScanStorageRecord
     */
    public static function unpack($hash, $value)
    {
        // pack format
        // 8 bytes timestamp
        // 1 byte verdict
        // 32 bytes sha2
        // 10 bytes signature (only for BLACK, DUAL_USE, RX_MALWARE, RX_SUSPICIOUS)
        // note - we will hold bloomfilter for file later on for those that are WHITE
        // it will be used to detect installed apps

        $signature = '';
        $timestamp = unpack("l", substr($value, 0, 8));
        $updated_ts = array_pop($timestamp);
        $verdict = $value[8];
        $verdict = intval(bin2hex($verdict));
        $sha2 = substr($value, 9, 32);
        if (in_array($verdict, array(self::BLACK, self::DUAL_USE, self::RX_MALWARE, self::RX_SUSPICIOUS))) {
            $signature = substr($value, 41, 10);  # 10 bytes signature string
        }
        if (strlen($value) > 51) {
            $snippet = substr($value, 51);
        } else {
            $snippet = '';
        }
        return new self($hash, $updated_ts, $verdict, $sha2, $signature, '', $snippet);
    }

    /**
     * Pack RapidScanStorageRecord to bytestring to save in db
     * @return string
     */
    public function pack()
    {
        $signature = '';
        if (strlen($this->signature) > 0) {
            $signature = $this->signature;
        }
        return (($this->updated_ts < 0) ? str_pad(pack("l", $this->updated_ts), 8, "\xff") : str_pad(pack("l", $this->updated_ts), 8, "\x00")) . pack("c", $this->verdict) . $this->sha2 . $signature . $this->snippet;
    }

    /**
     * Hash function for create hash of full filename to store in db as key
     * @param $str
     * @return int
     */
    private static function fileNameHash($str)
    {
        for ($i = 0, $h = 5381, $len = strlen($str); $i < $len; $i++) {
            $h = (($h << 5) + $h + ord($str[$i])) & 0x7FFFFFFF;
        }
        return $h;
    }

    /**
     * Convert string to utf-8 and fitting/padding it to 10 bytes
     * @param $str
     * @return string
     */
    private static function padTo10Bytes($str)
    {
        # convert string to bytes in UTF8, and add 0 bytes to pad it to 10
        # cut to 10 bytes of necessary
        $str = utf8_encode($str);
        $len = strlen($str);
        if ($len < 10) {
            $str = str_pad($str, 10, "\x00");
        } elseif ($len > 10) {
            $str = substr($str, 0, 10);
        }
        return $str;
    }

    /**
     * @return int
     */
    public function getUpdatedTs()
    {
        return $this->updated_ts;
    }

    /**
     * @return int
     */
    public function getVerdict()
    {
        return $this->verdict;
    }

    /**
     * @return string
     */
    public function getSha2()
    {
        return $this->sha2;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * @param $filename
     */
    public function setSnippet($snippet)
    {
        $this->snippet = $snippet;
    }
}


/**
 * Interface RapidScanStorage implements class to work with RapidScan db
 * @package Aibolit\Lib\Scantrack
 */
class RapidScanStorage
{
    /**
     * @var string
     */
    protected $old_dir;
    /**
     * @var string
     */
    protected $new_dir;
    /**
     * @var resource
     */
    protected $new_db;
    /**
     * @var resource
     */
    protected $old_db;
    /**
     * @var resource
     */
    private $wb;
    /**
     * @var int
     */
    public $batch_count;

    /**
     * RapidScanStorage constructor.
     * @param $base - folder where db located
     */
    public function __construct($base)
    {
        if(!is_dir($base)) mkdir($base);
        $this->old_dir = $base . '/current';
        $this->new_dir = $base . '/new';
        $options = array('create_if_missing' => true, 'compression'=> LEVELDB_NO_COMPRESSION);

        $this->new_db = new LevelDB($this->new_dir, $options);
        $this->old_db = new LevelDB($this->old_dir, $options);

        $this->wb = NULL;  // will be use to track writing to batch
        $this->batch_count = 0;
    }

    /**
     * @param RapidScanStorageRecord $record
     * @return bool
     */
    public function put(RapidScanStorageRecord $record)
    {
        $this->startBatch();
        $this->batch_count++;
        $value = $this->wb->put($record->getKey(), $record->pack());
        return $value;
    }

    /**
     * @param $hash
     * @return bool|RapidScanStorageRecord
     */
    public function getNew($hash)
    {
        $value = $this->new_db->get($hash);
        if($value) {
            $return = RapidScanStorageRecord::unpack($hash, $value);
            return $return;
        }
        return false;
    }

    /**
     * @param $hash
     * @return bool|RapidScanStorageRecord
     */
    public function getOld($hash)
    {
        $value = $this->old_db->get($hash);
        if($value) {
            $return = RapidScanStorageRecord::unpack($hash, $value);
            return $return;
        }
        return false;
    }

    /**
     * @param $hash
     * @return bool
     */
    public function delete($hash)
    {
        $return = $this->new_db->delete($hash);
        return $return;
    }

    /**
     * Close db, remove old db, move new to a new space
     */
    public function finish()
    {
        $this->old_db->close();
        $this->flushBatch();
        $this->new_db->close();

        self::rmtree($this->old_dir);
        rename($this->new_dir, $this->old_dir);
    }

    /**
     * Start batch operations
     */
    private function startBatch()
    {
        if(!$this->wb) {
            $this->wb = new LevelDBWriteBatch();
            $this->batch_count = 0;
        }
    }

    /**
     *  write all data in a batch, reset batch
     */
    public function flushBatch()
    {
        if ($this->wb) {
            $this->new_db->write($this->wb);
            $this->batch_count = 0;
            $this->wb = NULL;
        }
    }
    /**
     * Helper function to remove folder tree
     * @param $path
     */
    public static function rmTree($path)
    {
        if (is_dir($path)) {
            foreach (scandir($path) as $name) {
                if (in_array($name, array('.', '..'))) {
                    continue;
                }
                $subpath = $path.DIRECTORY_SEPARATOR . $name;
                self::rmTree($subpath);
            }
            rmdir($path);
        } else {
            unlink($path);
        }
    }
}

/**
 * This is actual class that does account level scan
 * and remembers the state of scan
 * Class RapidAccountScan
 * @package Aibolit\Lib\Scantrack
 */
class RapidAccountScan
{
    const RESCAN_ALL = 0; // mode of operation --> rescan all files that are not white/black/dual_use using cloud scanner & regex scanner
    const RESCAN_NONE = 1; // don't re-scan any files that we already scanned
    const RESCAN_SUSPICIOUS = 2; // only re-scan suspicious files using only regex scanner

    const MAX_BATCH = 1000; // max files to write in a db batch write
    const MAX_TO_SCAN = 1000; // max files to scan using cloud/rx scanner at a time

    private $db;
    private $vars = null;
    private $scanlist;
    private $collisions;
    private $processedFiles;
    private $rescan_count = 0;
    private $counter = 0;
    private $str_error = false;

    /**
     * RapidAccountScan constructor.
     * @param RapidScanStorage $rapidScanStorage
     */
    public function __construct($rapidScanStorage, &$vars, $counter = 0)
    {
        $this->db = $rapidScanStorage;
        $this->vars = $vars;
        $this->scanlist = array();
        $this->collisions = array();
        $this->processedFiles = 0;
        $this->counter = $counter;
    }

    /**
     * Get str error
     */
    public function getStrError()
    {
        return $this->str_error;
    }

    /**
     * Get count of rescan(regexp) files
     */
    public function getRescanCount()
    {
        return $this->rescan_count;
    }

    /**
     * placeholder for actual regex scan
     * return RX_GOOD, RX_MALWARE, RX_SUSPICIOUS and signature from regex scaner
     * if we got one
     */
    private function regexScan($filename, $i, $vars)
    {
        $this->rescan_count++;
        printProgress(++$this->processedFiles, $filename, $vars);
        $return = QCR_ScanFile($filename, $vars, null, $i, false);
        return $return;
    }

    /**
     * we will have batch of new files that we will scan
     * here we will write them into db once we scanned them
     * we need to check that there is no conflicts/collisions
     * in names, for that we check for data in db if such filename_hash
     * already exists, but we also keep set of filename_hashes of given
     * batch, to rule out conflicts in current batch as well
     */
    private function writeNew()
    {
        $this->collisions = array();
        foreach ($this->scanlist as $fileinfo) {
            if (in_array($fileinfo->getKey(), $this->collisions) || $this->db->getNew($fileinfo->getKey())) {
                $fileinfo->setVerdict(RapidScanStorageRecord::CONFLICT);
            }
            $this->collisions [] = $fileinfo->getKey();
            $this->db->put($fileinfo);
        }
    }

    /**
     * given a batch do cloudscan
     * @throws \Exception
     */
    private function doCloudScan()
    {
        if (count($this->scanlist) <= 0) {
            return;
        }

        $index_table = array();
        $blackfiles = array();

        $sha_list = array();

        foreach ($this->scanlist as $i => $fileinfo) {
            $sha_list[] = bin2hex($fileinfo->getSha2());
            $index_table[] = $i;
            $fileinfo->setVerdict(RapidScanStorageRecord::UNKNOWN);
        }

        $ca = Factory::instance()->create(CloudAssistedRequest::class, [CLOUD_ASSIST_TOKEN]);

        $white_raw = array();
        $black_raw = array();
        try {
            list($white_raw, $black_raw) = $ca->checkFilesByHash($sha_list);
        } catch (\Exception $e) {
            $this->str_error = $e->getMessage();
        }

        $dual = array_intersect($white_raw, $black_raw);

        foreach ($white_raw as $index) {
            $this->scanlist[$index_table[$index]]->setVerdict(RapidScanStorageRecord::WHITE);
        }

        foreach ($black_raw as $index) {
            $this->scanlist[$index_table[$index]]->setVerdict(RapidScanStorageRecord::BLACK);
            $this->scanlist[$index_table[$index]]->setSignature('BLACK'); //later on we will get sig info from cloud
            $blackfiles[$this->scanlist[$index_table[$index]]->getFilename()] = $sha_list[$index];
        }

        foreach ($dual as $index) {
            $this->scanlist[$index_table[$index]]->setVerdict(RapidScanStorageRecord::DUAL_USE);
            $this->scanlist[$index_table[$index]]->setSignature('DUAL'); //later on we will get sig info from cloud
        }

        // we can now update verdicts in batch for those that we know
        //add entries to report, when needed

        $this->vars->blackFiles = array_merge($this->vars->blackFiles, $blackfiles);

        unset($white_raw);
        unset($black_raw);
        unset($dual);
        unset($sha_list);
        unset($index_table);
    }

    /**
     * regex scan a single file, add entry to report if needed
     * @param $fileInfo
     * @param $i
     */
    private function _regexScan($fileInfo, $i, $vars)
    {
        $regex_res = $this->regexScan($fileInfo->getFilename(), $i, $vars);
        if (!is_array($regex_res)) {
            return;
        }
        list($result, $sigId, $snippet) = $regex_res;
        $fileInfo->setVerdict($result);
        if ($result !== RapidScanStorageRecord::RX_GOOD) {
            $fileInfo->setSignature($sigId);
            $fileInfo->setSnippet($snippet);
        }
    }

    /**
     * regex scan batch of files.
     */
    private function doRegexScan($vars)
    {
        foreach ($this->scanlist as $i => $fileinfo) {
            if (!in_array($fileinfo->getVerdict(), array(
                RapidScanStorageRecord::WHITE,
                RapidScanStorageRecord::BLACK,
                RapidScanStorageRecord::DUAL_USE
            ))
            ) {
                $this->_regexScan($fileinfo, $i, $vars);
            }
        }
    }

    private function processScanList($vars)
    {
        $this->doCloudScan();
        $this->doRegexScan($vars);
        $this->writeNew();
        $this->scanlist = array();
    }

    private function scanFile($filename, $rescan, $i, $vars)
    {
        global $g_Mnemo;

        if (!file_exists($filename)) {
            return false;
        }
        $file = RapidScanStorageRecord::fromFile($filename);

        $old_value = $this->db->getOld($file->getKey());
        $old_mtime = 0;
        if ($old_value) {
            $old_mtime = $old_value->getUpdatedTs();
            if ($file->getUpdatedTs() == $old_mtime) {
                $file = $old_value;
                $file->setFilename($filename);
            }
        }

        if ($file->getVerdict() == RapidScanStorageRecord::UNKNOWN
            || $file->getVerdict() == RapidScanStorageRecord::CONFLICT
            || $file->getUpdatedTs() !== $old_mtime
        ) {
            // these files has changed or we know nothing about them, lets re-calculate sha2
            // and do full scan
            $file->calcSha2();
            $file->setVerdict(RapidScanStorageRecord::NEWFILE);
            $this->scanlist[$i] = $file;
        } elseif ($file->getVerdict() == RapidScanStorageRecord::BLACK
            || $file->getVerdict() == RapidScanStorageRecord::DUAL_USE
        ) {
            //these files hasn't changed, but need to be reported as they are on one of the lists
            $this->vars->blackFiles[$filename] = bin2hex($file->getSha2());
            $this->db->put($file);
        } elseif (($rescan == self::RESCAN_SUSPICIOUS || $rescan == self::RESCAN_NONE)
            && $file->getVerdict() == RapidScanStorageRecord::RX_MALWARE
        ) {
            //this files were detected as rx malware before, let's report them

            $sigId = trim($file->getSignature(), "\0");

            if (isset($sigId) && isset($g_Mnemo[$sigId])) {
                $sigName = $g_Mnemo[$sigId];
                $snippet = $file->getSnippet();
                if (strpos($sigName, 'SUS') !== false && AI_EXTRA_WARN) {
                    $vars->warningPHP[] = $i;
                    $vars->warningPHPFragment[] = $snippet;
                    $vars->warningPHPSig[] = $sigId;
                } elseif (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) == 'js') {
                    $vars->criticalJS[] = $i;
                    $vars->criticalJSFragment[] = $snippet;
                    $vars->criticalJSSig[] = $sigId;
                } else {
                    $vars->criticalPHP[] = $i;
                    $vars->criticalPHPFragment[] = $snippet;
                    $vars->criticalPHPSig[] = $sigId;
                }
                AddResult($filename, $i, $vars);
                $this->db->put($file);
            } else {
                $this->scanlist[$i] = $file;
            }
        } elseif ((
                $rescan == self::RESCAN_ALL
                && in_array($file->getVerdict(), array(
                    RapidScanStorageRecord::RX_SUSPICIOUS,
                    RapidScanStorageRecord::RX_GOOD,
                    RapidScanStorageRecord::RX_MALWARE
                ))
            )
            || (
                $rescan == self::RESCAN_SUSPICIOUS
                && $file->getVerdict() == RapidScanStorageRecord::RX_SUSPICIOUS
            )
        ) {
            //rescan all mode, all none white/black/dual listed files need to be re-scanned fully

            $this->scanlist[$i] = $file;
        } else {
            //in theory -- we should have only white files here...
            $this->db->put($file);
        }

        if (count($this->scanlist) >= self::MAX_TO_SCAN) {
            // our scan list is big enough
            // let's flush db, and scan the list
            $this->db->flushBatch();
            $this->processScanList($vars);
        }

        if ($this->db->batch_count >= self::MAX_BATCH) {
            //we have added many entries to db, time to flush it
            $this->db->flushBatch();
            $this->processScanList($vars);
        }
    }

    public function scan($files, $vars, $rescan = self::RESCAN_SUSPICIOUS)
    {
        $i = 0;
        foreach ($files as $filepath) {
            $counter = $this->counter + $i;
            $vars->totalFiles++;
            $this->processedFiles = $counter - $vars->totalFolder - count($this->scanlist);
            printProgress($this->processedFiles, $filepath, $vars);
            $this->scanFile($filepath, $rescan, $counter, $vars);
            $i++;
        }

        //let's flush db again
        $this->db->flushBatch();

        //process whatever is left in our scan list
        if (count($this->scanlist) > 0) {
            $this->processScanList($vars);
        }

        // whitelist

        $snum = 0;
        $list = check_whitelist($vars->structure['crc'], $snum);
        $keys = array(
            'criticalPHP',
            'criticalJS',
            'g_Iframer',
            'g_Base64',
            'phishing',
            'adwareList',
            'g_Redirect',
            'warningPHP'
        );
        foreach ($keys as $p) {
            if (empty($vars->{$p})) {
                continue;
            }
            $p_Fragment = $p . 'Fragment';
            $p_Sig      = $p . 'Sig';
            if ($p == 'g_Redirect') {
                $p_Fragment = $p . 'PHPFragment';
            }
            if ($p == 'g_Phishing') {
                $p_Sig = $p . 'SigFragment';
            }

            $count = count($vars->{$p});
            for ($i = 0; $i < $count; $i++) {
                $id = $vars->{$p}[$i];
                if ($vars->structure['crc'][$id] !== 0 && in_array($vars->structure['crc'][$id], $list)) {
                    $rec = RapidScanStorageRecord::fromFile($vars->structure['n'][$id]);
                    $rec->calcSha2();
                    $rec->setVerdict(RapidScanStorageRecord::RX_GOOD);
                    $this->db->put($rec);
                    unset($vars->{$p}[$i]);
                    unset($vars->{$p_Sig}[$i]);
                    unset($vars->{$p_Fragment}[$i]);
                }
            }

            $vars->{$p}             = array_values($vars->{$p});
            $vars->{$p_Fragment}    = array_values($vars->{$p_Fragment});
            if (!empty($vars->{$p_Sig})) {
                $vars->{$p_Sig} = array_values($vars->{$p_Sig});
            }

            //close databases and rename new into 'current'
            $this->db->finish();
        }
    }
}

/**
 * DbFolderSpecification class file.
 */

/**
 * Class DbFolderSpecification.
 *
 * It can be use for checking requirements for a folder that is used for storing a RapidScan DB.
 */
class DbFolderSpecification
{
    /**
     * Check whether a particular folder satisfies requirements.
     *
     * @param string $folder
     * @return bool
     */
    public function satisfiedBy($folder)
    {
        if (!file_exists($folder) || !is_dir($folder)) {
            return false;
        }

        $owner_id = (int)fileowner($folder);
        if (function_exists('posix_getpwuid')) {
            $owner = posix_getpwuid($owner_id);
            if (!isset($owner['name']) || $owner['name'] !== 'root') {
                return false;
            }
        }
        elseif ($owner_id != 0) {
            return false;
        }

        $perms = fileperms($folder);
        if (($perms & 0x0100)                           // owner r
            && ($perms & 0x0080)                        // owner w
            && ($perms & 0x0040) && !($perms & 0x0800)  // owner x
            && !($perms & 0x0020)                       // group without r
            && !($perms & 0x0010)                       // group without w
            && (!($perms & 0x0008) || ($perms & 0x0400))// group without x
            && !($perms & 0x0004)                       // other without r
            && !($perms & 0x0002)                       // other without w
            && (!($perms & 0x0001) || ($perms & 0x0200))// other without x
        ) {
            return true;
        }
        return false;
    }
}
/**
 * CriticalFileSpecification class file.
 */

/**
 * Class CriticalFileSpecification.
 */
class CriticalFileSpecification
{
    /**
     * @var array list of extension
     */
    private static $extensions = array(
        'php',
        'htaccess',
        'cgi',
        'pl',
        'o',
        'so',
        'py',
        'sh',
        'phtml',
        'php3',
        'php4',
        'php5',
        'php6',
        'php7',
        'pht',
        'shtml',
        'susp',
        'suspected',
        'infected',
        'vir',
        'ico',
        'js',
        'json',
        'com',
        ''
    );

    /**
     * Check whether a particular file with specified path is critical.
     *
     * @param string $path
     * @return bool
     */
    public function satisfiedBy($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, self::$extensions);
    }
}
class Helpers
{
    public static function format($source)
    {
        $t_count = 0;
        $in_object = false;
        $in_at = false;
        $in_php = false;
        $in_for = false;
        $in_comp = false;
        $in_quote = false;
        $in_var = false;

        if (!defined('T_ML_COMMENT')) {
            define('T_ML_COMMENT', T_COMMENT);
        }

        $result = '';
        @$tokens = token_get_all($source);
        foreach ($tokens as $token) {
            if (is_string($token)) {
                $token = trim($token);
                if ($token == '{') {
                    if ($in_for) {
                        $in_for = false;
                    }
                    if (!$in_quote && !$in_var) {
                        $t_count++;
                        $result = rtrim($result) . ' ' . $token . "\n" . str_repeat('    ', $t_count);
                    } else {
                        $result = rtrim($result) . $token;
                    }
                } elseif ($token == '$') {
                    $in_var = true;
                    $result = $result . $token;
                } elseif ($token == '}') {
                    if (!$in_quote && !$in_var) {
                        $new_line = true;
                        $t_count--;
                        if ($t_count < 0) {
                            $t_count = 0;
                        }
                        $result = rtrim($result) . "\n" . str_repeat('    ', $t_count) .
                            $token . "\n" . @str_repeat('    ', $t_count);
                    } else {
                        $result = rtrim($result) . $token;
                    }
                    if ($in_var) {
                        $in_var = false;
                    }
                } elseif ($token == ';') {
                    if ($in_comp) {
                        $in_comp = false;
                    }
                    if ($in_for) {
                        $result .= $token . ' ';
                    } else {
                        $result .= $token . "\n" . str_repeat('    ', $t_count);
                    }
                } elseif ($token == ':') {
                    if ($in_comp) {
                        $result .= ' ' . $token . ' ';
                    } else {
                        $result .= $token . "\n" . str_repeat('    ', $t_count);
                    }
                } elseif ($token == '(') {
                    $result .= ' ' . $token;
                } elseif ($token == ')') {
                    $result .= $token;
                } elseif ($token == '@') {
                    $in_at = true;
                    $result .= $token;
                } elseif ($token == '.') {
                    $result .= ' ' . $token . ' ';
                } elseif ($token == '=') {
                    $result .= ' ' . $token . ' ';
                } elseif ($token == '?') {
                    $in_comp = true;
                    $result .= ' ' . $token . ' ';
                } elseif ($token == '"') {
                    if ($in_quote) {
                        $in_quote = false;
                    } else {
                        $in_quote = true;
                    }
                    $result .= $token;
                } else {
                    $result .= $token;
                }
            } else {
                list($id, $text) = $token;
                switch ($id) {
                    case T_OPEN_TAG:
                    case T_OPEN_TAG_WITH_ECHO:
                        $in_php = true;
                        $result .= trim($text) . "\n";
                        break;
                    case T_CLOSE_TAG:
                        $in_php = false;
                        $result .= trim($text);
                        break;
                    case T_FOR:
                        $in_for = true;
                        $result .= trim($text);
                        break;
                    case T_OBJECT_OPERATOR:
                        $result .= trim($text);
                        $in_object = true;
                        break;

                    case T_ENCAPSED_AND_WHITESPACE:
                    case T_WHITESPACE:
                        $result .= trim($text);
                        break;
                    case T_RETURN:
                        $result = rtrim($result) . "\n" . str_repeat('    ', $t_count) . trim($text) . ' ';
                        break;
                    case T_ELSE:
                    case T_ELSEIF:
                        $result = rtrim($result) . ' ' . trim($text) . ' ';
                        break;
                    case T_CASE:
                    case T_DEFAULT:
                        $result = rtrim($result) . "\n" . str_repeat('    ', $t_count - 1) . trim($text) . ' ';
                        break;
                    case T_FUNCTION:
                    case T_CLASS:
                        $result .= "\n" . str_repeat('    ', $t_count) . trim($text) . ' ';
                        break;
                    case T_AND_EQUAL:
                    case T_AS:
                    case T_BOOLEAN_AND:
                    case T_BOOLEAN_OR:
                    case T_CONCAT_EQUAL:
                    case T_DIV_EQUAL:
                    case T_DOUBLE_ARROW:
                    case T_IS_EQUAL:
                    case T_IS_GREATER_OR_EQUAL:
                    case T_IS_IDENTICAL:
                    case T_IS_NOT_EQUAL:
                    case T_IS_NOT_IDENTICAL:
                    case T_LOGICAL_AND:
                    case T_LOGICAL_OR:
                    case T_LOGICAL_XOR:
                    case T_MINUS_EQUAL:
                    case T_MOD_EQUAL:
                    case T_MUL_EQUAL:
                    case T_OR_EQUAL:
                    case T_PLUS_EQUAL:
                    case T_SL:
                    case T_SL_EQUAL:
                    case T_SR:
                    case T_SR_EQUAL:
                    case T_START_HEREDOC:
                    case T_XOR_EQUAL:
                        $result = rtrim($result) . ' ' . trim($text) . ' ';
                        break;
                    case T_COMMENT:
                        $result = rtrim($result) . "\n" . str_repeat('    ', $t_count) . trim($text) . ' ';
                        break;
                    case T_ML_COMMENT:
                        $result = rtrim($result) . "\n";
                        $lines = explode("\n", $text);
                        foreach ($lines as $line) {
                            $result .= str_repeat('    ', $t_count) . trim($line);
                        }
                        $result .= "\n";
                        break;
                    case T_INLINE_HTML:
                        $result .= $text;
                        break;
                    default:
                        $result .= trim($text);
                        break;
                }
            }
        }
        return $result;
    }

    public static function replaceCreateFunction($str)
    {
        $hangs = 20;
        while (strpos($str, 'create_function') !== false && $hangs--) {
            $start_pos = strpos($str, 'create_function');
            $end_pos = 0;
            $brackets = 0;
            $started = false;
            $opened = 0;
            $closed = 0;
            for ($i = $start_pos; $i < strlen($str); $i++) {
                if ($str[$i] == '(') {
                    $started = true;
                    $brackets++;
                    $opened++;
                } else if ($str[$i] == ')') {
                    $closed++;
                    $brackets--;
                }
                if ($brackets == 0 && $started) {
                    $end_pos = $i + 1;
                    break;
                }
            }

            $cr_func = substr($str, $start_pos, $end_pos - $start_pos);
            $func = implode('function(', explode('create_function(\'', $cr_func, 2));
            //$func = substr_replace('create_function(\'', 'function(', $cr_func);
            //$func = str_replace('\',\'', ') {', $func);
            $func = implode(') {', explode('\',\'', $func, 2));
            $func = substr($func, 0, -2) . '}';
            $str = str_replace($cr_func, $func, $str);
        }
        return $str;
    }

    public static function calc($expr)
    {
        if (is_array($expr)) {
            $expr = $expr[0];
        }
        preg_match('~(chr|min|max|round)?\(([^\)]+)\)~msi', $expr, $expr_arr);
        if (@$expr_arr[1] == 'min' || @$expr_arr[1] == 'max') {
            return $expr_arr[1](explode(',', $expr_arr[2]));
        } elseif (@$expr_arr[1] == 'chr') {
            if ($expr_arr[2][0] === '(') {
                $expr_arr[2] = substr($expr_arr[2], 1);
            }
            $expr_arr[2] = self::calc($expr_arr[2]);
            return $expr_arr[1](intval($expr_arr[2]));
        } elseif (@$expr_arr[1] == 'round') {
            $expr_arr[2] = self::calc($expr_arr[2]);
            return $expr_arr[1]($expr_arr[2]);
        } else {
            preg_match_all('~([\d\.a-fx]+)([\*\/\-\+\^\|\&])?~', $expr, $expr_arr);
            foreach ($expr_arr[1] as &$expr_arg) {
                if (strpos($expr_arg, "0x")!==false) {
                    $expr = str_replace($expr_arg, hexdec($expr_arg), $expr);
                    $expr_arg = hexdec($expr_arg);
                }
            }
            if (in_array('*', $expr_arr[2]) !== false) {
                $pos = array_search('*', $expr_arr[2]);
                $res = $expr_arr[1][$pos] * $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '*' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '*' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('/', $expr_arr[2]) !== false) {
                $pos = array_search('/', $expr_arr[2]);
                $res = $expr_arr[1][$pos] / $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '/' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '/' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('-', $expr_arr[2]) !== false) {
                $pos = array_search('-', $expr_arr[2]);
                $res = $expr_arr[1][$pos] - $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '-' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '-' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('+', $expr_arr[2]) !== false) {
                $pos = array_search('+', $expr_arr[2]);
                $res = $expr_arr[1][$pos] + $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '+' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '+' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('^', $expr_arr[2]) !== false) {
                $pos = array_search('^', $expr_arr[2]);
                $res = $expr_arr[1][$pos] ^ $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '^' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '^' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('|', $expr_arr[2]) !== false) {
                $pos = array_search('|', $expr_arr[2]);
                $res = $expr_arr[1][$pos] | $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '|' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '|' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('&', $expr_arr[2]) !== false) {
                $pos = array_search('&', $expr_arr[2]);
                $res = $expr_arr[1][$pos] & $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '&' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '&' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } else {
                return $expr;
            }

            return $expr;
        }
    }

    public static function getEvalCode($string)
    {
        preg_match("/eval\(([^\)]+)\)/msi", $string, $matches);
        return (empty($matches)) ? '' : end($matches);
    }

    public static function getTextInsideQuotes($string)
    {
        if (preg_match_all('/("(.*)")/msi', $string, $matches)) {
            return @end(end($matches));
        } elseif (preg_match_all('/\((\'(.*)\')/msi', $string, $matches)) {
            return @end(end($matches));
        } else {
            return '';
        }
    }

    public static function getNeedles($string)
    {
        preg_match_all("/'(.*?)'/msi", $string, $matches);

        return (empty($matches)) ? array() : $matches[1];
    }

    public static function getHexValues($string)
    {
        preg_match_all('/0x[a-fA-F0-9]{1,8}/msi', $string, $matches);
        return (empty($matches)) ? array() : $matches[0];
    }

    public static function formatPHP($string)
    {
        $string = str_replace('<?php', '', $string);
        $string = str_replace('?>', '', $string);
        $string = str_replace(PHP_EOL, "", $string);
        $string = str_replace(";", ";\n", $string);
        $string = str_replace("}", "}\n", $string);
        return $string;
    }

    public static function fnEscapedHexToHex($escaped)
    {
        return chr(hexdec($escaped[1]));
    }

    public static function fnEscapedOctDec($escaped)
    {
        return chr(octdec($escaped[1]));
    }

    public static function fnEscapedDec($escaped)
    {
        return chr($escaped[1]);
    }

    //from sample_16
    public static function someDecoder($str)
    {
        $str = base64_decode($str);
        $TC9A16C47DA8EEE87 = 0;
        $TA7FB8B0A1C0E2E9E = 0;
        $T17D35BB9DF7A47E4 = 0;
        $T65CE9F6823D588A7 = (ord($str[1]) << 8) + ord($str[2]);
        $i = 3;
        $T77605D5F26DD5248 = 0;
        $block = 16;
        $T7C7E72B89B83E235 = "";
        $T43D5686285035C13 = "";
        $len = strlen($str);

        $T6BBC58A3B5B11DC4 = 0;

        for (; $i < $len;) {
            if ($block == 0) {
                $T65CE9F6823D588A7 = (ord($str[$i++]) << 8);
                $T65CE9F6823D588A7 += ord($str[$i++]);
                $block = 16;
            }
            if ($T65CE9F6823D588A7 & 0x8000) {
                $TC9A16C47DA8EEE87 = (ord($str[$i++]) << 4);
                $TC9A16C47DA8EEE87 += (ord($str[$i]) >> 4);
                if ($TC9A16C47DA8EEE87) {
                    $TA7FB8B0A1C0E2E9E = (ord($str[$i++]) & 0x0F) + 3;
                    for ($T17D35BB9DF7A47E4 = 0; $T17D35BB9DF7A47E4 < $TA7FB8B0A1C0E2E9E; $T17D35BB9DF7A47E4++) {
                        $T7C7E72B89B83E235[$T77605D5F26DD5248 + $T17D35BB9DF7A47E4] =
                            $T7C7E72B89B83E235[$T77605D5F26DD5248 - $TC9A16C47DA8EEE87 + $T17D35BB9DF7A47E4];
                    }
                    $T77605D5F26DD5248 += $TA7FB8B0A1C0E2E9E;
                } else {
                    $TA7FB8B0A1C0E2E9E = (ord($str[$i++]) << 8);
                    $TA7FB8B0A1C0E2E9E += ord($str[$i++]) + 16;
                    for ($T17D35BB9DF7A47E4 = 0; $T17D35BB9DF7A47E4 < $TA7FB8B0A1C0E2E9E;
                         $T7C7E72B89B83E235[$T77605D5F26DD5248 + $T17D35BB9DF7A47E4++] = $str[$i]) {
                    }
                    $i++;
                    $T77605D5F26DD5248 += $TA7FB8B0A1C0E2E9E;
                }
            } else {
                $T7C7E72B89B83E235[$T77605D5F26DD5248++] = $str[$i++];
            }
            $T65CE9F6823D588A7 <<= 1;
            $block--;
            if ($i == $len) {
                $T43D5686285035C13 = $T7C7E72B89B83E235;
                if (is_array($T43D5686285035C13)) {
                    $T43D5686285035C13 = implode($T43D5686285035C13);
                }
                $T43D5686285035C13 = "?" . ">" . $T43D5686285035C13;
                return $T43D5686285035C13;
            }
        }
    }
    //

    public static function someDecoder2($WWAcmoxRAZq, $sBtUiFZaz)   //sample_05
    {
        $JYekrRTYM = str_rot13(gzinflate(str_rot13(base64_decode('y8svKCwqLiktK6+orFdZV0FWWljPyMzKzsmNNzQyNjE1M7ewNAAA'))));
        if ($WWAcmoxRAZq == 'asedferg456789034689gd') {
            $cEerbvwKPI = $JYekrRTYM[18] . $JYekrRTYM[19] . $JYekrRTYM[17] . $JYekrRTYM[17] . $JYekrRTYM[4] . $JYekrRTYM[21];
            return $cEerbvwKPI($sBtUiFZaz);
        } elseif ($WWAcmoxRAZq == 'zfcxdrtgyu678954ftyuip') {
            $JWTDeUKphI = $JYekrRTYM[1] . $JYekrRTYM[0] . $JYekrRTYM[18] . $JYekrRTYM[4] . $JYekrRTYM[32] .
                $JYekrRTYM[30] . $JYekrRTYM[26] . $JYekrRTYM[3] . $JYekrRTYM[4] . $JYekrRTYM[2] . $JYekrRTYM[14] .
                $JYekrRTYM[3] . $JYekrRTYM[4];
            return $JWTDeUKphI($sBtUiFZaz);
        } elseif ($WWAcmoxRAZq == 'gyurt456cdfewqzswexcd7890df') {
            $rezmMBMev = $JYekrRTYM[6] . $JYekrRTYM[25] . $JYekrRTYM[8] . $JYekrRTYM[13] . $JYekrRTYM[5] . $JYekrRTYM[11] . $JYekrRTYM[0] . $JYekrRTYM[19] . $JYekrRTYM[4];
            return $rezmMBMev($sBtUiFZaz);
        } elseif ($WWAcmoxRAZq == 'zcdfer45dferrttuihvs4321890mj') {
            $WbbQXOQbH = $JYekrRTYM[18] . $JYekrRTYM[19] . $JYekrRTYM[17] . $JYekrRTYM[26] . $JYekrRTYM[17] . $JYekrRTYM[14] . $JYekrRTYM[19] . $JYekrRTYM[27] . $JYekrRTYM[29];
            return $WbbQXOQbH($sBtUiFZaz);
        } elseif ($WWAcmoxRAZq == 'zsedrtre4565fbghgrtyrssdxv456') {
            $jPnPLPZcMHgH = $JYekrRTYM[2] . $JYekrRTYM[14] . $JYekrRTYM[13] . $JYekrRTYM[21] . $JYekrRTYM[4] . $JYekrRTYM[17] . $JYekrRTYM[19] . $JYekrRTYM[26] . $JYekrRTYM[20] . $JYekrRTYM[20] . $JYekrRTYM[3] . $JYekrRTYM[4] . $JYekrRTYM[2] . $JYekrRTYM[14] . $JYekrRTYM[3] . $JYekrRTYM[4];
            return $jPnPLPZcMHgH($sBtUiFZaz);
        }
    }

    function PHPJiaMi_decoder($str, $md5, $rand, $lower_range = '')
    {
        $md5_xor = md5($md5);
        $lower_range = !$lower_range ? ord($rand) : $lower_range;
        $layer1 = '';
        for ($i=0; $i < strlen($str); $i++) {
            $layer1 .= ord($str[$i]) < 245 ? ((ord($str[$i]) > $lower_range && ord($str[$i]) < 245) ? chr(ord($str[$i]) / 2) : $str[$i]) : '';
        }
        $layer1 = base64_decode($layer1);
        $result = '';
        $j = $len_md5_xor = strlen($md5_xor);
        for ($i=0; $i < strlen($layer1); $i++) {
            $j = $j ? $j : $len_md5_xor;
            $j--;
            $result .= $layer1[$i] ^ $md5_xor[$j];
        }
        return $result;
    }

    public static function stripsquoteslashes($str)
    {
        $res = '';
        for ($i = 0; $i < strlen($str); $i++) {
            if (isset($str[$i+1]) && ($str[$i] == '\\' && ($str[$i+1] == '\\' || $str[$i+1] == '\''))) {
                continue;
            } else {
                $res .= $str[$i];
            }
        }
        return $res;
    }

    ///////////////////////////////////////////////////////////////////////////
}




///////////////////////////////////////////////////////////////////////////

function parseArgs($argv){
    array_shift($argv); $o = array();
    foreach ($argv as $a){
        if (substr($a,0,2) == '--'){ $eq = strpos($a,'=');
            if ($eq !== false){ $o[substr($a,2,$eq-2)] = substr($a,$eq+1); }
            else { $k = substr($a,2); if (!isset($o[$k])){ $o[$k] = true; } } }
        else if (substr($a,0,1) == '-'){
            if (substr($a,2,1) == '='){ $o[substr($a,1,1)] = substr($a,3); }
            else { foreach (str_split(substr($a,1)) as $k){ if (!isset($o[$k])){ $o[$k] = true; } } } }
        else { $o[] = $a; } }
    return $o;
}


////////////////////////////////////////////////////////////////////////////////////////////////////////
// cli handler
if (!defined('AIBOLIT_START_TIME') && !defined('PROCU_CLEAN_DB') && @strpos(__FILE__, @$argv[0])!==false) {
    //echo "\n" . $argv[1] . "\n";

    set_time_limit(0);
    ini_set('max_execution_time', '900000');
    ini_set('realpath_cache_size', '16M');
    ini_set('realpath_cache_ttl', '1200');
    ini_set('pcre.backtrack_limit', '1000000');
    ini_set('pcre.recursion_limit', '12500');
    ini_set('pcre.jit', '1');
    $options = parseArgs($argv);
    $str = php_strip_whitespace($options[0]);
    $str2 = file_get_contents($options[0]);
    $d = new Deobfuscator($str, $str2);
    $start = microtime(true);
    $hangs = 0;
    while ($d->getObfuscateType($str)!=='' && $hangs < 15) {
        $str = $d->deobfuscate();
        $d = new Deobfuscator($str);
        $hangs++;
    }
    $code = $str;
    if (isset($options['prettyprint'])) {
        $code = Helpers::format($code);
    }
    echo $code;
    echo "\n";
    //echo 'Execution time: ' . round(microtime(true) - $start, 4) . ' sec.';
}

class Deobfuscator
{
    private $signatures = array(
        array(
            'full' => '~for\((\$\w{1,40})=\d+,(\$\w+)=\'([^\$]+)\',(\$\w+)=\'\';@?ord\(\2\[\1\]\);\1\+\+\)\{if\(\1<\d+\)\{(\$\w+)\[\2\[\1\]\]=\1;\}else\{\$\w+\.\=@?chr\(\(\5\[\2\[\1\]\]<<\d+\)\+\(\5\[\2\[\+\+\1\]\]\)\);\}\}\s*.{0,500}eval\(\4\);(if\(isset\(\$_(GET|REQUEST|POST|COOKIE)\[[\'"][^\'"]+[\'"]\]\)\)\{[^}]+;\})?~msi',
            'fast' => '~for\((\$\w{1,40})=\d+,(\$\w+)=\'([^\$]+)\',(\$\w+)=\'\';@?ord\(\2\[\1\]\);\1\+\+\)\{if\(\1<\d+\)\{(\$\w+)\[\2\[\1\]\]=\1;\}else\{\$\w+\.\=@?chr\(\(\5\[\2\[\1\]\]<<\d+\)\+\(\5\[\2\[\+\+\1\]\]\)\);\}\}\s*.{0,500}eval\(\4\);(if\(isset\(\$_(GET|REQUEST|POST|COOKIE)\[[\'"][^\'"]+[\'"]\]\)\)\{[^}]+;\})?~msi',
            'id' => 'parenthesesString'),

        array(
            'full' =>'~(\$\w+)\s*=\s*basename\s*\(trim\s*\(preg_replace\s*\(rawurldecode\s*\([\'"][%0-9a-f\.]+["\']\),\s*\'\',\s*__FILE__\)\)\);\s*(\$\w+)\s*=\s*["\']([^\'"]+)["\'];\s*eval\s*\(rawurldecode\s*\(\2\)\s*\^\s*substr\s*\(str_repeat\s*\(\1,\s*\(strlen\s*\(\2\)/strlen\s*\(\1\)\)\s*\+\s*1\),\s*0,\s*strlen\s*\(\2\)\)\);~msi',
            'fast' => '~(\$\w+)\s*=\s*basename\s*\(trim\s*\(preg_replace\s*\(rawurldecode\s*\([\'"][%0-9a-f\.]+["\']\),\s*\'\',\s*__FILE__\)\)\);\s*(\$\w+)\s*=\s*["\']([^\'"]+)["\'];\s*eval\s*\(rawurldecode\s*\(\2\)\s*\^\s*substr\s*\(str_repeat\s*\(\1,\s*\(strlen\s*\(\2\)/strlen\s*\(\1\)\)\s*\+\s*1\),\s*0,\s*strlen\s*\(\2\)\)\);~msi',
            'id' => 'xorFName'),

        array(
            'full' =>
                '~(\$\w{1,40})=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'([^\']+)\'\);eval\(\1\(gzuncompress\(\2\(\3\)\)\)\);~msi',
            'fast' => '~(\$\w{1,40})=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'([^\']+)\'\);eval\(\1\(gzuncompress\(\2\(\3\)\)\)\);~msi',
            'id' => 'phpMess'),

        array(
            'full' =>
                '~(\$\w{1,40})\s*=\s*\"([^\"]+)\";\s*\$\w+\s*=\s*\$\w+\(\1,\"[^\"]+\",\"[^\"]+\"\);\s*\$\w+\(\"[^\"]+\",\"[^\"]+\",\"\.\"\);~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\"([^\"]+)\";\s*\$\w+\s*=\s*\$\w+\(\1,\"[^\"]+\",\"[^\"]+\"\);\s*\$\w+\(\"[^\"]+\",\"[^\"]+\",\"\.\"\);~msi',
            'id' => 'pregReplaceSample05'),


        array(
            'full' => '~(\$\w{1,40})\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\"([^\"]+)\";\s*(\$\w+)\s*=\s*.+?;\s*\2\(\5,\"[^\']+\'\3\'[^\"]+\",\"\.\"\);~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\"([^\"]+)\";\s*(\$\w+)\s*=\s*.+?;\s*\2\(\5,\"[^\']+\'\3\'[^\"]+\",\"\.\"\);~msi',
            'id' => 'pregReplaceB64'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*\'([^\']+)\';\s*\1\s*=\s*gzinflate\s*\(base64_decode\s*\(\1\)\);\s*\1\s*=\s*str_replace\s*\(\"__FILE__\",\"\'\$\w+\'\",\1\);\s*eval\s*\(\1\);~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\'([^\']+)\';\s*\1\s*=\s*gzinflate\s*\(base64_decode\s*\(\1\)\);\s*\1\s*=\s*str_replace\s*\(\"__FILE__\",\"\'\$\w+\'\",\1\);\s*eval\s*\(\1\);~msi',
            'id' => 'GBE'),

        array(
            'full' => '~(\$GLOBALS\[\s*[\'"]_+\w{1,60}[\'"]\s*\])\s*=\s*\s*array\s*\(\s*base64_decode\s*\(.+?((.+?\1\[\d+\]).+?)+[^;]+;(\s*include\(\$_\d+\);)?}?((.+?___\d+\(\d+\))+[^;]+;)?~msi',
            'fast' => '~\$GLOBALS\[\s*[\'"]_+\w{1,60}[\'"]\s*\]\s*=\s*\s*array\s*\(\s*base64_decode\s*\(~msi',
            'id' => 'Bitrix'),

        array(
            'full' => '~\$\w{1,40}\s*=\s*(__FILE__|__LINE__);\s*\$\w{1,40}\s*=\s*(\d+);\s*eval(\s*\()+\$?\w+\s*\([\'"][^\'"]+[\'"](\s*\))+;\s*return\s*;\s*\?>(.+)~msi',
            'fast' => '~\$\w{1,40}\s*=\s*(__FILE__|__LINE__);\s*\$\w{1,40}\s*=\s*(\d+);\s*eval(\s*\()+\$?\w+\s*\([\'"][^\'"]+[\'"](\s*\))+;\s*return\s*;\s*\?>(.+)~msi',
            'id' => 'B64inHTML'),

        array(
            'full' => '~\$[O0]*=urldecode\(\'[%a-f0-9]+\'\);(\$(GLOBALS\[\')?[O0]*(\'\])?=(\d+);)?\s*(\$(GLOBALS\[\')?[O0]*(\'\])?\.?=(\$(GLOBALS\[\')?[O0]*(\'\])?([\{\[]\d+[\}\]])?\.?)+;)+[^\?]+\?\>[\s\w\~\=\/\+\\\\\^\{]+~msi',
            'fast' => '~\$[O0]*=urldecode\(\'[%a-f0-9]+\'\);(?:\$(GLOBALS\[\')?[O0]*(?:\'\])?=\d+;)?\s*(?:\$(?:GLOBALS\[\')?[O0]*(?:\'\])?\.?=(?:\$(?:GLOBALS\[\')?[O0]*(?:\'\])?(?:[\{\[]\d+[\}\]])?\.?)+;)+[^\?]+\?\>[\s\w\~\=\/\+\\\\\^\{]+~msi',
            'id' => 'LockIt'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*\"(\\\\142|\\\\x62)[0-9a-fx\\\\]+";\s*@?eval\s*\(\1\s*\([^\)]+\)+\s*;~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\"(\\\\142|\\\\x62)[0-9a-fx\\\\]+";\s*@?eval\s*\(\1\s*\(~msi',
            'id' => 'FOPO'),

        array(
            'full' => '~\$_F=__FILE__;\$_X=\'([^\']+\');eval\([^\)]+\)+;~msi',
            'fast' => '~\$_F=__FILE__;\$_X=\'([^\']+\');eval\(~ms',
            'id' => 'ByteRun'),

        array(
            'full' => '~(\$\w{1,40}=\'[^\']+\';\s*)+(\$[\w{1,40}]+)=(urldecode|base64_decode){0,1}\(?[\'"]([\w+%=-]+)[\'"]\)?;(\$[\w+]+=(\$(\w+\[\')?[O_0]*(\'\])?([\{\[]\d+[\}\]])?\.?)+;)+[^\?]+(\?\>[\w\~\=\/\+]+|.+\\\\x[^;]+;)~msi',
            'fast' => '~(\$\w{1,40}=\'[^\']+\';\s*)+(\$[\w{1,40}]+)=(urldecode|base64_decode){0,1}\(?[\'"]([\w+%=-]+)[\'"]\)?;(\$[\w+]+=(\$(\w+\[\')?[O_0]*(\'\])?([\{\[]\d+[\}\]])?\.?)+;)+[^\?]+(\?\>[\w\~\=\/\+]+|.+\\\\x[^;]+;)~msi',
            'id' => 'Urldecode'),

        array(
            'full' => '~(\$[\w{1,40}]+)=urldecode\(?[\'"]([\w+%=-]+)[\'"]\);(\s*\$\w+\.?=(\$\w+\{\d+\}\s*[\.;]?\s*)+)+((\$\w+=["\']([^\'"]+)[\'"];\s*eval\(\'\?>\'\.[\$\w\(\)\*\d,\s]+);|(eval\(\$\w+\([\'"]([^\'"]+)[\'"]\)+;))~msi',
            'fast' => '~(\$[\w{1,40}]+)=urldecode\(?[\'"]([\w+%=-]+)[\'"]\);(\s*\$\w+\.?=(\$\w+\{\d+\}\s*[\.;]?\s*)+)+((\$\w+=["\']([^\'"]+)[\'"];\s*eval\(\'\?>\'\.[\$\w\(\)\*\d,\s]+);|(eval\(\$\w+\([\'"]([^\'"]+)[\'"]\)+;))~msi',
            'id'   => 'UrlDecode2',
        ),

        array(
            'full' => '~explode\(\"\*\*\*\",\s*\$\w+\);\s*eval\(eval\(\"return strrev\(base64_decode\([^\)]+\)+;~msi',
            'fast' => '~explode\(\"\*\*\*\",\s*\$\w+\);\s*eval\(eval\(\"return strrev\(base64_decode\(~msi',
            'id' => 'cobra'),

        array(
            'full' => '~\$[O0]+=\(base64_decode\(strtr\(fread\(\$[O0]+,(\d+)\),\'([^\']+)\',\'([^\']+)\'\)\)\);eval\([^\)]+\)+;~msi',
            'fast' => '~\$[O0]+=\(base64_decode\(strtr\(fread\(\$[O0]+,(\d+)\),\'([^\']+)\',\'([^\']+)\'\)\)\);eval\(~msi',
            'id' => 'strtrFread'),

        array(
            'full' => '~if\s*\(\!extension_loaded\(\'IonCube_loader\'\)\).+pack\(\"H\*\",\s*\$__ln\(\"/\[A-Z,\\\\r,\\\\n\]/\",\s*\"\",\s*substr\(\$__lp,\s*([0-9a-fx]+\-[0-9a-fx]+)\)\)\)[^\?]+\?\>\s*[0-9a-z\r\n]+~msi',
            'fast' => '~IonCube_loader~ms',
            'id' => 'FakeIonCube'),

        array(
            'full' => '~(\$\w{1,40})="([\w\]\[\<\&\*\_+=/]{300,})";\$\w+=\$\w+\(\1,"([\w\]\[\<\&\*\_+=/]+)","([\w\]\[\<\&\*\_+=/]+)"\);~msi',
            'fast' => '~(\$\w{1,40})="([\w\]\[\<\&\*\_+=/]{300,})";\$\w+=\$\w+\(\1,"([\w\]\[\<\&\*\_+=/]+)","([\w\]\[\<\&\*\_+=/]+)"\);~msi',
            'id' => 'strtrBase64'),

        array(
            'full' => '~\$\w+\s*=\s*array\((\'[^\']+\',?)+\);\s*.+?(\$_\w{1,40}\[\w+\])\s*=\s*explode\(\'([^\']+)\',\s*\'([^\']+)\'\);.+?(\2\[[a-fx\d]+\])\(\);(.+?\2)+.+}~msi',
            'fast' => '~(\$_\w{1,40}\[\w+\])\s*=\s*explode\(\'([^\']+)\',\s*\'([^\']+)\'\);.+?(\1\[[a-fx\d]+\])\(\);~msi',
            'id' => 'explodeSubst'),

        array(
            'full' => '~(\$[\w{1,40}]+)\s*=\s*\'([\w+%=\-\#\\\\\'\*]+)\';(\$[\w+]+)\s*=\s*Array\(\);(\3\[\]\s*=\s*(\1\[\d+\]\.?)+;+)+(.+\3)[^}]+}~msi',
            'fast' => '~(\$[\w{1,40}]+)\s*=\s*\'([\w+%=\-\#\\\\\'\*]+)\';(\$[\w+]+)\s*=\s*Array\(\);(\3\[\]\s*=\s*(\1\[\d+\]\.?)+;+)+~msi',
            'id' => 'subst'),

        array(
            'full' => '~if\(!function_exists\(\"(\w+)\"\)\){function \1\(.+?eval\(\1\(\"[^\"]+\"\)\);~msi',
            'fast' => '~if\(!function_exists\(\"(\w+)\"\)\){function \1\(.+?eval\(\1\(\"[^\"]+\"\)\);~msi',
            'id' => 'decoder'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*\"riny\(\"\.(\$\w+)\(\"base64_decode\"\);\s*(\$\w+)\s*=\s*\2\(\1\.\'\("([^"]+)"\)\);\'\);\s*\$\w+\(\3\);~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\"riny\(\"\.(\$\w+)\(\"base64_decode\"\);\s*(\$\w+)\s*=\s*\2\(\1\.\'\("([^"]+)"\)\);\'\);\s*\$\w+\(\3\);~msi',
            'id' => 'GBZ'),

        array(
            'full' => '~\$\w+\s*=\s*\d+;\s*\$GLOBALS\[\'[^\']+\'\]\s*=\s*Array\(\);\s*global\s*\$\w+;(\$\w{1,40})\s*=\s*\$GLOBALS;\$\{"\\\\x[a-z0-9\\\\]+"\}\[(\'\w+\')\]\s*=\s*\"(([^\"\\\\]|\\\\.)*)\";\1\[(\1\[\2\]\[\d+\].?).+?exit\(\);\}+~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\$GLOBALS;\$\{"\\\\x[a-z0-9\\\\]+"\}\[(\'\w+\')\]\s*=\s*\"(([^\"\\\\]|\\\\.)*)\";\1\[(\1\[\2\]\[\d+\].?)~msi',
            'id' => 'globalsArray'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*\'(\\\\.|[^\']){0,100}\';\s*\$\w+\s*=\s*\'(\\\\.|[^\']){0,100}\'\^\1;[^)]+\)+;\s*\$\w+\(\);~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\'(\\\\.|[^\']){0,100}\';\s*\$\w+\s*=\s*\'(\\\\.|[^\']){0,100}\'\^\1;~msi',
            'id' => 'xoredVar'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*\'([^\']*)\';\s*(\$\w{1,40})\s*=\s*explode\s*\((chr\s*\(\s*\(\d+\-\d+\)\)),substr\s*\(\1,\s*\((\d+\-\d+)\),\s*\(\s*(\d+\-\d+)\)\)\);\s*(\$\w{1,40})\s*=\s*\3\[\d+\]\s*\(\3\[\s*\(\d+\-\d+\)\]\);\s*(\$\w{1,40})\s*=\s*\3\[\d+\]\s*\(\3\[\s*\(\d+\-\d+\)\]\);\s*if\s*\(!function_exists\s*\(\'([^\']*)\'\)\)\s*\{\s*function\s*\9\s*\(.+\1\s*=\s*\$\w+[+\-\*]\d+;~msi',
            'fast' => '~(\$\w{1,40})\s=\s\'([^\']*)\';\s(\$\w{1,40})=explode\((chr\(\(\d+\-\d+\)\)),substr\(\1,\((\d+\-\d+)\),\((\d+\-\d+)\)\)\);\s(\$\w{1,40})\s=\s\3\[\d+\]\(\3\[\(\d+\-\d+\)\]\);\s(\$\w{1,40})\s=\s\3\[\d+\]\(\3\[\(\d+\-\d+\)\]\);\sif\s\(!function_exists\(\'([^\']*)\'\)\)\s\{\sfunction\s*\9\(~msi',
            'id' => 'arrayOffsets'),

        array(
            'full' => '~(\$\w{1,50}\s*=\s*array\((\'\d+\',?)+\);)+\$\w{1,40}=\"([^\"]+)\";if\s*\(!function_exists\(\"\w{1,50}\"\)\)\s*\{\s*function\s*[^\}]+\}\s*return\s*\$\w+;\}[^}]+}~msi',
            'fast' => '~(\$\w{1,50}=\s*array\((\'\d+\',?)+\);)+\$\w{1,40}=\"[^\"]+\";if\s*\(!function_exists\(\"\w{1,50}\"\)\)\{\s*function ~msi',
            'id' => 'obfB64'),

        array(
            'full' => '~if\(\!function_exists\(\'findsysfolder\'\)\){function findsysfolder\(\$fld\).+\$REXISTHEDOG4FBI=\'([^\']+)\';\$\w+=\'[^\']+\';\s*eval\(\w+\(\'([^\']+)\',\$REXISTHEDOG4FBI\)\);~msi',
            'fast' => '~if\(!function_exists\(\'findsysfolder\'\)\){function findsysfolder\(\$fld\)\{\$fld1=dirname\(\$fld\);\$fld=\$fld1\.\'/scopbin\';clearstatcache\(\);if\(!is_dir\(\$fld\)\)return findsysfolder\(\$fld1\);else return \$fld;\}\}require_once\(findsysfolder\(__FILE__\)\.\'/911006\.php\'\);~msi',
            'id' => 'sourceCop'),

        array(
            'full' => '~function\s*(\w{1,40})\s*\(\s*(\$\w{1,40})\s*,\s*(\$\w{1,40})\s*\)\s*\{\s*(\$\w{1,40})\s*=\s*str_rot13\s*\(\s*gzinflate\s*\(\s*str_rot13\s*\(\s*base64_decode\s*\(\s*[\'"][^\'"]*[\'"]\s*\)\s*\)\s*\)\s*\)\s*;\s*(if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*(\$\w{1,40})\s*=(\$\w+[\{\[]\d+[\}\]]\.?)+;return\s*(\$\w+)\(\3\);\s*\}\s*else\s*)+\s*if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*return\s*eval\(\3\);\s*\}\s*\};\s*(\$\w{1,40})\s*=\s*[\'"][^\'"]*[\'"];(\s*\9\([\'"][^\'"]*[\'"],)+\s*[\'"][^\'"]*[\'"]\s*\)+;~msi',
            'fast' => '~function\s*(\w{1,40})\s*\(\s*(\$\w{1,40})\s*,\s*(\$\w{1,40})\s*\)\s*\{\s*(\$\w{1,40})\s*=\s*str_rot13\s*\(\s*gzinflate\s*\(\s*str_rot13\s*\(\s*base64_decode\s*\(\s*[\'"][^\'"]*[\'"]\s*\)\s*\)\s*\)\s*\)\s*;\s*(if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*(\$\w{1,40})\s*=(\$\w+[\{\[]\d+[\}\]]\.?)+;return\s*(\$\w+)\(\3\);\s*\}\s*else\s*)+\s*if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*return\s*eval\(\3\);\s*\}\s*\};\s*(\$\w{1,40})\s*=\s*[\'"][^\'"]*[\'"];(\s*\9\([\'"][^\'"]*[\'"],)+\s*[\'"][^\'"]*[\'"]\s*\)+;~msi',
            'id' => 'webshellObf',

        ),

        array(
            'full' => '~(\$\w{1,40})=\'([^\'\\\\]|.*?)\';\s*((\$\w{1,40})=(\1\[\d+].?)+;\s*)+(\$\w{1,40})=\'\';\s*(\$\w{1,40})\(\6,\$\w{1,40}\.\"([^\"]+)\"\.\$\w{1,40}\.\4\);~msi',
            'fast' => '~(\$\w{1,40})=\'([^\\\\\']|.*?)\';\s*((\$\w{1,40})=(\1\[\d+].?)+;\s*)+(\$\w{1,40})=\'\';~msi',
            'id' => 'substCreateFunc'
        ),

        array(
            'full' => '~(\$\w+)=[create_function".]+;\s*\1=\1\(\'(\$\w+)\',[\'.eval\("\?>".gzinflate\(base64_decode]+\2\)+;\'\);\s*\1\(\'([^\']+)\'\);~msi',
            'fast' => '~(\$\w+)=[create_function".]+;\s*\1=\1\(\'(\$\w+)\',[\'.eval\("\?>".gzinflate\(base64_decode]+\2\)+;\'\);\s*\1\(\'([^\']+)\'\);~msi',
            'id' => 'createFunc'
        ),

        array(
            'full' => '~(?(DEFINE)(?\'foreach\'(?:/\*\w+\*/)?\s*foreach\(\[[\d,]+\]\s*as\s*\$\w+\)\s*\{\s*\$\w+\s*\.=\s*\$\w+\[\$\w+\];\s*\}\s*(?:/\*\w+\*/)?\s*))(\$\w+)\s*=\s*"([^"]+)";\s*\$\w+\s*=\s*"";(?P>foreach)if\(isset\(\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\]\)+\{\s*\$\w+\s*=\s*\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\];(?:\s*\$\w+\s*=\s*"";\s*)+(?P>foreach)+\$\w+\s*=\s*\$\w+\([create_function\'\.]+\);\s*\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\)\);\s*\$\w+\(\);~mis',
            'fast' => '~(?(DEFINE)(?\'foreach\'(?:/\*\w+\*/)?\s*foreach\(\[[\d,]+\]\s*as\s*\$\w+\)\s*\{\s*\$\w+\s*\.=\s*\$\w+\[\$\w+\];\s*\}\s*(?:/\*\w+\*/)?\s*))(\$\w+)\s*=\s*"([^"]+)";\s*\$\w+\s*=\s*"";(?P>foreach)if\(isset\(\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\]\)+\{\s*\$\w+\s*=\s*\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\];(?:\s*\$\w+\s*=\s*"";\s*)+(?P>foreach)+\$\w+\s*=\s*\$\w+\([create_function\'\.]+\);\s*\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\)\);\s*\$\w+\(\);~mis',
            'id' => 'forEach'
        ),

        array(
            'full' => '~\$\w+\s*=\s*base64_decode\s*\([\'"][^\'"]+[\'"]\);\s*if\s*\(!function_exists\s*\("rotencode"\)\).{0,1000}eval\s*\(\$\w+\s*\(base64_decode\s*\([\'"][^"\']+[\'"]\)+;~msi',
            'fast' => '~\$\w+\s*=\s*base64_decode\s*\([\'"][^\'"]+[\'"]\);\s*if\s*\(!function_exists\s*\("rotencode"\)\).{0,1000}eval\s*\(\$\w+\s*\(base64_decode\s*\([\'"][^"\']+[\'"]\)+;~msi',
            'id' => 'PHPMyLicense',
        ),

        array(
            'full' => '~(\$\w{1,40})=file\(__FILE__\);if\(!function_exists\(\"([^\"]*)\"\)\)\{function\s*\2\((\$\w{1,40}),(\$\w{1,40})=\d+\)\{(\$\w{1,40})=implode\(\"[^\"]*\",\3\);(\$\w{1,40})=array\((\d+),(\d+),(\d+)\);if\(\4==0\)\s*(\$\w{1,40})=substr\(\5,\6\[\d+\],\6\[\d+\]\);elseif\(\4==1\)\s*\10=substr\(\5,\6\[\d+\]\+\6\[\d+\],\6\[\d+\]\);else\s*\10=trim\(substr\(\5,\6\[\d+\]\+\6\[\d+\]\+\6\[\d+\]\)\);return\s*\(\10\);\}\}eval\(\w{1,40}\(\2\(\1,2\),\2\(\1,1\)\)\);__halt_compiler\(\);[\w\+\=/]+~msi',
            'fast' => '~(\$\w{1,40})=file\(__FILE__\);if\(!function_exists\(\"([^\"]*)\"\)\)\{function\s*\2\((\$\w{1,40}),(\$\w{1,40})=\d+\)\{(\$\w{1,40})=implode\(\"[^\"]*\",\3\);(\$\w{1,40})=array\((\d+),(\d+),(\d+)\);if\(\4==0\)\s*(\$\w{1,40})=substr\(\5,\6\[\d+\],\6\[\d+\]\);elseif\(\4==1\)\s*\10=substr\(\5,\6\[\d+\]\+\6\[\d+\],\6\[\d+\]\);else\s*\10=trim\(substr\(\5,\6\[\d+\]\+\6\[\d+\]\+\6\[\d+\]\)\);return\s*\(\10\);\}\}eval\(\w{1,40}\(\2\(\1,2\),\2\(\1,1\)\)\);__halt_compiler\(\);~msi',
            'id' => 'zeura'),

        array(
            'full' => '~((\$\w{1,40})\s*=\s*[\'"]([^\'"]+)[\'"];)\s*.{0,10}?@?eval\s*\((base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\()+(\({0,1}\2\){0,1})\)+;~msi',
            'fast' => '~((\$\w{1,40})\s*=\s*[\'"]([^\'"]+)[\'"];)\s*.{0,10}?@?eval\s*\((base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\()+(\({0,1}\2\){0,1})\)+;~msi',
            'id' => 'evalVar'),

        array(
            'full' => '~function\s*(\w{1,40})\((\$\w{1,40})\)\{(\$\w{1,40})=\'base64_decode\';(\$\w{1,40})=\'gzinflate\';return\s*\4\(\3\(\2\)\);\}\$\w{1,40}=\'[^\']*\';\$\w{1,40}=\'[^\']*\';eval\(\1\(\'([^\']*)\'\)\);~msi',
            'fast' => '~function\s*(\w{1,40})\((\$\w{1,40})\)\{(\$\w{1,40})=\'base64_decode\';(\$\w{1,40})=\'gzinflate\';return\s*\4\(\3\(\2\)\);\}\$\w{1,40}=\'[^\']*\';\$\w{1,40}=\'[^\']*\';eval\(\1\(\'([^\']*)\'\)\);~msi',
            'id' => 'evalFunc'),

        array(
            'full' => '~function\s*(\w{1,40})\s*\((\$\w{1,40})\)\s*\{\s*(\$\w{1,40})\s*=\s*"\\\\x62\\\\x61\\\\x73\\\\x65\\\\x36\\\\x34\\\\x5f\\\\x64\\\\x65\\\\x63\\\\x6f\\\\x64\\\\x65";\s*(\$\w{1,40})\s*=\s*"\\\\x67\\\\x7a\\\\x69\\\\x6e\\\\x66\\\\x6c\\\\x61\\\\x74\\\\x65";\s*return\s*\4\s*\(\3\s*\(\2\)\);\s*\}\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*eval\s*\(\1\s*\(\"([^\"]*)\"\)\);~msi',
            'fast' => '~function\s*(\w{1,40})\s*\((\$\w{1,40})\)\s*\{\s*(\$\w{1,40})\s*=\s*"\\\\x62\\\\x61\\\\x73\\\\x65\\\\x36\\\\x34\\\\x5f\\\\x64\\\\x65\\\\x63\\\\x6f\\\\x64\\\\x65";\s*(\$\w{1,40})\s*=\s*"\\\\x67\\\\x7a\\\\x69\\\\x6e\\\\x66\\\\x6c\\\\x61\\\\x74\\\\x65";\s*return\s*\4\s*\(\3\s*\(\2\)\);\s*\}\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*eval\s*\(\1\s*\(\"([^\"]*)\"\)\);~msi',
            'id' => 'evalFunc'),

        array(
            'full' => '~preg_replace\(["\']/\.\*?/[^\)]+\)+;(["\'],["\'][^"\']+["\']\)+;)?~msi',
            'fast' => '~preg_replace\(["\']/\.\*?/~msi',
            'id' => 'eval'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*[\'"]([^\'"]*)[\'"]\s*;\s*(\$\w{1,40}\s*=\s*(strtolower|strtoupper)\s*\((\s*\1[\[\{]\s*\d+\s*[\]\}]\s*\.?\s*)+\);\s*)+\s*if\s*\(\s*isset\s*\(\s*\$\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*\{\s*eval\s*\(\s*\$\w{1,40}\s*\(\s*\$\s*\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*;\s*\}\s*~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*[\'"]([^\'"]*)[\'"]\s*;\s*(\$\w{1,40}\s*=\s*(strtolower|strtoupper)\s*\((\s*\1[\[\{]\s*\d+\s*[\]\}]\s*\.?\s*)+\);\s*)+\s*if\s*\(\s*isset\s*\(\s*\$\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*\{\s*eval\s*\(\s*\$\w{1,40}\s*\(\s*\$\s*\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*;\s*\}\s*~msi',
            'id' => 'evalInject'

        ),

        array(
            'full' => '~((\$\w+)\s*=\s*(([base64_decode\'\.\s]+)|([eval\'\.\s]+)|([create_function\'\.\s]+)|([stripslashes\'\.\s]+)|([gzinflate\'\.\s]+)|([strrev\'\.\s]+)|([str_rot13\'\.\s]+)|([gzuncompress\'\.\s]+)|([urldecode\'\.\s]+)([rawurldecode\'\.\s]+));\s*)+\$\w+\s*=\s*\$\w+\(\'\',(\s*\$\w+\s*\(\s*)+\'[^\']+\'\)+;\s*\$\w+\(\);~msi',
            'fast' => '~((\$\w+)\s*=\s*(([base64_decode\'\.\s]+)|([eval\'\.\s]+)|([create_function\'\.\s]+)|([stripslashes\'\.\s]+)|([gzinflate\'\.\s]+)|([strrev\'\.\s]+)|([str_rot13\'\.\s]+)|([gzuncompress\'\.\s]+)|([urldecode\'\.\s]+)([rawurldecode\'\.\s]+));\s*)+\$\w+\s*=\s*\$\w+\(\'\',(\s*\$\w+\s*\(\s*)+\'[^\']+\'\)+;\s*\$\w+\(\);~msi',
            'id' => 'createFuncConcat'

        ),

        array(
            'full' => '~(\$\w+)\s*=\s*base64_decode\("([^"]+)"\);(\1\s*=\s*ereg_replace\("([^"]+)","([^"]+)",\1\);)+\1=base64_decode\(\1\);eval\(\1\);~msi',
            'fast' => '~(\$\w+)\s*=\s*base64_decode\("([^"]+)"\);(\1\s*=\s*ereg_replace\("([^"]+)","([^"]+)",\1\);)+\1=base64_decode\(\1\);eval\(\1\);~msi',
            'id' => 'evalEregReplace'

        ),

        array(
            'full' => '~((\$\w+)\s*=\s*(([base64_decode"\'\.\s]+)|([eval"\'\.\s]+)|([create_function"\'\.\s]+)|([stripslashes"\'\.\s]+)|([gzinflate"\'\.\s]+)|([strrev"\'\.\s]+)|([str_rot13"\'\.\s]+)|([gzuncompress"\'\.\s]+)|([urldecode"\'\.\s]+)([rawurldecode"\'\.\s]+));\s*)+\s*@?eval\([^)]+\)+;~msi',
            'fast' => '~((\$\w+)\s*=\s*(([base64_decode"\'\.\s]+)|([eval"\'\.\s]+)|([create_function"\'\.\s]+)|([stripslashes"\'\.\s]+)|([gzinflate"\'\.\s]+)|([strrev"\'\.\s]+)|([str_rot13"\'\.\s]+)|([gzuncompress"\'\.\s]+)|([urldecode"\'\.\s]+)([rawurldecode"\'\.\s]+));\s*)+\s*@?eval\([^)]+\)+;~msi',
            'id' => 'evalWrapVar'

        ),

        array(
            'full' => '~\$\{"(.{1,20}?(\\\\x[0-9a-f]{2})+)+.?";@?eval\s*\(\s*([\'"?>.]+)?@?\s*(base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+\(?\$\{\$\{"[^\)]+\)+;~msi',
            'fast' => '~\$\{"(.{1,20}?(\\\\x[0-9a-f]{2})+)+.?";@?eval\s*\(\s*([\'"?>.]+)?@?\s*(base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+\(?\$\{\$\{"[^\)]+\)+;~msi',
            'id' => 'escapes'
        ),

        array(
            'full' => '~(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*@?\1\s*\(@?\2\s*\([\'"]([^\'"]+)[\'"]\)+;~msi',
            'fast' => '~(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*@?\1\s*\(@?\2\s*\([\'"]([^\'"]+)[\'"]\)+;~msi',
            'id' => 'assert',
        ),

        array(
            'full' => '~\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\'](\w+)[\'"];\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\']\2[\'"];\${\$\{"GLOBALS"\}\[[\'"]\3[\'"]\]}=[\'"]([^\'"]+)[\'"];eval.{10,50}?\$\{\$\{"GLOBALS"\}\[[\'"]\1[\'"]\]\}\)+;~msi',
            'fast' => '~\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\'](\w+)[\'"];\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\']\2[\'"];\${\$\{"GLOBALS"\}\[[\'"]\3[\'"]\]}=[\'"]([^\'"]+)[\'"];eval.{10,50}?\$\{\$\{"GLOBALS"\}\[[\'"]\1[\'"]\]\}\)+;~msi',
            'id' => 'evalVarVar',
        ),

        array(
            'full' => '~(\$\w+)=[\'"][^"\']+[\'"];(\$\w+)=strrev\(\'edoced_46esab\'\);eval\(\2\([\'"][^\'"]+[\'"]\)+;~msi',
            'fast' => '~(\$\w+)=[\'"][^"\']+[\'"];(\$\w+)=strrev\(\'edoced_46esab\'\);eval\(\2\([\'"][^\'"]+[\'"]\)+;~msi',
            'id' => 'edoced_46esab',
        ),

        array(
            'full' => '~(\$\w+)\s*=\s*"((?:[^"]|(?<=\\\\)")*)";(\$\w+)\s*=\s*(\1\[\d+\]\.?)+;(\$\w+)\s*=\s*[^;]+;(\$\w+)\s*=\s*"[^"]+";\$\w+\s*=\s*\5\."[^"]+"\.\6;\3\((\1\[\d+\]\.?)+,\s*\$\w+\s*,"\d+"\);~smi',
            'fast' => '~(\$\w+)\s*=\s*"((?:[^"]|(?<=\\\\)")*)";(\$\w+)\s*=\s*(\1\[\d+\]\.?)+;(\$\w+)\s*=\s*[^;]+;(\$\w+)\s*=\s*"[^"]+";\$\w+\s*=\s*\5\."[^"]+"\.\6;\3\((\1\[\d+\]\.?)+,\s*\$\w+\s*,"\d+"\);~smi',
            'id' => 'eval2'
        ),

        array(
            'full' => '~@?(eval|(\$\w+)\s*=\s*create_function)\s*\((\'\',)?\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|convert_uudecode\s*\(|htmlspecialchars_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+.*?[^\'")]+((\s*\.?[\'"]([^\'";]+\s*)+)?\s*[\'"\);]+)+(\s*\2\(\);)?~msi',
            'fast' => '~@?(eval|\$\w+\s*=\s*create_function)\s*\((\'\',)?\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|convert_uudecode\s*\(|htmlspecialchars_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|eval\s*\(|urldecode\s*\(|rawurldecode\s*\()+~msi',
            'id' => 'eval'
        ),

        array(
            'full' => '~eval\s*/\*[\w\s\.:,]+\*/\s*\([^\)]+\)+;~msi',
            'fast' => '~eval\s*/\*[\w\s\.:,]+\*/\s*\(~msi',
            'id' => 'eval'
        ),

        array(
            'full' => '~eval\("\\\\145\\\\166\\\\141\\\\154\\\\050\\\\142\\\\141\\\\163[^\)]+\)+;~msi',
            'fast' => '~eval\("\\\\145\\\\166\\\\141\\\\154\\\\050\\\\142\\\\141\\\\163~msi',
            'id' => 'evalHex'
        ),

        array(
            'full' => '~eval\s*\("\\\\x?\d+[^\)]+\)+;(?:[\'"]\)+;)?~msi',
            'fast' => '~eval\s*\("\\\\x?\d+~msi',
            'id' => 'evalHex'
        ),

        array(
            'full' => '~\$\w+=\'printf\';(\s*\$\w+\s*=\s*\'[^\']+\'\s*;)+\s*(\$\w+\s*=\s*\$\w+\([^\)]+\);\s*)+(\$\w+\s*=\s*\'[^\']+\';\s*)?(\s*(\$\w+\s*=\s*)?\$\w+\([^)]*\)+;\s*)+(echo\s*\$\w+;)?~msi',
            'fast' => '~\$\w+=\'printf\';(\s*\$\w+\s*=\s*\'[^\']+\'\s*;)+\s*(\$\w+\s*=\s*\$\w+\([^\)]+\);\s*)+(\$\w+\s*=\s*\'[^\']+\';\s*)?(\s*(\$\w+\s*=\s*)?\$\w+\([^)]*\)+;\s*)+(echo\s*\$\w+;)?~msi',
            'id' => 'seolyzer'
        ),

        array(
            'full' => '~(\$\w+)="((?:[^"]|(?<=\\\\)")*)";(\s*\$GLOBALS\[\'\w+\'\]\s*=\s*(?:\${)?(\1\[\d+\]}?\.?)+;\s*)+(.{0,400}\s*\1\[\d+\]\.?)+;\s*}~msi',
            'fast' => '~(\$\w+)="((?:[^"]|(?<=\\\\)")*)";(\s*\$GLOBALS\[\'\w+\'\]\s*=\s*(?:\${)?(\1\[\d+\]}?\.?)+;\s*)+(.{0,400}\s*\1\[\d+\]\.?)+;\s*}~msi',
            'id' => 'subst2',
        ),

        array(
            'full' => '~(\$\w+\s*=\s*"[^"]+";\s*)+(\$\w+\s*=\s*\$?\w+\("\w+"\s*,\s*""\s*,\s*"\w+"\);\s*)+\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\("\w+",\s*"",(\s*\$\w+\.?)+\)+;\$\w+\(\);~msi',
            'fast' => '~(\$\w+\s*=\s*"[^"]+";\s*)+(\$\w+\s*=\s*\$?\w+\("\w+"\s*,\s*""\s*,\s*"\w+"\);\s*)+\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\("\w+",\s*"",(\s*\$\w+\.?)+\)+;\$\w+\(\);~msi',
            'id' => 'strreplace',
        ),

        array(
            'full' => '~@?echo\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+.*?[^\'")]+((\s*\.?[\'"]([^\'";]+\s*)+)?\s*[\'"\);]+)+~msi',
            'fast' => '~@?echo\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+.*?[^\'")]+((\s*\.?[\'"]([^\'";]+\s*)+)?\s*[\'"\);]+)+~msi',
            'id' => 'echo',
        ),

        array(
            'full' => '~(\$\w+)="([^"]+)";\s*(\$\w+)=strtoupper\s*\((\1\[\d+\]\s*\.?\s*)+\)\s*;\s*if\(\s*isset\s*\(\${\s*\3\s*}\[\d*\s*\'\w+\'\s*\]\s*\)\s*\)\s*{eval\(\${\3\s*}\[\'\w+\']\s*\)\s*;}~smi',
            'fast' => '~(\$\w+)="([^"]+)";\s*(\$\w+)=strtoupper\s*\((\1\[\d+\]\s*\.?\s*)+\)\s*;\s*if\(\s*isset\s*\(\${\s*\3\s*}\[\d*\s*\'\w+\'\s*\]\s*\)\s*\)\s*{eval\(\${\3\s*}\[\'\w+\']\s*\)\s*;}~smi',
            'id' => 'strtoupper',
        ),

        array(
            'full' => '~(\$\w+)="[^"]+";\s*(\$\w+)=str_ireplace\("[^"]+","",\1\);(\$\w+)\s*=\s*"[^"]+";\s*function\s*(\w+)\((\$\w+,?)+\){\s*(\$\w+)=\s*create_function\(\'\',\$\w+\);\s*array_map\(\6,array\(\'\'\)+;\s*}\s*set_error_handler\(\'\4\'\);(\$\w+)=\2\(\3\);user_error\(\7,E_USER_ERROR\);\s*if\s*.+?}~msi',
            'fast' => '~(\$\w+)="[^"]+";\s*(\$\w+)=str_ireplace\("[^"]+","",\1\);(\$\w+)\s*=\s*"[^"]+";\s*function\s*(\w+)\((\$\w+,?)+\){\s*(\$\w+)=\s*create_function\(\'\',\$\w+\);\s*array_map\(\6,array\(\'\'\)+;\s*}\s*set_error_handler\(\'\4\'\);(\$\w+)=\2\(\3\);user_error\(\7,E_USER_ERROR\);\s*if\s*.+?}~msi',
            'id' => 'errorHandler',
        ),

        array(
            'full' => '~(\$\w+)=strrev\(str_ireplace\("[^"]+","","[^"]+"\)\);(\$\w+)="([^"]+)";eval\(\1\(\2\)+;}~msi',
            'fast' => '~(\$\w+)=strrev\(str_ireplace\("[^"]+","","[^"]+"\)\);(\$\w+)="([^"]+)";eval\(\1\(\2\)+;}~msi',
            'id' => 'evalIReplace',
        ),
        array(
            'full' => '~error_reporting\(0\);ini_set\("display_errors",\s*0\);if\(!defined\(\'(\w+)\'\)\){define\(\'\1\',__FILE__\);if\(!function_exists\("([^"]+)"\)\){function [^(]+\([^\)]+\).+?eval\(""\);.+?;eval\(\$[^\)]+\)\);[^\)]+\)+;return\s*\$[^;]+;\s*\?>([^;]+);~msi',
            'fast' => '~error_reporting\(0\);ini_set\("display_errors",\s*0\);if\(!defined\(\'(\w+)\'\)\){define\(\'\1\',__FILE__\);if\(!function_exists\("([^"]+)"\)\){function [^(]+\([^\)]+\).+?eval\(""\);.+?;eval\(\$[^\)]+\)\);[^\)]+\)+;return\s*\$[^;]+;\s*\?>([^;]+);~msi',
            'id' => 'PHPJiaMi',
        ),
    );

    private $full_source;
    private $prev_step;
    private $cur;
    private $obfuscated;
    private $max_level;
    private $max_time;
    private $run_time;
    private $fragments;

    public function __construct($text, $text2 = '', $max_level = 30, $max_time = 5)
    {
        if (
            strpos($text2, '=file(__FILE__);eval(base64_decode(')   //zeura hack
            && strpos($text2, '1)));__halt_compiler();')
        ) {
            $this->text = $text2;
            $this->full_source = $text2;
        } else {
            $this->text = $text;
            $this->full_source = $text;
        }
        $this->max_level = $max_level;
        $this->max_time = $max_time;
        $this->fragments = array();
    }

    public function getObfuscateType($str)
    {
        foreach ($this->signatures as $signature) {
            if (preg_match($signature['fast'], $str)) {
                return $signature['id'];
            }
        }
        return '';
    }

    private function getObfuscateFragment($str)
    {
        foreach ($this->signatures as $signature) {
            if (preg_match($signature['full'], $str, $matches)) {
                return $matches[0];
            }
        }
        return '';
    }

    public function getFragments()
    {
        $this->grabFragments();
        if (count($this->fragments) > 0) {
            return $this->fragments;
        }
        return false;
    }

    private function grabFragments()
    {
        if ($this->cur == null) {
            $this->cur = $this->text;
        }
        $str = $this->cur;
        while ($sign = current($this->signatures)) {
            $regex = $sign['full'];
            if (preg_match($regex, $str, $matches)) {
                $this->fragments[$matches[0]] = $matches[0];
                $str = str_replace($matches[0], '', $str);
            } else {
                next($this->signatures);
            }
        }
    }

    private function deobfuscateFragments()
    {
        $prev_step = '';
        if (count($this->fragments) > 0) {
            $i = 0;
            foreach ($this->fragments as $frag => $value) {
                $type = $this->getObfuscateType($value);
                while ($type !== '' && $i < 15) {
                    $find = $this->getObfuscateFragment($value);
                    $func = 'deobfuscate' . ucfirst($type);
                    $temp = @$this->$func($find);
                    $value = str_replace($find, $temp, $value);
                    $this->fragments[$frag] = $value;
                    $type = $this->getObfuscateType($value);
                    if ($prev_step == $value) {
                        break;
                    } else {
                        $prev_step = $value;
                    }
                    $i++;
                }
            }
        }
    }

    public function deobfuscate()
    {
        $prev_step = '';
        $deobfuscated = '';
        $this->run_time = microtime(true);
        $this->cur = $this->text;
        $this->grabFragments();
        $this->deobfuscateFragments();
        $deobfuscated = $this->cur;
        if (count($this->fragments) > 0 ) {
            foreach ($this->fragments as $fragment => $text) {
                $deobfuscated = str_replace($fragment, $text, $deobfuscated);
            }
        }

        $deobfuscated = preg_replace_callback('~"[\w\\\\\s=;_<>&/\.-]+"~msi', function ($matches) {
            return preg_match('~\\\\x[2-7][0-9a-f]|\\\\1[0-2][0-9]|\\\\[3-9][0-9]|\\\\0[0-4][0-9]|\\\\1[0-7][0-9]~msi', $matches[0]) ? stripcslashes($matches[0]) : $matches[0];
        }, $deobfuscated);

        $deobfuscated = preg_replace_callback('~echo\s*"((?:[^"]|(?<=\\\\)")*)"~msi', function ($matches) {
            return preg_match('~\\\\x[2-7][0-9a-f]|\\\\1[0-2][0-9]|\\\\[3-9][0-9]|\\\\0[0-4][0-9]|\\\\1[0-7][0-9]~msi', $matches[0]) ? stripcslashes($matches[0]) : $matches[0];
        }, $deobfuscated);

        preg_match_all('~(global\s*(\$[\w_]+);)\2\s*=\s*"[^"]+";~msi', $deobfuscated, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $deobfuscated = str_replace($match[0], '', $deobfuscated);
            $deobfuscated = str_replace($match[1], '', $deobfuscated);
        }

        preg_match_all('~\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\'](\w+)[\'"];~msi', $deobfuscated, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $deobfuscated = preg_replace_callback('~\$\{\$\{"GLOBALS"\}\[[\'"]' . $match[1] . '[\'"]\]\}~msi', function ($matches) use ($match) {
                return '$' . $match[2];
            }, $deobfuscated);
            $deobfuscated = str_replace($match[0], '', $deobfuscated);
        }

        $deobfuscated = preg_replace_callback('~\$\{(\$\w+)\}~msi', function ($matches) use ($deobfuscated) {
            if (isset($matches[1])) {
                preg_match('~\\' . $matches[1] . '\s*=\s*["\'](\w+)[\'"];~msi', $deobfuscated, $matches2);
                if (isset($matches2[1])) {
                    return '$' . $matches2[1];
                }
                return $matches[0];
            }
        }, $deobfuscated);

        if (strpos($deobfuscated, 'chr(')) {
            $deobfuscated = preg_replace_callback('~chr\((\d+)\)~msi', function ($matches) {
                return "'" . chr($matches[1]) . "'";
            }, $deobfuscated);
        }

        return $deobfuscated;
    }

    private function deobfuscatePHPJiaMi($str)
    {
        preg_match('~error_reporting\(0\);ini_set\("display_errors",\s*0\);if\(!defined\(\'(\w+)\'\)\){define\(\'\1\',__FILE__\);if\(!function_exists\("([^"]+)"\)\){function [^(]+\([^\)]+\).+?eval\(""\);.+?;eval\(\$[^\)]+\)\);[^\)]+\)+;return\s*\$[^;]+;\s*\?>([^;]+);~msi', $str, $matches);
        $find = $matches[0];
        $bin = bin2hex($str);
        preg_match('~6257513127293b24[a-z0-9]{2,30}3d24[a-z0-9]{2,30}2827([a-z0-9]{2,30})27293b~', $bin, $hash);
        preg_match('~2827([a-z0-9]{2})27293a24~', $bin, $rand);
        $hash = hex2bin($hash[1]);
        $rand = hex2bin($rand[1]);
        $res = Helpers::PHPJiaMi_decoder(substr($matches[3], 0, -46), $hash, $rand);

        $res = str_rot13(@gzuncompress($res) ? @gzuncompress($res) : $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEvalIReplace($str)
    {
        preg_match('~(\$\w+)=strrev\(str_ireplace\("[^"]+","","[^"]+"\)\);(\$\w+)="([^"]+)";eval\(\1\(\2\)+;}~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $res = base64_decode($matches[3]);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateErrorHandler($str)
    {
        preg_match('~(\$\w+)="[^"]+";\s*(\$\w+)=str_ireplace\("[^"]+","",\1\);(\$\w+)\s*=\s*"([^"]+)";\s*function\s*(\w+)\((\$\w+,?)+\){\s*(\$\w+)=\s*create_function\(\'\',\$\w+\);\s*array_map\(\7,array\(\'\'\)+;\s*}\s*set_error_handler\(\'\5\'\);(\$\w+)=\2\(\3\);user_error\(\8,E_USER_ERROR\);\s*if\s*.+?}~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $res = base64_decode($matches[4]);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateStrtoupper($str)
    {
        preg_match('~(\$\w+)="([^"]+)";\s*(\$\w+)=strtoupper\s*\((\1\[\d+\]\s*\.?\s*)+\)\s*;\s*if\(\s*isset\s*\(\${\s*\3\s*}\[\d*\s*\'\w+\'\s*\]\s*\)\s*\)\s*{eval\(\${\3\s*}\[\'\w+\']\s*\)\s*;}~smi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $alph = $matches[2];
        $var = $matches[1];
        $res = str_replace("{$var}=\"{$alph}\";", '', $res);
        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($var . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($var . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);
        $res = str_replace("' . '", '', $res);
        $res = str_replace("' '", '', $res);
        preg_match('~(\$\w+)\s*=\s*strtoupper\s*\(\s*\'(\w+)\'\s*\)\s*;~msi', $res, $matches);
        $matches[2] = strtoupper($matches[2]);
        $res = str_replace($matches[0], '', $res);
        $res = preg_replace_callback('~\${\s*\\'. $matches[1] .'\s*}~msi', function ($params) use ($matches) {
            return '$' . $matches[2];
        }, $res);

        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEval2($str)
    {
        preg_match('~(\$\w+)\s*=\s*"((?:[^"]|(?<=\\\\)")*)";(\$\w+)\s*=\s*(\1\[\d+\]\.?)+;(\$\w+)\s*=\s*[^;]+;(\$\w+)\s*=\s*"[^"]+";\$\w+\s*=\s*\5\."([^"]+)"\.\6;\3\((\1\[\d+\]\.?)+,\s*\$\w+\s*,"\d+"\);~smi', $str, $matches);
        $res = $str;
        $find = $matches[0];
        $alph = $matches[2];
        $var = $matches[1];
        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($var . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($var . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);
        $res = gzinflate(base64_decode(substr($matches[7], 1, -1)));
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEvalEregReplace($str)
    {
        preg_match('~(\$\w+)\s*=\s*base64_decode\("([^"]+)"\);(\1\s*=\s*ereg_replace\("([^"]+)","([^"]+)",\1\);)+\1=base64_decode\(\1\);eval\(\1\);~msi', $str, $matches);
        $find = $matches[0];
        $res = base64_decode($matches[2]);
        preg_match_all('~(\$\w+)\s*=\s*ereg_replace\("([^"]+)","([^"]+)",\1\);~smi', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $res = preg_replace('/' . $match[2] . '/', $match[3], $res);
        }
        $res = base64_decode($res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateStrreplace($str)
    {
        preg_match('~(\$\w+\s*=\s*"[^"]+";\s*)+(\$\w+\s*=\s*\$?\w+\("\w+"\s*,\s*""\s*,\s*"\w+"\);\s*)+\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\("\w+",\s*"",(\s*\$\w+\.?)+\)+;\$\w+\(\);~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;

        $str_replace = '';
        $base64_decode = '';
        $layer = '';

        preg_match_all('~(\$\w+)\s*=\s*\"([^"]+)\"\s*;~msi', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $i => $match) {
            $vars[$match[1]] = $match[2];
        }

        $res = preg_replace_callback('~(\$\w+)\s*=\s*str_replace\("(\w+)",\s*"",\s*"(\w+)"\)~msi',
            function ($matches) use (&$vars, &$str_replace) {
                $vars[$matches[1]] = str_replace($matches[2], "", $matches[3]);
                if ($vars[$matches[1]] == 'str_replace') {
                    $str_replace = $matches[1];
                }
                $tmp = $matches[1] . ' = "' . $vars[$matches[1]] . '"';
                return $tmp;
            }, $res);

        $res = preg_replace_callback('~(\$\w+)\s*=\s*\\' . $str_replace . '\("(\w+)",\s*"",\s*"(\w+)"\)~msi',
            function ($matches) use (&$vars, &$base64_decode) {
                $vars[$matches[1]] = str_replace($matches[2], "", $matches[3]);
                if ($vars[$matches[1]] == 'base64_decode') {
                    $base64_decode = $matches[1];
                }
                $tmp = $matches[1] . ' = "' . $vars[$matches[1]] . '"';
                return $tmp;
            }, $res);

        $res = preg_replace_callback('~\\' . $base64_decode . '\(\\' . $str_replace . '\("(\w+)",\s*"",\s*([\$\w\.]+)\)~msi',
            function ($matches) use (&$vars, &$layer) {
                $tmp = explode('.', $matches[2]);
                foreach ($tmp as &$item) {
                    $item = $vars[$item];
                }
                $tmp = implode('', $tmp);
                $layer = base64_decode(str_replace($matches[1], "", $tmp));
                return $matches[0];
            }, $res);

        $res = $layer;
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateSeolyzer($str)
    {
        preg_match('~\$\w+=\'printf\';(\s*\$\w+\s*=\s*\'[^\']+\'\s*;)+\s*(\$\w+\s*=\s*\$\w+\([^\)]+\);\s*)+(\$\w+\s*=\s*\'[^\']+\';\s*)?(\s*(\$\w+\s*=\s*)?\$\w+\([^)]*\)+;\s*)+(echo\s*\$\w+;)?~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $vars = array();
        $base64_decode = '';
        $layer = '';
        $gzuncompress = '';
        preg_match_all('~(\$\w+)\s*=\s*\'([^\']+)\'\s*;~msi', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $i => $match) {
            $vars[$match[1]] = $match[2];
            if ($match[2] == 'base64_decode') {
                $base64_decode = $match[1];
            }
        }

        $res = preg_replace_callback('~\s*=\s*\\' . $base64_decode . '\((\$\w+)\)~msi', function ($matches) use (&$vars, &$gzuncompress, &$layer) {
            if (isset($vars[$matches[1]])) {
                $tmp = base64_decode($vars[$matches[1]]);
                if ($tmp == 'gzuncompress') {
                    $gzuncompress = $matches[1];
                }
                $vars[$matches[1]] = $tmp;
                $tmp = " = '{$tmp}'";
            } else {
                $tmp = $matches[1];
            }
            return $tmp;
        }, $res);

        if ($gzuncompress !== '') {
            $res = preg_replace_callback('~\\' . $gzuncompress . '\(\s*\\' . $base64_decode . '\((\$\w+)\)~msi',
                function ($matches) use (&$vars, $gzuncompress, &$layer) {
                    if (isset($vars[$matches[1]])) {
                        $tmp = gzuncompress(base64_decode($vars[$matches[1]]));
                        $layer = $matches[1];
                        $vars[$matches[1]] = $tmp;
                        $tmp = "'{$tmp}'";
                    } else {
                        $tmp = $matches[1];
                    }
                    return $tmp;
                }, $res);
            $res = $vars[$layer];
        } else if (preg_match('~\$\w+\(\s*\\' . $base64_decode . '\((\$\w+)\)~msi', $res)) {
            $res = preg_replace_callback('~\$\w+\(\s*\\' . $base64_decode . '\((\$\w+)\)~msi',
                function ($matches) use (&$vars, &$layer) {
                    if (isset($vars[$matches[1]])) {
                        $tmp = base64_decode($vars[$matches[1]]);
                        $layer = $matches[1];
                        $vars[$matches[1]] = $tmp;
                        $tmp = "'{$tmp}'";
                    } else {
                        $tmp = $matches[1];
                    }
                    return $tmp;
                }, $res);
            $res = $vars[$layer];
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateCreateFunc($str)
    {
        preg_match('~(\$\w+)=[create_function".]+;\s*\1=\1\(\'(\$\w+)\',[\'.eval\("\?>".gzinflate\(base64_decode]+\2\)+;\'\);\s*\1\(\'([^\']+)\'\);~msi', $str, $matches);
        $find = $matches[0];
        $res = ' ?>' . gzinflate(base64_decode($matches[3]));
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateCreateFuncConcat($str)
    {
        preg_match('~((\$\w+)\s*=\s*(([base64_decode\'\.\s]+)|([eval\'\.\s]+)|([create_function\'\.\s]+)|([stripslashes\'\.\s]+)|([gzinflate\'\.\s]+)|([strrev\'\.\s]+)|([str_rot13\'\.\s]+)|([gzuncompress\'\.\s]+)|([urldecode\'\.\s]+)([rawurldecode\'\.\s]+));\s*)+\$\w+\s*=\s*\$\w+\(\'\',(\s*\$\w+\s*\(\s*)+\'[^\']+\'\)+;\s*\$\w+\(\);~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $vars = array();
        $res = preg_replace_callback('~(?|(\$\w+)\s*=\s*(([base64_decode\'\.\s]+)|([eval\'\.\s]+)|([create_function\'\.\s]+)|([stripslashes\'\.\s]+)|([gzinflate\'\.\s]+)|([strrev\'\.\s]+)|([str_rot13\'\.\s]+)|([gzuncompress\'\.\s]+)|([urldecode\'\.\s]+)([rawurldecode\'\.\s]+));)~', function($matches) use (&$vars) {
            $tmp = str_replace("' . '", '', $matches[0]);
            $tmp = str_replace("'.'", '', $tmp);
            $value = str_replace("' . '", '', $matches[2]);
            $value = str_replace("'.'", '', $value);
            $vars[$matches[1]] = substr($value, 1, -1);
            return $tmp;
        }, $res);

        foreach($vars as $key => $var) {
            $res = str_replace($key, $var, $res);
            $res = str_replace($var . " = '" . $var . "';", '', $res);
            $res = str_replace($var . ' = "";', '', $res);
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEvalWrapVar($str)
    {
        preg_match('~((\$\w+)\s*=\s*(([base64_decode"\'\.\s]+)|([eval"\'\.\s]+)|([create_function"\'\.\s]+)|([stripslashes"\'\.\s]+)|([gzinflate"\'\.\s]+)|([strrev"\'\.\s]+)|([str_rot13"\'\.\s]+)|([gzuncompress"\'\.\s]+)|([urldecode"\'\.\s]+)([rawurldecode"\'\.\s]+));\s*)+\s*@?eval\([^)]+\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $vars = array();
        $res = preg_replace_callback('~(?|(\$\w+)\s*=\s*(([base64_decode"\'\.\s]+)|([eval"\'\.\s]+)|([create_function"\'\.\s]+)|([stripslashes"\'\.\s]+)|([gzinflate"\'\.\s]+)|([strrev"\'\.\s]+)|([str_rot13"\'\.\s]+)|([gzuncompress"\'\.\s]+)|([urldecode"\'\.\s]+)([rawurldecode"\'\.\s]+));)~msi', function($matches) use (&$vars) {
            $tmp = str_replace("' . '", '', $matches[0]);
            $tmp = str_replace("'.'", '', $tmp);
            $value = str_replace("' . '", '', $matches[2]);
            $value = str_replace("'.'", '', $value);
            $vars[$matches[1]] = substr($value, 1, -1);
            return $tmp;
        }, $res);
        foreach($vars as $key => $var) {
            $res = str_replace($key, $var, $res);
            $res = str_replace($var . '="' . $var . '";', '', $res);
            $res = str_replace($var . ' = "' . $var . '";', '', $res);
        }

        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateForEach($str)
    {
        preg_match('~(?(DEFINE)(?\'foreach\'(?:/\*\w+\*/)?\s*foreach\(\[[\d,]+\]\s*as\s*\$\w+\)\s*\{\s*\$\w+\s*\.=\s*\$\w+\[\$\w+\];\s*\}\s*(?:/\*\w+\*/)?\s*))(\$\w+)\s*=\s*"([^"]+)";\s*\$\w+\s*=\s*"";(?P>foreach)if\(isset\(\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\]\)+\{\s*\$\w+\s*=\s*\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\];(?:\s*\$\w+\s*=\s*"";\s*)+(?P>foreach)+\$\w+\s*=\s*\$\w+\([create_function\'\.]+\);\s*\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\)\);\s*\$\w+\(\);~mis', $str, $matches);
        $find = $matches[0];
        $alph = $matches[3];
        $vars = array();
        $res = $str;

        preg_replace('~\s*/\*\w+\*/\s*~msi', '', $res);

        $res = preg_replace_callback('~foreach\(\[([\d,]+)\]\s*as\s*\$\w+\)\s*\{\s*(\$\w+)\s*\.=\s*\$\w+\[\$\w+\];\s*\}~mis', function($matches) use ($alph, &$vars) {
            $chars = explode(',', $matches[1]);
            $value = '';
            foreach ($chars as $char) {
                $value .= $alph[$char];
            }
            $vars[$matches[2]] = $value;
            return "{$matches[2]} = '{$value}';";
        }, $res);

        foreach($vars as $key => $var) {
            $res = str_replace($key, $var, $res);
            $res = str_replace($var . " = '" . $var . "';", '', $res);
            $res = str_replace($var . ' = "";', '', $res);
        }

        preg_match('~(\$\w+)\s*=\s*strrev\([create_function\.\']+\);~ms', $res, $matches);
        $res = str_replace($matches[0], '', $res);
        $res = str_replace($matches[1], 'create_function', $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateSubst2($str)
    {
        preg_match('~(\$\w+)="([^"])+(.{0,70}\1.{0,400})+;\s*}~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        preg_match('~(\$\w+)="(.+?)";~msi', $str, $matches);
        $alph = stripcslashes($matches[2]);
        $var = $matches[1];
        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($var . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($var . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);
        preg_match_all('~(\$GLOBALS\[\'\w{1,40}\'\])\s*=\s*\'(([^\'\\\\]++|\\\\.)*)\';~msi', $res, $matches, PREG_SET_ORDER);

        foreach ($matches as $index => $var) {
            $res = str_replace($var[1], $var[2], $res);
            $res = str_replace($var[2] . " = '" . $var[2] . "';", '', $res);
        }

        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateAssert($str)
    {
        preg_match('~(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*@?\1\s*\(@?\2\s*\([\'"]([^\'"]+)[\'"]\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = base64_decode($matches[3]);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateUrlDecode2($str)
    {
        preg_match('~(\$[\w{1,40}]+)=urldecode\(?[\'"]([\w+%=-]+)[\'"]\);(\s*\$\w+\.?=(\$\w+\{\d+\}\s*[\.;]?\s*)+)+((\$\w+=["\']([^\'"]+)[\'"];\s*eval\(\'\?>\'\.[\$\w\(\)\*\d,\s]+);|(eval\(\$\w+\([\'"]([^\'"]+)[\'"]\)+;))~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        if (isset($matches[9])) {
            $res = base64_decode($matches[9]);
        }
        preg_match('~\$\w+=["\']([^\'"]+)[\'"];\s*eval\(\'\?>\'\.[\$\w\(\)\*\d,\s]+;~msi', $res, $matches);
        $res = base64_decode(strtr(substr($matches[1], 52*2), substr($matches[1], 52, 52), substr($matches[1], 0, 52)));
        $res = str_replace($find, ' ?>' . $res, $str);
        return $res;
    }

    private function deobfuscatePHPMyLicense($str)
    {
        preg_match('~\$\w+\s*=\s*base64_decode\s*\([\'"][^\'"]+[\'"]\);\s*if\s*\(!function_exists\s*\("rotencode"\)\).{0,1000}eval\s*\(\$\w+\s*\(base64_decode\s*\([\'"]([^"\']+)[\'"]\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $hang = 10;
        while(preg_match('~eval\s*\(\$\w+\s*\(base64_decode\s*\([\'"]([^"\']+)[\'"]\)+;~msi', $res, $matches) && $hang--) {
            $res = gzinflate(base64_decode($matches[1]));
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEdoced_46esab($str)
    {
        preg_match('~(\$\w+)=[\'"]([^"\']+)[\'"];(\$\w+)=strrev\(\'edoced_46esab\'\);eval\(\3\([\'"]([^\'"]+)[\'"]\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = '';
        $decoder = base64_decode($matches[4]);
        preg_match('~(\$\w+)=base64_decode\(\$\w+\);\1=strtr\(\1,[\'"]([^\'"]+)[\'"],[\'"]([^\'"]+)[\'"]\);~msi', $decoder, $matches2);
        $res = base64_decode($matches[2]);
        $res = strtr($res, $matches2[2], $matches2[3]);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEvalVarVar($str)
    {
        preg_match('~\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\'](\w+)[\'"];\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\']\2[\'"];(\${\$\{"GLOBALS"\}\[[\'"]\3[\'"]\]})=[\'"]([^\'"]+)[\'"];eval.{10,50}?(\$\{\$\{"GLOBALS"\}\[[\'"]\1[\'"]\]\})\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = '';
        $res = str_replace($matches[4], '$' . $matches[2], $str);
        $res = str_replace($matches[6], '$' . $matches[2], $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEscapes($str)
    {
        preg_match('~\$\{"(.{1,20}?(\\\\x[0-9a-f]{2})+)+.?";@?eval\s*\(\s*([\'"?>.]+)?@?\s*(base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+\(?\$\{\$\{"[^\)]+\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = '';
        $res = stripcslashes($str);
        $res = str_replace($find, $res, $str);
        return $res;
    }


    private function deobfuscateparenthesesString($str)
    {
        preg_match('~for\((\$\w+)=\d+,(\$\w+)=\'([^\$]+)\',(\$\w+)=\'\';@?ord\(\2\[\1\]\);\1\+\+\)\{if\(\1<\d+\)\{(\$\w+)\[\2\[\1\]\]=\1;\}else\{\$\w+\.\=@?chr\(\(\5\[\2\[\1\]\]<<\d+\)\+\(\5\[\2\[\+\+\1\]\]\)\);\}\}\s*.{0,500}eval\(\4\);(if\(isset\(\$_(GET|REQUEST|POST|COOKIE)\[[\'"][^\'"]+[\'"]\]\)\)\{[^}]+;\})?~msi', $str, $matches);
        $find = $matches[0];
        $res = '';
        $temp = array();
        $matches[3] = stripcslashes($matches[3]);
        for($i=0; $i < strlen($matches[3]); $i++)
        {
            if($i < 16) $temp[$matches[3][$i]] = $i;
            else $res .= @chr(($temp[$matches[3][$i]]<<4) + ($temp[$matches[3][++$i]]));
        }

        if(!isset($matches[6])) {
            //$xor_key = 'SjJVkE6rkRYj';
            $xor_key = $res^"\n//adjust sy"; //\n//adjust system variables";
            $res = $res ^ substr(str_repeat($xor_key, (strlen($res) / strlen($xor_key)) + 1), 0, strlen($res));
        }
        if(substr($res,0,12)=="\n//adjust sy") {
            $res = str_replace($find, $res, $str);
            return $res;
        } else return $str;
    }

    private function deobfuscateEvalInject($str)
    {
        $res = $str;
        preg_match('~(\$\w{1,40})\s*=\s*[\'"]([^\'"]*)[\'"]\s*;\s*(\$\w{1,40}\s*=\s*(strtolower|strtoupper)\s*\((\s*\1[\[\{]\s*\d+\s*[\]\}]\s*\.?\s*)+\);\s*)+\s*if\s*\(\s*isset\s*\(\s*\$\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*\{\s*eval\s*\(\s*\$\w{1,40}\s*\(\s*\$\s*\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*;\s*\}\s*~msi', $str, $matches);
        $find = $matches[0];
        $alph = $matches[2];

        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($matches[1] . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[1] . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
        }

        $res = str_replace("''", '', $res);
        $res = str_replace("' '", '', $res);

        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateWebshellObf($str)
    {
        $res = $str;
        preg_match('~function\s*(\w{1,40})\s*\(\s*(\$\w{1,40})\s*,\s*(\$\w{1,40})\s*\)\s*\{\s*(\$\w{1,40})\s*=\s*str_rot13\s*\(\s*gzinflate\s*\(\s*str_rot13\s*\(\s*base64_decode\s*\(\s*[\'"]([^\'"]*)[\'"]\s*\)\s*\)\s*\)\s*\)\s*;\s*(if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*(\$\w{1,40})\s*=(\$\w+[\{\[]\d+[\}\]]\.?)+;return\s*(\$\w+)\(\3\);\s*\}\s*else\s*)+\s*if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*return\s*eval\(\3\);\s*\}\s*\};\s*(\$\w{1,40})\s*=\s*[\'"][^\'"]*[\'"];(\s*\10\([\'"][^\'"]*[\'"],)+\s*[\'"]([^\'"]*)[\'"]\s*\)+;~msi',$str, $matches);
        $find = $matches[0];

        $alph = str_rot13(gzinflate(str_rot13(base64_decode($matches[5]))));

        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($matches[4] . '{' . $i . '}.', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[4] . '{' . $i . '}', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);

        $res = base64_decode(gzinflate(str_rot13(convert_uudecode(gzinflate(base64_decode(strrev($matches[12])))))));
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateXorFName($str)
    {
        preg_match('~(\$\w+)\s*=\s*basename\s*\(trim\s*\(preg_replace\s*\(rawurldecode\s*\([\'"][%0-9a-f\.]+["\']\),\s*\'\',\s*__FILE__\)\)\);\s*(\$\w+)\s*=\s*["\']([^\'"]+)["\'];\s*eval\s*\(rawurldecode\s*\(\2\)\s*\^\s*substr\s*\(str_repeat\s*\(\1,\s*\(strlen\s*\(\2\)/strlen\s*\(\1\)\)\s*\+\s*1\),\s*0,\s*strlen\s*\(\2\)\)\);~msi', $str, $matches);
        $find = $matches[0];
        $xored = rawurldecode($matches[3]);
        $xor_key = $xored ^ 'if (!defined(';
        $php = $xored ^ substr(str_repeat($xor_key, (strlen($matches[3]) / strlen($xor_key)) + 1), 0, strlen($matches[3]));
        preg_match('~\$\w{1,40}\s*=\s*((\'[^\']+\'\s*\.?\s*)+);\s*\$\w+\s*=\s*Array\(((\'\w\'=>\'\w\',?\s*)+)\);~msi', $php, $matches);
        $matches[1] = str_replace(array(" ", "\r", "\n", "\t", "'.'"), '', $matches[1]);
        $matches[3] = str_replace(array(" ", "'", ">"), '', $matches[3]);
        $temp = explode(',', $matches[3]);
        $array = array();
        foreach ($temp as $value) {
            $temp = explode("=", $value);
            $array[$temp[0]] = $temp[1];
        }
        $res = '';
        for ($i=0; $i < strlen($matches[1]); $i++) {
            $res .= isset($array[$matches[1][$i]]) ? $array[$matches[1][$i]] : $matches[1][$i];
        }
        $res = substr(rawurldecode($res), 1, -2);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateSubstCreateFunc($str)
    {
        preg_match('~(\$\w{1,40})=\'(([^\'\\\\]|\\\\.)*)\';\s*((\$\w{1,40})=(\1\[\d+].?)+;\s*)+(\$\w{1,40})=\'\';\s*(\$\w{1,40})\(\7,\$\w{1,40}\.\"([^\"]+)\"\.\$\w{1,40}\.\5\);~msi', $str, $matches);
        $find = $matches[0];
        $php = base64_decode($matches[9]);
        preg_match('~(\$\w{1,40})=(\$\w{1,40})\("([^\']+)"\)~msi', $php, $matches);
        $matches[3] = base64_decode($matches[3]);
        $php = '';
        for ($i = 1; $i < strlen($matches[3]); $i++) {
            if ($i % 2) {
                $php .= substr($matches[3], $i, 1);
            }
        }
        $php = str_replace($find, $php, $str);
        return $php;
    }

    private function deobfuscateZeura($str)
    {
        preg_match('~(\$\w{1,40})=file\(__FILE__\);if\(!function_exists\(\"([^\"]*)\"\)\)\{function\s*\2\((\$\w{1,40}),(\$\w{1,40})=\d+\)\{(\$\w{1,40})=implode\(\"[^\"]*\",\3\);(\$\w{1,40})=array\((\d+),(\d+),(\d+)\);if\(\4==0\)\s*(\$\w{1,40})=substr\(\5,\6\[\d+\],\6\[\d+\]\);elseif\(\4==1\)\s*\10=substr\(\5,\6\[\d+\]\+\6\[\d+\],\6\[\d+\]\);else\s*\10=trim\(substr\(\5,\6\[\d+\]\+\6\[\d+\]\+\6\[\d+\]\)\);return\s*\(\10\);\}\}eval\(\w{1,40}\(\2\(\1,2\),\2\(\1,1\)\)\);__halt_compiler\(\);[\w\+\=/]+~msi', $str, $matches);
        $offset = intval($matches[8]) + intval($matches[9]);
        $obfPHP = explode('__halt_compiler();', $str);
        $obfPHP = end($obfPHP);
        $php = gzinflate(base64_decode(substr($obfPHP, $offset)));
        $php = stripcslashes($php);
        $php = str_replace($matches[0], $php, $str);
        return $php;
    }

    private function deobfuscateSourceCop($str)
    {
        preg_match('~if\(\!function_exists\(\'findsysfolder\'\)\){function findsysfolder\(\$fld\).+\$REXISTHEDOG4FBI=\'([^\']+)\';\$\w+=\'[^\']+\';\s*eval\(\w+\(\'([^\']+)\',\$REXISTHEDOG4FBI\)\);~msi', $str, $matches);
        $key = $matches[2];
        $obfPHP = $matches[1];
        $res = '';
        $index = 0;
        $len = strlen($key);
        $temp = hexdec('&H' . substr($obfPHP, 0, 2));
        for ($i = 2; $i < strlen($obfPHP); $i += 2) {
            $bytes = hexdec(trim(substr($obfPHP, $i, 2)));
            $index = (($index < $len) ? $index + 1 : 1);
            $decoded = $bytes ^ ord(substr($key, $index - 1, 1));
            if ($decoded <= $temp) {
                $decoded = 255 + $decoded - $temp;
            } else {
                $decoded = $decoded - $temp;
            }
            $res = $res . chr($decoded);
            $temp = $bytes;
        }
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateGlobalsSubst($str)
    {
        $vars = array();
        preg_match_all('~\$(\w{1,40})=\'([^\']+)\';~msi', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $vars[$match[1]] = $match[2];
        }
        foreach ($vars as $var => $value) {
            $str = str_replace('$GLOBALS[\'' . $var .'\']', $value, $str);
        }
        return $str;
    }

    private function deobfuscateGlobalsArray($str)
    {
        $res = $str;
        preg_match('~\$\w+\s*=\s*\d+;\s*\$GLOBALS\[\'[^\']+\'\]\s*=\s*Array\(\);\s*global\s*\$\w+;(\$\w{1,40})\s*=\s*\$GLOBALS;\$\{"\\\\x[a-z0-9\\\\]+"\}\[(\'\w+\')\]\s*=\s*\"(([^\"\\\\]|\\\\.)*)\";\1\[(\1\[\2\]\[\d+\].?).+?exit\(\);\}+~msi', $str, $matches);
        $alph = stripcslashes($matches[3]);
        $res = preg_replace('~\${"[\\\\x0-9a-f]+"}\[\'\w+\'\]\s*=\s*"[\\\\x0-9a-f]+";~msi', '', $res);

        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($matches[1] .'[' . $matches[2] . ']' . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[1] .'[' . $matches[2] . ']' . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);

        preg_match_all('~\\' . $matches[1] . '\[(\'\w+\')]\s*=\s*\'(\w+)\';~msi', $res, $funcs);

        $vars = $funcs[1];
        $func = $funcs[2];

        foreach ($vars as $index => $var) {
            $res = str_replace($matches[1] . '[' . $var . ']', $func[$index], $res);
        }

        foreach ($func as $remove) {
            $res = str_replace($remove . " = '" . $remove . "';", '', $res);
            $res = str_replace($remove . "='" . $remove . "';", '', $res);
        }
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateObfB64($str)
    {
        preg_match('~(\$\w{1,50}\s*=\s*array\((\'\d+\',?)+\);)+\$\w{1,40}=\"([^\"]+)\";if\s*\(!function_exists\(\"\w{1,50}\"\)\)\s*\{\s*function\s*[^\}]+\}\s*return\s*\$\w+;\}[^}]+}~msi', $str, $matches);
        $res = base64_decode($matches[3]);
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateArrayOffsets($str)
    {
        $vars = array();
        preg_match('~(\$\w{1,40})\s*=\s*\'([^\']*)\';\s*(\$\w{1,40})\s*=\s*explode\s*\((chr\s*\(\s*\(\d+\-\d+\)\)),substr\s*\(\1,\s*\((\d+\-\d+)\),\s*\(\s*(\d+\-\d+)\)\)\);.+\1\s*=\s*\$\w+[+\-\*]\d+;~msi', $str, $matches);

        $find = $matches[0];
        $obfPHP = $matches[2];
        $matches[4] = Helpers::calc($matches[4]);
        $matches[5] = intval(Helpers::calc($matches[5]));
        $matches[6] = intval(Helpers::calc($matches[6]));

        $func = explode($matches[4], strtolower(substr($obfPHP, $matches[5], $matches[6])));
        $func[1] = strrev($func[1]);
        $func[2] = strrev($func[2]);

        preg_match('~\$\w{1,40}\s=\sexplode\((chr\(\(\d+\-\d+\)\)),\'([^\']+)\'\);~msi', $str, $matches);
        $matches[1] = Helpers::calc($matches[1]);
        $offsets = explode($matches[1], $matches[2]);

        $res = '';
        for ($i = 0; $i < (sizeof($offsets) / 2); $i++) {
            $res .= substr($obfPHP, $offsets[$i * 2], $offsets[($i * 2) + 1]);
        }

        preg_match('~return\s*\$\w{1,40}\((chr\(\(\d+\-\d+\)\)),(chr\(\(\d+\-\d+\)\)),\$\w{1,40}\);~msi', $str, $matches);
        $matches[1] = Helpers::calc($matches[1]);
        $matches[2] = Helpers::calc($matches[2]);

        $res = Helpers::stripsquoteslashes(str_replace($matches[1], $matches[2], $res));
        $res = "<?php\n" . $res . "?>";

        preg_match('~(\$\w{1,40})\s=\simplode\(array_map\(\"[^\"]+\",str_split\(\"(([^\"\\\\]++|\\\\.)*)\"\)\)\);(\$\w{1,40})\s=\s\$\w{1,40}\(\"\",\s\1\);\s\4\(\);~msi', $res, $matches);

        $matches[2] = stripcslashes($matches[2]);
        for ($i=0; $i < strlen($matches[2]); $i++) {
            $matches[2][$i] = chr(ord($matches[2][$i])-1);
        }

        $res = str_replace($matches[0], $matches[2], $res);

        preg_match_all('~(\$\w{1,40})\s*=\s*\"(([^\"\\\\]++|\\\\.)*)\";~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $vars[$match[1]] = stripcslashes($match[2]);
        }

        preg_match_all('~(\$\w{1,40})\s*=\s*\'(([^\'\\\\]++|\\\\.)*)\';~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $vars[$match[1]] = Helpers::stripsquoteslashes($match[2]);
        }

        preg_match('~(\$\w{1,40})\s*=\s*\"\\\\x73\\\\164\\\\x72\\\\137\\\\x72\\\\145\\\\x70\\\\154\\\\x61\\\\143\\\\x65";\s(\$\w{1,40})\s=\s\'(([^\'\\\\]++|\\\\.)*)\';\seval\(\1\(\"(([^\"\\\\]++|\\\\.)*)\",\s\"(([^\"\\\\]++|\\\\.)*)\",\s\2\)\);~msi', $res, $matches);

        $matches[7] = stripcslashes($matches[7]);
        $matches[3] = Helpers::stripsquoteslashes(str_replace($matches[5], $matches[7], $matches[3]));


        $res = str_replace($matches[0], $matches[3], $res);

        preg_match_all('~(\$\w{1,40})\s*=\s*\"(([^\"\\\\]++|\\\\.)*)\";~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $vars[$match[1]] = stripcslashes($match[2]);
        }

        preg_match_all('~(\$\w{1,40})\s*=\s*\'(([^\'\\\\]++|\\\\.)*)\';~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $vars[$match[1]] = Helpers::stripsquoteslashes($match[2]);
        }

        preg_match('~\$\w{1,40}\s=\sarray\(((\'(([^\'\\\\]++|\\\\.)*)\',?(\.(\$\w{1,40})\.)?)+)\);~msi', $res, $matches);

        foreach ($vars as $var => $value) {
            $matches[1] = str_replace("'." . $var . ".'", $value, $matches[1]);
        }

        $array2 = explode("','", substr($matches[1], 1, -1));
        preg_match('~eval\(\$\w{1,40}\(array\((((\"[^\"]\"+),?+)+)\),\s(\$\w{1,40}),\s(\$\w{1,40})\)\);~msi', $res, $matches);

        $array1 = explode('","', substr($matches[1], 1, -1));

        $temp = array_keys($vars);
        $temp = $temp[9];

        $arr = explode('|', $vars[$temp]);
        $off=0;
        $funcs=array();

        for ($i = 0; $i<sizeof($arr); $i++) {
            if ($i == 0) {
                $off = 0;
            } else {
                $off = $arr[$i - 1] + $off;
            }
            $len = $arr[$i];
            $temp = array_keys($vars);
            $temp = $temp[7];

            $funcs[]= substr($vars[$temp], $off, $len);
        }

        for ($i = 0; $i < 5; $i++) {
            if ($i % 2 == 0) {
                $funcs[$i] = strrev($funcs[$i]);
                $g = substr($funcs[$i], strpos($funcs[$i], "9") + 1);
                $g = stripcslashes($g);
                $v = explode(":", substr($funcs[$i], 0, strpos($funcs[$i], "9")));
                for ($j = 0; $j < sizeof($v); $j++) {
                    $q = explode("|", $v[$j]);
                    $g = str_replace($q[0], $q[1], $g);
                }
                $funcs[$i] = $g;
            } else {
                $h = explode("|", strrev($funcs[$i]));
                $d = explode("*", $h[0]);
                $b = $h[1];
                for ($j = 0; $j < sizeof($d); $j++) {
                    $b = str_replace($j, $d[$j], $b);
                }
                $funcs[$i] = $b;
            }
        }
        $temp = array_keys($vars);
        $temp = $temp[8];
        $funcs[] = str_replace('9', ' ', strrev($vars[$temp]));
        $funcs = implode("\n", $funcs);
        preg_match('~\$\w{1,40}\s=\s\'.+?eval\([^;]+;~msi', $res, $matches);
        $res = str_replace($matches[0], $funcs, $res);
        $res = stripcslashes($res);
        $res = str_replace('}//}}', '}}', $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateXoredVar($str)
    {
        $res = $str;
        preg_match('~(\$\w{1,40})\s*=\s*\'(\\\\.|[^\']){0,100}\';\s*\$\w+\s*=\s*\'(\\\\.|[^\']){0,100}\'\^\1;[^)]+\)+;\s*\$\w+\(\);~msi', $str, $matches);
        $find = $matches[0];
        preg_match_all('~(\$\w{1,40})\s*=\s*\'((\\\\.|[^\'])*)\';~msi', $str, $matches, PREG_SET_ORDER);
        $vars = array();
        foreach ($matches as $match) {
            $vars[$match[1]]=$match[2];
        }

        preg_match_all('~(\$\w{1,40})\s*=\s*\'((\\\\.|[^\'])*)\'\^(\$\w+);~msi', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (isset($vars[$match[4]])) {
                $vars[$match[1]]=$match[2]^$vars[$match[4]];
                $res = str_replace($match[0], $match[1] . "='" . $vars[$match[1]] . "';", $res);
            }
        }

        preg_match_all('~(\$\w{1,40})\s*=\s*(\$\w+)\^\'((\\\\.|[^\'])*)\';~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (isset($vars[$match[2]])) {
                $vars[$match[1]]=$match[4]^$vars[$match[2]];
                $res = str_replace($match[0], $match[1] . "='" . $vars[$match[1]] . "';", $res);
            }
        }
        preg_match_all('~\'((\\\\.|[^\'])*)\'\^(\$\w+)~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (isset($vars[$match[3]])) {
                $res = str_replace($match[0], "'" . addcslashes($match[1]^$vars[$match[3]], '\\\'') . "'", $res);
            }
        }
        foreach ($vars as $var => $value) {
            $res = str_replace($var, $value, $res);
            $res = str_replace($value . "='" . $value . "';", '', $res);
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscatePhpMess($str)
    {
        $res = '';
        preg_match('~(\$\w{1,40})=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'([^\']+)\'\);eval\(\1\(gzuncompress\(\2\(\3\)\)\)\);~msi', $str, $matches);
        $res = base64_decode(gzuncompress(base64_decode(base64_decode($matches[4]))));
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscatePregReplaceSample05($str)
    {
        $res = '';
        preg_match('~(\$\w{1,40})\s*=\s*\"([^\"]+)\";\s*\$\w+\s*=\s*\$\w+\(\1,\"([^\"]+)\",\"([^\"]+)\"\);\s*\$\w+\(\"[^\"]+\",\"[^\"]+\",\"\.\"\);~msi', $str, $matches);
        $res = strtr($matches[2], $matches[3], $matches[4]);
        $res = base64_decode($res);
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscatePregReplaceB64($str)
    {
        $res = '';
        preg_match('~(\$\w{1,40})\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\"([^\"]+)\";\s*(\$\w+)\s*=\s*.+?;\s*\2\(\5,\"[^\']+\'\3\'[^\"]+\",\"\.\"\);~msi', $str, $matches);
        $find = $matches[0];
        $res = str_replace($find, base64_decode($matches[4]), $str);
        $res = stripcslashes($res);
        preg_match('~eval\(\${\$\{"GLOBALS"\}\[\"\w+\"\]}\(\${\$\{"GLOBALS"\}\[\"\w+\"]}\(\"([^\"]+)\"\)\)\);~msi', $res, $matches);
        $res = gzuncompress(base64_decode($matches[1]));
        preg_match('~eval\(\$\w+\(\$\w+\("([^"]+)"\)\)\);~msi', $res, $matches);
        $res = gzuncompress(base64_decode($matches[1]));
        preg_match('~eval\(\$\w+\(\$\w+\("([^"]+)"\)\)\);~msi', $res, $matches);
        $res = gzuncompress(base64_decode($matches[1]));
        preg_match_all('~\$(\w+)\s*(\.)?=\s*("[^"]*"|\$\w+);~msi', $res, $matches, PREG_SET_ORDER);
        $var = $matches[0][1];
        $vars = array();
        foreach ($matches as $match) {
            if($match[2]!=='.') {
                $vars[$match[1]] = substr($match[3], 1, -1);
            }
            else {
                $vars[$match[1]] .= $vars[substr($match[3], 1)];
            }
        }
        $res = str_replace("srrKePJUwrMZ", "=", $vars[$var]);
        $res = gzuncompress(base64_decode($res));
        preg_match_all('~function\s*(\w+)\(\$\w+,\$\w+\)\{.+?}\s*};\s*eval\(((\1\(\'(\w+)\',)+)\s*"([\w/\+]+)"\)\)\)\)\)\)\)\);~msi', $res, $matches);
        $decode = array_reverse(explode("',", str_replace($matches[1][0] . "('", '', $matches[2][0])));
        array_shift($decode);
        $arg = $matches[5][0];
        foreach ($decode as $val) {
            $arg = Helpers::someDecoder2($val, $arg);
        }
        $res = $arg;
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateDecoder($str)
    {
        preg_match('~if\(!function_exists\(\"(\w+)\"\)\){function \1\(.+eval\(\1\(\"([^\"]+)\"\)\);~msi', $str, $matches);
        $res = Helpers::someDecoder($matches[2]);
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateGBE($str)
    {
        preg_match('~(\$\w{1,40})=\'([^\']+)\';\1=gzinflate\(base64_decode\(\1\)\);\1=str_replace\(\"__FILE__\",\"\'\$\w+\'\",\1\);eval\(\1\);~msi', $str, $matches);
        $res = str_replace($matches[0], gzinflate(base64_decode($matches[2])), $str);
        return $res;
    }

    private function deobfuscateGBZ($str)
    {
        preg_match('~(\$\w{1,40})\s*=\s*\"riny\(\"\.(\$\w+)\(\"base64_decode\"\);\s*(\$\w+)\s*=\s*\2\(\1\.\'\("([^"]+)"\)\);\'\);\s*\$\w+\(\3\);~msi', $str, $matches);
        $res = str_replace($matches[0], base64_decode(str_rot13($matches[4])), $str);
        return $res;
    }

    private function deobfuscateBitrix($str)
    {
        preg_match('~(\$GLOBALS\[\s*[\'"]_+\w{1,60}[\'"]\s*\])\s*=\s*\s*array\s*\(\s*base64_decode\s*\(.+?((.+?\1\[\d+\]).+?)+[^;]+;(\s*include\(\$_\d+\);)?}?((.+?___\d+\(\d+\))+[^;]+;)?~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $funclist = array();
        $strlist = array();
        $res = preg_replace("|[\"']\s*\.\s*['\"]|smi", '', $res);
        $hangs = 0;
        while (preg_match('~(?:min|max|round)?\(\s*\d+[\.\,\|\s\|+\|\-\|\*\|\/]([\d\s\.\,\+\-\*\/]+)?\)~msi', $res) && $hangs < 15) {
            $res = preg_replace_callback('~(?:min|max|round)?\(\s*\d+[\.\,\|\s\|+\|\-\|\*\|\/]([\d\s\.\,\+\-\*\/]+)?\)~msi', array("Helpers","calc"), $res);
            $hangs++;
        }

        $res = preg_replace_callback(
            '|base64_decode\(["\'](.*?)["\']\)|smi',
            function ($matches) {
                return '"' . base64_decode($matches[1]) . '"';
            },
            $res
        );

        if (preg_match_all('|\$GLOBALS\[[\'"](.+?)[\'"]\]\s*=\s*Array\((.+?)\);|smi', $res, $founds, PREG_SET_ORDER)) {
            foreach ($founds as $found) {
                $varname = $found[1];
                $funclist[$varname] = explode(',', $found[2]);
                $funclist[$varname] = array_map(function ($value) {
                    return trim($value, "'\"");
                }, $funclist[$varname]);

                $res = preg_replace_callback(
                    '|\$GLOBALS\[[\'"]' . $varname . '[\'"]\]\[(\d+)\]|smi',
                    function ($matches) use ($varname, $funclist) {
                        return str_replace(array('"',"'"), '', $funclist[$varname][$matches[1]]);
                    },
                    $res
                );
                $res = str_replace($found[0], '', $res);
            }
        }

        if (preg_match_all('~function\s*(\w{1,60})\(\$\w+\){\$\w{1,60}\s*=\s*Array\((.{1,30000}?)\);\s*return\s*base64_decode[^}]+}~msi', $res, $founds, PREG_SET_ORDER)) {
            foreach ($founds as $found) {
                $strlist = explode(',', $found[2]);
                $res = preg_replace_callback(
                    '|' . $found[1] . '\((\d+)\)|smi',
                    function ($matches) use ($strlist) {
                        return "'" . base64_decode($strlist[$matches[1]]) . "'";
                    },
                    $res
                );
                $res = str_replace($found[0], '', $res);
            }
        }

        if (preg_match_all('~\s*function\s*(_+(.{1,60}?))\(\$[_0-9]+\)\s*\{\s*static\s*\$([_0-9]+)\s*=\s*(true|false);.{1,30000}?\$\3\s*=\s*array\((.*?)\);\s*return\s*base64_decode\(\$\3~smi', $res, $founds, PREG_SET_ORDER)) {
            foreach ($founds as $found) {
                $strlist = explode('",', $found[5]);
                $strlist = implode("',", $strlist);
                $strlist = explode("',", $strlist);
                $res = preg_replace_callback(
                    '|' . $found[1] . '\((\d+(\.\d+)?)\)|sm',
                    function ($matches) use ($strlist) {
                        return $strlist[$matches[1]] . '"';
                    },
                    $res
                );
            }
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateLockIt($str)
    {
        preg_match('~\$[O0]*=urldecode\(\'[%a-f0-9]+\'\);(\$(GLOBALS\[\')?[O0]*(\'\])?=(\d+);)?\s*(\$(GLOBALS\[\')?[O0]*(\'\])?\.?=(\$(GLOBALS\[\')?[O0]*(\'\])?([\{\[]\d+[\}\]])?\.?)+;)+[^\?]+\?\>[\s\w\~\=\/\+\\\\\^\{]+~msi', $str, $matches);
        $find = $matches[0];
        $obfPHP        = $str;
        $phpcode       = base64_decode(Helpers::getTextInsideQuotes(Helpers::getEvalCode($obfPHP)));
        $hexvalues     = Helpers::getHexValues($phpcode);
        $tmp_point     = Helpers::getHexValues($obfPHP);

        if (isset($tmp_point[0]) && $tmp_point[0]!=='') {
            $pointer1 = hexdec($tmp_point[0]);
        }
        if (isset($matches[4]) && $matches[4]!=='') {
            $pointer1 = $matches[4];
        }

        $needles       = Helpers::getNeedles($phpcode);
        if ($needles[2]=='__FILE__') {
            $needle        = $needles[0];
            $before_needle = $needles[1];
            preg_match('~\$_F=__FILE__;\$_X=\'([^\']+)\';\s*eval\s*\(\s*\$?\w{1,60}\s*\(\s*[\'"][^\'"]+[\'"]\s*\)\s*\)\s*;~msi', $str, $matches);
            $res = base64_decode($matches[1]);
            $phpcode = strtr($res, $needle, $before_needle);
        } else {
            $needle        = $needles[count($needles) - 2];
            $before_needle = end($needles);
            if (preg_match('~\$\w{1,40}\s*=\s*__FILE__;\s*\$\w{1,40}\s*=\s*([\da-fx]+);\s*eval\s*\(\$?\w+\s*\([\'"][^\'"]+[\'"]\)\);\s*return\s*;\s*\?>(.+)~msi', $str, $matches)) {
                $pointer1 = $matches[1];
                if (strpos($pointer1, '0x')!==false) {
                    $pointer1 = hexdec($pointer1);
                }
            }
            $temp = strtr($obfPHP, $needle, $before_needle);
            $end = 8;
            for ($i = strlen($temp) - 1; $i > strlen($temp) - 15; $i--) {
                if ($temp[$i] == '=') {
                    $end = strlen($temp) - 1 - $i;
                }
            }
            $phpcode = base64_decode(substr($temp, strlen($temp) - $pointer1 - $end, $pointer1));
        }
        $phpcode = str_replace($find, $phpcode, $str);
        return $phpcode;
    }

    private function deobfuscateB64inHTML($str)
    {
        $obfPHP        = $str;
        $phpcode       = base64_decode(Helpers::getTextInsideQuotes(Helpers::getEvalCode($obfPHP)));
        $needles       = Helpers::getNeedles($phpcode);
        $needle        = $needles[count($needles) - 2];
        $before_needle = end($needles);
        if (preg_match('~\$\w{1,40}\s*=\s*(__FILE__|__LINE__);\s*\$\w{1,40}\s*=\s*(\d+);\s*eval(\s*\()+\$?\w+\s*\([\'"][^\'"]+[\'"](\s*\))+;\s*return\s*;\s*\?>(.+)~msi', $str, $matches)) {
            $pointer1 = $matches[2];
        }
        $temp = strtr($obfPHP, $needle, $before_needle);
        $end = 8;
        for ($i = strlen($temp) - 1; $i > strlen($temp) - 15; $i--) {
            if ($temp[$i] == '=') {
                $end = strlen($temp) - 1 - $i;
            }
        }

        $phpcode = base64_decode(substr($temp, strlen($temp) - $pointer1 - ($end-1), $pointer1));
        $phpcode = str_replace($matches[0], $phpcode, $str);
        return $phpcode;
    }

    private function deobfuscateStrtrFread($str)
    {
        preg_match('~\$[O0]+=\(base64_decode\(strtr\(fread\(\$[O0]+,(\d+)\),\'([^\']+)\',\'([^\']+)\'\)\)\);eval\([^\)]+\)+;~msi', $str, $layer2);
        $str = explode('?>', $str);
        $str = end($str);
        $res = substr($str, $layer2[1], strlen($str));
        $res = base64_decode(strtr($res, $layer2[2], $layer2[3]));
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateStrtrBase64($str)
    {
        preg_match('~(\$\w{1,40})="([\w\]\[\<\&\*\_+=/]{300,})";\$\w+=\$\w+\(\1,"([\w\]\[\<\&\*\_+=/]+)","([\w\]\[\<\&\*\_+=/]+)"\);~msi', $str, $matches);
        $str = strtr($matches[2], $matches[3], $matches[4]);
        $res = base64_decode($str);
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateByteRun($str)
    {
        preg_match('~\$_F=__FILE__;\$_X=\'([^\']+)\';\s*eval\s*\(\s*\$?\w{1,60}\s*\(\s*[\'"][^\'"]+[\'"]\s*\)\s*\)\s*;~msi', $str, $matches);
        $res = base64_decode($matches[1]);
        $res = strtr($res, '123456aouie', 'aouie123456');
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateExplodeSubst($str)
    {
        preg_match('~\$\w+\s*=\s*array\((\'[^\']+\',?)+\);\s*.+?(\$_\w{1,40}\[\w+\])\s*=\s*explode\(\'([^\']+)\',\s*\'([^\']+)\'\);.+?(\2\[[a-fx\d]+\])\(\);(.+?\2)+.+}~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        preg_match_all('~function ([\w_]+)\(~msi', $res, $funcs);
        preg_match('~(\$_\w+\[\w+\])\s*=\s*explode\(\'([^\']+)\',\s*\'([^\']+)\'\);.+?(\1\[[a-fx\d]+\])\(\);~msi', $res, $matches);
        $subst_array = explode($matches[2], $matches[3]);
        $subst_var = $matches[1];
        $res = preg_replace_callback('~((\$_GET\[[O0]+\])|(\$[O0]+))\[([a-fx\d]+)\]~msi', function ($matches) use ($subst_array, $funcs) {
            if (function_exists($subst_array[hexdec($matches[4])]) || in_array($subst_array[hexdec($matches[4])], $funcs[1])) {
                return $subst_array[hexdec($matches[4])];
            } else {
                return "'" . $subst_array[hexdec($matches[4])] . "'";
            }
        }, $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateSubst($str)
    {
        preg_match('~(\$[\w{1,40}]+)\s*=\s*\'([\w+%=\-\#\\\\\'\*]+)\';(\$[\w+]+)\s*=\s*Array\(\);(\3\[\]\s*=\s*(\1\[\d+\]\.?)+;+)+(.+\3)[^}]+}~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $alph = stripcslashes($matches[2]);
        $funcs = $matches[4];

        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($matches[1] . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[1] . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);
        $var = $matches[3];

        preg_match_all('~\\' . $var . '\[\]\s*=\s*\'([\w\*\-\#]+)\'~msi', $res, $matches);

        for ($i = 0; $i <= count($matches[1]); $i++) {
            if (@function_exists($matches[1][$i])) {
                $res = str_replace($var . '[' . $i . ']', $matches[1][$i], $res);
            } else {
                $res = @str_replace($var . '[' . $i . ']', "'" . $matches[1][$i] . "'", $res);
            }
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateUrldecode($str)
    {
        preg_match('~(\$\w+=\'[^\']+\';\s*)+(\$[\w{1,40}]+)=(urldecode|base64_decode){0,1}\(?[\'"]([\w+%=-]+)[\'"]\)?;(\$[\w+]+=(\$(\w+\[\')?[O_0]*(\'\])?([\{\[]\d+[\}\]])?\.?)+;)+[^\?]+(\?\>[\w\~\=\/\+]+|.+\\\\x[^;]+;)~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $res = stripcslashes($res);
        if ($matches[3] == "urldecode") {
            $alph = urldecode($matches[4]);
            $res = str_replace('urldecode(\'' . $matches[4] . '\')', "'" . $alph . "'", $res);
        } elseif ($matches[3] == 'base64_decode') {
            $alph = base64_decode($matches[4]);
            $res = str_replace('base64_decode(\'' . $matches[4] . '\')', "'" . $alph . "'", $res);
        } else {
            $alph = $matches[4];
        }

        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($matches[2] . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[2] . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[2] . '{' . $i . '}.', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[2] . '{' . $i . '}', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);

        preg_match_all('~\$(\w+)\s*=\s*\'([\w\*\-\#]+)\'~msi', $res, $matches, PREG_SET_ORDER);
        for ($i = 0; $i < count($matches); $i++) {
            if (@function_exists($matches[$i][2])) {
                $res = str_replace('$' . $matches[$i][1], $matches[$i][2], $res);
                $res = str_replace('${"GLOBALS"}["' . $matches[$i][1] . '"]', $matches[$i][2], $res);
            } else {
                $res = str_replace('$' . $matches[$i][1], "'" . $matches[$i][2] . "'", $res);
                $res = str_replace('${"GLOBALS"}["' . $matches[$i][1] . '"]', "'" . $matches[$i][2] . "'", $res);
            }
            $res = str_replace("'" . $matches[$i][2] . "'='" . $matches[$i][2] . "';", '', $res);
            $res = str_replace($matches[$i][2] . "='" . $matches[$i][2] . "';", '', $res);
            $res = str_replace($matches[$i][2] . "=" . $matches[$i][2] . ';', '', $res);
        }
        $res = Helpers::replaceCreateFunction($res);
        preg_match('~\$([0_O]+)\s*=\s*function\s*\((\$\w+)\)\s*\{\s*\$[O_0]+\s*=\s*substr\s*\(\2,(\d+),(\d+)\);\s*\$[O_0]+\s*=\s*substr\s*\(\2,([\d-]+)\);\s*\$[O_0]+\s*=\s*substr\s*\(\2,(\d+),strlen\s*\(\2\)-(\d+)\);\s*return\s*gzinflate\s*\(base64_decode\s*\(\$[O_0]+\s*\.\s*\$[O_0]+\s*\.\s*\$[O_0]+\)+;~msi', $res, $matches);
        $res = preg_replace_callback('~\$\{"GLOBALS"}\["' . $matches[1] . '"\]\s*\(\'([^\']+)\'\)~msi', function ($calls) use ($matches) {
            $temp1 = substr($calls[1], $matches[3], $matches[4]);
            $temp2 = substr($calls[1], $matches[5]);
            $temp3 = substr($calls[1], $matches[6],strlen($calls[1]) - $matches[7]);
            return "'" . gzinflate(base64_decode($temp1 . $temp3 . $temp2)) . "'";
        }, $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    public function unwrapFuncs($string, $level = 0)
    {
        $close_tag = false;
        $res = '';

        if (trim($string) == '') {
            return '';
        }
        if ($level > 100) {
            return '';
        }

        if ((($string[0] == '\'') || ($string[0] == '"')) && (substr($string, 1, 2) != '?>')) {
            if($string[0] == '"' && preg_match('~\\\\x\d+~', $string)) {
                return stripcslashes($string);
            } else {
                return substr($string, 1, -2);
            }
        } elseif ($string[0] == '$') {
            preg_match('~\$\w{1,40}~', $string, $string);
            $string = $string[0];
            $matches = array();
            if (!@preg_match_all('~\\' . $string . '\s*=\s*(("([^;"\\\]+)(\\\)?)+");~msi', $this->full_source, $matches)) {
                @preg_match_all('~\\' . $string . '\s*=\s*((\'([^;\'\\\]+)(\\\)?)+\');~msi', $this->full_source, $matches);
                $str = @$matches[1][0];
            } else {
                $str = $matches[1][0];
            }
            $this->cur = str_replace($matches[0][0], '', $this->cur);
            $this->text = str_replace($matches[0][0], '', $this->text);
            return substr($str, 1, -1);
        } else {
            $pos      = strpos($string, '(');
            $function = substr($string, 0, $pos);
            $arg      = $this->unwrapFuncs(substr($string, $pos + 1), $level + 1);
            if (strpos($function, '?>') !== false) {
                $function = str_replace("'?>'.", "", $function);
                $function = str_replace('"?>".', "", $function);
                $function = str_replace("'?>' .", "", $function);
                $function = str_replace('"?>" .', "", $function);
                $close_tag = true;
            }
            $function = str_replace(array('@',' '), '', $function);
            if (strtolower($function) == 'base64_decode') {
                $res = @base64_decode($arg);
            } elseif (strtolower($function) == 'gzinflate') {
                $res = @gzinflate($arg);
            } elseif (strtolower($function) == 'gzuncompress') {
                $res = @gzuncompress($arg);
            } elseif (strtolower($function) == 'strrev') {
                $res = @strrev($arg);
            } elseif (strtolower($function) == 'str_rot13') {
                $res = @str_rot13($arg);
            } elseif (strtolower($function) == 'urldecode') {
                $res = @urldecode($arg);
            } elseif (strtolower($function) == 'rawurldecode') {
                $res = @rawurldecode($arg);
            } elseif (strtolower($function) == 'stripslashes') {
                $res = @stripslashes($arg);
            } elseif (strtolower($function) == 'htmlspecialchars_decode') {
                $res = @htmlspecialchars_decode($arg);
            } elseif (strtolower($function) == 'convert_uudecode') {
                $res = @convert_uudecode($arg);
            } else {
                $res = $arg;
            }
            if ($close_tag) {
                $res = "?> " . $res;
                $close_tag = false;
            }
            return $res;
        }
    }

    private function deobfuscateEvalFunc($str)
    {
        $res = $str;
        $res = stripcslashes($res);
        preg_match('~function\s*(\w{1,40})\((\$\w{1,40})\)\s*\{\s*(\$\w{1,40})\s*=\s*\"base64_decode\";\s*(\$\w{1,40})\s*=\s*\"gzinflate\";\s*return\s*\4\(\3\(\2\)\);\s*\}\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*eval\(\1\(\"([^\"]*)\"\)\);~msi', $res, $matches);
        $res = gzinflate(base64_decode($matches[5]));
        $res = str_replace($str, $res, $str);
        return $res;
    }

    private function deobfuscateEvalHex($str)
    {
        preg_match('~eval\s*\("(\\\\x?\d+[^"]+)"\);~msi', $str, $matches);
        $res = stripcslashes($matches[1]);
        $res = str_replace($matches[1], $res, $res);
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateEvalVar($str)
    {
        preg_match('~((\$\w+)\s*=\s*[\'"]([^\'"]+)[\'"];)\s*.{0,10}?@?eval\s*\((base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\()+(\({0,1}\2\){0,1})\)+;~msi', $str, $matches);
        $string = str_replace($matches[1], '', $matches[0]);
        $text = "'" . addcslashes(stripcslashes($matches[3]), "\\'") . "'";
        $string = str_replace($matches[5], $text, $string);
        $res = str_replace($matches[0], $string, $str);
        return $res;
    }

    private function deobfuscateEval($str)
    {
        $res = $str;
        if (preg_match('~(preg_replace\(["\']/\.\*?/[^"\']+\"\s*,\s*)[^\),]+(?:\)+;[\'"])?(,\s*["\'][^"\']+["\'])\)+;~msi', $res, $matches)) {
            $res = str_replace($matches[1], 'eval(', $res);
            $res = str_replace($matches[2], '', $res);
            return $res;
        }

        if (preg_match('~((\$\w+)\s*=\s*create_function\(\'\',\s*)[^\)]+\)+;\s*(\2\(\);)~msi', $res, $matches)) {
            $res = str_replace($matches[1], 'eval(', $res);
            $res = str_replace($matches[3], '', $res);
            return $res;
        }

        if (preg_match('~eval\s*/\*[\w\s\.:,]+\*/\s*\(~msi', $res, $matches)) {
            $res = str_replace($matches[0], 'eval(', $res);
            return $res;
        }

        preg_match('~@?eval\s*\(\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|convert_uudecode\s*\(|htmlspecialchars_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+.*?[^\'")]+((\s*\.?[\'"]([^\'";]+\s*)+)?\s*[\'"\);]+)+~msi', $res, $matches);
        $string = $matches[0];
        if (preg_match('~\$_(POST|GET|REQUEST|COOKIE)~ms', $res)) {
            return $res;
        }
        $string = substr($string, 5, strlen($string) - 7);
        $res = $this->unwrapFuncs($string);
        $res = str_replace($str, $res, $str);
        return $res;
    }

    private function deobfuscateEcho($str)
    {
        $res = $str;
        preg_match('~@?echo\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+.*?[^\'")]+((\s*\.?[\'"]([^\'";]+\s*)+)?\s*[\'"\);]+)+~msi', $res, $matches);
        $string = $matches[0];
        if (preg_match('~\$_(POST|GET|REQUEST|COOKIE)~ms', $res)) {
            return $res;
        }
        $string = substr($string, 5, strlen($string) - 7);
        $res = $this->unwrapFuncs($string);
        $res = str_replace($str, $res, $str);
        return $res;
    }

    private function deobfuscateFOPO($str)
    {
        preg_match('~(\$\w{1,40})\s*=\s*\"(\\\\142|\\\\x62)[0-9a-fx\\\\]+";\s*@?eval\s*\(\1\s*\([^\)]+\)+\s*;~msi', $str, $matches);
        $phpcode = Helpers::formatPHP($str);
        $phpcode = base64_decode(Helpers::getTextInsideQuotes(Helpers::getEvalCode($phpcode)));
        if (strpos($phpcode, 'eval') !== false) {
            preg_match_all('~\$\w+\(\$\w+\(\$\w+\("[^"]+"\)+~msi', $phpcode, $matches2);
            @$phpcode = gzinflate(base64_decode(str_rot13(Helpers::getTextInsideQuotes(end(end($matches2))))));
            $old = '';
            $hangs = 0;
            while (($old != $phpcode) && (strlen(strstr($phpcode, 'eval($')) > 0) && $hangs < 30) {
                $old = $phpcode;
                $funcs = explode(';', $phpcode);
                if (count($funcs) == 5) {
                    $phpcode = gzinflate(base64_decode(str_rot13(Helpers::getTextInsideQuotes(Helpers::getEvalCode($phpcode)))));
                } elseif (count($funcs) == 4) {
                    $phpcode = gzinflate(base64_decode(Helpers::getTextInsideQuotes(Helpers::getEvalCode($phpcode))));
                }
                $hangs++;
            }
        }
        $res = str_replace($matches[0], substr($phpcode, 2), $str);
        return $res;
    }

    private function deobfuscateFakeIonCube($str)
    {
        $subst_value = 0;
        preg_match('~if\s*\(\!extension_loaded\(\'IonCube_loader\'\)\).+pack\(\"H\*\",\s*\$__ln\(\"/\[A-Z,\\\\r,\\\\n\]/\",\s*\"\",\s*substr\(\$__lp,\s*([0-9a-fx]+\-[0-9a-fx]+)\)\)\)[^\?]+\?\>\s*[0-9a-z\r\n]+~msi', $str, $matches);
        $matches[1] = Helpers::calc($matches[1]);
        $subst_value = intval($matches[1])-21;
        $code = @pack("H*", preg_replace("/[A-Z,\r,\n]/", "", substr($str, $subst_value)));
        $res = str_replace($matches[0], $code, $str);
        return $res;
    }

    private function deobfuscateCobra($str)
    {
        preg_match('~explode\(\"\*\*\*\",\s*\$\w+\);\s*eval\(eval\(\"return strrev\(base64_decode\([^\)]+\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $res = preg_replace_callback(
            '~eval\(\"return strrev\(base64_decode\(\'([^\']+)\'\)\);\"\)~msi',
            function ($matches) {
                return strrev(base64_decode($matches[1]));
            },
            $res
        );

        $res = preg_replace_callback(
            '~eval\(gzinflate\(base64_decode\(\.\"\'([^\']+)\'\)\)\)\;~msi',
            function ($matches) {
                return gzinflate(base64_decode($matches[1]));
            },
            $res
        );

        preg_match('~(\$\w{1,40})\s*=\s*\"([^\"]+)\"\;\s*\1\s*=\s*explode\(\"([^\"]+)\",\s*\s*\1\);~msi', $res, $matches);
        $var = $matches[1];
        $decrypt = base64_decode(current(explode($matches[3], $matches[2])));
        $decrypt = preg_replace_callback(
            '~eval\(\"return strrev\(base64_decode\(\'([^\']+)\'\)\);\"\)~msi',
            function ($matches) {
                return strrev(base64_decode($matches[1]));
            },
            $decrypt
        );

        $decrypt = preg_replace_callback(
            '~eval\(gzinflate\(base64_decode\(\.\"\'([^\']+)\'\)\)\)\;~msi',
            function ($matches) {
                return gzinflate(base64_decode($matches[1]));
            },
            $decrypt
        );

        preg_match('~if\(\!function_exists\(\"(\w+)\"\)\)\s*\{\s*function\s*\1\(\$string\)\s*\{\s*\$string\s*=\s*base64_decode\(\$string\)\;\s*\$key\s*=\s*\"(\w+)\"\;~msi', $decrypt, $matches);

        $decrypt_func = $matches[1];
        $xor_key = $matches[2];

        $res = preg_replace_callback(
            '~\\' . $var . '\s*=\s*.*?eval\(' . $decrypt_func . '\(\"([^\"]+)\"\)\)\;\"\)\;~msi',
            function ($matches) use ($xor_key) {
                $string = base64_decode($matches[1]);
                $key = $xor_key;
                $xor = "";
                for ($i = 0; $i < strlen($string);) {
                    for ($j = 0; $j < strlen($key); $j++,$i++) {
                        if (isset($string{$i})) {
                            $xor .= $string{$i} ^ $key{$j};
                        }
                    }
                }
                return $xor;
            },
            $res
        );
        $res = str_replace($find, $res, $str);
        return $res;
    }
}


/**
 * Class Factory.
 */
class Factory
{
    /**
     * @var Factory
     */
    private static $instance;
    /**
     * @var array
     */
    private static $config;

    /**
     * Factory constructor.
     *
     * @throws Exception
     */
    private function __construct()
    {

    }

    /**
     * Instantiate and return a factory.
     *
     * @return Factory
     * @throws Exception
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Configure a factory.
     *
     * This method can be called only once.
     *
     * @param array $config
     * @throws Exception
     */
    public static function configure($config = [])
    {
        if (self::isConfigured()) {
            throw new Exception('The Factory::configure() method can be called only once.');
        }

        self::$config = $config;
    }

    /**
     * Return whether a factory is configured or not.
     *
     * @return bool
     */
    public static function isConfigured()
    {
        return self::$config !== null;
    }

    /**
     * Creates and returns an instance of a particular class.
     *
     * @param string $class
     *
     * @param array $constructorArgs
     * @return mixed
     * @throws Exception
     */
    public function create($class, $constructorArgs = [])
    {
        if (!isset(self::$config[$class])) {
            throw new Exception("The factory is not contains configuration for '{$class}'.");
        }

        if (is_callable(self::$config[$class])) {
            return call_user_func(self::$config[$class], $constructorArgs);
        } else {
            return new self::$config[$class](...$constructorArgs);
        }
    }
}
