# Umanit Block Bundle

Doctrine Block managment made easy.

* Simple implementation
* Flexible
* Orderable Blocks
* Database consistent
* User friendly
* Dev friendly

## Philosophy

Usually when dealing with blocks, developers lose their database consistency because they have to store many block types
in a single table. The most common way of storing many types of blocks in one single table is to store them in a json
column.

We think json is bad for database consistency and performances. Searching, indexing, managing relations, primary and
unique keys... You name it, none of them is possible with json.

UmanitBlockBundle intends to solve this problem by giving back their entities to the developers.

## Front requirements

* Use [Symfony UX](https://symfony.com/ux)

## Install

Register the bundle to your `config/bundles.php`

```php
<?php

return [
    // ...
    Umanit\BlockBundle\UmanitBlockBundle::class => ['all' => true],
];
```

Add one of the Twig's form theme

```yaml
# config/packages/twig.yaml
twig:
    form_themes:
        # When using Sylius, the only available for the moment
        - '@UmanitBlock/sylius/form/panel.html.twig'
```

Add `@umanit/block-bundle` dev-dependency in your `package.json`

```json
{
  //...
  "devDependencies": {
    // ...
    "@umanit/block-bundle": "file:vendor/umanit/block-bundle/Resources/assets"
  }
}
```

Add stimulus controllers to your `assets/controllers.json`

```json
{
  "controllers": {
    // ...
    "@umanit/block-bundle": {
      "blocks": {
        "enabled": true,
        "fetch": "lazy"
      },
      "item": {
        "enabled": true,
        "fetch": "lazy"
      },
      "sortable": {
        "enabled": true,
        "fetch": "lazy"
      }
    }
  }
  // ...
}
```

Don't forget to install the JavaScript dependencies as well and compile

```
yarn install --force
yarn encore dev
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

```php
use Umanit\BlockBundle\Form\PanelType;

$builder->add('content', PanelType::class);
```

Every block manager is available by default, if you want to filter them, you can give an option `authorized_blocks`, an
array of all the block types allowed to be selected, or `unauthorized_blocks`, an array of all the block types not
allowed to be selected.

```php
use Umanit\BlockBundle\Form\PanelType;

$builder->add('content', PanelType::class, [
    'authorized_blocks' => [MyBlock::class]
]);

$builder->add('content', PanelType::class, [
    'unauthorized_blocks' => [MyBlock::class]
]);
```

### Create a Block entity and its Block Manager

Start by creating your `Block` entity which should extends the bundle `Block` entity, like the following example:

```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Umanit\BlockBundle\Entity\Block;

/**
 * @ORM\Entity()
 */
class TitleAndText extends Block
{
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

Then, create a `Block Manager` service and it's `FormType` which should extend `AbstractBlockType`. This service will
define the form used to administrate your `Block`. It will also allow you to define the rendering of the `Block` in the
front end.

```php
<?php

namespace AppBundle\BlockManager;

use AppBundle\Entity\TitleAndText;
use AppBundle\Form\TitleAndTextType;
use Umanit\BlockBundle\Block\AbstractBlockManager;
use Umanit\BlockBundle\Model\BlockInterface;
use Twig\Environment;
use \Twig\Error\LoaderError;
use \Twig\Error\RuntimeError;
use \Twig\Error\SyntaxError;

class TitleAndTextManager extends AbstractBlockManager
{
    /** @var Environment */
    private $twig;

    /**
     * QuoteBlockManager constructor.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

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
     * This method must return the form typemanaged by this block manager.
     *
     * @return string
     */
    public function getManagedFormType(): string
    {
        return TitleAndTextType::class;
    }

    /**
     * Define how the block should be rendered on the front end.
     *
     * @param BlockInterface $block
     * @param array          $parameters
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(BlockInterface $block, array $parameters = []): string
    {
        return $this->twig->render('blocks/title-and-text.html.twig', ['block' => $block]);
    }
}
```

```php
<?php

namespace AppBundle\Form\TitleAndTextType;

use Umanit\BlockBundle\Form\AbstractBlockType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TitleAndTextType extends AbstractBlockType
{
    /**
     * Define the form used by the back end to administrate the block.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
}
```

Finally, tag your `Block Manager` with `umanit_block.manager`.

```yaml
# config/services.yml
services:
    app.block_manager.title_and_text_manager:
        class: AppBundle\BlockManager\TitleAndTextManager
        arguments: ['@twig']
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

You can pass an array of parameters to `umanit_block_render`. This parameters will be passed to the `render` method of
the `BlockManager`.

## Integration with UmanitTranslationBundle

This bundle is fully compatible with [UmanitTranslationBundle](https://github.com/umanit/translation-bundle). Once
translating a `Panel`, all the `Block` instances and their properties will also be translated. If you need a locale
parameter in you `BlockManager` form (to filter an `EntityType` for example), pass the parameter to the `PanelType` like
so:

```php
$builder->add('content', PanelType::class, ['locale' => 'be']);
```
