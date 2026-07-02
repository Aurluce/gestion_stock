<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$source = $root . '/docs/final/RAPPORT-PROJET-BASE-DE-DONNEES-FINAL.md';
$prepared = sys_get_temp_dir() . '/gestion_stock_final_report_prepared.md';
$reference = sys_get_temp_dir() . '/gestion_stock_docx/reference-times.docx';
$output = $root . '/docs/final/Rapport_Projet_Base_De_Donnees_Gestion_Stock_Final.docx';

if (!file_exists($reference)) {
    passthru('php ' . escapeshellarg($root . '/tools/generate_word_documents.php'), $code);
    if ($code !== 0) {
        exit($code);
    }
}

$markdown = file_get_contents($source);
if ($markdown === false) {
    fwrite(STDERR, "Unable to read final report markdown.\n");
    exit(1);
}

$markdown = str_replace("\r\n", "\n", $markdown);
$markdown = str_replace("\\newpage", pageBreak(), $markdown);
$markdown = preg_replace('/\n# (INTRODUCTION|CONCLUSION|ANNEXES)\n/', "\n" . pageBreak() . "\n# $1\n", $markdown) ?? $markdown;
$markdown = preg_replace('/\n## ([0-9]+)\. /', "\n" . pageBreak() . "\n## $1. ", $markdown) ?? $markdown;

file_put_contents($prepared, $markdown);

$cmd = sprintf(
    'pandoc %s --from=%s --to=docx --reference-doc=%s --resource-path=%s --output=%s',
    escapeshellarg($prepared),
    escapeshellarg('markdown+raw_attribute+link_attributes'),
    escapeshellarg($reference),
    escapeshellarg($root . '/docs/final'),
    escapeshellarg($output)
);

passthru($cmd, $code);
if ($code !== 0) {
    exit($code);
}

polishDocx($output);

echo "Generated: $output\n";

function pageBreak(): string
{
    return <<<MARKDOWN

```{=openxml}
<w:p><w:r><w:br w:type="page"/></w:r></w:p>
```

MARKDOWN;
}

function polishDocx(string $docx): void
{
    $zip = new ZipArchive();
    if ($zip->open($docx) !== true) {
        fwrite(STDERR, "Unable to open generated docx.\n");
        exit(1);
    }

    $document = $zip->getFromName('word/document.xml');
    if ($document !== false) {
        $document = preg_replace('/<w:pgSz[^>]*\/>\s*/', '', $document) ?? $document;
        $document = preg_replace('/<w:pgMar[^>]*\/>\s*/', '', $document) ?? $document;
        $pageLayout = '<w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="708" w:footer="708" w:gutter="0"/>';
        $document = preg_replace('/(<w:sectPr\b[^>]*>)/', '$1' . $pageLayout, $document, 1) ?? $document;
        $zip->addFromString('word/document.xml', $document);
    }

    $zip->close();
}
