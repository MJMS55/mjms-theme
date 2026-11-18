# MJMS Theme

Application de thème Nextcloud issue de la maquette **Institutionnel compacte**.
Identifiant technique : `mjms_theme`. Version : **0.1.3**.

## Fonctionnement

- Palette bleue sobre, navigation claire et séparateurs discrets.
- Marges réduites, navigation de 190 px et barres compactes sur ordinateur avec souris.
- Actions, sélection, partage, menus, permissions et recherche natifs conservés.
- Écran de connexion et pages publiques inchangés.
- Aucun JavaScript, appel externe, collecte de données ou dépendance à compiler.
- Désactivation immédiate sans modification des fichiers du cœur Nextcloud.
- Fond de tableau de bord uni, assorti au thème, avec contraste du bandeau et de la recherche.
- Icônes Lucide dans la navigation et sur les commandes reconnues du cœur et des applications
  (recherche, notifications, réglages, menus, téléchargement et autres actions courantes).
  Les applications et commandes non reconnues, les miniatures et les avatars utilisent leurs
  icônes natives. Le thème ne modifie pas les fichiers des applications.

Le thème adapte l’interface existante. Il ne déplace pas les applications dans une
nouvelle barre latérale comme dans la maquette et ne remplace pas les données réelles.
La liste virtualisée conserve sa hauteur native de 44 px : la réduire seulement en
CSS fausserait le calcul du défilement. Le gain de place porte sur les marges, la
navigation et les barres. Les écrans tactiles conservent les dimensions natives.

## Compatibilité

Nextcloud **35 et 36** sont déclarés dans `appinfo/info.xml`. Les API de chargement
et les composants de fichiers ont été examinés dans les sources officielles 35 et
dans la branche de développement disponible lors de la création.
**Cela ne remplace pas une validation sur les deux serveurs.** Cette version est une
candidate à tester, pas une certification de compatibilité ni une publication App Store.
Les applications tierces peuvent nécessiter des ajustements visuels.

La palette claire correspond à la maquette. Un jeu de couleurs sombres est fourni
pour l’attribut `data-theme-dark` de Nextcloud ; son rendu, comme le mode automatique
et le contraste élevé, doit être vérifié dans l’instance cible.

## Installation

Depuis le répertoire racine de votre serveur Nextcloud, décompresser l’archive
dans `custom_apps`. Le dossier final doit impérativement s’appeler `mjms_theme` :

```text
custom_apps/mjms_theme/appinfo/info.xml
custom_apps/mjms_theme/lib/AppInfo/Application.php
```

Puis, avec l’utilisateur du serveur web (adapter `www-data` à votre installation) :

```sh
sudo -u www-data php occ app:enable mjms_theme
```

Recharger complètement le navigateur. Le nom du dossier de développement
`mjms-theme` n’est pas le nom à utiliser sur le serveur.

## Configuration

La présentation compacte est active par défaut. Pour conserver uniquement la palette :

```sh
sudo -u www-data php occ config:app:set mjms_theme compact --value=no
```

Pour rétablir la présentation compacte :

```sh
sudo -u www-data php occ config:app:set mjms_theme compact --value=yes
```

Le logo et le nom de l’organisation restent configurables via la personnalisation
native de Nextcloud. Les autres thèmes CSS peuvent entrer en conflit : tester sans
autre thème avant de les combiner.

Pour retrouver les icônes natives :

```sh
sudo -u www-data php occ config:app:set mjms_theme modern_icons --value=no
```

Utiliser `--value=yes` pour réactiver les icônes modernes, puis recharger la page.

## Désactivation

```sh
sudo -u www-data php occ app:disable mjms_theme
```

## Vérifications et archive

```sh
php tests/run.php
```

Sous Windows, `powershell -File tools/package.ps1` construit une archive contenant
le dossier `mjms_theme`, les sources et la licence. Aucun accès au serveur nécessaire.

## Avant publication App Store

1. Installer sur des instances de test Nextcloud 35 et 36.
2. Vérifier la navigation, les icônes du bandeau, les menus et les champs de recherche.
3. Parcourir un dossier de plus de 1 000 fichiers, sélectionner des éléments au début
   et en fin de liste, renommer, partager, téléverser et basculer en grille.
4. Vérifier Agenda, Contacts, Talk, paramètres, mobile, clavier, zoom 200 % et modes
   sombre/contraste élevé ; vérifier aussi connexion et liens publics.
5. Vérifier l’accès public au [dépôt](https://github.com/MJMS55/mjms-theme) et aux
   [signalements de bugs](https://github.com/MJMS55/mjms-theme/issues), puis compléter les captures
   et les notes de version ; obtenir le certificat de signature de l’application.
6. Signer l’archive via le processus Nextcloud App Store et publier après validation.

Sources : [bootstrap](https://docs.nextcloud.com/server/stable/developer_manual/app_development/bootstrap.html),
[métadonnées](https://docs.nextcloud.com/server/stable/developer_manual/app_development/info.html),
[sources Nextcloud](https://github.com/nextcloud/server).

## Licence

AGPL-3.0-or-later. Voir `COPYING`. Icônes [Lucide](https://lucide.dev), version
0.468.0, sous licence ISC : voir `img/icons/LICENSE` pour les mentions complètes.
