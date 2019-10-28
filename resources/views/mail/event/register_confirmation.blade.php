@component('mail::message')
# Event registration confirmation

You succefully registered on event<br>
**{{ $event->title }}**<br>
<br><i>{{ $event->place }} - {{ $event->event_date }}</i>

![event]({{ $event->media->full_url }})

@component('mail::panel')
{{ $event->html_after_registration }}
@endcomponent

@component('mail::button', ['url' => config('app.url') . '/event/info/' . + $event->id])
Visit Event page
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
