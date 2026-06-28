<?php

class PageController extends AbstractController 
{
    // --- ACCUEIL ---
    public function home() : void
    {
        $isConnected = $this->isAuthenticated();
        $username = null;
        $user = null;

        if ($isConnected) {
            $username = $_SESSION['username'] ?? 'Utilisateur'; 
            $user = $_SESSION['user'] ?? null;
        }

        $this->render("home", [
            "pageTitle" => "Accueil - Tournois",
            "isConnected" => $isConnected,
            "username" => $username,
            "user" => $user,
        ]);
    }

    // --- PROFIL UTILISATEUR ---
    public function profile() : void
    {
        $isConnected = $this->isAuthenticated();
        $username = null;
        $user = null;

        if ($isConnected) {
            $username = $_SESSION['username'] ?? 'Utilisateur'; 
            $user = $_SESSION['user'] ?? null;
        }

        $this->render("profile", [
            "pageTitle" => "Mon Profil",
            "isConnected" => $isConnected,
            "username" => $username,
            "user" => $user,
        ]);
    }

    // --- ERREUR 404 ---
    public function notFound() : void
    {
        $isConnected = $this->isAuthenticated();
        $username = null;
        $user = null;

        if ($isConnected) {
            $username = $_SESSION['username'] ?? 'Utilisateur'; 
            $user = $_SESSION['user'] ?? null;
        }

        $this->render("notFound", [
            "pageTitle" => "Page introuvable",
            "isConnected" => $isConnected,
            "username"=> $username,
            "user" => $user,
        ]);
    }
}