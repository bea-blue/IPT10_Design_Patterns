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

// Prototype Interface
interface CallingCardPrototype
{
    public function clonePrototype(): static;
}

// Concrete Prototype (GD)
class GdCallingCardBuilder implements CallingCardPrototype
{
    private \GdImage $image;

    private int $cardLeft = 50;
    private int $cardTop = 50;
    private int $cardRight = 950;
    private int $cardBottom = 550;
    private int $accentRight = 75;
    private int $textLeft = 120;

    private int $background;
    private int $white;
    private int $black;
    private int $gray;
    private int $blue;

    public function createCanvas(int $width, int $height): static
    {
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

    // --- These are the parts that DIFFER per student ---
    // They are drawn AFTER cloning, on each student's own copy.

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

    public function getCard(): CallingCard
    {
        return new CallingCard($this->image);
    }

    public function __clone(): void
    {
        $width = imagesx($this->image);
        $height = imagesy($this->image);

        $copy = imagecreatetruecolor($width, $height);
        imagecopy($copy, $this->image, 0, 0, 0, 0, $width, $height);

        $this->image = $copy;
    }

    public function clonePrototype(): static
    {
        return clone $this;
    }
}

// Client
$businessName = "College of Computing Studies";
$position = "BSIT Student";
$street = "AUF CCS Building";
$city = "Angeles City";
$address = "$street, $city";
$website = 'www.auf.edu.ph';

// 1. Build the PROTOTYPE just once.
//    Only the parts SHARED by every student are
//    drawn here (background, card base, accent bar,
//    business name, position, divider, address, website).
//    Notice: no name, no email, no phone yet.
$prototype = (new GdCallingCardBuilder())
    ->createCanvas(1000, 600)
    ->drawBackground()
    ->drawCardBase()
    ->drawAccentBar()
    ->drawBusinessName($businessName)
    ->drawPosition($position)
    ->drawDivider()
    ->drawAddress($address)
    ->drawWebsite($website);

// 2. Student data
$students = [
    ['first_name' => 'Felicity', 'last_name' => 'Hampton'],
    ['first_name' => 'Hank', 'last_name' => 'Rice'],
    ['first_name' => 'Ada', 'last_name' => 'Wilson'],
    ['first_name' => 'Daniel', 'last_name' => 'Salgado'],
    ['first_name' => 'Avalynn', 'last_name' => 'Crane'],
    ['first_name' => 'Fox', 'last_name' => 'Summers'],
    ['first_name' => 'Frankie', 'last_name' => 'Andersen'],
    ['first_name' => 'Alistair', 'last_name' => 'Decker'],
    ['first_name' => 'Aleena', 'last_name' => 'Phillips'],
    ['first_name' => 'Andrew', 'last_name' => 'Marks'],
    ['first_name' => 'Monica', 'last_name' => 'French'],
    ['first_name' => 'Corey', 'last_name' => 'Hess'],
    ['first_name' => 'Kaliyah', 'last_name' => 'Richard'],
    ['first_name' => 'Ahmed', 'last_name' => 'Richardson'],
    ['first_name' => 'Allison', 'last_name' => 'Cortes'],
    ['first_name' => 'Banks', 'last_name' => 'McGee'],
    ['first_name' => 'Kayleigh', 'last_name' => 'Mendoza'],
    ['first_name' => 'Dominic', 'last_name' => 'Atkins'],
    ['first_name' => 'Mina', 'last_name' => 'Beasley'],
    ['first_name' => 'Stanley', 'last_name' => 'Jefferson'],
];

$outputDirectory = __DIR__ . '/cards';
if (!is_dir($outputDirectory)) {
    mkdir($outputDirectory, 0755, true);
}

foreach ($students as $student) {
    $firstName = $student['first_name'];
    $lastName = $student['last_name'];

    $name = "$firstName $lastName";
    $email = strtolower("{$lastName}.{$firstName}@auf.edu.ph");
    $phone = sprintf('+1 (555) %03d-%04d', rand(100, 999), rand(1000, 9999));

    // 3. CLONE the prototype instead of rebuilding
    //    the shared parts from scratch.
    $studentCard = $prototype->clonePrototype();

    // 4. Only draw what's different for this student.
    $studentCard
        ->drawPersonName($name)
        ->drawEmail($email)
        ->drawPhone($phone);

    $card = $studentCard->getCard();

    $filename = $outputDirectory . '/calling-card-' . strtolower("$firstName-$lastName") . '.png';

    if ($card->save($filename)) {
        echo "Generated: $name -> $filename\n";
    } else {
        echo "ERROR: Could not save card for $name\n";
    }
}

echo "\nDone. " . count($students) . " calling cards generated.\n";