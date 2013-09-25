CREATE TABLE `caring` (
    `id`         bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
    `node_id`    bigint(20) unsigned  NOT NULL,
    `created_by` bigint(20) unsigned  NOT NULL,
    `created_at` TIMESTAMP  NOT NULL  DEFAULT  CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP  NOT NULL,
    `status`     int(1)     unsigned  NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE = MyISAM AUTO_INCREMENT = 730 DEFAULT CHARSET = utf8mb4;
