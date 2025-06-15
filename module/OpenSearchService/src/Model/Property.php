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
        "StudioApartment",
        "1BedroomApartment",
        "2BedroomApartment",
        "3BedroomApartment",
        "Penthouse"
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
        $this->apartment_facilities = $this->randomSubset(self::$apartmentFacilitiesPool);
        $this->kitchen_facilities = $this->randomSubset(self::$kitchenFacilitiesPool);
        $this->building_facilities = $this->randomSubset(self::$buildingFacilitiesPool);
        $this->building_type = $this->randomSubset(self::$buildingTypePool, 1);
        $this->health_and_safety_facilities = $this->randomSubset(self::$healthAndSafetyFacilitiesPool);
        $this->sustainability = $this->randomSubset(self::$sustainabilityPool);
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

    private function randomSubset(array $pool, int $maxCount = null): array
    {
        $count = $maxCount ?? rand(1, max(1, floor(count($pool)/2)));
        shuffle($pool);
        return array_slice($pool, 0, $count);
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
            'apartment_facilities' => $this->apartment_facilities,
            'kitchen_facilities' => $this->kitchen_facilities,
            'building_facilities' => $this->building_facilities,
            'building_type' => $this->building_type,
            'health_and_safety_facilities' => $this->health_and_safety_facilities,
            'sustainability' => $this->sustainability,
        ];
    }
}