# Lost & Found

Application qui permet de publier des objets perdus ou trouvés, en particulier les cartes d'identité et autres documents, pour les rendre à leur propriétaire.

## Fonctionnement

1. **Publier** : la personne qui a perdu ou trouvé un objet remplit le formulaire (sans compte). Elle reçoit par e-mail un **lien secret** pour gérer son annonce.
2. **Chercher** : le propriétaire cherche son nom, un mot-clé, une catégorie ou une ville sur la page d'accueil.
3. **Contacter** : depuis la fiche, il envoie un message. L'auteur le reçoit par e-mail et répond directement ; son adresse n'est jamais affichée.
4. **Clôturer** : une fois l'objet rendu, l'auteur le marque comme « rendu » depuis son lien secret ; l'annonce disparaît des résultats.

## Installation

Prérequis : PHP 8.3+, Composer, Node.js.

```sh
composer update            # première fois : génère composer.lock
cp .env.example .env
php artisan key:generate
php artisan migrate --seed # données de démo (jeton de gestion : jeton-de-test)
php artisan storage:link   # affichage des photos
npm install
composer run dev           # serveur, file d'attente, logs et Vite
```

Les e-mails sont écrits dans `storage/logs/laravel.log` tant que `MAIL_MAILER=log`.

## Tests

```sh
php artisan test
```

## Suite du projet

Voir [Todo.md](Todo.md).
