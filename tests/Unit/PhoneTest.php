<?php

namespace Tests\Unit;

use App\Support\Phone;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function test_link_whatsapp(): void
    {
        $this->assertSame('https://wa.me/6281234567890', Phone::waLink('0812-3456-7890'));
        $this->assertSame('https://wa.me/6281234567890', Phone::waLink('+62 812 3456 7890'));
        $this->assertSame('https://wa.me/6281234567890', Phone::waLink('81234567890'));
        $this->assertNull(Phone::waLink('-'));
        $this->assertNull(Phone::waLink(''));
        $this->assertNull(Phone::waLink(null));
        $this->assertNull(Phone::waLink('08123'));
    }
}
