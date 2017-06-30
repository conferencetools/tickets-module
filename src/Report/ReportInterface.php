<?php


namespace ConferenceTools\Tickets\Report;


interface ReportInterface
{
    public function produceReport(): array;
}