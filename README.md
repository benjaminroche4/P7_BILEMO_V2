
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d0d7cc075de84fd1b847b2787a53ae55)](https://app.codacy.com/gh/benjaminroche4/P7_BILEMO_V2?utm_source=github.com&utm_medium=referral&utm_content=benjaminroche4/P7_BILEMO_V2&utm_campaign=Badge_Grade_Settings)

<h1>Projet n°7 - Bilemo</h1>
<p>Création d'une API pour le client Bilemo.</p>

<h2>Instalation du projet</h2>
<p>1. Clonez ou téléchargez le repository GitHub dans le dossier voulu :</p>

   ```
   git clone https://github.com/benjaminroche4/P7_Bilemo_V2.git
   ```

<p>2. Configurez la connexion à la base de données dans le fichier ".env" à la racine du projet :</p>

   ```
   DATABASE_URL="mysql://root:root@127.0.0.1:8889/db_name?serverVersion=13&charset=utf8"
   ```

<p>3. Téléchargez les dépendances nécessaires grace à composer :</p>

   ```
   composer install
   ```

<p>4. Installez la base de données à l'aide des commandes suivantes dans votre terminal :</p>

   ```
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update
   ```

<p>5. Lancez le serveur à l'aide du terminal avec la commande suivante :</p>

   ```
   symfony server:start
   ```

<p>6. Le projet est maintenant installé. Vous pouvez consulter la documentation de l'API à cette adresse :</p>

   ```
   http://127.0.0.1:8000/api/doc
   ```
