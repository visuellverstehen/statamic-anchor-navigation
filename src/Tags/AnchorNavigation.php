<?php

namespace VV\AnchorNavigation\Tags;

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
     * {{ anchor_navigation from="handle" }}
     */
    public function index(): ?array
    {
        if (! $this->fieldHandle = $this->params->get('from')) {
            return null;
        }

        if (! $this->entry = $this->context->get('entry_id')->augmentable()) {
            return null;
        }

        if (! $this->value = $this->entry->get($this->fieldHandle)) {
            return null;
        }

        $this->augmentor = self::defineAugmentor();

        return self::collectHeadings();
    }

    private function defineAugmentor(): Augmentor
    {
        $bard = $this->entry
            ->blueprint()
            ->field($this->fieldHandle)
            ->fieldtype();

        return new Augmentor($bard);
    }

    private function allowedHeadingLevels(): array
    {
        return config('anchor-navigation.heading.levels', []);
    }

    private function getId(string $html): ?string
    {
        if (preg_match('/id="([^"]+)"/', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function collectHeadings(): array
    {
        return collect($this->value)
            ->map(function ($item) {
                if (! array_key_exists('type', $item) || $item['type'] !== 'heading') {
                    return [];
                }

                if (! in_array($item['attrs']['level'], self::allowedHeadingLevels())) {
                    return [];
                }

                $htmlHeading = $this->augmentor->convertToHtml([$item]);

                if (! $id = self::getId($htmlHeading)) {
                    return [];
                }

                return [
                    'id' => $id,
                    'headline' => self::sanitize($item['content'][0]['text']),
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
    private function sanitize($string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, Config::get('statamic.system.charset', 'UTF-8'));
    }
}
