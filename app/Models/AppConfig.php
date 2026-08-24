<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property string $name
 * @property string|null $default
 * @property string|null $value
 */
class AppConfig extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'name';
    public $timestamps = false;

    protected $fillable = ['name', 'default', 'value'];

    public static function getValue(string $name)
    {
        $entry = static::query()->findOrFail($name);

        return $entry->value();
    }

    /**
     * @return Collection<string, string|null>
     */
    public static function dictionary(): Collection
    {
        $entries = [];

        foreach (self::all() as $row) {
            $entries[$row->name] = $row->value ?: $row->default;
        }

        return collect($entries);
    }

    public function value(): mixed
    {
        return $this->value ?? $this->default;
    }
}
