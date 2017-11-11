<?php


namespace Umanit\BlockBundle\Block;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;
use Umanit\BlockBundle\Model\BlockInterface;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
abstract class AbstractBlockManager extends AbstractType
{
    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * This method must return the block entity managed by this block manager.
     *
     * @example return QuoteTextBlock::class;
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

    /**
     * @param EngineInterface $engine
     */
    public function setEngine(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @inheritdoc
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('position', HiddenType::class, ['attr' => ['data-target' => 'position']]);
    }

    /**
     * @inheritdoc
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->getManagedBlockType(),
            'locale'     => 'en',
        ]);
    }

}
