<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    private const DIRECTORY = 'backups';

    protected $fillable = ['filename', 'size', 'created_by'];

    /** Only "_auto.zip" backups are rotated automatically. */
    public function isAuto(): bool
    {
        return str_ends_with($this->filename, '_auto.zip');
    }

    public function storagePath(): string
    {
        return self::DIRECTORY . '/' . basename($this->filename);
    }
}
