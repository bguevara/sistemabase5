<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportarDatosCommand extends Command
{
    protected static $defaultName = 'app:importar:datos';
    protected static $defaultDescription = 'Add a short description for your command';

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $urlConexion="https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml";
        $xml = new \SimpleXMLElement($this->get_curl($urlConexion));
        foreach ( $xml->children() as $child ) {
          foreach ( $child->Cube as $child2 ) {
            $output->writeln('---------------------La fecha es------------:'.$child2['time']);

            foreach ( $child2->Cube as $child3 ) {
                $output->writeln('La currency es:'.$child3['currency']);
                $output->writeln('La tasa es:'.$child3['rate']);

            }   
          }
        }   
        $io->success('Recorriendo las tasas');
        return Command::SUCCESS;
    }

    public function get_curl($urlconexion)
    {
        // Prepare new cURL resource
        $ch = curl_init($urlconexion);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_TIMEOUT, '30L');
        // Set HTTP Header for POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
            )
        );
        // Submit the POST request
        $respuesta = curl_exec($ch);
        curl_close($ch);
        return $respuesta;
    }
}
