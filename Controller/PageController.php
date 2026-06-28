<?php

class PageController extends AbstractController 
{
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