#!/usr/bin/php
<?php
require './vendor/autoload.php';

use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract, ParserFactory};
use PhpParser\PrettyPrinter;

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
  private $translatedDict = [];
  public $unTranslated = [];

  public function __construct($translatedDict)
  {
    $this->translatedDict = $translatedDict;
  }

  public function enterNode(Node $node)
  {
    if ($node instanceof Node\Expr\ArrayItem) {
      if ($node->value->value) {
        if (isset($this->translatedDict[$node->key->value]) && strlen($this->translatedDict[$node->key->value]) > 0) {
          $node->value = new Node\Scalar\String_($this->translatedDict[$node->key->value]);
        } else {
          // not available in the translation dictionary
          array_push($this->unTranslated, $node->key->value);
        }
      } else {
        // special case
        array_push($this->unTranslated, $node->key->value);
      }
    }
  }
}

function translate($ast, $translatedDict): array
{
  $traverser = new NodeTraverser;
  $extract = new BuildDictionary($translatedDict);
  $traverser->addVisitor($extract);
  $traverser->traverse($ast);

  return [
    'ast' => $ast,
    'unTranslated' => $extract->unTranslated,
  ];
}


function scan_and_make_translation_source($input_dir) {
  $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
  $prettyPrinter = new PrettyPrinter\Standard;
  foreach (array_merge(rglob($input_dir . "*.lang.php"), rglob($input_dir . "lang_*.php"), rglob($input_dir . "*_lang.php")) as $full_path_filename) {
    $code = file_get_contents($full_path_filename);
    $ast = $parser->parse($code);
    $cleanPath = str_replace( '/', '+', substr($full_path_filename, strlen($input_dir)));
    $raw = file_get_contents('./source_translated/' . $cleanPath . '.json');
    $translatedDict = json_decode($raw, true);

    $output = translate($ast, $translatedDict);

    file_put_contents($full_path_filename, $prettyPrinter->prettyPrintFile($output['ast']));
    shell_exec('./node_modules/.bin/prettier ' . $full_path_filename . ' --write --php-version=5.3  --single-quote');

    if (count($output['unTranslated']) > 0) {
      file_put_contents('./missing/' . $cleanPath . '.txt', implode(PHP_EOL, $output['unTranslated'] ));
    }
  }
}

scan_and_make_translation_source('./DiscuzX/upload/');
