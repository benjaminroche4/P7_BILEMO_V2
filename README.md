<h1>Projet n°7 - Bilemo</h1>
<p>Création d'une API pour le client Bilemo.</p>

<h2>Instalation du projet</h2>
<p>1. Clonez ou téléchargez le repository GitHub dans le dossier voulu :</p>
   ```
   git clone https://github.com/benjaminroche4/P6_Snowtrick.git
   ```
<p>2. Passez en mode "dev" dans le fichier ".env" :</p>
   ```
   APP_ENV=dev
   ```
<p>3. Configurez la connexion à la base de données dans le même fichier :</p>
   ```
   DATABASE_URL="mysql://root:root@127.0.0.1:8889/bilemonew?serverVersion=13&charset=utf8"
   ```<
<p>4. Téléchargez les dépendances nécessaires grace à composer :</p>
   ```
   composer install
   ```
<p>5. Installez la base de données à l'aide des commandes suivantes dans votre terminal :</p>
   ```
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update --force
   ```
<p>6. Lancez le serveur à l'aide du terminal avec la commande suivante :</p>
   ```
   symfony server:start
   ```
<p>7. Le projet est maintenant installé. Vous pouvez tester l'application à cette adresse :</p>
   ```
   http://127.0.0.1:8000
   ```