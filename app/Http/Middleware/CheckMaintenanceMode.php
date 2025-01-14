<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


// use Illuminate\Support\Facades\Response;
class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $isMaintenanceOn = AppSetting::first()?->is_maintenance_on ?? false;

        if ($isMaintenanceOn) {
            // Get the custom maintenance message if available
            $maintenanceMessage = AppSetting::first()?->maintenance_message ?? 'التطبيق الآن في وضع الصيانة ، الرجاء المحاولة لاحقا';

            return Response::json([
                'status' => false,
                'message' => $maintenanceMessage,
                'errors' => [$maintenanceMessage],
            ], 200);
        }

        return $next($request);
    }
}
