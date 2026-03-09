<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

trait LogsAuditTrail
{
    /**
     * Log an audit trail entry
     *
     * @param string $action The action performed (e.g., 'Created', 'Updated', 'Deleted')
     * @param string $description Description of the action
     * @return void
     */
    protected function logAuditTrail(string $action, string $description): void
    {
        try {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'datetime_stamp' => now(),
                'action' => $action,
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the application flow
            \Log::error('Failed to create audit trail: ' . $e->getMessage());
        }
    }
}
