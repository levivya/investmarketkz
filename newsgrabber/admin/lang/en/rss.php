<?php
$lang["page_title"] = "RSS sources";

$lang["title_rss_id"] = "ID";
$lang["title_rss_title"] = "Title";
$lang["title_rss_url"] = "URL";
$lang["title_groups_id"] = "Group";
$lang["title_rss_last_update"] = "Last update";
$lang["title_rss_active"] = "Active";
$lang["title_rss_fields"] = "Linking fields of RSS-source";
$lang["title_rss_striptags_description"] = "Remove tags (description)";
$lang["title_rss_striptags_text"] = "Remove tags (text)";
$lang["title_rss_recivetext"] = "Recive news content";
$lang["title_rss_reciveimages"] = "Recive images";
$lang["title_rss_texttemplate"] = "Template of news text";
$lang["title_rss_uniq_id"] = "Identifier of news page";
$lang["title_rss_print_page"] = "Print page";
$lang["title_rss_print_link_from"] = "Что заменять в URL для печатной версии";
$lang["title_rss_print_link_to"] = "На что заменять в URL для печатной версии";
$lang["title_rss_titletemplate"] = "Шаблон заголовков<br>Если задан, лента интерпретируется как html, а не rss";
$lang["title_rss_interval"] = "Интервал обновления (минут)";
$lang["title_rss_ignore_global_manual"] = "Игнорировать глобальную настройку ручной модерации";
$lang["title_rss_replacement"] = "Замены через |";
$lang["title_rss_reciveswf"] = "Загружать swf";

$lang["title_news_title"] = "News title";
$lang["title_news_description"] = "News description";
$lang["title_news_text"] = "News text";
$lang["title_news_link"] = "News link";

$lang["caption_rss_changeto"] = "change to";
$lang["caption_rss_except"] = "except";
$lang["caption_rss_except1"] = "specify successively, for example";
$lang["caption_rss_print_page"] = "С таких страниц легче шаблоны делать, а также иногда это решает проблему с парсингом новостей в оригинале которых встречается многостраничный вывод.

Подмена происходит с помощью замены части URL в исходном адресе новости (regexp). Если в адресе встречаются символы []!-.?*\() их необходимо экранировать слэшом (\).

Пример замены ссылки:
http://www.host.com/articles/article/289/
на
http://www.host.com/articles/printmode.html?id=289

что надо заменить: /article/([^/]+)/
на что надо заменить: /printmode.html?id=\1";

$lang["caption_rss_texttemplate"] = "<br><br>Используются блоки:<br>
   1. &#123;skip&#125; - пропустить<br>
   2. &#123;get&#125; - получить, все полученные части соединяются.";

$lang["caption_newsurl"] = "News URL";

$lang["caption_rss_uniq_id"] = "Используются только если текст получается через прокси для однозначной идентификации страницы новости. В это поле вставляется часть HTML кода которая присутствует во всех новостях. Например тэг картинки с логотипом сайта.";

$lang["caption_rss_titletemplate"] = "Возможные поля:<br>
   1. &#123;skip&#125; - пропусть, &lt;a href=\"&#123;skip&#125;\"&gt;... - содержимое кавычек ингнорируется<br>
   2. &#123;title&#125; - заголовок<br>
   3. &#123;description&#125; - описание<br>
   4. &#123;link&#125; - ссылка<br>
   5. &#123;text&#125; - текст<br>";

$lang["button_check_template"] = "Check template";
$lang["button_rss_update"] = "Update";

$lang["errors_rss_open"] = "Ошибка открытия rss-ленты. Сохранение приведет к сбросу настроек.";
$lang["errors_rss_fields_url"] = "Enter URL!";
$lang["errors_rss_htmlopen"] = "Ошибка открытия ссылки или ошибка 404";

?>