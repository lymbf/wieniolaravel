public function handle($request, Closure $next)
{
    if(auth()->check() && auth()->user()->is_admin) {
        return $next($request);
    }
    abort(403, 'Nie masz dostępu do panelu administracyjnego.');
}

