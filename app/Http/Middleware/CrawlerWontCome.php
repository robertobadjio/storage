<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class CrawlerWontCome
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $crawlerDetect = new CrawlerDetect();

        if ($crawlerDetect->isCrawler()) {
            return Response::create('Crawler detected', 400);
        }

        return $next($request);
    }
}