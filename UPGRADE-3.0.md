UPGRADE FROM 2.0 to 3.0
=======================

Assets
------

* Remove the `{{ asset('bundles/umanitblock/xxx') }}` calls
* Declare the stimulus controller in your `assets/controllers.json` as explained in the installation part of
  the `README.md`

Twig views
----------

* If you were using the view `src/Resources/views/sonata/form/panel.html.twig`, it's gone because to obsolete. Feel free
  to open an PR to submit a new one!
