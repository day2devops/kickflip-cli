<?php

declare(strict_types=1);

namespace Kickflip\View\Engine;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\FileEngine;
use Illuminate\View\Factory;
use Kickflip\Models\PageData;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Spatie\LaravelMarkdown\MarkdownRenderer as BaseMarkdownRenderer;

class MarkdownEngine extends FileEngine
{
    private BaseMarkdownRenderer $markdown;

    use MarkdownHelpers;

    /**
     * Create a new file engine instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files, BaseMarkdownRenderer $markdownRenderer)
    {
        $this->markdown = $markdownRenderer;
        parent::__construct($files);
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array<array-key|string, mixed>  $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        $rawMarkdown = $this->files->get($path);
        $renderedMarkdown = $this->markdown->convertToHtml($rawMarkdown);

        /*
         * The control path here looks complex but is actually rather simple:
         * IF TRUE, we wrap the results in another view; or IF NOT TRUE, we return rendered markdown directly.
         *
         * The ways we know to wrap the markdown in another view is if:
         * 1) all non-FrontMatter markdown when autoExtendMarkdown set TRUE,
         * 2) any FrontMatter markdown without `autoExpand: false` passed,
         */
        if (
            $this->autoExtendEnabled($data['site'], $data['page']) ||
            $this->pageExtendEnabled($data['page'], $renderedMarkdown)
        ) {
            return $this->makeView($data, $renderedMarkdown)->render();
        }

        return (string) $renderedMarkdown;
    }
}
