<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Class AbstractBlockType
 */
class AbstractBlockType extends FormType
{
    public function getParent(): string
    {
        return BaseBlockType::class;
    }
}
