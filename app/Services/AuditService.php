<?php
namespace App\Services;

use App\Models\{AuditLog};
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public static function log(string $action, ?Model $model = null, ?array $oldValues = null, ?array $newValues = null): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
        ]);
    }
}
