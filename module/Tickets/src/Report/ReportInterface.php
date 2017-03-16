<?php


namespace OpenTickets\Tickets\Report;


interface ReportInterface
{
    public function produceReport(): array;
}