<?php
    require_once "./config/app.php";
    require_once "./autoload.php";

    use app\controllers\viewsController;

    // Obtener la vista desde la URL: index.php?views=algo
    if (isset($_GET['views'])) {
        $url = explode("/", $_GET['views']);
    } else {
        // Vista por defecto: "menu"
        $url = ["menu"];
    }

    $viewsController = new viewsController();
    $vista = $viewsController->obtenerVistasControlador($url[0]);

    // Cargar la vista resultante
    require_once $vista;

