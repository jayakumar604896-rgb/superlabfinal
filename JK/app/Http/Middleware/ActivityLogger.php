<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogger
{
    protected ActivityLogRepositoryInterface $activityLog;

    public function __construct(ActivityLogRepositoryInterface $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            if ($this->shouldLogActivity($request, $response)) {
                $action = $request->method() . ' Action';
                $url = $request->path();
                $description = "Accessing path: /{$url} with values: " . json_encode($request->except(['_token', '_method', 'password', 'password_confirmation']));
                $description = substr($description, 0, 1000);

                $this->activityLog->log($action, $description);
            }
        }

        return $response;
    }

    /**
     * Skip logging validation failures and other unsuccessful mutations.
     */
    private function shouldLogActivity(Request $request, Response $response): bool
    {
        if ($response->isClientError() || $response->isServerError()) {
            return false;
        }

        if ($response->isRedirect() && $request->session()->has('errors')) {
            return false;
        }

        return true;
    }
}
