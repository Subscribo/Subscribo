<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelBase\Traits\SearchableByIdentifierTrait;

/**
 * Model Country
 *
 * Model class for being changed and used in the application
 *
 * @method \Subscribo\ModelCore\Models\Country findByIdentifier() static findByIdentifier(int|string $identifier)
 */
class Country extends \Subscribo\ModelCore\Bases\Country
{
    use SearchableByIdentifierTrait;
}
