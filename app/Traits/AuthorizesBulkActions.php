<?php

namespace App\Traits;

use Illuminate\Support\Facades\Gate;

trait AuthorizesBulkActions
{
    /**
     * Authorize bulk action on multiple models
     * 
     * @param string $ability The ability to check (e.g., 'update', 'delete')
     * @param \Illuminate\Support\Collection|array $models The models to authorize
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeBulk(string $ability, $models): void
    {
        foreach ($models as $model) {
            Gate::authorize($ability, $model);
        }
    }
}
