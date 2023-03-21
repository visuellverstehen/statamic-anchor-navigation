# Statamic Anchor Navigation

> Statamic Anchor Navigation is a Statamic addon that helps you build a server side anchor navigation from your bard content. 

## Features

This addon does:

- Extends the TipTap Heading Node and adds a slugified ID to all heading levels of your choice.
- The 'anchor_navigation'-tag makes it easy to build an anchor navigation from headings in your bard content.

## How to Install

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require visuellverstehen/statamic-anchor-navigation
```

## How to Use

Include the anchor_navigation tag in your template and provide it with the field handle of your bard field.
```
<ul>
    {{ anchor_navigation from="bard" }}
        <li>
            <a href="#{{ id }}">{{ headline }}</a>
        </li>
    {{ /anchor_navigation }}
</ul>
```

## Configurations

You can define which heading levels should be included in your anchor navigation. 
Level 2 headings are set as a default.
```
'heading' => [
    'levels' => [2],
],
```
