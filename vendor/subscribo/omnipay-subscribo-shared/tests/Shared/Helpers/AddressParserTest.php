<?php

namespace Subscribo\Omnipay\Shared\Helpers;

use PHPUnit_Framework_TestCase;
use Subscribo\Omnipay\Shared\Helpers\AddressParser;

class AddressParserTest extends PHPUnit_Framework_TestCase
{
    public function testParseFirstLine()
    {
        $this->assertSame(['Hauptstraße','12'], AddressParser::parseFirstLine('Hauptstraße 12'));
        $this->assertSame(['Hlavné n.', null], AddressParser::parseFirstLine('  Hlavné    n.  '));
        $this->assertSame(['Hlavné n.', '245 / B'], AddressParser::parseFirstLine('Hlavné n. 245 / B '));
        $this->assertSame(['Náměstí 15. pluku', '1224 / IV,V / 24a'], AddressParser::parseFirstLine('Náměstí 15. pluku 1224 / IV,V / 24a'));
        $this->assertSame(['Vedľajšia ulica','5 / i-iv'], AddressParser::parseFirstLine('Vedľajšia ulica 5 / i-iv'));
        $this->assertSame(['Street A. Lt.','12 / B -E'], AddressParser::parseFirstLine('Street A. Lt. 12 / B -E'));

        $this->assertSame(['Local Vi.','VI.'], AddressParser::parseFirstLine('Local Vi. VI.'));
        $this->assertSame(['Square of the Day of the 1st vic.', 'VI. - III.'], AddressParser::parseFirstLine('Square of the Day of the 1st vic. VI. - III. '));
        $this->assertSame(['Square of the Day of the 1st vic', 'VI. - III.'], AddressParser::parseFirstLine('Square of the Day of the 1st vic VI. - III. '));
        $this->assertSame(['Square of the Day of the 1st Vi.', 'VI. - III.'], AddressParser::parseFirstLine('Square of the Day of the 1st Vi. VI. - III. '));
        $this->assertSame(['Square of the Day of the', '1st VI. VI. - III.'], AddressParser::parseFirstLine('Square of the Day of the 1st VI. VI. - III. '));
        $this->assertSame(['Square of the Day of the', '1st vi. vi. - iii.'], AddressParser::parseFirstLine('Square of the Day of the 1st vi. vi. - iii. '));

        $this->assertSame(['Meierskamp','31b'], AddressParser::parseFirstLine('Meierskamp 31b'));
        $this->assertSame(['Meierskamp','31b'], AddressParser::parseFirstLine('Meierskamp31b'));
        $this->assertSame(['Meierskamp','31b'], AddressParser::parseFirstLine('Meierskamp,31b'));
        $this->assertSame(['Meierskamp','315XI'], AddressParser::parseFirstLine('Meierskamp315XI'));

        $this->assertSame(['Hauptstraße', '12'], AddressParser::parseFirstLine('Hauptstraße,12'));
        $this->assertSame(['Hauptstraße', ''], AddressParser::parseFirstLine('Hauptstraße,'));
        $this->assertSame(['Hauptstraße', null], AddressParser::parseFirstLine('Hauptstraße'));
        $this->assertSame(['Hauptstraße', '12'], AddressParser::parseFirstLine('Hauptstraße,,,12'));
        $this->assertSame(['Hauptstraße', '12'], AddressParser::parseFirstLine('Hauptstraße12'));
    }
}
