<?php

namespace App\Controllers\Admin;

class DashboardController
{
    public function index(): void
    {
        echo "Admin Dashboard";
    }

    public function villa(): void
    {
        echo "Admin Villa Management";
    }

    public function booking(): void
    {
        echo "Admin Booking Management";
    }
}
