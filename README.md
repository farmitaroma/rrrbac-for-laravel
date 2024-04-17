# RRRBac for Laravel
## Role, routes, rules, based access control.
***

In order to install the package, execute the following command:

```composer require farmit/rrrbac-for-laravel```

Create a _Rules_ directory inside _app_ directory

Add ```\Farmit\RrrbacForLaravel\Providers\RrrbacServiceProvider::class``` to the list of providers in _config/app.php_.

If you haven't already, publish Spatie Permission's vendor running ```php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"```  
and add ```HasRoles``` trait to User model.  

Change in _config/permission.php_  
```Spatie\Permission\Models\Role::class``` with ```\Farmit\RrrbacForLaravel\Models\Role::class```  
```Spatie\Permission\Models\Permission::class``` with ```\Farmit\RrrbacForLaravel\Models\Permission::class```

Then publish RRRBac vendor running ```php artisan vendor:publish --provider="Farmit\RrrbacForLaravel\Providers\RrrbacServiceProvider"```

Run ```php artisan migrate```

Run the following command to install the Filament assets:
```php artisan filament:install --tables --forms --notifications --infolists```

After defining roles and users that have access to the RRRbac control panel,  
add ```\Farmit\RrrbacForLaravel\Http\Middleware\RoutesPermission::class``` to the 'web' middleware group

To extend permission to Livewire add ```\Farmit\RrrbacForLaravel\Livewire\Trait\CanView``` trait to components