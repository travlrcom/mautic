<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Tests\Segment\Decorator\Date\Week;

use Mautic\CoreBundle\Helper\DateTimeHelper;
use Mautic\LeadBundle\Segment\ContactSegmentFilterCrate;
use Mautic\LeadBundle\Segment\Decorator\Date\DateOptionParameters;
use Mautic\LeadBundle\Segment\Decorator\Date\Week\DateWeekNext;
use Mautic\LeadBundle\Segment\Decorator\DateDecorator;

class DateWeekNextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Week\DateWeekNext::getOperator
     */
    public function testGetOperatorBetween()
    {
        $dateDecorator        = $this->createMock(DateDecorator::class);
        $dateOptionParameters = $this->createMock(DateOptionParameters::class);

        $dateOptionParameters->method('isBetweenRequired')
            ->willReturn(true);

        $contactSegmentFilterCrate = new ContactSegmentFilterCrate([]);

        $filterDecorator = new DateWeekNext($dateDecorator, $dateOptionParameters);

        $this->assertEquals('between', $filterDecorator->getOperator($contactSegmentFilterCrate));
    }

    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Week\DateWeekNext::getOperator
     */
    public function testGetOperatorLessOrEqual()
    {
        $dateDecorator        = $this->createMock(DateDecorator::class);
        $dateOptionParameters = $this->createMock(DateOptionParameters::class);

        $dateDecorator->method('getOperator')
            ->with()
            ->willReturn('==<<'); //Test that value is really returned from Decorator

        $dateOptionParameters->method('isBetweenRequired')
            ->willReturn(false);

        $filter        = [
            'operator' => '=<',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);

        $filterDecorator = new DateWeekNext($dateDecorator, $dateOptionParameters);

        $this->assertEquals('==<<', $filterDecorator->getOperator($contactSegmentFilterCrate));
    }

    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Week\DateWeekNext::getParameterValue
     */
    public function testGetParameterValueBetween()
    {
        $dateDecorator        = $this->createMock(DateDecorator::class);
        $dateOptionParameters = $this->createMock(DateOptionParameters::class);

        $dateOptionParameters->method('isBetweenRequired')
            ->willReturn(true);

        $date = new DateTimeHelper('2018-03-02', null, 'local');

        $dateDecorator->method('getDefaultDate')
            ->with()
            ->willReturn($date);

        $contactSegmentFilterCrate = new ContactSegmentFilterCrate([]);

        $filterDecorator = new DateWeekNext($dateDecorator, $dateOptionParameters);

        $this->assertEquals(['2018-03-05', '2018-03-11'], $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }

    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Week\DateWeekNext::getParameterValue
     */
    public function testGetParameterValueSingle()
    {
        $dateDecorator        = $this->createMock(DateDecorator::class);
        $dateOptionParameters = $this->createMock(DateOptionParameters::class);

        $dateOptionParameters->method('isBetweenRequired')
            ->willReturn(false);

        $date = new DateTimeHelper('2018-03-02', null, 'local');

        $dateDecorator->method('getDefaultDate')
            ->with()
            ->willReturn($date);

        $filter        = [
            'operator' => '<',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);

        $filterDecorator = new DateWeekNext($dateDecorator, $dateOptionParameters);

        $this->assertEquals('2018-03-05', $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }
}