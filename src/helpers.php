<?php

use Phin\Application;
use Illuminate\Contracts\Routing\ResponseFactory;

if (! function_exists('app')) {
    function app($make = null, $parameters = [])
    {
        if (is_null($make)) {
            return Application::getInstance();
        }
        return Application::getInstance()->make($make, $parameters);
    }
}

if (! function_exists('route')) {
    function route($name, $parameters = [], $absolute = true)
    {
        return app('url')->route($name, $parameters, $absolute);
    }
}

if (! function_exists('url')) {
    function url($path = null, $parameters = [], $secure = null)
    {
        if (is_null($path)) {
            return app(UrlGenerator::class);
        }
        return app(UrlGenerator::class)->to($path, $parameters, $secure);
    }
}

if (! function_exists('redirect')) {
    function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        if (is_null($to)) {
            return app('redirect');
        }
        return app('redirect')->to($to, $status, $headers, $secure);
    }
}

if (! function_exists('session')) {
    function session($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('session');
        }
        if (is_array($key)) {
            return app('session')->put($key);
        }
        return app('session')->get($key, $default);
    }
}

if (! function_exists('asset')) {
    function asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

if (! function_exists('config')) {
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }
        if (is_array($key)) {
            return app('config')->set($key);
        }
        return app('config')->get($key, $default);
    }
}

if (! function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return value($default);
        }
        switch (strtolower($value)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return;
        }
        return $value;
    }
}

if (! function_exists('environment')) {
    function environment($check = null)
    {
        if ($check) {
            return config('env') === $check;
        } else {
            return config('env', 'production');
        }
    }
}

if (! function_exists('response')) {
    function response($content = '', $status = 200, array $headers = [])
    {
        $factory = app(ResponseFactory::class);
        if (func_num_args() === 0) {
            return $factory;
        }
        return $factory->make($content, $status, $headers);
    }
}

if (! function_exists('view')) {
    function view($view = null, $data = [], $mergeData = [])
    {
        $factory = app('view');

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }
}

if (! function_exists('base_path')) {
    function base_path($path = '')
    {
        return app()->basePath().($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('site_path')) {
    function site_path($path = '')
    {
        return app('path').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('public_path')) {
    function public_path($path = '')
    {
        return app()->make('path.public').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('elixir')) {
    function elixir($file, $buildDirectory = 'build')
    {
        static $manifest;
        static $manifestPath;

        if (is_null($manifest) || $manifestPath !== $buildDirectory) {
            $manifest = json_decode(file_get_contents(public_path($buildDirectory.'/rev-manifest.json')), true);

            $manifestPath = $buildDirectory;
        }

        if (isset($manifest[$file])) {
            return '/'.trim($buildDirectory.'/'.$manifest[$file], '/');
        }

        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}
