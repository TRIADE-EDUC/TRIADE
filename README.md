# TRIADE
*Environnement Numérique de Travail (ENT) logiciel libre pour les écoles, collèges, lycées, établissements de formation supérieure, grandes écoles.*

## Présentation

[![](https://markdown-videos-api.jorgenkh.no/youtube/dcVtgSYhupQ)](https://youtu.be/dcVtgSYhupQ)

Découvrez TRIADE, la solution complète pour gérer efficacement la vie scolaire de votre établissement !

Grâce à notre plateforme en ligne, accédez facilement et instantanément à toutes les informations essentielles concernant votre établissement scolaire. **TRIADE vous offre un ensemble de fonctionnalités puissantes pour simplifier votre quotidien** :

* **Suivi scolaire optimisé :** Gardez une vision claire de la vie scolaire de vos élèves avec un suivi précis des absences, des retards, des dispenses, des notes, de l'emploi du temps, des réservations de ressources, des actualités, des disciplines et des sanctions.

* **Collaboration et communication fluides :** Facilitez le travail d'équipe et la communication entre les enseignants, les élèves et les parents grâce à notre messagerie intégrée, nos forums interactifs et nos circulaires administratives.

* **Pédagogie innovante :** Dynamisez vos cours avec des outils pédagogiques avancés. Créez des cours, accédez à une multitude de ressources numériques, gérez le cahier de texte, le livret de compétences, le carnet de suivi, l'agenda partagé et planifiez les devoirs surveillés (D.S.T).

* **Gestion des stages simplifiée :** Simplifiez la gestion des stages en offrant un accès dédié aux tuteurs de stage, en gérant le carnet de suivi et en facilitant la communication interne.

* **Solution adaptée aux établissements supérieurs :** Pour les universités et les écoles supérieures, TRIADE propose des fonctionnalités avancées telles que la gestion des frais de scolarité, les dossiers scolaires étudiants, les cours, les stages, les séjours à l'étranger, les projets, les examens, les soutenances, les crédits ECTS, les notes, les barèmes, et bien plus encore.

* **Parfait pour les CFA et les établissements professionnalisants :** Gérez facilement les dossiers d'entreprise, le recrutement et le placement en entreprise, la gestion des contrats, la rémunération et le remboursement des frais, tout en assurant un suivi pédagogique de l'apprenti en centre et en entreprise grâce à un carnet de liaison.

**TRIADE vous offre une solution clé en main**, entièrement personnalisable selon les besoins spécifiques de votre établissement scolaire. Profitez d'un outil convivial, intuitif et sécurisé qui vous permettra de gagner du temps, de renforcer la collaboration et d'optimiser les performances de votre équipe pédagogique.

Rejoignez dès maintenant notre communauté d'établissements scolaires satisfaits en choisissant TRIADE, la référence en matière de logiciel de vie scolaire. Essayez-le dès maintenant et découvrez comment TRIADE peut révolutionner votre établissement !

## TRIADE sur GitHub

Pour faciliter l'accès du code source aux développeurs, TRIADE met à votre disposition un repository GitHub actualisé à chaque version, les anciennes versions seront archivées dans des branches dédiées du repository (la branche main accueillant la dernière version de TRIADE).

Pour installer TRIADE pour votre établissement scolaire, **nous vous recommandons fortement de télécharger la version hébergée sur notre site officiel** : [https://triade-educ.org/accueil/telecharger.php](https://triade-educ.org/accueil/telecharger.php)

## Installer TRIADE
```
	Le logiciel Triade s'installe via un navigateur web 
	Une base MySql doit être installé.

	Pre-requis soft
	---------------

	- Serveur web (sous Linux, MacOs ou Windows)
	- MySql 5 ou 8
 	- Php 7.4

	Config PHP (php.ini)
	--------------------

	- error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE
	
	Config Mysql (my.ini)
	---------------------
	
	- key_buffer = 32M
	- read_buffer_size = 512K
	- myisam_sort_buffer_size = 16M


	Pre-requis Hard du serveur
	--------------------------

	- Pentium Dual Core (minimum)
	- 6Go Ram
	- 3Go disk 

	Lancement de l'installation
	---------------------------
	
	-->  http://votre-domaine/triade/
	ou
	-->  http://127.0.0.1/triade/   (si serveur en local avec wampserver 3.3 ou autre)
```