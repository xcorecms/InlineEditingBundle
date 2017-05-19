<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Twig;

use XcoreCMS\InlineEditing\Model\ContentProvider;
use XcoreCMS\InlineEditingBundle\Event\CheckInlinePermissionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingExtension extends \Twig_Extension
{
    /**
     * @var ContentProvider
     */
    private $contentProvider;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var bool|null
     */
    private $editationAllowed;

    /**
     * @param ContentProvider $contentProvider
     * @param EventDispatcherInterface $dispatcher
     * @param RouterInterface $router
     */
    public function __construct(
        ContentProvider $contentProvider,
        EventDispatcherInterface $dispatcher,
        RouterInterface $router
    ) {
        $this->contentProvider = $contentProvider;
        $this->dispatcher = $dispatcher;
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions(): array
    {
        return [
            'inline' => new \Twig_SimpleFunction(
                'inline',
                [$this, 'editableItem'],
                ['is_safe' => ['html'], 'needs_context' => true]
            ),
            'inline_dynamic' => new \Twig_SimpleFunction(
                'inline_*',
                [$this, 'editableItemDynamic'],
                ['is_safe' => ['html'], 'needs_context' => true]
            ),
            'inline_source' => new \Twig_SimpleFunction(
                'inline_source',
                [$this, 'editableSource'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getTokenParsers(): array
    {
        return ['inline_namespace' => new InlineEditingNamespaceTokenParser];
    }

    /**
     * @return string
     */
    public function editableSource(): string
    {
        return $this->isEditationAllowed() ?
            "<script src=\"/bundles/xcorecmsinlineediting/inline.js\" id=\"inline-editing-source\"
            data-source-css=\"/bundles/xcorecmsinlineediting/inline.css\"
            data-source-tinymce-js=\"/bundles/xcorecmsinlineediting/tinymce/tinymce.min.js\"
            data-source-gateway-url=\"{$this->router->generate('inline_editing')}\"></script>" : '';
    }

    /**
     * @param array $context
     * @param string $name
     * @param array $attr
     * @return string
     */
    public function editableItem(array $context, string $name, array $attr = []): string
    {
        return $this->editableItemDynamic($context, 'div', $name, $attr);
    }

    /**
     * @param array $context
     * @param string $elementTag
     * @param string $name
     * @param array $attr
     * @return string
     * @throws \Twig_Error
     */
    public function editableItemDynamic(array $context, string $elementTag, string $name, array $attr = []): string
    {
        if (!in_array($elementTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'strong', 'a', 'div'], true)) {
            throw new \Twig_Error("This tag '$elementTag' isn't allowed");
        }

        if ($elementTag === 'a' && empty($attr['attr']['href'])) {
            throw new \Twig_Error("You try use 'a' tag without href. Please specify href by {attr:{href:'link'}}");
        }

        return $this->getHtmlContent($context, $elementTag, $name, $attr);
    }

    /**
     * @param array $context
     * @param string $elementTag
     * @param string $name
     * @param array $attr
     * @return string
     */
    protected function getHtmlContent(array $context, string $elementTag, string $name, array $attr): string
    {
        $namespace = $attr['namespace'] ?? $context['_inline_namespace'] ?? '';
        $locale = $attr['locale'] ?? $context['app']->getRequest()->getLocale() ?? '';
        $content = $this->contentProvider->getContent($namespace, $locale, $name);

        $attrs = $attr['attr'] ?? [];
        $htmlAttrs = implode(' ', array_map(function ($v, $n) {
            return sprintf('%s="%s"', $n, $v);
        }, $attrs, array_keys($attrs)));

        return "<$elementTag $htmlAttrs " .
            ($this->isEditationAllowed() ?
                "data-inline-name=\"$name\" data-inline-namespace=\"$namespace\" data-inline-locale=\"$locale\"" :
                ''
            ) . ">{$content}</$elementTag>";
    }

    /**
     * @return bool
     */
    protected function isEditationAllowed(): bool
    {
        if ($this->editationAllowed === null) {
            /** @var CheckInlinePermissionEvent $event */
            $event = $this->dispatcher->dispatch(CheckInlinePermissionEvent::CHECK, new CheckInlinePermissionEvent);
            $this->editationAllowed = $event->isAllowed();
        }

        return $this->editationAllowed;
    }
}
