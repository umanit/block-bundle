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

* Use [Symfony UX](https://symfony.com/doc/current/frontend/ux.html)

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
        # When using Sylius
        - '@UmanitBlock/sylius/form/panel.html.twig'
        # Read further for integration with EasyAdmin 4
```

Add `@umanit/block-bundle` dev-dependency in your `package.json`. This part is automatically done if you use Flex in
your projet.

```json
{
  //...
  "devDependencies": {
    // ...
    "@umanit/block-bundle": "file:vendor/umanit/block-bundle/Resources/assets"
  }
}
```

Add stimulus controllers to your `assets/controllers.json`. This part is automatically done if you use Flex in your
projet.

```json
{
  "controllers": {
    // ...
    "@umanit/block-bundle": {
      "blocks": {
        "enabled": true,
        "fetch": "eager"
      },
      "item": {
        "enabled": true,
        "fetch": "eager"
      },
      "sortable": {
        "enabled": true,
        "fetch": "eager"
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

### Warning

Your Stimulus app must be running on your back end if you want to use this bundle: make sure the `bootstrap.js` file
that starts it is imported.
Your script must be loaded using `encore_entry_script_tags()`.

Here are exemples on how to do that depending on your back end library, given an `admin.js` file on your end:

### EasyAdmin 4

In your `DashboardController`, you can do it this way:

```php
class DashboardController extends AbstractDashboardController
{   
    public function configureAssets(): Assets
    {
        return parent::configureAssets()
                     ->addWebpackEncoreEntry('admin')
        ;
    }
}
```

### Sonata Admin 4

## Configuration

```yaml
sonata_admin:
    templates:
        form_theme:
            - 'admin/sonata_form_theme.html.twig'
    assets:
        extra_javascripts:
            - 'build/admin/app.js'
```

* `app.js` needs to import your `bootstrap.js`, that loads both your own Stimulus controllers and those of Block Bundle
  in your Stimulus backend application
    * if using CKEditor, see [the dedicated documentation](ckeditor.md)

You'll have to modify your `webpack.config.js` to allow your Stimulus controllers to work in your Sonata backend:

```js
// DO
Encore.disableSingleRuntimeChunk();

// DON'T
Encore
  .splitEntryChunks()
  .enableSingleRuntimeChunk();
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
 #[ORM\Entity]
class Page
{
    // Your other fields...
    
    /**
     * @var Panel
     *
     * @ORM\ManyToOne(targetEntity="Umanit\BlockBundle\Entity\Panel", cascade={"persist"})
     * @ORM\JoinColumn(name="panel_id", referencedColumnName="id")
     */
    #[ORM\ManyToOne(targetEntity: 'Umanit\BlockBundle\Entity\Panel', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'panel_id', referencedColumnName: 'id')]
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

**[Read further](#integration-with-easyadmin-4) for integration with EasyAdmin 4 and defining a `PanelField` in your
CRUDController**

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
 #[ORM\Entity]
class TitleAndText extends Block
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    #[ORM\Column(name: 'title', type: 'string')]
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    #[ORM\Column(name: 'text', type: 'text')]
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

Then, create a `Block Manager` service and its `FormType` which should extend `AbstractBlockType`. This service will
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

## Embedding a panel within a collection item

Block Bundle uses Stimulus, so you don't need to do anything on your end: controllers will be instantiated as they're
added to the DOM.

## Integration with UmanitTranslationBundle

This bundle is fully compatible with [UmanitTranslationBundle](https://github.com/umanit/translation-bundle). Once
translating a `Panel`, all the `Block` instances and their properties will also be translated. If you need a locale
parameter in you `BlockManager` form (to filter an `EntityType` for example), pass the parameter to the `PanelType` like
so:

```php
$builder->add('content', PanelType::class, ['locale' => 'be']);
```

## Integration with EasyAdmin 4

### Registering the provided form theme

In your `DashboardController`:

```php
class DashboardController extends AbstractDashboardController
{
    public function configureCrud(): Crud
    {
        return Crud::new()
                   // ...
                   ->setFormThemes([
                       // ...
                       '@UmanitBlock/easy_admin/form/panel.html.twig'
                   ])
        ;
    }
}
```

### Using the `PanelField`

If defining a `CrudController`, you can use the provided `PanelField`:

```php
public function configureFields(string $pageName): iterable
{
    yield PanelField::new('content');
}
```

As with `PanelType`, you can define either `authorized_blocks` or `unauthorized_blocks`
if you need to restrict the available block list to some chosen options.

```php
                    ->setFormTypeOption('authorized_blocks', [MyBlock::class])
                    // or
                    ->setFormTypeOption('unauthorized_blocks', [MyBlock::class])
```

### Using a form type from EasyAdmin within a block

You might want to use `FileUploadType`, for example, in your blocks.
Block Bundle only works with Symfony form types, which means you will not be able to use EA fields in them and leverage
their powerful configurators. You can, however, still use the associated form type in your block form type but you might
need to get your hands dirty with the options.

The `block.js` Stimulus controllers dispatches the `ea.collection.item-added` after a block has been added to the DOM,
so EA JS will be bound to it.

## CKEditor

[Read the dedicated CKEditor documentation if you want to use it in your blocks](_doc/ckeditor.md)
