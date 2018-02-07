<?php

namespace Brotzka\TranslationManager\Module;

use Illuminate\Database\Eloquent\Model;

class TranslationGroup extends Model
{
	protected $fillable = ['name', 'description'];

    public function entries()
    {
        return $this->hasMany('Brotzka\TranslationManager\Module\Translation', 'translation_group');
    }
}
