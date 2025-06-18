<?php
namespace OpenSearchService\Model;

class Property
{
    public string $name;
    public string $date;
    public int $apartment_id;
    public int $property_id;
    public string $postcode;
    public string $city;
    public string $area;
    public string $apartment_type;
    public float $rate;
    public array $location; // geo_point as ['lat' => float, 'lon' => float]
    public bool $available;
    public int $rating;
    public string $imageUrl;
    public float $emission;
    public array $apartment_facilities;
    public array $kitchen_facilities;
    public array $building_facilities;
    public array $building_type;
    public array $health_and_safety_facilities;
    public array $sustainability;

    private static array $apartmentFacilitiesPool = [
        "housekeeping_daily",
        "housekeeping_once_weekly",
        "housekeeping_twice_weekly",
        "onsite_parking",
        "offsite_parking",
        "internet",
        "work_desk",
        "sofa_bed",
        "air_conditioning",
        "walk_on_balcony"
    ];

    private static array $kitchenFacilitiesPool = [
        "dishwasher",
        "fully_equiped_kitchen",
        "basic_kithenette",
        "washer_dryer",
        "washing_machine_and_seperate_dryer",
        "washing_machine_only"
    ];

    private static array $buildingFacilitiesPool = [
        "business_center",
        "meeting_room",
        "restaurant",
        "bar",
        "fitness_center",
        "pool",
        "lifts",
        "gardens",
        "pets_allowed"
    ];

    private static array $buildingTypePool = [
        "dedicated_serviced_apartment_building",
        "apart_hotel",
        "individual_apartments_within_a_residential_development"
    ];

    private static array $healthAndSafetyFacilitiesPool = [
        "apartment_smoke_alarm",
        "fire_blanket",
        "fire_extinguisher",
        "electronic_key_cards",
        "spyhole_intercom",
        "public_area_smoke_alarm",
        "on-site_security"
    ];

    private static array $sustainabilityPool = [
        "public_sustainability_commitment",
        "green_energy",
        "no_single_use_plastics",
        "led_lightbulbs",
        "bike_storage",
        "smart_lighting",
        "waste_management_and_recycling",
        "water_conservation",
        "sustainable_sourcing_and_procurement"
    ];

    private static array $cities = [
        'London' => ['Clerkenwell', 'Camden', 'Islington', 'Hackney'],
        'Manchester' => ['Northern Quarter', 'Didsbury', 'Ancoats'],
        'Birmingham' => ['Digbeth', 'Jewellery Quarter', 'Moseley']
    ];

    private static array $apartmentTypes = [
        "studio_apartment",
        "1_bedroom_apartment",
        "2_bedroom_apartment",
        "3_bedroom_apartment",
        "penthouse"
    ];

    private static array $imageUrls = [
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

    public function __construct()
    {
        $this->name = $this->randomName();
        $this->date = $this->randomDate('2025-01-01', '2025-12-31');
        $this->apartment_id = rand(1000, 9999);
        $this->property_id = rand(1, 100);
        $this->postcode = $this->randomPostcode();
        $cityData = $this->randomCityAndArea();
        $this->city = $cityData['city'];
        $this->area = $cityData['area'];
        $this->apartment_type = $this->randomApartmentType();
        $this->rate = $this->randomRate();
        $this->location = $this->randomLocationForCity($this->city);
        $this->available = (bool) rand(0, 1);
        $this->rating = rand(1, 5);
        $this->emission = $this->randomEmission();
        $this->apartment_facilities = $this->randomSubset(self::$apartmentFacilitiesPool);
        $this->kitchen_facilities = $this->randomSubset(self::$kitchenFacilitiesPool);
        $this->building_facilities = $this->randomSubset(self::$buildingFacilitiesPool);
        $this->building_type = $this->randomSubset(self::$buildingTypePool, 1);
        $this->health_and_safety_facilities = $this->randomSubset(self::$healthAndSafetyFacilitiesPool);
        $this->sustainability = $this->randomSubset(self::$sustainabilityPool);
        $this->imageUrl = $this->randomImageUrl();

    }

    private function randomName(): string
    {
        $names = ["The Grange", "The Gate", "Parkview Residence", "Riverside Apartments", "City Lights"];
        return $names[array_rand($names)];
    }

    private function randomDate(string $start, string $end): string
    {
        $min = strtotime($start);
        $max = strtotime($end);
        $val = mt_rand($min, $max);
        return date('Y-m-d', $val);
    }

    private function randomPostcode(): string
    {
        // Simple UK postcode generator format: A9 9AA or A99 9AA
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $letter1 = $letters[rand(0, 25)];
        $digit1 = rand(1, 9);
        $digit2 = rand(0, 9);
        $space = ' ';
        $digit3 = rand(1, 9);
        $letter2 = $letters[rand(0, 25)];
        $letter3 = $letters[rand(0, 25)];

        if (rand(0,1)) {
            // Format A9 9AA
            return "{$letter1}{$digit1}{$space}{$digit3}{$letter2}{$letter3}";
        } else {
            // Format A99 9AA
            return "{$letter1}{$digit1}{$digit2}{$space}{$digit3}{$letter2}{$letter3}";
        }
    }

    private function randomCityAndArea(): array
    {
        $city = array_rand(self::$cities);
        $areas = self::$cities[$city];
        $area = $areas[array_rand($areas)];
        return ['city' => $city, 'area' => $area];
    }

    private function randomApartmentType(): string
    {
        return self::$apartmentTypes[array_rand(self::$apartmentTypes)];
    }

    private function randomRate(): int
    {
        return rand(80, 250);
    }

    private function randomLocationForCity(string $city): array
    {
        // Approximate lat/lon ranges for demo cities
        $ranges = [
            'London' => ['lat' => [51.48, 51.56], 'lon' => [-0.14, -0.10]],
            'Manchester' => ['lat' => [53.46, 53.50], 'lon' => [-2.28, -2.20]],
            'Birmingham' => ['lat' => [52.44, 52.50], 'lon' => [-1.95, -1.88]],
        ];

        if (!isset($ranges[$city])) {
            // Default fallback to London
            $city = 'London';
        }

        $lat = $this->randomFloat($ranges[$city]['lat'][0], $ranges[$city]['lat'][1]);
        $lon = $this->randomFloat($ranges[$city]['lon'][0], $ranges[$city]['lon'][1]);

        return ['lat' => $lat, 'lon' => $lon];
    }

    private function randomFloat(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    private function randomImageUrl(): string
    {
        return self::$imageUrls[array_rand(self::$imageUrls)];
    }

    private function randomSubset(array $pool, int $maxCount = null): array
    {
        $count = $maxCount ?? rand(1, max(1, floor(count($pool)/2)));
        shuffle($pool);
        return array_slice($pool, 0, $count);
    }

    private function randomEmission(): float
    {
        return round(rand(0, 20) + (rand(0, 99) / 100), 2);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'date' => $this->date,
            'apartment_id' => $this->apartment_id,
            'property_id' => $this->property_id,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'area' => $this->area,
            'apartment_type' => $this->apartment_type,
            'rate' => $this->rate,
            'location' => $this->location,
            'available' => $this->available,
            'rating' => $this->rating,
            'emission' => $this->emission,
            'image_url' => $this->imageUrl,
            'apartment_facilities' => $this->apartment_facilities,
            'kitchen_facilities' => $this->kitchen_facilities,
            'building_facilities' => $this->building_facilities,
            'building_type' => $this->building_type,
            'health_and_safety_facilities' => $this->health_and_safety_facilities,
            'sustainability' => $this->sustainability,
        ];
    }
}