<?php

namespace Brotzka\TranslationManager\Module;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['language', 'key', 'value', 'parent', 'translation_group'];

    /**
     * Relationshipss
     */
    public function getParent()
    {
        return $this->belongsTo('Brotzka\TranslationManager\Module\Translation', 'parent');
    }

    public function children()
    {
        return $this->hasMany('Brotzka\TranslationManager\Module\Translation', 'parent');
    }

    public function getGroup()
    {
        return $this->belongsTo('Brotzka\TranslationManager\Module\TranslationGroup', 'translation_group');
    }
}
