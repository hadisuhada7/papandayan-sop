<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class FilterMenuByRole implements FilterInterface
{
    /**
     * Transforms a menu item. Add the restricted property to a menu item
     * when the user doesn't have the required role.
     *
     * @param  array  $item  A menu item
     * @return array
     */
    public function transform($item)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return $item;
        }

        // Check if menu item has role restrictions
        if (isset($item['role'])) {
            $allowedRoles = is_array($item['role']) ? $item['role'] : [$item['role']];
            
            // Check if user has any of the allowed roles
            $hasRole = false;
            foreach ($allowedRoles as $role) {
                if (Auth::user()->hasRole($role)) {
                    $hasRole = true;
                    break;
                }
            }
            
            // If user doesn't have the required role, mark item as restricted
            if (!$hasRole) {
                $item['restricted'] = true;
            }
        }

        return $item;
    }
}
