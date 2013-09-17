CREATE TABLE `tokens` (
    `id`            bigint(20)   unsigned  NOT NULL AUTO_INCREMENT,
    `code`          varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `person_id`     bigint(20)   unsigned,
    `client`        varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `category`      varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `resource_hash` varchar(32)  NOT NULL,
    `scope`         TEXT,
    `data`          TEXT         NOT NULL,
    `created_at`    TIMESTAMP    NOT NULL  DEFAULT  CURRENT_TIMESTAMP,
    `touched_at`    TIMESTAMP    NOT NULL,
    `updated_at`    TIMESTAMP    NOT NULL,
    `expires_at`    TIMESTAMP    NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 730 DEFAULT CHARSET = utf8mb4;
