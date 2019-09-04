<?php

namespace CleaniqueCoders\Console\Tests\Stubs;

use CleaniqueCoders\Console\Traits\ComposerTrait;
use CleaniqueCoders\Console\Traits\GitTrait;
use CleaniqueCoders\Console\Traits\ProjectTrait;
use CleaniqueCoders\Console\Traits\QualifiedClassNameTrait;

class Commander
{
    use ComposerTrait;
    use ProjectTrait;
    use QualifiedClassNameTrait;
    use GitTrait;
}
