# TaskLinker

TaskLinker est une application web interne, développée sous Symfony, permettant de suivre et gérer les projets de l'entreprise (BeWize).

## Démarrage Rapide (Installation)

Instructions pour installer le projet en local pour le développement.

### 1. Prérequis

* PHP 8.1 ou supérieur
* [Composer](https://getcomposer.org/)
* [Symfony CLI](https://symfony.com/download)
* [Node.js](https://nodejs.org/en/) (incluant npm)
* Un serveur de base de données (ex: MySQL)

### 2. Installation

1.  **Cloner le projet**
    ```bash
    git clone [URL_DU_REPOSITORY_GIT]
    cd tasklinker
    ```

2.  **Installer les dépendances PHP**
    ```bash
    composer install
    ```

3.  **Installer les dépendances Node.js**
    ```bash
    npm install
    ```

4.  **Configurer l'environnement**

    Créez votre fichier d'environnement local et configurez votre base de données :
    ```bash
    cp .env .env.local
    ```
    Ouvrez le fichier `.env.local` et modifiez la ligne `DATABASE_URL` :
    > **Exemple pour MySQL :**
    > ```
    > DATABASE_URL="mysql://root:password@127.0.0.1:3306/tasklinker?serverVersion=8.0&charset=utf8mb4"
    > ```

5.  **Base de données & Fixtures**

    Créez la base de données, appliquez les migrations et chargez les données de test (Foundry) :
    ```bash
    # Créer la base de données
    php bin/console doctrine:database:create

    # Lancer les migrations
    php bin/console doctrine:migrations:migrate

    # Charger les fixtures (données de test)
    php bin/console doctrine:fixtures:load
    ```

6.  **Compiler les assets (CSS/JS)**

    Compilez les fichiers CSS et JS avec Webpack Encore :
    ```bash
    npm run dev
    ```
    (ou `npm run watch` pour compiler automatiquement pendant que vous codez)

7.  **Lancer le serveur**

    Utilisez le serveur local de Symfony :
    ```bash
    symfony server:start -d
    ```
    L'application est maintenant accessible sur [http://localhost:8000](http://localhost:8000).
