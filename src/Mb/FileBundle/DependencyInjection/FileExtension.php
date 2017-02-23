<?php

namespace Mb\FileBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Goodby\CSV\Export\Standard\Exporter;
use Symfony\Component\HttpFoundation\StreamedResponse;


class FileExtension extends Extension
{


    public function load(array $configs, ContainerBuilder $container)
    {
    }

    public function exportFromArray(&$list){
        $response = new StreamedResponse();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=iso-8859-1');
		$response->setCharset(' iso-8859-1');
        $response->setCallback(function() use($list) {
            $config = new ExporterConfig();
            $config->setDelimiter(";")
			->setToCharset('iso-8859-1');
            $exporter = new Exporter($config);

            $exporter->export('php://output', $list);
        });
         $response->headers->set('Content-Disposition','attachment; filename="export.csv"');
        $response->send();

        return $response;
    }

    public function readFromFile($separator,$file_form,$function){
        $delemiter;
		if(is_object($separator))
			switch ($separator->getData()){
				case "1":
					$delemiter = ';';
					break;
				default:
					$delemiter = ',';
					break;
        }else{
			$delemiter = $separator;
		}
        $file = $file_form->getData();
        // Get file
        $extension = $file->guessExtension();
        if (!$extension) {
            // extension cannot be guessed
            $extension = 'bin';
        }

        $fileName = rand(1, 99999).'.'.$extension;
        $filePath;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $filePath = "tmp/getAway/";
        } else {
            $filePath = "/tmp/getAway/";
        }
        
        $file->move($filePath, $fileName);


        $config = new LexerConfig();        
        $config->setDelimiter($delemiter);
        $lexer = new Lexer($config);

        $interpreter = new Interpreter();
        $interpreter->addObserver($function);

        $lexer->parse($filePath.$fileName, $interpreter);
        unlink($filePath.$fileName);
    }
}
