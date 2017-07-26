Using guide
===========

Installation
------------

First install bundle and add routes + js file block (`installation <https://github.com/XcoreCMS/InlineEditingBundle/blob/master/src/Resources/doc/installation.rst>`_).


How it works?
-------------

- Template extension is looking for content in php class array -> cache -> database.

- From database and cache script loads full namespace with specific locale. Locale is automatically loaded from request (or you can define it).


Examples
--------

Warning!!! There's no XSS protection. Admin can add XSS and so on...


Simple
``````

.. code-block:: twig

    {# basic usage - generate div tag #}
    {{ inline('name') }}

    {# with html attributes #}
    {{ inline('name', {attr: {class: 'super-text'}}) }}

    {# generate specifig tag #}
    {# available: h1, h2, h3, h4, h5, h6, span, strong, a, div #}
    {{ inline_h1('name-h1') }}
    {{ inline_span('name-span') }}
    {{ inline_strong('name-strong') }}

    {# override locale #}
    {{ inline('custom-locale', {locale: 'de'}) }}

    {# override namespace #}
    {{ inline('custom-namespace', {namespace: 'superNamespace'}) }}

    {# using namespace #}
    {% inline_namespace testNamespace %}
        {{ inline('using-global-namespace') }}
    {% end_inline_namespace %}

    {# include js+css files - don't forget add this line to end of body!!!  #}
    {{ inline_source() }}
..


Entity
``````

.. code-block:: php

    class TestController extends Controller
    {
        ...

        public function index()
        {
            return $this->render('index.html.twig', [
                'feed' => $this->testRepository->find(1),
                'feed2' => $this->testRepository->find(2),
            ]);
        }
    }

..

.. code-block:: twig

    {# with entity container #}
    {% inline_entity feed %}
        {# h2 without wysiwyg #}
        {{ inline_field_h2('title') }}
        {# default span without wysiwyg #}
        {{ inline_field('perex') }}
        {# default div with wysiwyg #}
        {{ inline_field_html('content') }}
        {# strong with wysiwyg #}
        {{ inline_field_html_strong('info') }}
    {% end_inline_entity %}

    {# without entity container #}
    {# h2 without wysiwyg #}
    {{ inline_entity_h2(feed, 'title') }}
    {# default span without wysiwyg #}
    {{ inline_entity(feed, 'perex') }}
    {# default div with wysiwyg #}
    {{ inline_entity_html(feed, 'content') }}
    {# strong with wysiwyg #}
    {{ inline_entity_html_strong(feed, 'info') }}

    {# include js+css files - don't forget add this line to end of body!!!  #}
    {{ inline_source() }}
..

`More info about entities <https://github.com/XcoreCMS/InlineEditingBundle/blob/master/src/Resources/doc/entity.rst>`_
