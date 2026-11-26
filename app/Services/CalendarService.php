<?php

namespace App\Services;

use App\Models\InvitationEvent;
use Carbon\Carbon;
use DateTimeZone;

class CalendarService
{
    public function googleUrl(InvitationEvent $event): string
    {
        $start = $this->toUtc($event, $event->starts_at);
        $end = $this->toUtc($event, $event->ends_at ?: $event->starts_at?->copy()->addHours(2));

        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => $event->invitation->title.' - '.$event->name,
            'dates' => $start->format('Ymd\THis\Z').'/'.$end->format('Ymd\THis\Z'),
            'details' => 'Undangan digital: '.route('invitation.public', $event->invitation->slug),
            'location' => trim(($event->venue ?? '').' '.($event->address ?? '')),
        ]);
    }

    public function ics(InvitationEvent $event): string
    {
        $start = $this->toUtc($event, $event->starts_at);
        $end = $this->toUtc($event, $event->ends_at ?: $event->starts_at?->copy()->addHours(2));

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//UndanganKu//ID',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            'UID:event-'.$event->id.'@undangan.test',
            'DTSTAMP:'.now()->utc()->format('Ymd\THis\Z'),
            'DTSTART:'.$start->format('Ymd\THis\Z'),
            'DTEND:'.$end->format('Ymd\THis\Z'),
            'SUMMARY:'.$this->escape($event->invitation->title.' - '.$event->name),
            'DESCRIPTION:'.$this->escape('Undangan digital: '.route('invitation.public', $event->invitation->slug)),
            'LOCATION:'.$this->escape(trim(($event->venue ?? '').' '.($event->address ?? ''))),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines)."\r\n";
    }

    private function toUtc(InvitationEvent $event, ?Carbon $date): Carbon
    {
        $date ??= now();
        $timezone = config('undangan.timezones.'.$event->timezone, 'Asia/Jakarta');

        return Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $date->format('Y-m-d H:i:s'),
            new DateTimeZone($timezone),
        )->utc();
    }

    private function escape(string $value): string
    {
        return str_replace(['\\', ';', ',', "\n"], ['\\\\', '\\;', '\\,', '\\n'], $value);
    }
}
