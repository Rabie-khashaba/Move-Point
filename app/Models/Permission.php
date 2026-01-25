<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
        'guard_name'
    ];

    /**
     * Get the display name or fallback to formatted name
     */
    public function getDisplayNameAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        // Format the name if no display_name is set
        return $this->formatPermissionName($this->name);
    }

    /**
     * Format permission name for display
     */
    private function formatPermissionName($name)
    {
        // Remove common prefixes
        $name = str_replace(['view_', 'create_', 'edit_', 'delete_', 'manage_'], '', $name);
        
        // Convert to title case
        $name = ucwords(str_replace('_', ' ', $name));
        
        return $name;
    }

    /**
     * Get the action type from permission name
     */
    public function getActionTypeAttribute()
    {
        if (str_starts_with($this->name, 'view_')) {
            return 'view';
        } elseif (str_starts_with($this->name, 'create_')) {
            return 'create';
        } elseif (str_starts_with($this->name, 'edit_')) {
            return 'edit';
        } elseif (str_starts_with($this->name, 'delete_')) {
            return 'delete';
        } elseif (str_starts_with($this->name, 'manage_')) {
            return 'manage';
        }
        
        return 'other';
    }

    /**
     * Get the module name from permission name
     */
    public function getModuleNameAttribute()
    {
        $parts = explode('_', $this->name);
        if (count($parts) > 1) {
            return ucfirst($parts[1]);
        }
        return ucfirst($this->name);
    }
}
