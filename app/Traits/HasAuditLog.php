<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait HasAuditLog
{
    /**
     * Boot the trait and register model events
     */
    protected static function bootHasAuditLog()
    {
        // Event saat data dibuat
        static::created(function ($model) {
            $model->logAudit('created');
        });

        // Event saat data diupdate
        static::updated(function ($model) {
            $model->logAudit('updated');
        });

        // Event saat data dihapus
        static::deleted(function ($model) {
            $model->logAudit('deleted');
        });

        // Event saat data di-restore (soft delete)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logAudit('restored');
            });
        }
    }

    /**
     * Log audit trail
     */
    public function logAudit($action)
    {
        // Skip jika tidak ada user yang login (misalnya dari seeder atau console)
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $changes = [];
        $oldValues = [];
        $newValues = [];

        // Untuk action updated, dapatkan perubahan spesifik
        if ($action === 'updated') {
            $changes = $this->getAuditChanges();
            $oldValues = $this->getOriginal();
            $newValues = $this->getAttributes();
        } elseif ($action === 'deleted') {
            $oldValues = $this->getAttributes();
        } elseif ($action === 'created') {
            $newValues = $this->getAttributes();
        }

        // Hilangkan field yang tidak perlu di-track
        $excludedFields = ['created_at', 'updated_at', 'deleted_at', 'password', 'remember_token'];
        $oldValues = array_diff_key($oldValues, array_flip($excludedFields));
        $newValues = array_diff_key($newValues, array_flip($excludedFields));

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'model_identifier' => $this->getAuditIdentifier(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get changes untuk audit log
     */
    protected function getAuditChanges()
    {
        $changes = [];
        $dirty = $this->getDirty();

        foreach ($dirty as $field => $newValue) {
            $oldValue = $this->getOriginal($field);

            // Skip jika tidak berubah atau field yang di-exclude
            if ($oldValue == $newValue) {
                continue;
            }

            $changes[$field] = [
                'old' => $oldValue,
                'new' => $newValue,
            ];
        }

        return $changes;
    }

    /**
     * Get identifier untuk ditampilkan di audit log
     * Override method ini di model jika ingin custom identifier
     */
    protected function getAuditIdentifier()
    {
        // Coba beberapa field umum sebagai identifier
        $identifierFields = ['nama', 'name', 'nipp', 'kode_jabatan', 'program_kerja', 'email'];

        foreach ($identifierFields as $field) {
            if (isset($this->{$field})) {
                return $this->{$field};
            }
        }

        return $this->id;
    }

    /**
     * Relationship ke audit logs
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model', 'model_type', 'model_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get audit history
     */
    public function getAuditHistory()
    {
        return $this->auditLogs()->with('user')->get();
    }
}
