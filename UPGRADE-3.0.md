UPGRADE FROM 2.0.x to 3.0.x
===========================

Assets
------

* Remove the `{{ asset('bundles/umanitblock/xxx') }}` calls
* Declare the stimulus controller in your `assets/controllers.json` as explained in the installation part of
  the `README.md`

Twig views
----------

* If you were using the view `src/Resources/views/sonata/form/panel.html.twig`, it's gone because to obsolete. Feel free
  to open an PR to submit a new one!
* If you have overrided the view `src/Resources/views/sylius/form/panel.html.twig`, you must update it to use stimulus
  markup (controllers, targets).

Translations
------------

Translations are now using a custom domain `UmanitBlockBundle`.
