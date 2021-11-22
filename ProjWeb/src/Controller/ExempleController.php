<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class ExempleController
{
    public function index()
    {
        return new Response("Hello World !");
    }
}

