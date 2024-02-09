UPGRADE FROM 2.0.x to 3.0.x
===========================

Assets
------

* Remove the `{{ asset('bundles/umanitblock/xxx') }}` calls from you Twig or the configuration file if using Sonata Admin
* Declare the stimulus controller in your `assets/controllers.json` as explained in the installation part of
  the `README.md`

Twig views
----------

* If you have overriden the view `src/Resources/views/sylius/form/panel.html.twig`, you must update it to use stimulus
  markup (controllers, targets).

Translations
------------

Translations now use a custom domain (`UmanitBlockBundle`).
