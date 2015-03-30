<?php namespace Subscribo\ModelBase\Traits;

use Subscribo\TranslatableModel\Traits\TranslatableModelTrait;

trait CustomizedTranslatableModelTrait
{
    use TranslatableModelTrait;

    public function getLocaleKey()
    {
        return 'translated_for_locale';
    }

    protected function useFallback()
    {
        return true;
    }

    private function alwaysFillable()
    {
        return false;
    }


}
