Installation
============

Ce fichier contient les instructions d'installation de la plate-forme ADES.

* [Téléchargement](#download)
* [Installation vierge](#fresh)
  * [Répertoire d'installation](#rootfolder)
  * [Configuration sur serveur web](#webserver)
  * [Premier lancement de la plate-forme](#firstlaunch)
* [Mise à jour d'une installation existante](#update)
  * [Mise à jour manuelle](#manual)
  * [Mise à jour semi-automatique](#semiauto)
  * [Mise à jour automatique](#fullauto)

<a name="download"></a>Téléchargement
-------------------------------------

Le code source de la plate-forme est disponible sur [Github](https://github.com/doc212/ades).

Une archive zip de la dernière version stable de production est disponible [ici](https://github.com/doc212/ades/archive/master.zip).

<a name="fresh"></a>Installation vierge
-------------------

Cette section décrit comment faire pour installer ADES la toute première fois.

### <a name="rootfolder"></a>Répertoire d'installation

L'ensemble du code source doit être copié dans le répertoire d'installation d'ADES.
Pour illustrer nos propos, nous appellerons ce répertoire `%ROOT%`.

La structure du répertoire d'installation doit donc être la suivante:

    %ROOT%/app/
    %ROOT%/composer.json
    %ROOT%/composer.lock
    %ROOT%/config/
    %ROOT%/COPYING
    %ROOT%/doc/
    %ROOT%/LICENSE
    %ROOT%/local/
    %ROOT%/README.md
    %ROOT%/scripts/
    %ROOT%/src/
    %ROOT%/vendor/
    %ROOT%/web/

### <a name="webserver"></a>Configuration du serveur web

La racine du site web doit pointer vers le répertoire `%ROOT%/web/`.
Donc si ADES est accessible via l'adresse `http://monsite.org`, il faut que l'adresse `http://monsite.org/index.php` pointe vers `%ROOT%/web/index.php`.

Seuls les fichiers dans `%ROOT%/web` seront accessible via le site web.
Le serveur web ne doit pas servir les fichiers situés dans les autres répertoires mais doit y avoir accès en lecture.

Il doit cependant être autorisé à écrire dans les répertoires `%ROOT%/local` et `%ROOT%/local/db_backup`.

### <a name="firstlaunch"></a>Création de la base de données

Après avoir installé les sources dans le répertoire d'installation `%ROOT%` et correctement configuré la racine du site web,
vous pouvez simplement vous rendre sur le site,
qui vous guidera à travers la procédure de création de la base de données.

Veillez à préparer un accès au serveur MySQL avec tous les droits sur le schéma:
`DROP TABLE`, `CREATE TABLE`, `ALTER TABLE`, `SHOW TABLES`, `SELECT`, `INSERT`, `UPDATE`, `DELETE`.

Les tables nécessaires seront crées et seront préfixées du préfixe `ades_`.

Il est préférable de partir d'un base de données vierge mais si certaines tables d'ADES existent déjà, **elles ne seront pas recréées**.

Si tout se passe bien, le site vous demandera ensuite de configurer le nom de l'école et vous redirigera vers la page d'accueil.

<a name="update"></a>Mise à jour d'une installation existante
----------------------------------------

Avant toute mise à jour, il est **vivement conseillé** de faire un backup de la base de données et du répertoire d'installation dans son entièreté.
Cela vous permettra de revenir en arrière si quelque chose se passe mal.
Non pas que ça risque d'arriver, mais on ne sait jamais ;-).


### <a name="manual"></a>Mise à jour manuelle

**ATTENTION!**
La procédure manuelle décrite ci-dessous **ne fonctionnera pas correctement** si vous partez d'une version d'ADES **antérieure à 2014**.
Pour mettre à jour une telle installation, veuillez utiliser la procédure de [*mise à jour semi-automatique*](#semiauto) décrite dans la section suivante,
mais il est *utile* de lire la procédure de mise à jour manuelle.

Pour mettre à jour manuellement votre installation:

* Créez une copie de sauvegarde du répertoire `%ROOT%/local`.
* Supprimez ensuite l'entièreté du contenu du répertoire d'installation `%ROOT%`.
* Copiez toutes les nouvelles sources dans le répertoire d'installation `%ROOT%` (comme pour une [installation vierge](#fresh)).
* Ecrasez le répertoire `%ROOT%/local` avec votre sauvegarde du répertoire `local`.
* Rendez-vous sur le site et connectez vous avec un compte administrateur : si nécessaire,
  le site lancera une mise à jour de le base de données.
* Votre installation est maintenant à jour.


### <a name="semiauto"></a>Mise à jour semi-automatique

**Cette procédure est la seule possible pour les versions antérieure à 2014.**

Cette mise à jour semi-automatique procède automatiquement aux étapes de la mise à jour manuelle décrite [ci-dessus](#manual).
De plus, si vous partez d'une ancienne version d'ADES (antérieure à 2014),
cette procédure s'occupera de sauvegarder certains fichiers de configurations qui se trouvaient ailleurs que dans le répertoire `%ROOT%/local` comme par exemple,
le fichier de configuration de connexion à la base de données `config/confbd.inc.php`.

Pour procéder à la mise à jour semi-automatique:

* [Téléchargez](#download) une archive zip et nommez la `archive.zip`
* De l'archive, extrayez le fichier `scripts/extract.php` *et uniquement ce fichier*.
* Placez `archive.zip` directement dans `%ROOT%` et `extract.php` dans le répertoire `%ROOT%/web`.
* Visitez la page `extract.php` sur votre site, à partir d'un navigateur internet (par exemple : `http://monsite.org/extract.php`).
* Si tout se passe bien, la page va afficher un message `archive extracted`.
  Sinon elle affichera des messages d'erreur.
* Si tout s'est bien passé, rendez vous sur le site et connectez-vous en tant qu'administrateur.
  Si nécessaire la plate-forme procédera à une mise à jour de la base de données.
* Votre installation est maintenant à jour.

### <a name="fullauto"></a>Mise à jour automatique

Il est possible de configurer GitHub et l'installation d'ADES pour que lors de la publication de nouveau code,
ce nouveau code soit automatiquement déployé vers votre installation.

Pour ce faire, veuillez contacter l'équipe de développement d'ADES.

Pour information, cette fonctionnalité sur base sur les *webhooks* de GitHub qui feront appel au script situé dans `%ROOT%/web/pull.php`.


Copyright (c) 2014 Educ-Action
