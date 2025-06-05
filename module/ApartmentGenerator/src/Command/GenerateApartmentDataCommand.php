<?php

namespace ApartmentGenerator\Command;

use Laminas\Cli\Command\AbstractParamAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateApartmentDataCommand extends AbstractParamAwareCommand
{
    protected static $defaultName = 'apartment:generate';

    protected function configure()
    {
        $this->setDescription('Generates sample apartment data with image URLs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sampleData = $this->generateSampleData(5);

        $output->writeln(json_encode($sampleData, JSON_PRETTY_PRINT));
        return 0;
    }

    private function generateSampleData(int $count): array
    {
        $names = ['The Gate London City', 'Native Bankside', 'Citadines Trafalgar Square', 'Marlin Aldgate Tower Bridge', 'SACO Fitzrovia'];
        $locations = ['Aldgate', 'Southwark', 'Westminster', 'Whitechapel', 'Fitzrovia'];
        $types = ['Studio | 1 Bed', '1 Bed | 2 Bed', '1 Bed | 2 Bed | 3 Bed', 'Studio | 1 Bed | 2 Bed', '1 Bed | Others'];
        $descriptions = [
            "Cove Landmark Pinnacle provides corporate serviced accommodation in Liverpool, within easy reach of public transport and city amenities. These studio, one, and two bedroom apartments feature fully equipped kitchens, Smart TVs, washer/dryers, and weekly housekeeping. Some units offer air conditioning, balconies, or access to on-site gyms, lounges, or communal gardens.",
            "The Gate London City provides corporate serviced accommodation in Glasgow, within easy reach of public transport and city amenities. These studio, one, and two bedroom apartments feature fully equipped kitchens, Smart TVs, washer/dryers, and weekly housekeeping. Some units offer air conditioning, balconies, or access to on-site gyms, lounges, or communal gardens.",
            "Westvale Urban Suites provides corporate serviced accommodation in Brighton, within easy reach of public transport and city amenities. These studio, one, and two bedroom apartments feature fully equipped kitchens, Smart TVs, washer/dryers, and weekly housekeeping. Some units offer air conditioning, balconies, or access to on-site gyms, lounges, or communal gardens.",
            "Beckford Quay Residences provides corporate serviced accommodation in Manchester, within easy reach of public transport and city amenities. These studio, one, and two bedroom apartments feature fully equipped kitchens, Smart TVs, washer/dryers, and weekly housekeeping. Some units offer air conditioning, balconies, or access to on-site gyms, lounges, or communal gardens.",
            "Larchway Heights provides corporate serviced accommodation in London, within easy reach of public transport and city amenities. These studio, one, and two bedroom apartments feature fully equipped kitchens, Smart TVs, washer/dryers, and weekly housekeeping. Some units offer air conditioning, balconies, or access to on-site gyms, lounges, or communal gardens."
        ];
        $images = [
            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxfHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1515263487990-61b07816b324?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyfHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwzfHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1460317442991-0ec209397118?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw0fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw1fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1523192193543-6e7296d960e4?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw2fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw3fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1502672023488-70e25813eb80?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw4fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw5fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1459767129954-1b1c1f9b9ace?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxMHx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxMXx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1580216643062-cf460548a66a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxMnx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1628592102751-ba83b0314276?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxM3x8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1630699144867-37acec97df5a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxNHx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1574362848149-11496d93a7c7?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxNXx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1484154218962-a197022b5858?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxNnx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1580041065738-e72023775cdc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxN3x8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1579632652768-6cb9dcf85912?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxOHx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1560440021-33f9b867899d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxOXx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1556020685-ae41abfc9365?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyMHx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1630699375895-fe5996d163ee?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyMXx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1497366754035-f200968a6e72?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyMnx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1536376072261-38c75010e6c9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyM3x8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1612320648993-61c1cd604b71?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyNHx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1493606371202-6275828f90f3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyNXx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1595039357995-905cad2933e3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyNnx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1525438160292-a4a860951216?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyN3x8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1630699376289-b62375a35505?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyOHx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1433849665221-d2f93042ae54?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyOXx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400',
            'https://images.unsplash.com/photo-1558778909-1d4ea850da7d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwzMHx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=400'
        ];

        $data = [];

        for ($i = 0; $i < $count; $i++) {
            $index = $i % count($names);

            $data[] = [
                'name' => $names[$index],
                'location' => "{$locations[$index]}, London",
                'apartment_types' => $types[$index],
                'emission' => round(mt_rand(45, 75) / 10, 2) . 'kg CO2e',
                'rate' => 'From £' . number_format(mt_rand(110, 160), 2) . ' per night',
                'image' => $images[$index],
            ];
        }        

        return $data;
    }
}