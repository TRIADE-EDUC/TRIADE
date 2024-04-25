# Notice de sécurité

> [!IMPORTANT]  
> Si vous êtes administrateur d'une instance TRIADE, nous vous conseillons de lire cette page

## La sécurité, une affaire qui nous concerne tous
La sécurité est l'une des priorités dans le développement de TRIADE, gardez à l'esprit qu'aucun système d'information n'est infaillible et que la 1ère faille de sécurité est l'utilisateur lui-même.

## Vulnérabilités dans TRIADE
Bien que nous faisons notre maximum pour fournir un environnement numérique de travail sécurisée, nous ne pouvons garantir que le logiciel TRIADE est exempt de failles de sécurité et que des vulnérabilités peuvent être découvertes à tout moment. 

**Si jamais vous tombez sur une faille de sécurité dans le logiciel TRIADE, veuillez prévenir par voie privée (e-mail, ticket support, ticket Discord) le support TRIADE immédiatement (avec la description la plus explicite possible)**. Nous ferons le nécessaire pour corriger la faille au plus vite possible.

Aussi, veuillez restez informés des mises à jour de TRIADE par les moyens de notification mises à disposition (mail, RSS, Forum, Discord) afin de pouvoir corriger la faille de votre coté si jamais un tél évènement venait à se produire.

## Les bons réflexes
En tant qu'administrateur d'une instance TRIADE, il est fortement recommandé d'effectuer les actions suivantes (afin de renforcer la sécurité de votre TRIADE) :
- Sécuriser le serveur (par exemple en n'utilisant que les modules du serveur nécessaire au bon fonctionnement de TRIADE)
- Placer votre serveur derrière un pare-feu et/ou un proxy afin de filtrer tout activité logicielle.
- Sécuriser le transfert client/serveur (notamment via un certificat SSL/TLS donnant le fameux HTTPS) afin de déjouer les tentatives d'interception de données.

Les utilisateurs de TRIADE doivent, quant à eux, adopter ces réflexes suivants :
- Le mot de passe doit être complexe (mot de passe alphanumérique comportant des caractères spéciaux ou phrase de passe), cette sécurité peut être forcée dans le logiciel TRIADE
- Le mot de passe est confidentiel et ne doit **jamais être communiqué à des tiers**
- Dans le cas ou les utilisateurs se connectent sur des ordinateurs publics, toute proposition d'enregistrement de mot de passe doivent être systématiquement refusées et les utilisateurs doivent se déconnecter quand ils ont fini leur activité sur TRIADE

## Mesures de sécurité mise en place sur TRIADE
Des procédures sont mise en place sur TRIADE afin de sécuriser au maximum l'environnement numérique de travail :
1. 3 niveaux de sécurité pour la gestion des mots de passe.
2. Mot de passe hashé dans la base de données.
3. Gestion des attaques en direct (temporisation sur le renouvellement du mot de passe si erreur) plus il y a d'erreur plus la demande pour recommencer le mot de passe est long ...
4. Journalisation des opérations effectués par Triade
5. Possibilité de renforcer cette journalisation en positionnant le variable LOG à "OUI" dans le fichier triade/common/config.inc.php le fichier de log est alors créé dans le répertoire triade/data/install_log/access.log
6. Module vérification (très important) permet non seulement de verifier la structure de la base mais aussi la sécurité de Triade et de ses données.
7. Gestion de blacklist (si un compte tente d'accéder à une page dont il n'est pas autorisé)
8. Gestion de sécurité via les sessions coté serveur rien n'est stocké sur le poste client.
