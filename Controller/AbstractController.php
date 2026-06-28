<?php



abstract class AbstractController
{
    protected function render(string $template, array $data) : void
    {
        extract($data);
        require "templates/partials/_nav.phtml";
        require "templates/layout.phtml";
        
    }

    protected function redirect(string $route) : void
    {
        header("Location: $route");
    }
    protected function isAuthenticated(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // 🔄 On vérifie 'user' au lieu de 'user_id' car c'est là qu'est stocké l'objet User !
    return isset($_SESSION['user']) && $_SESSION['user'] !== null;
}
}