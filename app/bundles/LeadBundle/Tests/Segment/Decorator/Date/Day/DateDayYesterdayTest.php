<?php

/*
 * @copyright   2017 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Tests\Segment\Decorator\Date\Day;

use Mautic\CoreBundle\Helper\DateTimeHelper;
use Mautic\LeadBundle\Segment\ContactSegmentFilterCrate;
use Mautic\LeadBundle\Segment\Decorator\Date\DateOptionParameters;
use Mautic\LeadBundle\Segment\Decorator\Date\Day\DateDayYesterday;
use Mautic\LeadBundle\Segment\Decorator\DateDecorator;

class DateDayYesterdayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Day\DateDayYesterday::getOperator
     */
    public function testGetOperatorBetween()
    {
        $dateDecorator        = $this->createMock(DateDecorator::class);
        $dateOptionParameters = $this->createMock(DateOptionParameters::class);

        $dateOptionParameters->method('isBetweenRequired')
            ->willReturn(true);

        $contactSegmentFilterCrate = new ContactSegmentFilterCrate([]);

        $filterDecorator = new DateDayYesterday($dateDecorator, $dateOptionParameters);

        $this->assertEquals('like', $filterDecorator->getOperator($contactSegmentFilterCrate));
    }

    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Day\DateDayYesterday::getOperator
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

        $filterDecorator = new DateDayYesterday($dateDecorator, $dateOptionParameters);

        $this->assertEquals('==<<', $filterDecorator->getOperator($contactSegmentFilterCrate));
    }

    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Day\DateDayYesterday::getParameterValue
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

        $filterDecorator = new DateDayYesterday($dateDecorator, $dateOptionParameters);

        $this->assertEquals('2018-03-01%', $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }

    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Day\DateDayYesterday::getParameterValue
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

        $contactSegmentFilterCrate = new ContactSegmentFilterCrate([]);

        $filterDecorator = new DateDayYesterday($dateDecorator, $dateOptionParameters);

        $this->assertEquals('2018-03-01', $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }
}