<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Twig;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Twig\Error\Error;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use XcoreCMS\InlineEditing\Model\Simple\ContentProvider;
use XcoreCMS\InlineEditingBundle\Event\CheckInlinePermissionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingExtension extends AbstractExtension
{
    /** @var string */
    private $defaultNamespace;

    /** @var ContentProvider */
    private $contentProvider;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var RouterInterface */
    private $router;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    /** @var bool|null */
    private $editationAllowed;

    public function __construct(
        string $defaultNamespace,
        ContentProvider $contentProvider,
        EventDispatcherInterface $dispatcher,
        RouterInterface $router,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->defaultNamespace = $defaultNamespace;
        $this->contentProvider = $contentProvider;
        $this->dispatcher = $dispatcher;
        $this->router = $router;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $htmlOptions = ['is_safe' => ['html']];
        $fullOptions = ['is_safe' => ['html'], 'needs_context' => true];

        return [
            // entity
            new TwigFunction('inline_entity', [$this, 'editableEntity'], $htmlOptions),
            new TwigFunction('inline_entity_*', [$this, 'editableEntityDynamic'], $htmlOptions),
            // entity html
            new TwigFunction('inline_entity_html', [$this, 'editableEntityHtml'], $htmlOptions),
            new TwigFunction('inline_entity_html_*', [$this, 'editableEntityHtmlDynamic'], $htmlOptions),
            // entity field
            new TwigFunction('inline_field', [$this, 'editableEntityField'], $fullOptions),
            new TwigFunction('inline_field_*', [$this, 'editableEntityFieldDynamic'], $fullOptions),
            // entity field
            new TwigFunction('inline_field_html', [$this, 'editableEntityHtmlField'], $fullOptions),
            new TwigFunction('inline_field_html_*', [$this, 'editableEntityHtmlFieldDynamic'], $fullOptions),
            // simple
            new TwigFunction('inline', [$this, 'editableItem'], $fullOptions),
            new TwigFunction('inline_*', [$this, 'editableItemDynamic'], $fullOptions),
            // source
            new TwigFunction('inline_source', [$this, 'editableSource'], $htmlOptions),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers(): array
    {
        return [new InlineEditingNamespaceTokenParser(), new InlineEditingEntityTokenParser()];
    }

    /* ==== ENTITY ==== */

    /**
     * @param mixed $entity
     * @param string $property
     * @param array $attr
     * @return string
     */
    public function editableEntity($entity, string $property, array $attr = []): string
    {
        return $this->editableEntityDynamic('span', $entity, $property, $attr);
    }


    /**
     * @param string $elementTag
     * @param mixed $entity
     * @param string $property
     * @param array $attr
     * @param bool $specific
     * @return string
     * @throws Error
     */
    public function editableEntityDynamic(
        string $elementTag,
        $entity,
        string $property,
        array $attr = [],
        bool $specific = true
    ): string {
        if (
            $specific === false &&
            !in_array($elementTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'strong', 'div'], true)
        ) {
            throw new Error("This tag '$elementTag' isn't allowed");
        }

        $type = $specific === true ? 'entity-specific' : 'entity';
        $class = get_class($entity);
        $id = $this->propertyAccessor->getValue($entity, 'id');
        $content = $this->propertyAccessor->getValue($entity, $property);

        $attrs = $attr['attr'] ?? [];
        $htmlAttrs = implode(' ', array_map(static function ($v, $n) {
            return sprintf('%s="%s"', $n, $v);
        }, $attrs, array_keys($attrs)));

        return "<$elementTag $htmlAttrs " .
            ($this->isEditationAllowed() ?
                "id=\"inline_{$class}_{$id}_{$property}\"" .
                "data-inline-type=\"{$type}\" data-inline-entity=\"$class\" " .
                "data-inline-id=\"$id\" data-inline-property=\"$property\"" :
                ''
            ) . ">{$content}</$elementTag>";
    }

    /**
     * @param mixed $entity
     * @param string $property
     * @param array $attr
     * @return string
     */
    public function editableEntityHtml($entity, string $property, array $attr = []): string
    {
        return $this->editableEntityDynamic('div', $entity, $property, $attr, false);
    }

    /**
     * @param string $elementTag
     * @param mixed $entity
     * @param string $property
     * @param array $attr
     * @return string
     */
    public function editableEntityHtmlDynamic(string $elementTag, $entity, string $property, array $attr = []): string
    {
        return $this->editableEntityDynamic($elementTag, $entity, $property, $attr, false);
    }

    /**
     * @param array $context
     * @param string $property
     * @param array $attr
     * @return string
     */
    public function editableEntityField(array $context, string $property, array $attr = []): string
    {
        return $this->editableEntityDynamic('span', $context['_inline_entity'], $property, $attr);
    }

    /**
     * @param array $context
     * @param string $elementTag
     * @param string $property
     * @param array $attr
     * @return string
     */
    public function editableEntityFieldDynamic(
        array $context,
        string $elementTag,
        string $property,
        array $attr = []
    ): string {
        return $this->editableEntityDynamic($elementTag, $context['_inline_entity'], $property, $attr);
    }

    /**
     * @param array $context
     * @param string $property
     * @param array $attr
     * @return string
     */
    public function editableEntityHtmlField(array $context, string $property, array $attr = []): string
    {
        return $this->editableEntityDynamic('div', $context['_inline_entity'], $property, $attr, false);
    }

    /**
     * @param array $context
     * @param string $elementTag
     * @param string $property
     * @param array $attr
     * @return string
     */
    public function editableEntityHtmlFieldDynamic(
        array $context,
        string $elementTag,
        string $property,
        array $attr = []
    ): string {
        return $this->editableEntityDynamic($elementTag, $context['_inline_entity'], $property, $attr, false);
    }

    /* ==== SIMPLE ==== */

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
     * @throws Error
     */
    public function editableItemDynamic(array $context, string $elementTag, string $name, array $attr = []): string
    {
        if (!in_array($elementTag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'strong', 'a', 'div'], true)) {
            throw new Error("This tag '$elementTag' isn't allowed");
        }

        if ($elementTag === 'a' && empty($attr['attr']['href'])) {
            throw new Error("You try use 'a' tag without href. Please specify href by {attr:{href:'link'}}");
        }

        return $this->getHtmlContent($context, $elementTag, $name, $attr);
    }

    /* ==== BASE ==== */

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
     * @param string $elementTag
     * @param string $name
     * @param array $attr
     * @return string
     */
    protected function getHtmlContent(array $context, string $elementTag, string $name, array $attr): string
    {
        $namespace = $attr['namespace'] ?? $context['_inline_namespace'] ?? $this->defaultNamespace;
        $locale = $attr['locale'] ?? $context['app']->getRequest()->getLocale() ?? '';
        $content = $this->contentProvider->getContent($namespace, $locale, $name);

        $attrs = $attr['attr'] ?? [];
        $htmlAttrs = implode(' ', array_map(static function ($v, $n) {
            return sprintf('%s="%s"', $n, $v);
        }, $attrs, array_keys($attrs)));

        return "<$elementTag $htmlAttrs " .
            ($this->isEditationAllowed() ?
                "data-inline-type=\"simple\" data-inline-name=\"$name\" " .
                "data-inline-namespace=\"$namespace\" data-inline-locale=\"$locale\"" :
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
            $event = $this->dispatcher->dispatch(new CheckInlinePermissionEvent());
            $this->editationAllowed = $event->isAllowed();
        }

        return $this->editationAllowed;
    }
}
