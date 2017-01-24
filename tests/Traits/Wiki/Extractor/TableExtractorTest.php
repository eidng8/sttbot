<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-10-30
 * Time: 16:04
 */

namespace eidng8\Tests\Traits\Wiki;

use eidng8\Tests\TestCase;
use eidng8\Traits\Wiki\Cache;
use eidng8\Traits\Wiki\Extractor\TableExtractor;

/**
 * TableExtractorTest
 */
class TableExtractorTest extends TestCase
{
    use Cache, TableExtractor;

// @codingStandardsIgnoreStart
    public const TEXT
        = <<<'EOT'
{| class="wikitable"
! Avatar Icon
! Character
! Notes
|- style="align:center;"
! data-sort-type="text" | <small>Crew Sorting</small>
! <small>Character Sorting</small>
! &nbsp;
! data-sort-type="number"| &nbsp;
! data-sort-type="number"| &nbsp;
! data-sort-type="number"| &nbsp;
! data-sort-type="number"| &nbsp;
! data-sort-type="number"| &nbsp;
! data-sort-type="number"| &nbsp;
! &nbsp;
|-
{{MPCrewList
| CrewName = "Dark Ages" McCoy
| CharName = McCoy
| Stars = 5
| DIP = 32
| SEC = 57
| MED = 73
| Race = Human
<!--Traits-->|Federation|Starfleet|Civilian|Human|Doctor}}
{{MPCrewList
| CrewName = Antaak
| CharName = Antaak (Character){{!}}Antaak
| Stars = 4
| SCI = 39
| MED = 52
| Race = Klingon
<!--Traits-->|Klingon|Doctor|Desperate|Geneticist}}
|-
|
| Spider Barclay
| In an interview with PocketNow, executive producer Rich Gallup has stated that the company hopes to include Spider Barlcay in a future update <ref name='PocketNow'>http://pocketnow.com/2015/03/07/star-trek-timelines-pax-east-2015</ref>.
|}
EOT;

// @codingStandardsIgnoreEnd

    public function testNumTables()
    {
        $this->foundTables = [1, 2, 3, 4];
        $this->assertSame(4, $this->numTables());
    }//end testNumTables()

    public function testNumTablesEmpty()
    {
        $this->foundTables = false;
        $this->assertSame(0, $this->numTables());
    }//end testNumTables()

    public function testNumTablesBuild()
    {
        $this->foundTables = null;
        $this->assertSame(0, $this->numTables());
    }//end testNumTables()

    public function testTable()
    {
        $this->foundTables = null;
        $this->wikiText = json_decode(
            file_get_contents(
                static::DIR_CACHE .
                '/parse/Crew_1e8d43d73cc2f92192ca041f6ef6fcc7.json'
            ),
            true
        )['wikitext']['*'];
        $this->assertSame(2, $this->numTables());
    }//end testTable()

    public function testTables()
    {
        $this->tables(static::TEXT);
        $this->assertSame(static::TEXT, $this->table(0));
    }//end testTables()

    public function testTableGet()
    {
        $this->tables(static::TEXT);
        $text = '{| class="wikitable"';
        $this->assertSame($text, substr($this->table(0), 0, strlen($text)));
    }//end testTable()
}//end class
