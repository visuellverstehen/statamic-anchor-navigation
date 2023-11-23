<?php

namespace VV\AnchorNavigation\Nodes;

use Statamic\Support\Str;
use Tiptap\Nodes\Heading as TipTapHeading;
use Tiptap\Utils\HTML;

class Heading extends TipTapHeading
{
    public function renderHTML($node, $HTMLAttributes = [])
    {
        $hasLevel = in_array($node->attrs->level, $this->options['levels']);

        $level = $hasLevel ?
            $node->attrs->level :
            $this->options['levels'][0];

        // Add slugified id to specific headlines.
        if (in_array($node->attrs->level, config('anchor-navigation.heading.levels', []))) {

            if (property_exists($node->content[0], 'text') && !is_null($node->content[0]->text)) {
                $HTMLAttributes['id'] = Str::slug($node->content[0]->text);
            }
        }

        return [
            "h{$level}",
            HTML::mergeAttributes($this->options['HTMLAttributes'], $HTMLAttributes),
            0,
        ];
    }
}
