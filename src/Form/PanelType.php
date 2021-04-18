<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Contracts\Translation\TranslatorInterface;
use Umanit\BlockBundle\Block\AbstractBlockManager;
use Umanit\BlockBundle\Form\DataTransformer\PanelDataTransformer;
use Umanit\BlockBundle\Resolver\BlockManagerResolver;

class PanelType extends AbstractType
{
    /** @var BlockManagerResolver */
    private $blockManagerResolver;

    /** @var PanelDataTransformer */
    private $panelDataTransformer;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        BlockManagerResolver $blockManagerResolver,
        PanelDataTransformer $panelDataTransformer,
        TranslatorInterface $translator
    ) {
        $this->blockManagerResolver = $blockManagerResolver;
        $this->panelDataTransformer = $panelDataTransformer;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Filter blocks available
        $blockManagers = $this->getBlockManagers($options);

        // Add block select type
        $builder->add('block_select_type', ChoiceType::class, [
            'mapped'       => false,
            'choices'      => $blockManagers,
            'label'        => false,
            'choice_label' => function (AbstractBlockManager $value) {
                return $this->translator->trans($value->getPublicName(), [], 'UmanitBlockBundle');
            },
            'choice_value' => static function ($value) {
                return null === $value ? '' : (new \ReflectionClass($value->getManagedBlockType()))->getShortName();
            },
            'attr'         => [
                'data-role' => 'selecttoggle',
            ],
            'choice_attr'  => function (AbstractBlockManager $value) {
                return [
                    'data-target' => 'type-'.(new \ReflectionClass($value->getManagedBlockType()))->getShortName(),
                    'data-name'   => $this->translator->trans($value->getPublicName(), [], 'UmanitBlockBundle'),
                ];
            },
            'required'     => false,
            'placeholder'  => 'Add a new block',
        ]);

        $blocks = $builder->create('blocks', FormType::class, [
            'compound' => true,
            'label'    => false,
        ]);

        // Adds the form associated to block types
        foreach ($blockManagers as $blockManager) {
            $blockName = (new \ReflectionClass($blockManager->getManagedBlockType()))->getShortName();

            $blocks->add($blockName, CollectionType::class, [
                'by_reference'   => false,
                'entry_type'     => $blockManager->getManagedFormType(),
                'entry_options'  => [
                    'label'      => false,
                    'locale'     => $options['locale'],
                    'data_class' => $blockManager->getManagedBlockType(),
                ],
                'attr'           => [
                    'data-type' => $blockName,
                    'data-name' => $this->translator->trans($blockManager->getPublicName(), [], 'UmanitBlockBundle'),
                ],
                'label'          => false,
                'allow_add'      => true,
                'allow_delete'   => true,
                'prototype_name' => '__umanit_block__',
                'constraints'    => [new Valid()],
            ]);
        }

        $builder->add($blocks);
        $builder->addModelTransformer($this->panelDataTransformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['ordered_blocks'] = [];
        $blockManagers = $this->getBlockManagers($options);

        if ($form->getData()) {
            foreach ($form->getData()->getBlocks() as $block) {
                foreach ($blockManagers as $blockManager) {
                    if (\get_class($block) !== $blockManager->getManagedBlockType()) {
                        continue;
                    }

                    $view->vars['ordered_blocks'][] = [
                        'name'    => $this->translator->trans($blockManager->getPublicName(), [], 'UmanitBlockBundle'),
                        'type'    => (new \ReflectionClass($block))->getShortName(),
                        'content' => $block,
                    ];
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'locale'              => 'en',
            'authorized_blocks'   => [],
            'unauthorized_blocks' => [],
        ]);
    }

    /**
     * Returns all block managers available
     *
     * @param array $options
     *
     * @return AbstractBlockManager[]
     * @throws \ReflectionException
     */
    public function getBlockManagers(array $options): array
    {
        $blockManagers = [];

        foreach ($this->blockManagerResolver->getAll() as $blockManager) {
            if (
                !empty($options['authorized_blocks']) &&
                !\in_array($blockManager->getManagedBlockType(), $options['authorized_blocks'], true)
            ) {
                continue;
            }

            if (!empty($options['unauthorized_blocks']) &&
                \in_array($blockManager->getManagedBlockType(), $options['unauthorized_blocks'], true)
            ) {
                continue;
            }

            $blockManagers[$this->translator->trans($blockManager->getPublicName(), [], 'UmanitBlockBundle')] = $blockManager;
        }

        ksort($blockManagers);

        return $blockManagers;
    }
}
