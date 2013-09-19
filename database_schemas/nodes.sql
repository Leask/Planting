CREATE TABLE `nodes` (
    `id`          bigint(20)   unsigned  NOT NULL AUTO_INCREMENT,
    `created_at`  TIMESTAMP    NOT NULL  DEFAULT  CURRENT_TIMESTAMP,
    `when`        TIMESTAMP    NOT NULL,
    `what`        varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `created_by`  bigint(20)   unsigned  NOT NULL,
    `updated_at`  TIMESTAMP    NOT NULL,
    `reply_to`    bigint(20)   unsigned  DEFAULT  NULL,
    `status`      int(1)       unsigned  NOT NULL DEFAULT '0',
    `via`         varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 730 DEFAULT CHARSET = utf8mb4;

--  a bug in mysql for CURRENT_TIMESTAMP
