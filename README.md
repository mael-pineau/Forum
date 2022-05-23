# Forum
Un projet scolaire

## Run

Pour lancer le projet, il faut d'abord cloner le repository avec la commande :
```
git clone https://github.com/CorentinRubio/Forum.git
```

Ensuite pour installer les librairies et dépendences, entrer la commande : 
```
composer install
```
dans le repértoire
> api

et dans le répertoire 
> client

Ensuite, changer le fichier
> .env

dans le dossier 
> api

avec les paramètres nécessaires de la base de données

Ensuite, générer la base de donnée vide avec les informations du fichier 
> .env

avec la commande :
```
php bin/console doctrine:database:create
```

Pour crée la structure de la base de données, exécuter la commande :
```
php bin/console doctrine:migrations:migrate
```

Pour remplir la base de données avec des données de tests par défaut, importer le fichier
> testValues.sql 

dans la base de données

Le mot de passe de tout les utilisateurs avec ces données de test est :
> passw0rd1

Pour lancer les serveurs, exécuter le fichier
> start.bat

dans le dossier 
> api

et dans le dossier 
> client

Il faut aussi lancer son serveur local (Wamp/Xamp/Mamp etc...)