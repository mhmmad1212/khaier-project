<?php

namespace App\Services;

use App\Models\PageTemplate;
use Illuminate\Support\Facades\File;

class TemplateFileGenerator
{
    public static function generate(PageTemplate $template): void
    {
        $pageType = trim((string) ($template->page_type ?: 'home'));
        $safePageType = preg_replace('/[^A-Za-z0-9_-]/', '_', $pageType);

        $templateId = (int) $template->id;
        $fileBaseName = 'template_' . $templateId;

        $folder = resource_path('views/generated-templates/' . $safePageType);

        if (! File::exists($folder)) {
            File::makeDirectory($folder, 0775, true);
        }

        $filePath = $folder . '/' . $fileBaseName . '.blade.php';
        $viewPath = 'generated-templates.' . $safePageType . '.' . $fileBaseName;

        $content = (string) ($template->template_content ?? '');

        $css = trim((string) ($template->template_css ?? ''));
        if ($css !== '') {
            $styleBlock = "\n<style>\n{$css}\n</style>\n";
            if (stripos($content, '</head>') !== false) {
                $content = preg_replace('/<\/head>/i', $styleBlock . '</head>', $content, 1);
            } else {
                $content = $styleBlock . $content;
            }
        }

        $js = trim((string) ($template->template_js ?? ''));
        if ($js !== '') {
            $scriptBlock = "\n<script>\n{$js}\n</script>\n";
            if (stripos($content, '</body>') !== false) {
                $content = preg_replace('/<\/body>/i', $scriptBlock . '</body>', $content, 1);
            } else {
                $content .= $scriptBlock;
            }
        }

        File::put($filePath, $content);

        if ($template->view_path !== $viewPath) {
            $template->view_path = $viewPath;
            $template->saveQuietly();
        }
    }
}
