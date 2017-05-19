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

    {# using namespace - don't forget add this line to end of body!!!  #}
    {{ inline_source() }}
..
