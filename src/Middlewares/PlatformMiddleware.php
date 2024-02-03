<?php

namespace Dot\Platform\Middlewares;

use Closure;

class PlatformMiddleware
{
    public function handle($request, Closure $next)
    {

        // Getting frontend status.

        if (!$request->is(ADMIN . '/*')) {
            if (option("site_status", "1") != "1") {
                return response(view("errors.000")->render());
            }
        }

        return $next($request);
    }
}
