<?php

namespace App\Http\Middleware;

use Closure;

class BasicAuth
{
    /**
     * @var string
     */
    private string $basicAuthUserName;

    /**
     * @var string
     */
    private string $basicAuthPassword;

    /**
     * BasicAuth constructor.
     */
    public function __construct()
    {
        $this->basicAuthUserName = config('api-auth.username');
        $this->basicAuthPassword = config('api-auth.password');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->getUser() !== $this->basicAuthUserName || $request->getPassword() !== $this->basicAuthPassword) {
            return response('Unauthorized', 401);
        }

        return $next($request);
    }
}
