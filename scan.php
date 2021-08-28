#!/usr/bin/php
<?php
require './vendor/autoload.php';

use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract, ParserFactory};

define('IN_DISCUZ', true);

function rglob($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}



class BuildDictionary extends NodeVisitorAbstract
{
    public $output = [];

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\ArrayItem) {
            $this->output[$node->key->value] = $node->value->value;
        }
    }
}

function build_tran_dictionary($ast): array
{
    $traverser = new NodeTraverser;
    $extract = new BuildDictionary();
    $traverser->addVisitor($extract);
    $traverser->traverse($ast);
    return $extract->output;
}

function scan_and_make_translation_source($input_dir, $output_dir) {
    $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    foreach (array_merge(rglob($input_dir . "*.lang.php"), rglob($input_dir . "lang_*.php")) as $full_path_filename) {
        $code = file_get_contents($full_path_filename);
        $ast = $parser->parse($code);
        $tran_dictionary = build_tran_dictionary($ast);
        $file_path = substr($full_path_filename, strlen($input_dir));
        $out_file = str_replace('/', '+', $file_path);
        file_put_contents($output_dir . $out_file . '.json', json_encode($tran_dictionary, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}

scan_and_make_translation_source('./discuzviethoa/', './source_ref/');
scan_and_make_translation_source('./DiscuzX/upload/', './source_china/');