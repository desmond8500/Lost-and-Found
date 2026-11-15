# Todo

## Description
Trouver toutes les cartes perdues et les rendre à leur propriétaire.

## Fait
- [x] Mise à jour vers Laravel 13
- [x] Publier une annonce « perdu » ou « trouvé » sans créer de compte
- [x] Catégories : carte d'identité, passeport, permis, carte étudiant, portefeuille, clés, téléphone, autre
- [x] Recherche par nom inscrit sur l'objet, mot-clé, type, catégorie et ville
- [x] Fiche annonce avec photo facultative
- [x] Contact entre la personne qui a trouvé l'objet et son propriétaire (l'e-mail de l'auteur n'est jamais affiché)
- [x] Lien secret de gestion envoyé par e-mail : lire les messages, marquer comme rendu, supprimer
- [x] Protection anti-spam (champ piège + limite de requêtes)
- [x] Interface et messages de validation en français
- [x] Tests automatisés (publication, recherche, contact, gestion)

## À faire
- [ ] Lancer `composer update` en local et committer `composer.lock`
- [ ] Configurer un vrai service d'envoi d'e-mails (actuellement `MAIL_MAILER=log`)
- [ ] Alerte par e-mail quand une carte au nom recherché est publiée
- [ ] Comptes utilisateurs (retrouver toutes ses annonces au même endroit)
- [ ] Signalement des annonces abusives et page de modération
- [ ] Floutage automatique des photos de documents d'identité
- [ ] Expiration automatique des annonces après 6 mois
- [ ] Partenariats : points de dépôt (commissariats, mairies) affichés sur une carte
- [ ] API pour une application mobile (`php artisan install:api`)
