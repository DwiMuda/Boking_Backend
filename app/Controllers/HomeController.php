<?php

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        require_once BASE_PATH . '/app/Views/home.php';
    }

    public function detail(string $id): void
    {
        echo "Villa detail: $id";
    }

    public function booking(): void
    {
        echo "Booking submitted";
    }
}
