<?php

namespace Umanit\BlockBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Umanit\BlockBundle\Block\AbstractBlockManager;
use Umanit\BlockBundle\Form\DataTransformer\PanelDataTransformer;
use Umanit\BlockBundle\Resolver\BlockManagerResolver;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class PanelType extends AbstractType
{
    /**
     * @var BlockManagerResolver
     */
    private $blockManagerResolver;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * PanelType constructor.
     *
     * @param BlockManagerResolver   $blockManagerResolver
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     */
    public function __construct(
        BlockManagerResolver $blockManagerResolver,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ) {
        $this->blockManagerResolver = $blockManagerResolver;
        $this->translator           = $translator;
        $this->em                   = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Filter blocks available
        $blockManagers = [];
        foreach ($this->blockManagerResolver->getAll() as $blockManager) {
            if (
                !empty($options['authorized_blocks']) &&
                array_search($blockManager->getManagedBlockType(), $options['authorized_blocks']) === false
            ) {
                continue;
            }

            $blockManagers[] = $blockManager;
        }

        // Add block select type
        $builder
            ->add('block_select_type', ChoiceType::class, [
                'mapped'       => false,
                'choices'      => $blockManagers,
                'label'        => false,
                'choice_label' => function (AbstractBlockManager $value) {
                    return $this->translator->trans($value->getPublicName());
                },
                'choice_value' => function ($value) {
                    return null === $value ? '' : (new \ReflectionClass($value->getManagedBlockType()))->getShortName();
                },
                'attr'         => [
                    'data-role' => 'selecttoggle',
                ],
                'choice_attr'  => function (AbstractBlockManager $value) {
                    return [
                        'data-target' => 'type-'.(new \ReflectionClass($value->getManagedBlockType()))->getShortName(),
                        'data-name'   => $this->translator->trans($value->getPublicName()),
                    ];
                },
                'required'     => false,
                'placeholder'  => 'Add a new block',
            ])
        ;

        $blocks = $builder->create('blocks', FormType::class, [
            'compound' => true,
            'label'    => false,
        ]);

        // Adds the form associated to block types
        foreach ($blockManagers as $blockManager) {
            $blockName      = (new \ReflectionClass($blockManager->getManagedBlockType()))->getShortName();
            $blockOptions   = [
                'by_reference'  => false,
                'entry_type'    => get_class($blockManager),
                'entry_options' => ['label' => false, 'locale' => $options['locale']],
                'attr'          => [
                    'data-type' => $blockName,
                    'data-name' => $this->translator->trans($blockManager->getPublicName()),
                ],
                'label'         => false,
                'required'      => false,
                'allow_add'     => true,
                'allow_delete'  => true,
                'constraints'   => [
                    new Valid(),
                ],
            ];

            $blocks->add($blockName, CollectionType::class, $blockOptions);
        }
        $builder->add($blocks);
        $builder->addModelTransformer(new PanelDataTransformer($this->em));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['ordered_blocks'] = [];

        if ($form->getData()) {
            foreach ($form->getData()->getBlocks() as $block) {
                $view->vars['ordered_blocks'][] = [
                    'type'    => (new \ReflectionClass($block))->getShortName(),
                    'content' => $block,
                ];
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'locale'            => 'en',
            'authorized_blocks' => [],
        ]);
    }
}
