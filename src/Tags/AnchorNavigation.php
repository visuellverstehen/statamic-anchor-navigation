<?php

namespace VV\AnchorNavigation\Tags;

use Statamic\Entries\Entry;
use Statamic\Facades\Config;
use Statamic\Fieldtypes\Bard\Augmentor;
use Statamic\Tags\Tags;

class AnchorNavigation extends Tags
{
    protected static $handle = 'anchor_navigation';

    protected ?string $fieldHandle;
    protected $entry;
    protected Augmentor $augmentor;
    protected ?array $value;

    /*
     * In order to generate an anchor navigation
     * you need to provide the field handle for a bard field.
     *
     * {{ anchor_navigation from="handle" entry="entry" }}
     */
    public function index(): ?array
    {
        if (! $this->fieldHandle = $this->params->get('from')) {
            return null;
        }

        if (! $this->entry = $this->getEntry()) {
            return null;
        }

        if (! $this->value = $this->entry->value($this->fieldHandle)) {
            return null;
        }

        $this->augmentor = $this->defineAugmentor();

        return $this->collectHeadings();
    }
    
    public function count(): int
    {
        $headings = $this->index() ?? [];
            
        return count($headings);
    }

    private function defineAugmentor(): Augmentor
    {
        $bard = $this->entry
            ->blueprint()
            ->field($this->fieldHandle)
            ->fieldtype();

        return new Augmentor($bard);
    }

    protected function allowedHeadingLevels(): array
    {
        return config('anchor-navigation.heading.levels', []);
    }

    protected function getId(string $html): ?string
    {
        if (preg_match('/id="([^"]+)"/', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function collectHeadings(): array
    {
        return collect($this->value)
            ->map(function ($item) {
                if (! array_key_exists('type', $item) || $item['type'] !== 'heading') {
                    return [];
                }

                if (! in_array($item['attrs']['level'], $this->allowedHeadingLevels())) {
                    return [];
                }

                $htmlHeading = $this->augmentor->convertToHtml([$item]);

                if (! $id = $this->getId($htmlHeading)) {
                    return [];
                }

                return [
                    'id' => $id,
                    'headline' => $this->sanitize($item['content'][0]['text']),
                    'level' => $item['attrs']['level'],
                ];
            })
            ->filter()
            ->toArray();
    }

    /*
     * This logic has been taken right out of the Statamic cores 'sanitize'-modifier.
     * @see vendor/statamic/cms/src/Support/Html.php
     */
    protected function sanitize($string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, Config::get('statamic.system.charset', 'UTF-8'));
    }


    /**
     * This method makes it possible to create an Anchor navigation not only
     * from the current context, but also by passing another entry.
     */
    private function getEntry(): ?Entry
    {
        return $this->params->get('entry')
            ? $this->params->get('entry')
            : $this->context->get('id')?->augmentable();
    }
}
