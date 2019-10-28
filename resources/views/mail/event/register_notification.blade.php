@component('mail::message')
# New event registration

**Event**<br>
<b>{{ $event->title }}</b>
<br><i>{{ $event->place }} - {{ $event->event_date }}</i>

**User Data**
<ul>
    <li><b>Name:</b> {{ $user['name'] }}</li>
    <li><b>Email:</b> {{ $user['email'] }}</li>
    <li><b>City:</b> {{ $user['city'] }}</li>
    <li><b>Phone:</b> {{ $user['phone'] }}</li>
    <li><b>Comments:</b> {{ $user['comments'] }}</li>
</ul>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
