<?
$lang["page_title"] = "Настройки";

$lang["title_grab_text_save_images"] = "Сохранять картинки";
$lang["title_grab_text_imagesize_limit"] = "Минимальный и максимальный размеры картинок (width_min:height_min:width_max:height_max)";
$lang["title_grab_text_show_source"] = "Показывать хост-источник";
$lang["title_grab_text_use_proxy"] = "Использовать прокси для получения текста новости";
$lang["title_grab_manual_moderate"] = "Ручная модерация награбленного";
$lang["title_manual_show_description"] = "Показывать описания в адм. списке новостей";

$lang["title_grab_manual_email_count"] = "Через сколько новостей посылать уведомление (0 - не отправлять)";
$lang["title_grab_manual_email_addres"] = "Адрес, куда посылать письма о награбленном";
$lang["title_grab_manual_email_message"] = "Текст письма";
$lang["title_grab_use_cron"] = "Кол-во уникальных посетителей для обновления лент<br>(0 - работа через крон)";
$lang["title_grab_text_first_chars_use"] = "Сколько символов из текста использовать, если нет дескрипшена (0 - отключено)"; //, <br>XXX - кол-во от начала, <br>'text[:+][:XXX]' - берем все до text, + - включая text, XXX - но не более кол-ва символов
$lang["title_grab_text_get_full"] = "Получать полный текст";
$lang["title_grab_links_encode"] = "Кодировать ссылки в тексте с помощью JavaScript";
$lang["title_grab_links_nofollow"] = "Добавлять в ссылки rel=&quot;nofollow&quot;";
$lang["title_grab_links_open_in_blank"] = "Открывать ссылки из текста новости в новом окне";
$lang["title_user_lang"] = "Язык для консоли администратора";
$lang["title_shingle_check"] = "Проверка на дубликаты для новых текстов, оставлять с совпадением не более";
$lang["title_grab_shingles_days"] = "Сколько дней хранить шинглы (0 - не удалять)";
$lang["title_news_title_limit"] = "Лимит символов в заголовке новости (обезается до целого слова, 0 - откл.)";
$lang["title_rss_mogrify_path"] = "Уменьшать картинки используя mogrify. Укажите полный путь к модулю.<br>(если задан и существует - GD не используется)";
$lang["title_rss_usegd"] = "Уменьшать картинки используя GD. Изображения в формате GIF не сохраняются. Использование GIF с GD возможно только при преобразовании размера &quot;на лету&quot;; Для уменьшения &quot;на лету&quot; галочку не ставить (уменьшить &quot;на лету&quot;: &#123;HTTP_ROOT&#125;get_img.php?img=[name]&size=[XXX:YYY])";
$lang["title_rss_sizes"] = "Создавать картинки с размерами (указывать XXX: - изменение по ширине, :YYY - изменение по высоте, например &quot;:100,250:,150:200&quot;), название картинки будет: prw_XXXxYYY_of_name.ext";

$lang["title_news_mainblock_enabled"] = "Показывать блок главных новостей при просмотре новости";
$lang["title_news_lastblock_enabled"] = "Показывать блок последних новостей при просмотре новости";
$lang["title_news_fields"] = "Поля новости при просмотре";
$lang["title_news_fields_date"] = "дата";
$lang["title_news_fields_time"] = "время";
$lang["title_news_fields_header"] = "заголовок";
$lang["title_news_fields_description"] = "описание";
$lang["title_news_fields_image"] = "картинка";
$lang["title_news_fields_source"] = "источник";
$lang["title_news_fields_group"] = "категория";
$lang["title_news_fields_subgroup"] = "подкатегория";
$lang["title_news_fields_text"] = "текст";
$lang["title_comments_enabled"] = "Комментирование включено";
$lang["title_comments_use_captcha"] = "Использовать цифровой код";
$lang["title_groups_showall"] = "Показывать все новости группы, если нет подгруппы";

$lang["title_rss_count"] = "кол-во новостей";
$lang["title_rss_image_show"] = "выводить изображения";
$lang["title_rss_title"] = "заголовок ленты";
$lang["title_rss_description"] = "описание ленты";
$lang["title_rss_lang"] = "язык ленты (ru, en...)";
$lang["title_rss_copyright"] = "copyright ленты";
$lang["title_rss_editormail"] = "email редактора ленты";
$lang["title_rss_category"] = "категория ленты";
$lang["title_rss_generator"] = "название генератора ленты";
$lang["title_rss_imageurl"] = "изображение ленты";
$lang["title_rss_imagelink"] = "ссылка изображения ленты";
$lang["title_rss_imagetitle"] = "title изображения ленты";

$lang["error_rss_imageurl_load"] = "Ошибка загрузки файла";
$lang["error_rss_imageurl_save"] = "Ошибка сохранения файла (проверьте наличие папки ".$DOCUMENT_ROOT."/img и права записи в нее)";
$lang["error_rss_imageurl_delete"] = "Файл не был удален, проверьте доступ";

$lang["title_news_use_translate"] = "Использовать символьное преобразование URL новости (trans title)";
$lang["title_transtitle_maxlength"] = "Максимальная длина trans title";

$lang["header_get_news"] = "Получение новостей";
$lang["header_show_news"] = "Отображение новостей";
$lang["header_other"] = "Разное";

?>