<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleFormData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (($request->isMethod('PUT') || $request->isMethod('PATCH')) &&
            strpos($request->header('Content-Type'), 'multipart/form-data') !== false) {
            $content = $request->getContent();

            $pattern = '/name=\"([^\"]*)\"\s*([^\-]+)/';
            preg_match_all($pattern, $content, $matches);

            $data = [];
            for ($i = 0; $i < count($matches[1]); $i++) {
                $key = $matches[1][$i];
                $value = trim($matches[2][$i]);

                if (strpos($key, '[]') !== false) {
                    $key = str_replace('[]', '', $key);
                    if (!isset($data[$key])) {
                        $data[$key] = [];
                    }
                    $data[$key][] = $value;
                } else {
                    $data[$key] = $value;
                }
            }

            $request->merge($data);
        }

        return $next($request);
    }
}
