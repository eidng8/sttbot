<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 2016-11-30
 * Time: 23:12
 */

namespace eidng8\Tests\Wiki\Templates;

use eidng8\Tests\TestCase;
use eidng8\Wiki\Templates\InfoBox;
use eidng8\Wiki\Templates\MissionCost;

/**
 * InfoBoxTest
 */
class InfoBoxTest extends TestCase
{
    public function testEpisodeMission()
    {
        // @codingStandardsIgnoreStart
        $text
            = <<<'EOT'
{{Infobox mission
 |Box title = The Wrong Crowd [E1-M1]
 |Image file = AT-Romulan Chasing Federation.png
 |Image size = 200px
 |Row 1 title = Episode 
 |Row 1 info = 1 - [[The Art of War]]
 |Row 2 title = Mission 
 |Row 2 info = 1st
 |Row 3 title = Type
 |Row 3 info = Away Team
 |Row 4 title = Cost
 |Row 4 info =  {{currency|CRN}} {{triple| 4 | 4 | 8 }}
 |Row 5 title = 
 |Row 5 info = 
}}

'''The Wrong Crowd''' is the first [[mission]] in Episode 1, [[The Art of War]]. It takes place on [[K'thar]] in the [[Qo'noS system]].

__TOC__

{{Clear}}


EOT;
        // @codingStandardsIgnoreEnd
        $info = new InfoBox($text);
        $this->assertInstanceOf(InfoBox::class, $info);
        $actual = $info->get();
        $this->assertSame('The Wrong Crowd', $actual['title']);
        $this->assertSame('The Art of War', $actual['episode']);
        $this->assertSame(1, $actual['mission']);
        $this->assertSame('away team', $actual['type']);

        /* @var MissionCost $cost */
        $cost = $actual['cost'];
        $this->assertInstanceOf(MissionCost::class, $cost);
        $this->assertSame(4, $cost->normal());
        $this->assertSame(4, $cost->elite());
        $this->assertSame(8, $cost->epic());
        $this->assertTrue($cost->useChroniton());
        $this->assertFalse($cost->useTicket());
    }//end testEpisodeMission()

    public function testDistressCallMission()
    {
        // @codingStandardsIgnoreStart
        $text
            = "{{Infobox mission\n |Box title = Long Distance Call [DE-M1]\n |Image file = SB-Borg Scout Ship.png\n |Image size = 200px\n |Row 1 title = [[Distress Calls]]\n |Row 1 info = [[Delphic Expanse]]\n |Row 2 title = Mission \n |Row 2 info = 1st\n |Row 3 title = Type\n |Row 3 info = [[Space Battle]]\n |Row 4 title\t= Cost\n |Row 4 info\t= {{currency|CRN}} 4 / 4 / 8\n |Row 5 title\t= \n |Row 5 info\t= \n}}\n\n'''Long Distance Call''' is the first distress call [[mission]] in the [[Delphic Expanse]]. It takes place at [[Omicron Theta]] in the [[Omicron Theta (system)|Omicron Theta system]].\n\n__TOC__\n\n{{Clear}}\n\n== Mission Walkthrough ==\n\n{| class=\"wikitable\" style=\"text-align: center; margin-left: auto; margin-right: auto;\"\n! colspan=\"4\" | Long Distance Call\n|-\n| colspan=\"4\" | '''Captain Jean-Luc Picard:''' \"Priority Alert.<br>A damaged Borg vessel has entered the Alpha Quadrant.<br />If we strike quickly, before the Borg can make repairs,<br>we can eliminate this threat in its infancy.\"<br />\n'''Against''': [[Damaged Borg Cube]] in a [[Borg Scout Ship]] {{stars|5}}<br>'''Chroniton cost''': 4 / 4 / 8\n|-\n! \n! {{level|1}}\n! {{level|2}}\n! {{level|3}}\n|-\n| '''Shields'''\n| 14000\n| 14000\n| 18000\n|-\n| '''Shield Regeneration''' \n| 800\n| 900\n| 1200\n|-\n| '''Hull'''\n| 38000\n| 40000\n| 45000\n|-\n| '''Evasion'''\n| 0\n| 0\n| 0\n|-\n| '''Evasion Power''' \n| 100\n| 100\n| 100\n|-\n| '''Attack'''\n| 2\n| 2\n| 2\n|-\n| '''Attack Power''' \n| 1900\n| 1900\n| 2000\n|-\n| '''Attack Speed'''\n| 0.9 \n| 0.9 \n| 0.9\n|-\n| '''Accuracy''' \n| 1\n| 1\n| 1\n|-\n| '''Accuracy Power'''\n| 1400\n| 1400\n| 1500\n|-\n| '''Crit Rating''' \n| 500\n| 500\n| 500\n|-\n| '''Crit Bonus'''\n| 5000\n| 5000\n| 5000\n|-\n| colspan=\"4\" | ''A Borg cube is usually a death sentence,<br>but with this particular vessel weakened, we were able to win the day.<br>Starfleet will remain on the lookout for other Borg ships.''\n|}\n\n== Mission Rewards ==\n===Standard Rewards===\n{{RewardList|Standard\n|Normal=\n* {{Item|Phaser Type 2 (TNG)|Basic}}\n* {{Item|Phaser Type 2 (DS9)|Basic}}\n* {{Item|Starfleet Uniform (TNG)|Basic}}\n* {{Item|Starfleet Uniform (TOS)|Basic}}\n* {{Item|Casing|Basic}}\n|Elite=\n* {{Item|Phaser Type 1|Uncommon}}\n* {{Item|Sensor|Common}}\n* {{Item|Radiation Collector|Basic}}\n* {{Item|Emitter Crystal|Basic}} (x2)\n* {{Item|Plasma Rifle|Basic}}\n|Epic=\n* {{Item|Replicator Ration|Rare}}\n* {{Item|Subprocessor|Uncommon}}\n* {{Item|Gold-Pressed Latinum|Common}}\n* {{Item|Optronic Circuit|Basic}}\n* &nbsp; <!-- Intentionally Blank -->\n}}\n\n====Drop Chance====\n{{DropTest/multi}}\n\n=== Other Rewards ===\n{| style=\"text-align: center; margin:0px auto;\" class=\"wikitable\"\n! colspan=\"2\" | Currency<br>Rewards\n| {{level|n|m}}\n| {{level|el|m}}\n| {{level|ep|m}}\n|-\n! Capt XP\n! <small>1st-Run<br>Repeat</small>\n| 37<br>2\n| 355<br>15\n| 625<br>25\n|- \n! colspan=\"2\" | Crew XP\n| 6\n| 33\n| 57\n|-\n! colspan=\"2\" | [[Crew Experience Training|Training XP]]<br />at [[Warp]] 1+10\n| 20<br>125\n| 70<br>695\n| ?<br>?\n|-\n! colspan=\"2\" | Credits {{currency|CRD}}<br />{{Currency|crd}} at [[Warp]] 1\n| 300<br>210\n| 300<br>210\n| 600<br>?\n|}\n\n{{MissionsNav}}\n\n[[Category:Space Battle Missions]]\n[[Category:Delphic Expanse]]";
        // @codingStandardsIgnoreEnd
        $actual = new InfoBox($text);
        $this->assertInstanceOf(InfoBox::class, $actual);
        $this->assertSame('Long Distance Call', $actual->name());
        $this->assertSame('Delphic Expanse', $actual->episode());
        $this->assertSame(1, $actual->sequence());
        $this->assertSame('space battle', $actual->type());

        /* @var MissionCost $cost */
        $cost = $actual->cost();
        $this->assertInstanceOf(MissionCost::class, $cost);
        $this->assertSame(4, $cost->normal());
        $this->assertSame(4, $cost->elite());
        $this->assertSame(8, $cost->epic());
        $this->assertTrue($cost->useChroniton());
        $this->assertFalse($cost->useTicket());
    }//end testDistressCallMission()

    public function testCadetChallengeMission()
    {
        // @codingStandardsIgnoreStart
        $text
            = "{{Infobox mission\n |Box title = Bravery in Action<br />[EA-M1]\n |Image file = AT-Bolian Repairs Console.png\n |Image size = 200px\n |Row 1 title = [[Cadet Challenge]] \n |Row 1 info = [[Ex Astris, Scientia]]\n |Row 2 title = Mission \n |Row 2 info = 1st\n |Row 3 title = Type\n |Row 3 info = [[Away Team]]\n |Row 4 title = Cost\n |Row 4 info =  {{currency|tik}} 1\n}}\n\n'''Bravery in Action''' is the first [[mission]] in Cadet Challenge [[Ex Astris, Scientia]]. It takes place on [[Alpha Onias II]] in the [[Alpha Onias]] system.\n\n__TOC__\n\n{{Clear}}\n\n== Mission Walkthrough ==\n{{MWHead\n| MIntro    = The Taranko colony is under attack! Repel the last of the Romulan invaders and help the colonists recover.\n<!--Traits-->|Desperate|Jury Rigger|Inspiring|Maverick|Counselor|Resourceful|Nurse|Doctor|Hunter|Marksman\n| Notes     = Ideal crew: [[Promoted Sisko]] for {{skill|cmd}}/{{skill|sec}}, [[CMO Crusher]] for {{skill|med}}, and [[Lt Commander Scott]] for {{skill|eng}}.<br>Alternatively: [[Nurse Paris]] for {{skill|med}}/{{skill|cmd}}, [[Ensign Seska]] for {{skill|sec}}/{{skill|eng}}, and any crew. <small>(Maxed, can 3-star Epic but at a gamble)</small>\n}}\n{{MWalkHead}}\n{{MWalk1\n| Step        = 1\n| StepTitle   = Establish Base Camp\n\n| ChoiceA     = Push Romulans to Colony\n| ChoiceAPic  = Shuttlecraft Under Attack\n| SkillReqA   = {{triple|Security| 55 | 190 | 290}}\n| OtherReqA   = \n| BonusTraitA = {{triple|Resourceful|15|30|60|nobonus=all}}\n| ItemRewardA = \n| SuccessA    = Through combined air and land assaults, {{Char}} has pushed the Romulans back into the colony itself.\n| FailA       = {{Char}} finally manages to secure land for a base camp, though many officers are injured in the effort.\n}}\n{{MWalk1\n| Step        = 2\n| StepTitle   = Tend to Wounded Officers\n\n| ChoiceA     = Perform Field Surgery\n| ChoiceAPic  = Under Surgery\n| SkillReqA   = {{triple|Medical|55|190|290}}\n| OtherReqA   = \n| BonusTraitA = {{triple|Doctor|Nurse|15|30|60|nobonus=all}}\n| ItemRewardA = {{item|Medical Lesson|common}}<br />{{item|EPI Capacitor|uncommon}}<br />{{item|Covert Contacts|rare}}\n| SuccessA    = {{Char}} pulls off major surgery on a critically wounded officer. There are no signs of infection as he recovers.\n| FailA       = Though the surgery appears to go well, {{Char}} cannot stave off infection. The wounded officer dies.\n}}\n{{MWalk1\n| Step        = 3\n| StepTitle   = Repel Romulan Invaders\n\n| ChoiceA     = Snipe Romulan Soldiers\n| ChoiceAPic  = Andorians Chased By Federation\n| SkillReqA   = {{triple|Security| 55 | 190 | 290}}\n| OtherReqA   = \n| BonusTraitA = {{triple|Hunter|Marksman| 15 | 30 | 60 |nobonus=all}}\n| ItemRewardA = {{item|Phaser Type 2 (TMP)|common}}<br />{{item|Science Experiment|uncommon}}<br />{{item|Starfleet Uniform Pattern|rare}}\n| SuccessA    = One by one, {{Char}} eliminates Romulan soldiers until they retreat from the Taranko colony.\n| FailA       = {{Char}} misses several Romulan soldiers. It takes many hours to break their resolve, but they finally retreat.\n}}\n{{MWalk1\n| Step        = 4\n| StepTitle   = Repair Colony Utilities\n\n| ChoiceA     = Rig Self-Sustaining Life Support\n| ChoiceAPic  = Bolian Repairs Console\n| SkillReqA   = {{triple|Engineering| 55 | 190 | 290}}\n| OtherReqA   = \n| BonusTraitA = {{triple|Desperate|Jury Rigger| 15 | 30 | 60 |nobonus=all}}\n| ItemRewardA = \n| SuccessA    = {{Char}} uses the colony's natural resources to ensure they can survive without an artificial power source.\n| FailA       = {{Char}} has trouble identifying natural resources to use as power sources. The away team is forced to call for supplies.\n}}\n{{MWalk2\n| Step        = 5\n| StepTitle   = Tend to Wounded Colonists\n\n| ChoiceA     = Organize Medical Evacuations\n| ChoiceAPic  = Starfleet Headquarters Meeting\n| SkillReqA   = {{triple|Command| 55 | 190 | 290}}\n| OtherReqA   = \n| BonusTraitA = {{triple|Inspiring|Maverick|15|30|60|nobonus=all}}\n| ItemRewardA = {{item|Andorian Ale|common}}<br />{{item|Medical Experiment|uncommon}}<br />{{item|Andorian Ale|common}}\n| SuccessA    = {{Char}} has shuttles land at the Taranko colony in record time, ensure[sic] the injured colonists will have the best care.\n| FailA       = {{Char}} cannot cut through the red tape fast enough. Several colonists die of their injuries while waiting for evac.\n\n| ChoiceB     = Field Dress Civilian Injuries\n| ChoiceBPic  = Recovering Orion Beauty\n| SkillReqB   = {{triple|Medical| 55 | 190 | 290}}\n| OtherReqB   = \n| BonusTraitB = {{triple|Counselor| 15 | 30 | 60 |nobonus=all}}\n| ItemRewardB = {{item|Dermal Regenerator|common}}<br />[[Constellation Class Schematic]] (x10)<br />[[Nova Class Schematic]] (x10)\n| SuccessB    = {{Char}} heals the colonists in both body and spirit. The colony is not only free, but confident in Starfleet.\n| FailB       = {{Char}}'s bedside manner leaves something to be desires. The colonists grumble about leaving the Federation.\n}}\n{{MWalkFoot\n| MissionPass = The Taranko colony is safe once more, now that we have repelled the Romulans and ensured the colony can recover on their own.<br>I hope Admiral Riker is as impressed with my officers as I am...\n| MissionFail = The Romulans have proves intransigent, and the Taranko colony remains under their control.<br>We must free the colony and help them to recover, or be drummed out of Starfleet by Admiral Riker...\n}}\n\n== Mission Rewards ==\n===Rare Rewards===\n{{RewardList|Rare\n|Normal=\n* {{item|Andorian Ale|common}}\n* {{item|Medical Lesson|common}}\n* {{item|Phaser Type 2 (TMP)|common}}\n* {{item|Dermal Regenerator|common}}\n|Elite=\n* {{item|Science Experiment|uncommon}}\n* {{item|Medical Experiment|uncommon}}\n* {{item|EPI Capacitor|uncommon}}\n* [[Constellation Class Schematic]] (x10)\n|Epic=\n* {{item|Andorian Ale|common}}\n* {{item|Covert Contacts|rare}}\n* {{item|Starfleet Uniform Pattern|rare}}\n* [[Nova Class Schematic]] (x10)\n}}\n\n===Standard Rewards===\n{{RewardList|Standard\n|Normal=\n* {{item|Ancient Film|basic}}\n* {{item|Writing PADD (TOS)|basic}}\n* {{item|Starfleet Uniform Pattern|basic}}\n* {{item|Spices|basic}}\n* [[Chronitons]] (x15)\n|Elite=\n* {{item|Multidimensional Transporter Device|basic}}\n* {{item|Holoprogram|basic}}\n* {{item|First Contact Protocol|basic}}\n* {{item|Starfleet Uniform Pattern|common}}\n* [[Chronitons]] (x20)\n|Epic=\n* {{item|Security Codes|uncommon}}\n* {{item|Authorization Code|common}}\n* {{item|Case Files|common}}\n* {{item|TR-580 Medical Tricorder|basic}}\n* [[Chronitons]] (x25)\n}}\n\n====Drop Chance====\n{{DropTest/multi}}\n\n===Other Rewards===\n{| style=\"text-align: center; margin-left: auto; margin-right: auto;\" class=\"wikitable\"\n! colspan=\"2\" | Currency<br>Rewards\n| {{level|n|m}}\n| {{level|el|m}}\n| {{level|ep|m}}\n|-\n! Capt XP\n! <small>1st-Run<br>Repeat</small>\n| 511<br>21\n| 769<br>29\n| 902<br>32\n|-\n! colspan=\"2\" | Crew XP\n| 45\n| 75\n| 90\n|-\n! colspan=\"2\" | Credits {{currency|CRD}}\n| 450\n| 750\n| 1200\n|-\n! colspan=\"2\" | Chronitons {{currency|CRN}}\n| 15\n| 20\n| 25\n|}\n\n{{CCMissionNav}}\n\n[[Category:Away Team Missions]]\n[[Category:Ex Astris, Scientia]]";
        // @codingStandardsIgnoreEnd
        $actual = new InfoBox($text);
        $this->assertInstanceOf(InfoBox::class, $actual);
        $this->assertSame('Bravery in Action', $actual->name());
        $this->assertSame('Ex Astris, Scientia', $actual->episode());
        $this->assertSame(1, $actual->sequence());
        $this->assertSame('away team', $actual->type());

        /* @var MissionCost $cost */
        $cost = $actual->cost();
        $this->assertInstanceOf(MissionCost::class, $cost);
        $this->assertSame(1, $cost->ticket());
        $this->assertTrue($cost->useTicket());
        $this->assertFalse($cost->useChroniton());
    }//end testCadetChallengeMission()

    public function testResearchFutureHistory()
    {
        // @codingStandardsIgnoreStartc
        $text
            = <<<'EOT'
{{Infobox mission
 |Box title = Researching Future History<br />[E7-M9]
 |Image file = AT-Artifact Discussion.png
 |Image size = 200px
 |Row 1 title = Episode 
 |Row 1 info = 7 - [[When Falcons Clash]]
 |Row 2 title = Mission 
 |Row 2 info = 9th
 |Row 3 title = Type
 |Row 3 info = [[Away Team]]
 |Row 4 title = Cost
 |Row 4 info = {{currency|CRN}} {{triple| 16 |22 |24 }}
 |Row 5 title = 
 |Row 5 info = 
 }}

'''{{PAGENAME}}''' is the ninth [[mission]] in Episode 7, [[When Falcons Clash]]. It takes place in [[Arloff IX]] in the [[Arloff|Arloff System]].

__TOC__
{{Clear}}
EOT;
        // @codingStandardsIgnoreEnd

        $actual = new InfoBox($text);
        $this->assertInstanceOf(InfoBox::class, $actual);
        $this->assertSame('Researching Future History', $actual->name());
        $this->assertSame('When Falcons Clash', $actual->episode());
        $this->assertSame(9, $actual->sequence());
        $this->assertSame('away team', $actual->type());

        /* @var MissionCost $cost */
        $cost = $actual->cost();
        $this->assertInstanceOf(MissionCost::class, $cost);
        $this->assertSame(16, $cost->normal());
        $this->assertSame(22, $cost->elite());
        $this->assertSame(24, $cost->epic());
        $this->assertTrue($cost->useChroniton());
        $this->assertFalse($cost->useTicket());
    }//end testResearchFutureHistory()
}//end class
