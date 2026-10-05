-- Table abonnement visio (une ligne par instance TRIADE)
-- À exécuter via phpMyAdmin ou patch.php

CREATE TABLE IF NOT EXISTS visio_abonnement (
    id                   SERIAL PRIMARY KEY,
    plan                 VARCHAR(20)    NOT NULL DEFAULT 'starter',
    statut               VARCHAR(10)    NOT NULL DEFAULT 'inactif',
    date_debut           DATE,
    date_fin             DATE,
    prix_mois            DECIMAL(8,2)   DEFAULT 0,
    nb_max_participants  INT            DEFAULT 6,
    nb_max_salles        INT            DEFAULT 2,
    contact_facturation  VARCHAR(200),
    notes                TEXT,
    created_at           TIMESTAMP      DEFAULT NOW(),
    updated_at           TIMESTAMP      DEFAULT NOW()
);

-- Ligne initiale (inactif par défaut)
INSERT INTO visio_abonnement (plan, statut) VALUES ('starter', 'inactif');
