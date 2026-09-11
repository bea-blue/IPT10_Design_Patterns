<?php
// Product
class CallingCard
{
    private \GdImage $image;

    public function __construct(\GdImage $image)
    {
        $this->image = $image;
    }

    public function save(string $filename): bool
    {
        return imagepng($this->image, $filename);
    }

    public function __destruct()
    {
        imagedestroy($this->image);
    }
}

// Builder Interface
interface CallingCardBuilder
{
    public function createCanvas(int $width, int $height): static;
    public function drawBackground(): static;
    public function drawCardBase(): static;
    public function drawAccentBar(): static;
    public function drawBusinessName(string $businessName): static;
    public function drawPersonName(string $name): static;
    public function drawPosition(string $position): static;
    public function drawDivider(): static;
    public function drawEmail(string $email): static;
    public function drawPhone(string $phone): static;
    public function drawAddress(string $address): static;
    public function drawWebsite(string $website): static;
    public function getCard(): CallingCard;
}

// Concrete Builder (GD)
class GdCallingCardBuilder implements CallingCardBuilder
{
    private \GdImage $image;

    private int $width = 0;
    private int $height = 0;

    private int $background;
    private int $white;
    private int $black;
    private int $gray;
    private int $blue;

    // Layout constants (same numbers as your original script)
    private int $cardLeft = 50;
    private int $cardTop = 50;
    private int $cardRight = 950;
    private int $cardBottom = 550;
    private int $accentRight = 75;
    private int $textLeft = 120;

    public function createCanvas(int $width, int $height): static
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($width, $height);

        $this->background = imagecolorallocate($this->image, 245, 247, 250);
        $this->white      = imagecolorallocate($this->image, 255, 255, 255);
        $this->black      = imagecolorallocate($this->image, 30, 30, 30);
        $this->gray       = imagecolorallocate($this->image, 100, 100, 100);
        $this->blue       = imagecolorallocate($this->image, 40, 100, 200);

        return $this;
    }

    public function drawBackground(): static
    {
        imagefill($this->image, 0, 0, $this->background);
        return $this;
    }

    public function drawCardBase(): static
    {
        imagefilledrectangle(
            $this->image,
            $this->cardLeft,
            $this->cardTop,
            $this->cardRight,
            $this->cardBottom,
            $this->white
        );
        return $this;
    }

    public function drawAccentBar(): static
    {
        imagefilledrectangle(
            $this->image,
            $this->cardLeft,
            $this->cardTop,
            $this->accentRight,
            $this->cardBottom,
            $this->blue
        );
        return $this;
    }

    public function drawBusinessName(string $businessName): static
    {
        imagestring(
            $this->image,
            5,
            $this->textLeft,
            100,
            strtoupper($businessName),
            $this->blue
        );
        return $this;
    }

    public function drawPersonName(string $name): static
    {
        imagestring(
            $this->image,
            5,
            $this->textLeft,
            170,
            $name,
            $this->black
        );
        return $this;
    }

    public function drawPosition(string $position): static
    {
        imagestring(
            $this->image,
            4,
            $this->textLeft,
            210,
            $position,
            $this->blue
        );
        return $this;
    }

    public function drawDivider(): static
    {
        imageline(
            $this->image,
            $this->textLeft,
            260,
            $this->cardRight - $this->textLeft + $this->cardLeft,
            260,
            $this->gray
        );
        return $this;
    }

    public function drawEmail(string $email): static
    {
        imagestring(
            $this->image,
            4,
            $this->textLeft,
            310,
            'Email: ' . $email,
            $this->black
        );
        return $this;
    }

    public function drawPhone(string $phone): static
    {
        imagestring(
            $this->image,
            4,
            $this->textLeft,
            365,
            'Phone: ' . $phone,
            $this->black
        );
        return $this;
    }

    public function drawAddress(string $address): static
    {
        imagestring(
            $this->image,
            4,
            $this->textLeft,
            420,
            'Address: ' . $address,
            $this->black
        );
        return $this;
    }

    public function drawWebsite(string $website): static
    {
        imagestring(
            $this->image,
            3,
            $this->textLeft,
            485,
            $website,
            $this->gray
        );
        return $this;
    }

    public function getCard(): CallingCard
    {
        return new CallingCard($this->image);
    }
}


// Director
class CallingCardDirector
{
    public function __construct(private CallingCardBuilder $builder)
    {
    }

    public function buildStandardCard(array $data, int $width = 1000, int $height = 600): CallingCard
    {
        $this->builder
            ->createCanvas($width, $height)
            ->drawBackground()
            ->drawCardBase()
            ->drawAccentBar()
            ->drawBusinessName($data['businessName'])
            ->drawPersonName($data['name'])
            ->drawPosition($data['position'])
            ->drawDivider()
            ->drawEmail($data['email'])
            ->drawPhone($data['phone'])
            ->drawAddress($data['address'])
            ->drawWebsite($data['website']);

        return $this->builder->getCard();
    }
}

// Client
$firstName = "Juan";
$lastName = "Dela Cruz";

$businessName = "College of Computing Studies";
$position = "BSIT Student";

$street = "AUF CCS Building";
$city = "Angeles City";

$cardData = [
    'name' => "$firstName $lastName",
    'businessName' => $businessName,
    'position' => $position,
    'email' => strtolower("{$lastName}.{$firstName}@auf.edu.ph"),
    'phone' => sprintf('+1 (555) %03d-%04d', rand(100, 999), rand(1000, 9999)),
    'address' => "$street, $city",
    'website' => 'www.auf.edu.ph',
];

$builder = new GdCallingCardBuilder();
$director = new CallingCardDirector($builder);
$card = $director->buildStandardCard($cardData);

$outputDirectory = __DIR__ . '/cards';

if (!is_dir($outputDirectory)) {
    mkdir($outputDirectory, 0755, true);
}

$filename = $outputDirectory . '/calling-card-' . uniqid() . '.png';

if ($card->save($filename)) {
    echo "Calling card generated successfully.\n";
    echo "File: $filename\n";
} else {
    echo "ERROR: Could not save image.\n";
}