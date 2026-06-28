Présentation du Projet
TournamentMaster est un site de gestion et d'organisation de tournois d'e-sport. Elle permet à des passionnés de pouvoir participer à des compétitions organisées par un organisateur ou un administrateur.
Spécifications Fonctionnelles
Authentification et Profils
Inscription : Création de compte avec pseudo, email, mot de passe 
Connexion : connexion grâce à ses code utilisateur.
Profil : Historique des tournois auquel l’utilisateur a participé, palmarès par jeu (victoires/défaites) et statistiques globales.
type de compte : administrateur, organisateur, utilisateur
Modification : l’utilisateur peuvent modifier leur profile 
Gestion des Tournois
Création : Les organisateurs peuvent créer, modifier et supprimer des tournois.
Détails du tournoi : Titre, jeu, format (1v1, 5v5), date et heure
États du tournoi : Gestion du cycle de vie : Inscriptions ouvertes, En cours, Terminé.
Inscriptions et Gestion des Droits
Postulation : Un utilisateur peut s'inscrire à un tournoi. Si c'est un tournoi par équipe, il soumet le nom de son équipe et de chaque membre de celle ci
Validation : L'organisateur reçoit les demandes et peut accepter ou rejeter un participant.
Permissions spécifiques :
Organisateur : Seul lui et l’administrateur peuvent modifier les scores et clore le tournoi.
Participant : Peut consulter les matchs en cour et a venir (mettre l’arbre de match si temps)
Public : Peut consulter les résultats sans interagir et  les matchs en cour et a venir (+ arbre de match si temps)
Recherche et Filtrage 
Recherche : Barre de recherche par nom de jeu ou titre du tournoi ou organisateur
Filtres : Filtrage par statut, par format ou par tags associés.
Spécifications Techniques
stack : html/css/js/php
Base de données : MySQL
Sécurité :
Hachage des mots de passe
protection contre les injection SQL et faille XSS
 Modération et Administration
Signalement : Possibilité de signaler un utilisateur pour différent motif : 
profile inapproprié (avatar ou pseudo inapproprié)
Interface Admin : Gestion globale des utilisateur, suppression des tournois. création des comptes organisateur(seul l’admin peut les créer). une page de gestionnaire de tournois semblable au dashboard organisateur
