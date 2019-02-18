# Umanit Block Bundle

Doctrine Block managment made easy.

* Simple implementation
* Flexible
* Orderable Blocks
* Database consistent
* User friendly
* Dev friendly

## Philosophy

Usually when dealing with blocks, developers lose their database consistency because they have to store many block types in a single table.
The most common way of storing many types of blocks in one single table is to store them in a json column.

We think json is bad for database consistency and performances.
Searching, indexing, managing relations, primary and unique keys... you name it, none of them is possible with json.

UmanitBlockBundle intends to solve this problem by giving back their entities to the developers.

## Front requirements

* jQuery
* [jQueryUI sortable](https://jqueryui.com/sortable/)
* [FontAwesome](https://fontawesome.com/)
* jQuery [Select2](https://select2.org/) (best have)

## Install

Register the bundle to your 'app/AppKernel.php'

```php
    new Umanit\BlockBundle\UmanitBlockBundle(),
```

Add CSS and JS in global layout

```twig
  <link rel="stylesheet" href="{{ asset('bundles/umanitblock/css/panel.css') }}">
  <script src="{{ asset('bundles/umanitblock/js/panel.js') }}"></script>
```

## Usage

### Terminology

* A `Block` is a simple Doctrine entity that implements `Umanit\BlockBundle\Model\BlockInterface`.
* A `Block Manager` is a service used to administrate and render a `Block`.
* A `Panel` is a Doctrine entity that contains a collection of ordered `Block` instances.

### Create an entity containing the Panel

Usually, you'll have a content entity (here we'll call it `Page`) having one or more `Panels`.

```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Umanit\BlockBundle\Entity\Panel;

/**
 * @ORM\Entity()
 */
class Page
{
    // Your other fields...
    
    /**
     * @var Panel
     *
     * @ORM\ManyToOne(targetEntity="Umanit\BlockBundle\Entity\Panel", cascade={"persist"})
     * @ORM\JoinColumn(name="panel_id", referencedColumnName="id")
     */
    protected $content;
    
    // Getters and Setters...
}
```

Next, use the provided `PanelType` form to administrate the `Page` content.

The `PanelType` requires jQuery, jQuery UI and jQuery sortable in order to function.
The markup of the form is based on [AdminLTE](https://adminlte.io/themes/AdminLTE/index2.html).
The form type integrates natively with SonataAdmin.

```php
use Umanit\BlockBundle\Form\PanelType;

$builder->add('content', PanelType::class);
```

Every block manager is available by default, if you want to filter them, you can give an option `authorized_blocks`, an array of all the block types allowed to be selected.

```php
use Umanit\BlockBundle\Form\PanelType;

$builder->add('content', PanelType::class, [
    'authorized_blocks' => [MyBlock::class]
]);
```

### Create a Block entity and its Block Manager

Start by creating your Block entity like the following example:

```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Umanit\BlockBundle\Model\BlockInterface;
use Umanit\BlockBundle\Model\BlockTrait;

/**
 * @ORM\Entity()
 */
class TitleAndText implements BlockInterface
{
    use BlockTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;
    
    // getters and setters ...
    
    /**
     * 
     */
    public function __toString()
    {
        return $this->getTitle() ? : 'New TitleAndText';
    }
}
```
Then, create a `Block Manager` service.
This service will define the form used to administrate your `Block`.
It will also allow you to define the rendering of the `Block` in the front end.

```php
<?php

namespace AppBundle\BlockManager;

use AppBundle\Entity\TitleAndText;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Umanit\BlockBundle\Block\AbstractBlockManager;
use Umanit\BlockBundle\Model\BlockInterface;

class TitleAndTextManager extends AbstractBlockManager
{
    /**
     * Define which Block type is managed by this Manager
     *
     * @return string
     */
    public function getManagedBlockType(): string
    {
        return TitleAndText::class;
    }

    /**
     * Define the form used by the back end to administrate the block.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('text', TextareaType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    /**
     * Define how the block should be rendered on the front end.
     *
     * @param BlockInterface $block
     *
     * @return string
     */
    public function render(BlockInterface $block): string
    {
        return $this->engine->render('blocks/title-and-text.html.twig', ['block' => $block]);
    }
}
```
Finally, register your `Block Manager` as a service.

```yaml
# app/config/services.yml
services:
    app.block_manager.title_and_text_manager:
        class: AppBundle\BlockManager\TitleAndTextManager
        tags: ['umanit_block.manager']
```

### Render your blocks

Use the twig function `umanit_block_render` to render each of your blocks.

```twig
{# page.html.twig #}

{% for block in page.content.blocks %}
    {{ umanit_block_render(block) }}
{% endfor %}
```

`umanit_block_render` will find the right `BlockManager` and call its `render` method.

## Integration with UmanitTranslationBundle

This bundle is fully compatible with [UmanitTranslationBundle](https://github.com/umanit/translation-bundle).
Once translating a `Panel`, all the `Block` instances and their properties will also be translated.
If you need a locale parameter in you `BlockManager` form (to filter an `EntityType` for example), pass the parameter to the `PanelType` like so:

```php
$builder->add('content', PanelType::class, ['locale' => 'be']);
```
