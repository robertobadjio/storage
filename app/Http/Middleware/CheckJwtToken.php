<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

/**
 * Class CheckJwtToken
 * @package App\Http\Middleware
 */
class CheckJwtToken
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $parser = new Parser();
        $token = $parser->parse($request->token);

        $privateKeyFile = config('security.jwt.storage.path');

        $privateKey = file_get_contents($privateKeyFile);
        $privateKeyObject = new Key($privateKey);

        $signer = new Sha256();

        if ($token->verify($signer, $privateKeyObject)) {
            return $next($request);
        }

        return Response::create('Access denied', 403);
    }
}
