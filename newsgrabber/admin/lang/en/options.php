<?
$lang["page_title"] = "Options";

$lang["title_grab_text_save_images"] = "Save images";
$lang["title_grab_text_imagesize_limit"] = "Min and max image size (width_min:height_min:width_max:height_max)";
$lang["title_grab_text_show_source"] = "Show host-source";
$lang["title_grab_text_use_proxy"] = "Use proxy";
$lang["title_grab_manual_moderate"] = "Manual moderate news";
$lang["title_manual_show_description"] = "Показывать описания в адм. списке новостей";
$lang["title_grab_manual_email_count"] = "Through how many news to send the notice (0 - no send)";
$lang["title_grab_manual_email_addres"] = "The e-mail where to send letters about stolen";
$lang["title_grab_manual_email_message"] = "The text of the e-mail";
$lang["title_grab_use_cron"] = "Кол-во уникальных посетителей для обновления лент<br>(0 - работа через крон)";
$lang["title_grab_text_first_chars_use"] = "How many symbols from the text to use, if there is no description (0 - disabled)"; //, <br>XXX - кол-во от начала, <br>'text[:+][:XXX]' - берем все до text, + - включая text, XXX - но не более кол-ва символов)
$lang["title_grab_text_get_full"] = "Get full text";
$lang["title_grab_links_encode"] = "Encode links in the text";
$lang["title_grab_links_nofollow"] = "Добавлять в ссылки rel=&quot;nofollow&quot;";
$lang["title_grab_links_open_in_blank"] = "Open links in a new window";
$lang["title_user_lang"] = "Language";
$lang["title_shingle_check"] = "Use shingle check for text, совпадение не более";
$lang["title_grab_shingles_days"] = "Сколько дней хранить шинглы (0 - не удалять)";
$lang["title_news_title_limit"] = "Лимит символов в заголовке новости (обезается до целого слова, 0 - откл.)";
$lang["title_rss_mogrify_path"] = "Уменьшать картинки используя mogrify. Укажите полный путь к модулю.<br>(если задан и существует - GD не используется)";
$lang["title_rss_usegd"] = "Уменьшать картинки используя GD (GIF не сохраняется - только &quot;на лету&quot;; не отмечать для уменьшения &quot;на лету&quot;)<br>(возможно уменьшать &quot;на лету&quot;: &#123;HTTP_ROOT&#125;get_img.php?img=[name]&size=[XXX:YYY])";
$lang["title_rss_sizes"] = "Создавать картинки с размерами (указывать XXX: - изменение по ширине, :YYY - изменение по высоте, например &quot;:100,250:,150:200&quot;), название картинки будет: prw_XXXxYYY_of_name.ext";

$lang["title_news_mainblock_enabled"] = "Показывать блок главных новостей при просмотре новости";
$lang["title_news_lastblock_enabled"] = "Показывать блок последних новостей при просмотре новости";
$lang["title_news_fields"] = "Поля новости при просмотре";
$lang["title_news_fields_date"] = "date";
$lang["title_news_fields_time"] = "time";
$lang["title_news_fields_header"] = "title";
$lang["title_news_fields_description"] = "description";
$lang["title_news_fields_image"] = "image";
$lang["title_news_fields_source"] = "source";
$lang["title_news_fields_group"] = "group";
$lang["title_news_fields_subgroup"] = "subgroup";
$lang["title_news_fields_text"] = "text";
$lang["title_comments_enabled"] = "Comments enabled";
$lang["title_comments_use_captcha"] = "Use captcha";
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

$lang["title_news_use_translate"] = "Use translit in news URL";
$lang["title_transtitle_maxlength"] = "Max length translit title";

$lang["header_get_news"] = "Getting news";
$lang["header_show_news"] = "Отображение новостей";
$lang["header_other"] = "Other";

?>