<?php
declare(strict_types=1);

/**
 * Generate professional DOCX files from the project Markdown documentation.
 *
 * Requirements:
 * - pandoc available in PATH
 * - PHP ZipArchive extension
 */

$root = dirname(__DIR__);
$tmpDir = sys_get_temp_dir() . '/gestion_stock_docx';
$outDir = $root . '/docs/word';

if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0777, true);
}
if (!is_dir($outDir)) {
    mkdir($outDir, 0777, true);
}

if (!class_exists('ZipArchive')) {
    fwrite(STDERR, "ZipArchive extension is required.\n");
    exit(1);
}

if (!trim((string)shell_exec('command -v pandoc'))) {
    fwrite(STDERR, "pandoc is required.\n");
    exit(1);
}

$referenceDoc = $tmpDir . '/reference-times.docx';
createStyledReferenceDoc($referenceDoc);

$documents = [
    [
        'source' => $root . '/docs/RAPPORT-TECHNIQUE.md',
        'prepared' => $tmpDir . '/rapport-technique-prepared.md',
        'output' => $outDir . '/Rapport_Technique_Gestion_Stock.docx',
    ],
    [
        'source' => $root . '/docs/MANUEL-UTILISATION.md',
        'prepared' => $tmpDir . '/manuel-utilisation-prepared.md',
        'output' => $outDir . '/Manuel_Utilisation_Gestion_Stock.docx',
    ],
];

foreach ($documents as $doc) {
    $markdown = file_get_contents($doc['source']);
    if ($markdown === false) {
        fwrite(STDERR, "Unable to read {$doc['source']}.\n");
        exit(1);
    }

    $prepared = prepareMarkdownForWord($markdown);
    file_put_contents($doc['prepared'], $prepared);

    $cmd = sprintf(
        'pandoc %s --from=%s --to=docx --reference-doc=%s --resource-path=%s --output=%s',
        escapeshellarg($doc['prepared']),
        escapeshellarg('markdown+raw_attribute+link_attributes'),
        escapeshellarg($referenceDoc),
        escapeshellarg($root),
        escapeshellarg($doc['output'])
    );

    passthru($cmd, $code);
    if ($code !== 0) {
        fwrite(STDERR, "Pandoc failed for {$doc['source']}.\n");
        exit($code);
    }

    polishDocx($doc['output']);
    echo "Generated: {$doc['output']}\n";
}

function prepareMarkdownForWord(string $markdown): string
{
    $markdown = str_replace("\r\n", "\n", $markdown);

    // Isolate the cover page from the table of contents.
    $markdown = preg_replace('/\n## Sommaire\n/', "\n" . pageBreak() . "\n## Sommaire\n", $markdown, 1) ?? $markdown;

    // Start every major numbered section on a new page.
    $markdown = preg_replace('/\n## ([0-9]+)\. /', "\n" . pageBreak() . "\n## $1. ", $markdown) ?? $markdown;

    // Keep code-block comments from being interpreted as headings by simple viewers.
    return $markdown;
}

function pageBreak(): string
{
    return <<<MARKDOWN

```{=openxml}
<w:p><w:r><w:br w:type="page"/></w:r></w:p>
```

MARKDOWN;
}

function createStyledReferenceDoc(string $referenceDoc): void
{
    $defaultDoc = shell_exec('pandoc --print-default-data-file reference.docx');
    if ($defaultDoc === null || $defaultDoc === '') {
        fwrite(STDERR, "Unable to get pandoc default reference.docx.\n");
        exit(1);
    }

    file_put_contents($referenceDoc, $defaultDoc);

    $zip = new ZipArchive();
    if ($zip->open($referenceDoc) !== true) {
        fwrite(STDERR, "Unable to open reference docx.\n");
        exit(1);
    }

    $styles = $zip->getFromName('word/styles.xml');
    if ($styles === false) {
        fwrite(STDERR, "word/styles.xml not found in reference docx.\n");
        exit(1);
    }

    $styles = styleDocumentXml($styles);
    $zip->addFromString('word/styles.xml', $styles);
    $zip->close();
}

function polishDocx(string $docx): void
{
    $zip = new ZipArchive();
    if ($zip->open($docx) !== true) {
        fwrite(STDERR, "Unable to open generated docx: $docx\n");
        exit(1);
    }

    $document = $zip->getFromName('word/document.xml');
    if ($document !== false) {
        $zip->addFromString('word/document.xml', polishDocumentXml($document));
    }

    $settings = $zip->getFromName('word/settings.xml');
    if ($settings !== false && !str_contains($settings, '<w:updateFields')) {
        $settings = str_replace('</w:settings>', '<w:updateFields w:val="true"/></w:settings>', $settings);
        $zip->addFromString('word/settings.xml', $settings);
    }

    $zip->close();
}

function styleDocumentXml(string $xml): string
{
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = false;
    $dom->loadXML($xml);

    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

    $styles = $xpath->query('//w:style');
    foreach ($styles as $style) {
        if (!$style instanceof DOMElement) {
            continue;
        }

        $type = $style->getAttributeNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'type');
        $styleId = $style->getAttributeNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'styleId');

        if ($type === 'paragraph') {
            $pPr = getOrCreateChild($dom, $style, 'w:pPr');
            $spacing = getOrCreateChild($dom, $pPr, 'w:spacing');
            setWAttr($spacing, 'line', '360');
            setWAttr($spacing, 'lineRule', 'auto');
            setWAttr($spacing, 'after', in_array($styleId, ['Title', 'Subtitle'], true) ? '240' : '120');
            setWAttr($spacing, 'before', str_starts_with($styleId, 'Heading') ? '240' : '0');

            $jc = null;
            if ($styleId === 'Title' || $styleId === 'Subtitle') {
                $jc = getOrCreateChild($dom, $pPr, 'w:jc');
                setWAttr($jc, 'val', 'center');
            }
        }

        $rPr = getOrCreateChild($dom, $style, 'w:rPr');
        applyRunStyle($dom, $rPr, fontSizeForStyle($styleId), str_starts_with($styleId, 'Heading') || $styleId === 'Title');
    }

    $docDefaults = getOrCreatePath($dom, $dom->documentElement, ['w:docDefaults', 'w:rPrDefault', 'w:rPr']);
    applyRunStyle($dom, $docDefaults, '24', false);

    $pDefaults = getOrCreatePath($dom, $dom->documentElement, ['w:docDefaults', 'w:pPrDefault', 'w:pPr']);
    $spacing = getOrCreateChild($dom, $pDefaults, 'w:spacing');
    setWAttr($spacing, 'line', '360');
    setWAttr($spacing, 'lineRule', 'auto');
    setWAttr($spacing, 'after', '120');

    return $dom->saveXML();
}

function polishDocumentXml(string $xml): string
{
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = false;
    $dom->loadXML($xml);

    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

    $sectPr = $xpath->query('//w:body/w:sectPr')->item(0);
    if ($sectPr instanceof DOMElement) {
        $pgSz = getOrCreateChild($dom, $sectPr, 'w:pgSz');
        setWAttr($pgSz, 'w', '11906');
        setWAttr($pgSz, 'h', '16838');

        $pgMar = getOrCreateChild($dom, $sectPr, 'w:pgMar');
        setWAttr($pgMar, 'top', '1440');
        setWAttr($pgMar, 'right', '1440');
        setWAttr($pgMar, 'bottom', '1440');
        setWAttr($pgMar, 'left', '1440');
        setWAttr($pgMar, 'header', '708');
        setWAttr($pgMar, 'footer', '708');
        setWAttr($pgMar, 'gutter', '0');
    }

    // Ensure direct paragraph content also follows 1.5 line spacing.
    foreach ($xpath->query('//w:body/w:p') as $p) {
        if (!$p instanceof DOMElement) {
            continue;
        }
        $pPr = getOrCreateChild($dom, $p, 'w:pPr', true);
        $spacing = getOrCreateChild($dom, $pPr, 'w:spacing');
        setWAttr($spacing, 'line', '360');
        setWAttr($spacing, 'lineRule', 'auto');
    }

    return $dom->saveXML();
}

function applyRunStyle(DOMDocument $dom, DOMElement $rPr, string $size, bool $bold): void
{
    $fonts = getOrCreateChild($dom, $rPr, 'w:rFonts');
    setWAttr($fonts, 'ascii', 'Times New Roman');
    setWAttr($fonts, 'hAnsi', 'Times New Roman');
    setWAttr($fonts, 'eastAsia', 'Times New Roman');
    setWAttr($fonts, 'cs', 'Times New Roman');
    removeWAttr($fonts, 'asciiTheme');
    removeWAttr($fonts, 'hAnsiTheme');
    removeWAttr($fonts, 'eastAsiaTheme');
    removeWAttr($fonts, 'cstheme');
    removeWAttr($fonts, 'csTheme');

    $sz = getOrCreateChild($dom, $rPr, 'w:sz');
    setWAttr($sz, 'val', $size);
    $szCs = getOrCreateChild($dom, $rPr, 'w:szCs');
    setWAttr($szCs, 'val', $size);

    if ($bold) {
        getOrCreateChild($dom, $rPr, 'w:b');
        getOrCreateChild($dom, $rPr, 'w:bCs');
    }
}

function fontSizeForStyle(string $styleId): string
{
    return match ($styleId) {
        'Title' => '36',
        'Subtitle' => '26',
        'Heading1' => '32',
        'Heading2' => '28',
        'Heading3' => '24',
        default => '24',
    };
}

function getOrCreatePath(DOMDocument $dom, DOMElement $parent, array $path): DOMElement
{
    $current = $parent;
    foreach ($path as $name) {
        $current = getOrCreateChild($dom, $current, $name);
    }
    return $current;
}

function getOrCreateChild(DOMDocument $dom, DOMElement $parent, string $qualifiedName, bool $prepend = false): DOMElement
{
    [$prefix, $localName] = explode(':', $qualifiedName, 2);
    $namespace = $prefix === 'w'
        ? 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'
        : null;

    foreach ($parent->childNodes as $child) {
        if ($child instanceof DOMElement && $child->localName === $localName && $child->namespaceURI === $namespace) {
            return $child;
        }
    }

    $element = $dom->createElementNS($namespace, $qualifiedName);
    if ($prepend && $parent->firstChild) {
        $parent->insertBefore($element, $parent->firstChild);
    } else {
        $parent->appendChild($element);
    }
    return $element;
}

function setWAttr(DOMElement $element, string $name, string $value): void
{
    $element->setAttributeNS(
        'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
        'w:' . $name,
        $value
    );
}

function removeWAttr(DOMElement $element, string $name): void
{
    $element->removeAttributeNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', $name);
}

