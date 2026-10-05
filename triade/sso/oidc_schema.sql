-- ─────────────────────────────────────────────────────────────────────────────
-- TRIADE OIDC — tables DB
-- À exécuter une seule fois (MySQL/MariaDB)
-- Pour PostgreSQL : remplacer AUTO_INCREMENT par SERIAL, tinyint(1) par boolean,
--                  datetime par timestamp, ENGINE=InnoDB par rien.
-- ─────────────────────────────────────────────────────────────────────────────

-- Registre des applications clientes OIDC
CREATE TABLE IF NOT EXISTS `tria_oidc_clients` (
    `id`            int(11)       NOT NULL AUTO_INCREMENT,
    `client_id`     varchar(64)   NOT NULL,
    `client_secret` varchar(128)  NOT NULL DEFAULT '',  -- vide = client public PKCE-only
    `client_name`   varchar(100)  NOT NULL DEFAULT '',
    `redirect_uris` text          NOT NULL,              -- JSON array d'URLs autorisées
    `scopes`        varchar(255)  NOT NULL DEFAULT 'openid profile',
    `grant_types`   varchar(100)  NOT NULL DEFAULT 'authorization_code',
    `pkce_required` tinyint(1)    NOT NULL DEFAULT 0,    -- 1 = PKCE obligatoire (app mobile)
    `active`        tinyint(1)    NOT NULL DEFAULT 1,
    `created_at`    datetime      NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Codes d'autorisation courts-vécus (10 minutes par défaut)
CREATE TABLE IF NOT EXISTS `tria_oidc_auth_codes` (
    `id`                    int(11)      NOT NULL AUTO_INCREMENT,
    `code`                  varchar(128) NOT NULL,
    `client_id`             varchar(64)  NOT NULL,
    `id_pers`               int(11)      NOT NULL DEFAULT 0,
    `membre`                varchar(50)  NOT NULL DEFAULT '',
    `nom`                   varchar(100) NOT NULL DEFAULT '',
    `prenom`                varchar(100) NOT NULL DEFAULT '',
    `redirect_uri`          varchar(500) NOT NULL DEFAULT '',
    `scope`                 varchar(255) NOT NULL DEFAULT 'openid',
    `nonce`                 varchar(255) NOT NULL DEFAULT '',
    `code_challenge`        varchar(255) NOT NULL DEFAULT '',
    `code_challenge_method` varchar(10)  NOT NULL DEFAULT '',
    `expires_at`            datetime     NOT NULL,
    `used`                  tinyint(1)   NOT NULL DEFAULT 0,
    `created_at`            datetime     NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`),
    KEY `client_id` (`client_id`),
    KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Access tokens actifs
CREATE TABLE IF NOT EXISTS `tria_oidc_tokens` (
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `access_token` varchar(128) NOT NULL,
    `client_id`    varchar(64)  NOT NULL,
    `id_pers`      int(11)      NOT NULL DEFAULT 0,
    `membre`       varchar(50)  NOT NULL DEFAULT '',
    `nom`          varchar(100) NOT NULL DEFAULT '',
    `prenom`       varchar(100) NOT NULL DEFAULT '',
    `scope`        varchar(255) NOT NULL DEFAULT 'openid',
    `expires_at`   datetime     NOT NULL,
    `revoked`      tinyint(1)   NOT NULL DEFAULT 0,
    `created_at`   datetime     NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `access_token` (`access_token`),
    KEY `client_id` (`client_id`),
    KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- ─────────────────────────────────────────────────────────────────────────────
-- Exemples de clients à insérer (adapter les redirect_uris et secrets)
-- ─────────────────────────────────────────────────────────────────────────────

-- Client Moodle (confidentiel, secret requis)
INSERT INTO `tria_oidc_clients`
    (client_id, client_secret, client_name, redirect_uris, scopes, pkce_required, active, created_at)
VALUES (
    'moodle',
    'CHANGER_SECRET_MOODLE_MIN32CHARS',
    'Moodle',
    '["https://moodle.monecole.fr/auth/oauth2/callback.php"]',
    'openid profile',
    0,
    1,
    NOW()
);

-- Client Pronote Mobile (public, PKCE obligatoire, pas de secret)
INSERT INTO `tria_oidc_clients`
    (client_id, client_secret, client_name, redirect_uris, scopes, pkce_required, active, created_at)
VALUES (
    'pronote',
    '',
    'Pronote',
    '["pronote://oidc/callback"]',
    'openid profile',
    1,
    1,
    NOW()
);
