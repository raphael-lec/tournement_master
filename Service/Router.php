<?php
require_once "Model/tournement.php";     
require_once "Controller/TournamentController.php";
require_once "Controller/AuthController.php";
require_once "Controller/PageController.php";
require_once "Controller/DashboardController.php";
    class Router
    {  
        public function handleRequest(array $get) : void
        {
            if(isset($get["route"]))
            {
                if($get["route"] === "home")
                {
                    $ctrl = new PageController();
                    $ctrl->home();
                }
                else if ($get["route"] === "login")    
                {
                    $ctrl = new AuthController();
                    $ctrl->login();
                }
                else if ($get["route"] === "inscription")    
                {
                    $ctrl = new AuthController();
                    $ctrl->register();
                }
                else if ($get["route"] === "logout") 
                {
                    $ctrl = new AuthController(); // Ou ton contrôleur qui gère la connexion/déconnexion
                    $ctrl->logout();
                }
                else if ($get["route"] === "tournemant_list")
                {
                    $ctrl = new TournamentController();
                    $ctrl->tournemant_list();
                }
                else if ($get["route"] === "tournemant_home")
                {
                    $ctrl = new TournamentController();
                    $ctrl->tournemant_home();
                }

                else if ($get["route"] === "tournemant_match") 
                {
                    $ctrl = new TournamentController(); 
                    $ctrl->tournemant_match(); 
                }
                else if ($get["route"] === "tournemant_result") 
                {
                    $ctrl = new TournamentController(); 
                    $ctrl->tournemant_result(); 
                }
                else if ($get["route"] === "tournemant_player_list") 
                {
                    $ctrl = new TournamentController(); 
                    $ctrl->tournemant_player_list();
                }
                else if ($get["route"] === "profile") 
                {
                    $ctrl = new DashboardController(); 
                    $ctrl->profile();
                }
                else if ($get["route"] === "create_tournament") 
                {
                    $ctrl = new DashboardController();
                    $ctrl->create_tournament();
                }
                else if ($get["route"] === "gestionary_tournemant_dashboard") 
                {
                    $ctrl = new DashboardController();
                    $ctrl->gestionary_tournemant_dashboard();
                }
                else if ($get["route"] === "update_status") {
                    $ctrl = new DashboardController();
                    $ctrl->update_status();
                }
                else if ($get["route"] === "admin_dashboard") 
                {
                    $ctrl = new DashboardController(); 
                    $ctrl->admin_dashboard(); 
                }
                else if ($get["route"] === "create_game") 
                {
                    $ctrl = new DashboardController();
                    $ctrl->create_game();
                }
                else if ($get["route"] === "admin_change_role") 
                {
                    $ctrl = new DashboardController();
                    $ctrl->change_role();
                }
                // ➕ AJOUTE CES DEUX BLOCS POUR LES SUPPRESSIONS :
                else if ($get["route"] === "admin_delete_user") 
                {
                    $ctrl = new DashboardController();
                    $ctrl->delete_user();
                }
                else if ($get["route"] === "admin_delete_tournament") 
                {
                    $ctrl = new DashboardController();
                    $ctrl->delete_tournament();
                }
                else
                {
                    $ctrl = new PageController();
                    $ctrl->notFound();
                }
                
            }
            else
            {
                $ctrl = new PageController();
                $ctrl->home();
            }
        }
    }
?>