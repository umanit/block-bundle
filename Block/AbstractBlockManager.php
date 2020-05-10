<?php

namespace Umanit\BlockBundle\Block;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;
use Umanit\BlockBundle\Model\BlockInterface;

/**
 * Class AbstractBlockManager
 */
abstract class AbstractBlockManager extends AbstractType
{
    /** @var EngineInterface */
    protected $engine;

    /**
     * This method must return the block entity managed by this block manager.
     *
     * @return string
     */
    abstract public function getManagedBlockType(): string;

    /**
     * This method will be called to render a block entity.
     *
     * @param BlockInterface $block
     *
     * @return string
     */
    abstract public function render(BlockInterface $block): string;

    public function setEngine(EngineInterface $engine): void
    {
        $this->engine = $engine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('position', HiddenType::class, ['attr' => ['data-target' => 'position']]);
    }

    /**
     * Returns the name to use in the Panel form.
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getPublicName(): string
    {
        $elements = preg_split(
            "/((?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z]))/",
            (new \ReflectionClass($this->getManagedBlockType()))->getShortName()
        );

        return trim(implode(' ', $elements));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->getManagedBlockType(),
            'locale'     => 'en',
        ]);
    }
}
