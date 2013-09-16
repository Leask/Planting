CREATE TABLE `people` (
    `id`          bigint(20)   unsigned  NOT NULL AUTO_INCREMENT,
    `external_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `provider`    varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `name`        varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `screen_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `avatar`      text         CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
    `created_at`  TIMESTAMP    NOT NULL  DEFAULT  CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP    NOT NULL,
    `status`      int(1)       unsigned  NOT NULL DEFAULT '0',
    `timezone`    varchar(255) NOT NULL,
    `locale`      varchar(10)  NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 730 DEFAULT CHARSET = utf8mb4;

ALTER TABLE `people` CHANGE COLUMN `description` `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `people` CHANGE COLUMN `name`        `name`        varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `people` ADD COLUMN    `password`                  varchar(32)  CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `people` ADD COLUMN    `salt`                      varchar(32)  CHARACTER SET utf8 COLLATE utf8_unicode_ci;
