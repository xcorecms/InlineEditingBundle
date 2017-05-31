Installation
============


Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: bash

    $ composer require xcore/inline-editing-bundle
..


Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the ``app/AppKernel.php`` file of your project:

.. code-block:: php

    <?php
    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...

                new XcoreCMS\InlineEditingBundle\XcoreCMSInlineEditingBundle(),
            );

            // ...
        }

        // ...
    }

..


Step 3: Set routes
------------------

.. code-block:: yaml

    # app/config/routing.yml

    inline:
        resource: "@XcoreCMSInlineEditingBundle/Resources/config/routing.xml"

..


Step 4: Add js source file to twig base template
------------------------------------------------

.. code-block:: twig

    <body>
        ...
        {{ inline_source() }}
    </body>
..


Step 5: Create table
--------------------

.. code-block:: bash

    $ vendor/bin/inline dns="mysql:host=127.0.0.1;dbname=test" username=root password=pass tableName=table

    # parameters:
    #   dns - required
    #   username - required
    #   password - optional
    #   tableName - optional (default `inline_content`)
..


Step 6: Create symlink for assets
---------------------------------

If you don't have added script *Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::installAssets* in your composer, please link assets:

.. code-block:: bash

    $ php bin/console assets:install
..


Step 7: Add inline enabler (optionally)
---------------------------------------

Add subscriber for allow editing. For example:

.. code-block:: php

    class InlineEnablerSubscriber extends AbstractInlineEnablerSubscriber
    {
        /**
         * @var AuthorizationCheckerInterface
         */
        private $authorizationChecker;

        /**
         * @param AuthorizationCheckerInterface $authorizationChecker
         */
        public function __construct(AuthorizationCheckerInterface $authorizationChecker)
        {
            $this->authorizationChecker = $authorizationChecker;
        }

        /**
         * @return bool
         */
        protected function isAllowedForEditation(): bool
        {
            return $this->authorizationChecker->isGranted('ROLE_ADMIN');
        }
    }
..


Step 8: Full configuration (optional)
-------------------------------------

.. code-block:: yaml

    # app/config/config.yml

    xcore_inline:
        fallback: 'en' #default false
        table_name: 'super_table' #default inline_content
        url_path: '/ok-go' #default /inline-editing
        connection: 'doctrine.dbal.inline_connection' # default doctrine.dbal.default_connection
..


Step 9: How to use it?
----------------------

`Using guide <https://github.com/XcoreCMS/InlineEditingBundle/blob/master/src/Resources/doc/using.rst>`_
