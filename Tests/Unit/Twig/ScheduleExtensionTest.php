<?php

namespace CanalTP\MttBundle\Tests\Twig;

/**
 * Description courte de la classe ScheduleExtensionTest
 *
 * @copyright  Copyright (c) 2008-2016 CanalTP. (http://www.canaltp.fr/)
 * @author     Thomas Chevily <thomas.chevily@canaltp.fr>
 * @version
 * @since 2016/04/07
 */
use CanalTP\MttBundle\Twig\ScheduleExtension;
use CanalTP\MttBundle\Entity\LayoutConfig;

class ScheduleExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CanalTP\MttBundle\Twig\ScheduleExtension
     */
    private $extension;

    /**
     * Minute where superscript has to be added
     *
     * @var string
     */
    private $expectedMinute = '26';

    private $journey;
    private $calendar;
    private $notes;
    private $noteColor = '#e44155';
    private $noteType;

    protected function setUp()
    {
        parent::setUp();
        $this->extension = new ScheduleExtension();
    }

    /**
     * Tests that schedule method adds superscript element when Note type isn't set
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::scheduleFilter
     */
    public function testThatSceduleAddsSuperscriptWhenNoteTypeIsNotSet()
    {
        $this->initDatasForSchedule();

        $scheduleValue = $this->getScheduleValue();
        $expected = sprintf('%s<sup>a</sup><sup></sup>', $this->expectedMinute);

        $this->assertEquals($expected, $scheduleValue);
    }

    /**
     * Tests that schedule method returns only minute string when links is empty
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::scheduleFilter
     */
    public function testThatScheduleRetrunsMinuteWhenJourneyDoesntContainLink()
    {
        $this->initDatasForSchedule();

        $this->journey->links = [];
        $this->noteType = null;

        $scheduleValue = $this->getScheduleValue();

        $this->assertEquals($this->expectedMinute, $scheduleValue);
    }

    /**
     * Tests that schedule method adds background color to the minute when Note type is set to "color"
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::scheduleFilter
     */
    public function testThatScheduleColorizesWhenNoteTypeIsSetToColor()
    {
        $this->initDatasForSchedule();

        $this->noteType = LayoutConfig::NOTES_TYPE_COLOR;

        $scheduleValue = $this->getScheduleValue();
        $this->assertScheduleAddsColor($scheduleValue);
    }

    /**
     * Tests that when note type is color and calendar is false, superscript element is added to the element
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::findNoteIndex
     */
    public function testFindNoteIndexForColorNoteTypeWithoutCalendar()
    {
        $this->initDatasForSchedule();

        $this->noteType = LayoutConfig::NOTES_TYPE_COLOR;
        $this->calendar = false;

        $scheduleValue = $this->getScheduleValue();
        $this->assertScheduleAddsColor($scheduleValue);
    }

    /**
     * Tests that when note type is color and calendar correct, superscript element is added to the element
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::findNoteIndex
     */
    public function testFindNoteIndexForColorNoteTypeWithCorrectCalendar()
    {
        $this->initDatasForSchedule();

        $this->noteType = LayoutConfig::NOTES_TYPE_COLOR;

        $scheduleValue = $this->getScheduleValue();
        $this->assertScheduleAddsColor($scheduleValue);
    }

    /**
     * Tests that when note type is color and calendar id is wrong, superscript element is added to the element
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::findNoteIndex
     */
    public function testFindNoteIndexForColorNoteTypeWithWrongCalendar()
    {
        $this->initDatasForSchedule();

        $this->noteType = LayoutConfig::NOTES_TYPE_COLOR;
        $this->calendar->id = null;

        $scheduleValue = $this->getScheduleValue();
        $this->assertScheduleAddsColor($scheduleValue);
    }

    /**
     * Tests that extension name is correct
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::getName
     */
    public function testExtensionName()
    {
        $this->assertEquals('schedule_extension', $this->extension->getName());
    }

    /**
     * Tests that schedule extension filter list is correct
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::getFilters
     */
    public function testAvailableFilterList()
    {
        $expectedFilterNames = ['schedule', 'footnote', 'calendarMax'];
        $availableFilters = $this->extension->getFilters();

        $this->assertSame($expectedFilterNames, array_keys($availableFilters));

        foreach ($availableFilters as $availableFilter) {
            $this->assertInstanceOf('\Twig_Filter_Method', $availableFilter);
        }
    }

    /**
     * Tests that calendar contains at least 12 lines for minutes for each hour
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::calendarMax
     */
    public function testLowerBoundOfCalendarLines()
    {
        $calendar = $this->mockCalendar();

        $linesPerHour = $this->extension->calendarMax($calendar);
        $this->assertEquals(12, $linesPerHour);
    }

    /**
     * Tests that if calendar contains more than desired number of lines,
     * number of this lines is returned
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::calendarMax
     */
    public function testUpperBoundOfCalendarLines()
    {
        $calendar = $this->mockCalendar();

        $dateTimes = $calendar->schedules->date_times;

        $maxLinesPerHour = 0;
        foreach ($dateTimes as $dateTime) {
            $dateTimePerHour = count($dateTime);
            $maxLinesPerHour = $dateTimePerHour > $maxLinesPerHour ? $dateTimePerHour : $maxLinesPerHour;
        }

        $linesPerHour = $this->extension->calendarMax($calendar, 2);
        $this->assertEquals($maxLinesPerHour, $linesPerHour);
    }

    /**
     * Tests that generated footnote is empty string when index is wrong
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::footnoteFilter
     */
    public function testThatFootnoteIsEmptyWhenIndexIsWrong()
    {
        $footnote = $this->extension->footnoteFilter(false);
        $this->assertEquals('', $footnote);
    }

    /**
     * Tests that generated footnote is a letter when index is an integer
     *
     * @covers CanalTP\MttBundle\Twig\ScheduleExtension::footnoteFilter
     */
    public function testThatFootnoteIsLetterWhenIndexIsCorrect()
    {
        $footnote = $this->extension->footnoteFilter(0);
        $this->assertEquals('a', $footnote);
    }

    private function mockJourney()
    {
        $journey = [
          'date_time' => '',
          'additional_informations' => [],
          'links' => [
            [
              'internal' => true,
              'type' => 'notes',
              'id' => 'note:930833458516092538',
              'rel' => 'notes',
            ],
            [
              'internal' => true,
              'rel' => 'exceptions',
              'type' => 'exceptions',
              'id' => 'exception:120160328',
            ],
            [
              'type' => 'vehicle_journey',
              'value' => 'vehicle_journey:BIB:2905-52-1_dst_1',
              'rel' => 'vehicle_journeys',
              'id' => 'vehicle_journey:BIB:2905-52-1_dst_1',
            ]
          ],
          'data_freshness' => 'base_schedule',
        ];

        $jsonObj = json_decode(json_encode($journey));
        $jsonObj->date_time = new \DateTime('2016-04-07 00:00:00');
        $jsonObj->date_time->setTime(23, $this->expectedMinute, 0);

        return $jsonObj;
    }

    private function mockNotes()
    {
        $note = [
          'type' => 'notes',
          'id' => 'note:930833458516092538',
          'value' => 'c: du dimanche au mercredi ces horaires fonctionnent sur réservation au 02 98 34 42 22',
          'calendarId' => 'Y2FsZW5kYXI6NzIwMA',
          'color' => $this->noteColor,
        ];

        $jsonNote = json_decode(json_encode($note));

        return [$jsonNote];
    }

    private function mockCalendar()
    {
        $calendarMockPath = realpath((__DIR__) . '/../../DataFixtures/Navitia/calendar.exception.json');
        $json = file_get_contents($calendarMockPath);

        return json_decode($json);
    }

    /**
     * Initializes parameters for Schedule method
     */
    private function initDatasForSchedule()
    {
        $this->journey  = $this->mockJourney();
        $this->notes    = $this->mockNotes();
        $this->noteType = LayoutConfig::NOTES_TYPE_EXPONENT;
        $this->calendar = $this->mockCalendar();
    }

    /**
     * Exxecutes Schedule method of twig extension with class properties
     *
     * properties used $journey, $notes, $noteType, calendar
     *
     * @return string
     */
    private function getScheduleValue()
    {
        return $this->extension->scheduleFilter(
                    $this->journey,
                    $this->notes,
                    $this->noteType,
                    $this->calendar
                );
    }

    private function assertScheduleAddsColor($scheduleValue)
    {
        $pattern = '<span style="background-color: %1$s"><span style="background-color: %1$s">%2$s</span></span>';
        $expected = sprintf($pattern, $this->noteColor, $this->expectedMinute);

        $this->assertEquals($expected, $scheduleValue);
    }

}