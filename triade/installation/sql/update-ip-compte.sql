-- Migration : table tria_ip_compte
-- Stocke les IPs de confiance pour le bypass A2F (mobile et web), fenêtre 60 jours
-- A exécuter une seule fois sur les installations existantes

CREATE TABLE IF NOT EXISTS `tria_ip_compte` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT,
  `idpers`        INT(11)      NOT NULL,
  `membre`        VARCHAR(20)  NOT NULL,
  `tuteur`        VARCHAR(20)  NOT NULL DEFAULT '',
  `ip`            VARCHAR(45)  NOT NULL,
  `source`        VARCHAR(10)  NOT NULL DEFAULT 'mobile' COMMENT 'mobile | web',
  `dernier_acces` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pers_ip` (`idpers`,`membre`,`tuteur`,`ip`,`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optionnel : colonne ip sur tria_jeton_mobile (utilisée par recupJetonMobileAvecIp)
ALTER TABLE `tria_jeton_mobile` ADD COLUMN IF NOT EXISTS `ip` VARCHAR(45) DEFAULT NULL;
