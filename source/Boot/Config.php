<?php

// Configuração base; valores podem ser sobrescritos por variáveis de ambiente (getenv)

/**
 * APP ENVIRONMENT
 */
if (!defined("CONF_ENV")) {
    define("CONF_ENV", getenv("APP_ENV") ?: "production");
}

/**
 * DATABASE
 */

if (!defined("CONF_DB_HOST")) {
    define("CONF_DB_HOST", getenv("DB_HOST") ?: "localhost");
}
if (!defined("CONF_DB_USER")) {
    define("CONF_DB_USER", getenv("DB_USER") ?: "root");
}
if (!defined("CONF_DB_PASS")) {
    define("CONF_DB_PASS", getenv("DB_PASS") ?: "");
}
if (!defined("CONF_DB_NAME")) {
    define("CONF_DB_NAME", getenv("DB_NAME") ?: "cvnsu_bd");
}

/**
 * PROJECT URLs
 */
if (!defined("CONF_URL_BASE")) {
    define("CONF_URL_BASE", getenv("APP_URL") ?: "https://cvnsu.co.ao");
}
if (!defined("CONF_URL_TEST")) {
    define("CONF_URL_TEST", getenv("APP_URL_TEST") ?: "https://cvn.local");
}
if (!defined("CONF_URL_ADMIN")) {
    define("CONF_URL_ADMIN", getenv("APP_URL_ADMIN") ?: "/admin");
}

/**
 * SITE
 */
if (!defined("CONF_SITE_NAME")) {
    define("CONF_SITE_NAME", getenv("SITE_NAME") ?: "Clínica-Videira Nguepi");
}
if (!defined("CONF_SITE_TITLE")) {
    define("CONF_SITE_TITLE", getenv("SITE_TITLE") ?: "Gestão da clínica no clique");
}
if (!defined("CONF_SITE_DESC")) {
    define(
        "CONF_SITE_DESC",
        getenv("SITE_DESC") ?: "G-Clinico é um sistema de gestão da Clínica Videira Nguepi, criado exclusivamente para esse grupo com sede em Viana Luanda Angola "
    );
}
if (!defined("CONF_SITE_LANG")) {
    define("CONF_SITE_LANG", getenv("SITE_LANG") ?: "pt");
}
if (!defined("CONF_SITE_DOMAIN")) {
    define("CONF_SITE_DOMAIN", getenv("SITE_DOMAIN") ?: "192.168.1.55");
}
if (!defined("CONF_SITE_ADDR_STREET")) {
    define("CONF_SITE_ADDR_STREET", getenv("SITE_ADDR_STREET") ?: "KM 25, Porto seco Viana");
}
if (!defined("CONF_SITE_ADDR_NUMBER")) {
    define("CONF_SITE_ADDR_NUMBER", getenv("SITE_ADDR_NUMBER") ?: " 955847745/55877458");
}
if (!defined("CONF_SITE_ADDR_COMPLEMENT")) {
    define("CONF_SITE_ADDR_COMPLEMENT", getenv("SITE_ADDR_COMPLEMENT") ?: "Clinica Videira Nguepe - SU");
}
if (!defined("CONF_SITE_ADDR_CITY")) {
    define("CONF_SITE_ADDR_CITY", getenv("SITE_ADDR_CITY") ?: "Luanda - Angola");
}
if (!defined("CONF_SITE_ADDR_STATE")) {
    define("CONF_SITE_ADDR_STATE", getenv("SITE_ADDR_STATE") ?: "SU");
}
if (!defined("CONF_SITE_ADDR_ZIPCODE")) {
    define("CONF_SITE_ADDR_ZIPCODE", getenv("SITE_ADDR_ZIPCODE") ?: " ");
}

/**
 * SOCIAL
 */
if (!defined("CONF_SOCIAL_TWITTER_CREATOR")) {
    define("CONF_SOCIAL_TWITTER_CREATOR", getenv("SOCIAL_TWITTER_CREATOR") ?: "@cvnangola");
}
if (!defined("CONF_SOCIAL_TWITTER_PUBLISHER")) {
    define("CONF_SOCIAL_TWITTER_PUBLISHER", getenv("SOCIAL_TWITTER_PUBLISHER") ?: "@cvnangola");
}
if (!defined("CONF_SOCIAL_FACEBOOK_APP")) {
    define("CONF_SOCIAL_FACEBOOK_APP", getenv("SOCIAL_FACEBOOK_APP") ?: "626590460695980");
}
if (!defined("CONF_SOCIAL_FACEBOOK_PAGE")) {
    define("CONF_SOCIAL_FACEBOOK_PAGE", getenv("SOCIAL_FACEBOOK_PAGE") ?: "cvnangola");
}
if (!defined("CONF_SOCIAL_FACEBOOK_AUTHOR")) {
    define("CONF_SOCIAL_FACEBOOK_AUTHOR", getenv("SOCIAL_FACEBOOK_AUTHOR") ?: "cvnangolaoficial");
}
if (!defined("CONF_SOCIAL_GOOGLE_PAGE")) {
    define("CONF_SOCIAL_GOOGLE_PAGE", getenv("SOCIAL_GOOGLE_PAGE") ?: "107305124528362639842");
}
if (!defined("CONF_SOCIAL_GOOGLE_AUTHOR")) {
    define("CONF_SOCIAL_GOOGLE_AUTHOR", getenv("SOCIAL_GOOGLE_AUTHOR") ?: "103958419096641225872");
}
if (!defined("CONF_SOCIAL_INSTAGRAM_PAGE")) {
    define("CONF_SOCIAL_INSTAGRAM_PAGE", getenv("SOCIAL_INSTAGRAM_PAGE") ?: "cvnangola");
}
if (!defined("CONF_SOCIAL_YOUTUBE_PAGE")) {
    define("CONF_SOCIAL_YOUTUBE_PAGE", getenv("SOCIAL_YOUTUBE_PAGE") ?: "cvnangola");
}

/**
 * DATES
 */
if (!defined("CONF_DATE_AO")) {
    define("CONF_DATE_AO", "d/m/Y H:i:s");
}
if (!defined("CONF_DATE_APP")) {
    define("CONF_DATE_APP", "Y-m-d H:i:s");
}

/**
 * PASSWORD
 */
if (!defined("CONF_PASSWD_MIN_LEN")) {
    define("CONF_PASSWD_MIN_LEN", 5);
}
if (!defined("CONF_PASSWD_MAX_LEN")) {
    define("CONF_PASSWD_MAX_LEN", 40);
}
if (!defined("CONF_PASSWD_ALGO")) {
    define("CONF_PASSWD_ALGO", PASSWORD_DEFAULT);
}
if (!defined("CONF_PASSWD_OPTION")) {
    define("CONF_PASSWD_OPTION", ["cost" => 10]);
}


/**
 * VIEW
 */
if (!defined("CONF_VIEW_PATH")) {
    define("CONF_VIEW_PATH", __DIR__ . "/../../shared/views");
}
if (!defined("CONF_VIEW_EXT")) {
    define("CONF_VIEW_EXT", "php");
}

/**
 * UPLOAD
 */
if (!defined("CONF_UPLOAD_DIR")) {
    define("CONF_UPLOAD_DIR", "storage");
}
if (!defined("CONF_UPLOAD_IMAGE_DIR")) {
    define("CONF_UPLOAD_IMAGE_DIR", "images");
}
if (!defined("CONF_UPLOAD_FILE_DIR")) {
    define("CONF_UPLOAD_FILE_DIR", "files");
}
if (!defined("CONF_UPLOAD_MEDIA_DIR")) {
    define("CONF_UPLOAD_MEDIA_DIR", "medias");
}

/**
 * IMAGES
 */
if (!defined("CONF_IMAGE_CACHE")) {
    define("CONF_IMAGE_CACHE", CONF_UPLOAD_DIR . "/" . CONF_UPLOAD_IMAGE_DIR . "/cache");
}
if (!defined("CONF_IMAGE_SIZE")) {
    define("CONF_IMAGE_SIZE", 2000);
}
if (!defined("CONF_IMAGE_QUALITY")) {
    define("CONF_IMAGE_QUALITY", ["jpg" => 75, "png" => 5]);
}

/**
 * MAIL
 */
if (!defined("CONF_MAIL_HOST")) {
    define("CONF_MAIL_HOST", getenv("MAIL_HOST") ?: "ssl://mail.sga-ed.info");
}
if (!defined("CONF_MAIL_PORT")) {
    define("CONF_MAIL_PORT", getenv("MAIL_PORT") ?: "465");
}
if (!defined("CONF_MAIL_USER")) {
    define("CONF_MAIL_USER", getenv("MAIL_USER") ?: "suporte@sga-ed.info");
}
if (!defined("CONF_MAIL_PASS")) {
    define("CONF_MAIL_PASS", getenv("MAIL_PASS") ?: "QfGyrnvJ]p7K");
}
if (!defined("CONF_MAIL_SENDER")) {
    define("CONF_MAIL_SENDER", [
        "name" => getenv("MAIL_SENDER_NAME") ?: "Suporte SGA",
        "address" => getenv("MAIL_SENDER_ADDRESS") ?: "suporte@sga-ed.info"
    ]);
}
if (!defined("CONF_MAIL_SUPPORT")) {
    define("CONF_MAIL_SUPPORT", getenv("MAIL_SUPPORT") ?: "suporte@sga-ed.info");
}
if (!defined("CONF_MAIL_OPTION_LANG")) {
    define("CONF_MAIL_OPTION_LANG", "pt");
}
if (!defined("CONF_MAIL_OPTION_HTML")) {
    define("CONF_MAIL_OPTION_HTML", true);
}
if (!defined("CONF_MAIL_OPTION_AUTH")) {
    define("CONF_MAIL_OPTION_AUTH", true);
}
if (!defined("CONF_MAIL_OPTION_SECURE")) {
    define("CONF_MAIL_OPTION_SECURE", "tls");
}
if (!defined("CONF_MAIL_OPTION_CHARSET")) {
    define("CONF_MAIL_OPTION_CHARSET", "utf-8");
}

if (!defined("CONF_MESSAGE_CLASS")) {
    define("CONF_MESSAGE_CLASS", "alert ");
}
if (!defined("CONF_MESSAGE_ERROR")) {
    define("CONF_MESSAGE_ERROR", "alert-danger");
}
if (!defined("CONF_MESSAGE_WARNING")) {
    define("CONF_MESSAGE_WARNING", "alert-warning");
}
if (!defined("CONF_MESSAGE_SUCCESS")) {
    define("CONF_MESSAGE_SUCCESS", "alert-success");
}
if (!defined("CONF_MESSAGE_INFO")) {
    define("CONF_MESSAGE_INFO", "alert-info");
}

/**
 * SMS
 */
if (!defined("CONF_SMS_HOST")) {
    define("CONF_SMS_HOST", getenv("SMS_HOST") ?: "https://api.wesender.co.ao/envio/apikey");
}
if (!defined("CONF_SMS_METHOD")) {
    define("CONF_SMS_METHOD", getenv("SMS_METHOD") ?: "POST");
}
