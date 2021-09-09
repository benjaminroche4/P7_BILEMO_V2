<h1>Projet n°7 - Bilemo</h1>
<p>Création d'une API pour le client Bilemo.</p>

<h2>Instalation du projet</h2>
1. Clonez ou téléchargez le repository GitHub dans le dossier voulu :
   ```
   git clone https://github.com/benjaminroche4/P6_Snowtrick.git
   ```
2. Passez en mode "dev" dans le fichier ".env" :
   ```
   APP_ENV=dev
   ```
3. Configurez la connexion à la base de données dans le même fichier :
   ```
   DATABASE_URL="mysql://root:root@127.0.0.1:8889/bilemonew?serverVersion=13&charset=utf8"
   ```
4. Téléchargez les dépendances nécessaires grace à composer :
   ```
   composer install
   ```
5. Installez la base de données à l'aide des commandes suivantes dans votre terminal :
   ```
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update --force
   ```
6. Lancez le serveur à l'aide du terminal avec la commande suivante :
   ```
   symfony server:start
   ```
7. Le projet est maintenant installé. Vous pouvez tester l'application à cette adresse :
   ```
   http://127.0.0.1:8000
   ```