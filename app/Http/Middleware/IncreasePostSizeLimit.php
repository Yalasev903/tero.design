<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncreasePostSizeLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        ini_set('upload_max_filesize', '200M');
        ini_set('post_max_size', '200M');
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');
        return $next($request);
    }
}
